<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Categorie extends Model
{
    use HasUuid;

    protected $fillable = ['libelle'];

    public function livres()
    {
        return $this->hasMany(Livre::class);
    }
}
