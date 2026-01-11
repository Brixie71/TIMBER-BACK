<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementDetectionSetting extends Model
{
    protected $table = 'measurement_detection_settings';

    protected $fillable = [
        'threshold1',
        'threshold2',
        'min_area',
        'blur_kernel',
        'dilation',
        'erosion',
        'roi_size',
        'brightness',
        'contrast',
        'mm_per_pixel',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mm_per_pixel' => 'float',
    ];
}
