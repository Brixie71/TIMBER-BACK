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
        Schema::table('calibration_settings', function (Blueprint $table) {
            // Add calibration_image_size column after segment_boxes
            $table->json('calibration_image_size')->nullable()->after('segment_boxes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calibration_settings', function (Blueprint $table) {
            $table->dropColumn('calibration_image_size');
        });
    }
};