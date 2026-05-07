<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasUuid;

    protected $table = 'devis';

    protected $fillable = [
        'livre_id',
        'nom_complet',
        'telephone',
        'email',
        'nombre_exemplaires',
        'message',
        'statut',
    ];

    protected $casts = [
        'nombre_exemplaires' => 'integer',
    ];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}
