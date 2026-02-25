<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDetectionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MeasurementDetectionSettingController extends Controller
{
    private array $defaultParams = [
        'threshold1' => 52,
        'threshold2' => 104,
        'mask_thresh' => 0,
        'open_k' => 3,
        'close_k' => 5,
        'min_area' => 1000,
        'blur_kernel' => 21,
        'dilation' => 1,
        'erosion' => 1,
        'roi_size' => 60,
        'roi_shape' => 'square',
        'brightness' => 0,
        'contrast' => 101,
        'mm_per_pixel' => 0.1288,
        'edge_thickness' => 2,
        'denoise_enabled' => true,
        'denoise_h' => 6,
        'denoise_template' => 7,
        'denoise_search' => 21,
    ];

    private array $defaultPresets = [
        'flexure' => ['mm_per_pixel' => 0.1568, 'roi_size' => 70],
        'compressive' => ['mm_per_pixel' => 0.1288, 'roi_size' => 65],
        'shear' => ['mm_per_pixel' => 0.1288, 'roi_size' => 65],
    ];

    public function index()
    {
        try {
            $rows = MeasurementDetectionSetting::orderByDesc('id')->get();
            $list = $rows->map(function ($row) {
                $params = $this->sanitizeParams($row->params ?? $row->toArray());
                $presets = $this->sanitizePresets($row->presets);
                return [
                    'id' => $row->id,
                    'profile_name' => $row->profile_name,
                    'is_active' => $row->is_active,
                    'notes' => $row->notes,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'params' => $params,
                    'presets' => $presets,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $list,
            ]);
        } catch (\Throwable $e) {
            Log::error('Measurement settings index() failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to list measurement settings',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $row = MeasurementDetectionSetting::findOrFail($id);
            $params = $this->sanitizeParams($row->params ?? $row->toArray());
            $presets = $this->sanitizePresets($row->presets);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $row->id,
                    'profile_name' => $row->profile_name,
                    'is_active' => $row->is_active,
                    'notes' => $row->notes,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'params' => $params,
                    'presets' => $presets,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Measurement setting not found',
            ], 404);
        }
    }

    private function sanitizeParams(array $params): array
    {
        $p = array_merge($this->defaultParams, $params);

        $odd = function ($v, $min = 1) {
            $v = max($min, intval(round($v)));
            if ($v % 2 === 0) $v += 1;
            return $v;
        };

        $p['threshold1'] = max(0, intval($p['threshold1']));
        $p['threshold2'] = max(0, intval($p['threshold2']));
        $p['mask_thresh'] = max(0, intval($p['mask_thresh']));
        $p['open_k'] = $odd($p['open_k']);
        $p['close_k'] = $odd($p['close_k']);
        $p['min_area'] = max(0, intval($p['min_area']));
        $p['blur_kernel'] = $odd($p['blur_kernel']);
        $p['dilation'] = max(0, intval($p['dilation']));
        $p['erosion'] = max(0, intval($p['erosion']));
        $p['roi_size'] = max(5, min(100, intval($p['roi_size'])));
        $shape = strtolower(strval($p['roi_shape']));
        $p['roi_shape'] = in_array($shape, ['rectangle', 'rect', '0'], true) ? 'rectangle' : 'square';
        $p['brightness'] = max(-100, min(100, intval($p['brightness'])));
        $p['contrast'] = max(0, min(200, intval($p['contrast'])));
        $p['mm_per_pixel'] = max(0.0001, floatval($p['mm_per_pixel']));
        $p['edge_thickness'] = max(1, intval($p['edge_thickness']));
        $p['denoise_enabled'] = boolval($p['denoise_enabled']);
        $p['denoise_h'] = max(1, intval($p['denoise_h']));
        $p['denoise_template'] = $odd($p['denoise_template']);
        $p['denoise_search'] = $odd($p['denoise_search']);

        return $p;
    }

    private function sanitizePresets(?array $presets): array
    {
        $merged = array_merge($this->defaultPresets, $presets ?? []);
        foreach ($merged as $k => $preset) {
            $mm = isset($preset['mm_per_pixel']) ? floatval($preset['mm_per_pixel']) : $this->defaultPresets[$k]['mm_per_pixel'];
            $roi = isset($preset['roi_size']) ? intval($preset['roi_size']) : $this->defaultPresets[$k]['roi_size'];
            $merged[$k] = [
                'mm_per_pixel' => max(0.0001, $mm),
                'roi_size' => max(5, min(100, $roi)),
            ];
        }
        return $merged;
    }

    /**
     * GET /api/measurement-settings/active
     */
    public function active()
    {
        try {
            $row = MeasurementDetectionSetting::where('is_active', true)
                ->orderByDesc('id')
                ->first();

            $params = $this->defaultParams;
            $presets = $this->defaultPresets;

            if ($row) {
                $params = $this->sanitizeParams($row->params ?? $row->toArray());
                $presets = $this->sanitizePresets($row->presets);
            }

            return response()->json([
                'success' => true,
                'source' => $row ? 'database' : 'default',
                'id' => $row?->id,
                'profile_name' => $row?->profile_name,
                'notes' => $row?->notes,
                'params' => $params,
                'presets' => $presets,
                // legacy flat fields (helps older clients)
                ...$params,
            ], 200);

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
            $payload = $request->all();
            $params = $this->sanitizeParams($payload['params'] ?? $payload);
            $presets = $this->sanitizePresets($payload['presets'] ?? null);
            $profileName = $request->input('profile_name');
            $notes = $request->input('notes');
            $setActive = $request->boolean('is_active', true);

            $row = DB::transaction(function () use ($params, $presets, $profileName, $notes, $setActive) {
                if ($setActive) {
                    MeasurementDetectionSetting::where('is_active', true)
                        ->update(['is_active' => false]);
                }

                return MeasurementDetectionSetting::create([
                    'profile_name' => $profileName,
                    'threshold1' => $params['threshold1'],
                    'threshold2' => $params['threshold2'],
                    'mask_thresh' => $params['mask_thresh'],
                    'open_k' => $params['open_k'],
                    'close_k' => $params['close_k'],
                    'min_area' => $params['min_area'],
                    'blur_kernel' => $params['blur_kernel'],
                    'dilation' => $params['dilation'],
                    'erosion' => $params['erosion'],
                    'roi_size' => $params['roi_size'],
                    'roi_shape' => $params['roi_shape'],
                    'brightness' => $params['brightness'],
                    'contrast' => $params['contrast'],
                    'mm_per_pixel' => $params['mm_per_pixel'],
                    'edge_thickness' => $params['edge_thickness'],
                    'denoise_enabled' => $params['denoise_enabled'],
                    'denoise_h' => $params['denoise_h'],
                    'denoise_template' => $params['denoise_template'],
                    'denoise_search' => $params['denoise_search'],
                    'params' => $params,
                    'presets' => $presets,
                    'notes' => $notes,
                    'is_active' => $setActive,
                ]);
            });

            return response()->json([
                'success' => true,
                'id' => $row->id,
                'params' => $params,
                'presets' => $presets,
                'profile_name' => $row->profile_name,
                'is_active' => $row->is_active,
            ], 201);

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

    public function update(Request $request, $id)
    {
        try {
            $row = MeasurementDetectionSetting::findOrFail($id);
            $payload = $request->all();
            $params = $this->sanitizeParams($payload['params'] ?? $payload);
            $presets = $this->sanitizePresets($payload['presets'] ?? null);
            $profileName = $request->input('profile_name', $row->profile_name);
            $notes = $request->input('notes', $row->notes);
            $setActive = $request->boolean('is_active', $row->is_active);

            $row = DB::transaction(function () use ($row, $params, $presets, $profileName, $notes, $setActive) {
                if ($setActive) {
                    MeasurementDetectionSetting::where('is_active', true)
                        ->where('id', '!=', $row->id)
                        ->update(['is_active' => false]);
                }

                $row->fill([
                    'profile_name' => $profileName,
                    'threshold1' => $params['threshold1'],
                    'threshold2' => $params['threshold2'],
                    'mask_thresh' => $params['mask_thresh'],
                    'open_k' => $params['open_k'],
                    'close_k' => $params['close_k'],
                    'min_area' => $params['min_area'],
                    'blur_kernel' => $params['blur_kernel'],
                    'dilation' => $params['dilation'],
                    'erosion' => $params['erosion'],
                    'roi_size' => $params['roi_size'],
                    'roi_shape' => $params['roi_shape'],
                    'brightness' => $params['brightness'],
                    'contrast' => $params['contrast'],
                    'mm_per_pixel' => $params['mm_per_pixel'],
                    'edge_thickness' => $params['edge_thickness'],
                    'denoise_enabled' => $params['denoise_enabled'],
                    'denoise_h' => $params['denoise_h'],
                    'denoise_template' => $params['denoise_template'],
                    'denoise_search' => $params['denoise_search'],
                    'params' => $params,
                    'presets' => $presets,
                    'notes' => $notes,
                    'is_active' => $setActive,
                ]);
                $row->save();
                return $row;
            });

            return response()->json([
                'success' => true,
                'id' => $row->id,
                'params' => $params,
                'presets' => $presets,
                'profile_name' => $row->profile_name,
                'is_active' => $row->is_active,
            ]);
        } catch (\Throwable $e) {
            Log::error('Measurement settings update() failed', [
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function activate($id)
    {
        try {
            $row = MeasurementDetectionSetting::findOrFail($id);
            DB::transaction(function () use ($row) {
                MeasurementDetectionSetting::where('is_active', true)
                    ->update(['is_active' => false]);
                $row->is_active = true;
                $row->save();
            });

            return response()->json(['success' => true, 'id' => $row->id]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to activate measurement setting',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $row = MeasurementDetectionSetting::findOrFail($id);
            $wasActive = $row->is_active;
            $row->delete();

            if ($wasActive) {
                $latest = MeasurementDetectionSetting::orderByDesc('id')->first();
                if ($latest) {
                    $latest->is_active = true;
                    $latest->save();
                }
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete measurement setting',
            ], 500);
        }
    }
}
