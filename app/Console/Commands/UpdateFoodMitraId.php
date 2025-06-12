<?php

namespace App\Console\Commands;

use App\Models\Food;
use Illuminate\Console\Command;

class UpdateFoodMitraId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'food:update-mitra-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing food records to set mitra_id from user_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update food records...');
        
        // Get all food records where mitra_id is null but user_id is not null
        $foods = Food::whereNull('mitra_id')
                    ->whereNotNull('user_id')
                    ->get();
        
        $this->info("Found {$foods->count()} food records to update.");
        
        $updated = 0;
        
        foreach ($foods as $food) {
            $food->mitra_id = $food->user_id;
            $food->save();
            $updated++;
            
            if ($updated % 50 == 0) {
                $this->info("Updated {$updated} records...");
            }
        }
        
        $this->info("Successfully updated {$updated} food records.");
        $this->info('All food records now have mitra_id set.');
        
        return 0;
    }
}
