<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlexureData extends Model
{
    use HasFactory;

    protected $table = 'flexure_data';
    protected $primaryKey = 'flexure_id';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'specimen_name',
        'test_type',

        'base',
        'height',
        'length',
        'area',

        // ✅ NEW: store sensor pressure in bar
        'pressure_bar',

        // legacy/optional
        'max_force',
        'stress',
        'moisture_content',

        'species_id',

        'photo',
    ];

    protected $casts = [
        'base' => 'float',
        'height' => 'float',
        'length' => 'float',
        'area' => 'float',

        'pressure_bar' => 'float',
        'max_force' => 'float',
        'stress' => 'float',
        'moisture_content' => 'float',

        'species_id' => 'integer',

        'photo' => 'string',
    ];
}
