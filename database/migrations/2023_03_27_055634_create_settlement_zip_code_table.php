<?php

use App\Models\Settlement;
use App\Models\ZipCode;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settlement_zip_code', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Settlement::class)->constrained();
            $table->foreignIdFor(ZipCode::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_zip_code');
    }
};
