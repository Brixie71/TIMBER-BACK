<?php

namespace App\Http\Controllers;

use App\Models\CompressiveData;
use Illuminate\Http\Request;

class CompressiveDataController extends Controller
{
    /**
     * Get all compressive test data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $compressiveData = CompressiveData::all();
        return response()->json($compressiveData);
    }

    /**
     * Get a specific compressive test record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $compressiveData = CompressiveData::findOrFail($id);
        return response()->json($compressiveData);
    }

    /**
     * Create a new compressive test record
     * Note: pressure and stress are auto-calculated by the model
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'test_type' => 'required|string|max:191',
            'specimen_name' => 'required|string|max:191',
            'base' => 'required|numeric|min:0',              // Changed from 'width'
            'height' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'moisture_content' => 'nullable|numeric|min:0|max:100',
            'max_force' => 'required|numeric|min:0',
            'species_id' => 'nullable|integer|exists:reference_values,id',  // NEW
            'photo' => 'nullable|string|max:191'
            // pressure and stress are auto-calculated, not validated
        ]);

        $compressiveData = CompressiveData::create($validatedData);

        // Return with calculated values
        return response()->json($compressiveData, 201);
    }

    /**
     * Update an existing compressive test record
     * Note: pressure and stress are auto-calculated by the model
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $compressiveData = CompressiveData::findOrFail($id);

        $validatedData = $request->validate([
            'test_type' => 'sometimes|string|max:191',
            'specimen_name' => 'sometimes|string|max:191',
            'base' => 'sometimes|numeric|min:0',              // Changed from 'width'
            'height' => 'sometimes|numeric|min:0',
            'length' => 'sometimes|numeric|min:0',
            'area' => 'sometimes|numeric|min:0',
            'moisture_content' => 'nullable|numeric|min:0|max:100',
            'max_force' => 'sometimes|numeric|min:0',
            'species_id' => 'nullable|integer|exists:reference_values,id',  // NEW
            'photo' => 'nullable|string|max:191'
            // pressure and stress are auto-calculated, not validated
        ]);

        $compressiveData->update($validatedData);

        // Return with recalculated values
        return response()->json($compressiveData);
    }

    /**
     * Delete a compressive test record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $compressiveData = CompressiveData::findOrFail($id);
        $compressiveData->delete();
        return response()->json(null, 204);
    }

    /**
     * Get compressive test data filtered by test type
     *
     * @param string $testType
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByTestType($testType)
    {
        $compressiveData = CompressiveData::where('test_type', $testType)->get();
        return response()->json($compressiveData);
    }

    /**
     * Get compressive test data filtered by species
     *
     * @param int $speciesId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySpecies($speciesId)
    {
        $compressiveData = CompressiveData::where('species_id', $speciesId)->get();
        return response()->json($compressiveData);
    }

    /**
     * Get compressive test data with species information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexWithSpecies()
    {
        $compressiveData = CompressiveData::with('species')->get();
        return response()->json($compressiveData);
    }

    /**
     * Recalculate stress and pressure for a specific record
     * Useful for manual recalculation after data updates
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function recalculate($id)
    {
        $compressiveData = CompressiveData::findOrFail($id);

        // Simply saving will trigger auto-calculation
        $compressiveData->save();

        return response()->json([
            'message' => 'Calculations updated successfully',
            'data' => $compressiveData
        ]);
    }
}
