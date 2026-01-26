<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Role extends Model
{
    use HasUuid;

    protected $fillable = ['role'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
