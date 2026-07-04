<?php

namespace App\Http\Controllers;

use App\Exports\CategoriesExport;
use App\Models\Category;
use App\Models\Part;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['createdBy', 'updatedBy'])->orderBy('name')->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function export()
    {
        return Excel::download(new CategoriesExport(), 'categories.xlsx');
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $validated['updated_by'] = $request->user()->id;

        $category->update($validated);

        return redirect()->route('categories.index', $request->query())->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category)
    {
        if (Part::where('category_id', $category->id)->exists()) {
            return redirect()->route('categories.index', $request->query())->with('error', 'This category is being used by parts and cannot be deleted.');
        }

        $category->delete();

        return redirect()->route('categories.index', $request->query())->with('success', 'Category deleted successfully.');
    }

}
