<?php

namespace Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Blog\Enums\PostStatus;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['nullable', 'array', 'min:1'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.excerpt' => ['nullable', 'string'],
            'translations.*.content' => ['required', 'string'],

            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:blog_categories,id'],

            'status' => ['nullable', 'integer', 'in:'.implode(',', PostStatus::getValues())],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
