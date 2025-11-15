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

        $compressiveData = CompressiveData::create($validatedData);
        return response()->json($compressiveData, 201);
    }

    /**
     * Update an existing compressive test record
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
            'width' => 'sometimes|numeric',
            'height' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'area' => 'sometimes|numeric',
            'moisture_content' => 'nullable|numeric',
            'max_force_load' => 'sometimes|numeric',
            'photo' => 'nullable|string|max:191'
        ]);

        $compressiveData->update($validatedData);
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
}