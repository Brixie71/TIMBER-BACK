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
        Schema::create('measurement_detection_settings', function (Blueprint $table) {
            $table->id();

            $table->integer('threshold1')->default(52);
            $table->integer('threshold2')->default(104);
            $table->integer('min_area')->default(1000);

            $table->integer('blur_kernel')->default(21);
            $table->integer('dilation')->default(1);
            $table->integer('erosion')->default(1);

            $table->integer('roi_size')->default(60);

            $table->integer('brightness')->default(0);
            $table->integer('contrast')->default(101);

            $table->decimal('mm_per_pixel', 10, 6)->default(0.100000);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Optional: index for faster "active" lookup
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_detection_settings');
    }
};
