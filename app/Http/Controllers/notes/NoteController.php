<?php

namespace App\Http\Controllers\notes;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Livre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Liste des notes pour un livre
     */
    public function index(Livre $livre)
    {
        $notes = Note::with('user')
            ->where('id_livre', $livre->id)
            ->latest()
            ->get();

        return response()->json($notes);
    }

    /**
     * Liste de toutes les notes (pour le dashboard)
     */
    public function allNotes()
    {
        $notes = Note::with(['user', 'livre'])
            ->latest()
            ->paginate(20);

        return response()->json($notes);
    }

    /**
     * Ajouter une note
     */
    public function store(Request $request)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string',
            'id_livre' => 'required|exists:livres,id'
        ]);

        // On permet désormais plusieurs avis par utilisateur comme demandé

        $note = Note::create([
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'id_livre' => $request->id_livre,
            'id_user' => Auth::id()
        ]);

        return response()->json([
            'message' => 'Merci pour votre avis !',
            'data' => $note->load('user')
        ], 201);
    }

    /**
     * Supprimer une note (Modération)
     */
    public function destroy(Note $note)
    {
        // On pourrait vérifier si c'est l'auteur ou un admin, 
        // mais le middleware se chargera de la protection globale pour le dashboard.
        // Pour les utilisateurs lambda, on pourrait ajouter une vérification ici si on veut qu'ils puissent supprimer leur propre note.
        
        $note->delete();

        return response()->json([
            'message' => 'Note supprimée avec succès'
        ]);
    }
}
