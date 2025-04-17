<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mitra_id',
        'status',
        'total_amount',
        'delivery_address'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
