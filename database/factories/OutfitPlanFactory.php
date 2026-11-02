<?php

namespace Database\Factories;

use App\Models\Ootd;
use App\Models\OutfitPlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<OutfitPlan>
 */
class OutfitPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ootd_id' => Ootd::factory(),
            'user_id' => fn (array $attributes) => Ootd::findOrFail($attributes['ootd_id'])->user_id,
            'planned_for' => Carbon::today()->addDays(fake()->numberBetween(0, 30)),
        ];
    }
}
