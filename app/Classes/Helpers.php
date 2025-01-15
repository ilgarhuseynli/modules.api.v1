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
}
