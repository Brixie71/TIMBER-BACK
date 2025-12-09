<?php

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
        Schema::create('actuator_calibrations', function (Blueprint $table) {
            $table->id();
            $table->decimal('midpoint', 10, 2)->default(0)->comment('Midpoint position (N)');
            $table->decimal('max_distance_left', 10, 2)->default(0)->comment('Maximum distance left from midpoint (DL)');
            $table->decimal('max_distance_right', 10, 2)->default(0)->comment('Maximum distance right from midpoint (DR)');
            $table->boolean('is_active')->default(true)->comment('Is this calibration active');
            $table->boolean('is_calibrated')->default(false)->comment('Is calibration complete');
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actuator_calibrations');
    }
};