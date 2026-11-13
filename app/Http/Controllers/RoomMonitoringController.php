<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RoomMonitoringController extends Controller
{
    public function index()
    {
        $locations = config('room_monitoring.locations');
        $statuses = DB::table('room_monitoring_statuses')->get()->keyBy('location');

        return view('room-monitoring.index', compact('locations', 'statuses'));
    }
}
