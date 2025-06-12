<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Services\NotificationService;

class MitraNotificationBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_notification_badge_appears_when_order_created()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'price' => 25000
        ]);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $food->id,
            'quantity' => 2,
            'price' => 25000,
            'subtotal' => 50000
        ]);

        // Create notification for order
        NotificationService::orderCreated($order);

        // Login as mitra
        $this->actingAs($mitra);

        // Access mitra dashboard
        $response = $this->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        
        // Check if notification badge is displayed
        $response->assertSee('bg-red-500'); // Badge background
        $response->assertSee('1'); // Notification count
        
        // Check if notification link exists
        $response->assertSee(route('notifications.index'));
    }

    public function test_mitra_notification_badge_shows_correct_count()
    {
        // Create mitra
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create multiple unread notifications
        for ($i = 1; $i <= 3; $i++) {
            Notification::create([
                'user_id' => $mitra->id,
                'message' => "Test notification {$i}",
                'status' => 'unread'
            ]);
        }

        // Login as mitra
        $this->actingAs($mitra);

        // Access mitra dashboard
        $response = $this->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        
        // Check if notification badge shows correct count
        $response->assertSee('3'); // Should show 3 notifications
    }

    public function test_mitra_notification_badge_shows_9_plus_for_many_notifications()
    {
        // Create mitra
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create 12 unread notifications
        for ($i = 1; $i <= 12; $i++) {
            Notification::create([
                'user_id' => $mitra->id,
                'message' => "Test notification {$i}",
                'status' => 'unread'
            ]);
        }

        // Login as mitra
        $this->actingAs($mitra);

        // Access mitra dashboard
        $response = $this->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        
        // Check if notification badge shows 9+
        $response->assertSee('9+'); // Should show 9+ for more than 9 notifications
    }

    public function test_mitra_notification_badge_hidden_when_no_unread_notifications()
    {
        // Create mitra
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create read notification
        Notification::create([
            'user_id' => $mitra->id,
            'message' => 'Test notification',
            'status' => 'read'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Access mitra dashboard
        $response = $this->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        
        // Check that notification badge is not displayed
        $response->assertDontSee('bg-red-500'); // Badge should not appear
    }

    public function test_complete_order_to_mitra_notification_flow()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Cimol Spesial',
            'price' => 15000
        ]);

        // Step 1: Customer creates order (simulated)
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 30000,
            'delivery_address' => 'Test Address'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $food->id,
            'quantity' => 2,
            'price' => 15000,
            'subtotal' => 30000
        ]);

        // Step 2: Notification service creates notifications
        NotificationService::orderCreated($order);

        // Step 3: Verify mitra notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $mitra->id,
            'status' => 'unread'
        ]);

        // Step 4: Login as mitra and check dashboard
        $this->actingAs($mitra);
        $dashboardResponse = $this->get(route('mitra.dashboard'));
        
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('bg-red-500'); // Badge appears
        $dashboardResponse->assertSee('1'); // Shows 1 notification

        // Step 5: Access notifications page
        $notificationsResponse = $this->get(route('notifications.index'));
        
        $notificationsResponse->assertStatus(200);
        $notificationsResponse->assertSee('Pesanan baru'); // Should see order notification
        $notificationsResponse->assertSee($order->id); // Should see order ID
    }

    public function test_mitra_can_mark_notification_as_read()
    {
        // Create mitra
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create unread notification
        $notification = Notification::create([
            'user_id' => $mitra->id,
            'message' => 'Test notification',
            'status' => 'unread'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Mark notification as read
        $response = $this->post(route('notifications.markAsRead', $notification->id));

        $response->assertRedirect();
        
        // Verify notification is marked as read
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'status' => 'read'
        ]);

        // Verify badge no longer appears
        $dashboardResponse = $this->get(route('mitra.dashboard'));
        $dashboardResponse->assertDontSee('bg-red-500');
    }
}
