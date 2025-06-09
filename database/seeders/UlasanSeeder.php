<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;   // <-- Import Model Order
use App\Models\Ulasan;  // <-- Import Model Ulasan

class UlasanSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk database.
     */
    public function run(): void
    {
        $this->command->info('Memulai UlasanSeeder...');

        // 1. Ambil semua pesanan yang statusnya sudah 'completed'
        // dan belum memiliki ulasan sama sekali untuk mencegah duplikat.
        $completedOrders = Order::where('status', 'completed')
                                ->whereDoesntHave('ulasan')
                                ->get();

        if ($completedOrders->isEmpty()) {
            $this->command->info('Tidak ditemukan pesanan "completed" yang bisa diberi ulasan.');
            return;
        }

        // Siapkan beberapa data dummy yang realistis
        $possibleComments = [
            'Rasa makanannya luar biasa, pengiriman juga cepat!',
            'Enak banget, porsinya pas. Pasti pesan lagi.',
            'Cukup bagus, tapi kemasannya sedikit rusak.',
            'Sesuai ekspektasi. Terima kasih.',
            'Salah satu makanan terbaik yang pernah saya coba.',
        ];

        $possibleReasons = ['Kualitas Bagus', 'Pengiriman Cepat', 'Kemasan Rusak', 'Rasa Enak', 'Harga Terjangkau'];

        // 2. Loop melalui setiap pesanan yang sudah selesai
        foreach ($completedOrders as $order) {
            
            // Pilih beberapa alasan secara acak (0 sampai 2 alasan)
            $selectedReasons = collect($possibleReasons)->random(rand(0, 2))->values()->all();

            // 3. Buat satu data ulasan untuk setiap pesanan tersebut
            Ulasan::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id, // Ulasan dibuat oleh user yang memesan
                'rating' => rand(3, 5),       // Beri rating acak antara 3 sampai 5 bintang
                'comment' => $possibleComments[array_rand($possibleComments)], // Pilih komentar acak
                'reasons' => $selectedReasons, // Simpan alasan yang dipilih
            ]);
        }

        $this->command->info('UlasanSeeder berhasil membuat ' . $completedOrders->count() . ' data ulasan baru.');
    }
}