<?php

namespace Modules\Rental\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Rental\Enums\BookingStatus;
use Modules\Rental\Enums\PaymentStatus;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_id' => ['required', 'integer', 'exists:rental_cars,id'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'pickup_location_id' => ['nullable', 'integer', 'exists:rental_locations,id'],
            'dropoff_location_id' => ['nullable', 'integer', 'exists:rental_locations,id'],
            'pickup_date' => ['required', 'date'],
            'dropoff_date' => ['required', 'date', 'after:pickup_date'],
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
