<?php

namespace App\Exports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PartsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        return Part::with(['category', 'plant', 'unit'])
            ->when($this->filters['name'] ?? null, fn ($query, $value) => $query->where('name', 'like', '%' . $value . '%'))
            ->when($this->filters['category_id'] ?? null, fn ($query, $value) => $query->where('category_id', $value))
            ->when($this->filters['plant_id'] ?? null, fn ($query, $value) => $query->where('plant_id', $value))
            ->when(($this->filters['is_active'] ?? '') !== '', fn ($query) => $query->where('is_active', (int) $this->filters['is_active']))
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return ['Name', 'Brand', 'Model', 'Plant', 'Category', 'Unit', 'Location', 'Min Qty', 'Active', 'Created At'];
    }

    public function map($part): array
    {
        return [
            $part->name,
            $part->brand,
            $part->model,
            $part->plant?->name,
            $part->category?->name,
            $part->unit?->name,
            $part->location,
            $part->min_qty,
            $part->is_active ? 'Yes' : 'No',
            $part->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
