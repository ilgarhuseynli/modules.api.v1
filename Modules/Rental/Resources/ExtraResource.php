<?php

namespace Modules\Rental\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExtraResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'price_type' => $this->price_type?->meta(),
            'is_global' => $this->is_global,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'pivot' => $this->whenPivotLoaded('rental_car_extra', fn () => [
                'price' => $this->pivot->price,
                'price_type' => $this->pivot->price_type,
            ]),
            'translations' => $this->whenLoaded('translations', fn () => $this->translations->keyBy('locale')->map(fn ($t) => [
                'name' => $t->name,
                'description' => $t->description,
            ])),
            'created_at' => strtotime($this->created_at),
        ];
    }
}
