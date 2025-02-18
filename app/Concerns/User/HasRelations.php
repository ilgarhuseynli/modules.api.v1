<?php

namespace App\Concerns\User;

use App\File;
use App\Models\ActiveDevice;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasRelations
{
    public function activeDevices(): HasMany
    {
        return $this->hasMany(ActiveDevice::class);
    }

    public function avatar(){
        return $this->morphOne(File::class,'model')
            ->where('type', 'avatar')
            ->latest();
    }

    public function files(){
        return $this->morphMany(File::class,'model')->where('type', 'files');
    }

}
