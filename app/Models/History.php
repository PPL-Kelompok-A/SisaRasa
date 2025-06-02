<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    // Ini baris penting buat ngasih tahu Laravel kalau nama tabel-nya singular
    protected $table = 'history';

    protected $fillable = ['name', 'status', 'quantity', 'price', 'image', 'payment_method'];
}
