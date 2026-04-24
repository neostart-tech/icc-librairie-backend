<?php

namespace App\Http\Controllers\popups;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use App\Http\Resources\PopupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PopupController extends Controller
{
    /**
     * Liste des popups (Admin)
     */
    public function index()
    {
        $popups = Popup::orderBy('created_at', 'desc')->get();
        return PopupResource::collection($popups);
    }

    /**
     * Popup active pour le frontend
     */
    public function active()
    {
        $popup = Popup::where('is_active', true)->first();
        if (!$popup) {
            return response()->json(['data' => null]);
        }
        return new PopupResource($popup);
    }

    /**
     * Création d'un popup
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
            'is_active' => 'nullable|boolean',
            'link' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('popups', 'public');

        if ($request->is_active) {
            Popup::where('id', '>', 0)->update(['is_active' => false]);
        }

        $popup = Popup::create([
            'image_path' => $path,
            'is_active' => $request->is_active ?? false,
            'link' => $request->link,
        ]);

        return response()->json([
            'message' => 'Popup créé avec succès',
            'data' => new PopupResource($popup)
        ], 201);
    }

    /**
     * Détail d'un popup
     */
    public function show(Popup $popup)
    {
        return new PopupResource($popup);
    }

    /**
     * Modification d'un popup
     */
    public function update(Request $request, Popup $popup)
    {
        $request->validate([
            'image' => 'nullable|image|max:4096',
            'is_active' => 'nullable|boolean',
            'link' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($popup->image_path) {
                Storage::disk('public')->delete($popup->image_path);
            }
            $popup->image_path = $request->file('image')->store('popups', 'public');
        }

        if ($request->has('is_active')) {
            if ($request->is_active) {
                Popup::where('id', '!=', $popup->id)->update(['is_active' => false]);
            }
            $popup->is_active = $request->is_active;
        }

        if ($request->has('link')) $popup->link = $request->link;

        $popup->save();

        return response()->json([
            'message' => 'Popup mis à jour avec succès',
            'data' => new PopupResource($popup)
        ]);
    }

    /**
     * Suppression d'un popup
     */
    public function destroy(Popup $popup)
    {
        if ($popup->image_path) {
            Storage::disk('public')->delete($popup->image_path);
        }
        $popup->delete();

        return response()->json([
            'message' => 'Popup supprimé avec succès'
        ]);
    }
}
