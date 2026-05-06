<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'setting_key');

        return response()->json($settings);
    }

    public function show(string $key): JsonResponse
    {
        $setting = Setting::where('setting_key', $key)->firstOrFail();

        return response()->json($setting);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'setting_key' => 'required|string|max:255|unique:settings,setting_key',
            'value' => 'required',
            'description' => 'nullable|string',
            'type' => 'required|in:string,integer,boolean,json',
        ]);

        $setting = Setting::create($validated);

        return response()->json([
            'success' => true,
            'id' => $setting->id,
            'message' => 'Setting created successfully',
        ], 201);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $setting = Setting::where('setting_key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'required',
            'description' => 'sometimes|nullable|string',
            'type' => 'sometimes|in:string,integer,boolean,json',
        ]);

        $setting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
        ]);
    }

    public function destroy(string $key): JsonResponse
    {
        $setting = Setting::where('setting_key', $key)->firstOrFail();
        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully',
        ]);
    }
}
