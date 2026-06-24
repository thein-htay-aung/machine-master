<?php

namespace App\Http\Controllers;

use App\Exports\DailyStocksExport;
use App\Models\CurrentStock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    /**
     * Display current stock balances.
     */
    public function index(Request $request)
    {
        $query = CurrentStock::with(['item.category', 'item.plant', 'item.unit'])
            ->whereHas('item')
            ->orderByDesc('qty');

        $name = $request->query('name');

        if ($name !== null && $name !== '') {
            $query->whereHas('item', function ($builder) use ($name) {
                $builder->where('name', 'like', '%' . $name . '%')
                    ->orWhere('model', 'like', '%' . $name . '%');
            });
        }

        $stocks = $query->paginate(10)->withQueryString();

        return view('stocks.index', compact('stocks'));
    }

    /**
     * Download daily stock data as an Excel file.
     */
    public function dailyExport(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $validated['date_from'] ?? now()->toDateString();
        $dateTo = $validated['date_to'] ?? now()->toDateString();

        return Excel::download(
            new DailyStocksExport($dateFrom, $dateTo),
            'daily-stocks.xlsx'
        );
    }
}
