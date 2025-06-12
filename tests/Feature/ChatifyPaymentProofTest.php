<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;

class ChatifyPaymentProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_process_redirects_to_chatify_with_order_id()
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

        // Login as customer
        $this->actingAs($customer);

        // Process payment
        $response = $this->post(route('payment.process'), [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        // Should redirect to chatify with mitra_id and order_id
        $response->assertRedirect("/chatify/{$mitra->id}?order_id={$order->id}");
        $response->assertSessionHas('success');

        // Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }

    public function test_chatify_shows_payment_proof_upload_form_when_order_id_present()
    {
        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'processing',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Access chatify with order_id
        $response = $this->get("/chatify/{$mitra->id}?order_id={$order->id}");

        $response->assertStatus(200);
        
        // Should contain payment proof upload form
        $response->assertSee('Upload Bukti Pembayaran');
        $response->assertSee('chat.sendPaymentProof');
        $response->assertSee('proof_image');
    }

    public function test_upload_payment_proof_via_chatify()
    {
        Storage::fake('public');

        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'processing',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Create fake image file
        $file = UploadedFile::fake()->image('payment_proof.jpg', 800, 600);

        // Upload payment proof
        $response = $this->post(route('chat.sendPaymentProof'), [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        // Should redirect back to chatify
        $response->assertRedirect("/chatify/{$mitra->id}");
        $response->assertSessionHas('success');

        // Verify file was stored
        Storage::disk('public')->assertExists('payment_proofs/' . $file->hashName());

        // Verify order was updated
        $order->refresh();
        $this->assertNotNull($order->payment_proof);
        $this->assertEquals('processing', $order->status);

        // Verify notification was created for mitra
        $this->assertDatabaseHas('notifications', [
            'user_id' => $mitra->id,
            'order_id' => $order->id,
            'status' => 'unread'
        ]);
    }

    public function test_upload_payment_proof_validates_file_type()
    {
        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'processing',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Create fake non-image file
        $file = UploadedFile::fake()->create('document.pdf', 100);

        // Try to upload non-image file
        $response = $this->post(route('chat.sendPaymentProof'), [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        // Should return validation error
        $response->assertSessionHasErrors('proof_image');
    }

    public function test_upload_payment_proof_validates_file_size()
    {
        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'processing',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Create fake large image file (3MB)
        $file = UploadedFile::fake()->image('large_image.jpg')->size(3072);

        // Try to upload large file
        $response = $this->post(route('chat.sendPaymentProof'), [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        // Should return validation error
        $response->assertSessionHasErrors('proof_image');
    }

    public function test_cannot_upload_payment_proof_for_other_users_order()
    {
        Storage::fake('public');

        // Create users
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create order for customer1
        $order = Order::create([
            'user_id' => $customer1->id,
            'mitra_id' => $mitra->id,
            'status' => 'processing',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);

        // Login as customer2 (different user)
        $this->actingAs($customer2);

        // Create fake image file
        $file = UploadedFile::fake()->image('payment_proof.jpg');

        // Try to upload payment proof for customer1's order
        $response = $this->post(route('chat.sendPaymentProof'), [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        // Should return 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_complete_payment_to_chatify_upload_flow()
    {
        Storage::fake('public');

        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Cimol Spesial',
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

        // Step 1: Process payment
        $paymentResponse = $this->post(route('payment.process'), [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);

        $paymentResponse->assertRedirect("/chatify/{$mitra->id}?order_id={$order->id}");

        // Step 2: Access chatify with order_id
        $chatifyResponse = $this->get("/chatify/{$mitra->id}?order_id={$order->id}");
        
        $chatifyResponse->assertStatus(200);
        $chatifyResponse->assertSee('Upload Bukti Pembayaran');

        // Step 3: Upload payment proof
        $file = UploadedFile::fake()->image('payment_proof.jpg');
        
        $uploadResponse = $this->post(route('chat.sendPaymentProof'), [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        $uploadResponse->assertRedirect("/chatify/{$mitra->id}");
        $uploadResponse->assertSessionHas('success');

        // Step 4: Verify everything is updated
        $order->refresh();
        $this->assertNotNull($order->payment_proof);
        $this->assertEquals('processing', $order->status);

        // Verify notification for mitra
        $this->assertDatabaseHas('notifications', [
            'user_id' => $mitra->id,
            'order_id' => $order->id,
            'status' => 'unread'
        ]);
    }
}
