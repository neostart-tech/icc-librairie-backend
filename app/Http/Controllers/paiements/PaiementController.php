<?php

namespace App\Http\Controllers\Paiements;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaiementResource;
use App\Models\Paiement;
use App\Notifications\CommandeTermineeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NouvelleCommandeNotification;
use App\Notifications\StockFaibleNotification;
use App\Notifications\StockEpuiseNotification;
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

        $token = $request->input('token');
        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 400);
        }

        try {
            $payload = JWT::decode(
                $token,
                new Key(config('services.cashpay.apikey'), 'HS256')
            );
        } catch (\Exception $e) {
            \Log::error("JWT invalide", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'JWT invalide'], 400);
        }

        $paiement = Paiement::with('commande.detailcommandes.livre.stock')
            ->where('reference_transaction', $payload->order_reference)
            ->first();

        if (!$paiement) {
            return response()->json(['error' => 'Paiement inconnu'], 404);
        }

        if ((int) $payload->amount !== (int) $paiement->montant) {
            return response()->json(['error' => 'Montant incohérent'], 403);
        }

        // Sécurité : éviter double callback
        if ($paiement->statut === 'success') {
            return response()->json(['success' => true]);
        }

        DB::transaction(function () use ($payload, $paiement) {

            if ($payload->state !== 'Paid') {
                $paiement->update(['statut' => 'failed']);
                return;
            }

            // Paiement OK
            $paiement->update(['statut' => 'success']);
            $commande = $paiement->commande;
            $commande->update(['statut' => 'termine']);

            // Envoyer notification à l’utilisateur
            $commande->user->notify(new CommandeTermineeNotification($commande));

            // Décrémentation stock
            foreach ($commande->detailcommandes as $detail) {

                $stock = $detail->livre->stock;

                if ($stock->quantite < $detail->quantite) {
                    throw new \Exception(
                        "Stock insuffisant pour {$detail->livre->titre}"
                    );
                }

                $stock->decrement('quantite', $detail->quantite);

                // Notifications stock
                $stockRestant = $stock->quantite;
                $admins = User::admins();

                if ($stockRestant === 0) {
                    Notification::send(
                        $admins,
                        new StockEpuiseNotification($detail->livre)
                    );
                } elseif ($stockRestant <= 3) {
                    Notification::send(
                        $admins,
                        new StockFaibleNotification($detail->livre, $stockRestant)
                    );
                }
            }

            // Notification commande terminée
            Notification::send(
                User::admins(),
                new NouvelleCommandeNotification($commande)
            );
        });

        return response()->json(['success' => true]);
    }




    /**
     * Consulter le statut d'un paiement
     */
    public function show($id)
    {
        $paiement = Paiement::with('commande')->findOrFail($id);

        return response()->json(new PaiementResource($paiement));
    }

    /**
     * Liste des paiements
     */
    public function index(Request $request)
    {
        $paiements = Paiement::with('commande')
            ->latest()
            ->get(); // <-- Important !

        return response()->json(PaiementResource::collection($paiements));
    }


    /** Liste des paiements de l'utilisateur connecté
     */
    public function userPayments(Request $request)
    {
        $paiements = Paiement::with('commande')
            ->whereHas('commande', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->get(); // <-- Important !

        return response()->json(PaiementResource::collection($paiements));
    }

}

