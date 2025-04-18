<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Pembeli Demo',
            'email' => 'pembeli@sisarasa.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'address' => 'Jl. Pembeli No. 456, Bandung',
            'phone' => '089876543210',
            'balance' => 100000
        ]);
    }
}
