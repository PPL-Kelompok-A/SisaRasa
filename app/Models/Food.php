<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    
    protected $table = 'foods';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
        'category', 
        'rating',
        'on_flash_sale',
        'discount_price',
        'discount_percentage',
        'flash_sale_starts_at',
        'flash_sale_ends_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_available' => 'boolean',
        'on_flash_sale' => 'boolean',
        'flash_sale_starts_at' => 'datetime',
        'flash_sale_ends_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Check if the food item is currently on an active flash sale
     *
     * @return bool
     */
    public function isOnActiveFlashSale()
    {
        if (!$this->on_flash_sale) {
            return false;
        }
        
        $now = now();
        
        // If no start/end dates are set, just check the on_flash_sale flag
        if (!$this->flash_sale_starts_at && !$this->flash_sale_ends_at) {
            return $this->on_flash_sale;
        }
        
        // If only start date is set, check if we've passed it
        if ($this->flash_sale_starts_at && !$this->flash_sale_ends_at) {
            return $now->gte($this->flash_sale_starts_at);
        }
        
        // If only end date is set, check if we haven't passed it
        if (!$this->flash_sale_starts_at && $this->flash_sale_ends_at) {
            return $now->lte($this->flash_sale_ends_at);
        }
        
        // If both dates are set, check if we're in the range
        return $now->between($this->flash_sale_starts_at, $this->flash_sale_ends_at);
    }
    
    /**
     * Get the current sale price (either discount_price or calculated from percentage)
     *
     * @return float
     */
    public function getCurrentPrice()
    {
        if (!$this->isOnActiveFlashSale()) {
            return $this->price;
        }
        
        if ($this->discount_price) {
            return $this->discount_price;
        }
        
        if ($this->discount_percentage) {
            $discountAmount = ($this->price * $this->discount_percentage) / 100;
            return $this->price - $discountAmount;
        }
        
        return $this->price;
    }
}
