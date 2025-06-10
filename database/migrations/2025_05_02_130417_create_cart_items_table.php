<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // Pastikan tipe return-nya `: void`
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            // Perbaikan: Hapus .change() saat membuat tabel
            $table->unsignedBigInteger('mitra_id')->nullable(); // Sudah benar untuk kolom yang boleh NULL
            $table->string('name');
            $table->string('desc');
            $table->integer('price');
            $table->string('img');
            $table->integer('quantity')->default(1);
            $table->boolean('selected')->default(false);
            $table->timestamps();

            // Opsional: Foreign key constraint jika diperlukan
            // $table->foreign('mitra_id')->references('id')->on('mitras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // Pastikan tipe return-nya `: void`
    {
        // Perbaikan: Tutup kurung kurawal yang benar dan gunakan dropIfExists
        Schema::dropIfExists('cart_items');
    }
};