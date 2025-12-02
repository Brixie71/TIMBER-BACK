<?php

namespace App\Http\Controllers;

use App\Models\ShearData;
use Illuminate\Http\Request;

class ShearDataController extends Controller
{
    /**
     * Get all shear test data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $shearData = ShearData::all();
        return response()->json($shearData);
    }

    /**
     * Get a specific shear test record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $shearData = ShearData::findOrFail($id);
        return response()->json($shearData);
    }

    /**
     * Create a new shear test record
     * Note: pressure and stress are auto-calculated by the model
     * Stress calculation automatically handles single vs double shear
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

        $shearData = ShearData::create($validatedData);

        // Return with calculated values
        return response()->json($shearData, 201);
    }

    /**
     * Update an existing shear test record
     * Note: pressure and stress are auto-calculated by the model
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $shearData = ShearData::findOrFail($id);

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

        $shearData->update($validatedData);

        // Return with recalculated values
        return response()->json($shearData);
    }

    /**
     * Delete a shear test record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $shearData = ShearData::findOrFail($id);
        $shearData->delete();
        return response()->json(null, 204);
    }

    /**
     * Get shear test data filtered by test type
     *
     * @param string $testType
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByTestType($testType)
    {
        $shearData = ShearData::where('test_type', $testType)->get();
        return response()->json($shearData);
    }

    /**
     * Get single shear tests only
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleShear()
    {
        $shearData = ShearData::where('test_type', 'like', '%Single%')->get();
        return response()->json($shearData);
    }

    /**
     * Get double shear tests only
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDoubleShear()
    {
        $shearData = ShearData::where('test_type', 'like', '%Double%')->get();
        return response()->json($shearData);
    }

    /**
     * Get shear test data filtered by species
     *
     * @param int $speciesId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySpecies($speciesId)
    {
        $shearData = ShearData::where('species_id', $speciesId)->get();
        return response()->json($shearData);
    }

    /**
     * Get shear test data with species information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexWithSpecies()
    {
        $shearData = ShearData::with('species')->get();
        return response()->json($shearData);
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
        $shearData = ShearData::findOrFail($id);

        // Simply saving will trigger auto-calculation
        $shearData->save();

        return response()->json([
            'message' => 'Calculations updated successfully',
            'data' => $shearData,
            'is_double_shear' => $shearData->isDoubleShear()
        ]);
    }
}
