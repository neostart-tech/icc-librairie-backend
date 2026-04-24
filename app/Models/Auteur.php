<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Auteur extends Model
{
    use HasUuid;

    protected $fillable = ['nom', 'biographie', 'bibliographie'];

    public function livres()
    {
        return $this->hasMany(Livre::class, 'id_auteur');
    }
}
