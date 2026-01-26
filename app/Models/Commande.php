<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Commande extends Model
{
    use HasUuid;

    protected $fillable = ['reference', 'prix_total', 'statut', 'user_id'];

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
