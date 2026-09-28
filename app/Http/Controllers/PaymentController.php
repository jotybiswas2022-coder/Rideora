<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\NotificationService;
use App\Support\FileUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    /**
     * Manual payment instructions + submission form for a booking.
     */
    public function create(Booking $booking): View|RedirectResponse
    {
        $this->authorize('view', $booking);

        if ($booking->payment_status === Booking::PAYMENT_PAID) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', 'This booking is already paid and confirmed.');
        }

        $booking->load(['vehicle.category', 'vehicle.images', 'vehicle.primaryImage', 'payments.paymentMethod']);

        $paymentMethods = PaymentMethod::query()->active()->orderBy('name')->get();

        return view('frontend.payments.create', [
            'booking' => $booking,
            'paymentMethods' => $paymentMethods,
            'latestPayment' => $booking->payments->first(),
        ]);
    }

    /**
     * Store the manual payment submission (transaction id + screenshot).
     */
    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $booking = Booking::findOrFail($request->integer('booking_id'));

        $this->authorize('view', $booking);

        if (! $booking->canBePaid()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'This booking cannot accept a new payment right now.');
        }

        $proofPath = $request->hasFile('payment_proof')
            ? FileUploader::store($request->file('payment_proof'), 'payments')
            : null;

        $payment = DB::transaction(function () use ($request, $booking, $proofPath) {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'payment_method_id' => $request->integer('payment_method_id'),
                'amount' => (float) $request->input('amount'),
                'transaction_id' => $request->string('transaction_id')->trim()->value(),
                'payment_proof' => $proofPath,
                'status' => Payment::STATUS_PENDING,
                'admin_note' => null,
            ]);

            $booking->update([
                'payment_status' => Booking::PAYMENT_PENDING,
                'booking_status' => Booking::STATUS_PAYMENT_SUBMITTED,
            ]);

            return $payment;
        });

        $this->notifications->paymentSubmitted($booking, $payment);

        return redirect()
            ->route('payments.success', $payment)
            ->with('success', 'Payment submitted successfully. Our team will verify it shortly.');
    }

    /**
     * Confirmation screen after a manual payment is submitted.
     */
    public function success(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['paymentMethod', 'booking.vehicle.images', 'booking.vehicle.primaryImage']);

        return view('frontend.payments.success', compact('payment'));
    }
}
