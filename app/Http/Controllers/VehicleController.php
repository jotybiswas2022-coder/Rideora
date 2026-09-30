<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:150'],
            'brand' => ['nullable', 'string', 'max:120'],
            'fuel_type' => ['nullable', 'string', 'max:40'],
            'transmission' => ['nullable', 'string', 'max:40'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:60'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:160'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc,name_asc,rating'],
            'pickup_date' => ['nullable', 'date_format:Y-m-d'],
            'return_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:pickup_date'],
        ]);

        // `average_rating` is what the vehicle card reads, so it is always eager loaded.
        $query = Vehicle::query()->listable()->with(['category', 'images', 'primaryImage'])
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('status', 'approved')], 'rating');

        $query->when($filters['q'] ?? null, function ($builder, $value) {
            $builder->where(function ($inner) use ($value) {
                $inner->where('name', 'like', '%'.$value.'%')
                    ->orWhere('brand', 'like', '%'.$value.'%')
                    ->orWhere('model', 'like', '%'.$value.'%')
                    ->orWhere('registration_number', 'like', '%'.$value.'%');
            });
        });

        $query->when($filters['category'] ?? null, fn ($builder, $value) => $builder
            ->whereHas('category', fn ($category) => $category->where('slug', $value)));

        $query->when($filters['brand'] ?? null, fn ($builder, $value) => $builder->where('brand', $value));
        $query->when($filters['fuel_type'] ?? null, fn ($builder, $value) => $builder->where('fuel_type', $value));
        $query->when($filters['transmission'] ?? null, fn ($builder, $value) => $builder->where('transmission', $value));
        $query->when($filters['seats'] ?? null, fn ($builder, $value) => $builder->where('seats', '>=', $value));
        $query->when($filters['min_price'] ?? null, fn ($builder, $value) => $builder->where('price_per_day', '>=', $value));
        $query->when($filters['max_price'] ?? null, fn ($builder, $value) => $builder->where('price_per_day', '<=', $value));

        $query->when($filters['location'] ?? null, fn ($builder, $value) => $builder
            ->where('location', 'like', '%'.$value.'%'));

        // Only vehicles without an overlapping active booking for the requested window.
        if (! empty($filters['pickup_date']) && ! empty($filters['return_date'])) {
            $pickup = $filters['pickup_date'];
            $return = $filters['return_date'];

            $query->whereDoesntHave('bookings', function ($builder) use ($pickup, $return) {
                $builder->whereIn('booking_status', Booking::BLOCKING_STATUSES)
                    ->where('pickup_date', '<', $return)
                    ->where('return_date', '>', $pickup);
            });
        }

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price_per_day'),
            'price_desc' => $query->orderByDesc('price_per_day'),
            'name_asc' => $query->orderBy('name'),
            'rating' => $query->orderByDesc('average_rating'),
            default => $query->orderByDesc('created_at'),
        };

        $vehicles = $query->paginate(9)->withQueryString();

        $categories = VehicleCategory::query()->active()->orderBy('name')->get();
        $brands = Vehicle::query()->listable()->distinct()->orderBy('brand')->pluck('brand');
        $locations = Vehicle::query()->listable()->whereNotNull('location')->distinct()->orderBy('location')->pluck('location');

        return view('frontend.vehicles.index', compact('vehicles', 'categories', 'brands', 'locations', 'filters'));
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        abort_if($vehicle->status === Vehicle::STATUS_INACTIVE && ! $isAdmin, 404);

        $vehicle->load(['category', 'images', 'primaryImage']);

        $reviews = $vehicle->approvedReviews()->with('user')->take(12)->get();

        $requestedPickup = $request->query('pickup_date');
        $requestedReturn = $request->query('return_date');

        $pickupDate = $requestedPickup ?: now()->addDay()->toDateString();
        $returnDate = $requestedReturn ?: now()->addDays(2)->toDateString();
        $pickupTime = (string) $request->query('pickup_time', '10:00');
        $returnTime = (string) $request->query('return_time', '10:00');

        // A completed rental the signed in customer has not reviewed yet.
        $reviewableBooking = auth()->check() && ! auth()->user()->isAdmin()
            ? Booking::query()
                ->where('user_id', auth()->id())
                ->where('vehicle_id', $vehicle->id)
                ->where('booking_status', Booking::STATUS_COMPLETED)
                ->whereDoesntHave('review')
                ->latest()
                ->first()
            : null;

        $quote = null;
        $isAvailableForRequest = null;

        if ($requestedPickup && $requestedReturn) {
            $isAvailableForRequest = $this->bookingService->isAvailable($vehicle, $requestedPickup, $requestedReturn);

            try {
                $quote = $this->bookingService->quote(
                    $vehicle,
                    $requestedPickup,
                    $pickupTime,
                    $requestedReturn,
                    $returnTime
                );
            } catch (\InvalidArgumentException) {
                $quote = null;
            }
        }

        $relatedVehicles = Vehicle::query()
            ->listable()
            ->where('id', '!=', $vehicle->id)
            ->where('category_id', $vehicle->category_id)
            ->with(['images', 'primaryImage'])
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->take(3)
            ->get();

        return view('frontend.vehicles.show', compact(
            'vehicle',
            'reviews',
            'relatedVehicles',
            'quote',
            'isAvailableForRequest',
            'requestedPickup',
            'requestedReturn',
            'pickupDate',
            'returnDate',
            'pickupTime',
            'returnTime',
            'reviewableBooking'
        ));
    }

    /**
     * JSON endpoint used by the booking form for live availability + price calculation.
     */
    public function availability(Request $request, Vehicle $vehicle): JsonResponse
    {
        $data = $request->validate([
            'pickup_date' => ['required', 'date_format:Y-m-d'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'return_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:pickup_date'],
            'return_time' => ['required', 'date_format:H:i'],
        ]);

        try {
            $quote = $this->bookingService->quote(
                $vehicle,
                $data['pickup_date'],
                $data['pickup_time'],
                $data['return_date'],
                $data['return_time']
            );
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'available' => false,
                'message' => $exception->getMessage(),
                'quote' => null,
            ], 422);
        }

        $available = $this->bookingService->isAvailable($vehicle, $data['pickup_date'], $data['return_date']);

        return response()->json([
            'available' => $available,
            'message' => $available
                ? 'This vehicle is available for the selected dates.'
                : 'This vehicle is already booked during part of the selected period.',
            'quote' => [
                'rental_days' => $quote['rental_days'],
                'rental_hours' => $quote['rental_hours'],
                'base_amount' => (float) $quote['base_amount'],
                'security_deposit' => (float) $quote['security_deposit'],
                'discount' => (float) $quote['discount'],
                'discount_percent' => $quote['discount_percent'],
                'total_amount' => (float) $quote['total_amount'],
                'base_amount_formatted' => bdt($quote['base_amount']),
                'security_deposit_formatted' => bdt($quote['security_deposit']),
                'discount_formatted' => bdt($quote['discount']),
                'total_amount_formatted' => bdt($quote['total_amount']),
                'duration_label' => $quote['rental_days'] > 0
                    ? $quote['rental_days'].' day(s)'.($quote['rental_hours'] > 0 ? ' + '.$quote['rental_hours'].' hour(s)' : '')
                    : $quote['rental_hours'].' hour(s)',
            ],
            'blocked_ranges' => $vehicle->blockedRanges(),
        ]);
    }
}
