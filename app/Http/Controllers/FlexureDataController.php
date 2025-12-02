<?php

namespace App\Http\Controllers;

use App\Models\FlexureData;
use Illuminate\Http\Request;

class FlexureDataController extends Controller
{
    /**
     * Get all flexure test data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $flexureData = FlexureData::all();
        return response()->json($flexureData);
    }

    /**
     * Get a specific flexure test record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $flexureData = FlexureData::findOrFail($id);
        return response()->json($flexureData);
    }

    /**
     * Create a new flexure test record
     * Note: pressure and stress are auto-calculated by the model
     * Flexural stress uses the formula f = Mc/I
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

        $flexureData = FlexureData::create($validatedData);

        // Return with calculated values
        return response()->json($flexureData, 201);
    }

    /**
     * Update an existing flexure test record
     * Note: pressure and stress are auto-calculated by the model
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $flexureData = FlexureData::findOrFail($id);

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

        $flexureData->update($validatedData);

        // Return with recalculated values
        return response()->json($flexureData);
    }

    /**
     * Delete a flexure test record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $flexureData = FlexureData::findOrFail($id);
        $flexureData->delete();
        return response()->json(null, 204);
    }

    /**
     * Get flexure test data filtered by test type
     *
     * @param string $testType
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByTestType($testType)
    {
        $flexureData = FlexureData::where('test_type', $testType)->get();
        return response()->json($flexureData);
    }

    /**
     * Get flexure test data filtered by species
     *
     * @param int $speciesId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySpecies($speciesId)
    {
        $flexureData = FlexureData::where('species_id', $speciesId)->get();
        return response()->json($flexureData);
    }

    /**
     * Get flexure test data with species information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexWithSpecies()
    {
        $flexureData = FlexureData::with('species')->get();
        return response()->json($flexureData);
    }

    /**
     * Get detailed calculation breakdown for a specific test
     * Useful for verification and educational purposes
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalculationDetails($id)
    {
        $flexureData = FlexureData::findOrFail($id);

        $forceN = $flexureData->max_force * 1000;
        $M = $flexureData->getBendingMoment();
        $c = $flexureData->height / 2;
        $I = $flexureData->getMomentOfInertia();

        return response()->json([
            'test_data' => $flexureData,
            'calculations' => [
                'force_N' => $forceN,
                'bending_moment_Nmm' => $M,
                'distance_to_neutral_axis_mm' => $c,
                'moment_of_inertia_mm4' => $I,
                'flexural_stress_MPa' => $flexureData->stress,
                'pressure_Nmm2' => $flexureData->pressure,
                'formula' => 'f = (M × c) / I where M = (F × L) / 4',
                'units' => [
                    'Force (F)' => 'N',
                    'Length (L)' => 'mm',
                    'Base (b)' => 'mm',
                    'Height (h)' => 'mm',
                    'Stress (f)' => 'MPa (N/mm²)'
                ]
            ]
        ]);
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
        $flexureData = FlexureData::findOrFail($id);

        // Simply saving will trigger auto-calculation
        $flexureData->save();

        return response()->json([
            'message' => 'Calculations updated successfully',
            'data' => $flexureData,
            'bending_moment' => $flexureData->getBendingMoment(),
            'moment_of_inertia' => $flexureData->getMomentOfInertia()
        ]);
    }
}   