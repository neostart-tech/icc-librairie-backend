<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Note extends Model
{
    use HasUuid;

    protected $fillable = [
        'note',
        'commentaire',
        'id_livre',
        'id_user'
    ];

    public function livre()
    {
        return $this->belongsTo(Livre::class, 'id_livre');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
