<?php

namespace App\Exports;

use App\Models\StockAdjustment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockAdjustmentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        $dateFrom = $this->filters['date_from'] ?? now()->toDateString();
        $dateTo = $this->filters['date_to'] ?? now()->toDateString();

        return StockAdjustment::with(['part', 'plant', 'createdBy'])
            ->when($this->filters['adjustment_no'] ?? null, fn ($query, $value) => $query->where('adjustment_no', 'like', '%' . $value . '%'))
            ->when($this->filters['category_id'] ?? null, fn ($query, $value) => $query->whereHas('part', fn ($builder) => $builder->where('category_id', $value)))
            ->when($this->filters['part_name'] ?? null, fn ($query, $value) => $query->whereHas('part', fn ($builder) => $builder
                ->where('name', 'like', '%' . $value . '%')
                ->orWhere('model', 'like', '%' . $value . '%')))
            ->when($this->filters['plant_id'] ?? null, fn ($query, $value) => $query->where('plant_id', $value))
            ->when($this->filters['part_id'] ?? null, fn ($query, $value) => $query->where('part_id', $value))
            ->whereDate('adjusted_date', '>=', $dateFrom)
            ->whereDate('adjusted_date', '<=', $dateTo)
            ->latest('created_at')
            ->get();
    }

    public function headings(): array
    {
        return ['Adjustment No', 'Plant Name', 'Part Name', 'Brand', 'Model', 'Symbol', 'Qty', 'Price', 'Amount', 'Reason', 'Adjusted Date', 'Adjusted By', 'Created By', 'Created At'];
    }

    public function map($adjustment): array
    {
        return [
            $adjustment->adjustment_no,
            $adjustment->plant?->name,
            $adjustment->part?->name,
            $adjustment->part?->brand,
            $adjustment->part?->model,
            $adjustment->symbol,
            $adjustment->qty,
            $adjustment->price,
            $adjustment->amount,
            $adjustment->reason,
            $adjustment->adjusted_date?->format('Y-m-d'),
            $adjustment->adjusted_by,
            $adjustment->createdBy?->name ?? 'System',
            $adjustment->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
