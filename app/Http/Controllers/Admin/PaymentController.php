<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:'.implode(',', Payment::STATUSES)],
            'method' => ['nullable', 'integer', 'exists:payment_methods,id'],
        ]);

        $payments = Payment::query()
            ->with(['user', 'booking.vehicle', 'paymentMethod', 'verifier'])
            ->when($filters['q'] ?? null, function ($query, $value) {
                $query->where(function ($inner) use ($value) {
                    $inner->where('transaction_id', 'like', '%'.$value.'%')
                        ->orWhereHas('booking', fn ($booking) => $booking->where('booking_code', 'like', '%'.$value.'%'))
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('name', 'like', '%'.$value.'%')
                            ->orWhere('email', 'like', '%'.$value.'%'));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['method'] ?? null, fn ($query, $value) => $query->where('payment_method_id', $value))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'verified' => Payment::where('status', Payment::STATUS_VERIFIED)->count(),
            'rejected' => Payment::where('status', Payment::STATUS_REJECTED)->count(),
            'verified_amount' => (float) Payment::where('status', Payment::STATUS_VERIFIED)->sum('amount'),
        ];

        $methods = \App\Models\PaymentMethod::orderBy('name')->get();

        return view('admin.payments.index', compact('payments', 'filters', 'stats', 'methods'));
    }

    public function show(Payment $payment): View
    {
        $payment->load([
            'user',
            'paymentMethod',
            'verifier',
            'booking.vehicle.images',
            'booking.vehicle.primaryImage',
            'booking.payments.paymentMethod',
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('verify', $payment);

        if ($payment->status === Payment::STATUS_VERIFIED) {
            return back()->with('error', 'This payment is already verified.');
        }

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $payment) {
            $payment->update([
                'status' => Payment::STATUS_VERIFIED,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'admin_note' => $request->filled('admin_note')
                    ? $request->string('admin_note')->trim()->value()
                    : $payment->admin_note,
            ]);

            $payment->booking->update([
                'payment_status' => Booking::PAYMENT_PAID,
                'booking_status' => Booking::STATUS_CONFIRMED,
            ]);
        });

        $this->notifications->paymentVerified($payment->booking, $payment);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Payment verified and booking '.$payment->booking->booking_code.' confirmed.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('verify', $payment);

        $data = $request->validate([
            'admin_note' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'admin_note.required' => 'Please provide a reason so the customer knows what to fix.',
            'admin_note.min' => 'The rejection reason must be at least 5 characters.',
        ]);

        DB::transaction(function () use ($data, $payment) {
            $payment->update([
                'status' => Payment::STATUS_REJECTED,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'admin_note' => $data['admin_note'],
            ]);

            // The booking goes back to pending so the customer can submit a valid payment.
            $payment->booking->update([
                'payment_status' => Booking::PAYMENT_REJECTED,
                'booking_status' => Booking::STATUS_PENDING,
            ]);
        });

        $this->notifications->paymentRejected($payment->booking, $payment);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Payment rejected. The customer has been notified.');
    }
}
