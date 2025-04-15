<?php

namespace App\Services;

use App\Classes\Helpers;
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


    public function filterValidData(Request $request): array
    {
        $validUserFields = $request->validated();


        // Filter phones
        $filteredPhones = [];
        $primaryPhone = null;

        foreach ($validUserFields['phones'] as $key => $phone) {
            $filteredPhones[] = Helpers::filterPhone($phone['number']);

            if ($key === 0 || @$phone['is_primary']) {
                $primaryPhone = Helpers::filterPhone($phone['number']);
            }
        }

        $validUserFields['phones'] = $filteredPhones;
        $validUserFields['phone'] = $primaryPhone;


        // Filter address list
        $filteredAddressList = [];
        $primaryAddress = null;

        foreach ($validUserFields['address_list'] as $key => $address) {
            if ($address['street']){
                $filteredAddressList[] = $address;

                if (@$address['is_primary']) {
                    $primaryAddress = $address;
                }
            }
        }

        if (!$primaryAddress && !empty($filteredAddressList)) {
            $primaryAddress = $filteredAddressList[0];
            $filteredAddressList[0]['is_primary'] = true;
        }

        $validUserFields['address'] = $primaryAddress;
        $validUserFields['address_list'] = $filteredAddressList;

        return $validUserFields;
    }


}
