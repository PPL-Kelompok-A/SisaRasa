<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Food>
 */
class FoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Food::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mitra_id' => null, // Will be set by test if needed
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(10000, 100000),
            'image' => null,
            'is_available' => true,
            'category' => $this->faker->randomElement(['vegetarian', 'non-vegetarian']),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'on_flash_sale' => false,
            'discount_price' => null,
            'discount_percentage' => null,
            'flash_sale_starts_at' => null,
            'flash_sale_ends_at' => null,
        ];
    }
}
