<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MitraSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Cimol Bojot Aa',
            'email' => 'cimol@sisarasa.com',
            'password' => Hash::make('password123'),
            'role' => 'mitra',
            'address' => 'Jl. Cimol No. 123, Bandung',
            'phone' => '081234567890'
        ]);
    }
}
