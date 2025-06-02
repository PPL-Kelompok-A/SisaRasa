<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // BAGIAN INI DIKOMENTARI SEMENTARA:
        // Schema::create('orders', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->integer('user_id');
        //     $table->date('tanggal');
        //     $table->string('status');
        //     $table->integer('kode');
        //     $table->integer('jumlah_harga');
        //     $table->timestamps();
        // });
        // ^^^ BAGIAN INI DIKOMENTARI SEMENTARA
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};