<?php

namespace Modules\Rental\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationMinlistResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translations->where('locale', app()->getLocale())->first()?->name
                ?? $this->translations->first()?->name,
            'address' => $this->translations->where('locale', app()->getLocale())->first()?->address
                ?? $this->translations->first()?->address,
        ];
    }
}
