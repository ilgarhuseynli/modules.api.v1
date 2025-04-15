<?php

namespace App\Http\Requests\User;

use App\Enums\AdminstrationLevel;
use Illuminate\Validation\Rule;
use App\Enums\UserGender;
use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->route('user');

        if (!Gate::allows('user_edit', $user)) {
            return false;
        }

        return true;
    }

    public function rules()
    {
        $id = request()->route('user')->id ?? request()->route('user');

        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id .',id'],

            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'is_company' => ['boolean'],
            'administrator_level' => ['nullable', 'integer', 'in:' . implode(',', AdminstrationLevel::getValues())],
            'send_notification' => ['boolean'],
//            'type' => ['required', 'in:' . implode(',', UserType::getValues())],
            'avatar' => ['nullable', 'string', 'max:255'],
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
            'type.in' => 'The user type must be one of: ' . implode(', ', UserType::getValues()),
            'gender.in' => 'The gender must be one of: ' . implode(', ', UserGender::getValues()),
            'administrator_level.in' => 'The admin level must be one of: ' . implode(', ', AdminstrationLevel::getValues()),
        ];
    }
}
