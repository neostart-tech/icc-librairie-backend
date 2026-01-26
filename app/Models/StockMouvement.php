<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class StockMouvement extends Model
{
    use HasUuid;

    protected $fillable = [
        'type',
        'quantite',
        'commentaire',
        'livre_id',
    ];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}
