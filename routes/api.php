<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShearDataController;
use App\Http\Controllers\FlexureDataController;
use App\Http\Controllers\CompressiveDataController;
use App\Http\Controllers\CalibrationSettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Shear Data Routes
Route::prefix('shear-data')->group(function () {
    Route::get('/', [ShearDataController::class, 'index']);
    Route::get('/{id}', [ShearDataController::class, 'show']);
    Route::post('/{id}', [ShearDataController::class, 'store']);
    Route::put('/{id}', [ShearDataController::class, 'update']);
    Route::delete('/{id}', [ShearDataController::class, 'destroy']);
    Route::get('/type/{testType}', [ShearDataController::class, 'getByTestType']);
});

// Flexure Data Routes
Route::prefix('flexure-data')->group(function () {
    Route::get('/', [FlexureDataController::class, 'index']);
    Route::get('/{id}', [FlexureDataController::class, 'show']);
    Route::post('/{id}', [FlexureDataController::class, 'store']);
    Route::put('/{id}', [FlexureDataController::class, 'update']);
    Route::delete('/{id}', [FlexureDataController::class, 'destroy']);
    Route::get('/type/{testType}', [FlexureDataController::class, 'getByTestType']);
});

// Compressive Data Routes
Route::prefix('compressive-data')->group(function () {
    Route::get('/', [CompressiveDataController::class, 'index']);
    Route::get('/{id}', [CompressiveDataController::class, 'show']);
    Route::post('/', [CompressiveDataController::class, 'store']);
    Route::put('/{id}', [CompressiveDataController::class, 'update']);
    Route::delete('/{id}', [CompressiveDataController::class, 'destroy']);
});

// Calibration Settings Routes
Route::prefix('calibration')->group(function () {
    Route::get('/', [CalibrationSettingController::class, 'index']);
    Route::get('/active', [CalibrationSettingController::class, 'getActive']);
    Route::get('/{id}', [CalibrationSettingController::class, 'show']);
    Route::post('/', [CalibrationSettingController::class, 'store']);
    Route::put('/{id}', [CalibrationSettingController::class, 'update']);
    Route::delete('/{id}', [CalibrationSettingController::class, 'destroy']);
    Route::post('/{id}/activate', [CalibrationSettingController::class, 'setActive']);
});

// Seven-Segment Calibration Routes
Route::prefix('seven-segment')->group(function () {
    // Create default segment boxes
    Route::match(['post', 'options'], '/create-defaults', function (Request $request) {
        if ($request->isMethod('options')) {
            return response('', 200);
        }

        $displayBox = $request->input('displayBox');
        $numDigits = $request->input('numDigits', 3);

        if (!$displayBox) {
            return response()->json([
                'success' => false,
                'error' => 'displayBox is required'
            ], 400);
        }

        // Create default segment boxes
        $digitBoxes = [];
        $displayWidth = $displayBox['width'];
        $displayHeight = $displayBox['height'];
        $digitWidth = $displayWidth / $numDigits;

        for ($digitIdx = 0; $digitIdx < $numDigits; $digitIdx++) {
            $digitX = $displayBox['x'] + ($digitIdx * $digitWidth);
            $digitY = $displayBox['y'];

            // Segment templates: [x_ratio, y_ratio, width_ratio, height_ratio]
            $segmentTemplates = [
                [0.2, 0.05, 0.6, 0.1],   // A - Top
                [0.7, 0.1, 0.2, 0.35],   // B - Top-right
                [0.7, 0.55, 0.2, 0.35],  // C - Bottom-right
                [0.2, 0.85, 0.6, 0.1],   // D - Bottom
                [0.1, 0.55, 0.2, 0.35],  // E - Bottom-left
                [0.1, 0.1, 0.2, 0.35],   // F - Top-left
                [0.2, 0.45, 0.6, 0.1]    // G - Middle
            ];

            $segments = [];
            foreach ($segmentTemplates as [$xRatio, $yRatio, $wRatio, $hRatio]) {
                $segments[] = [
                    'x' => $digitX + ($xRatio * $digitWidth),
                    'y' => $digitY + ($yRatio * $displayHeight),
                    'width' => $wRatio * $digitWidth,
                    'height' => $hRatio * $displayHeight
                ];
            }

            $digitBoxes[] = $segments;
        }

        return response()->json([
            'success' => true,
            'segmentBoxes' => $digitBoxes
        ]);
    });

    // Calibrate (save to database)
    Route::match(['post', 'options'], '/calibrate', function (Request $request) {
        if ($request->isMethod('options')) {
            return response('', 200);
        }
        return app(CalibrationSettingController::class)->store($request);
    });

    // Get active calibration
    Route::get('/calibration', [CalibrationSettingController::class, 'getActive']);

    // Recognize (proxy to Python)
    Route::match(['post', 'options'], '/recognize', function (Request $request) {
        if ($request->isMethod('options')) {
            return response('', 200);
        }

        return response()->json([
            'success' => false,
            'error' => 'Recognition must be done through Python Flask backend at ' . env('VITE_PYTHON_API_URL', 'http://localhost:5000')
        ], 501);
    });
});