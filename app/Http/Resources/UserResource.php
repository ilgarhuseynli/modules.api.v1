<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'address' => $this->address ?? false,
//            'avatar' => File::getFileObject($this->avatar,'user'),
//            'role' => Role::getById($this->role_id),
            'phone' => $this->phone,
            'email' => $this->email,
            'created_at' => strtotime($this->created_at),
        ];
    }

}
