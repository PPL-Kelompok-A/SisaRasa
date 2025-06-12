<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPullVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_item_to_cart_still_works()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food with mitra_id
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'price' => 25000,
        ]);

        $this->actingAs($customer);

        // Add to cart
        $response = $this->post('/cart/add', ['food_id' => $food->id]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Berhasil ditambahkan ke keranjang!');

        // Verify cart item created
        $this->assertDatabaseHas('cart_items', [
            'food_id' => $food->id,
            'name' => 'Test Food',
            'quantity' => 1,
            'mitra_id' => $mitra->id
        ]);
    }

    public function test_checkout_to_payment_still_works()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'price' => 25000,
        ]);

        // Create selected cart item
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

        $this->actingAs($customer);

        // Checkout
        $response = $this->post('/checkout');

        // Verify redirect to payment
        $response->assertRedirect();
        $this->assertTrue(str_contains($response->headers->get('Location'), '/payment'));

        // Verify order created
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000
        ]);

        // Verify order item created
        $order = Order::where('user_id', $customer->id)->first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'food_id' => $food->id,
            'quantity' => 2,
            'subtotal' => 50000
        ]);

        // Verify cart item removed
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_quantity_buttons_still_work()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
        ]);

        // Create cart item
        $cartItem = CartItem::create([
            'food_id' => $food->id,
            'name' => $food->name,
            'desc' => $food->description,
            'price' => $food->price,
            'img' => 'test.jpg',
            'quantity' => 2,
            'selected' => false,
            'mitra_id' => $mitra->id
        ]);

        $this->actingAs($customer);

        // Test increase quantity
        $response = $this->post("/cart/{$cartItem->id}/quantity", ['delta' => 1]);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kuantitas berhasil diperbarui.');

        $cartItem->refresh();
        $this->assertEquals(3, $cartItem->quantity);

        // Test decrease quantity
        $response = $this->post("/cart/{$cartItem->id}/quantity", ['delta' => -1]);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kuantitas berhasil diperbarui.');

        $cartItem->refresh();
        $this->assertEquals(2, $cartItem->quantity);
    }

    public function test_payment_page_loads_and_contains_chat_redirect()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $response = $this->get('/payment');

        $response->assertStatus(200);
        $response->assertViewIs('payment.index');
        
        // Check if page contains chat redirect functionality
        $response->assertSee('window.location.href');
        $response->assertSee('/chat');
    }

    public function test_cart_page_loads_correctly()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
    }

    public function test_toggle_select_functionality()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
        ]);

        // Create cart item (not selected)
        $cartItem = CartItem::create([
            'food_id' => $food->id,
            'name' => $food->name,
            'desc' => $food->description,
            'price' => $food->price,
            'img' => 'test.jpg',
            'quantity' => 1,
            'selected' => false,
            'mitra_id' => $mitra->id
        ]);

        $this->actingAs($customer);

        // Test select item
        $response = $this->post("/cart/{$cartItem->id}/select");
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item dipilih untuk checkout.');

        $cartItem->refresh();
        $this->assertTrue($cartItem->selected);

        // Test unselect item
        $response = $this->post("/cart/{$cartItem->id}/select");
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item dibatalkan dari checkout.');

        $cartItem->refresh();
        $this->assertFalse($cartItem->selected);
    }

    public function test_remove_item_functionality()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
        ]);

        // Create cart item
        $cartItem = CartItem::create([
            'food_id' => $food->id,
            'name' => $food->name,
            'desc' => $food->description,
            'price' => $food->price,
            'img' => 'test.jpg',
            'quantity' => 1,
            'selected' => false,
            'mitra_id' => $mitra->id
        ]);

        $this->actingAs($customer);

        // Test remove item
        $response = $this->delete("/cart/{$cartItem->id}");
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item berhasil dihapus dari keranjang.');

        // Verify item removed
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }
}
