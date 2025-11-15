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

        $flexureData = FlexureData::create($validatedData);
        return response()->json($flexureData, 201);
    }

    /**
     * Update an existing flexure test record
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
            'width' => 'sometimes|numeric',
            'height' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'area' => 'sometimes|numeric',
            'moisture_content' => 'nullable|numeric',
            'max_force_load' => 'sometimes|numeric',
            'photo' => 'nullable|string|max:191'
        ]);

        $flexureData->update($validatedData);
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
}