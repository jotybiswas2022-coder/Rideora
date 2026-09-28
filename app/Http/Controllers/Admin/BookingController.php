<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingRequest;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:'.implode(',', Booking::STATUSES)],
            'payment_status' => ['nullable', 'in:'.implode(',', Booking::PAYMENT_STATUSES)],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $bookings = Booking::query()
            ->with(['user', 'vehicle', 'payment'])
            ->when($filters['q'] ?? null, function ($query, $value) {
                $query->where(function ($inner) use ($value) {
                    $inner->where('booking_code', 'like', '%'.$value.'%')
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('name', 'like', '%'.$value.'%')
                            ->orWhere('email', 'like', '%'.$value.'%')
                            ->orWhere('phone', 'like', '%'.$value.'%'))
                        ->orWhereHas('vehicle', fn ($vehicle) => $vehicle
                            ->where('name', 'like', '%'.$value.'%')
                            ->orWhere('registration_number', 'like', '%'.$value.'%'));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('booking_status', $value))
            ->when($filters['payment_status'] ?? null, fn ($query, $value) => $query->where('payment_status', $value))
            ->when($filters['from'] ?? null, fn ($query, $value) => $query->whereDate('pickup_date', '>=', $value))
            ->when($filters['to'] ?? null, fn ($query, $value) => $query->whereDate('pickup_date', '<=', $value))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Booking::query()
            ->selectRaw('booking_status, count(*) as total')
            ->groupBy('booking_status')
            ->pluck('total', 'booking_status')
            ->all();

        return view('admin.bookings.index', compact('bookings', 'filters', 'statusCounts'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'user',
            'vehicle.category',
            'vehicle.images',
            'payments.paymentMethod',
            'payments.verifier',
            'review',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $oldStatus = $booking->booking_status;

        $booking->update([
            'booking_status' => $request->string('booking_status')->value(),
            'payment_status' => $request->string('payment_status')->value(),
            'admin_note' => $request->filled('admin_note') ? $request->string('admin_note')->trim()->value() : $booking->admin_note,
        ]);

        if ($oldStatus !== $booking->booking_status) {
            $this->notifications->bookingStatusChanged($booking, $oldStatus);
        }

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', 'Booking '.$booking->booking_code.' updated successfully.');
    }

    /**
     * Quick status change from the bookings table.
     */
    public function quickStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'booking_status' => ['required', 'in:'.implode(',', Booking::STATUSES)],
        ]);

        $oldStatus = $booking->booking_status;
        $booking->update(['booking_status' => $data['booking_status']]);

        if ($oldStatus !== $booking->booking_status) {
            $this->notifications->bookingStatusChanged($booking, $oldStatus);
        }

        return back()->with('success', 'Booking '.$booking->booking_code.' is now '.$booking->statusLabel().'.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        if (in_array($booking->booking_status, Booking::BLOCKING_STATUSES, true)) {
            return back()->with('error', 'Cancel the booking before deleting it.');
        }

        $code = $booking->booking_code;
        $booking->delete();

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking '.$code.' deleted.');
    }
}
