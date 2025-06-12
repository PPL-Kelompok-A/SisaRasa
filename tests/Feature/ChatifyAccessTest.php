<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ChatifyAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatify_requires_authentication()
    {
        // Test accessing chatify without authentication
        $response = $this->get('/chatify');
        
        // Should redirect to login
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_chatify()
    {
        // Create and authenticate user
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);

        // Test accessing chatify
        $response = $this->get('/chatify');
        
        // Should return 200 OK
        $response->assertStatus(200);
        
        // Should contain chatify elements
        $response->assertSee('BincangRasa'); // Title from chatify config
    }

    public function test_mitra_can_access_chatify()
    {
        // Create and authenticate mitra
        $mitra = User::factory()->create(['role' => 'mitra']);
        $this->actingAs($mitra);

        // Test accessing chatify
        $response = $this->get('/chatify');
        
        // Should return 200 OK
        $response->assertStatus(200);
        
        // Should contain chatify elements
        $response->assertSee('BincangRasa');
    }

    public function test_chatify_navbar_link_works_for_mitra()
    {
        // Create and authenticate mitra
        $mitra = User::factory()->create(['role' => 'mitra']);
        $this->actingAs($mitra);

        // Test accessing mitra dashboard (which contains chatify link)
        $response = $this->get(route('mitra.dashboard'));
        
        $response->assertStatus(200);
        
        // Should contain link to chatify
        $response->assertSee('/chatify');
    }

    public function test_chatify_with_specific_user()
    {
        // Create users
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);
        
        $this->actingAs($customer);

        // Test accessing chatify with specific user
        $response = $this->get("/chatify/{$mitra->id}");
        
        // Should return 200 OK
        $response->assertStatus(200);
        
        // Should contain chatify elements
        $response->assertSee('BincangRasa');
    }

    public function test_chatify_api_endpoints_work()
    {
        // Create user
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);

        // Test getting contacts
        $response = $this->get('/chatify/getContacts');
        
        // Should return 200 OK
        $response->assertStatus(200);
    }
}
