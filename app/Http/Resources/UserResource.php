<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $fileService;

    public function __construct($resource, FileService $fileService)
    {
        parent::__construct($resource);
        $this->fileService = $fileService;
    }

    public function toArray($request)
    {
        $imageUrl = asset('images/users/avatar-4.jpg');
        $avatar = $this->fileService->getResource($this->avatar);

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'keyword' => $this->keyword,
            'name' => $this->name,
            'address' => $this->address ?? false,
            'image' => $imageUrl,
            'avatar' => $avatar,
            'role' => $this->role,
            'phone' => $this->phone,
            'email' => $this->email,
            'created_at' => strtotime($this->created_at),
        ];
    }

}
