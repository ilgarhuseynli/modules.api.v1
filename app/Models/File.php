<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $guarded = [];


    protected $casts = [
        'sizes' => 'array'
    ];



    public function model()
    {
        return $this->morphTo();
    }

}
