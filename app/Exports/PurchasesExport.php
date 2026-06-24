<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        $dateFrom = $this->filters['date_from'] ?? now()->toDateString();
        $dateTo = $this->filters['date_to'] ?? now()->toDateString();

        return Purchase::with(['part', 'createdBy'])
            ->when($this->filters['invoice'] ?? null, fn ($query, $value) => $query->where('invoice', 'like', '%' . $value . '%'))
            ->when($this->filters['part_id'] ?? null, fn ($query, $value) => $query->where('part_id', $value))
            ->whereDate('purchased_date', '>=', $dateFrom)
            ->whereDate('purchased_date', '<=', $dateTo)
            ->latest('created_at')
            ->get();
    }

    public function headings(): array
    {
        return ['Invoice', 'Part Name', 'Brand', 'Model', 'Price', 'Qty', 'Amount', 'Remark', 'Purchased Date', 'Purchase By', 'Created By', 'Created At'];
    }

    public function map($purchase): array
    {
        return [
            $purchase->invoice,
            $purchase->part?->name,
            $purchase->part?->brand,
            $purchase->part?->model,
            $purchase->price,
            $purchase->qty,
            $purchase->amount,
            $purchase->remark,
            $purchase->purchased_date?->format('Y-m-d'),
            $purchase->purchase_by,
            $purchase->createdBy?->name ?? 'System',
            $purchase->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
