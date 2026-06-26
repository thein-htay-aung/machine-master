<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoriesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Category::with(['plant', 'createdBy', 'updatedBy'])->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Plant', 'Created By', 'Created At', 'Updated By', 'Updated At'];
    }

    public function map($category): array
    {
        return [
            $category->name,
            $category->plant?->name,
            $category->createdBy?->name ?? 'System',
            $category->created_at?->format('Y-m-d H:i:s'),
            $category->updatedBy?->name ?? 'System',
            $category->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
