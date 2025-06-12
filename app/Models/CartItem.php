<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['food_id', 'name', 'desc', 'price', 'img', 'quantity', 'selected', 'mitra_id'];
}
