<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Livre extends Model
{
    use HasUuid;

    protected $fillable = [
        'titre', 
        'auteur', 
        'id_auteur', 
        'description', 
        'prix', 
        'prix_promo', 
        'categorie_id',
        'is_selection_mois',
        'is_selection_mois_precedent',
        'is_vogue'
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function auteurRel()
    {
        return $this->belongsTo(Auteur::class, 'id_auteur');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function stockMouvements()
    {
        return $this->hasMany(StockMouvement::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function detailCommandes()
    {
        return $this->hasMany(DetailCommande::class);
    }
}
