<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Terhubung ke tabel users
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Terhubung ke tabel orders
            $table->tinyInteger('rating'); // Angka 1-5
            $table->text('comment')->nullable(); // Komentar dari user, boleh kosong
            $table->json('reasons')->nullable(); // Untuk menyimpan tag alasan (reasons), boleh kosong
            $table->timestamps(); // Membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasans');
    }
};