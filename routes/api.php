<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ShearDataController;
use App\Http\Controllers\FlexureDataController;
use App\Http\Controllers\CompressiveDataController;
use App\Http\Controllers\CalibrationSettingController;
use App\Http\Controllers\ReferenceValueController;
use App\Http\Controllers\ActuatorCalibrationController;
use App\Http\Controllers\MeasurementDetectionSettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================================================
// COMPRESSIVE DATA ROUTES
// ============================================================================

// ✅ GET all compressive data
Route::get('compressive-data', function (Request $request) {
    try {
        $data = DB::table('compressive_data')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data, 200);

    } catch (\Exception $e) {
        Log::error('❌ Error fetching compressive data: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// ✅ POST new compressive data
Route::post('compressive-data', function (Request $request) {
    Log::info('📥 Compressive data POST received');
    Log::info('Request data:', $request->all());

    try {
        $validated = $request->validate([
            'test_type' => 'required|string',
            'specimen_name' => 'required|string|max:255',
            'base' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'moisture_content' => 'nullable|numeric',
            'max_force' => 'required|numeric',
            'pressure' => 'nullable|numeric',
            'stress' => 'nullable|numeric',
            'species_id' => 'nullable|integer',
            'photo' => 'nullable|string',
        ]);

        Log::info('✅ Validation passed');

        $id = DB::table('compressive_data')->insertGetId([
            'test_type' => $validated['test_type'],
            'specimen_name' => $validated['specimen_name'],
            'base' => $validated['base'] ?? 0,
            'height' => $validated['height'] ?? 0,
            'length' => $validated['length'] ?? 0,
            'area' => $validated['area'] ?? 0,
            'moisture_content' => $validated['moisture_content'] ?? null,
            'max_force' => $validated['max_force'],
            'pressure' => $validated['pressure'] ?? ($validated['max_force'] ?? 0),
            'stress' => $validated['stress'] ?? null,
            'species_id' => $validated['species_id'] ?? null,
            'photo' => $validated['photo'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('✅ Data saved successfully with ID: ' . $id);

        return response()->json([
            'success' => true,
            'message' => 'Test data saved successfully',
            'id' => $id,
            'data' => $validated
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('❌ Validation failed:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('❌ Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// ============================================================================
// SHEAR DATA ROUTES
// ============================================================================

// ✅ GET all shear data
Route::get('shear-data', function (Request $request) {
    try {
        $data = DB::table('shear_data')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data, 200);

    } catch (\Exception $e) {
        Log::error('❌ Error fetching shear data: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// ✅ POST new shear data
Route::post('shear-data', function (Request $request) {
    Log::info('📥 Shear data POST received');
    Log::info('Request data:', $request->all());

    try {
        $validated = $request->validate([
            'test_type' => 'required|string',
            'specimen_name' => 'required|string|max:255',
            'base' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'moisture_content' => 'nullable|numeric',
            'max_force' => 'required|numeric',
            'pressure' => 'nullable|numeric',
            'stress' => 'nullable|numeric',
            'species_id' => 'nullable|integer',
            'photo' => 'nullable|string',
        ]);

        Log::info('✅ Validation passed');

        $id = DB::table('shear_data')->insertGetId([
            'test_type' => $validated['test_type'],
            'specimen_name' => $validated['specimen_name'],
            'base' => $validated['base'] ?? 0,
            'height' => $validated['height'] ?? 0,
            'length' => $validated['length'] ?? 0,
            'area' => $validated['area'] ?? 0,
            'moisture_content' => $validated['moisture_content'] ?? null,
            'max_force' => $validated['max_force'],
            'pressure' => $validated['pressure'] ?? ($validated['max_force'] ?? 0),
            'stress' => $validated['stress'] ?? null,
            'species_id' => $validated['species_id'] ?? null,
            'photo' => $validated['photo'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('✅ Data saved successfully with ID: ' . $id);

        return response()->json([
            'success' => true,
            'message' => 'Test data saved successfully',
            'id' => $id,
            'data' => $validated
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('❌ Validation failed:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('❌ Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// ============================================================================
// FLEXURE DATA ROUTES
// ============================================================================

// ✅ GET all flexure data
Route::get('flexure-data', function (Request $request) {
    try {
        $data = DB::table('flexure_data')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data, 200);

    } catch (\Exception $e) {
        Log::error('❌ Error fetching flexure data: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// ✅ POST new flexure data
Route::post('flexure-data', function (Request $request) {
    Log::info('📥 Flexure data POST received');
    Log::info('Request data:', $request->all());

    try {
        $validated = $request->validate([
            'test_type' => 'required|string',
            'specimen_name' => 'required|string|max:255',
            'base' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'moisture_content' => 'nullable|numeric',
            'max_force' => 'required|numeric',
            'pressure' => 'nullable|numeric',
            'stress' => 'nullable|numeric',
            'species_id' => 'nullable|integer',
            'photo' => 'nullable|string',
        ]);

        Log::info('✅ Validation passed');

        $id = DB::table('flexure_data')->insertGetId([
            'test_type' => $validated['test_type'],
            'specimen_name' => $validated['specimen_name'],
            'base' => $validated['base'] ?? 0,
            'height' => $validated['height'] ?? 0,
            'length' => $validated['length'] ?? 0,
            'area' => $validated['area'] ?? 0,
            'moisture_content' => $validated['moisture_content'] ?? null,
            'max_force' => $validated['max_force'],
            'pressure' => $validated['pressure'] ?? ($validated['max_force'] ?? 0),
            'stress' => $validated['stress'] ?? null,
            'species_id' => $validated['species_id'] ?? null,
            'photo' => $validated['photo'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('✅ Data saved successfully with ID: ' . $id);

        return response()->json([
            'success' => true,
            'message' => 'Test data saved successfully',
            'id' => $id,
            'data' => $validated
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('❌ Validation failed:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('❌ Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// ============================================================================
// CONTROLLER-BASED ROUTES (for edit/delete operations)
// ============================================================================

// Shear Data Routes (Controller-based for PUT/DELETE)
Route::prefix('shear-data')->group(function () {
    Route::get('{id}', [ShearDataController::class, 'show']);
    Route::put('{id}', [ShearDataController::class, 'update']);
    Route::delete('{id}', [ShearDataController::class, 'destroy']);
    Route::get('type/{testType}', [ShearDataController::class, 'getByTestType']);
});

// Flexure Data Routes (Controller-based for PUT/DELETE)
Route::prefix('flexure-data')->group(function () {
    Route::get('{id}', [FlexureDataController::class, 'show']);
    Route::put('{id}', [FlexureDataController::class, 'update']);
    Route::delete('{id}', [FlexureDataController::class, 'destroy']);
    Route::get('type/{testType}', [FlexureDataController::class, 'getByTestType']);
});

// Compressive Data Routes (Controller-based for PUT/DELETE)
Route::prefix('compressive-data')->group(function () {
    Route::get('{id}', [CompressiveDataController::class, 'show']);
    Route::put('{id}', [CompressiveDataController::class, 'update']);
    Route::delete('{id}', [CompressiveDataController::class, 'destroy']);
    Route::get('type/{testType}', [CompressiveDataController::class, 'getByTestType']);
});

// ============================================================================
// ACTUATOR CALIBRATION ROUTES
// ============================================================================

Route::prefix('actuator-calibrations')->group(function () {
    // Get active calibration
    Route::get('active', [ActuatorCalibrationController::class, 'getActive']);

    // Set midpoint (N)
    Route::post('set-midpoint', [ActuatorCalibrationController::class, 'setMidpoint']);

    // Set limits (DL or DR)
    Route::post('set-limits', [ActuatorCalibrationController::class, 'setLimits']);

    // Validate position
    Route::post('validate-position', [ActuatorCalibrationController::class, 'validatePosition']);

    // Reset calibration
    Route::post('reset', [ActuatorCalibrationController::class, 'reset']);

    // CRUD operations
    Route::get('/', [ActuatorCalibrationController::class, 'index']);
    Route::post('/', [ActuatorCalibrationController::class, 'store']);
    Route::get('{id}', [ActuatorCalibrationController::class, 'show']);
    Route::put('{id}', [ActuatorCalibrationController::class, 'update']);
    Route::delete('{id}', [ActuatorCalibrationController::class, 'destroy']);
});

// Alias routes for actuator calibration (singular form for Flask compatibility)
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

// Calibration Routes
Route::prefix('calibration')->group(function () {
    Route::get('', [CalibrationSettingController::class, 'index']);
    Route::get('active', [CalibrationSettingController::class, 'getActive']);
    Route::post('', [CalibrationSettingController::class, 'store']);
    Route::get('{id}', [CalibrationSettingController::class, 'show']);
    Route::put('{id}', [CalibrationSettingController::class, 'update']);
    Route::delete('{id}', [CalibrationSettingController::class, 'destroy']);
    Route::post('{id}/activate', [CalibrationSettingController::class, 'setActive']);
});

// Seven-Segment Routes
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
// REFERENCE VALUES ROUTES (Wood Species)
// ============================================================================

// Reference Values Routes
Route::prefix('reference-values')->group(function () {
    Route::get('', [ReferenceValueController::class, 'index']);
    Route::post('', [ReferenceValueController::class, 'store']);
    Route::get('{id}', [ReferenceValueController::class, 'show']);
    Route::put('{id}', [ReferenceValueController::class, 'update']);
    Route::delete('{id}', [ReferenceValueController::class, 'destroy']);
    Route::get('meta/strength-groups', [ReferenceValueController::class, 'getStrengthGroups']);
    Route::get('search/species', [ReferenceValueController::class, 'searchSpecies']);
});

Route::get('/measurement-settings/active', [MeasurementDetectionSettingController::class, 'active']);
Route::post('/measurement-settings', [MeasurementDetectionSettingController::class, 'store']);