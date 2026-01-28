<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $fillable = [
        'semoa_id',
        'reference',
        'libelle',
        'psp',
        'psp_logo',
        'methode',
        'logo_url',
        'actif',
    ];
}

