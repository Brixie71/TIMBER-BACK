<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'strength_group',
        'common_name',
        'botanical_name',
        'compression_parallel',
        'compression_perpendicular',
        'shear_parallel',
        'bending_tension_parallel'
    ];

    protected $casts = [
        'compression_parallel' => 'decimal:2',
        'compression_perpendicular' => 'decimal:2',
        'shear_parallel' => 'decimal:2',
        'bending_tension_parallel' => 'decimal:2',
    ];

    /**
     * Scope to filter by strength group
     */
    public function scopeByStrengthGroup($query, $group)
    {
        return $query->where('strength_group', $group);
    }

    /**
     * Scope to search by name
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->where('common_name', 'like', "%{$search}%")
                    ->orWhere('botanical_name', 'like', "%{$search}%");
    }

    /**
     * Get formatted strength group name
     */
    public function getStrengthGroupLabelAttribute(): string
    {
        return match($this->strength_group) {
            'high' => 'High Strength Group',
            'moderately_high' => 'Moderately High Strength Group',
            'medium' => 'Medium Strength Group',
            default => $this->strength_group,
        };
    }
}