<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\history::create([
            'name' => 'Italy Pizza',
            'status' => 'Completed',
            'quantity' => 1,
            'price' => 30000,
            'image' => 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480_1_5x/img/recipe/ras/Assets/2caca97b-77f6-48e7-837d-62642c0c9861/Derivates/12591894-e010-4a02-b04e-2627d8374298.jpg',
            'payment_method' => 'Dana'
        ]);

        \App\Models\History::create([
            'name' => 'Original Pizza',
            'status' => 'Completed',
            'quantity' => 1,
            'price' => 25000,
            'image' => 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480_1_5x/img/recipe/ras/Assets/2caca97b-77f6-48e7-837d-62642c0c9861/Derivates/12591894-e010-4a02-b04e-2627d8374298.jpg',
            'payment_method' => 'Ovo'
        ]);
    }
}
