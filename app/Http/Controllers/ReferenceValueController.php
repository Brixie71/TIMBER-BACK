<?php

namespace App\Http\Controllers;

use App\Models\ReferenceValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferenceValueController extends Controller
{
    /**
     * Get all reference values with optional filtering
     */
    public function index(Request $request)
    {
        $query = ReferenceValue::query();

        // Filter by strength group
        if (
            $request->has("strength_group") &&
            $request->strength_group !== "all"
        ) {
            $query->byStrengthGroup($request->strength_group);
        }

        // Search by name
        if ($request->has("search") && $request->search) {
            $query->searchByName($request->search);
        }

        // Sort
        $sortField = $request->get("sort_by", "common_name");
        $sortOrder = $request->get("sort_order", "asc");
        $query->orderBy($sortField, $sortOrder);

        // Paginate or get all
        if ($request->has("paginate") && $request->paginate === "true") {
            $perPage = $request->get("per_page", 15);
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
        Log::info("📥 Creating new reference value", [
            "data" => $request->all(),
        ]);

        try {
            $validated = $request->validate([
                "strength_group" =>
                    "required|string|in:high,moderately_high,medium",
                "common_name" => "required|string|max:255",
                "botanical_name" => "nullable|string|max:255",
                "compression_parallel" => "required|numeric|min:0",
                "compression_perpendicular" => "required|numeric|min:0",
                "shear_parallel" => "required|numeric|min:0",
                "bending_tension_parallel" => "required|numeric|min:0",
            ]);

            Log::info("✅ Validation passed", ["validated" => $validated]);

            $referenceValue = ReferenceValue::create($validated);

            Log::info("✅ Reference value created", [
                "id" => $referenceValue->id,
                "common_name" => $referenceValue->common_name,
            ]);

            // ✅ FIX: Return the object directly, not wrapped in { data: ... }
            return response()->json($referenceValue, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("❌ Validation failed", ["errors" => $e->errors()]);
            return response()->json(
                [
                    "message" => "Validation failed",
                    "errors" => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            Log::error("❌ Error creating reference value", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return response()->json(
                [
                    "message" => "Failed to create reference value",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Update a reference value
     */
    public function update(Request $request, $id)
    {
        Log::info("📝 Updating reference value {$id}", [
            "data" => $request->all(),
        ]);

        try {
            $referenceValue = ReferenceValue::findOrFail($id);

            $validated = $request->validate([
                "strength_group" =>
                    "sometimes|string|in:high,moderately_high,medium",
                "common_name" => "sometimes|string|max:255",
                "botanical_name" => "nullable|string|max:255",
                "compression_parallel" => "sometimes|numeric|min:0",
                "compression_perpendicular" => "sometimes|numeric|min:0",
                "shear_parallel" => "sometimes|numeric|min:0",
                "bending_tension_parallel" => "sometimes|numeric|min:0",
            ]);

            Log::info("✅ Validation passed", ["validated" => $validated]);

            $referenceValue->update($validated);

            Log::info("✅ Reference value updated", [
                "id" => $referenceValue->id,
                "common_name" => $referenceValue->common_name,
            ]);

            // ✅ FIX: Return the object directly, not wrapped in { data: ... }
            return response()->json($referenceValue);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("❌ Validation failed", ["errors" => $e->errors()]);
            return response()->json(
                [
                    "message" => "Validation failed",
                    "errors" => $e->errors(),
                ],
                422,
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("❌ Reference value {$id} not found");
            return response()->json(
                [
                    "message" => "Reference value not found",
                ],
                404,
            );
        } catch (\Exception $e) {
            Log::error("❌ Error updating reference value", [
                "id" => $id,
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return response()->json(
                [
                    "message" => "Failed to update reference value",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Delete a reference value (permanently)
     */
    public function destroy($id)
    {
        Log::info("🗑️ Deleting reference value {$id}");

        try {
            $referenceValue = ReferenceValue::findOrFail($id);

            // ✅ Use forceDelete() to permanently remove from database
            // instead of delete() which only soft-deletes
            $referenceValue->forceDelete();

            Log::info("✅ Reference value permanently deleted", ["id" => $id]);

            return response()->json([
                "success" => true,
                "message" => "Reference value deleted successfully",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("❌ Reference value {$id} not found");
            return response()->json(
                [
                    "message" => "Reference value not found",
                ],
                404,
            );
        } catch (\Exception $e) {
            Log::error("❌ Error deleting reference value", [
                "id" => $id,
                "message" => $e->getMessage(),
            ]);
            return response()->json(
                [
                    "message" => "Failed to delete reference value",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get unique strength groups
     */
    public function getStrengthGroups()
    {
        $groups = ReferenceValue::select("strength_group")
            ->distinct()
            ->orderBy("strength_group")
            ->pluck("strength_group");

        return response()->json($groups);
    }

    /**
     * Search species by name
     */
    public function searchSpecies(Request $request)
    {
        $search = $request->get("q", "");

        $results = ReferenceValue::searchByName($search)
            ->select("id", "common_name", "botanical_name", "strength_group") // ✅ FIX: Use 'id' not 'reference_value_id'
            ->limit(10)
            ->get();

        return response()->json($results);
    }
}
