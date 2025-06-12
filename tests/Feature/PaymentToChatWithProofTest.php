<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentToChatWithProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_redirects_to_chat_with_mitra()
    {
        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'Test Mitra']);
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

        // Should redirect to chatify with mitra_id and order_id
        $response->assertRedirect("/chatify/{$mitra->id}?order_id={$order->id}");
        $response->assertSessionHas('success', 'Pembayaran berhasil diproses! Silakan upload bukti pembayaran dan chat dengan mitra.');

        // Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }

    public function test_chatify_displays_payment_proof_form()
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

        // Access chatify with order_id parameter
        $response = $this->get("/chatify/{$mitra->id}?order_id={$order->id}");

        $response->assertStatus(200);

        // Verify payment proof form is displayed
        $response->assertSee('Upload Bukti Pembayaran');
        $response->assertSee('proof_image');
        $response->assertSee('Kirim Bukti Pembayaran');
        $response->assertSee("value=\"{$order->id}\"", false); // Hidden order_id input
    }

    public function test_upload_payment_proof_ajax()
    {
        Storage::fake('public');

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

        // Create fake image file
        $file = UploadedFile::fake()->image('payment_proof.jpg');

        // Send AJAX request
        $response = $this->postJson('/chat/sendPaymentProof', [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil dikirim!'
        ]);

        // Verify order updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->payment_proof);

        // Verify file was stored
        Storage::disk('public')->assertExists($order->payment_proof);
    }

    public function test_upload_payment_proof_non_ajax()
    {
        Storage::fake('public');

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

        // Create fake image file
        $file = UploadedFile::fake()->image('payment_proof.jpg');

        // Send regular POST request
        $response = $this->post('/chat/sendPaymentProof', [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        $response->assertRedirect("/chatify/{$mitra->id}");
        $response->assertSessionHas('success', 'Bukti pembayaran berhasil dikirim! Silakan chat dengan mitra untuk konfirmasi.');

        // Verify order updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->payment_proof);

        // Verify file was stored
        Storage::disk('public')->assertExists($order->payment_proof);
    }

    public function test_upload_payment_proof_unauthorized()
    {
        // Create two customers and mitra
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

        // Login as customer2 and try to upload proof for customer1's order
        $this->actingAs($customer2);

        $file = UploadedFile::fake()->image('payment_proof.jpg');

        $response = $this->post('/chat/sendPaymentProof', [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_upload_payment_proof_validation()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        // Test without order_id
        $response = $this->postJson('/chat/sendPaymentProof', [
            'proof_image' => UploadedFile::fake()->image('test.jpg')
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_id']);

        // Test without proof_image
        $response = $this->postJson('/chat/sendPaymentProof', [
            'order_id' => 999
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['proof_image']);

        // Test with invalid order_id
        $response = $this->postJson('/chat/sendPaymentProof', [
            'order_id' => 999,
            'proof_image' => UploadedFile::fake()->image('test.jpg')
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_id']);
    }

    public function test_upload_payment_proof_file_size_validation()
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

        // Create file larger than 2MB (2048KB)
        $largeFile = UploadedFile::fake()->image('large_payment_proof.jpg')->size(3000);

        $response = $this->postJson('/chat/sendPaymentProof', [
            'order_id' => $order->id,
            'proof_image' => $largeFile
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['proof_image']);
    }

    public function test_complete_payment_to_chat_flow()
    {
        Storage::fake('public');

        // Create mitra and customer
        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'Test Mitra']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'price' => 25000,
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

        $this->actingAs($customer);

        // Step 1: Access payment page with order
        $paymentResponse = $this->get("/payment?order_id={$order->id}");
        $paymentResponse->assertStatus(200);
        $paymentResponse->assertSee('Test Food');
        $paymentResponse->assertSee('50.000');

        // Step 2: Process payment
        $processResponse = $this->post('/payment/process', [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);
        $processResponse->assertRedirect("/chatify/{$mitra->id}?order_id={$order->id}");

        // Step 3: Access chatify with payment proof form
        $chatResponse = $this->get("/chatify/{$mitra->id}?order_id={$order->id}");
        $chatResponse->assertStatus(200);
        $chatResponse->assertSee('Upload Bukti Pembayaran');

        // Step 4: Upload payment proof
        $file = UploadedFile::fake()->image('payment_proof.jpg');
        $uploadResponse = $this->postJson('/chat/sendPaymentProof', [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);
        $uploadResponse->assertStatus(200);
        $uploadResponse->assertJson(['success' => true]);

        // Verify final state
        $order->refresh();
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->payment_proof);
        Storage::disk('public')->assertExists($order->payment_proof);
    }
}
