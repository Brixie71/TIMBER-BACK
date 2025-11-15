<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShearDataController;
use App\Http\Controllers\FlexureDataController;
use App\Http\Controllers\CompressiveDataController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
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
    Route::get('/', [CompressiveDataController::class, 'index']); // Allow GET requests
    Route::get('/{id}', [CompressiveDataController::class, 'show']);
    Route::post('/', [CompressiveDataController::class, 'store']); // POST request
    Route::put('/{id}', [CompressiveDataController::class, 'update']);
    Route::delete('/{id}', [CompressiveDataController::class, 'destroy']);
});


