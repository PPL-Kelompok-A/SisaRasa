<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food;
use Illuminate\Support\Facades\DB;

class UpdateFoodMitraIdSeeder extends Seeder
{
    public function run()
    {
        // Update semua food yang mitra_id nya null dengan user_id
        DB::statement('UPDATE foods SET mitra_id = user_id WHERE mitra_id IS NULL');
        
        echo "Updated food mitra_id successfully!\n";
        
        // Show sample data
        $foods = Food::select('id', 'name', 'mitra_id', 'user_id')->take(5)->get();
        foreach ($foods as $food) {
            echo "Food ID: {$food->id}, Name: {$food->name}, mitra_id: {$food->mitra_id}, user_id: {$food->user_id}\n";
        }
    }
}
