<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRideoraData;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use CreatesRideoraData;
    use RefreshDatabase;

    public function test_registration_creates_a_customer_and_signs_them_in(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Nusrat Jahan',
            'email' => 'NUSRAT@example.com',
            'phone' => '+880 1711-100002',
            'city' => 'Chattogram',
            'address' => 'House 3, Road 2',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $user = User::where('email', 'nusrat@example.com')->first();

        $this->assertNotNull($user);
        $this->assertFalse($user->isAdmin());
        $this->assertSame('active', $user->status);
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id]);
        $this->assertNotSame('password123', $user->password, 'Passwords must be hashed.');
    }

    public function test_registration_validates_input(): void
    {
        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'A',
            'email' => 'not-an-email',
            'phone' => 'abc',
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'password', 'terms']);
        $this->assertGuest();
    }

    public function test_customer_login_redirects_to_customer_dashboard(): void
    {
        $customer = $this->makeCustomer(['password' => 'password123']);

        $response = $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($customer);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = $this->makeAdmin(['password' => 'password123']);

        $response = $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $customer = $this->makeCustomer(['password' => 'password123']);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_inactive_customer_cannot_login(): void
    {
        $customer = $this->makeCustomer(['password' => 'password123', 'status' => 'inactive']);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->actingAs($customer)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
