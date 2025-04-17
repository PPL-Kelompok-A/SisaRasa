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
        Schema::table('foods', function (Blueprint $table) {
            $table->boolean('on_flash_sale')->default(false);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('discount_percentage')->nullable();
            $table->timestamp('flash_sale_starts_at')->nullable();
            $table->timestamp('flash_sale_ends_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn([
                'on_flash_sale',
                'discount_price',
                'discount_percentage',
                'flash_sale_starts_at',
                'flash_sale_ends_at'
            ]);
        });
    }
};
