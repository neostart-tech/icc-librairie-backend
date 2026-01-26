<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeResource extends JsonResource
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
            'reference' => $this->reference,
            'prix_total' => $this->prix_total,
            'statut' => $this->statut,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user' => new UserResource($this->whenLoaded('user')),
            'detailcommandes' => DetailCommandeResource::collection($this->whenLoaded('detailcommandes')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),

        ];
    }
}
