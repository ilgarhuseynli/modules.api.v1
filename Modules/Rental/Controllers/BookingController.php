<?php

namespace Modules\Rental\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Rental\Models\Booking;
use Modules\Rental\Requests\StoreBookingRequest;
use Modules\Rental\Requests\UpdateBookingRequest;
use Modules\Rental\Resources\BookingResource;
use Modules\Rental\Services\BookingService;

class BookingController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Booking::$sortable);

        $bookings = Booking::with('car.translations', 'customer', 'extras', 'pickupLocation.translations', 'dropoffLocation.translations')
            ->filter($request->only(['status', 'payment_status', 'car_id', 'customer_id', 'date_from', 'date_to']))
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return BookingResource::collection($bookings);
    }

    public function store(StoreBookingRequest $request, BookingService $bookingService): mixed
    {
        $booking = $bookingService->create($request->validated());

        return response()->json(['id' => $booking->id]);
    }

    public function show(Booking $booking): mixed
    {
        $booking->load('car.translations', 'customer', 'extras', 'pickupLocation.translations', 'dropoffLocation.translations');

        return response()->json(new BookingResource($booking));
    }

    public function update(UpdateBookingRequest $request, Booking $booking, BookingService $bookingService): mixed
    {
        $bookingService->update($booking, $request->validated());

        return response()->json(['id' => $booking->id]);
    }

    public function destroy(Booking $booking): mixed
    {
        $booking->delete();

        return response()->noContent();
    }
}
