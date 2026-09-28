<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Booking;
use App\Support\FileUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'bookings' => $user->bookings()->count(),
            'completed' => $user->bookings()->where('booking_status', Booking::STATUS_COMPLETED)->count(),
            'reviews' => $user->reviews()->count(),
            'spent' => (float) $user->bookings()->where('payment_status', Booking::PAYMENT_PAID)->sum('total_amount'),
        ];

        return view('frontend.profile.index', compact('user', 'stats'));
    }

    public function edit(): View
    {
        return view('frontend.profile.edit', ['user' => Auth::user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $data = [
            'name' => $request->string('name')->trim()->value(),
            'email' => mb_strtolower($request->string('email')->trim()->value()),
            'phone' => $request->string('phone')->trim()->value(),
            'city' => $request->filled('city') ? $request->string('city')->trim()->value() : null,
            'address' => $request->filled('address') ? $request->string('address')->trim()->value() : null,
            'driving_license_no' => $request->filled('driving_license_no')
                ? $request->string('driving_license_no')->trim()->value()
                : null,
        ];

        DB::transaction(function () use ($request, $user, &$data) {
            if ($request->hasFile('avatar')) {
                FileUploader::delete($user->avatar_path);
                $data['avatar_path'] = FileUploader::store($request->file('avatar'), 'avatars');
            }

            if ($request->filled('password')) {
                $data['password'] = $request->string('password')->value();
            }

            $user->update($data);
        });

        return redirect()
            ->route('profile.index')
            ->with('success', $request->filled('password')
                ? 'Profile and password updated successfully.'
                : 'Profile updated successfully.');
    }
}
