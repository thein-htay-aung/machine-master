<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Exports\PartsExport;
use App\Models\CurrentStock;
use App\Models\Part;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PartController extends Controller
{
    use ResolvesPlantOptions;

    public function index(Request $request)
    {
        $query = Part::with(['category', 'plant', 'unit'])
            ->leftJoin('categories', 'parts.category_id', '=', 'categories.id')
            ->select('parts.*')
            ->orderBy('categories.name')
            ->orderBy('parts.name')
            ->orderBy('parts.brand')
            ->orderBy('parts.model');

        $name = $request->query('name', null);
        $plantId = $request->query('plant_id', null);
        $categoryId = $request->query('category_id', null);
        $isActive = $request->query('is_active', null);

        if ($name !== null && $name !== '') {
            $query->where('parts.name', 'like', '%' . $name . '%');
        }

        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();
        $categories = $this->selectableCategories();
        $selectableCategoryIds = $categories->pluck('id')->all();

        if ($categoryId !== null && $categoryId !== '' && in_array((int) $categoryId, $selectableCategoryIds, true)) {
            $query->where('parts.category_id', $categoryId);
        }

        $selectablePlantIds = $plants->pluck('id')->all();

        if ($plantId !== null && $plantId !== '' && in_array((int) $plantId, $selectablePlantIds, true)) {
            $query->where('parts.plant_id', $plantId);
        } elseif ($defaultPlantId) {
            $query->where('parts.plant_id', $defaultPlantId);
        }

        if ($isActive !== null && $isActive !== '') {
            $query->where('parts.is_active', (int) $isActive);
        }

        $parts = $query->paginate(10)->withQueryString();

        return view('parts.index', compact('parts', 'categories', 'plants', 'defaultPlantId'));
    }

    public function export(Request $request)
    {
        return Excel::download(new PartsExport($request->query()), 'parts.xlsx');
    }

    /**
     * Server-side search used by BOM modal (returns partial HTML when AJAX).
     */
    public function search(Request $request)
    {
        $q = $request->query('q', null);

        $query = Part::with(['category', 'plant', 'unit'])
            ->leftJoin('categories', 'parts.category_id', '=', 'categories.id')
            ->select('parts.*')
            ->orderBy('categories.name')
            ->orderBy('parts.name')
            ->orderBy('parts.brand')
            ->orderBy('parts.model');

        if ($q !== null && $q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('parts.name', 'like', '%' . $q . '%')
                        ->orWhere('parts.model', 'like', '%' . $q . '%');
            });
        }

        $parts = $query->paginate(8)->withQueryString();

        if ($request->ajax()) {
            return view('parts._search_results', compact('parts'));
        }

        $categories = $this->selectableCategories();
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();
        return view('parts.index', compact('parts', 'categories', 'plants', 'defaultPlantId'));
    }

    public function create()
    {
        $plants = $this->selectablePlants();
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $defaultPlantId = $this->defaultPlantId();

        return view('parts.create', compact('categories', 'plants', 'units', 'defaultPlantId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'plant_id' => ['required', $this->plantValidationRule()],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id'),
            ],
            'is_active' => 'sometimes|boolean',
            'unit_id' => [
                'required',
                Rule::exists('units', 'id'),
            ],
            'min_qty' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('parts', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        DB::transaction(function () use ($validated) {
            $part = Part::create($validated);

            CurrentStock::create([
                'item_id' => $part->id,
                'qty' => 0,
            ]);
        });

        return redirect()->route('parts.index')->with('success', 'Part created successfully.');
    }

    public function show(Part $part)
    {
        $part->load(['category', 'plant', 'unit', 'createdBy', 'updatedBy']);

        return view('parts.show', compact('part'));
    }

    public function edit(Part $part)
    {
        $plants = $this->selectablePlants();
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $defaultPlantId = $this->defaultPlantId($part->plant_id);

        return view('parts.edit', compact('part', 'categories', 'plants', 'units', 'defaultPlantId'));
    }

    public function update(Request $request, Part $part)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'plant_id' => ['required', $this->plantValidationRule()],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id'),
            ],
            'is_active' => 'sometimes|boolean',
            'unit_id' => [
                'required',
                Rule::exists('units', 'id'),
            ],
            'min_qty' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'remove_image' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($part->image && Storage::disk('public')->exists($part->image)) {
                Storage::disk('public')->delete($part->image);
            }
            $validated['image'] = $request->file('image')->store('parts', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($part->image && Storage::disk('public')->exists($part->image)) {
                Storage::disk('public')->delete($part->image);
            }
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }
        unset($validated['remove_image']);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = $request->user()->id;

        $part->update($validated);

        return redirect()->route('parts.index', $request->query())->with('success', 'Part updated successfully.');
    }

    public function destroy(Request $request, Part $part)
    {
        if ($part->purchases()->exists() || $part->issues()->exists() || $part->stockAdjustments()->exists()) {
            return redirect()->route('parts.index', $request->query())->with('error', 'This part is being used by purchase, issue, or stock adjustment records and cannot be deleted.');
        }

        $image = $part->image;

        DB::transaction(function () use ($part) {
            $part->currentStock()->delete();
            $part->delete();
        });

        if ($image && Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('parts.index', $request->query())->with('success', 'Part deleted successfully.');
    }

}
