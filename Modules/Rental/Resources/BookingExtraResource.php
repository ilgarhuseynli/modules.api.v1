<?php

namespace Modules\Rental\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingExtraResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'extra_id' => $this->extra_id,
            'name' => $this->name,
            'price' => $this->price,
            'price_type' => $this->price_type?->meta(),
            'total' => $this->total,
        ];
    }
}
