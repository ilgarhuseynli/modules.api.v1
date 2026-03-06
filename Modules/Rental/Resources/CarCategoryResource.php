<?php

namespace Modules\Rental\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'translations' => $this->whenLoaded('translations', fn () => $this->translations->keyBy('locale')->map(fn ($t) => [
                'name' => $t->name,
                'slug' => $t->slug,
            ])),
            'cars_count' => $this->whenCounted('cars'),
            'created_at' => strtotime($this->created_at),
        ];
    }
}
