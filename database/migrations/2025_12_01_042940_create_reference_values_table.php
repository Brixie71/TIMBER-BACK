<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_values', function (Blueprint $table) {
            $table->id();
            $table->string('strength_group')->index(); // 'High', 'Moderately High', 'Medium'
            $table->string('common_name');
            $table->string('botanical_name')->nullable();
            $table->decimal('compression_parallel', 8, 2); // Fc (Mpa)
            $table->decimal('compression_perpendicular', 8, 2); // Fc⊥ (Mpa)
            $table->decimal('shear_parallel', 8, 2); // Fv (Mpa)
            $table->decimal('bending_tension_parallel', 8, 2); // FbFt (Mpa)
            $table->timestamps();
            $table->softDeletes();

            // Indexes for search
            $table->index('common_name');
            $table->index('botanical_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_values');
    }
};