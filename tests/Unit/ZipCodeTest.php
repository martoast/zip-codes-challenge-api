<?php

namespace Tests\Unit;

use App\Models\FederalEntity;
use App\Models\Municipality;
use App\Models\Settlement;
use App\Models\ZipCode;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ZipCodeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Global model for unit testing
     */
    protected $zip_code;

    /**
     * Settlements Generated
     */
    protected $total_settlemants = 5;
    
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();

        $settlements = Settlement::factory()
            ->count($this->total_settlemants)
            ->create();

        $this->zip_code = ZipCode::factory()
            ->hasAttached($settlements)
            ->create();
    }

    /**
     * test
     *
     * @return void
     */
    public function test_if_belongs_to_federal_entity()
    {
        $this->assertInstanceOf(
            FederalEntity::class, 
            $this->zip_code->federalEntity
        );
    }

    /**
     * test
     *
     * @return void
     */
    public function test_if_belongs_to_municipality()
    {
        $this->assertInstanceOf(
            Municipality::class, 
            $this->zip_code->municipality
        );
    }

    /**
     * test
     *
     * @return void
     */
    public function test_if_has_many_settlements()
    {
        $this->assertContainsOnlyInstancesOf(
            Settlement::class, 
            $this->zip_code->settlements
        );

        $this->assertCount(
            $this->total_settlemants, 
            $this->zip_code->settlements
        );
    }
}

