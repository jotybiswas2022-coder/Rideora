<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('frontend.auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->trim()->value(),
            'email' => mb_strtolower($request->string('email')->trim()->value()),
            'phone' => $request->string('phone')->trim()->value(),
            'city' => $request->filled('city') ? $request->string('city')->trim()->value() : null,
            'address' => $request->filled('address') ? $request->string('address')->trim()->value() : null,
            'password' => $request->string('password')->value(),
            'is_admin' => false,
            'status' => 'active',
        ]);

        event(new Registered($user));

        Notification::notify(
            $user->id,
            'Welcome to Rideora',
            'Your account is ready. Browse our fleet and book your first ride whenever you are.',
            Notification::TYPE_GENERAL,
            route('vehicles.index')
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Welcome to Rideora, '.$user->name.'! Your account has been created.');
    }
}
