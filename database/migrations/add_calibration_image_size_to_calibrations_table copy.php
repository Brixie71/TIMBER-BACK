<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('calibration_settings', 'calibration_image_size')) {
            Schema::table('calibration_settings', function (Blueprint $table) {
                $table->json('calibration_image_size')->nullable()->after('segment_boxes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('calibration_settings', 'calibration_image_size')) {
            Schema::table('calibration_settings', function (Blueprint $table) {
                $table->dropColumn('calibration_image_size');
            });
        }
    }
};
