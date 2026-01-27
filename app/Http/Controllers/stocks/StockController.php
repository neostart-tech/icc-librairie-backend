<?php

namespace App\Http\Controllers\stocks;

use App\Http\Controllers\Controller;
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

        $data = $request->validate([
            'livre_id' => 'required|exists:articles,id',
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'commentaire' => 'nullable|string',
        ]);

        $livre = Livre::findOrFail($data['livre_id']);

        // Récupérer ou créer le stock
        $stock = Stock::firstOrCreate(
            ['livre_id' => $livre->id],
            ['quantite' => 0]
        );

        // Si sortie, vérifier stock
        if ($data['type'] === 'sortie' && $stock->quantite < $data['quantite']) {
            return response()->json([
                'message' => 'Stock insuffisant'
            ], 422);
        }

        // Créer mouvement
        $mouvement = StockMouvement::create($data);

        // Mettre à jour le stock
        if ($data['type'] === 'entree') {
            $stock->increment('quantite', $data['quantite']);
        } else {
            $stock->decrement('quantite', $data['quantite']);
        }

        return response()->json([
            'message' => 'Mouvement enregistré',
            'stock' => $stock,
            'mouvement' => $mouvement
        ]);
    }


    /**
     * Historique des mouvements d’un article
     */
    public function mouvements($articleId)
    {
        $mouvements = StockMouvement::where('article_id', $articleId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($mouvements);
    }
}
