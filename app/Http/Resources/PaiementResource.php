<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
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
            'moyen_paiement' => $this->moyen_paiement,
            'reference_transaction' => $this->reference_transaction,
            'statut' => $this->statut,
            'commande_id' => $this->commande_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'commande' => new CommandeResource($this->whenLoaded('commande')),
        ];
    }
}
