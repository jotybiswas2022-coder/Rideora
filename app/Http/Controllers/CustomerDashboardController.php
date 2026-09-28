<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $stats = [
            'total' => Booking::where('user_id', $userId)->count(),
            'pending_payments' => Booking::where('user_id', $userId)
                ->whereIn('payment_status', [Booking::PAYMENT_UNPAID, Booking::PAYMENT_PENDING])->count(),
            'confirmed' => Booking::where('user_id', $userId)
                ->whereIn('booking_status', [Booking::STATUS_CONFIRMED, Booking::STATUS_ONGOING])->count(),
            'completed' => Booking::where('user_id', $userId)->where('booking_status', Booking::STATUS_COMPLETED)->count(),
            'cancelled' => Booking::where('user_id', $userId)->where('booking_status', Booking::STATUS_CANCELLED)->count(),
            'spent' => (float) Booking::where('user_id', $userId)->where('payment_status', Booking::PAYMENT_PAID)->sum('total_amount'),
        ];

        $recentBookings = Booking::query()
            ->where('user_id', $userId)
            ->with(['vehicle.images', 'vehicle.primaryImage', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        $reviewableBookings = Booking::query()
            ->where('user_id', $userId)
            ->where('booking_status', Booking::STATUS_COMPLETED)
            ->whereDoesntHave('review')
            ->with('vehicle.images')
            ->latest()
            ->take(3)
            ->get();

        $unreadNotifications = Auth::user()->notifications()->unread()->take(5)->get();

        return view('frontend.dashboard', compact('stats', 'recentBookings', 'reviewableBookings', 'unreadNotifications'));
    }
}
