<?php

use App\Http\Controllers\Api\ColdStoreTemperatureController;
use App\Http\Controllers\Api\RoomReadingController;
use Illuminate\Support\Facades\Route;

Route::post('/cold-store-temperatures', [ColdStoreTemperatureController::class, 'store'])
    ->middleware('throttle:60,1');

Route::post('/room-readings', [RoomReadingController::class, 'store'])
    ->middleware('throttle:120,1');
