<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Validation\Rule;

trait ResolvesPlantOptions
{
    private const USER_SELECTABLE_PLANTS = ['WTY', 'SLB'];

    protected function selectablePlants()
    {
        $userPlantName = auth()->user()?->plant?->name;

        if ($userPlantName === null || $userPlantName === 'All') {
            return Plant::whereIn('name', self::USER_SELECTABLE_PLANTS)->orderBy('name')->get();
        }

        return Plant::where('name', $userPlantName)->orderBy('name')->get();
    }

    protected function plantValidationRule()
    {
        $plantIds = $this->selectablePlantIds();

        return Rule::exists('plants', 'id')->where(fn ($query) => $query->whereIn('id', $plantIds));
    }

    protected function selectablePlantIds(): array
    {
        return $this->selectablePlants()->pluck('id')->all();
    }

    protected function selectableCategories()
    {
        return Category::orderBy('name')->get();
    }

    protected function defaultPlantId(?int $currentPlantId = null): ?int
    {
        $plants = $this->selectablePlants();

        if ($plants->count() === 1) {
            return $plants->first()->id;
        }

        return $currentPlantId;
    }
}
