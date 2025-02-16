<?php

namespace App\Classes;

class Helpers
{
    public static function success($data = [],$description = 'Success',$title = 'Success'){
        return \response()->json([
            'status'=>'success',
            'data'=>$data,
            'title'=>$title,
            'message'=>$description
        ]);
    }

    public static function error($description = '',$errorCode = 422,$title = 'Error'){
        return \response()->json([
            'status'=>'error',
            'title'=>$title,
            'message'=>$description,
            'error_code'=>$errorCode,
        ],$errorCode);
    }

    public static function custom($data = []){
        return \response()->json($data);
    }


    public static function filterPhone($phone){
        $phone = preg_replace("/[^0-9]/", '', $phone);

//        if (strlen($phone) == 10){
//            if (substr($phone,0,1) != 0){
//                $phone = '1'.$phone; // add us number prefix
//            }
//        }

        return $phone;
    }


    public static function formatPhone($number){

        $number = self::filterPhone($number);

        if (strlen($number) == 11 && substr($number,0,1) == 1){
            $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $number);
        }
        if (strlen($number) == 12 && substr($number,0,3) == 994){
            $number = preg_replace("/^994?(\d{2})(\d{3})(\d{2})(\d{2})$/", "(994)$1-$2-$3-$4", $number);
        }
        return $number;
    }
}
