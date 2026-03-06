<?php

namespace Modules\Rental\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarCategoryMinlistResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translations->where('locale', app()->getLocale())->first()?->name
                ?? $this->translations->first()?->name,
            'slug' => $this->translations->where('locale', app()->getLocale())->first()?->slug
                ?? $this->translations->first()?->slug,
        ];
    }
}
