<?php

namespace App\Exports;

use App\Models\DailyStock;
use App\Models\Part;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyStocksExport implements FromArray, WithHeadings
{
    public function __construct(
        private readonly string $dateFrom,
        private readonly string $dateTo
    ) {
    }

    public function array(): array
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->startOfDay();

        $parts = Part::with(['category', 'unit'])
            ->orderBy('name')
            ->get();

        $dailyStocks = DailyStock::whereDate('date', '>=', $dateFrom->toDateString())
            ->whereDate('date', '<=', $dateTo->toDateString())
            ->get()
            ->keyBy(fn ($stock) => $stock->date->format('Y-m-d') . '-' . $stock->item_id);

        $openingBalances = DailyStock::whereDate('date', '<', $dateFrom->toDateString())
            ->orderBy('date')
            ->get()
            ->groupBy('item_id')
            ->map(fn ($stocks) => $stocks->last()->stock_qty);

        $rows = [];
        $balances = $openingBalances->all();

        foreach (CarbonPeriod::create($dateFrom, $dateTo) as $date) {
            $stockDate = $date->format('Y-m-d');

            foreach ($parts as $part) {
                $dailyStock = $dailyStocks->get($stockDate . '-' . $part->id);
                $openingBalance = $balances[$part->id] ?? 0;
                $stockQty = $dailyStock?->stock_qty ?? $openingBalance;

                $rows[] = [
                    $stockDate,
                    $part->name,
                    $part->brand,
                    $part->model,
                    $part->category?->name,
                    $part->unit?->name,
                    $openingBalance,
                    $dailyStock?->in_qty ?? 0,
                    $dailyStock?->out_qty ?? 0,
                    $stockQty,
                ];

                $balances[$part->id] = $stockQty;
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Part Name',
            'Brand',
            'Model',
            'Category',
            'Unit',
            'Opening Balance',
            'In Qty',
            'Out Qty',
            'Stock Qty',
        ];
    }

}
