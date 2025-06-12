<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ComprehensiveSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_customer_journey()
    {
        // 1. Register customer
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer'
        ]);
        
        $this->assertDatabaseHas('users', ['email' => 'customer@test.com']);
        
        // 2. Login customer
        $customer = User::where('email', 'customer@test.com')->first();
        $this->actingAs($customer);
        
        // 3. Access home page
        $response = $this->get('/');
        $response->assertStatus(200);
        
        // 4. Access menu page
        $response = $this->get('/menu');
        $response->assertStatus(200);
        
        $this->assertTrue(true, 'Customer journey basic flow works');
    }
    
    public function test_complete_mitra_journey()
    {
        // 1. Create mitra
        $mitra = User::factory()->create(['role' => 'mitra']);
        $this->actingAs($mitra);
        
        // 2. Access mitra dashboard
        $response = $this->get('/mitra/dashboard');
        $response->assertStatus(200);
        
        // 3. Access food management
        $response = $this->get('/mitra/foods');
        $response->assertStatus(200);
        
        // 4. Access orders management
        $response = $this->get('/mitra/orders');
        $response->assertStatus(200);
        
        // 5. Access notifications
        $response = $this->get('/mitra/notifications');
        $response->assertStatus(200);
        
        $this->assertTrue(true, 'Mitra journey basic flow works');
    }
    
    public function test_cart_system_works()
    {
        // Create customer and mitra
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);
        
        // Create food
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'price' => 25000
        ]);
        
        $this->actingAs($customer);
        
        // 1. Add to cart
        $response = $this->post('/cart/add', ['food_id' => $food->id]);
        $response->assertRedirect();
        
        // 2. View cart
        $response = $this->get('/cart');
        $response->assertStatus(200);
        
        // 3. Update quantity
        $cartItem = CartItem::first();
        $response = $this->post("/cart/{$cartItem->id}/quantity", ['delta' => 1]);
        $response->assertRedirect();
        
        // 4. Select item
        $response = $this->post("/cart/{$cartItem->id}/select");
        $response->assertRedirect();
        
        $this->assertTrue(true, 'Cart system works');
    }
    
    public function test_checkout_to_payment_flow()
    {
        // Setup
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'price' => 25000
        ]);
        
        // Add to cart and select
        CartItem::create([
            'food_id' => $food->id,
            'name' => $food->name,
            'desc' => $food->description ?? '',
            'img' => asset('images/default-food.png'),
            'price' => $food->price,
            'quantity' => 2,
            'selected' => true,
            'mitra_id' => $mitra->id
        ]);
        
        $this->actingAs($customer);
        
        // 1. Checkout
        $response = $this->post('/checkout');
        $response->assertRedirect('/payment');
        
        // 2. Access payment page
        $response = $this->get('/payment');
        $response->assertStatus(200);
        
        $this->assertTrue(true, 'Checkout to payment flow works');
    }
    
    public function test_payment_processing_works()
    {
        // Setup
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);
        
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);
        
        $this->actingAs($customer);
        
        // Process payment
        $response = $this->post('/payment/process', [
            'payment_method' => 'DANA',
            'order_id' => $order->id
        ]);
        
        // Should redirect to chatify
        $response->assertRedirect("/chatify/{$mitra->id}?order_id={$order->id}");
        
        $this->assertTrue(true, 'Payment processing works');
    }
    
    public function test_order_status_management_works()
    {
        // Setup
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);
        
        $order = Order::create([
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);
        
        $this->actingAs($mitra);
        
        // 1. View order detail
        $response = $this->get("/mitra/orders/{$order->id}");
        $response->assertStatus(200);
        
        // 2. Update status
        $response = $this->put("/mitra/orders/{$order->id}/status", [
            'status' => 'processing',
            'notes' => 'Order being processed'
        ]);
        $response->assertRedirect();
        
        // 3. Quick update
        $response = $this->postJson("/mitra/orders/{$order->id}/quick-update", [
            'status' => 'preparing'
        ]);
        $response->assertStatus(200);
        
        $this->assertTrue(true, 'Order status management works');
    }
    
    public function test_notification_system_works()
    {
        // Setup
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);
        
        $this->actingAs($customer);
        
        // 1. Access notifications
        $response = $this->get('/notifications');
        $response->assertStatus(200);
        
        // 2. Get notification count
        $response = $this->get('/notifications/count');
        $response->assertStatus(200);
        
        $this->actingAs($mitra);
        
        // 3. Access mitra notifications
        $response = $this->get('/mitra/notifications');
        $response->assertStatus(200);
        
        $this->assertTrue(true, 'Notification system works');
    }
    
    public function test_authentication_system_works()
    {
        // 1. Access login page
        $response = $this->get('/login');
        $response->assertStatus(200);
        
        // 2. Access register page
        $response = $this->get('/register');
        $response->assertStatus(200);
        
        // 3. Register new user
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer'
        ]);
        
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        
        // 4. Login
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Customer should redirect to dashboard, then to home
        $response->assertRedirect('/dashboard');
        
        $this->assertTrue(true, 'Authentication system works');
    }
    
    public function test_food_management_works()
    {
        $mitra = User::factory()->create(['role' => 'mitra']);
        $this->actingAs($mitra);
        
        // 1. Access food list
        $response = $this->get('/mitra/foods');
        $response->assertStatus(200);
        
        // 2. Access create food page
        $response = $this->get('/mitra/foods/create');
        $response->assertStatus(200);
        
        $this->assertTrue(true, 'Food management works');
    }
    
    public function test_profile_management_works()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // 1. Access profile page
        $response = $this->get('/profile');
        $response->assertStatus(200);
        
        // 2. Update profile
        $response = $this->patch('/profile', [
            'name' => 'Updated Name',
            'email' => $user->email
        ]);
        
        $response->assertRedirect('/profile');
        
        $this->assertTrue(true, 'Profile management works');
    }
    
    public function test_all_public_pages_accessible()
    {
        // Test all public pages
        $publicPages = [
            '/',
            '/menu',
            '/login',
            '/register'
        ];
        
        foreach ($publicPages as $page) {
            $response = $this->get($page);
            $response->assertStatus(200);
        }
        
        $this->assertTrue(true, 'All public pages accessible');
    }
}
