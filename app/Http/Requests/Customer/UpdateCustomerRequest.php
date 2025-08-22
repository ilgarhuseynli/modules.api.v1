<?php

namespace App\Http\Requests\Customer;

use App\Enums\AdminstrationLevel;
use App\Enums\UserGender;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->route('user');

        Gate::authorize('customer_edit', $user);

        return true;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $userId = request()->route('customer')->id ?? request()->route('customer');

            $email = $this->input('email', '');

            if ($email){
                $existUser = User::where('email', $email)
                    ->where('type',UserType::CUSTOMER)
                    ->where('id','!=',$userId)
                    ->first();

                if ($existUser) {
                    $validator->errors()->add('phone', 'Email already exists. - '. $userId);
                }
            }
        });
    }

    public function rules()
    {

        return [
            'email' => ['required', 'string', 'email', 'max:255'],

            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'is_company' => ['boolean'],
            'send_notification' => ['boolean'],
            'gender' => ['nullable', 'in:' . implode(',', UserGender::getValues())],
            'birth_date' => ['nullable', 'date'],
            'address_list' => ['array'],
            'phones' => ['array'],
        ];
    }



    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'is_company' => 'company status',
            'administrator_level' => 'admin level',
            'send_notification' => 'notification preference',
            'birth_date' => 'birth date',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gender.in' => 'The gender must be one of: ' . implode(', ', UserGender::getValues()),
            'administrator_level.in' => 'The admin level must be one of: ' . implode(', ', AdminstrationLevel::getValues()),
        ];
    }
}
