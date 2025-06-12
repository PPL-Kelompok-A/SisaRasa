<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Order;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user dan order pertama sebagai contoh
        $user = User::first();
        $order = Order::first();

        if (!$user || !$order) {
            return;
        }

        $payments = [
            [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'payment_method' => 'dana',
                'amount' => 32500,
                'status' => 'success',
            ],
            [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'payment_method' => 'bca',
                'amount' => 30000,
                'status' => 'pending',
            ],
            [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'payment_method' => 'shopeepay',
                'amount' => 32000,
                'status' => 'failed',
            ],
        ];

        foreach ($payments as $payment) {
            Payment::create($payment);
        }
    }
}

