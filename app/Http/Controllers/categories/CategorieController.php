<?php

namespace App\Http\Controllers\categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Http\Resources\CategorieResource;
use Illuminate\Support\Facades\Validator;

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

        $categorie->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}
