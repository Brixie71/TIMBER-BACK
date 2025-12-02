<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlexureData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flexure_data';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'flexure_id';

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
        'stress',            // NEW: Auto-calculated (flexural stress)
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
     * Calculate flexural stress (f = Mc/I)
     * For center-point loading (3-point bending):
     * M = FL/4 (bending moment)
     * c = h/2 (distance to neutral axis)
     * I = bh³/12 (moment of inertia)
     *
     * @return float Stress in MPa (N/mm²)
     */
    public function calculateFlexuralStress(): float
    {
        if ($this->base <= 0 || $this->height <= 0 || $this->length <= 0) {
            return 0.0;
        }

        $forceN = $this->max_force * 1000; // Convert kN to N

        // For center-point loading (3-point bending)
        $M = ($forceN * $this->length) / 4; // Bending moment (N·mm)
        $c = $this->height / 2; // Distance to neutral axis (mm)
        $I = ($this->base * pow($this->height, 3)) / 12; // Moment of inertia (mm⁴)

        if ($I <= 0) {
            return 0.0;
        }

        return ($M * $c) / $I; // Returns MPa
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

            // Auto-calculate stress if relevant dimensions changed
            if ($model->isDirty(['max_force', 'base', 'height', 'length']) || $model->stress === null) {
                $model->stress = $model->calculateFlexuralStress();
            }
        });
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

    /**
     * Get bending moment for this test
     *
     * @return float Bending moment in N·mm
     */
    public function getBendingMoment(): float
    {
        $forceN = $this->max_force * 1000;
        return ($forceN * $this->length) / 4;
    }

    /**
     * Get moment of inertia for this specimen
     *
     * @return float Moment of inertia in mm⁴
     */
    public function getMomentOfInertia(): float
    {
        return ($this->base * pow($this->height, 3)) / 12;
    }
}
