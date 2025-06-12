<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'user_id' => 1,
                'message' => 'Pesanan kamu sudah diproses.',
                'status' => 'unread',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'user_id' => 1,
                'message' => 'Pesanan kamu sedang dikirim.',
                'status' => 'read',
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30)
            ],
            [
                'user_id' => 2,
                'message' => 'Pesanan kamu dibatalkan.',
                'status' => 'unread',
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now()->subHour()
            ],
        ]);
    }
}
