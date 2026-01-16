<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesTestData;
use App\Http\Requests\TestDataRequest;
use App\Models\FlexureData;
use Illuminate\Http\Request;

class FlexureDataController extends Controller
{
    use HandlesTestData;

    public function index(Request $request)
    {
        $data = $this->respondWithTestData(
            $request,
            $this->applyTestDataFilters(FlexureData::query(), $request)
        );

        return response()->json($data);
    }

    public function show(FlexureData $flexureData)
    {
        return response()->json($flexureData);
    }

    public function store(TestDataRequest $request)
    {
        $row = FlexureData::create($request->validated());

        return response()->json($row, 201);
    }

    public function update(TestDataRequest $request, FlexureData $flexureData)
    {
        $flexureData->update($request->validated());

        return response()->json($flexureData->fresh());
    }

    public function destroy(FlexureData $flexureData)
    {
        $flexureData->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function getByTestType(Request $request, string $testType)
    {
        $request->merge(['test_type' => $testType]);

        $data = $this->respondWithTestData(
            $request,
            $this->applyTestDataFilters(FlexureData::query(), $request)
        );

        return response()->json($data);
    }
}
