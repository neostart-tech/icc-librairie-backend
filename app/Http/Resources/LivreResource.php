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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'categorie' => new CategorieResource($this->whenLoaded('categorie')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'stock' => new StockResource($this->whenLoaded('stock')),
            'stockMouvements' => StockMouvementResource::collection($this->whenLoaded('stockMouvements')),
        ];
    }
}
