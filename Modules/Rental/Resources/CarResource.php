<?php

namespace Modules\Rental\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray($request): array
    {
        $avatar = FileService::getResource($this->avatar);

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => new CarCategoryResource($this->whenLoaded('category')),
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'plate_number' => $this->plate_number,
            'color' => $this->color,
            'transmission' => $this->transmission?->meta(),
            'fuel_type' => $this->fuel_type?->meta(),
            'body_type' => $this->body_type?->meta(),
            'seats' => $this->seats,
            'doors' => $this->doors,
            'engine' => $this->engine,
            'price_daily' => $this->price_daily,
            'price_weekly' => $this->price_weekly,
            'price_monthly' => $this->price_monthly,
            'deposit' => $this->deposit,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'avatar' => $avatar,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => FileService::getResource($img))),
            'translations' => $this->whenLoaded('translations', fn () => $this->translations->keyBy('locale')->map(fn ($t) => [
                'description' => $t->description,
            ])),
            'badges' => BadgeResource::collection($this->whenLoaded('badges')),
            'extras' => ExtraResource::collection($this->whenLoaded('extras')),
            'created_at' => strtotime($this->created_at),
            'updated_at' => strtotime($this->updated_at),
        ];
    }
}
