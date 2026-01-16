<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $tables = ['compressive_data', 'flexure_data', 'shear_data'];
        $columns = [
            'base' => 'DECIMAL(16,8) NOT NULL',
            'height' => 'DECIMAL(16,8) NOT NULL',
            'length' => 'DECIMAL(16,8) NOT NULL',
            'area' => 'DECIMAL(16,8) NOT NULL',
            'pressure' => 'DECIMAL(16,8) NULL',
            'moisture_content' => 'DECIMAL(16,8) NULL',
            'max_force' => 'DECIMAL(16,8) NOT NULL',
            'stress' => 'DECIMAL(16,8) NULL',
        ];

        foreach ($tables as $table) {
            foreach ($columns as $column => $definition) {
                DB::statement("ALTER TABLE {$table} MODIFY {$column} {$definition}");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $tables = ['compressive_data', 'flexure_data', 'shear_data'];
        $columns = [
            'base' => 'FLOAT NOT NULL',
            'height' => 'FLOAT NOT NULL',
            'length' => 'FLOAT NOT NULL',
            'area' => 'FLOAT NOT NULL',
            'pressure' => 'FLOAT NULL',
            'moisture_content' => 'FLOAT NULL',
            'max_force' => 'FLOAT NOT NULL',
            'stress' => 'FLOAT NULL',
        ];

        foreach ($tables as $table) {
            foreach ($columns as $column => $definition) {
                DB::statement("ALTER TABLE {$table} MODIFY {$column} {$definition}");
            }
        }
    }
};
