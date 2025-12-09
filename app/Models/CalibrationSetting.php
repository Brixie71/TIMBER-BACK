<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalibrationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_type',
        'device_name',
        'display_box',
        'segment_boxes',
        'calibration_image_size',  // ← ADDED
        'num_digits',
        'has_decimal_point',
        'decimal_position',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'display_box' => 'array',
        'segment_boxes' => 'array',
        'calibration_image_size' => 'array',  // ← ADDED
        'is_active' => 'boolean',
        'has_decimal_point' => 'boolean',
        'num_digits' => 'integer',
        'decimal_position' => 'integer'
    ];

    /**
     * Boot the model and set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($calibration) {
            if (!isset($calibration->has_decimal_point)) {
                $calibration->has_decimal_point = false;
            }
            if (!isset($calibration->decimal_position)) {
                $calibration->decimal_position = 1;
            }
        });
    }

    /**
     * Scope to get only active calibrations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get seven-segment type calibrations
     */
    public function scopeSevenSegment($query)
    {
        return $query->where('setting_type', 'seven_segment');
    }

    /**
     * Format a raw number string with decimal point based on calibration settings
     *
     * @param string $rawNumber The raw number string (e.g., "319")
     * @return string Formatted number (e.g., "31.9")
     */
    public function formatNumber(string $rawNumber): string
    {
        if (!$this->has_decimal_point || str_contains($rawNumber, '?')) {
            return $rawNumber;
        }

        if (strlen($rawNumber) < $this->decimal_position) {
            return $rawNumber;
        }

        // Insert decimal from right
        // decimal_position=1 means XX.X (insert before last digit)
        // decimal_position=2 means X.XX (insert before last 2 digits)
        $insertPos = strlen($rawNumber) - $this->decimal_position;

        $formatted = substr($rawNumber, 0, $insertPos) . '.' . substr($rawNumber, $insertPos);

        return $formatted;
    }

    /**
     * Get human-readable decimal format description
     *
     * @return string|null
     */
    public function getDecimalFormatAttribute(): ?string
    {
        if (!$this->has_decimal_point) {
            return null;
        }

        return $this->decimal_position === 1 ? 'XX.X (e.g., 31.9)' : 'X.XX (e.g., 3.19)';
    }
}