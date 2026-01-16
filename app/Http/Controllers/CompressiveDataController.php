<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesTestData;
use App\Http\Requests\TestDataRequest;
use App\Models\CompressiveData;
use Illuminate\Http\Request;

class CompressiveDataController extends Controller
{
    use HandlesTestData;

    public function index(Request $request)
    {
        $data = $this->respondWithTestData(
            $request,
            $this->applyTestDataFilters(CompressiveData::query(), $request)
        );

        return response()->json($data);
    }

    public function show(CompressiveData $compressiveData)
    {
        return response()->json($compressiveData);
    }

    public function store(TestDataRequest $request)
    {
        $row = CompressiveData::create($request->validated());

        return response()->json($row, 201);
    }

    public function update(TestDataRequest $request, CompressiveData $compressiveData)
    {
        $compressiveData->update($request->validated());

        return response()->json($compressiveData->fresh());
    }

    public function destroy(CompressiveData $compressiveData)
    {
        $compressiveData->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function getByTestType(Request $request, string $testType)
    {
        $request->merge(['test_type' => $testType]);

        $data = $this->respondWithTestData(
            $request,
            $this->applyTestDataFilters(CompressiveData::query(), $request)
        );

        return response()->json($data);
    }
}
