<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'rating',
        'comment',
        'reasons',
    ];

    /**
     * (Opsional tapi sangat direkomendasikan)
     * Tambahkan ini untuk mengubah 'reasons' dari JSON ke array secara otomatis.
     */
    protected $casts = [
        'reasons' => 'array',
    ];
}