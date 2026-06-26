<?php

namespace App\Exports;

use App\Models\Machine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MachinesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        return Machine::with(['plant', 'status', 'createdBy', 'updatedBy'])
            ->when($this->filters['control_no'] ?? null, fn ($query, $value) => $query->where('control_no', 'like', '%' . $value . '%'))
            ->when($this->filters['name'] ?? null, fn ($query, $value) => $query->where('name', 'like', '%' . $value . '%'))
            ->when($this->filters['status_id'] ?? null, fn ($query, $value) => $query->where('status_id', $value))
            ->when($this->filters['plant_id'] ?? null, fn ($query, $value) => $query->where('plant_id', $value))
            ->orderBy('control_no')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Control No.',
            'Name',
            'Brand',
            'Model',
            'Serial No.',
            'Supplier',
            'Arrived Date',
            'Location',
            'Status',
            'Category',
            'Plant',
            'Dimension',
            'Weight',
            'Electrical',
            'Currency',
            'Unit Price',
            'Fixed Asset',
            'Remark',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }

    public function map($machine): array
    {
        return [
            $machine->control_no,
            $machine->name,
            $machine->brand,
            $machine->model,
            $machine->serial_no,
            $machine->supplier,
            $machine->arrived_date?->format('Y-m-d'),
            $machine->location,
            $machine->status?->name,
            $machine->category,
            $machine->plant?->name,
            $machine->dimension,
            $machine->weight,
            $machine->electrical,
            $machine->currency,
            $machine->unit_price,
            $machine->is_fixed_asset ? 'Yes' : 'No',
            $machine->remark,
            $machine->createdBy?->name ?? 'System',
            $machine->updatedBy?->name ?? 'System',
            $machine->created_at?->format('Y-m-d H:i:s'),
            $machine->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
