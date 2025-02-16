<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'created_at' => strtotime($this->created_at),
        ];
    }
}
