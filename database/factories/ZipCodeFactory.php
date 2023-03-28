<?php

namespace Database\Factories;

use App\Models\FederalEntity;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ZipCode>
 */
class ZipCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'zip_code' => fake()->randomNumber(4),
            'locality' => strtoupper(fake()->word()),
            'federal_entity_id' => function() {
                return FederalEntity::factory()->create()->id;
            },
            'municipality_id' => function() {
                return Municipality::factory()->create()->id;
            }
        ];
    }
}
