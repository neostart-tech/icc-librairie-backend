<?php

namespace App\Http\Controllers\categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Http\Resources\CategorieResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CategorieController extends Controller
{
    /**
     * Liste des categories
     */
    public function index()
    {
        $categories = Categorie::latest()->get();
        return CategorieResource::collection($categories);
    }

    /**
     * Création d'une categorie (admin seulement)
     */
    public function store(Request $request)
    {

        // Validation
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $categorie = Categorie::create($validator->validated());

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'data' => new CategorieResource($categorie)
        ], 201);
    }

    /**
     * Détails d'une categorie
     */
    public function show(Categorie $categorie)
    {
        return new CategorieResource($categorie);
    }

    /**
     * Mise à jour (admin seulement)
     */
    public function update(Request $request, Categorie $categorie)
    {
        $data = $request->validate([
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $categorie->update($data);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'data' => new CategorieResource($categorie->fresh())
        ]);
    }


    /**
     * Suppression (admin seulement)
     */
    public function destroy(Categorie $categorie)
    {
        try {
            DB::beginTransaction();

            foreach ($categorie->livres as $livre) {
                // Supprimer l'image physique
                if ($livre->image) {
                    Storage::disk('public')->delete($livre->image);
                }

                // Supprimer les dépendances du livre
                $livre->stock()->delete();
                $livre->stockMouvements()->delete();
                $livre->detailCommandes()->delete();
                $livre->notes()->delete(); // On supprime aussi les notes pour être sûr
                
                $livre->delete();
            }

            $categorie->delete();

            DB::commit();

            return response()->json([
                'message' => 'Catégorie et livres associés supprimés avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la suppression de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
