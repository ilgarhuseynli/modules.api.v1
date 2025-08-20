<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Uppercase implements ValidationRule
{
    /**
     * @var bool
     * refers to whether a custom validation rule should run even when the field is null or missing from the input.
     */
    public bool $implicit = true;


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== strtoupper($value)) {
            $fail('The :attribute must be uppercase.');
        }
    }
}
