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
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'test_type' => 'required|string|max:191',
            'specimen_name' => 'required|string|max:191',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'length' => 'required|numeric',
            'area' => 'required|numeric',
            'moisture_content' => 'nullable|numeric',
            'max_force_load' => 'required|numeric',
            'photo' => 'nullable|string|max:191'
        ]);

        $shearData = ShearData::create($validatedData);
        return response()->json($shearData, 201);
    }

    /**
     * Update an existing shear test record
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
            'width' => 'sometimes|numeric',
            'height' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'area' => 'sometimes|numeric',
            'moisture_content' => 'nullable|numeric',
            'max_force_load' => 'sometimes|numeric',
            'photo' => 'nullable|string|max:191'
        ]);

        $shearData->update($validatedData);
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
}