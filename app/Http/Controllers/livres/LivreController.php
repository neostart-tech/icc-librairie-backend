<?php

namespace App\Http\Controllers\livres;

use App\Http\Controllers\Controller;
use App\Models\Livre;
use App\Http\Resources\LivreResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivreController extends Controller
{
    /**
     *  Liste des livres
     */
    public function index()
    {
        $livres = Livre::with([
            'stock',
            'categorie',
            'auteurRel'
        ])->latest()->get();

        return LivreResource::collection($livres);
    }

    /**
     *  Détail d'un livre
     */
    public function show(Livre $livre)
    {
        $livre->load([
            'stock',
            'categorie',
            'auteurRel'
        ]);

        return new LivreResource($livre);
    }

    /**
     *  Création de livre
     */
    public function store(Request $request)
    {
        \Log::info($request->all());

        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'id_auteur' => 'nullable|exists:auteurs,id',
            'image' => 'nullable|image|max:4096',
            'is_selection_annee' => 'nullable|boolean',
            'is_livre_du_mois' => 'nullable|boolean',
            'is_livre_duo' => 'nullable|boolean',
            'featured_order' => 'nullable|integer',
        ]);

        $livreData = [
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'prix' => $request->prix,
            'prix_promo' => $request->prix_promo,
            'categorie_id' => $request->categorie_id,
            'id_auteur' => $request->id_auteur,
            'is_selection_annee' => $request->boolean('is_selection_annee'),
            'is_livre_du_mois' => $request->boolean('is_livre_du_mois'),
            'is_livre_duo' => $request->boolean('is_livre_duo'),
            'featured_order' => $request->integer('featured_order', 0),
        ];

        if ($request->boolean('is_livre_du_mois')) {
            Livre::query()->update(['is_livre_du_mois' => false]);
        }

        if ($request->boolean('is_livre_duo')) {
            Livre::query()->update(['is_livre_duo' => false]);
        }

        // Upload image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $path = $file->store('livres', 'public');
                if ($path) {
                    $livreData['image'] = (string) $path;
                }
            }
        }

        $livre = Livre::create($livreData);

        return response()->json([
            'message' => 'Livre créé avec succès',
            'data' => new LivreResource($livre->load(['stock', 'categorie', 'auteurRel']))
        ], 201);
    }

    /*
     *  Modification de livre
     */
    public function update(Request $request, Livre $livre)
    {
        \Log::info($request->all());

        $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'auteur' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'sometimes|required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0',
            'categorie_id' => 'sometimes|required|exists:categories,id',
            'id_auteur' => 'nullable|exists:auteurs,id',
            'image' => 'nullable|image|max:4096',
            'is_selection_annee' => 'nullable|boolean',
            'is_livre_du_mois' => 'nullable|boolean',
            'is_livre_duo' => 'nullable|boolean',
            'featured_order' => 'nullable|integer',
        ]);

        if ($request->has('is_livre_du_mois') && $request->boolean('is_livre_du_mois')) {
            Livre::where('id', '!=', $livre->id)->update(['is_livre_du_mois' => false]);
        }

        if ($request->has('is_livre_duo') && $request->boolean('is_livre_duo')) {
            Livre::where('id', '!=', $livre->id)->update(['is_livre_duo' => false]);
        }

        $livreData = $request->only([
            'titre',
            'auteur',
            'description',
            'prix',
            'prix_promo',
            'categorie_id',
            'id_auteur',
            'image',
        ]);

        if ($request->has('is_selection_annee')) $livreData['is_selection_annee'] = $request->boolean('is_selection_annee');
        if ($request->has('is_livre_du_mois')) $livreData['is_livre_du_mois'] = $request->boolean('is_livre_du_mois');
        if ($request->has('is_livre_duo')) $livreData['is_livre_duo'] = $request->boolean('is_livre_duo');
        if ($request->has('featured_order')) $livreData['featured_order'] = $request->integer('featured_order');

        // Update image
        if ($request->hasFile('image')) {
            // Delete old image
            if ($livre->image) {
                Storage::disk('public')->delete($livre->image);
            }

            $file = $request->file('image');
            if ($file->isValid()) {
                $path = $file->store('livres', 'public');
                if ($path) {
                    $livreData['image'] = (string) $path;
                }
            }
        }

        $livre->update($livreData);

        return response()->json([
            'message' => 'Livre mis à jour avec succès',
            'data' => new LivreResource($livre->load(['stock', 'categorie', 'auteurRel']))
        ]);
    }

    /**
     *  Suppression d'article
     */
    public function destroy(Livre $livre)
    {
        try {
            \DB::beginTransaction();

            // Supprimer l'image physique
            if ($livre->image) {
                Storage::disk('public')->delete($livre->image);
            }

            // Supprimer le stock associé
            $livre->stock()->delete();

            // Supprimer les mouvements de stock associés
            $livre->stockMouvements()->delete();

            // Supprimer les détails de commandes (pour éviter l'erreur de contrainte)
            $livre->detailCommandes()->delete();

            // Supprimer le livre
            $livre->delete();

            \DB::commit();

            return response()->json([
                'message' => 'Livre supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     *  Récupérer les livres mis en avant (mois, mois précédent, vogue)
     */
    public function getFeatured()
    {
        $selection_annee = Livre::with(['stock', 'categorie', 'auteurRel'])
            ->where('is_selection_annee', true)
            ->orderBy('featured_order', 'asc')
            ->get();

        $livre_du_mois = Livre::with(['stock', 'categorie', 'auteurRel'])
            ->where('is_livre_du_mois', true)
            ->first();

        $livre_duo = Livre::with(['stock', 'categorie', 'auteurRel'])
            ->where('is_livre_duo', true)
            ->first();

        return response()->json([
            'selection_annee' => LivreResource::collection($selection_annee),
            'livre_du_mois' => $livre_du_mois ? new LivreResource($livre_du_mois) : null,
            'livre_duo' => $livre_duo ? new LivreResource($livre_duo) : null
        ]);
    }

    /**
     *  Mettre à jour l'ordre des livres mis en avant
     */
    public function reorderFeatured(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:livres,id',
            'orders.*.featured_order' => 'required|integer',
        ]);

        foreach ($request->orders as $order) {
            Livre::where('id', $order['id'])->update(['featured_order' => $order['featured_order']]);
        }

        return response()->json([
            'message' => 'Ordre mis à jour avec succès'
        ]);
    }
}
