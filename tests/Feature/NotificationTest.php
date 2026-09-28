<?php

namespace Tests\Feature;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRideoraData;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use CreatesRideoraData;
    use RefreshDatabase;

    public function test_dropdown_reports_unread_count_and_items(): void
    {
        $user = $this->makeCustomer();
        Notification::notify($user->id, 'Booking created', 'Your booking is held.', Notification::TYPE_BOOKING, '/my-bookings');
        Notification::notify($user->id, 'Payment due', 'Complete the payment.', Notification::TYPE_PAYMENT, null);

        $response = $this->actingAs($user)->getJson(route('notifications.dropdown'));

        $response->assertOk();
        $response->assertJsonCount(2, 'items');
        $response->assertJsonPath('unread', 2);
        $response->assertJsonPath('items.0.is_read', false);
        // Items always expose the endpoint the navbar uses to mark them read.
        $response->assertJsonStructure(['items' => [['id', 'title', 'message', 'type', 'icon', 'is_read', 'time', 'link', 'read_url']]]);
    }

    public function test_marking_a_single_notification_as_read(): void
    {
        $user = $this->makeCustomer();
        $notification = Notification::notify($user->id, 'Booking created', 'Held.', Notification::TYPE_BOOKING, '/my-bookings');
        $other = Notification::notify($user->id, 'Payment due', 'Due.', Notification::TYPE_PAYMENT, null);

        $response = $this->actingAs($user)->postJson(route('notifications.read', $notification));

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('unread', 1);
        $response->assertJsonPath('redirect', '/my-bookings');

        $this->assertTrue($notification->fresh()->is_read);
        $this->assertFalse($other->fresh()->is_read);
    }

    public function test_marking_all_notifications_as_read(): void
    {
        $user = $this->makeCustomer();
        Notification::notify($user->id, 'One', 'a');
        Notification::notify($user->id, 'Two', 'b');
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Three',
            'message' => 'c',
            'type' => Notification::TYPE_PAYMENT,
            'link' => null,
            'is_read' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('notifications.read-all'));

        $response->assertOk();
        $response->assertJsonPath('unread', 0);

        $this->assertSame(0, $user->notifications()->unread()->count());
        // Already read rows stay read and untouched.
        $this->assertSame(3, $user->notifications()->count());
    }

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = $this->makeCustomer();
        $intruder = $this->makeCustomer();
        $notification = Notification::notify($owner->id, 'Private', 'Not yours.', Notification::TYPE_BOOKING, '/my-bookings');

        $this->actingAs($intruder)->postJson(route('notifications.read', $notification))->assertForbidden();
        $this->actingAs($intruder)->postJson(route('notifications.read-all'))->assertOk();

        $this->assertFalse($notification->fresh()->is_read);
    }

    public function test_notifications_page_only_offers_actions_that_apply(): void
    {
        $user = $this->makeCustomer();
        Notification::notify($user->id, 'Unread one', 'a', Notification::TYPE_BOOKING, '/my-bookings');
        Notification::notify($user->id, 'Unread two', 'b', Notification::TYPE_PAYMENT, null);

        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('2 unread', false);
        // The bulk action is only offered while something is unread.
        $response->assertSee('Mark all as read', false);
        $response->assertSee('Mark read', false);

        $this->actingAs($user)->post(route('notifications.read-all'))->assertRedirect();

        $after = $this->actingAs($user)->get(route('notifications.index'));
        $after->assertOk();
        $after->assertSee('0 unread', false);
        $after->assertDontSee('Mark all as read', false);
        $after->assertDontSee('Mark read', false);
    }
}
