<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MachineController extends Controller
{
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

        Machine::create($validated);

        return redirect()->route('machines.index')->with('info', 'Machine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Machine $machine)
    {
        $machine->load(['plant', 'status']);

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

        $machine->update($validated);

        return redirect()->route('machines.index')->with('info', 'Machine updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();

        return redirect()->route('machines.index')->with('info', 'Machine deleted successfully.');
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
        $recentMachines = Machine::with(['plant', 'status'])->orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard', compact('totalMachines', 'machinesByStatus', 'machinesByPlant', 'fixedAssetsCount', 'recentMachines'));
    }
}
