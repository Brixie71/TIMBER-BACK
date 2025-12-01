<?php

namespace App\Http\Controllers;

use App\Models\ReferenceValue;
use Illuminate\Http\Request;

class ReferenceValueController extends Controller
{
    /**
     * Get all reference values with optional filtering
     */
    public function index(Request $request)
    {
        $query = ReferenceValue::query();

        // Filter by strength group
        if ($request->has('strength_group') && $request->strength_group !== 'all') {
            $query->byStrengthGroup($request->strength_group);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->searchByName($request->search);
        }

        // Sort
        $sortField = $request->get('sort_by', 'common_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        // Paginate or get all
        if ($request->has('paginate') && $request->paginate === 'true') {
            $perPage = $request->get('per_page', 15);
            $data = $query->paginate($perPage);
        } else {
            $data = $query->get();
        }

        return response()->json($data);
    }

    /**
     * Get a specific reference value
     */
    public function show($id)
    {
        $referenceValue = ReferenceValue::findOrFail($id);
        return response()->json($referenceValue);
    }

    /**
     * Create a new reference value
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'strength_group' => 'required|string|in:high,moderately_high,medium',
            'common_name' => 'required|string|max:255',
            'botanical_name' => 'nullable|string|max:255',
            'compression_parallel' => 'required|numeric|min:0',
            'compression_perpendicular' => 'required|numeric|min:0',
            'shear_parallel' => 'required|numeric|min:0',
            'bending_tension_parallel' => 'required|numeric|min:0',
        ]);

        $referenceValue = ReferenceValue::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reference value created successfully',
            'data' => $referenceValue
        ], 201);
    }

    /**
     * Update a reference value
     */
    public function update(Request $request, $id)
    {
        $referenceValue = ReferenceValue::findOrFail($id);

        $validated = $request->validate([
            'strength_group' => 'sometimes|string|in:high,moderately_high,medium',
            'common_name' => 'sometimes|string|max:255',
            'botanical_name' => 'nullable|string|max:255',
            'compression_parallel' => 'sometimes|numeric|min:0',
            'compression_perpendicular' => 'sometimes|numeric|min:0',
            'shear_parallel' => 'sometimes|numeric|min:0',
            'bending_tension_parallel' => 'sometimes|numeric|min:0',
        ]);

        $referenceValue->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reference value updated successfully',
            'data' => $referenceValue
        ]);
    }

    /**
     * Delete a reference value
     */
    public function destroy($id)
    {
        $referenceValue = ReferenceValue::findOrFail($id);
        $referenceValue->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reference value deleted successfully'
        ]);
    }

    /**
     * Get unique strength groups
     */
    public function getStrengthGroups()
    {
        $groups = ReferenceValue::select('strength_group')
            ->distinct()
            ->orderBy('strength_group')
            ->pluck('strength_group');

        return response()->json($groups);
    }

    /**
     * Search species by name
     */
    public function searchSpecies(Request $request)
    {
        $search = $request->get('q', '');

        $results = ReferenceValue::searchByName($search)
            ->select('id', 'common_name', 'botanical_name', 'strength_group')
            ->limit(10)
            ->get();

        return response()->json($results);
    }
}