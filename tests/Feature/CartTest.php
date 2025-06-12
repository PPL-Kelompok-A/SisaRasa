<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_food_to_cart_with_mitra_id()
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

        // Create a food item with mitra_id
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Test Food',
            'description' => 'Test Description',
            'price' => 25000,
            'is_available' => true
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Add food to cart
        $response = $this->post('/cart/add', [
            'food_id' => $food->id
        ]);

        // Assert successful redirect
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Berhasil ditambahkan ke keranjang!');

        // Assert cart item was created
        $this->assertDatabaseHas('cart_items', [
            'name' => 'Test Food',
            'mitra_id' => $mitra->id,
            'quantity' => 1
        ]);
    }

    public function test_can_add_food_to_cart_without_mitra_id_using_user_id_fallback()
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

        // Create a food item without mitra_id (old data scenario)
        $food = Food::factory()->create([
            'user_id' => $mitra->id,
            'mitra_id' => null, // Simulate old data
            'name' => 'Test Food Old',
            'description' => 'Test Description',
            'price' => 30000,
            'is_available' => true
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Add food to cart
        $response = $this->post('/cart/add', [
            'food_id' => $food->id
        ]);

        // Assert successful redirect
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Berhasil ditambahkan ke keranjang!');

        // Assert cart item was created with user_id as mitra_id
        $this->assertDatabaseHas('cart_items', [
            'name' => 'Test Food Old',
            'mitra_id' => $mitra->id, // Should use user_id as fallback
            'quantity' => 1
        ]);
    }

    public function test_cannot_add_food_without_mitra_info()
    {
        // Create a customer user
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Test Customer',
            'email' => 'customer@test.com'
        ]);

        // Create a food item without any mitra info
        $food = Food::factory()->create([
            'user_id' => null,
            'mitra_id' => null,
            'name' => 'Test Food No Mitra',
            'description' => 'Test Description',
            'price' => 35000,
            'is_available' => true
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Try to add food to cart
        $response = $this->post('/cart/add', [
            'food_id' => $food->id
        ]);

        // Assert error redirect
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Gagal menambahkan item: Informasi mitra untuk makanan ini tidak ditemukan.');

        // Assert no cart item was created
        $this->assertDatabaseMissing('cart_items', [
            'name' => 'Test Food No Mitra'
        ]);
    }
}
