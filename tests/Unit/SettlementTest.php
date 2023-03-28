<?php

namespace Tests\Unit;

use App\Models\Settlement;
use App\Models\SettlementType;
use App\Models\ZipCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettlementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Global model for unit testing
     */
    protected $settlement;

    /**
     * Zip Codes Generated
     */
    protected $total_zip_codes;
    
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();

        $zip_codes = ZipCode::factory()->count($this->total_zip_codes)->create();

        $this->settlement = Settlement::factory()
            ->hasAttached($zip_codes)
            ->create();
    }

    /**
     * test
     *
     * @return void
     */
    public function test_if_has_one_settlement_type()
    {
        $this->assertInstanceOf(
            SettlementType::class, 
            $this->settlement->settlementType
        );
    }

    /**
     * test
     *
     * @return void
     */
    public function test_if_has_many_zip_codes()
    {
        $this->assertContainsOnlyInstancesOf(
            ZipCode::class, 
            $this->settlement->zipCodes
        );
    }
}
