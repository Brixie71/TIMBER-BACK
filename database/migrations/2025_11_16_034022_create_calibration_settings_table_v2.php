<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table already exists before creating
        if (!Schema::hasTable('calibration_settings')) {
            Schema::create('calibration_settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_type')->default('seven_segment');
                $table->string('device_name')->nullable();
                $table->json('display_box');
                $table->json('segment_boxes');
                $table->integer('num_digits')->default(3);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('setting_type');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('calibration_settings');
    }
};