<?php

namespace App\Http\Controllers\Paiements;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PaiementController extends Controller
{
    /**
     * Callback Semoa (appelé par CashPay)
     */
    public function callback(Request $request)
    {
        \Log::info("CALLBACK SEMOA RECU", [
            'body' => $request->getContent(),
            'all' => $request->all(),
        ]);

        // Récupérer le token depuis le JSON
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 400);
        }

        // Décoder le JWT
        try {
            $payload = JWT::decode(
                $token,
                new Key(config('services.cashpay.apikey'), 'HS256')
            );
        } catch (\Exception $e) {
            \Log::error("JWT invalide", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'JWT invalide'], 400);
        }

        \Log::info("PAYLOAD SEMOA DECODE", (array) $payload);

        // Retrouver le paiement
        $paiement = Paiement::where('reference_transaction', $payload->order_reference)->first();

        if (!$paiement) {
            return response()->json(['error' => 'Paiement inconnu'], 404);
        }

        // Vérification montant
        if ((int) $payload->amount !== (int) $paiement->montant) {
            return response()->json(['error' => 'Montant incohérent'], 403);
        }

        // Mise à jour statuts
        if ($payload->state === 'Paid') {
            $paiement->update(['statut' => 'success']);
            $paiement->commande->update(['statut' => 'termine']);
        } else {
            $paiement->update(['statut' => 'failed']);
        }

        return response()->json(['success' => true]);
    }



    /**
     * Consulter le statut d'un paiement
     */
    public function show($id)
    {
        $paiement = Paiement::with('commande')->findOrFail($id);

        return response()->json([
            'id' => $paiement->id,
            'statut' => $paiement->statut,
            'montant' => $paiement->montant,
            'commande' => $paiement->commande->reference,
        ]);
    }
}

