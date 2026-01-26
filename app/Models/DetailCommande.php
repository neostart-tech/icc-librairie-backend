<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class DetailCommande extends Model
{
    use HasUuid;

    protected $fillable = ['quantite', 'prix_unitaire', 'commande_id', 'livre_id'];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}
