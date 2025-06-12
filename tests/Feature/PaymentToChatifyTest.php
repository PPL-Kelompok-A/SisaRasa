<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentToChatifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_page_redirects_to_chatify()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $response = $this->get('/payment');

        $response->assertStatus(200);
        $response->assertViewIs('payment.index');
        
        // Check if page contains chatify redirect
        $response->assertSee('/chatify');
        $response->assertSee('window.location.href');
    }

    public function test_process_payment_redirects_to_chatify()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $response = $this->post('/payment/process', [
            'payment_method' => 'DANA'
        ]);

        $response->assertRedirect('/chatify');
        $response->assertSessionHas('success', 'Pembayaran berhasil diproses! Silakan chat dengan mitra untuk konfirmasi.');
    }

    public function test_confirm_payment_redirects_to_chatify()
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

        $response = $this->post('/payment/payment/confirm', [
            'order_id' => $order->id
        ]);

        $response->assertRedirect('/chatify');
        $response->assertSessionHas('success', 'Pembayaran berhasil diproses! Silakan chat dengan mitra untuk konfirmasi.');

        // Verify order status updated
        $order->refresh();
        $this->assertEquals('waiting_verification', $order->status);
    }

    public function test_send_payment_proof_redirects_to_chatify()
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

        $response = $this->post('/chat/sendPaymentProof', [
            'order_id' => $order->id,
            'proof_image' => $file
        ]);

        $response->assertRedirect('/chatify');
        $response->assertSessionHas('success', 'Bukti pembayaran berhasil dikirim! Silakan chat dengan mitra untuk konfirmasi.');

        // Verify order updated
        $order->refresh();
        $this->assertEquals('waiting_verification', $order->status);
        $this->assertNotNull($order->payment_proof);

        // Verify file was stored
        Storage::disk('public')->assertExists($order->payment_proof);
    }

    public function test_send_payment_proof_validation()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        // Test without order_id
        $response = $this->post('/chat/sendPaymentProof', [
            'proof_image' => UploadedFile::fake()->image('test.jpg')
        ]);

        $response->assertSessionHasErrors(['order_id']);

        // Test without proof_image
        $response = $this->post('/chat/sendPaymentProof', [
            'order_id' => 999
        ]);

        $response->assertSessionHasErrors(['proof_image']);

        // Test with invalid order_id
        $response = $this->post('/chat/sendPaymentProof', [
            'order_id' => 999,
            'proof_image' => UploadedFile::fake()->image('test.jpg')
        ]);

        $response->assertSessionHasErrors(['order_id']);
    }

    public function test_chatify_route_exists()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $response = $this->get('/chatify');

        // Should not return 404
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_payment_proof_file_upload_works()
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

        // Test different image formats
        $formats = ['jpg', 'jpeg', 'png', 'gif'];
        
        foreach ($formats as $format) {
            $file = UploadedFile::fake()->image("payment_proof.{$format}");

            $response = $this->post('/chat/sendPaymentProof', [
                'order_id' => $order->id,
                'proof_image' => $file
            ]);

            $response->assertRedirect('/chatify');
            
            $order->refresh();
            $this->assertNotNull($order->payment_proof);
            
            // Verify file was stored
            Storage::disk('public')->assertExists($order->payment_proof);
        }
    }

    public function test_payment_proof_file_size_validation()
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

        $response = $this->post('/chat/sendPaymentProof', [
            'order_id' => $order->id,
            'proof_image' => $largeFile
        ]);

        $response->assertSessionHasErrors(['proof_image']);
    }
}
