<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Food;
use App\Models\CartItem;
use App\Models\Order;

class CheckoutToPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_redirects_to_payment_page()
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

        // Create cart item and select it
        $cartItem = CartItem::create([
            'food_id' => $food->id,
            'name' => $food->name,
            'desc' => $food->description,
            'price' => $food->price,
            'img' => 'test.jpg',
            'quantity' => 2,
            'selected' => true,
            'mitra_id' => $mitra->id
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Perform checkout
        $response = $this->post('/checkout');

        // Assert redirect to payment page
        $response->assertRedirect(route('payment.show'));
        
        // Assert order was created
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 30000 // 15000 * 2
        ]);

        // Assert order_id is in session
        $response->assertSessionHas('order_id');
    }

    public function test_payment_page_displays_order_from_session()
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

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 30000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Access payment page with order_id in session
        $response = $this->withSession(['order_id' => $order->id])
                         ->get(route('payment.show'));

        // Assert payment page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('payment.index');
        $response->assertViewHas('order');
        
        // Assert order data is passed to view
        $viewOrder = $response->viewData('order');
        $this->assertEquals($order->id, $viewOrder->id);
        $this->assertEquals($order->total_amount, $viewOrder->total_amount);
    }

    public function test_payment_page_with_query_parameter()
    {
        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 25000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Access payment page with order_id as query parameter
        $response = $this->get(route('payment.show', ['order_id' => $order->id]));

        // Assert payment page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('payment.index');
        $response->assertViewHas('order');
        
        // Assert order data is passed to view
        $viewOrder = $response->viewData('order');
        $this->assertEquals($order->id, $viewOrder->id);
    }

    public function test_payment_page_without_order_shows_empty_state()
    {
        // Create customer
        $customer = User::factory()->create(['role' => 'customer']);

        // Login as customer
        $this->actingAs($customer);

        // Access payment page without order
        $response = $this->get(route('payment.show'));

        // Assert payment page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('payment.index');
        $response->assertViewHas('order', null);
    }

    public function test_cannot_access_other_users_order()
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

        // Try to access customer1's order
        $response = $this->get(route('payment.show', ['order_id' => $order->id]));

        // Assert access is forbidden
        $response->assertStatus(403);
    }
}
