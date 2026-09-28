<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function index(): View
    {
        $userId = Auth::id();

        $reviews = Review::query()
            ->where('user_id', $userId)
            ->with(['vehicle.images', 'vehicle.primaryImage', 'booking'])
            ->latest()
            ->paginate(10);

        $reviewableBookings = Booking::query()
            ->where('user_id', $userId)
            ->where('booking_status', Booking::STATUS_COMPLETED)
            ->whereDoesntHave('review')
            ->with(['vehicle.images', 'vehicle.primaryImage'])
            ->latest()
            ->get();

        return view('frontend.reviews.index', compact('reviews', 'reviewableBookings'));
    }

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $booking = Booking::findOrFail($request->integer('booking_id'));

        $review = Review::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $booking->vehicle_id,
            'booking_id' => $booking->id,
            'rating' => $request->integer('rating'),
            'comment' => $request->string('comment')->trim()->value(),
            'status' => Review::STATUS_PENDING,
        ]);

        $this->notifications->notifyAdmins(
            'New review awaiting moderation',
            Auth::user()->name.' left a '.$review->rating.'-star review for '.$booking->vehicle->name.'.',
            Notification::TYPE_REVIEW,
            route('admin.reviews.index')
        );

        return redirect()
            ->route('reviews.index')
            ->with('success', 'Thank you! Your review has been submitted and is awaiting moderation.');
    }
}
