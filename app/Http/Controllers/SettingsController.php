<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $settings = Settings::all();
        return response()->json($settings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings',
            'value' => 'required|json'
        ]);

        $settings = Settings::create($validated);
        return response()->json($settings, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $key): JsonResponse
    {
        $settings = Settings::where('key', $key)->firstOrFail();
        return response()->json($settings);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $key)
    {
        try {
            return $request->all();
            $settings = Settings::firstOrNew(['key' => $key]);

            $settings->value = $request->value;
            $settings->save();

            return response()->json([
                'message' => 'Settings updated successfully',
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $key): JsonResponse
    {
        $settings = Settings::where('key', $key)->firstOrFail();
        $settings->delete();
        return response()->json(null, 204);
    }
}
