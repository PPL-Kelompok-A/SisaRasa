<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MitraDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MitraSeeder extends Seeder
{
    public function run(): void
    {
        // Mitra 1 - Cimol Bojot
        $mitra1 = User::create([
            'name' => 'Cimol Bojot Aa',
            'email' => 'cimol@sisarasa.com',
            'password' => Hash::make('password123'),
            'role' => 'mitra',
            'address' => 'Jl. Dipatiukur No. 123, Bandung',
            'phone' => '081234567890'
        ]);

        MitraDetail::create([
            'user_id' => $mitra1->id,
            'alamat_lengkap' => 'Jl. Dipatiukur No. 123, Lebakgede, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132',
            'latitude' => -6.893291,
            'longitude' => 107.619854
        ]);

        // Mitra 2 - Nasi Goreng Mafia
        $mitra2 = User::create([
            'name' => 'Nasi Goreng Mafia',
            'email' => 'nasgor.mafia@sisarasa.com',
            'password' => Hash::make('password123'),
            'role' => 'mitra',
            'address' => 'Jl. Ir. H. Djuanda No. 45, Bandung',
            'phone' => '081234567891'
        ]);

        MitraDetail::create([
            'user_id' => $mitra2->id,
            'alamat_lengkap' => 'Jl. Ir. H. Djuanda No. 45, Dago, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132',
            'latitude' => -6.887752,
            'longitude' => 107.613620
        ]);

        // Mitra 3 - Warung Steak Ranjau
        $mitra3 = User::create([
            'name' => 'Warung Steak Ranjau',
            'email' => 'steak.ranjau@sisarasa.com',
            'password' => Hash::make('password123'),
            'role' => 'mitra',
            'address' => 'Jl. Tubagus Ismail No. 78, Bandung',
            'phone' => '081234567892'
        ]);

        MitraDetail::create([
            'user_id' => $mitra3->id,
            'alamat_lengkap' => 'Jl. Tubagus Ismail No. 78, Sekeloa, Kecamatan Coblong, Kota Bandung, Jawa Barat 40134',
            'latitude' => -6.888517,
            'longitude' => 107.619441
        ]);

        // Mitra 4 - Sate Taichan Bang Jali
        $mitra4 = User::create([
            'name' => 'Sate Taichan Bang Jali',
            'email' => 'sate.taichan@sisarasa.com',
            'password' => Hash::make('password123'),
            'role' => 'mitra',
            'address' => 'Jl. Gelap Nyawang No. 12, Bandung',
            'phone' => '081234567893'
        ]);

        MitraDetail::create([
            'user_id' => $mitra4->id,
            'alamat_lengkap' => 'Jl. Gelap Nyawang No. 12, Lebaksiliwangi, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132',
            'latitude' => -6.885693,
            'longitude' => 107.611846
        ]);

        // Mitra 5 - Dimsum & Mie Ayam Ko Aan
        $mitra5 = User::create([
            'name' => 'Dimsum & Mie Ayam Ko Aan',
            'email' => 'dimsum.aan@sisarasa.com',
            'password' => Hash::make('password123'),
            'role' => 'mitra',
            'address' => 'Jl. Sumur Bandung No. 90, Bandung',
            'phone' => '081234567894'
        ]);

        MitraDetail::create([
            'user_id' => $mitra5->id,
            'alamat_lengkap' => 'Jl. Sumur Bandung No. 90, Lebakgede, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132',
            'latitude' => -6.891234,
            'longitude' => 107.615789
        ]);
    }
}
