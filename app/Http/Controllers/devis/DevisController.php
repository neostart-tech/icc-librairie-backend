<?php

namespace App\Http\Controllers\devis;

use App\Http\Controllers\Controller;
use App\Mail\DevisAdminMail;
use App\Mail\DevisClientMail;
use App\Models\Devis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DevisController extends Controller
{
    /**
     * Soumettre une demande de devis (public)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'livre_id'           => 'required|exists:livres,id',
            'nom_complet'        => 'required|string|max:255',
            'telephone'          => 'required|string|max:50',
            'email'              => 'nullable|email|max:255',
            'nombre_exemplaires' => 'required|integer|min:1',
            'message'            => 'nullable|string|max:2000',
        ]);

        $devis = Devis::create($validated);
        $devis->load('livre');

        // Mail de confirmation au client
        if ($devis->email) {
            try {
                Mail::to($devis->email)->send(new DevisClientMail($devis));
            } catch (\Exception $e) {
                \Log::error('DevisClientMail error: ' . $e->getMessage());
            }
        }

        // Mail de notification à tous les admins
        $admins = User::whereHas('role', fn($q) => $q->whereIn('role', ['admin', 'superadmin']))->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new DevisAdminMail($devis));
            } catch (\Exception $e) {
                \Log::error('DevisAdminMail error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Votre demande de devis a bien été enregistrée. Vous serez contacté très prochainement.',
            'data'    => $devis,
        ], 201);
    }

    /**
     * Liste de tous les devis (admin)
     */
    public function index()
    {
        $devis = Devis::with('livre')->latest()->get();

        return response()->json(['data' => $devis]);
    }

    /**
     * Détail d'un devis (admin)
     */
    public function show(Devis $devis)
    {
        $devis->load('livre');

        return response()->json(['data' => $devis]);
    }

    /**
     * Mettre à jour le statut d'un devis (admin)
     */
    public function updateStatut(Request $request, Devis $devis)
    {
        $request->validate([
            'statut' => 'required|in:nouveau,traite,refuse',
        ]);

        $devis->update(['statut' => $request->statut]);

        return response()->json([
            'message' => 'Statut mis à jour.',
            'data'    => $devis,
        ]);
    }
}
