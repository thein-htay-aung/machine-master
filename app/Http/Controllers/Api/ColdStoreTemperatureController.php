<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ColdStoreTemperature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ColdStoreTemperatureController extends Controller
{
    public function store(Request $request)
    {
        $apiKey = (string) config('cold_stores.api_key');
        $providedKey = (string) $request->header('X-API-Key');

        if ($apiKey === '' || ! hash_equals($apiKey, $providedKey)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $storeNames = array_keys(config('cold_stores.stores'));
        $validated = $request->validate([
            'readings' => ['required', 'array', 'size:'.count($storeNames)],
            'readings.*.cold_store' => ['required', 'string', 'distinct', Rule::in($storeNames)],
            'readings.*.temperature' => ['required', 'numeric'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $receivedStoreNames = collect($validated['readings'])->pluck('cold_store')->sort()->values()->all();
        $expectedStoreNames = collect($storeNames)->sort()->values()->all();

        if ($receivedStoreNames !== $expectedStoreNames) {
            return response()->json(['message' => 'A reading for each configured cold store is required.'], 422);
        }

        $recordedAt = $validated['recorded_at'] ?? now();
        $timestamp = now();
        $rows = collect($validated['readings'])->map(fn (array $reading) => [
            'cold_store' => $reading['cold_store'],
            'temperature' => $reading['temperature'],
            'recorded_at' => $recordedAt,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])->all();

        DB::table('cold_store_temperatures')->insert($rows);

        return response()->json([
            'message' => 'Temperatures saved.',
            'recorded_at' => $recordedAt,
            'count' => count($rows),
        ], 201);
    }
}
