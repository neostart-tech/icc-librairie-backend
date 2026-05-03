<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Commande extends Model
{
    use HasUuid;

    protected $fillable = [
        'reference', 
        'prix_total', 
        'type_livraison',
        'adresse_livraison',
        'numero_livraison',
        'frais_livraison',
        'statut', 
        'user_id',
        'preuve_paiement',
        'reference_paiement_client',
        'motif_refus_paiement'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailcommandes()
    {
        return $this->hasMany(DetailCommande::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
