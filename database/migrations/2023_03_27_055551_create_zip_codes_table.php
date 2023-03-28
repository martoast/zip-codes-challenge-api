<?php

use App\Models\FederalEntity;
use App\Models\Municipality;

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
        Schema::create('zip_codes', function (Blueprint $table) {
            $table->id();
            $table->string('zip_code')->index();
            $table->string('locality')->nullable()->default(null);
            $table->foreignIdFor(FederalEntity::class)->constrained();
            $table->foreignIdFor(Municipality::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zip_codes');
    }
};
