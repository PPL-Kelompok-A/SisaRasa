<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\User;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        // Mencari user mitra, pastikan user ini ada di database Anda
        // Anda bisa cek di tabel 'users' atau lewat seeder lain.
        $mitra = User::where('email', 'cimol@sisarasa.com')->first();

        if (!$mitra) {
            // Jika mitra tidak ditemukan, hentikan seeder agar tidak error.
            // Anda bisa tambahkan pesan ini untuk debugging:
            // $this->command->warn('User mitra cimol@sisarasa.com tidak ditemukan, FoodSeeder dilewati.');
            return;
        }

        $foods = [
            [
                'name' => 'Cimol Original',
                'description' => 'Cimol dengan bumbu original yang gurih dan renyah',
                'price' => 10000,
                'is_available' => true,
                'category' => 'non-vegetarian',
            ],
            [
                'name' => 'Cimol Pedas',
                'description' => 'Cimol dengan bumbu pedas yang menggugah selera',
                'price' => 12000,
                'is_available' => true,
                'category' => 'non-vegetarian',
            ],
            [
                'name' => 'Cimol Keju',
                'description' => 'Cimol dengan taburan keju yang melimpah',
                'price' => 15000,
                'is_available' => true,
                'category' => 'non-vegetarian',
            ],
            [
                'name' => 'Cimol BBQ',
                'description' => 'Cimol dengan bumbu BBQ yang lezat',
                'price' => 13000,
                'is_available' => false,
                'category' => 'non-vegetarian',
            ],
            [
                'name' => 'Cimol Seaweed',
                'description' => 'Cimol dengan taburan rumput laut yang gurih',
                'price' => 14000,
                'is_available' => true,
                'category' => 'vegetarian',
            ],
        ];

        foreach ($foods as $food) {
            Food::create([
                'user_id' => $mitra->id,
                'name' => $food['name'],
                'description' => $food['description'],
                'price' => $food['price'],
                'is_available' => $food['is_available'],
                'category' => $food['category'], // <-- TAMBAHKAN BARIS INI
            ]);
        }
    }
}