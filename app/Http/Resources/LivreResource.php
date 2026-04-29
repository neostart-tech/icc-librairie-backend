<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivreResource extends JsonResource
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
            'titre' => $this->titre,
            'auteur' => $this->auteur,
            'description' => $this->description,
            'prix' => $this->prix,
            'prix_promo' => $this->prix_promo,
            'categorie_id' => $this->categorie_id,
            'id_auteur' => $this->id_auteur,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_selection_mois' => (bool)$this->is_selection_mois,
            'is_selection_mois_precedent' => (bool)$this->is_selection_mois_precedent,
            'is_vogue' => (bool)$this->is_vogue,
            'image' => $this->image,

            'categorie' => new CategorieResource($this->whenLoaded('categorie')),
            'auteurRel' => new AuteurResource($this->whenLoaded('auteurRel')),
            'stock' => new StockResource($this->whenLoaded('stock')),
            'stockMouvements' => StockMouvementResource::collection($this->whenLoaded('stockMouvements')),
        ];
    }
}
