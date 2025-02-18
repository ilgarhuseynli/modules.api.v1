<?php

namespace App\Services;

use Illuminate\Http\Request;

class UserService
{
    public function joinKeywords(Request $request): string
    {
        return collect([
            $request->first_name,
            $request->last_name,
            $request->phone,
            $request->email,
        ])->filter()->implode(' ');
    }



}
