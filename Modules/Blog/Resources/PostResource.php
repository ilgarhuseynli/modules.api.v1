<?php

namespace Modules\Blog\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        $featuredImage = FileService::getResource($this->cover_image);

        return [
            'id' => $this->id,
            'author_id' => $this->author_id,
            'author' => $this->whenLoaded('author', fn () => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ]),
            'status' => $this->status?->data(),
            'cover_image' => $featuredImage,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => FileService::getResource($img))),
            'is_featured' => $this->is_featured,
            'published_at' => $this->published_at?->toDateTimeString(),
            'sort_order' => $this->sort_order,
            'translations' => $this->whenLoaded('translations', fn () => $this->translations->keyBy('locale')->map(fn ($t) => [
                'title' => $t->title,
                'slug' => $t->slug,
                'excerpt' => $t->excerpt,
                'content' => $t->content,
            ])),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => strtotime($this->created_at),
            'updated_at' => strtotime($this->updated_at),
        ];
    }
}
