<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('user_id'); // Atau setelah kolom lain yang sesuai
            // Anda bisa tambahkan foreign key jika ada tabel 'mitras' atau 'users' sebagai mitra
            // $table->foreign('mitra_id')->references('id')->on('mitras')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn('mitra_id');
        });
    }
};