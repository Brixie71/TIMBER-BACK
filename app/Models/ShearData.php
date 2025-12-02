<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShearData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shear_data';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'shear_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'test_type',
        'specimen_name',
        'base',              // Changed from 'width'
        'height',
        'length',
        'area',
        'pressure',          // NEW: Auto-calculated
        'moisture_content',
        'max_force',
        'stress',            // NEW: Auto-calculated (handles single/double shear)
        'species_id',        // For reference value comparison
        'photo'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'base' => 'float',
        'height' => 'float',
        'length' => 'float',
        'area' => 'float',
        'pressure' => 'float',
        'moisture_content' => 'float',
        'max_force' => 'float',
        'stress' => 'float',
        'species_id' => 'integer'
    ];

    /**
     * Relationship with ReferenceValue model
     */
    public function species()
    {
        return $this->belongsTo(ReferenceValue::class, 'species_id', 'id');
    }

    /**
     * Calculate shear stress (τv = V/A)
     * Handles both single and double shear tests
     *
     * @return float Stress in MPa (N/mm²)
     */
    public function calculateShearStress(): float
    {
        if ($this->area <= 0) {
            return 0.0;
        }

        $forceN = $this->max_force * 1000; // Convert kN to N

        // Check if it's double shear (force is distributed across two shear planes)
        $isDoubleShear = stripos($this->test_type, 'double') !== false;
        $shearForce = $isDoubleShear ? $forceN / 2 : $forceN;

        return $shearForce / $this->area; // Returns MPa
    }

    /**
     * Calculate pressure from maximum force
     *
     * @return float Pressure in N/mm²
     */
    public function calculatePressure(): float
    {
        if ($this->area <= 0) {
            return 0.0;
        }

        $forceN = $this->max_force * 1000; // Convert kN to N
        return $forceN / $this->area;
    }

    /**
     * Auto-calculate and update pressure and stress before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-calculate pressure if max_force or area changed
            if ($model->isDirty(['max_force', 'area']) || $model->pressure === null) {
                $model->pressure = $model->calculatePressure();
            }

            // Auto-calculate stress if max_force, area, or test_type changed
            if ($model->isDirty(['max_force', 'area', 'test_type']) || $model->stress === null) {
                $model->stress = $model->calculateShearStress();
            }
        });
    }

    /**
     * Check if this is a double shear test
     *
     * @return bool
     */
    public function isDoubleShear(): bool
    {
        return stripos($this->test_type, 'double') !== false;
    }

    /**
     * Get formatted stress value with unit
     *
     * @return string
     */
    public function getFormattedStress(): string
    {
        return number_format($this->stress, 2) . ' MPa';
    }

    /**
     * Get formatted pressure value with unit
     *
     * @return string
     */
    public function getFormattedPressure(): string
    {
        return number_format($this->pressure, 2) . ' N/mm²';
    }
}
