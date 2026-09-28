<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function index(): View
    {
        return view('frontend.contact');
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email:filter', 'max:180'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]{6,30}$/'],
            'subject' => ['required', 'string', 'min:3', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'phone.regex' => 'Enter a valid phone number.',
        ]);

        // Mail is written to the configured mailer (log driver in local development).
        Log::channel(config('logging.default'))->info('Rideora contact enquiry', $data);

        $this->notifications->notifyAdmins(
            'New contact enquiry',
            $data['name'].' ('.$data['email'].') wrote about "'.$data['subject'].'".'
        );

        return back()->with('success', 'Thanks for reaching out! Our team will get back to you within one business day.');
    }
}
