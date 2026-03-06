<?php

namespace Modules\Rental\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'car' => new CarResource($this->whenLoaded('car')),
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ]),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'pickup_location' => new LocationResource($this->whenLoaded('pickupLocation')),
            'dropoff_location' => new LocationResource($this->whenLoaded('dropoffLocation')),
            'pickup_date' => $this->pickup_date?->toDateTimeString(),
            'dropoff_date' => $this->dropoff_date?->toDateTimeString(),
            'days' => $this->days,
            'price_tier' => $this->price_tier?->meta(),
            'price_per_day' => $this->price_per_day,
            'base_price' => $this->base_price,
            'extras_total' => $this->extras_total,
            'discount' => $this->discount,
            'total_price' => $this->total_price,
            'deposit' => $this->deposit,
            'note' => $this->note,
            'coupon_code' => $this->coupon_code,
            'status' => $this->status?->meta(),
            'payment_status' => $this->payment_status?->meta(),
            'paid_amount' => $this->paid_amount,
            'extras' => BookingExtraResource::collection($this->whenLoaded('extras')),
            'created_at' => strtotime($this->created_at),
            'updated_at' => strtotime($this->updated_at),
        ];
    }
}
