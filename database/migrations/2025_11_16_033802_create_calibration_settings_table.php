<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calibration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_type')->default('seven_segment'); // For future: could be 'wood_measurement', etc.
            $table->string('device_name')->nullable(); // Optional: to support multiple devices
            $table->json('display_box'); // Store display bounding box
            $table->json('segment_boxes'); // Store segment bounding boxes
            $table->integer('num_digits')->default(3);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calibration_settings');
    }
};