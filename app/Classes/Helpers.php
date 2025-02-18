<?php

namespace App\Classes;

class Helpers
{

    public static function success($data = [],$title = 'Success',$description = ''){
        return \response()->json([
            'status'=>'success',
            'data'=>$data,
            'title'=>$title,
            'description'=>$description
        ]);
    }

    public static function error($title = 'Error',$description = '',$errorCode = 422){
        return \response()->json([
            'status'=>'error',
            'title'=>$title,
            'description'=>$description,
            'message'=>$description,
            'error_code'=>$errorCode,
        ],$errorCode);
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



    public static function manageLimitRequest($limit, $min = 50, $max = 200)
    {
        $limit = (int)$limit;

        if ($limit < 5) {
            $limit = $min;
        } else if ($limit > 200) {
            $limit = $max;
        }

        return $limit;
    }


    public static function manageSortRequest($sortField, $sortType, $fields = false, $defaultParams = [])
    {
        $defaultSortField = @$defaultParams['sort_field'] ?: 'created_at';
        $defaultSortType = @$defaultParams['sort_type'] ?: 'desc';
        $sortType = $sortType ?: $defaultSortType;

        $fields = is_array($fields) ? $fields : ['id', 'created_at', 'title'];

        $sort_field = trim(strtolower($sortField));

        if (@$fields[$sort_field]){
            $sort_field = $fields[$sort_field];
        }elseif(!in_array($sort_field, $fields)){
            $sort_field = $defaultSortField;
        }

        $sortType = $sortType == 'asc' ? 'asc' : 'desc';

        return [
            'field' => $sort_field,
            'direction' => $sortType
        ];
    }

}
