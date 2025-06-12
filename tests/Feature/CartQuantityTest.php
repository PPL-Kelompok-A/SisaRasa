<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Food;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartQuantityTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_increase_cart_item_quantity()
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
            'selected' => false,
            'mitra_id' => $mitra->id
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Increase quantity
        $response = $this->post("/cart/{$cartItem->id}/quantity", [
            'delta' => 1
        ]);

        // Assert successful redirect
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kuantitas berhasil diperbarui.');

        // Assert quantity increased
        $cartItem->refresh();
        $this->assertEquals(3, $cartItem->quantity);
    }

    public function test_can_decrease_cart_item_quantity()
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

        // Create cart item with quantity 3
        $cartItem = CartItem::create([
            'food_id' => $food->id,
            'name' => $food->name,
            'desc' => $food->description,
            'price' => $food->price,
            'img' => 'test.jpg',
            'quantity' => 3,
            'selected' => false,
            'mitra_id' => $mitra->id
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Decrease quantity
        $response = $this->post("/cart/{$cartItem->id}/quantity", [
            'delta' => -1
        ]);

        // Assert successful redirect
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kuantitas berhasil diperbarui.');

        // Assert quantity decreased
        $cartItem->refresh();
        $this->assertEquals(2, $cartItem->quantity);
    }

    public function test_cannot_decrease_quantity_below_one()
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

        // Create cart item with quantity 1
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

        // Login as customer
        $this->actingAs($customer);

        // Try to decrease quantity below 1
        $response = $this->post("/cart/{$cartItem->id}/quantity", [
            'delta' => -1
        ]);

        // Assert successful redirect with delete message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item dihapus dari keranjang.');

        // Assert item was deleted
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id
        ]);
    }

    public function test_invalid_delta_value_returns_validation_error()
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
            'selected' => false,
            'mitra_id' => $mitra->id
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Try with invalid delta value
        $response = $this->post("/cart/{$cartItem->id}/quantity", [
            'delta' => 5  // Invalid value, should be -1 or 1
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['delta']);
    }

    public function test_update_quantity_with_nonexistent_item_returns_error()
    {
        // Create a customer user
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Test Customer',
            'email' => 'customer@test.com'
        ]);

        // Login as customer
        $this->actingAs($customer);

        // Try to update quantity of non-existent item
        $response = $this->post("/cart/999/quantity", [
            'delta' => 1
        ]);

        // Assert error message
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Item tidak ditemukan di keranjang.');
    }
}
