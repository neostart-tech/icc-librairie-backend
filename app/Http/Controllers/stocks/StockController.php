<?php

namespace App\Http\Controllers\stocks;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMouvementResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Notifications\StockEpuiseNotification;
use App\Notifications\StockFaibleNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Livre;
use App\Models\Stock;
use App\Models\StockMouvement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Liste des stocks
     */
    public function index()
    {
        $stocks = Stock::with('livre')->get();
        return response()->json($stocks);
    }

    /**
     * Détail stock d’un livre
     */
    public function show($livreId)
    {
        $stock = Stock::with('livre')->where('livre_id', $livreId)->firstOrFail();
        return response()->json($stock);
    }

    /**
     * Ajouter un mouvement de stock (entrée ou sortie)
     */
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'livre_id' => 'required|exists:livres,id',
                'type' => 'required|in:entree,sortie',
                'quantite' => 'required|integer|min:1',
                'commentaire' => 'nullable|string',
            ]);

            return DB::transaction(function () use ($data) {

                $livre = Livre::lockForUpdate()->findOrFail($data['livre_id']);

                $stock = Stock::firstOrCreate(
                    ['livre_id' => $livre->id],
                    ['quantite' => 0]
                );

                if ($data['type'] === 'sortie' && $stock->quantite < $data['quantite']) {
                    abort(422, 'Stock insuffisant');
                }

                // Créer mouvement CORRECTEMENT
                $mouvement = StockMouvement::create([
                    'livre_id' => $livre->id,
                    'type' => $data['type'],
                    'quantite' => $data['quantite'],
                    'commentaire' => $data['commentaire'] ?? null
                ]);

                // Mise à jour stock
                if ($data['type'] === 'entree') {
                    $stock->increment('quantite', $data['quantite']);
                } else {
                    $stock->decrement('quantite', $data['quantite']);
                }

                $stock->refresh();
                $stockRestant = $stock->quantite;

                // Notifications
                $admins = User::whereHas(
                    'role',
                    fn($q) =>
                    $q->whereIn('role', ['admin', 'superadmin'])
                )->get();

                if ($admins->isNotEmpty()) {
                    if ($stockRestant === 0) {
                        Notification::send($admins, new StockEpuiseNotification($livre));
                    } elseif ($stockRestant <= 3) {
                        Notification::send(
                            $admins,
                            new StockFaibleNotification($livre, $stockRestant)
                        );
                    }
                }

                return response()->json([
                    'message' => 'Mouvement enregistré',
                    'stock' => $stock,
                    'mouvement' => $mouvement,
                ]);
            });
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement du mouvement',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Historique des mouvements d’un article
     */
    public function mouvements($livreId)
    {
        $mouvements = StockMouvement::where('livre_id', $livreId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($mouvements);
    }

    /**
     * Historique de tous les mouvements
     */

    public function allMouvements()
    {
        $mouvements = StockMouvement::with('livre.images')->get();
        return response()->json($mouvements);
    }
}
