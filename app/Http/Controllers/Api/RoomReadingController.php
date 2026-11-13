<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoomReadingController extends Controller
{
    public function store(Request $request)
    {
        $apiKey = (string) config('room_monitoring.api_key');
        $providedKey = (string) $request->header('X-API-Key');

        if ($apiKey === '' || ! hash_equals($apiKey, $providedKey)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', Rule::in(array_keys(config('room_monitoring.locations')))],
            'temperature' => ['required', 'numeric'],
            'humidity' => ['required', 'numeric', 'between:0,100'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $recordedAt = isset($validated['recorded_at']) ? Carbon::parse($validated['recorded_at']) : now();
        $now = now();
        $interval = (int) config('room_monitoring.history_interval_minutes', 15);

        $historySaved = DB::transaction(function () use ($validated, $recordedAt, $now, $interval) {
            $status = DB::table('room_monitoring_statuses')
                ->where('location', $validated['location'])
                ->lockForUpdate()
                ->first();

            $saveHistory = ! $status
                || ! $status->last_logged_at
                || Carbon::parse($status->last_logged_at)->addMinutes($interval)->lte($recordedAt);

            DB::table('room_monitoring_statuses')->updateOrInsert(
                ['location' => $validated['location']],
                [
                    'device_id' => $validated['device_id'],
                    'temperature' => $validated['temperature'],
                    'humidity' => $validated['humidity'],
                    'recorded_at' => $recordedAt,
                    'last_logged_at' => $saveHistory ? $recordedAt : $status?->last_logged_at,
                    'created_at' => $status?->created_at ?? $now,
                    'updated_at' => $now,
                ]
            );

            if ($saveHistory) {
                DB::table('room_monitoring_logs')->insert([
                    'device_id' => $validated['device_id'],
                    'location' => $validated['location'],
                    'temperature' => $validated['temperature'],
                    'humidity' => $validated['humidity'],
                    'recorded_at' => $recordedAt,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return $saveHistory;
        });

        return response()->json([
            'message' => 'Room reading saved.',
            'history_saved' => $historySaved,
            'recorded_at' => $recordedAt->toIso8601String(),
        ], 201);
    }
}
