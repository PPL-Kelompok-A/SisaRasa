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
    // Menggunakan DB Facade (lebih dekat ke SQL Anda)
    DB::table('foods')->where('name', 'Cimol Original')->update(['category' => 'non-vegetarian']);
    DB::table('foods')->where('name', 'Cimol Pedas')->update(['category' => 'non-vegetarian']);
    DB::table('foods')->where('name', 'Cimol Keju')->update(['category' => 'non-vegetarian']);
    DB::table('foods')->where('name', 'Cimol BBQ')->update(['category' => 'non-vegetarian']);
    DB::table('foods')->where('name', 'Cimol Seaweed')->update(['category' => 'vegetarian']);

    // Atau menggunakan Eloquent jika Anda lebih suka
    // Food::where('name', 'Cimol Original')->update(['category' => 'non-vegetarian']);
    // ... dan seterusnya
}

public function down(): void
{
    // Opsional: Anda bisa membuat logika untuk mengembalikan nilai category ke NULL jika di-rollback
    DB::table('foods')
        ->whereIn('name', ['Cimol Original', 'Cimol Pedas', 'Cimol Keju', 'Cimol BBQ', 'Cimol Seaweed'])
        ->update(['category' => null]);
}
};
