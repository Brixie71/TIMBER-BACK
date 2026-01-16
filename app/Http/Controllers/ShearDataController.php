<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesTestData;
use App\Http\Requests\TestDataRequest;
use App\Models\ShearData;
use Illuminate\Http\Request;

class ShearDataController extends Controller
{
    use HandlesTestData;

    public function index(Request $request)
    {
        $data = $this->respondWithTestData(
            $request,
            $this->applyTestDataFilters(ShearData::query(), $request)
        );

        return response()->json($data);
    }

    public function show(ShearData $shearData)
    {
        return response()->json($shearData);
    }

    public function store(TestDataRequest $request)
    {
        $row = ShearData::create($request->validated());

        return response()->json($row, 201);
    }

    public function update(TestDataRequest $request, ShearData $shearData)
    {
        $shearData->update($request->validated());

        return response()->json($shearData->fresh());
    }

    public function destroy(ShearData $shearData)
    {
        $shearData->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function getByTestType(Request $request, string $testType)
    {
        $request->merge(['test_type' => $testType]);

        $data = $this->respondWithTestData(
            $request,
            $this->applyTestDataFilters(ShearData::query(), $request)
        );

        return response()->json($data);
    }
}
