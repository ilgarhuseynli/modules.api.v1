<?php

namespace App\Concerns\User;

use App\Models\File;
use App\Models\ActiveDevice;
use App\Models\Permission;
use App\Models\Role;
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

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withPivot(['allow','all']);
    }

}
