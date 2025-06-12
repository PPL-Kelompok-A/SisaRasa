<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Models\Food;
use Illuminate\Console\Command;

class UpdateCartItemsFoodId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:update-food-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing cart items to set food_id based on food name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update cart items...');
        
        // Get all cart items where food_id is null
        $cartItems = CartItem::whereNull('food_id')->get();
        
        $this->info("Found {$cartItems->count()} cart items to update.");
        
        $updated = 0;
        $notFound = 0;
        
        foreach ($cartItems as $cartItem) {
            // Try to find the food by name
            $food = Food::where('name', $cartItem->name)->first();
            
            if ($food) {
                $cartItem->food_id = $food->id;
                $cartItem->save();
                $updated++;
                
                if ($updated % 10 == 0) {
                    $this->info("Updated {$updated} cart items...");
                }
            } else {
                $this->warn("Food not found for cart item: {$cartItem->name}");
                $notFound++;
            }
        }
        
        $this->info("Successfully updated {$updated} cart items.");
        
        if ($notFound > 0) {
            $this->warn("{$notFound} cart items could not be matched to foods.");
            $this->warn("These items may need to be removed manually or the food names may have changed.");
        }
        
        $this->info('Cart items update completed.');
        
        return 0;
    }
}
