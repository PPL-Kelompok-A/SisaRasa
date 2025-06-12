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

class EndToEndCartFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_cart_to_payment_flow()
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
            'description' => 'Delicious test food 1',
            'price' => 25000,
            'is_available' => true
        ]);

        $food2 = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food 2',
            'description' => 'Delicious test food 2',
            'price' => 30000,
            'is_available' => true
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Step 1: Add first food to cart
        $response1 = $this->post('/cart/add', [
            'food_id' => $food1->id
        ]);

        $response1->assertRedirect();
        $response1->assertSessionHas('success', 'Berhasil ditambahkan ke keranjang!');

        // Verify cart item was created
        $this->assertDatabaseHas('cart_items', [
            'food_id' => $food1->id,
            'name' => 'Test Food 1',
            'quantity' => 1,
            'mitra_id' => $mitra->id
        ]);

        // Step 2: Add second food to cart
        $response2 = $this->post('/cart/add', [
            'food_id' => $food2->id
        ]);

        $response2->assertRedirect();
        $response2->assertSessionHas('success', 'Berhasil ditambahkan ke keranjang!');

        // Step 3: Increase quantity of first item
        $cartItem1 = CartItem::where('food_id', $food1->id)->first();
        $response3 = $this->post("/cart/{$cartItem1->id}/quantity", [
            'delta' => 1
        ]);

        $response3->assertRedirect();
        $response3->assertSessionHas('success', 'Kuantitas berhasil diperbarui.');

        // Verify quantity increased
        $cartItem1->refresh();
        $this->assertEquals(2, $cartItem1->quantity);

        // Step 4: Select items for checkout
        $cartItem2 = CartItem::where('food_id', $food2->id)->first();
        
        $this->post("/cart/{$cartItem1->id}/select");
        $this->post("/cart/{$cartItem2->id}/select");

        // Verify items are selected
        $cartItem1->refresh();
        $cartItem2->refresh();
        $this->assertTrue($cartItem1->selected);
        $this->assertTrue($cartItem2->selected);

        // Step 5: Checkout
        $response4 = $this->post('/checkout');

        // Verify redirect to payment
        $response4->assertRedirect();
        $this->assertTrue(str_contains($response4->headers->get('Location'), '/payment'));

        // Verify order was created
        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($mitra->id, $order->mitra_id);
        $this->assertEquals(80000, $order->total_amount); // (25000 * 2) + (30000 * 1)

        // Verify order items were created
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

        // Verify cart items were removed after checkout
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem1->id
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem2->id
        ]);
    }

    public function test_payment_page_redirects_to_chat()
    {
        // Create a customer user
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Test Customer',
            'email' => 'customer@test.com'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Access payment page
        $response = $this->get('/payment');

        // Verify payment page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('payment.index');

        // Verify that the page contains redirect to chat
        $response->assertSee('chat.index');
        $response->assertSee('window.location.href');
    }

    public function test_cart_quantity_buttons_work_correctly()
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

        // Login as customer
        $this->actingAs($customer);

        // Add item to cart
        $this->post('/cart/add', [
            'food_id' => $food->id
        ]);

        $cartItem = CartItem::where('food_id', $food->id)->first();

        // Test increase quantity
        $response1 = $this->post("/cart/{$cartItem->id}/quantity", [
            'delta' => 1
        ]);

        $response1->assertRedirect();
        $response1->assertSessionHas('success', 'Kuantitas berhasil diperbarui.');

        $cartItem->refresh();
        $this->assertEquals(2, $cartItem->quantity);

        // Test decrease quantity
        $response2 = $this->post("/cart/{$cartItem->id}/quantity", [
            'delta' => -1
        ]);

        $response2->assertRedirect();
        $response2->assertSessionHas('success', 'Kuantitas berhasil diperbarui.');

        $cartItem->refresh();
        $this->assertEquals(1, $cartItem->quantity);

        // Test delete when quantity becomes 0
        $response3 = $this->post("/cart/{$cartItem->id}/quantity", [
            'delta' => -1
        ]);

        $response3->assertRedirect();
        $response3->assertSessionHas('success', 'Item dihapus dari keranjang.');

        // Verify item was deleted
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id
        ]);
    }

    public function test_cart_page_displays_correctly()
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

        // Create cart item
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

        // Access cart page
        $response = $this->get('/cart');

        // Verify cart page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('cart.index');

        // Verify cart item is displayed
        $response->assertSee($food->name);
        $response->assertSee($food->description);
        $response->assertSee('25.000'); // Price formatting
        $response->assertSee('2'); // Quantity

        // Verify total is calculated correctly
        $response->assertSee('50.000'); // Total: 25000 * 2
    }
}
