<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompressiveData; // Or your model name

class TestController extends Controller
{
    public function storeCompressive(Request $request)
    {
        $validated = $request->validate([
            'test_type' => 'required|string',
            'sub_type' => 'nullable|string',
            'specimen_name' => 'required|string|max:255',
            'base' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'moisture_content' => 'nullable|numeric',
            'maximum_force' => 'required|numeric',
            'pressure' => 'nullable|numeric',
            'stress' => 'nullable|numeric',
            'test_duration' => 'nullable|numeric',
        ]);

        $test = CompressiveData::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Test data saved successfully',
            'data' => $test
        ], 201)
        ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}