<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_page_displays_order_items()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'Test Mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create foods
        $food1 = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Nasi Goreng',
            'description' => 'Nasi goreng spesial',
            'price' => 25000,
        ]);

        $food2 = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Ayam Bakar',
            'description' => 'Ayam bakar bumbu kecap',
            'price' => 30000,
        ]);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 80000,
            'delivery_address' => 'Test Address'
        ]);

        // Create order items
        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $food1->id,
            'quantity' => 2,
            'price' => 25000,
            'subtotal' => 50000
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $food2->id,
            'quantity' => 1,
            'price' => 30000,
            'subtotal' => 30000
        ]);

        $this->actingAs($customer);

        // Access payment page with order_id
        $response = $this->get("/payment?order_id={$order->id}");

        $response->assertStatus(200);
        $response->assertViewIs('payment.index');

        // Verify order information is displayed
        $response->assertSee("Order ID: #{$order->id}");
        $response->assertSee('Test Mitra');
        $response->assertSee('pending');

        // Verify food items are displayed
        $response->assertSee('Nasi Goreng');
        $response->assertSee('Nasi goreng spesial');
        $response->assertSee('Ayam Bakar');
        $response->assertSee('Ayam bakar bumbu kecap');

        // Verify quantities and prices
        $response->assertSee('Qty: 2');
        $response->assertSee('Qty: 1');
        $response->assertSee('25.000');
        $response->assertSee('30.000');
        $response->assertSee('50.000'); // Subtotal
        $response->assertSee('30.000'); // Subtotal

        // Verify total amount
        $response->assertSee('80.000');
    }

    public function test_payment_page_without_order_shows_fallback()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        // Access payment page without order_id
        $response = $this->get('/payment');

        $response->assertStatus(200);
        $response->assertViewIs('payment.index');

        // Verify fallback content is displayed
        $response->assertSee('Tidak ada pesanan');
        $response->assertSee('Silakan lakukan checkout terlebih dahulu');
        $response->assertSee('Kembali ke Keranjang');
    }

    public function test_payment_page_unauthorized_order_access()
    {
        // Create two customers
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order for customer1
        $order = Order::create([
            'user_id' => $customer1->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer2 and try to access customer1's order
        $this->actingAs($customer2);

        $response = $this->get("/payment?order_id={$order->id}");

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_payment_page_with_nonexistent_order()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        // Try to access non-existent order
        $response = $this->get('/payment?order_id=999');

        // Should return 404 Not Found
        $response->assertStatus(404);
    }

    public function test_payment_form_includes_order_id()
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

        $this->actingAs($customer);

        $response = $this->get("/payment?order_id={$order->id}");

        $response->assertStatus(200);

        // Verify hidden input with order_id is present
        $response->assertSee('name="order_id"', false);
        $response->assertSee("value=\"{$order->id}\"", false);
    }

    public function test_process_payment_with_order_id()
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

        $this->actingAs($customer);

        // Process payment with order_id
        $response = $this->post('/payment/process', [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        $response->assertRedirect('/chatify');
        $response->assertSessionHas('success', 'Pembayaran berhasil diproses! Silakan chat dengan mitra untuk konfirmasi.');

        // Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }

    public function test_process_payment_unauthorized_order()
    {
        // Create two customers
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order for customer1
        $order = Order::create([
            'user_id' => $customer1->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer2 and try to process customer1's order
        $this->actingAs($customer2);

        $response = $this->post('/payment/process', [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_payment_page_displays_food_images()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food with image
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'image' => 'foods/test-image.jpg',
            'price' => 25000,
        ]);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 25000,
            'delivery_address' => 'Test Address'
        ]);

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $food->id,
            'quantity' => 1,
            'price' => 25000,
            'subtotal' => 25000
        ]);

        $this->actingAs($customer);

        $response = $this->get("/payment?order_id={$order->id}");

        $response->assertStatus(200);

        // Verify image is displayed
        $response->assertSee('foods/test-image.jpg');
    }
}
