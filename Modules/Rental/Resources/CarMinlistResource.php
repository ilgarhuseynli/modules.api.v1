<?php

namespace Modules\Rental\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarMinlistResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'plate_number' => $this->plate_number,
            'label' => "{$this->brand} {$this->model} ({$this->year})",
        ];
    }
}
