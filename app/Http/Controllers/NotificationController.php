<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(15);

        return view('frontend.notifications.index', compact('notifications'));
    }

    /**
     * JSON feed powering the navbar bell dropdown.
     */
    public function dropdown(): JsonResponse
    {
        $user = Auth::user();

        $items = $user->notifications()->take(8)->get()->map(fn (Notification $notification) => [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
            'icon' => bi_icon($notification->icon()),
            'is_read' => $notification->is_read,
            'time' => $notification->created_at->diffForHumans(),
            'link' => $notification->link ?? route('notifications.index'),
            'read_url' => route('notifications.read', $notification),
        ]);

        return response()->json([
            'unread' => $user->notifications()->unread()->count(),
            'items' => $items,
        ]);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread' => Auth::user()->notifications()->unread()->count(),
                'redirect' => $notification->link,
            ]);
        }

        return $notification->link
            ? redirect()->to($notification->link)
            : redirect()->route('notifications.index');
    }

    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        Auth::user()->notifications()->unread()->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'unread' => 0]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
