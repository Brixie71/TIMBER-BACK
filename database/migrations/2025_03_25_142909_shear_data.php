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
        Schema::create('shear_data', function (Blueprint $table) {
            $table->id('shear_id');
            $table->string('test_type');
            $table->string('specimen_name');
            $table->float('base');                      // Changed from 'width' to 'base'
            $table->float('height');
            $table->float('length');
            $table->float('area');
            $table->float('pressure')->nullable();      // NEW: Pressure in N/mm²
            $table->float('moisture_content')->nullable();
            $table->float('max_force');
            $table->float('stress')->nullable();        // NEW: Shear stress in N/mm²
            $table->unsignedInteger('species_id')->nullable();  // NEW: Foreign key to reference_values
            $table->string('photo')->nullable();
            $table->timestamps();

            // Add index for species_id for better query performance
            $table->index('species_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shear_data');
    }
};