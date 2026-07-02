<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Exports\UnitsExport;
use App\Models\Part;
use App\Models\Unit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UnitController extends Controller
{
    use ResolvesPlantOptions;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();
        $plantId = $request->query('plant_id');
        $selectablePlantIds = $plants->pluck('id')->all();

        $query = Unit::with(['plant', 'createdBy', 'updatedBy'])->orderBy('name');

        if ($plantId !== null && $plantId !== '' && in_array((int) $plantId, $selectablePlantIds, true)) {
            $query->where('plant_id', $plantId);
        } elseif ($defaultPlantId) {
            $query->where('plant_id', $defaultPlantId);
        }

        $units = $query->paginate(10)->withQueryString();

        return view('units.index', compact('units', 'plants', 'defaultPlantId'));
    }

    public function export()
    {
        return Excel::download(new UnitsExport(), 'units.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();

        return view('units.create', compact('plants', 'defaultPlantId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'plant_id' => ['required', $this->plantValidationRule()],
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId($unit->plant_id);

        return view('units.edit', compact('unit', 'plants', 'defaultPlantId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'plant_id' => ['required', $this->plantValidationRule()],
        ]);

        $validated['updated_by'] = $request->user()->id;

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        if (Part::where('unit_id', $unit->id)->exists()) {
            return redirect()->route('units.index')->with('error', 'This unit is being used by parts and cannot be deleted.');
        }

        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }

}
