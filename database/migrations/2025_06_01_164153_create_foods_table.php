<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() // Hanya ada SATU metode up()
    {
        // BAGIAN INI HARUS DIKOMENTARI SEMENTARA:
        // Schema::create('foods', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('nama_foods');
        //     $table->integer('harga');
        //     $table->integer('stok');
        //     $table->string('keterangan');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};