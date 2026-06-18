<?php

namespace App\Http\Controllers;

use App\Imports\MachineImport;
use App\Models\Machine;
use App\Models\Plant;
use App\Models\Status;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MachineController extends Controller
{
    private const IMPORT_CURRENCIES = ['MMK', 'USD', 'SGD', 'JPY', 'CNY'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $control_no = $request->input('control_no');
        $name = $request->input('name');
        $status_id = $request->input('status_id');
        $plant_id = $request->input('plant_id');

        $query = Machine::with(['plant', 'status']);

        if ($control_no) {
            $query->where('control_no', 'like', '%' . $control_no . '%');
        }

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($status_id) {
            $query->where('status_id', $status_id);
        }

        if ($plant_id) {
            $query->where('plant_id', $plant_id);
        }

        $machines = $query->orderBy('control_no')->paginate(10)->withQueryString();

        $plants = Plant::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();

        return view('machines.index', compact('machines', 'plants', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plants = Plant::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();

        return view('machines.create', compact('plants', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'control_no' => 'required|string|max:255|unique:machines,control_no',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'arrived_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'dimension' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'electrical' => 'nullable|string|max:255',
            'currency' => 'nullable|string|in:MMK,USD,SGD,JPY,CNY',
            'unit_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'is_fixed_asset' => 'sometimes|boolean',
            'remark' => 'nullable|string',
            'plant_id' => 'required|exists:plants,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('machines', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_fixed_asset'] = $request->boolean('is_fixed_asset');
        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        Machine::create($validated);

        return redirect()->route('machines.index')->with('info', 'Machine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Machine $machine)
    {
        $machine->load(['plant', 'status', 'parts.unit', 'parts.category']);

        return view('machines.show', compact('machine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Machine $machine)
    {
        $plants = Plant::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();

        return view('machines.edit', compact('machine', 'plants', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'control_no' => 'required|string|max:255|unique:machines,control_no,' . $machine->id,
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'arrived_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'dimension' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'electrical' => 'nullable|string|max:255',
            'currency' => 'nullable|string|in:MMK,USD,SGD,JPY,CNY',
            'unit_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'is_fixed_asset' => 'sometimes|boolean',
            'remark' => 'nullable|string',
            'plant_id' => 'required|exists:plants,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        if ($request->hasFile('image')) {
            if ($machine->image && Storage::disk('public')->exists($machine->image)) {
                Storage::disk('public')->delete($machine->image);
            }
            $validated['image'] = $request->file('image')->store('machines', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_fixed_asset'] = $request->boolean('is_fixed_asset');
        $validated['updated_by'] = $request->user()->id;

        $machine->update($validated);

        return redirect()->route('machines.index', $request->query())->with('info', 'Machine updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Machine $machine)
    {
        $machine->delete();

        return redirect()->route('machines.index', $request->query())->with('info', 'Machine deleted successfully.');
    }

    public function dashboard()
    {
        $totalMachines = Machine::count();
        $machinesByStatus = Machine::with('status')->get()->groupBy('status.name')->map(function ($group) {
            return $group->count();
        });
        $machinesByPlant = Machine::with('plant')->get()->groupBy('plant.name')->map(function ($group) {
            return $group->count();
        });
        $fixedAssetsCount = Machine::where('is_fixed_asset', true)->count();
        $totalPartsQuantity = DB::table('machine_part')->sum('quantity');
        $recentMachines = Machine::with(['plant', 'status'])->orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard', compact('totalMachines', 'machinesByStatus', 'machinesByPlant', 'fixedAssetsCount', 'totalPartsQuantity', 'recentMachines'));
    }

    public function importForm()
    {
        return view('machines.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv',
        ]);

        $plant = Plant::find(1);
        if (!$plant) {
            return back()->withErrors(['import_file' => 'Plant with ID 1 was not found.']);
        }

        $file = $request->file('import_file');

        try {
            $rows = $this->parseImportFile($file);
        } catch (\Throwable $exception) {
            return back()->withErrors(['import_file' => $exception->getMessage()]);
        }

        $validRows = [];
        $seenControlNos = [];
        $errors = [];

        foreach ($rows as $rowData) {
            $rowNumber = $rowData['row'] ?? null;
            if ($this->isImportRowEmpty($rowData)) {
                continue;
            }

            $controlNo = trim((string) ($rowData['control_no'] ?? ''));
            if ($controlNo === '') {
                $errors[] = "Row {$rowNumber}: Control No. is required.";
                continue;
            }

            if (isset($seenControlNos[$controlNo])) {
                $errors[] = "Row {$rowNumber}: Control No '{$controlNo}' is duplicated in the file.";
                continue;
            }

            $seenControlNos[$controlNo] = true;

            $name = trim((string) ($rowData['name'] ?? ''));
            $currencyRaw = trim((string) ($rowData['currency'] ?? ''));
            $currency = $currencyRaw === '' ? null : strtoupper($currencyRaw);
            $unitPriceRaw = trim((string) ($rowData['unit_price'] ?? ''));
            $arrivalDateRaw = trim((string) ($rowData['arrival_date'] ?? ''));
            $location = trim((string) ($rowData['location'] ?? ''));

            if ($currency !== null && !in_array($currency, self::IMPORT_CURRENCIES, true)) {
                $errors[] = "Row {$rowNumber}: Currency '{$currency}' is invalid. Allowed values: " . implode(', ', self::IMPORT_CURRENCIES) . '.';
                continue;
            }

            $unitPrice = null;
            if ($unitPriceRaw !== '') {
                $unitPriceNormalized = str_replace([',', ' '], '', $unitPriceRaw);
                if (!is_numeric($unitPriceNormalized)) {
                    $errors[] = "Row {$rowNumber}: Unit Price '{$unitPriceRaw}' must be a numeric value.";
                    continue;
                }
                $unitPrice = (float) $unitPriceNormalized;
            }

            $arrivedDate = null;
            if ($arrivalDateRaw !== '') {
                $arrivedDate = $this->parseImportDate($arrivalDateRaw);
                if (!$arrivedDate) {
                    $errors[] = "Row {$rowNumber}: Arrival Date '{$arrivalDateRaw}' is not a valid date.";
                    continue;
                }
            }

            $validRows[] = [
                'row' => $rowNumber,
                'control_no' => $controlNo,
                'name' => $name,
                'brand' => trim((string) ($rowData['brand'] ?? '')),
                'model' => trim((string) ($rowData['model'] ?? '')),
                'serial_no' => trim((string) ($rowData['serial_no'] ?? '')),
                'supplier' => trim((string) ($rowData['supplier'] ?? '')),
                'arrived_date' => $arrivedDate,
                'currency' => $currency,
                'unit_price' => $unitPrice,
                'location' => $location,
                'remark' => trim((string) ($rowData['remark'] ?? '')),
            ];
        }

        if (empty($validRows) && empty($errors)) {
            return back()->withErrors(['import_file' => 'No valid data rows were found in the uploaded file.']);
        }

        if (!empty($errors)) {
            return back()->withErrors(['import_file' => $errors]);
        }

        $existingControlNos = Machine::whereIn('control_no', array_keys($seenControlNos))
            ->pluck('control_no')
            ->all();

        if (!empty($existingControlNos)) {
            $errors = [];
            foreach ($validRows as $rowData) {
                if (in_array($rowData['control_no'], $existingControlNos, true)) {
                    $errors[] = "Row {$rowData['row']}: Control No '{$rowData['control_no']}' already exists in the system.";
                }
            }
            return back()->withErrors(['import_file' => $errors]);
        }

        $timestamp = now();
        $userId = $request->user()?->id;

        $insertData = array_map(function ($rowData) use ($timestamp, $userId) {
            return [
                'control_no' => $rowData['control_no'],
                'name' => $rowData['name'],
                'brand' => $rowData['brand'],
                'model' => $rowData['model'],
                'serial_no' => $rowData['serial_no'],
                'supplier' => $rowData['supplier'],
                'arrived_date' => $rowData['arrived_date'],
                'location' => $rowData['location'],
                'currency' => $rowData['currency'],
                'unit_price' => $rowData['unit_price'],
                'plant_id' => 1,
                'status_id' => 1,
                'is_fixed_asset' => false,
                'remark' => $rowData['remark'],
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $validRows);

        Machine::insert($insertData);

        return redirect()->route('machines.index')->with('info', count($insertData) . ' machines imported successfully.');
    }

    /**
     * Show BOM editor for a machine.
     */
    public function editParts(Machine $machine)
    {
        $parts = Part::with(['category', 'unit'])->orderBy('name')->get();
        $machine->load('parts');

        return view('machines.parts', compact('machine', 'parts'));
    }

    /**
     * Return a partial list of machines for copying BOM (AJAX).
     */
    public function listForCopy(Request $request, Machine $machine)
    {
        $q = $request->query('q', null);

        $query = Machine::with('plant')->where('id', '!=', $machine->id)->orderBy('control_no');

        if ($q !== null && $q !== '') {
            $query->where(function ($b) use ($q) {
                $b->where('control_no', 'like', '%' . $q . '%')
                  ->orWhere('name', 'like', '%' . $q . '%');
            });
        } else {
            // By default, show machines that have the same name as the source machine
            $query->where('name', $machine->name);
        }

        $machines = $query->paginate(10)->withQueryString();

        return view('machines._copy_list', compact('machines', 'machine'));
    }

    /**
     * Copy BOM from $machine to selected target machines.
     */
    public function copyTo(Request $request, Machine $machine)
    {
        $request->validate([
            'targets' => 'required|array',
            'targets.*' => 'exists:machines,id|different:'.$machine->id,
        ]);

        $targets = $request->input('targets', []);

        // Prepare sync data from source machine parts
        $syncData = [];
        foreach ($machine->parts as $part) {
            $syncData[$part->id] = [
                'quantity' => $part->pivot->quantity ?? 1,
                'notes' => $part->pivot->notes ?? null,
            ];
        }

        foreach ($targets as $targetId) {
            $target = Machine::find($targetId);
            if ($target) {
                // Replace target BOM with source BOM
                $target->parts()->sync($syncData);
            }
        }

        session()->flash('info', 'BOM copied to selected machines.');

        if ($request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect()->route('machines.parts.edit', $machine->id)->with('info', 'BOM copied to selected machines.');
    }

    /**
     * Update BOM (parts list) for a machine.
     */
    public function updateParts(Request $request, Machine $machine)
    {
        $inputParts = $request->input('parts', []);
        $syncData = [];

        foreach ($inputParts as $partId => $data) {
            $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;
            if ($quantity > 0) {
                $syncData[$partId] = ['quantity' => $quantity];
            }
        }

        $machine->parts()->sync($syncData);

        return redirect()->route('machines.show', $machine->id)->with('info', 'Bill of Materials updated.');
    }

    private function isImportRowEmpty(array $rowData): bool
    {
        foreach (['control_no', 'name', 'brand', 'model', 'serial_no', 'supplier', 'arrival_date', 'currency', 'unit_price', 'location', 'remark'] as $field) {
            if (trim((string) ($rowData[$field] ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    private function parseImportFile($file): array
    {
        $sheets = Excel::toCollection(new MachineImport(), $file);
        $sheet = $sheets->first();

        if ($sheet === null) {
            return [];
        }

        $rows = [];
        foreach ($sheet as $index => $row) {
            if ($index === 0) {
                continue;
            }

            $rows[] = [
                'row' => $index + 1,
                'control_no' => $row[1] ?? '',
                'name' => $row[2] ?? '',
                'brand' => $row[3] ?? '',
                'model' => $row[4] ?? '',
                'serial_no' => $row[5] ?? '',
                'supplier' => $row[6] ?? '',
                'arrival_date' => $row[7] ?? '',
                'currency' => $row[8] ?? '',
                'unit_price' => $row[9] ?? '',
                'location' => $row[10] ?? '',
                'remark' => $row[12] ?? '',
            ];
        }

        return $rows;
    }

    private function parseImportDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $floatValue = (float) $value;
            if ($floatValue > 59) {
                $floatValue -= 1;
            }
            $unixTimestamp = ($floatValue - 25569) * 86400;
            return gmdate('Y-m-d', (int) round($unixTimestamp));
        }

        try {
            return (new \DateTime($value))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
