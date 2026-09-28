<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:'.implode(',', Review::STATUSES)],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $reviews = Review::query()
            ->with(['user', 'vehicle', 'booking'])
            ->when($filters['q'] ?? null, function ($query, $value) {
                $query->where(function ($inner) use ($value) {
                    $inner->where('comment', 'like', '%'.$value.'%')
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$value.'%'))
                        ->orWhereHas('vehicle', fn ($vehicle) => $vehicle->where('name', 'like', '%'.$value.'%'));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['rating'] ?? null, fn ($query, $value) => $query->where('rating', $value))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending' => Review::where('status', Review::STATUS_PENDING)->count(),
            'approved' => Review::where('status', Review::STATUS_APPROVED)->count(),
            'rejected' => Review::where('status', Review::STATUS_REJECTED)->count(),
            'average' => round((float) Review::where('status', Review::STATUS_APPROVED)->avg('rating'), 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'filters', 'stats'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => Review::STATUS_APPROVED]);

        Notification::notify(
            $review->user_id,
            'Review published',
            'Thanks for the feedback! Your review of '.$review->vehicle->name.' is now live on Rideora.',
            Notification::TYPE_REVIEW,
            route('vehicles.show', $review->vehicle)
        );

        return back()->with('success', 'Review approved and now visible on the vehicle page.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['status' => Review::STATUS_REJECTED]);

        return back()->with('success', 'Review rejected and hidden from the website.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
