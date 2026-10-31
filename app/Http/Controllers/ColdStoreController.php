<?php

namespace App\Http\Controllers;

use App\Models\ColdStoreTemperature;

class ColdStoreController extends Controller
{
    public function index()
    {
        $stores = config('cold_stores.stores');
        $latestTemperatures = ColdStoreTemperature::query()
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('cold_store_temperatures')
                    ->groupBy('cold_store');
            })
            ->get()
            ->keyBy('cold_store');

        return view('cold-stores.index', compact('stores', 'latestTemperatures'));
    }
}
