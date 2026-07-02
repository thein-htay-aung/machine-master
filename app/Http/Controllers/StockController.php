<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Exports\DailyStocksExport;
use App\Models\CurrentStock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    use ResolvesPlantOptions;

    /**
     * Display current stock balances.
     */
    public function index(Request $request)
    {
        $query = CurrentStock::with(['item.category', 'item.plant', 'item.unit'])
            ->whereHas('item')
            ->join('parts', 'current_stocks.item_id', '=', 'parts.id')
            ->leftJoin('categories', 'parts.category_id', '=', 'categories.id')
            ->select('current_stocks.*')
            ->orderBy('categories.name')
            ->orderBy('parts.name')
            ->orderBy('parts.brand')
            ->orderBy('parts.model');

        $name = $request->query('name');
        $categoryId = $request->query('category_id');
        $plantId = $request->query('plant_id');
        $categories = $this->selectableCategories();
        $selectableCategoryIds = $categories->pluck('id')->all();
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();
        $selectablePlantIds = $plants->pluck('id')->all();

        if ($name !== null && $name !== '') {
            $query->whereHas('item', function ($builder) use ($name) {
                $builder->where('name', 'like', '%' . $name . '%')
                    ->orWhere('model', 'like', '%' . $name . '%');
            });
        }

        if ($categoryId !== null && $categoryId !== '' && in_array((int) $categoryId, $selectableCategoryIds, true)) {
            $query->whereHas('item', fn ($builder) => $builder->where('category_id', $categoryId));
        }

        if ($plantId !== null && $plantId !== '' && in_array((int) $plantId, $selectablePlantIds, true)) {
            $query->whereHas('item', fn ($builder) => $builder->where('plant_id', $plantId));
        } elseif ($defaultPlantId) {
            $query->whereHas('item', fn ($builder) => $builder->where('plant_id', $defaultPlantId));
        }

        $stocks = $query->paginate(10)->withQueryString();

        return view('stocks.index', compact('stocks', 'categories', 'plants', 'defaultPlantId'));
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
            new DailyStocksExport($dateFrom, $dateTo, $this->selectablePlantIds()),
            'daily-stocks.xlsx'
        );
    }
}
