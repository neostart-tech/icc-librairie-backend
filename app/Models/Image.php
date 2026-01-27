<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Image extends Model
{
    use HasUuid;

    protected $fillable = [
        'path',
        'livre_id',
    ];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}
