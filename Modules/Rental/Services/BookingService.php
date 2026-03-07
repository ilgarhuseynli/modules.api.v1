<?php

namespace Modules\Rental\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Rental\Enums\PriceTier;
use Modules\Rental\Models\Booking;
use Modules\Rental\Models\Car;
use Modules\Rental\Models\Extra;
use Modules\Rental\Models\Location;

class BookingService
{
    public function create(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $car = Car::findOrFail($data['car_id']);

            $pickupDate = Carbon::parse($data['pickup_date']);
            $dropoffDate = Carbon::parse($data['dropoff_date']);
            $days = max(1, $pickupDate->diffInDays($dropoffDate));

            $pricePerDay = $car->getPriceForDays($days);
            $priceTier = $this->determinePriceTier($days);
            $basePrice = bcmul($pricePerDay, (string) $days, 2);

            $extrasTotal = '0.00';
            $extraSnapshots = [];

            if (! empty($data['extra_ids'])) {
                [$extrasTotal, $extraSnapshots] = $this->calculateExtras($data['extra_ids'], $days);
            }

            $locationsTotal = $this->calculateLocationsTotal(
                $data['pickup_location_id'] ?? null,
                $data['dropoff_location_id'] ?? null,
            );

            $discount = $data['discount'] ?? '0.00';
            $totalPrice = bcsub(bcadd(bcadd($basePrice, $extrasTotal, 2), $locationsTotal, 2), $discount, 2);

            $booking = Booking::create([
                'car_id' => $data['car_id'],
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'pickup_location_id' => $data['pickup_location_id'] ?? null,
                'dropoff_location_id' => $data['dropoff_location_id'] ?? null,
                'pickup_date' => $data['pickup_date'],
                'dropoff_date' => $data['dropoff_date'],
                'days' => $days,
                'price_tier' => $priceTier,
                'price_per_day' => $pricePerDay,
                'base_price' => $basePrice,
                'extras_total' => $extrasTotal,
                'locations_total' => $locationsTotal,
                'discount' => $discount,
                'total_price' => $totalPrice,
                'deposit' => $data['deposit'] ?? $car->deposit,
                'note' => $data['note'] ?? null,
                'coupon_code' => $data['coupon_code'] ?? null,
                'status' => $data['status'] ?? 1,
                'payment_status' => $data['payment_status'] ?? 1,
                'paid_amount' => $data['paid_amount'] ?? 0,
            ]);

            foreach ($extraSnapshots as $snapshot) {
                $booking->extras()->create($snapshot);
            }

            return $booking->load('car', 'extras', 'pickupLocation', 'dropoffLocation', 'customer');
        });
    }

    public function update(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $carChanged = isset($data['car_id']) && (int) $data['car_id'] !== $booking->car_id;
            $carId = $data['car_id'] ?? $booking->car_id;

            $pickupDate = Carbon::parse($data['pickup_date'] ?? $booking->pickup_date);
            $dropoffDate = Carbon::parse($data['dropoff_date'] ?? $booking->dropoff_date);
            $days = max(1, $pickupDate->diffInDays($dropoffDate));

            $priceTier = $this->determinePriceTier($days);

            $car = Car::findOrFail($carId);
            
            if ($carChanged) {
                $pricePerDay = $car->getPriceForDays($days);
            }elseif ($priceTier != $booking->price_tier){
                $pricePerDay = $car->getPriceForDays($days);
            } else {
                $pricePerDay = $booking->price_per_day;
            }

            $basePrice = bcmul((string) $pricePerDay, (string) $days, 2);

            $extrasTotal = '0.00';
            $extraSnapshots = [];

            if (array_key_exists('extra_ids', $data)) {
                $booking->extras()->delete();

                if (! empty($data['extra_ids'])) {
                    [$extrasTotal, $extraSnapshots] = $this->calculateExtras($data['extra_ids'], $days);
                }
            } else {
                $extrasTotal = $booking->extras_total;
            }

            $locationsTotal = $this->calculateLocationsTotal(
                $data['pickup_location_id'] ?? $booking->pickup_location_id,
                $data['dropoff_location_id'] ?? $booking->dropoff_location_id,
            );

            $discount = $data['discount'] ?? $booking->discount;
            $totalPrice = bcsub(bcadd(bcadd($basePrice, $extrasTotal, 2), $locationsTotal, 2), $discount, 2);

            $booking->update([
                'car_id' => $carId,
                'customer_name' => $data['customer_name'] ?? $booking->customer_name,
                'customer_phone' => $data['customer_phone'] ?? $booking->customer_phone,
                'pickup_location_id' => $data['pickup_location_id'] ?? $booking->pickup_location_id,
                'dropoff_location_id' => $data['dropoff_location_id'] ?? $booking->dropoff_location_id,
                'pickup_date' => $data['pickup_date'] ?? $booking->pickup_date,
                'dropoff_date' => $data['dropoff_date'] ?? $booking->dropoff_date,
                'days' => $days,
                'price_tier' => $priceTier,
                'price_per_day' => $pricePerDay,
                'base_price' => $basePrice,
                'extras_total' => $extrasTotal,
                'locations_total' => $locationsTotal,
                'discount' => $discount,
                'total_price' => $totalPrice,
                'deposit' => $data['deposit'] ?? $booking->deposit,
                'note' => $data['note'] ?? $booking->note,
                'coupon_code' => $data['coupon_code'] ?? $booking->coupon_code,
                'status' => $data['status'] ?? $booking->status,
                'payment_status' => $data['payment_status'] ?? $booking->payment_status,
                'paid_amount' => $data['paid_amount'] ?? $booking->paid_amount,
            ]);

            foreach ($extraSnapshots as $snapshot) {
                $booking->extras()->create($snapshot);
            }

            return $booking->load('car', 'extras', 'pickupLocation', 'dropoffLocation', 'customer');
        });
    }

    private function calculateLocationsTotal(?int $pickupLocationId, ?int $dropoffLocationId): string
    {
        $total = '0.00';

        $ids = array_filter([$pickupLocationId, $dropoffLocationId]);
        if (empty($ids)) {
            return $total;
        }

        $locations = Location::whereIn('id', $ids)->get()->keyBy('id');

        if ($pickupLocationId && $locations->has($pickupLocationId)) {
            $total = bcadd($total, (string) ($locations->get($pickupLocationId)->price ?? '0'), 2);
        }

        if ($dropoffLocationId && $locations->has($dropoffLocationId)) {
            $total = bcadd($total, (string) ($locations->get($dropoffLocationId)->price ?? '0'), 2);
        }

        return $total;
    }

    private function determinePriceTier(int $days): PriceTier
    {
        if ($days >= 30) {
            return PriceTier::MONTHLY;
        }

        if ($days >= 7) {
            return PriceTier::WEEKLY;
        }

        return PriceTier::DAILY;
    }

    /**
     * @return array{0: string, 1: array}
     */
    private function calculateExtras(array $extraIds, int $days): array
    {
        $extras = Extra::with('translations')->whereIn('id', $extraIds)->get();

        $total = '0.00';
        $snapshots = [];

        foreach ($extras as $extra) {
            $price = $extra->price;
            $priceType = $extra->price_type;

            $extraTotal = $priceType === \Modules\Rental\Enums\PriceType::PER_DAY
                ? bcmul((string) $price, (string) $days, 2)
                : (string) $price;

            $total = bcadd($total, $extraTotal, 2);

            $name = $extra->translations->where('locale', app()->getLocale())->first()?->name
                ?? $extra->translations->first()?->name
                ?? '';

            $snapshots[] = [
                'extra_id' => $extra->id,
                'name' => $name,
                'price' => $price,
                'price_type' => $priceType,
                'total' => $extraTotal,
            ];
        }

        return [$total, $snapshots];
    }
}
