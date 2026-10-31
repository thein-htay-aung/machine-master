<?php

use App\Http\Controllers\Api\ColdStoreTemperatureController;
use Illuminate\Support\Facades\Route;

Route::post('/cold-store-temperatures', [ColdStoreTemperatureController::class, 'store'])
    ->middleware('throttle:60,1');
