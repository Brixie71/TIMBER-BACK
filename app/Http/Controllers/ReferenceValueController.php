<?php

namespace App\Http\Controllers;

use App\Models\ReferenceValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferenceValueController extends Controller
{
    private const SORTABLE_FIELDS = [
        'common_name',
        'botanical_name',
        'strength_group',
        'compression_parallel',
        'compression_perpendicular',
        'shear_parallel',
        'bending_tension_parallel',
        'created_at',
    ];

    /**
     * Get all reference values with optional filtering.
     */
    public function index(Request $request)
    {
        $query = ReferenceValue::query();

        if ($request->filled('strength_group') && $request->strength_group !== 'all') {
            $query->byStrengthGroup($request->strength_group);
        }

        if ($request->filled('search')) {
            $query->searchByName($request->search);
        }

        [$sortField, $sortOrder] = $this->normalizeSort(
            $request->get('sort_by'),
            $request->get('sort_order')
        );

        $query->orderBy($sortField, $sortOrder);

        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);

        if ($request->boolean('paginate', true)) {
            $data = $query->paginate($perPage);
        } else {
            $limit = min(
                max((int) ($request->get('limit', $request->get('per_page', 500))), 1),
                2000
            );
            $data = $query->limit($limit)->get();
        }

        return response()->json($data);
    }

    /**
     * Get a specific reference value.
     */
    public function show($id)
    {
        return response()->json(ReferenceValue::findOrFail($id));
    }

    /**
     * Create a new reference value.
     */
    public function store(Request $request)
    {
        Log::info('Reference value create request', ['payload' => $request->all()]);

        try {
            $validated = $request->validate($this->validationRules());
            $referenceValue = ReferenceValue::create($validated);

            Log::info('Reference value created', ['id' => $referenceValue->id]);

            return response()->json($referenceValue, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Reference value validation failed', ['errors' => $e->errors()]);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Failed to create reference value', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to create reference value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a reference value.
     */
    public function update(Request $request, $id)
    {
        Log::info('Reference value update request', [
            'id' => $id,
            'payload' => $request->all(),
        ]);

        try {
            $referenceValue = ReferenceValue::findOrFail($id);

            $validated = $request->validate($this->validationRules(isUpdate: true));
            $referenceValue->update($validated);

            Log::info('Reference value updated', ['id' => $referenceValue->id]);

            return response()->json($referenceValue);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Reference value validation failed', ['errors' => $e->errors()]);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Reference value not found',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Failed to update reference value', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to update reference value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a reference value (hard delete).
     */
    public function destroy($id)
    {
        try {
            $referenceValue = ReferenceValue::findOrFail($id);
            $referenceValue->delete();

            Log::info('Reference value deleted', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Reference value deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Reference value not found',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Failed to delete reference value', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to delete reference value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unique strength groups.
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
     * Search species by name.
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

    private function validationRules(bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return [
            'strength_group' => [$required, 'string', 'in:high,moderately_high,medium'],
            'common_name' => [$required, 'string', 'max:255'],
            'botanical_name' => ['nullable', 'string', 'max:255'],
            'compression_parallel' => [$required, 'numeric', 'min:0'],
            'compression_perpendicular' => [$required, 'numeric', 'min:0'],
            'shear_parallel' => [$required, 'numeric', 'min:0'],
            'bending_tension_parallel' => [$required, 'numeric', 'min:0'],
        ];
    }

    private function normalizeSort(?string $field, ?string $order): array
    {
        if ($field && in_array($field, self::SORTABLE_FIELDS, true)) {
            return [$field, strtolower((string) $order) === 'desc' ? 'desc' : 'asc'];
        }

        return ['common_name', 'asc'];
    }
}
