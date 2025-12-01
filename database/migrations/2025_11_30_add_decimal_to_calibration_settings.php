<?php
// database/migrations/2025_11_30_add_decimal_to_calibration_settings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('calibration_settings', function (Blueprint $table) {
            $table->boolean('has_decimal_point')->default(false)->after('num_digits');
            $table->integer('decimal_position')->default(1)->after('has_decimal_point')
                  ->comment('Position from right: 1=XX.X, 2=X.XX');
        });
    }

    public function down()
    {
        Schema::table('calibration_settings', function (Blueprint $table) {
            $table->dropColumn(['has_decimal_point', 'decimal_position']);
        });
    }
};