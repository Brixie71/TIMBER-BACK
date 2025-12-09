<?php

namespace App\Http\Controllers;

use App\Models\CalibrationSetting;
use Illuminate\Http\Request;

class CalibrationSettingController extends Controller
{
    /**
     * Get the active calibration setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActive()
    {
        $calibration = CalibrationSetting::where('is_active', true)
            ->where('setting_type', 'seven_segment')
            ->latest()
            ->first();

        if (!$calibration) {
            return response()->json([
                'success' => false,
                'message' => 'No active calibration found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'calibration' => $calibration
        ]);
    }

    /**
     * Get all calibration settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $calibrations = CalibrationSetting::orderBy('created_at', 'desc')->get();
        return response()->json($calibrations);
    }

    /**
     * Get a specific calibration setting
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $calibration = CalibrationSetting::findOrFail($id);
        return response()->json($calibration);
    }

    /**
     * Save new calibration setting
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'setting_type' => 'sometimes|string|max:191',
            'device_name' => 'nullable|string|max:191',
            'display_box' => 'required|array',
            'display_box.x' => 'required|numeric',
            'display_box.y' => 'required|numeric',
            'display_box.width' => 'required|numeric',
            'display_box.height' => 'required|numeric',
            'segment_boxes' => 'required|array',
            'calibration_image_size' => 'nullable|array',  // ← ADDED
            'calibration_image_size.width' => 'required_with:calibration_image_size|numeric',  // ← ADDED
            'calibration_image_size.height' => 'required_with:calibration_image_size|numeric',  // ← ADDED
            'num_digits' => 'sometimes|integer|min:1|max:10',
            'has_decimal_point' => 'sometimes|boolean',
            'decimal_position' => 'sometimes|integer|min:1|max:9',
            'notes' => 'nullable|string'
        ]);

        // Deactivate all previous calibrations of the same type
        CalibrationSetting::where('setting_type', $validatedData['setting_type'] ?? 'seven_segment')
            ->update(['is_active' => false]);

        // Create new active calibration
        $calibration = CalibrationSetting::create([
            'setting_type' => $validatedData['setting_type'] ?? 'seven_segment',
            'device_name' => $validatedData['device_name'] ?? null,
            'display_box' => $validatedData['display_box'],
            'segment_boxes' => $validatedData['segment_boxes'],
            'calibration_image_size' => $validatedData['calibration_image_size'] ?? null,  // ← ADDED
            'num_digits' => $validatedData['num_digits'] ?? 3,
            'has_decimal_point' => $validatedData['has_decimal_point'] ?? false,
            'decimal_position' => $validatedData['decimal_position'] ?? 1,
            'is_active' => true,
            'notes' => $validatedData['notes'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Calibration saved successfully',
            'calibration' => $calibration
        ], 201);
    }

    /**
     * Update an existing calibration setting
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $calibration = CalibrationSetting::findOrFail($id);

        $validatedData = $request->validate([
            'setting_type' => 'sometimes|string|max:191',
            'device_name' => 'nullable|string|max:191',
            'display_box' => 'sometimes|array',
            'segment_boxes' => 'sometimes|array',
            'calibration_image_size' => 'nullable|array',  // ← ADDED
            'calibration_image_size.width' => 'required_with:calibration_image_size|numeric',  // ← ADDED
            'calibration_image_size.height' => 'required_with:calibration_image_size|numeric',  // ← ADDED
            'num_digits' => 'sometimes|integer|min:1|max:10',
            'has_decimal_point' => 'sometimes|boolean',
            'decimal_position' => 'sometimes|integer|min:1|max:9',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string'
        ]);

        // If setting as active, deactivate others
        if (isset($validatedData['is_active']) && $validatedData['is_active']) {
            CalibrationSetting::where('setting_type', $calibration->setting_type)
                ->where('id', '!=', $id)
                ->update(['is_active' => false]);
        }

        $calibration->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Calibration updated successfully',
            'calibration' => $calibration
        ]);
    }

    /**
     * Delete a calibration setting
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $calibration = CalibrationSetting::findOrFail($id);
        $calibration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Calibration deleted successfully'
        ], 200);
    }

    /**
     * Set a calibration as active
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setActive($id)
    {
        $calibration = CalibrationSetting::findOrFail($id);

        // Deactivate all other calibrations of the same type
        CalibrationSetting::where('setting_type', $calibration->setting_type)
            ->where('id', '!=', $id)
            ->update(['is_active' => false]);

        // Activate this one
        $calibration->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Calibration activated successfully',
            'calibration' => $calibration
        ]);
    }
}