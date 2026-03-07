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
        foreach ((array) $this->phones as $phone) {
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
            'birth_date' => $this->birth_date,
            'address' => $this->address ?: null,
            'address_list' => $this->addresses ?? [],
            'image' => $avatar['url'] ?? '',
            'avatar' => $avatar,
            'is_company' => $this->is_company,
            'gender' => $this->gender?->value,
            'send_notification' => $this->send_notification,
            'role_id' => $this->role_id,
            'role' => $this->role,
            'administrator_level' => $this->administrator_level?->value,
            'phone' => $this->phone,
            'phones' => $formatedPhones,
            'email' => $this->email,
            'two_factor_enabled' => $this->two_factor_enabled,
            'created_at' => strtotime($this->created_at),
        ];
    }
}
