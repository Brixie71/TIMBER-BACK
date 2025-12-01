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
        Schema::table('compressive_data', function (Blueprint $table) {
            $table->unsignedInteger('species_id')->nullable()->after('max_force_load');
        });

        Schema::table('shear_data', function (Blueprint $table) {
            $table->unsignedInteger('species_id')->nullable()->after('max_force_load');
        });

        Schema::table('flexure_data', function (Blueprint $table) {
            $table->unsignedInteger('species_id')->nullable()->after('max_force_load');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compressive_data', function (Blueprint $table) {
            $table->dropColumn('species_id');
        });

        Schema::table('shear_data', function (Blueprint $table) {
            $table->dropColumn('species_id');
        });

        Schema::table('flexure_data', function (Blueprint $table) {
            $table->dropColumn('species_id');
        });
    }
};