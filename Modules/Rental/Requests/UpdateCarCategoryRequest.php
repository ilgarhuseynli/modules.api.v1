<?php

namespace Modules\Rental\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCarCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['nullable', 'array', 'min:1'],
            'translations.*.name' => ['nullable', 'string', 'max:255'],

            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $translations = $this->input('translations', []);

            if (empty($translations)) {
                return;
            }

            $hasComplete = false;

            foreach ($translations as $locale => $translation) {
                if (filled($translation['name'] ?? null)) {
                    $hasComplete = true;
                    break;
                }
            }

            if (! $hasComplete) {
                $validator->errors()->add('translations', 'At least one language must have a name filled.');
            }
        });
    }
}
