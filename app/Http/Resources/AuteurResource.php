<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuteurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'biographie' => $this->biographie,
            'bibliographie' => $this->bibliographie,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
