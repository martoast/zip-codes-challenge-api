<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\FederalEntity;
use App\Models\ZipCode;
use App\Models\Municipality;
use App\Models\Settlement;
use App\Models\SettlementType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $federal_entities = FederalEntity::factory()->count(31)->create();
        $municipalities = Municipality::factory()->count(100)->create();

        $settlement_types = SettlementType::factory()->count(15)->create();

        $federal_entities->each(function($federal_entity) use($municipalities, $settlement_types) {
            $settlements = Settlement::factory()
                ->count(fake()->randomNumber(2))
                ->for($settlement_types->random())
                ->create();

            ZipCode::factory()
                ->count(200)
                ->for($federal_entity)
                ->for($municipalities->random())
                ->hasAttached($settlements)
                ->create();
        });
    }
}
