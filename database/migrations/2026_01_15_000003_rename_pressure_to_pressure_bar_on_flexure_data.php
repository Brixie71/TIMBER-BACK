<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('flexure_data', function (Blueprint $table) {
            if (!Schema::hasColumn('flexure_data', 'pressure_bar')) {
                $table->float('pressure_bar')->nullable()->after('max_force');
            }
        });

        if (Schema::hasColumn('flexure_data', 'pressure')) {
            DB::statement("UPDATE flexure_data SET pressure_bar = pressure WHERE pressure_bar IS NULL");
        }

        Schema::table('flexure_data', function (Blueprint $table) {
            if (Schema::hasColumn('flexure_data', 'pressure')) {
                $table->dropColumn('pressure');
            }
        });
    }

    public function down(): void
    {
        Schema::table('flexure_data', function (Blueprint $table) {
            if (!Schema::hasColumn('flexure_data', 'pressure')) {
                $table->float('pressure')->nullable()->after('max_force');
            }
        });

        if (Schema::hasColumn('flexure_data', 'pressure_bar')) {
            DB::statement("UPDATE flexure_data SET pressure = pressure_bar WHERE pressure IS NULL");
        }

        Schema::table('flexure_data', function (Blueprint $table) {
            if (Schema::hasColumn('flexure_data', 'pressure_bar')) {
                $table->dropColumn('pressure_bar');
            }
        });
    }
};
