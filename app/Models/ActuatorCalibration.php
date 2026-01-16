<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActuatorCalibration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'midpoint',
        'max_distance_left',
        'max_distance_right',
        'is_active',
        'is_calibrated',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'midpoint' => 'decimal:2',
        'max_distance_left' => 'decimal:2',
        'max_distance_right' => 'decimal:2',
        'is_active' => 'boolean',
        'is_calibrated' => 'boolean',
    ];

    /**
     * Get the active calibration.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Set this calibration as active and deactivate all others.
     */
    public function setAsActive()
    {
        return DB::transaction(function () {
            self::where('is_active', true)->update(['is_active' => false]);

            $this->is_active = true;
            $this->save();

            return $this;
        });
    }

    /**
     * Calculate the total travel range.
     */
    public function getTotalRangeAttribute()
    {
        return $this->max_distance_left + $this->max_distance_right;
    }

    /**
     * Get the minimum position (leftmost).
     */
    public function getMinPositionAttribute()
    {
        return $this->midpoint - $this->max_distance_left;
    }

    /**
     * Get the maximum position (rightmost).
     */
    public function getMaxPositionAttribute()
    {
        return $this->midpoint + $this->max_distance_right;
    }

    /**
     * Validate if a position is within calibrated limits.
     */
    public function isPositionValid($position)
    {
        if (!$this->is_calibrated) {
            return false;
        }

        return $position >= $this->min_position && $position <= $this->max_position;
    }

    /**
     * Get distance from midpoint.
     */
    public function getDistanceFromMidpoint($position)
    {
        return $position - $this->midpoint;
    }

    /**
     * Get direction from midpoint.
     */
    public function getDirectionFromMidpoint($position)
    {
        return $position < $this->midpoint ? 'left' : 'right';
    }
}
