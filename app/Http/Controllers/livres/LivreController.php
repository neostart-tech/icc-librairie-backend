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
            'is_selection_mois' => 'nullable|boolean',
            'is_selection_mois_precedent' => 'nullable|boolean',
            'is_vogue' => 'nullable|boolean',
        ]);

        if ($request->is_vogue) {
            Livre::where('is_vogue', true)->update(['is_vogue' => false]);
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
            'is_selection_mois',
            'is_selection_mois_precedent',
            'is_vogue'
        ]);

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
            'is_selection_mois' => 'nullable|boolean',
            'is_selection_mois_precedent' => 'nullable|boolean',
            'is_vogue' => 'nullable|boolean',
        ]);

        if ($request->is_vogue) {
            Livre::where('id', '!=', $livre->id)->update(['is_vogue' => false]);
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
            'is_selection_mois',
            'is_selection_mois_precedent',
            'is_vogue'
        ]);

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
        $selection_mois = Livre::with(['stock', 'categorie', 'auteurRel'])
            ->where('is_selection_mois', true)
            ->get();

        $selection_mois_precedent = Livre::with(['stock', 'categorie', 'auteurRel'])
            ->where('is_selection_mois_precedent', true)
            ->get();

        $en_vogue = Livre::with(['stock', 'categorie', 'auteurRel'])
            ->where('is_vogue', true)
            ->first(); // On n'en prend qu'un pour "Livre en vogue"

        return response()->json([
            'selection_mois' => LivreResource::collection($selection_mois),
            'selection_mois_precedent' => LivreResource::collection($selection_mois_precedent),
            'en_vogue' => $en_vogue ? new LivreResource($en_vogue) : null
        ]);
    }
}
