<?php

namespace Modules\Rental\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Rental\Enums\BookingStatus;
use Modules\Rental\Enums\PaymentStatus;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_id' => ['nullable', 'integer', 'exists:rental_cars,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'pickup_location_id' => ['nullable', 'integer', 'exists:rental_locations,id'],
            'dropoff_location_id' => ['nullable', 'integer', 'exists:rental_locations,id'],
            'pickup_date' => ['nullable', 'date'],
            'dropoff_date' => ['nullable', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'coupon_code' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'integer', 'in:'.implode(',', BookingStatus::getValues())],
            'payment_status' => ['nullable', 'integer', 'in:'.implode(',', PaymentStatus::getValues())],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],

            'extra_ids' => ['nullable', 'array'],
            'extra_ids.*' => ['integer', 'exists:rental_extras,id'],
        ];
    }
}
