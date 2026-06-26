<?php

namespace App\Exports;

use App\Models\Issue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IssuesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        $dateFrom = $this->filters['date_from'] ?? now()->toDateString();
        $dateTo = $this->filters['date_to'] ?? now()->toDateString();

        return Issue::with(['part', 'plant', 'createdBy'])
            ->when($this->filters['issue_no'] ?? null, fn ($query, $value) => $query->where('issue_no', 'like', '%' . $value . '%'))
            ->when($this->filters['category_id'] ?? null, fn ($query, $value) => $query->whereHas('part', fn ($builder) => $builder->where('category_id', $value)))
            ->when($this->filters['part_name'] ?? null, fn ($query, $value) => $query->whereHas('part', fn ($builder) => $builder
                ->where('name', 'like', '%' . $value . '%')
                ->orWhere('model', 'like', '%' . $value . '%')))
            ->when($this->filters['plant_id'] ?? null, fn ($query, $value) => $query->where('plant_id', $value))
            ->when($this->filters['part_id'] ?? null, fn ($query, $value) => $query->where('part_id', $value))
            ->whereDate('issued_date', '>=', $dateFrom)
            ->whereDate('issued_date', '<=', $dateTo)
            ->latest('created_at')
            ->get();
    }

    public function headings(): array
    {
        return ['Issue No', 'Plant Name', 'Part Name', 'Brand', 'Model', 'Qty', 'Price', 'Amount', 'Remark', 'Issued Date', 'Issue By', 'Created By', 'Created At'];
    }

    public function map($issue): array
    {
        return [
            $issue->issue_no,
            $issue->plant?->name,
            $issue->part?->name,
            $issue->part?->brand,
            $issue->part?->model,
            $issue->qty,
            $issue->price,
            $issue->amount,
            $issue->remark,
            $issue->issued_date?->format('Y-m-d'),
            $issue->issue_by,
            $issue->createdBy?->name ?? 'System',
            $issue->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
