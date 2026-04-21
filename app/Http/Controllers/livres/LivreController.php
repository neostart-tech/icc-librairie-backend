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
            'images',
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
            'images',
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
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'id_auteur' => 'nullable|exists:auteurs,id',
            'images.*' => 'nullable|image|max:4096',
            'is_selection_mois' => 'nullable|boolean',
            'is_selection_mois_precedent' => 'nullable|boolean',
            'is_vogue' => 'nullable|boolean',
        ]);

        $livre = Livre::create($request->only([
            'titre',
            'auteur',
            'description',
            'prix',
            'prix_promo',
            'categorie_id',
            'id_auteur',
            'is_selection_mois',
            'is_selection_mois_precedent',
            'is_vogue'
        ]));

        // Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('livres', 'public');

                $livre->images()->create([
                    'path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Livre créé avec succès',
            'data' => new LivreResource($livre->load(['images', 'stock', 'categorie', 'auteurRel']))
        ], 201);
    }

    /*
     *  Modification de livre
     */
    public function update(Request $request, Livre $livre)
    {
        $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'auteur' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'sometimes|required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0',
            'categorie_id' => 'sometimes|required|exists:categories,id',
            'id_auteur' => 'nullable|exists:auteurs,id',
            'images.*' => 'nullable|image|max:4096',
            'is_selection_mois' => 'nullable|boolean',
            'is_selection_mois_precedent' => 'nullable|boolean',
            'is_vogue' => 'nullable|boolean',
        ]);

        $livre->update($request->only([
            'titre',
            'auteur',
            'description',
            'prix',
            'prix_promo',
            'categorie_id',
            'id_auteur',
            'is_selection_mois',
            'is_selection_mois_precedent',
            'is_vogue'
        ]));

        // Ajouter nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('livres', 'public');

                $livre->images()->create([
                    'path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Livre mis à jour avec succès',
            'data' => new LivreResource($livre->load(['images', 'stock', 'categorie', 'auteurRel']))
        ]);
    }

    /**
     *  Suppression d'article
     */
    public function destroy(Livre $livre)
    {
        // Supprimer les images physiques
        foreach ($livre->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        // Supprimer livre
        $livre->delete();
        return response()->json([
            'message' => 'Livre supprimé avec succès'
        ]);
    }

    /**
     *  Récupérer les livres mis en avant (mois, mois précédent, vogue)
     */
    public function getFeatured()
    {
        $selection_mois = Livre::with(['images', 'stock', 'categorie', 'auteurRel'])
            ->where('is_selection_mois', true)
            ->get();

        $selection_mois_precedent = Livre::with(['images', 'stock', 'categorie', 'auteurRel'])
            ->where('is_selection_mois_precedent', true)
            ->get();

        $en_vogue = Livre::with(['images', 'stock', 'categorie', 'auteurRel'])
            ->where('is_vogue', true)
            ->first(); // On n'en prend qu'un pour "Livre en vogue"

        return response()->json([
            'selection_mois' => LivreResource::collection($selection_mois),
            'selection_mois_precedent' => LivreResource::collection($selection_mois_precedent),
            'en_vogue' => $en_vogue ? new LivreResource($en_vogue) : null
        ]);
    }
}
