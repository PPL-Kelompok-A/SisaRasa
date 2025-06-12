<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_badge_appears_in_navbar()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        // Create unread notification
        Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification',
            'status' => 'unread'
        ]);

        $this->actingAs($customer);

        $response = $this->get('/');

        $response->assertStatus(200);
        // Check if notification badge is displayed
        $response->assertSee('bg-red-500'); // Badge background
        $response->assertSee('1'); // Notification count
    }

    public function test_notification_service_creates_order_notifications()
    {
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Test order created notification
        NotificationService::orderCreated($order);

        // Check customer notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'status' => 'unread'
        ]);

        // Check mitra notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $mitra->id,
            'status' => 'unread'
        ]);

        $customerNotification = Notification::where('user_id', $customer->id)->first();
        $this->assertStringContainsString("Pesanan #{$order->id} berhasil dibuat", $customerNotification->message);

        $mitraNotification = Notification::where('user_id', $mitra->id)->first();
        $this->assertStringContainsString("Pesanan baru #{$order->id} diterima", $mitraNotification->message);
    }

    public function test_notification_service_creates_payment_notifications()
    {
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Test payment processed notification
        NotificationService::paymentProcessed($order);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'status' => 'unread'
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $mitra->id,
            'status' => 'unread'
        ]);
    }

    public function test_notification_service_creates_payment_proof_notifications()
    {
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'processing',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Test payment proof uploaded notification
        NotificationService::paymentProofUploaded($order);

        $customerNotification = Notification::where('user_id', $customer->id)->first();
        $this->assertStringContainsString('Bukti pembayaran', $customerNotification->message);

        $mitraNotification = Notification::where('user_id', $mitra->id)->first();
        $this->assertStringContainsString('Bukti pembayaran', $mitraNotification->message);
    }

    public function test_notifications_index_page()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        // Create notifications
        Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification 1',
            'status' => 'unread'
        ]);

        Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification 2',
            'status' => 'read'
        ]);

        $this->actingAs($customer);

        $response = $this->get('/notifications');

        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
        $response->assertSee('Test notification 1');
        $response->assertSee('Test notification 2');
        $response->assertSee('Tandai Semua Dibaca'); // Mark all as read button
    }

    public function test_mark_notification_as_read()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        $notification = Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification',
            'status' => 'unread'
        ]);

        $this->actingAs($customer);

        $response = $this->post("/notifications/{$notification->id}/mark-as-read");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Notifikasi telah ditandai dibaca.');

        $notification->refresh();
        $this->assertEquals('read', $notification->status);
    }

    public function test_mark_all_notifications_as_read()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        // Create multiple unread notifications
        Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification 1',
            'status' => 'unread'
        ]);

        Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification 2',
            'status' => 'unread'
        ]);

        $this->actingAs($customer);

        $response = $this->post('/notifications/mark-all-as-read');

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Semua notifikasi telah ditandai dibaca.');

        // Check all notifications are marked as read
        $unreadCount = Notification::where('user_id', $customer->id)
            ->where('status', 'unread')
            ->count();
        
        $this->assertEquals(0, $unreadCount);
    }

    public function test_unread_count_api()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        // Create unread notifications
        Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification 1',
            'status' => 'unread'
        ]);

        Notification::create([
            'user_id' => $customer->id,
            'message' => 'Test notification 2',
            'status' => 'unread'
        ]);

        $this->actingAs($customer);

        $response = $this->get('/notifications/unread-count');

        $response->assertStatus(200);
        $response->assertJson(['count' => 2]);
    }

    public function test_notification_authorization()
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        
        $notification = Notification::create([
            'user_id' => $customer1->id,
            'message' => 'Test notification',
            'status' => 'unread'
        ]);

        // Login as customer2 and try to mark customer1's notification as read
        $this->actingAs($customer2);

        $response = $this->post("/notifications/{$notification->id}/mark-as-read");

        // Should return 404 because notification doesn't belong to customer2
        $response->assertStatus(404);
    }

    public function test_complete_notification_flow()
    {
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'price' => 25000,
        ]);

        $this->actingAs($customer);

        // Step 1: Create order (should create notifications)
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        NotificationService::orderCreated($order);

        // Check notifications created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'status' => 'unread'
        ]);

        // Step 2: Process payment (should create notifications)
        $response = $this->post('/payment/process', [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        // Check payment processed notifications
        $paymentNotifications = Notification::where('user_id', $customer->id)->get();
        $this->assertGreaterThan(1, $paymentNotifications->count());

        // Step 3: Check notification badge in navbar
        $response = $this->get('/');
        $response->assertSee('bg-red-500'); // Notification badge

        // Step 4: View notifications page
        $response = $this->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Pesanan');

        // Step 5: Mark all as read
        $response = $this->post('/notifications/mark-all-as-read');
        $response->assertRedirect();

        // Check badge disappears
        $response = $this->get('/');
        $response->assertDontSee('bg-red-500'); // No notification badge
    }
}
