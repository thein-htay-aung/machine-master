<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Exports\CategoriesExport;
use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    use ResolvesPlantOptions;

    public function index(Request $request)
    {
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();
        $plantId = $request->query('plant_id');
        $selectablePlantIds = $plants->pluck('id')->all();

        $query = Category::with(['plant', 'createdBy', 'updatedBy'])->orderBy('name');

        if ($plantId !== null && $plantId !== '' && in_array((int) $plantId, $selectablePlantIds, true)) {
            $query->where('plant_id', $plantId);
        } elseif ($defaultPlantId) {
            $query->where('plant_id', $defaultPlantId);
        }

        $categories = $query->paginate(10)->withQueryString();

        return view('categories.index', compact('categories', 'plants', 'defaultPlantId'));
    }

    public function export()
    {
        return Excel::download(new CategoriesExport(), 'categories.xlsx');
    }

    public function create()
    {
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId();

        return view('categories.create', compact('plants', 'defaultPlantId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'plant_id' => ['required', $this->plantValidationRule()],
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $plants = $this->selectablePlants();
        $defaultPlantId = $this->defaultPlantId($category->plant_id);

        return view('categories.edit', compact('category', 'plants', 'defaultPlantId'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'plant_id' => ['required', $this->plantValidationRule()],
        ]);

        $validated['updated_by'] = $request->user()->id;

        $category->update($validated);

        return redirect()->route('categories.index', $request->query())->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index', $request->query())->with('success', 'Category deleted successfully.');
    }

}
