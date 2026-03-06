<?php

namespace Modules\Rental\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Rental\Enums\BodyType;
use Modules\Rental\Enums\FuelType;
use Modules\Rental\Enums\Transmission;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['nullable', 'array'],
            'translations.*.description' => ['nullable', 'string'],

            'category_id' => ['nullable', 'integer', 'exists:rental_car_categories,id'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'transmission' => ['required', 'integer', 'in:'.implode(',', Transmission::getValues())],
            'fuel_type' => ['required', 'integer', 'in:'.implode(',', FuelType::getValues())],
            'body_type' => ['required', 'integer', 'in:'.implode(',', BodyType::getValues())],
            'seats' => ['nullable', 'integer', 'min:1', 'max:50'],
            'doors' => ['nullable', 'integer', 'min:1', 'max:10'],
            'engine' => ['nullable', 'string', 'max:100'],
            'price_daily' => ['required', 'numeric', 'min:0'],
            'price_weekly' => ['nullable', 'numeric', 'min:0'],
            'price_monthly' => ['nullable', 'numeric', 'min:0'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],

            'badge_ids' => ['nullable', 'array'],
            'badge_ids.*' => ['integer', 'exists:rental_badges,id'],

            'extra_ids' => ['nullable', 'array'],

            'avatar_id' => ['nullable', 'integer', 'exists:files,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string'],
        ];
    }
}
