<?php

namespace App\Http\Controllers;

use App\Models\ActuatorCalibration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActuatorCalibrationController extends Controller
{
    /**
     * Display a listing of the calibrations.
     */
    public function index()
    {
        $calibrations = ActuatorCalibration::orderBy('created_at', 'desc')->get();
        return response()->json($calibrations);
    }

    /**
     * Get the active calibration.
     */
    public function getActive()
    {
        $calibration = ActuatorCalibration::getActive();

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
     * Store a newly created calibration.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'midpoint' => 'required|numeric',
            'max_distance_left' => 'required|numeric|min:0',
            'max_distance_right' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $calibration = ActuatorCalibration::create([
            'midpoint' => $request->midpoint,
            'max_distance_left' => $request->max_distance_left,
            'max_distance_right' => $request->max_distance_right,
            'is_calibrated' => true,
            'is_active' => true,
            'notes' => $request->notes,
        ]);

        // Set as active (will deactivate others)
        $calibration->setAsActive();

        return response()->json([
            'success' => true,
            'message' => 'Calibration created successfully',
            'calibration' => $calibration
        ], 201);
    }

    /**
     * Display the specified calibration.
     */
    public function show($id)
    {
        $calibration = ActuatorCalibration::find($id);

        if (!$calibration) {
            return response()->json([
                'success' => false,
                'message' => 'Calibration not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'calibration' => $calibration
        ]);
    }

    /**
     * Update the specified calibration.
     */
    public function update(Request $request, $id)
    {
        $calibration = ActuatorCalibration::find($id);

        if (!$calibration) {
            return response()->json([
                'success' => false,
                'message' => 'Calibration not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'midpoint' => 'sometimes|numeric',
            'max_distance_left' => 'sometimes|numeric|min:0',
            'max_distance_right' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $calibration->update($request->only([
            'midpoint',
            'max_distance_left',
            'max_distance_right',
            'notes'
        ]));

        // Check if calibration is complete
        if ($calibration->midpoint != 0 &&
            $calibration->max_distance_left > 0 &&
            $calibration->max_distance_right > 0) {
            $calibration->is_calibrated = true;
            $calibration->save();
        }

        // If setting as active
        if ($request->has('is_active') && $request->is_active) {
            $calibration->setAsActive();
        }

        return response()->json([
            'success' => true,
            'message' => 'Calibration updated successfully',
            'calibration' => $calibration->fresh()
        ]);
    }

    /**
     * Set midpoint for calibration.
     */
    public function setMidpoint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'midpoint' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get active calibration or create new one
        $calibration = ActuatorCalibration::getActive();

        if (!$calibration) {
            $calibration = ActuatorCalibration::create([
                'midpoint' => $request->midpoint,
                'is_active' => true,
            ]);
        } else {
            $calibration->update(['midpoint' => $request->midpoint]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Midpoint set successfully',
            'calibration' => $calibration
        ]);
    }

    /**
     * Set travel limits (DL or DR).
     */
    public function setLimits(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_position' => 'required|numeric',
            'direction' => 'required|in:left,right',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $calibration = ActuatorCalibration::getActive();

        if (!$calibration) {
            return response()->json([
                'success' => false,
                'message' => 'No active calibration found. Please set midpoint first.'
            ], 400);
        }

        if ($calibration->midpoint == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please set midpoint first'
            ], 400);
        }

        $distance = abs($request->current_position - $calibration->midpoint);

        if ($request->direction === 'left') {
            $calibration->max_distance_left = $distance;
        } else {
            $calibration->max_distance_right = $distance;
        }

        // Check if calibration is complete
        if ($calibration->max_distance_left > 0 && $calibration->max_distance_right > 0) {
            $calibration->is_calibrated = true;
        }

        $calibration->save();

        return response()->json([
            'success' => true,
            'message' => "Maximum distance {$request->direction} set to " . number_format($distance, 2),
            'calibration' => $calibration
        ]);
    }

    /**
     * Validate if a position is within limits.
     */
    public function validatePosition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $calibration = ActuatorCalibration::getActive();

        if (!$calibration || !$calibration->is_calibrated) {
            return response()->json([
                'success' => true,
                'is_valid' => false,
                'message' => 'Actuator not calibrated'
            ]);
        }

        $position = $request->position;
        $isValid = $calibration->isPositionValid($position);
        $distance = $calibration->getDistanceFromMidpoint($position);
        $direction = $calibration->getDirectionFromMidpoint($position);
        $absDistance = abs($distance);

        $maxDistance = $direction === 'left'
            ? $calibration->max_distance_left
            : $calibration->max_distance_right;

        return response()->json([
            'success' => true,
            'is_valid' => $isValid,
            'position' => $position,
            'midpoint' => $calibration->midpoint,
            'distance_from_midpoint' => $distance,
            'absolute_distance' => $absDistance,
            'direction' => $direction,
            'max_allowed_distance' => $maxDistance,
            'within_limits' => $isValid,
            'min_position' => $calibration->min_position,
            'max_position' => $calibration->max_position,
        ]);
    }

    /**
     * Reset calibration (deactivate and create new).
     */
    public function reset()
    {
        // Deactivate all calibrations
        ActuatorCalibration::where('is_active', true)->update(['is_active' => false]);

        // Create new default calibration
        $calibration = ActuatorCalibration::create([
            'midpoint' => 0,
            'max_distance_left' => 0,
            'max_distance_right' => 0,
            'is_active' => true,
            'is_calibrated' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Calibration reset successfully',
            'calibration' => $calibration
        ]);
    }

    /**
     * Delete a calibration.
     */
    public function destroy($id)
    {
        $calibration = ActuatorCalibration::find($id);

        if (!$calibration) {
            return response()->json([
                'success' => false,
                'message' => 'Calibration not found'
            ], 404);
        }

        $calibration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Calibration deleted successfully'
        ]);
    }
}