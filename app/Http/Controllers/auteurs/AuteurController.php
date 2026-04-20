<?php

namespace App\Http\Controllers\auteurs;

use App\Http\Controllers\Controller;
use App\Models\Auteur;
use App\Http\Resources\AuteurResource;
use Illuminate\Http\Request;

class AuteurController extends Controller
{
    public function index()
    {
        $auteurs = Auteur::latest()->get();
        return AuteurResource::collection($auteurs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'biographie' => 'nullable|string',
            'bibliographie' => 'nullable|string',
        ]);

        $auteur = Auteur::create($validated);
        
        return response()->json([
            'message' => 'Auteur créé avec succès',
            'data' => new AuteurResource($auteur)
        ], 201);
    }

    public function show(string $id)
    {
        $auteur = Auteur::findOrFail($id);
        return new AuteurResource($auteur);
    }

    public function update(Request $request, string $id)
    {
        $auteur = Auteur::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'biographie' => 'nullable|string',
            'bibliographie' => 'nullable|string',
        ]);

        $auteur->update($validated);

        return response()->json([
            'message' => 'Auteur mis à jour avec succès',
            'data' => new AuteurResource($auteur->fresh())
        ]);
    }

    public function destroy(string $id)
    {
        $auteur = Auteur::findOrFail($id);
        $auteur->delete();
        
        return response()->json([
            'message' => 'Auteur supprimé avec succès'
        ]);
    }
}
