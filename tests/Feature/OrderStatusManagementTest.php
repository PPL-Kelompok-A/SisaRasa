<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;

class OrderStatusManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_can_view_orders_list()
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

        // Create orders
        $order1 = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        $order2 = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'completed',
            'total_amount' => 30000,
            'delivery_address' => 'Test Address 2'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Access orders list
        $response = $this->get(route('mitra.orders'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Pesanan');
        $response->assertSee('#' . $order1->id);
        $response->assertSee('#' . $order2->id);
        $response->assertSee('Pending');
        $response->assertSee('Completed');
    }

    public function test_mitra_can_view_order_detail()
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

        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $food->id,
            'quantity' => 2,
            'price' => 25000,
            'subtotal' => 50000
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Access order detail
        $response = $this->get(route('mitra.order.show', $order->id));

        $response->assertStatus(200);
        $response->assertSee('Detail Pesanan #' . $order->id);
        $response->assertSee($customer->name);
        $response->assertSee($food->name);
        $response->assertSee('Update Status Pesanan');
    }

    public function test_mitra_can_update_order_status()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Update order status
        $response = $this->put(route('mitra.order.updateStatus', $order->id), [
            'status' => 'processing',
            'notes' => 'Order sedang diproses'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
        $this->assertEquals('Order sedang diproses', $order->notes);

        // Verify notification created for customer
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'status' => 'unread'
        ]);
    }

    public function test_mitra_can_quick_update_order_status()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Quick update order status via AJAX
        $response = $this->postJson(route('mitra.order.quickUpdate', $order->id), [
            'status' => 'preparing'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'new_status' => 'preparing'
        ]);

        // Verify order status updated
        $order->refresh();
        $this->assertEquals('preparing', $order->status);

        // Verify notification created for customer
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'status' => 'unread'
        ]);
    }

    public function test_mitra_cannot_access_other_mitra_orders()
    {
        // Create two mitras and customer
        $mitra1 = User::factory()->create(['role' => 'mitra']);
        $mitra2 = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create order for mitra1
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra1->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as mitra2
        $this->actingAs($mitra2);

        // Try to access mitra1's order
        $response = $this->get(route('mitra.order.show', $order->id));
        $response->assertStatus(403);

        // Try to update mitra1's order
        $response = $this->put(route('mitra.order.updateStatus', $order->id), [
            'status' => 'processing'
        ]);
        $response->assertStatus(403);
    }

    public function test_order_status_filter_works()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create orders with different statuses
        $pendingOrder = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        $completedOrder = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'completed',
            'total_amount' => 30000,
            'delivery_address' => 'Test Address 2'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Filter by pending status
        $response = $this->get(route('mitra.orders', ['status' => 'pending']));
        $response->assertStatus(200);
        $response->assertSee('#' . $pendingOrder->id);
        $response->assertDontSee('#' . $completedOrder->id);

        // Filter by completed status
        $response = $this->get(route('mitra.orders', ['status' => 'completed']));
        $response->assertStatus(200);
        $response->assertSee('#' . $completedOrder->id);
        $response->assertDontSee('#' . $pendingOrder->id);
    }

    public function test_completing_order_creates_notification_for_both_users()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'delivered',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Complete the order
        $response = $this->put(route('mitra.order.updateStatus', $order->id), [
            'status' => 'completed'
        ]);

        $response->assertRedirect();

        // Verify notifications created for both customer and mitra
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'status' => 'unread'
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $mitra->id,
            'order_id' => $order->id,
            'status' => 'unread'
        ]);
    }

    public function test_order_status_validation()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as mitra
        $this->actingAs($mitra);

        // Try to update with invalid status
        $response = $this->put(route('mitra.order.updateStatus', $order->id), [
            'status' => 'invalid_status'
        ]);

        $response->assertSessionHasErrors('status');

        // Verify order status not changed
        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }
}
