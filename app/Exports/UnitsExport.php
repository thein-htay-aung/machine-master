<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnitsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Unit::with(['plant', 'createdBy', 'updatedBy'])->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Plant', 'Created By', 'Created At', 'Updated By', 'Updated At'];
    }

    public function map($unit): array
    {
        return [
            $unit->name,
            $unit->plant?->name,
            $unit->createdBy?->name ?? 'System',
            $unit->created_at?->format('Y-m-d H:i:s'),
            $unit->updatedBy?->name ?? 'System',
            $unit->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
