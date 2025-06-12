<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_checkout_cart_items_successfully()
    {
        // Create a mitra user
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'Test Mitra',
            'email' => 'mitra@test.com'
        ]);

        // Create a customer user
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Test Customer',
            'email' => 'customer@test.com'
        ]);

        // Create food items
        $food1 = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food 1',
            'price' => 25000,
            'is_available' => true
        ]);

        $food2 = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food 2',
            'price' => 30000,
            'is_available' => true
        ]);

        // Create cart items
        $cartItem1 = CartItem::create([
            'food_id' => $food1->id,
            'name' => $food1->name,
            'desc' => $food1->description,
            'price' => $food1->price,
            'img' => 'test.jpg',
            'quantity' => 2,
            'selected' => true,
            'mitra_id' => $mitra->id
        ]);

        $cartItem2 = CartItem::create([
            'food_id' => $food2->id,
            'name' => $food2->name,
            'desc' => $food2->description,
            'price' => $food2->price,
            'img' => 'test2.jpg',
            'quantity' => 1,
            'selected' => true,
            'mitra_id' => $mitra->id
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Perform checkout
        $response = $this->post('/checkout');

        // Assert successful redirect to payment
        $response->assertRedirect();
        $this->assertTrue(str_contains($response->headers->get('Location'), '/payment'));

        // Assert order was created
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 80000 // (25000 * 2) + (30000 * 1)
        ]);

        // Assert order items were created
        $order = Order::where('user_id', $customer->id)->first();
        
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'food_id' => $food1->id,
            'quantity' => 2,
            'price' => 25000,
            'subtotal' => 50000
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'food_id' => $food2->id,
            'quantity' => 1,
            'price' => 30000,
            'subtotal' => 30000
        ]);

        // Assert cart items were removed after checkout
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem1->id
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem2->id
        ]);
    }

    public function test_cannot_checkout_without_selected_items()
    {
        // Create a customer user
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Test Customer',
            'email' => 'customer@test.com'
        ]);

        // Create a mitra user
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'Test Mitra',
            'email' => 'mitra@test.com'
        ]);

        // Create food item
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'price' => 25000,
            'is_available' => true
        ]);

        // Create cart item but not selected
        CartItem::create([
            'food_id' => $food->id,
            'name' => $food->name,
            'desc' => $food->description,
            'price' => $food->price,
            'img' => 'test.jpg',
            'quantity' => 1,
            'selected' => false, // Not selected
            'mitra_id' => $mitra->id
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Try to checkout
        $response = $this->post('/checkout');

        // Assert redirect back to cart with error
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Pilih item yang akan di-checkout!');

        // Assert no order was created
        $this->assertDatabaseMissing('orders', [
            'user_id' => $customer->id
        ]);
    }
}
