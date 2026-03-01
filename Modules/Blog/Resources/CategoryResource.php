<?php

namespace Modules\Blog\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $image = FileService::getResource($this->image);

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'image' => $image,
            'translations' => $this->whenLoaded('translations', fn () => $this->translations->keyBy('locale')->map(fn ($t) => [
                'name' => $t->name,
                'slug' => $t->slug,
                'description' => $t->description,
            ])),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'created_at' => strtotime($this->created_at),
        ];
    }
}
