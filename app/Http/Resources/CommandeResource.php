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
            'prix_total' => (float) $this->prix_total,
            'frais_livraison' => (float) $this->frais_livraison,
            'type_livraison' => $this->type_livraison,
            'adresse_livraison' => $this->adresse_livraison,
            'numero_livraison' => $this->numero_livraison,
            'statut' => $this->statut,
            'preuve_paiement' => $this->preuve_paiement,
            'reference_paiement_client' => $this->reference_paiement_client,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user' => new UserResource($this->whenLoaded('user')),
            'detailcommandes' => DetailCommandeResource::collection($this->whenLoaded('detailcommandes')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),

        ];
    }
}
