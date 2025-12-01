<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompressiveData extends Model
{
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
        'width',
        'height',
        'length',
        'area',
        'moisture_content',
        'max_force_load',
        'species_id',
        'photo'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'width' => 'float',
        'height' => 'float',
        'length' => 'float',
        'area' => 'float',
        'moisture_content' => 'float',
        'max_force_load' => 'float'
    ];
}