<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly NotificationService $notifications,
    ) {
    }

    /**
     * Customer booking history with optional status filter.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $bookings = Booking::query()
            ->where('user_id', Auth::id())
            ->with(['vehicle.images', 'vehicle.primaryImage', 'payment'])
            ->when(in_array($status, Booking::STATUSES, true), fn ($query) => $query->where('booking_status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'all' => Booking::where('user_id', Auth::id())->count(),
            'pending' => Booking::where('user_id', Auth::id())->where('booking_status', Booking::STATUS_PENDING)->count(),
            'payment_submitted' => Booking::where('user_id', Auth::id())->where('booking_status', Booking::STATUS_PAYMENT_SUBMITTED)->count(),
            'confirmed' => Booking::where('user_id', Auth::id())->where('booking_status', Booking::STATUS_CONFIRMED)->count(),
            'completed' => Booking::where('user_id', Auth::id())->where('booking_status', Booking::STATUS_COMPLETED)->count(),
            'cancelled' => Booking::where('user_id', Auth::id())
                ->whereIn('booking_status', [Booking::STATUS_CANCELLED, Booking::STATUS_REJECTED])->count(),
        ];

        return view('frontend.bookings.index', compact('bookings', 'counts', 'status'));
    }

    /**
     * Booking form with a server side quote preview.
     */
    public function create(Request $request, Vehicle $vehicle): View|RedirectResponse
    {
        abort_if($vehicle->status === Vehicle::STATUS_INACTIVE, 404);

        $pickupDate = $request->query('pickup_date', now()->addDay()->toDateString());
        $returnDate = $request->query('return_date', now()->addDays(2)->toDateString());
        $pickupTime = $request->query('pickup_time', '10:00');
        $returnTime = $request->query('return_time', '10:00');

        $quote = null;
        $available = $this->bookingService->isAvailable($vehicle, $pickupDate, $returnDate);

        try {
            $quote = $this->bookingService->quote($vehicle, $pickupDate, $pickupTime, $returnDate, $returnTime);
        } catch (\InvalidArgumentException) {
            $quote = null;
        }

        $vehicle->load(['category', 'images', 'primaryImage']);

        return view('frontend.bookings.create', compact(
            'vehicle',
            'quote',
            'available',
            'pickupDate',
            'returnDate',
            'pickupTime',
            'returnTime'
        ));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $vehicle = $request->vehicle();

        if (in_array($vehicle->status, [Vehicle::STATUS_INACTIVE, Vehicle::STATUS_MAINTENANCE], true)) {
            return back()->withInput()->with('error', 'This vehicle is not available for booking right now.');
        }

        if (! $this->bookingService->isAvailable($vehicle, $request->pickupDate(), $request->returnDate())) {
            return back()
                ->withInput()
                ->with('error', 'Sorry, this vehicle was booked for part of the selected period. Please choose other dates.');
        }

        $booking = $this->bookingService->createBooking($vehicle, Auth::id(), [
            'pickup_location' => $request->string('pickup_location')->trim()->value(),
            'dropoff_location' => $request->filled('dropoff_location')
                ? $request->string('dropoff_location')->trim()->value()
                : $request->string('pickup_location')->trim()->value(),
            'pickup_date' => $request->pickupDate(),
            'pickup_time' => (string) $request->input('pickup_time'),
            'return_date' => $request->returnDate(),
            'return_time' => (string) $request->input('return_time'),
            'customer_note' => $request->filled('customer_note')
                ? $request->string('customer_note')->trim()->value()
                : null,
        ]);

        $this->notifications->bookingCreated($booking);

        return redirect()
            ->route('bookings.payment', $booking)
            ->with('success', 'Booking '.$booking->booking_code.' created. Please complete the payment to confirm it.');
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load([
            'vehicle.category',
            'vehicle.images',
            'vehicle.primaryImage',
            'payments.paymentMethod',
            'review',
        ]);

        return view('frontend.bookings.show', [
            'booking' => $booking,
            'payment' => $booking->payments->first(),
        ]);
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        $booking->update(['booking_status' => Booking::STATUS_CANCELLED]);

        if ($booking->payment_status === Booking::PAYMENT_PENDING) {
            $booking->update(['payment_status' => Booking::PAYMENT_REJECTED]);
            $booking->payments()->where('status', 'pending')->update([
                'status' => 'rejected',
                'admin_note' => 'Booking cancelled by the customer.',
            ]);
        }

        $this->notifications->bookingStatusChanged($booking, Booking::STATUS_CANCELLED);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking '.$booking->booking_code.' has been cancelled.');
    }
}
