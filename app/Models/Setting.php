<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $table = 'settings';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'key',
        'value',
        'created_at',
        'updated_at',
    ];


    const WORK_HOURS = [
        6  => ['slot' => 6,  'title' => "6AM"],
        7  => ['slot' => 7,  'title' => "7AM"],
        8  => ['slot' => 8,  'title' => "8AM"],
        9  => ['slot' => 9,  'title' => "9AM"],
        10 => ['slot' => 10, 'title' => "10AM"],
        11 => ['slot' => 11, 'title' => "11AM"],
        12 => ['slot' => 12, 'title' => "12PM"],
        13 => ['slot' => 13, 'title' => "1PM"],
        14 => ['slot' => 14, 'title' => "2PM"],
        15 => ['slot' => 15, 'title' => "3PM"],
        16 => ['slot' => 16, 'title' => "4PM"],
        17 => ['slot' => 17, 'title' => "5PM"],
        18 => ['slot' => 18, 'title' => "6PM"],
        19 => ['slot' => 19, 'title' => "7PM"],
        20 => ['slot' => 20, 'title' => "8PM"],
        21 => ['slot' => 21, 'title' => "9PM"],
        22 => ['slot' => 22, 'title' => "10PM"],
        23 => ['slot' => 23, 'title' => "11PM"],
    ];



}
