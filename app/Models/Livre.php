<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Livre extends Model
{
    use HasUuid;

    protected $fillable = ['titre', 'auteur', 'description', 'categorie_id', 'prix'];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }
}
