<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempFile extends Model
{
    public $table = 'temp_files';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'sizes' => 'array'
    ];

    protected $fillable = [
        'name',
        'path',
        'url',
        'size',
        'sizes',
        'mime_type',
        'created_at',
        'updated_at',
    ];
}
