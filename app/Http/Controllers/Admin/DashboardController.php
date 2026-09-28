<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'available_vehicles' => Vehicle::where('status', Vehicle::STATUS_AVAILABLE)->count(),
            'total_customers' => User::where('is_admin', false)->count(),
            'total_bookings' => Booking::count(),
            'pending_payments' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'confirmed_bookings' => Booking::whereIn('booking_status', [Booking::STATUS_CONFIRMED, Booking::STATUS_ONGOING])->count(),
            'completed_rentals' => Booking::where('booking_status', Booking::STATUS_COMPLETED)->count(),
            'total_revenue' => (float) Payment::where('status', Payment::STATUS_VERIFIED)->sum('amount'),
            'vehicles_on_rent' => Booking::where('booking_status', Booking::STATUS_ONGOING)->count(),
            'pending_reviews' => Review::where('status', Review::STATUS_PENDING)->count(),
        ];

        $recentBookings = Booking::query()
            ->with(['user', 'vehicle'])
            ->latest()
            ->take(6)
            ->get();

        $pendingPayments = Payment::query()
            ->pending()
            ->with(['user', 'booking.vehicle', 'paymentMethod'])
            ->latest()
            ->take(6)
            ->get();

        $statusStats = Booking::query()
            ->select('booking_status', DB::raw('count(*) as total'))
            ->groupBy('booking_status')
            ->pluck('total', 'booking_status')
            ->all();

        // Revenue for the last 6 months from verified payments. Grouped in PHP so the
        // query stays portable across database drivers (MySQL, PostgreSQL, SQLite).
        $revenueByMonth = Payment::query()
            ->where('status', Payment::STATUS_VERIFIED)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['amount', 'created_at'])
            ->groupBy(fn (Payment $payment) => $payment->created_at->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $months[$key] = round((float) ($revenueByMonth[$key] ?? 0), 2);
        }

        $topVehicles = Vehicle::query()
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'pendingPayments',
            'statusStats',
            'months',
            'topVehicles'
        ));
    }
}
