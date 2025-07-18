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
         Schema::create('history', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('status');
        $table->integer('quantity');
        $table->integer('price');
        $table->string('image')->nullable(); // URL base64 atau path
        $table->string('payment_method');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
