<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompressiveData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'compressive_data';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'compressive_id';

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
        'stress',            // NEW: Auto-calculated
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
     * Calculate compressive stress (σc = P/A)
     *
     * @return float Stress in MPa (N/mm²)
     */
    public function calculateCompressiveStress(): float
    {
        if ($this->area <= 0) {
            return 0.0;
        }

        $forceN = $this->max_force * 1000; // Convert kN to N
        return $forceN / $this->area; // Returns MPa (N/mm² = MPa)
    }

    /**
     * Calculate pressure from maximum force
     * Pressure (N/mm²) = Force (N) / Area (mm²)
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

            // Auto-calculate stress if max_force or area changed
            if ($model->isDirty(['max_force', 'area']) || $model->stress === null) {
                $model->stress = $model->calculateCompressiveStress();
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
}
