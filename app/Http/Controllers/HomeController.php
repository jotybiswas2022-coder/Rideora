<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $categories = VehicleCategory::query()
            ->active()
            ->withCount(['vehicles' => fn ($query) => $query->listable()])
            ->orderBy('name')
            ->get();

        $featuredVehicles = Vehicle::query()
            ->listable()
            ->with(['category', 'images', 'primaryImage'])
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $testimonials = Review::query()
            ->approved()
            ->where('rating', '>=', 4)
            ->with(['user', 'vehicle'])
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'vehicles' => Vehicle::query()->listable()->count(),
            'customers' => User::query()->where('is_admin', false)->count(),
            'bookings' => Booking::query()->whereIn('booking_status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])->count(),
            'reviews' => Review::query()->approved()->count(),
        ];

        return view('frontend.home', compact('categories', 'featuredVehicles', 'testimonials', 'stats'));
    }

    public function about(): View
    {
        $stats = [
            'vehicles' => Vehicle::query()->listable()->count(),
            'customers' => User::query()->where('is_admin', false)->count(),
            'completed' => Booking::query()->where('booking_status', Booking::STATUS_COMPLETED)->count(),
        ];

        return view('frontend.about', compact('stats'));
    }
}
