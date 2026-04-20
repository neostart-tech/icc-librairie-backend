<?php

namespace App\Http\Controllers\banners;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Http\Resources\BannerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Liste des bannières
     */
    public function index()
    {
        $banners = Banner::orderBy('order', 'asc')->get();
        return BannerResource::collection($banners);
    }

    /**
     * Liste des bannières actives pour le frontend
     */
    public function actives()
    {
        $banners = Banner::where('is_active', true)->orderBy('order', 'asc')->get();
        return BannerResource::collection($banners);
    }

    /**
     * Création d'une bannière
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
            'title' => 'nullable|string|max:255',
            'order' => 'required|integer|unique:banners,order',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        $banner = Banner::create([
            'image_path' => $path,
            'title' => $request->title,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Bannière créée avec succès',
            'data' => new BannerResource($banner)
        ], 201);
    }

    /**
     * Détail d'une bannière
     */
    public function show(Banner $banner)
    {
        return new BannerResource($banner);
    }

    /**
     * Modification d'une bannière
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|max:4096',
            'title' => 'nullable|string|max:255',
            'order' => 'nullable|integer|unique:banners,order,' . $banner->id,
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $banner->image_path = $request->file('image')->store('banners', 'public');
        }

        if ($request->has('title')) $banner->title = $request->title;
        if ($request->has('order')) $banner->order = $request->order;
        if ($request->has('is_active')) $banner->is_active = $request->is_active;

        $banner->save();

        return response()->json([
            'message' => 'Bannière mise à jour avec succès',
            'data' => new BannerResource($banner)
        ]);
    }

    /**
     * Suppression d'une bannière
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();

        return response()->json([
            'message' => 'Bannière supprimée avec succès'
        ]);
    }
}
