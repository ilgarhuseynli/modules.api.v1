<?php

namespace Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Blog\Enums\PostStatus;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.excerpt' => ['nullable', 'string'],
            'translations.*.content' => ['nullable', 'string'],

            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:blog_categories,id'],

            'status' => ['nullable', 'integer', 'in:'.implode(',', PostStatus::getValues())],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $translations = $this->input('translations', []);
            $hasComplete = false;

            foreach ($translations as $locale => $translation) {
                if (filled($translation['title'] ?? null) && filled($translation['content'] ?? null)) {
                    $hasComplete = true;
                    break;
                }
            }

            if (! $hasComplete) {
                $validator->errors()->add('translations', 'At least one language must have title and content filled.');
            }
        });
    }
}
