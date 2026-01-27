<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Livre extends Model
{
    use HasUuid;

    protected $fillable = ['titre', 'auteur', 'description', 'prix', 'prix_promo', 'categorie_id'];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
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
}
