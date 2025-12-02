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
        // Add species_id to compressive_data if it doesn't exist
        if (!Schema::hasColumn('compressive_data', 'species_id')) {
            Schema::table('compressive_data', function (Blueprint $table) {
                $table->unsignedInteger('species_id')->nullable()->after('max_force');
                $table->index('species_id');
            });
        }

        // Add species_id to shear_data if it doesn't exist
        if (!Schema::hasColumn('shear_data', 'species_id')) {
            Schema::table('shear_data', function (Blueprint $table) {
                $table->unsignedInteger('species_id')->nullable()->after('max_force');
                $table->index('species_id');
            });
        }

        // Add species_id to flexure_data if it doesn't exist
        if (!Schema::hasColumn('flexure_data', 'species_id')) {
            Schema::table('flexure_data', function (Blueprint $table) {
                $table->unsignedInteger('species_id')->nullable()->after('max_force');
                $table->index('species_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('compressive_data', 'species_id')) {
            Schema::table('compressive_data', function (Blueprint $table) {
                $table->dropIndex(['species_id']);
                $table->dropColumn('species_id');
            });
        }

        if (Schema::hasColumn('shear_data', 'species_id')) {
            Schema::table('shear_data', function (Blueprint $table) {
                $table->dropIndex(['species_id']);
                $table->dropColumn('species_id');
            });
        }

        if (Schema::hasColumn('flexure_data', 'species_id')) {
            Schema::table('flexure_data', function (Blueprint $table) {
                $table->dropIndex(['species_id']);
                $table->dropColumn('species_id');
            });
        }
    }
};