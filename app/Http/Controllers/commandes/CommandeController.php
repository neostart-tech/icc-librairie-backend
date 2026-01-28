<?php

namespace App\Http\Controllers\commandes;

use App\Http\Controllers\Controller;
use App\Services\CashPayService;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\DetailCommande;
use App\Models\Livre;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CommandeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'gateway_id' => 'required|integer',
            'livres' => 'required|array|min:1',
            'livres.*.livre_id' => 'required|uuid|exists:livres,id',
            'livres.*.quantite' => 'required|integer|min:1',
        ]);

        $paymentUrl = null;

        DB::transaction(function () use ($request, &$paymentUrl) {

            // Calcul total
            $total = 0;
            $lignes = [];

            foreach ($request->livres as $item) {
                $livre = Livre::lockForUpdate()->findOrFail($item['livre_id']);

                if ($livre->stock->quantite < $item['quantite']) {
                    throw new \Exception("Stock insuffisant pour " . $livre->titre);
                }

                $prix = $livre->prix_promo ?? $livre->prix;
                $total += $prix * $item['quantite'];

                $lignes[] = [
                    'livre' => $livre,
                    'quantite' => $item['quantite'],
                    'prix' => $prix,
                ];
            }

            // Création commande
            $commande = Commande::create([
                'id' => Str::uuid(),
                'reference' => 'CMD-' . time(),
                'prix_total' => $total,
                'statut' => 'en_cours',
                'user_id' => auth()->id(),
            ]);

            // Détails + décrément stock
            foreach ($lignes as $ligne) {

                DetailCommande::create([
                    'id' => Str::uuid(),
                    'commande_id' => $commande->id,
                    'livre_id' => $ligne['livre']->id,
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix'],
                ]);

                $ligne['livre']->stock->decrement('quantite', $ligne['quantite']);
            }

            // Paiement (PENDING)
            $paiement = Paiement::create([
                'id' => Str::uuid(),
                'commande_id' => $commande->id,
                'moyen_paiement' => $request->gateway_id,
                'reference_transaction' => 'TMP-' . uniqid(),
                'montant' => $total,
                'statut' => 'pending',
            ]);

            // Appel Semoa POS
            $cashpay = app(CashPayService::class);

            $result = $cashpay->createOrder(
                $total,
                $request->phone,
                $request->gateway_id
            );

            // Mise à jour ref Semoa
            $paiement->update([
                'reference_transaction' => $result['order_reference']
            ]);

            $paymentUrl = $result['bill_url'];
        });

        return response()->json([
            'success' => true,
            'payment_url' => $paymentUrl
        ]);
    }

}
