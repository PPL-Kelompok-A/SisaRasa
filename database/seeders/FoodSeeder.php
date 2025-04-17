<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\User;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $mitra = User::where('email', 'cimol@sisarasa.com')->first();

        if (!$mitra) {
            return;
        }

        $foods = [
            [
                'name' => 'Cimol Original',
                'description' => 'Cimol dengan bumbu original yang gurih dan renyah',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'name' => 'Cimol Pedas',
                'description' => 'Cimol dengan bumbu pedas yang menggugah selera',
                'price' => 12000,
                'is_available' => true,
            ],
            [
                'name' => 'Cimol Keju',
                'description' => 'Cimol dengan taburan keju yang melimpah',
                'price' => 15000,
                'is_available' => true,
            ],
            [
                'name' => 'Cimol BBQ',
                'description' => 'Cimol dengan bumbu BBQ yang lezat',
                'price' => 13000,
                'is_available' => false,
            ],
            [
                'name' => 'Cimol Seaweed',
                'description' => 'Cimol dengan taburan rumput laut yang gurih',
                'price' => 14000,
                'is_available' => true,
            ],
        ];

        foreach ($foods as $food) {
            Food::create([
                'user_id' => $mitra->id,
                'name' => $food['name'],
                'description' => $food['description'],
                'price' => $food['price'],
                'is_available' => $food['is_available'],
            ]);
        }
    }
}
