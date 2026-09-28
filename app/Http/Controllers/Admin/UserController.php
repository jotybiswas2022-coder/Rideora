<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Support\FileUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:newest,name,bookings'],
        ]);

        $customers = User::query()
            ->where('is_admin', false)
            ->withCount(['bookings', 'reviews'])
            ->when($filters['q'] ?? null, function ($query, $value) {
                $query->where(function ($inner) use ($value) {
                    $inner->where('name', 'like', '%'.$value.'%')
                        ->orWhere('email', 'like', '%'.$value.'%')
                        ->orWhere('phone', 'like', '%'.$value.'%');
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when(($filters['sort'] ?? 'newest') === 'name', fn ($query) => $query->orderBy('name'))
            ->when(($filters['sort'] ?? 'newest') === 'bookings', fn ($query) => $query->orderByDesc('bookings_count'))
            ->when(($filters['sort'] ?? 'newest') === 'newest', fn ($query) => $query->orderByDesc('created_at'))
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('customers', 'filters'));
    }

    public function show(User $user): View
    {
        abort_if($user->is_admin, 404);

        $user->loadCount(['bookings', 'reviews', 'payments']);

        $bookings = $user->bookings()
            ->with(['vehicle', 'payment'])
            ->latest()
            ->paginate(10, ['*'], 'bookings_page');

        $payments = $user->payments()
            ->with(['booking.vehicle', 'paymentMethod'])
            ->latest()
            ->take(10)
            ->get();

        $spent = (float) $user->bookings()->where('payment_status', Booking::PAYMENT_PAID)->sum('total_amount');

        return view('admin.users.show', compact('user', 'bookings', 'payments', 'spent'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);

        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);

        return back()->with('success', $user->name.' is now '.$user->status.'.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);

        if ($user->bookings()->whereIn('booking_status', Booking::BLOCKING_STATUSES)->exists()) {
            return back()->with('error', 'This customer has active bookings. Close them before deleting the account.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        FileUploader::delete($user->avatar_path);

        // Clean up payment screenshots and vehicle review images before removing the account.
        foreach ($user->payments as $payment) {
            FileUploader::delete($payment->payment_proof);
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Customer '.$name.' deleted.');
    }
}
