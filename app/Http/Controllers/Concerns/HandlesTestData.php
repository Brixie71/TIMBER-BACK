<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesTestData
{
    /**
     * Apply common filters for test data resources.
     */
    protected function applyTestDataFilters(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('test_type'), fn (Builder $q) => $q->where('test_type', $request->input('test_type')))
            ->when($request->filled('species_id'), fn (Builder $q) => $q->where('species_id', $request->input('species_id')))
            ->when($request->filled('search'), fn (Builder $q) => $q->where('specimen_name', 'like', '%' . $request->input('search') . '%'));
    }

    /**
     * Return either paginated or limited collections for test data endpoints.
     */
    protected function respondWithTestData(Request $request, Builder $query)
    {
        $query->orderByDesc('created_at');

        $perPage = min(max((int) $request->get('per_page', 25), 1), 100);

        if ($request->boolean('paginate', true)) {
            return $query->paginate($perPage);
        }

        $limit = min(
            max((int) ($request->get('limit', $request->get('per_page', 500))), 1),
            2000
        );

        return $query->limit($limit)->get();
    }
}
