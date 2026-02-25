<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementDetectionSetting extends Model
{
    protected $table = 'measurement_detection_settings';

    protected $fillable = [
        'profile_name',
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
        'params',
        'presets',
        'roi_shape',
        'mask_thresh',
        'open_k',
        'close_k',
        'edge_thickness',
        'denoise_enabled',
        'denoise_h',
        'denoise_template',
        'denoise_search',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mm_per_pixel' => 'float',
        'params' => 'array',
        'presets' => 'array',
        'denoise_enabled' => 'boolean',
    ];
}
