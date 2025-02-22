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

    protected $fillable = [
        'name',
        'path',
        'url',
        'size',
        'mime_type',
        'created_at',
        'updated_at',
    ];
}
