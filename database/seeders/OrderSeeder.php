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
        $mitra = User::where('email', 'cimol@sisarasa.com')->first();
        $foods = Food::where('user_id', $mitra->id)->get();

        if ($foods->isEmpty()) {
            return;
        }

        // Create some customers
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = User::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => "Jl. Customer No. {$i}",
                'phone' => "08123456789{$i}",
            ]);
        }

        // Create orders with different dates and statuses
        $statuses = ['completed', 'processing', 'pending', 'cancelled'];
        $dates = [
            Carbon::today(),
            Carbon::today()->subDays(1),
            Carbon::today()->subDays(2),
            Carbon::today()->subDays(3),
            Carbon::today()->subWeek(),
            Carbon::today()->subWeeks(2),
            Carbon::today()->subMonth(),
        ];

        foreach ($dates as $date) {
            foreach ($customers as $customer) {
                // Not every customer orders every day
                if (rand(0, 1)) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $order = Order::create([
                    'user_id' => $customer->id,
                    'mitra_id' => $mitra->id,
                    'status' => $status,
                    'total_amount' => 0,
                    'delivery_address' => 'Jl. Customer No. ' . $customer->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // Add 1-3 items to each order
                $orderTotal = 0;
                $numItems = rand(1, 3);
                for ($i = 0; $i < $numItems; $i++) {
                    $food = $foods->random();
                    $quantity = rand(1, 3);
                    
                    $subtotal = $food->price * $quantity;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'food_id' => $food->id,
                        'quantity' => $quantity,
                        'price' => $food->price,
                        'subtotal' => $subtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $orderTotal += $food->price * $quantity;
                }

                $order->update(['total_amount' => $orderTotal]);
            }
        }
    }
}
