<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailCommandeResource extends JsonResource
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
            'quantite' => $this->quantite,
            'prix_unitaire' => $this->prix_unitaire,
            'commande_id' => $this->commande_id,
            'livre_id' => $this->livre_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'livre' => new LivreResource($this->whenLoaded('livre')),
            'commande' => new CommandeResource($this->whenLoaded('commande')),
        ];
    }
}
