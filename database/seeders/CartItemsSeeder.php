<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartItem;

class CartItemsSeeder extends Seeder
{
    public function run()
    {
        CartItem::create([
            'name' => 'Pizza Margherita',
            'desc' => 'Pizza dengan saus tomat, mozzarella, dan basil',
            'price' => 70000,
            'img' => 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480_1_5x/img/recipe/ras/Assets/2caca97b-77f6-48e7-837d-62642c0c9861/Derivates/12591894-e010-4a02-b04e-2627d8374298.jpg',
            'quantity' => 1,
            'selected' => false
        ]);

        CartItem::create([
            'name' => 'Spaghetti Carbonara',
            'desc' => 'Spaghetti dengan saus carbonara klasik',
            'price' => 85000,
            'img' => 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480_1_5x/img/recipe/ras/Assets/2caca97b-77f6-48e7-837d-62642c0c9861/Derivates/12591894-e010-4a02-b04e-2627d8374298.jpg',
            'quantity' => 1,
            'selected' => false
        ]);
        
        CartItem::create([
            'name' => 'Spaghetti Original',
            'desc' => 'Spaghetti dengan saus original',
            'price' => 65000,
            'img' => 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480_1_5x/img/recipe/ras/Assets/2caca97b-77f6-48e7-837d-62642c0c9861/Derivates/12591894-e010-4a02-b04e-2627d8374298.jpg',
            'quantity' => 1,
            'selected' => false
        ]);
    }
}

