<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('measurement_detection_settings', function (Blueprint $table) {
            $table->string('profile_name')->nullable()->after('id');
            $table->text('notes')->nullable()->after('presets');
        });
    }

    public function down(): void
    {
        Schema::table('measurement_detection_settings', function (Blueprint $table) {
            $table->dropColumn(['profile_name', 'notes']);
        });
    }
};
