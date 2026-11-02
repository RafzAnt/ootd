<?php

namespace Database\Factories;

use App\Models\Ootd;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ootd>
 */
class OotdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'image_path' => 'ootds/test/outfit.jpg',
            'description' => fake()->sentence(4),
            'occasion' => fake()->randomElement(array_keys(config('ootd.occasions'))),
        ];
    }
}
