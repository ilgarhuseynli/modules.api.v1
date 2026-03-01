<?php

namespace Modules\Blog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryMinlistResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translations->where('locale', app()->getLocale())->first()?->name
                ?? $this->translations->first()?->name,
            'slug' => $this->translations->where('locale', app()->getLocale())->first()?->slug
                ?? $this->translations->first()?->slug,
            'parent_id' => $this->parent_id,
        ];
    }
}
