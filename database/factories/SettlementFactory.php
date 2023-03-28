<?php

namespace Database\Factories;

use App\Models\SettlementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Settlement>
 */
class SettlementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'key' => fake()->randomNumber(3),
            'name' => strtoupper(fake()->word()),
            'zone_type' => strtoupper(fake()->word()),
            'settlement_type_id' => function() {
                return SettlementType::factory()->create()->id;
            }
        ];
    }
}