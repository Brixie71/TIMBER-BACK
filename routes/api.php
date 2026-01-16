<?php

use App\Http\Controllers\ActuatorCalibrationController;
use App\Http\Controllers\CalibrationSettingController;
use App\Http\Controllers\CompressiveDataController;
use App\Http\Controllers\FlexureDataController;
use App\Http\Controllers\MeasurementDetectionSettingController;
use App\Http\Controllers\ReferenceValueController;
use App\Http\Controllers\ShearDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/**
 * NOTE ON UNITS (IMPORTANT)
 * - pressure column is now treated as PRESSURE IN BAR (raw sensor reading).
 * - Convert in frontend:
 *      MPa (N/mm^2) = bar * 0.1
 */

// ============================================================================
// COMPRESSIVE DATA ROUTES
// ============================================================================
Route::get('compressive-data/type/{testType}', [CompressiveDataController::class, 'getByTestType']);
Route::apiResource('compressive-data', CompressiveDataController::class)
    ->parameters(['compressive-data' => 'compressiveData'])
    ->except(['create', 'edit']);

// ============================================================================
// SHEAR DATA ROUTES
// ============================================================================
Route::get('shear-data/type/{testType}', [ShearDataController::class, 'getByTestType']);
Route::apiResource('shear-data', ShearDataController::class)
    ->parameters(['shear-data' => 'shearData'])
    ->except(['create', 'edit']);

// ============================================================================
// FLEXURE DATA ROUTES
// ============================================================================
Route::get('flexure-data/type/{testType}', [FlexureDataController::class, 'getByTestType']);
Route::apiResource('flexure-data', FlexureDataController::class)
    ->parameters(['flexure-data' => 'flexureData'])
    ->except(['create', 'edit']);

// ============================================================================
// ACTUATOR CALIBRATION ROUTES
// ============================================================================
Route::prefix('actuator-calibrations')->group(function () {
    Route::get('active', [ActuatorCalibrationController::class, 'getActive']);
    Route::post('set-midpoint', [ActuatorCalibrationController::class, 'setMidpoint']);
    Route::post('set-limits', [ActuatorCalibrationController::class, 'setLimits']);
    Route::post('validate-position', [ActuatorCalibrationController::class, 'validatePosition']);
    Route::post('reset', [ActuatorCalibrationController::class, 'reset']);

    Route::get('/', [ActuatorCalibrationController::class, 'index']);
    Route::post('/', [ActuatorCalibrationController::class, 'store']);
    Route::get('{id}', [ActuatorCalibrationController::class, 'show']);
    Route::put('{id}', [ActuatorCalibrationController::class, 'update']);
    Route::delete('{id}', [ActuatorCalibrationController::class, 'destroy']);
});

// Alias routes (singular form)
Route::prefix('actuator-calibration')->group(function () {
    Route::get('active', [ActuatorCalibrationController::class, 'getActive']);
    Route::post('set-midpoint', [ActuatorCalibrationController::class, 'setMidpoint']);
    Route::post('set-limits', [ActuatorCalibrationController::class, 'setLimits']);
    Route::post('validate-position', [ActuatorCalibrationController::class, 'validatePosition']);
    Route::post('reset', [ActuatorCalibrationController::class, 'reset']);
});

// ============================================================================
// CALIBRATION SETTINGS ROUTES (Seven-Segment Display)
// ============================================================================

Route::prefix('calibration')->group(function () {
    Route::get('', [CalibrationSettingController::class, 'index']);
    Route::get('active', [CalibrationSettingController::class, 'getActive']);
    Route::post('', [CalibrationSettingController::class, 'store']);
    Route::get('{id}', [CalibrationSettingController::class, 'show']);
    Route::put('{id}', [CalibrationSettingController::class, 'update']);
    Route::delete('{id}', [CalibrationSettingController::class, 'destroy']);
    Route::post('{id}/activate', [CalibrationSettingController::class, 'setActive']);
});

Route::prefix('seven-segment')->group(function () {
    Route::post('create-defaults', function (Request $request) {
        $displayBox = $request->input('displayBox');
        $numDigits = $request->input('numDigits', 3);

        if (!$displayBox) {
            return response()->json(['success' => false, 'error' => 'displayBox required'], 400);
        }

        $digitBoxes = [];
        $displayWidth = $displayBox['width'];
        $displayHeight = $displayBox['height'];
        $digitWidth = $displayWidth / $numDigits;

        for ($digitIdx = 0; $digitIdx < $numDigits; $digitIdx++) {
            $digitX = $displayBox['x'] + ($digitIdx * $digitWidth);
            $digitY = $displayBox['y'];

            $segmentTemplates = [
                [0.2, 0.05, 0.6, 0.1],
                [0.7, 0.1, 0.2, 0.35],
                [0.7, 0.55, 0.2, 0.35],
                [0.2, 0.85, 0.6, 0.1],
                [0.1, 0.55, 0.2, 0.35],
                [0.1, 0.1, 0.2, 0.35],
                [0.2, 0.45, 0.6, 0.1]
            ];

            $segments = [];
            foreach ($segmentTemplates as [$xRatio, $yRatio, $wRatio, $hRatio]) {
                $segments[] = [
                    'x' => $digitX + ($xRatio * $digitWidth),
                    'y' => $digitY + ($yRatio * $displayHeight),
                    'width' => $wRatio * $digitWidth,
                    'height' => $hRatio * $digitWidth
                ];
            }

            $digitBoxes[] = $segments;
        }

        return response()->json(['success' => true, 'segmentBoxes' => $digitBoxes]);
    });

    Route::post('calibrate', function (Request $request) {
        return app(CalibrationSettingController::class)->store($request);
    });

    Route::get('calibration', [CalibrationSettingController::class, 'getActive']);
});

// ============================================================================
// REFERENCE VALUES ROUTES
// ============================================================================

Route::prefix('reference-values')->group(function () {
    Route::get('', [ReferenceValueController::class, 'index']);
    Route::post('', [ReferenceValueController::class, 'store']);
    Route::get('{id}', [ReferenceValueController::class, 'show']);
    Route::put('{id}', [ReferenceValueController::class, 'update']);
    Route::delete('{id}', [ReferenceValueController::class, 'destroy']);
    Route::get('meta/strength-groups', [ReferenceValueController::class, 'getStrengthGroups']);
    Route::get('search/species', [ReferenceValueController::class, 'searchSpecies']);
});

// ============================================================================
// MEASUREMENT SETTINGS ROUTES
// ============================================================================

Route::get('/measurement-settings/active', [MeasurementDetectionSettingController::class, 'active']);
Route::post('/measurement-settings', [MeasurementDetectionSettingController::class, 'store']);
