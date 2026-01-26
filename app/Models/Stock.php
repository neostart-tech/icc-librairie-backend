<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Stock extends Model
{
    use HasUuid;


    protected $fillable = [
        'quantite',
        'livre_id',
    ];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}
