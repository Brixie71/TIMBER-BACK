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
        Schema::create('flexure_data', function (Blueprint $table) {
            $table->id('flexure_id');
            $table->string('test_type');  // Added test_type field
            $table->string('specimen_name');
            $table->float('width');
            $table->float('height');
            $table->float('length');  // Added length field
            $table->float('area');
            $table->float('moisture_content')->nullable();
            $table->float('max_force_load');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flexure_data');
    }
};