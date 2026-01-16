<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compressive_data', function (Blueprint $table) {
            $table->index('created_at', 'compressive_data_created_at_index');
            $table->index('test_type', 'compressive_data_test_type_index');
        });

        Schema::table('shear_data', function (Blueprint $table) {
            $table->index('created_at', 'shear_data_created_at_index');
            $table->index('test_type', 'shear_data_test_type_index');
        });

        Schema::table('flexure_data', function (Blueprint $table) {
            $table->index('created_at', 'flexure_data_created_at_index');
            $table->index('test_type', 'flexure_data_test_type_index');
        });
    }

    public function down(): void
    {
        Schema::table('compressive_data', function (Blueprint $table) {
            $table->dropIndex('compressive_data_created_at_index');
            $table->dropIndex('compressive_data_test_type_index');
        });

        Schema::table('shear_data', function (Blueprint $table) {
            $table->dropIndex('shear_data_created_at_index');
            $table->dropIndex('shear_data_test_type_index');
        });

        Schema::table('flexure_data', function (Blueprint $table) {
            $table->dropIndex('flexure_data_created_at_index');
            $table->dropIndex('flexure_data_test_type_index');
        });
    }
};
