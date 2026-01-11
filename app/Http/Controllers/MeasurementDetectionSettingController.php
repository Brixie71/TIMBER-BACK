<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDetectionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MeasurementDetectionSettingController extends Controller
{
    /**
     * GET /api/measurement-settings/active
     */
    public function active()
    {
        try {
            $row = MeasurementDetectionSetting::where('is_active', true)
                ->orderByDesc('id')
                ->first();

            if (!$row) {
                return response()->json([
                    'threshold1' => 52,
                    'threshold2' => 104,
                    'min_area' => 1000,
                    'blur_kernel' => 21,
                    'dilation' => 1,
                    'erosion' => 1,
                    'roi_size' => 60,
                    'brightness' => 0,
                    'contrast' => 101,
                    'mm_per_pixel' => 0.1,
                    'is_active' => true,
                ], 200);
            }

            return response()->json($row, 200);

        } catch (\Throwable $e) {
            Log::error('Measurement settings active() failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load measurement settings',
            ], 500);
        }
    }

    /**
     * POST /api/measurement-settings
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'threshold1' => 'required|integer|min:0|max:255',
                'threshold2' => 'required|integer|min:0|max:255',
                'min_area' => 'required|integer|min:0',
                'blur_kernel' => 'required|integer|min:1',
                'dilation' => 'required|integer|min:0',
                'erosion' => 'required|integer|min:0',
                'roi_size' => 'required|integer|min:10|max:100',
                'brightness' => 'required|integer|min:-100|max:100',
                'contrast' => 'required|integer|min:0|max:200',
                'mm_per_pixel' => 'required|numeric|min:0',
            ]);

            // Force odd blur kernel
            if ($data['blur_kernel'] % 2 === 0) {
                $data['blur_kernel']++;
            }

            DB::transaction(function () use ($data) {
                MeasurementDetectionSetting::where('is_active', true)
                    ->update(['is_active' => false]);

                MeasurementDetectionSetting::create([
                    ...$data,
                    'is_active' => true,
                ]);
            });

            return response()->json(['success' => true], 201);

        } catch (\Throwable $e) {
            Log::error('Measurement settings store() failed', [
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
