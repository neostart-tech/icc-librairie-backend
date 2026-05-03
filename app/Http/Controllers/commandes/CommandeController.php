<?php

namespace App\Http\Controllers\commandes;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommandeResource;
use App\Notifications\CommandeTraiteeNotification;
use App\Services\CashPayService;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\DetailCommande;
use App\Models\Livre;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderRequestMail;
use App\Mail\PaymentDeclaredMail;
use App\Mail\NewPaymentAdminMail;
use App\Mail\PaymentValidatedMail;
use App\Mail\PaymentRefusedMail;
use App\Notifications\PaymentValidatedNotification;
use App\Notifications\PaymentDeclaredNotification;
use App\Notifications\PaymentRefusedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;


class CommandeController extends Controller
{
    /**
     * Liste des commandes de l'utilisateur connecté
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $commandes = Commande::with([
            'detailcommandes.livre',
            'paiements'
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return CommandeResource::collection($commandes);
    }

    /**
     * Liste de toutes les commandes (admin)
     */
    public function allOrders()
    {
        $commandes = Commande::with([
            'detailcommandes.livre',
            'paiements',
            'user'
        ])
            ->where('statut', '!=', 'en_cours')
            ->latest()
            ->get();
        return CommandeResource::collection($commandes);
    }

    /**
     * Détail d'une commande
     */
    public function show(string $id)
    {
        $commande = Commande::with([
            'detailcommandes.livre',
            'paiements'
        ])
            ->where(function($query) {
                if (!auth()->user()->isAdmin()) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->findOrFail($id);

        return new CommandeResource($commande);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_livraison' => 'required|in:livraison,retrait',
            'adresse_livraison' => 'required_if:type_livraison,livraison|string|nullable',
            'numero_livraison' => 'required_if:type_livraison,livraison|string|nullable',
            'livres' => 'required|array|min:1',
            'livres.*.livre_id' => 'required|uuid|exists:livres,id',
            'livres.*.quantite' => 'required|integer|min:1',
        ]);

        $commande = null;

        DB::transaction(function () use ($request, &$commande) {
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

            $frais_livraison = 0;
            if ($request->type_livraison === 'livraison') {
                $frais_livraison = (int) \App\Models\Setting::get('delivery_fee', 0);
            }

            // Création commande
            $commande = Commande::create([
                'id' => Str::uuid(),
                'reference' => 'CMD-' . time(),
                'prix_total' => $total,
                'type_livraison' => $request->type_livraison,
                'adresse_livraison' => $request->adresse_livraison,
                'numero_livraison' => $request->numero_livraison,
                'frais_livraison' => $frais_livraison,
                'statut' => 'en_cours',
                'user_id' => auth()->id(),
            ]);

            // Détails de commande
            foreach ($lignes as $ligne) {
                DetailCommande::create([
                    'id' => Str::uuid(),
                    'commande_id' => $commande->id,
                    'livre_id' => $ligne['livre']->id,
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix'],
                ]);
            }

            /* COMMENTED OUT SEMOA INTEGRATION
            $paiement = Paiement::create([
                'id' => Str::uuid(),
                'commande_id' => $commande->id,
                'moyen_paiement' => $request->gateway_id,
                'reference_transaction' => 'TMP-' . uniqid(),
                'montant' => $total + $frais_livraison,
                'statut' => 'pending',
            ]);

            try {
                $cashpay = app(CashPayService::class);
                $result = $cashpay->createOrder($total + $frais_livraison, $request->phone, $request->gateway_id);
                $paiement->update(['reference_transaction' => $result['order_reference']]);
                $paymentUrl = $result['bill_url'];
            } catch (\Throwable $e) {
                logger()->error('Erreur Semoa', ['message' => $e->getMessage()]);
                throw new \Exception("Erreur lors de l'initialisation du paiement");
            }
            */
        });

        // Envoyer mail de confirmation de demande
        try {
            Mail::to($commande->user->email)->send(new OrderRequestMail($commande));
        } catch (\Exception $e) {
            logger()->error('Mail error (store)', ['msg' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Commande créée avec succès',
            'commande' => $commande->load('detailcommandes.livre'),
            'payment_instructions' => \App\Models\Setting::get('payment_message'),
            'total_a_payer' => $commande->prix_total + $commande->frais_livraison
        ], 201);
    }

    /**
     * Déclarer le paiement d'une commande
     */
    public function declarerPaiement(Request $request, Commande $commande)
    {
        $request->validate([
            'reference_paiement_client' => 'required_without:preuve_paiement|string|nullable',
            'preuve_paiement' => 'required_without:reference_paiement_client|image|max:2048|nullable',
        ]);

        if ($commande->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $path = null;
        if ($request->hasFile('preuve_paiement')) {
            $path = $request->file('preuve_paiement')->store('preuves_paiement', 'public');
        }

        $commande->update([
            'statut' => 'en_attente_validation',
            'preuve_paiement' => $path,
            'reference_paiement_client' => $request->reference_paiement_client,
        ]);

        // Notifications
        try {
            // Notification au client (Mail + Database)
            $commande->user->notify(new PaymentDeclaredNotification($commande));

            // Notification aux admins (Mail + Database)
            $admins = User::admins();
            Notification::send($admins, new PaymentDeclaredNotification($commande));
        } catch (\Exception $e) {
            logger()->error('Notification error (declarerPaiement)', ['msg' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Votre déclaration de paiement a été reçue et sera validée prochainement.',
            'data' => $commande
        ]);
    }

    /**
     * Valider un paiement (Admin)
     */
    public function validerPaiement(Commande $commande)
    {
        DB::transaction(function () use ($commande) {
            $commande->update(['statut' => 'valide']);

            // Décrémentation du stock
            foreach ($commande->detailcommandes as $detail) {
                $stock = $detail->livre->stock;
                if ($stock->quantite < $detail->quantite) {
                    throw new \Exception("Stock insuffisant pour {$detail->livre->titre}");
                }
                $stock->decrement('quantite', $detail->quantite);
                
                // On peut ajouter ici les notifications de stock faible/épuisé
            }

            // Créer l'entrée dans la table paiements
            Paiement::create([
                'id' => Str::uuid(),
                'commande_id' => $commande->id,
                'moyen_paiement' => 'manuel',
                'reference_transaction' => $commande->reference_paiement_client ?? 'MANUAL-' . time(),
                'montant' => $commande->prix_total + $commande->frais_livraison,
                'statut' => 'success',
            ]);
        });

        // Notifications
        try {
            // Notification au client (Mail + Database)
            $commande->user->notify(new PaymentValidatedNotification($commande));

            // Notification aux admins (Mail + Database)
            $admins = User::admins();
            Notification::send($admins, new PaymentValidatedNotification($commande));

        } catch (\Exception $e) {
            logger()->error('Notification error (validerPaiement)', ['msg' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Paiement validé avec succès. Le stock a été mis à jour.',
            'data' => $commande
        ]);
    }

    /**
     * Refuser un paiement (Admin)
     */
    public function refuserPaiement(Request $request, Commande $commande)
    {
        $request->validate(['motif' => 'required|string']);

        $commande->update([
            'statut' => 'paiement_refuse',
            'motif_refus_paiement' => $request->motif
        ]);

        // Notifications
        try {
            $commande->user->notify(new PaymentRefusedNotification($commande, $request->motif));
        } catch (\Exception $e) {
            logger()->error('Notification error (refuserPaiement)', ['msg' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Paiement refusé.',
            'data' => $commande
        ]);
    }

    /**
     * Marquer comme traitée (Admin) - quand le client est servi
     */
    public function finaliserCommande(Commande $commande)
    {
        $commande->update(['statut' => 'traite']);

        // Notifications
        try {
            // Notification au client (Mail + Database)
            $commande->user->notify(new CommandeTraiteeNotification($commande));

            // Notification aux admins (Mail + Database)
            $admins = User::admins();
            Notification::send($admins, new CommandeTraiteeNotification($commande));
        } catch (\Exception $e) {
            logger()->error('Notification error (finaliserCommande)', ['msg' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Commande marquée comme traitée.',
            'data' => $commande
        ]);
    }

    /**
     * Vente au comptoir (Admin)
     */
    public function venteComptoir(Request $request)
    {
        $request->validate([
            'livres' => 'required|array|min:1',
            'livres.*.livre_id' => 'required|uuid|exists:livres,id',
            'livres.*.quantite' => 'required|integer|min:1',
            'user_id' => 'nullable|uuid|exists:users,id',
        ]);

        $commande = null;

        DB::transaction(function () use ($request, &$commande) {
            $total = 0;
            $lignes = [];

            foreach ($request->livres as $item) {
                $livre = Livre::lockForUpdate()->findOrFail($item['livre_id']);
                if ($livre->stock->quantite < $item['quantite']) {
                    throw new \Exception("Stock insuffisant pour " . $livre->titre);
                }
                $prix = $livre->prix_promo ?? $livre->prix;
                $total += $prix * $item['quantite'];
                $lignes[] = ['livre' => $livre, 'quantite' => $item['quantite'], 'prix' => $prix];
            }

            $commande = Commande::create([
                'id' => Str::uuid(),
                'reference' => 'CPT-' . time(),
                'prix_total' => $total,
                'type_livraison' => 'retrait',
                'statut' => 'traite',
                'user_id' => $request->user_id ?? auth()->id(), // On peut associer à un client existant ou à l'admin
            ]);

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

            Paiement::create([
                'id' => Str::uuid(),
                'commande_id' => $commande->id,
                'moyen_paiement' => 'comptoir',
                'reference_transaction' => 'CPT-' . time(),
                'montant' => $total,
                'statut' => 'success',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Vente au comptoir enregistrée avec succès',
            'data' => $commande->load('detailcommandes.livre')
        ], 201);
    }


}
