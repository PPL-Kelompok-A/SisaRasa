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
        \App\Models\History::create([
            'name' => 'Cimol Original',
            'status' => 'Completed',
            'quantity' => 1,
            'price' => 8000,
            'image' => 'images/bbq.jpg',
            'payment_method' => 'Dana'
        ]);

        \App\Models\History::create([
            'name' => 'Cimol Pedas',
            'status' => 'Completed',
            'quantity' => 2,
            'price' => 10000,
            'image' => 'images/cimol2.jpg',
            'payment_method' => 'Ovo'
        ]);

        \App\Models\History::create([
            'name' => 'Cimol Keju',
            'status' => 'Completed',
            'quantity' => 1,
            'price' => 12000,
            'image' => 'images/cimol-keju-lumer-1.jpeg',
            'payment_method' => 'Dana'
        ]);
    }
}

//php artisan db:seed --class=HistorySeeder
