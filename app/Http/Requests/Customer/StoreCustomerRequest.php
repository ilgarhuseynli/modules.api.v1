<?php

namespace App\Http\Requests\Customer;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules;
use App\Enums\UserType;
use App\Enums\UserGender;
use App\Enums\AdminstrationLevel;


class StoreCustomerRequest extends FormRequest
{
    public function authorize()
    {
        Gate::authorize('customer_create');

        return true;
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $email = $this->input('email','');
            if ($email){
                $existUser = User::where('email', $email)
                    ->where('type',UserType::CUSTOMER)
                    ->first();

                if ($existUser) {
                    $validator->errors()->add('phone', 'Email already exists.');
                }
            }
        });
    }

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;


    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255',],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_company' => ['boolean'],
            'send_notification' => ['boolean'],
            'avatar' => ['nullable',],
            'gender' => ['nullable', 'in:' . implode(',', UserGender::getValues())],
            'birth_date' => ['nullable', 'date'],
            'address_list' => ['array'],

            'phones' => ['array'],
            'phones.*.number' => [
                'string',
                'max:20',
            ],
            'phones.*.is_primary' => [
                'nullable',
                'boolean',
            ],
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
