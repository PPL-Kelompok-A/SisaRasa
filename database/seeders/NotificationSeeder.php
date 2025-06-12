<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing notifications
        \App\Models\Notification::truncate();

        // Get customer and mitra users
        $customer = \App\Models\User::where('email', 'customer@test.com')->first();
        $mitra = \App\Models\User::where('email', 'cimol@sisarasa.com')->first(); // Cimol Bojot yang asli

        if (!$customer) {
            echo "Customer user not found. Please run CustomerUserSeeder first.\n";
            return;
        }

        if (!$mitra) {
            echo "Cimol Bojot mitra not found. Please run MitraSeeder and FoodSeeder first.\n";
            return;
        }

        // Get existing cimol foods from Cimol Bojot mitra
        $cimolOriginal = \App\Models\Food::where('name', 'Cimol Original')->where('user_id', $mitra->id)->first();
        $cimolPedas = \App\Models\Food::where('name', 'Cimol Pedas')->where('user_id', $mitra->id)->first();
        $cimolKeju = \App\Models\Food::where('name', 'Cimol Keju')->where('user_id', $mitra->id)->first();

        if (!$cimolOriginal || !$cimolPedas || !$cimolKeju) {
            echo "Cimol menu items not found. Please run FoodSeeder first.\n";
            return;
        }

        // Order 1 - Cimol Original (Customer orders from Cimol Bojot)
        $order1 = \App\Models\Order::create([
            'user_id' => $customer->id,  // Customer yang pesan
            'mitra_id' => $mitra->id,    // Cimol Bojot yang jual
            'total_amount' => 10000,
            'status' => 'pending',
            'created_at' => now()->subMinutes(5)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order1->id,
            'food_id' => $cimolOriginal->id,
            'quantity' => 1,
            'price' => 10000,
            'subtotal' => 10000
        ]);

        // Order 2 - Cimol Pedas (Customer orders from Cimol Bojot)
        $order2 = \App\Models\Order::create([
            'user_id' => $customer->id,  // Customer yang pesan
            'mitra_id' => $mitra->id,    // Cimol Bojot yang jual
            'total_amount' => 12000,
            'status' => 'completed',
            'created_at' => now()->subMinutes(15)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order2->id,
            'food_id' => $cimolPedas->id,
            'quantity' => 1,
            'price' => 12000,
            'subtotal' => 12000
        ]);

        // Order 3 - Cimol Keju (Customer orders from Cimol Bojot)
        $order3 = \App\Models\Order::create([
            'user_id' => $customer->id,  // Customer yang pesan
            'mitra_id' => $mitra->id,    // Cimol Bojot yang jual
            'total_amount' => 15000,
            'status' => 'cancelled',
            'created_at' => now()->subMinutes(25)
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order3->id,
            'food_id' => $cimolKeju->id,
            'quantity' => 1,
            'price' => 15000,
            'subtotal' => 15000
        ]);

        // Create CUSTOMER notifications (what customer sees)
        \App\Models\Notification::create([
            'user_id' => $customer->id,
            'order_id' => $order1->id,
            'message' => "Pesanan #{$order1->id} berhasil dibuat. Total: Rp " . number_format($order1->total_amount, 0, ',', '.') . ". Silakan lakukan pembayaran.",
            'status' => 'unread',
            'created_at' => now()->subMinutes(5)
        ]);

        \App\Models\Notification::create([
            'user_id' => $customer->id,
            'order_id' => $order1->id,
            'message' => "Pembayaran untuk pesanan #{$order1->id} sedang diproses. Silakan upload bukti pembayaran dan chat dengan mitra.",
            'status' => 'unread',
            'created_at' => now()->subMinutes(10)
        ]);

        \App\Models\Notification::create([
            'user_id' => $customer->id,
            'order_id' => $order2->id,
            'message' => "Pesanan #{$order2->id} telah selesai dan siap untuk dinikmati!",
            'status' => 'read',
            'created_at' => now()->subMinutes(15)
        ]);

        \App\Models\Notification::create([
            'user_id' => $customer->id,
            'order_id' => $order3->id,
            'message' => "Pesanan #{$order3->id} telah dibatalkan karena tidak ada konfirmasi pembayaran.",
            'status' => 'read',
            'created_at' => now()->subMinutes(20)
        ]);

        // Create MITRA notifications (what mitra sees)
        \App\Models\Notification::create([
            'user_id' => $mitra->id,
            'order_id' => $order1->id,
            'message' => "Pesanan baru #{$order1->id} diterima dari customer. Total: Rp " . number_format($order1->total_amount, 0, ',', '.') . ".",
            'status' => 'unread',
            'created_at' => now()->subMinutes(5)
        ]);

        \App\Models\Notification::create([
            'user_id' => $mitra->id,
            'order_id' => $order1->id,
            'message' => "Customer telah memproses pembayaran untuk pesanan #{$order1->id}. Menunggu bukti pembayaran.",
            'status' => 'unread',
            'created_at' => now()->subMinutes(10)
        ]);

        \App\Models\Notification::create([
            'user_id' => $mitra->id,
            'order_id' => $order2->id,
            'message' => "Pesanan #{$order2->id} telah diselesaikan dan diterima customer.",
            'status' => 'read',
            'created_at' => now()->subMinutes(15)
        ]);

        \App\Models\Notification::create([
            'user_id' => $mitra->id,
            'order_id' => $order3->id,
            'message' => "Pesanan #{$order3->id} dibatalkan karena customer tidak melakukan pembayaran.",
            'status' => 'read',
            'created_at' => now()->subMinutes(20)
        ]);

        echo "Sample notifications created successfully!\n";
        echo "Customer notifications: 4 items\n";
        echo "Mitra notifications: 4 items\n";
        echo "Login as customer: customer@test.com\n";
        echo "Login as mitra: cimol@sisarasa.com (Cimol Bojot)\n";
    }
}
