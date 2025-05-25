<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $imageUrl = asset('images/users/avatar-4.jpg');
        $avatar = FileService::getResource($this->avatar);

        $formatedPhones = [];
        foreach ((array)$this->phones as $phone) {
            $formatedPhones[] = [
                'number' => $phone,
                'is_primary' => $phone == $this->phone,
            ];
        }

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'keyword' => $this->keyword,
            'name' => $this->name,
            'address_list' => $this->addresses ?? [],
            'image' => $imageUrl,
            'avatar' => $avatar,
            'is_company' => $this->is_company,
            'role' => $this->role,
            'phone' => $this->phone,
            'phones' => $formatedPhones,
            'email' => $this->email,
            'created_at' => strtotime($this->created_at),
        ];
    }

}
