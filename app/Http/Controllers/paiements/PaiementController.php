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
        $token = $request->getContent();

        try {
            $payload = JWT::decode(
                $token,
                new Key(config('services.semoa.apikey'), 'HS256')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'JWT invalide'], 400);
        }

        $paiement = Paiement::where('reference_transaction', $payload->order_reference)->first();

        if (!$paiement) {
            return response()->json(['error' => 'Paiement inconnu'], 404);
        }

        // Sécurité: vérification montant
        if ((int) $payload->amount !== (int) $paiement->montant) {
            return response()->json(['error' => 'Montant incohérent'], 403);
        }

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

