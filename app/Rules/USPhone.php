<?php

namespace App\Rules;

use App\Classes\Helpers;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class USPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Helpers::validateUsPhone($value)) {
            $fail('Only US phone numbers are allowed.');
        }
    }
}
