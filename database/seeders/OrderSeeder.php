<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Find the mitra user
        $mitra = User::where('email', 'cimol@sisarasa.com')->first();
        if (!$mitra) {
            $this->command->error('Mitra user not found!');
            return;
        }
        
        // Get foods from this mitra
        $foods = Food::where('user_id', $mitra->id)->get();
        if ($foods->isEmpty()) {
            $this->command->error('No foods found for this mitra!');
            return;
        }
        
        // Get existing customers or create new ones if needed
        $customers = User::where('role', 'customer')->take(5)->get();
        
        if ($customers->count() < 5) {
            $existingCount = $customers->count();
            for ($i = $existingCount + 1; $i <= 5; $i++) {
                $customers[] = User::create([
                    'name' => "Customer {$i}",
                    'email' => "customer{$i}@example.com",
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'address' => "Jl. Customer No. {$i}",
                    'phone' => "08123456789{$i}",
                ]);
            }
        }
        
        // Create 20 new pending orders
        $this->command->info('Creating 20 new pending orders...');
        
        // Get the latest order number
        $latestOrder = Order::orderBy('id', 'desc')->first();
        $orderCount = $latestOrder ? intval(substr($latestOrder->id, 4)) : 0;
        
        for ($i = 1; $i <= 20; $i++) {
            // Randomly select a customer
            $customer = $customers->random();
            
            // Create a new pending order
            $order = Order::create([
                'user_id' => $customer->id,
                'mitra_id' => $mitra->id,
                'status' => 'pending',
                'total_amount' => 0,
                'delivery_address' => $customer->address ?? 'Jl. Customer No. ' . $customer->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Add 1-3 random food items to the order
            $orderTotal = 0;
            $numItems = rand(1, 3);
            
            for ($j = 0; $j < $numItems; $j++) {
                $food = $foods->random();
                $quantity = rand(1, 3);
                
                $subtotal = $food->price * $quantity;
                OrderItem::create([
                    'order_id' => $order->id,
                    'food_id' => $food->id,
                    'quantity' => $quantity,
                    'price' => $food->price,
                    'subtotal' => $subtotal,
                ]);
                
                $orderTotal += $subtotal;
            }
            
            // Update the order total
            $order->update(['total_amount' => $orderTotal]);
            
            $this->command->info("Created pending order #{$i} with total: Rp {$orderTotal}");
        }
        
        $this->command->info('Order seeding completed successfully!');
    }
}
