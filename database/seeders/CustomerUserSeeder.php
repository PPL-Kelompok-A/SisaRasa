<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customer user
        $customer = \App\Models\User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer Test',
                'email' => 'customer@test.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'balance' => 100000
            ]
        );

        // Create mitra user
        $mitra = \App\Models\User::firstOrCreate(
            ['email' => 'mitra@test.com'],
            [
                'name' => 'Cimol Bojot (Mitra)',
                'email' => 'mitra@test.com',
                'password' => bcrypt('password'),
                'role' => 'mitra',
                'balance' => 50000
            ]
        );

        echo "Customer and Mitra users created successfully!\n";
        echo "Customer: {$customer->email} (ID: {$customer->id})\n";
        echo "Mitra: {$mitra->email} (ID: {$mitra->id})\n";
    }
}
