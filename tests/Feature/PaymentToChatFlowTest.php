<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;

class PaymentToChatFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_process_redirects_to_chat_with_mitra()
    {
        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

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

        // Login as customer
        $this->actingAs($customer);

        // Process payment
        $response = $this->post(route('payment.process'), [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        // Should redirect to chatify with mitra_id and order_id
        $response->assertRedirect("/chatify/{$mitra->id}?order_id={$order->id}");
        $response->assertSessionHas('success', 'Pembayaran berhasil diproses! Silakan upload bukti pembayaran dan chat dengan mitra.');

        // Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }

    public function test_payment_process_without_order_redirects_to_general_chat()
    {
        // Create customer
        $customer = User::factory()->create(['role' => 'customer']);

        // Login as customer
        $this->actingAs($customer);

        // Process payment without order_id
        $response = $this->post(route('payment.process'), [
            'payment_method' => 'BCA'
        ]);

        // Should redirect to general chatify
        $response->assertRedirect('/chatify');
        $response->assertSessionHas('success', 'Pembayaran berhasil diproses! Silakan chat dengan mitra untuk konfirmasi.');
    }

    public function test_payment_process_validates_payment_method()
    {
        // Create customer
        $customer = User::factory()->create(['role' => 'customer']);

        // Login as customer
        $this->actingAs($customer);

        // Process payment with invalid method
        $response = $this->post(route('payment.process'), [
            'payment_method' => 'INVALID_METHOD'
        ]);

        // Should return validation error
        $response->assertSessionHasErrors('payment_method');
    }

    public function test_payment_process_validates_order_ownership()
    {
        // Create users
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order for customer1
        $order = Order::create([
            'user_id' => $customer1->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 25000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer2 (different user)
        $this->actingAs($customer2);

        // Try to process payment for customer1's order
        $response = $this->post(route('payment.process'), [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_complete_checkout_to_chat_flow()
    {
        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'price' => 15000
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Step 1: Add to cart and checkout (simulated by creating order directly)
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

        // Step 2: Access payment page
        $paymentResponse = $this->withSession(['order_id' => $order->id])
                                ->get(route('payment.show'));
        
        $paymentResponse->assertStatus(200);
        $paymentResponse->assertViewHas('order');

        // Step 3: Process payment
        $processResponse = $this->post(route('payment.process'), [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        // Step 4: Verify redirect to chat
        $processResponse->assertRedirect("/chatify/{$mitra->id}?order_id={$order->id}");
        
        // Step 5: Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }
}
