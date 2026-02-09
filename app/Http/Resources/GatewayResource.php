<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GatewayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'semoa_id' => $this->semoa_id,
            'reference' => $this->reference,
            'libelle' => $this->libelle,
            'psp' => $this->psp,
            'psp_logo' => $this->psp_logo,
            'methode' => $this->methode,
            'logo_url' => $this->logo_url,
            'actif' => $this->actif,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
