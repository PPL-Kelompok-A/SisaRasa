<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void // Atau public function up() tergantung versi Laravel Anda
    {
        Schema::table('foods', function (Blueprint $table) {
            // Menambahkan kolom 'category'
            // Anda bisa menyesuaikan posisi kolom dengan ->after('nama_kolom_sebelumnya')
            $table->string('category')->nullable(); // Contoh: setelah kolom 'keterangan' dari migrasi awal Anda

            // Menambahkan kolom 'rating'
            // DECIMAL(2, 1) berarti total 2 digit, dengan 1 digit di belakang koma (misal: 3.5, 4.0, 5.0)
            $table->decimal('rating', 2, 1)->nullable()->after('category'); // Contoh: setelah kolom 'category' yang baru ditambahkan
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void // Atau public function down()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn('rating');   // Hapus kolom rating dulu jika menambahkannya setelah category
            $table->dropColumn('category');
        });
    }
};