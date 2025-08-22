<?php

namespace App\Classes;


class Parameters
{

    public static function payment_status()
    {
        return [
            ["id" => 0, "value" => 'unpaid',"title" => 'Unpaid'],
            ["id" => 1, "value" => 'paid' ,"title" => 'Paid'],
        ];
    }


    public static function weekDays($index = false)
    {
        $dayNames = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        return $index ? $dayNames[$index] : $dayNames;
    }


    public static function currency_list()
    {
        return [
            ['value' => 'usd', 'sign' => '$', 'slug' => 'usd', 'label' => 'USD'],
            ['value' => 'azn', 'sign' => '₼', 'slug' => 'azn', 'label' => 'AZN'],
            ['value' => 'eur', 'sign' => '€', 'slug' => 'eur', 'label' => 'EUR'],
        ];
    }


}
