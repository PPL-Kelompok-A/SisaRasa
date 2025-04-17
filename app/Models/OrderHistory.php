<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'order_id',
        'order_number',
        'total_amount',
        'status',
        'order_items',
        'completed_at'
    ];
    
    protected $casts = [
        'order_items' => 'array',
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
