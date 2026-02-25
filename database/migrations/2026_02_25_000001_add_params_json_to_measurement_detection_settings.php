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
        Schema::table('measurement_detection_settings', function (Blueprint $table) {
            // Flexible storage for full parameter set and per-test presets
            $table->json('params')->nullable()->after('mm_per_pixel');
            $table->json('presets')->nullable()->after('params');

            // Extra individual columns for newer params (optional, keeps quick access)
            $table->integer('mask_thresh')->default(0)->after('threshold2');
            $table->integer('open_k')->default(3)->after('mask_thresh');
            $table->integer('close_k')->default(5)->after('open_k');
            $table->integer('edge_thickness')->default(2)->after('contrast');
            $table->string('roi_shape', 16)->default('square')->after('roi_size');
            $table->boolean('denoise_enabled')->default(true)->after('edge_thickness');
            $table->integer('denoise_h')->default(6)->after('denoise_enabled');
            $table->integer('denoise_template')->default(7)->after('denoise_h');
            $table->integer('denoise_search')->default(21)->after('denoise_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('measurement_detection_settings', function (Blueprint $table) {
            $table->dropColumn([
                'params',
                'presets',
                'mask_thresh',
                'open_k',
                'close_k',
                'edge_thickness',
                'roi_shape',
                'denoise_enabled',
                'denoise_h',
                'denoise_template',
                'denoise_search',
            ]);
        });
    }
};
