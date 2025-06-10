<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Anda bisa menghapus baris ini jika tidak digunakan
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Perbaikan: Hapus duplikasi $this->call([
        $this->call([
            MitraSeeder::class,
            CustomerSeeder::class,
            FoodSeeder::class,
            OrderSeeder::class,
            UlasanSeeder::class,
            NotificationSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}