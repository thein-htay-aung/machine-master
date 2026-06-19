<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartController extends Controller
{
    public function index(Request $request)
    {
        $query = Part::with(['category', 'unit'])->orderBy('name');

        $name = $request->query('name', null);
        $categoryId = $request->query('category_id', null);
        $isActive = $request->query('is_active', null);

        if ($name !== null && $name !== '') {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($categoryId !== null && $categoryId !== '') {
            $query->where('category_id', $categoryId);
        }

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', (int) $isActive);
        }

        $parts = $query->paginate(10)->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('parts.index', compact('parts', 'categories'));
    }

    /**
     * Server-side search used by BOM modal (returns partial HTML when AJAX).
     */
    public function search(Request $request)
    {
        $q = $request->query('q', null);

        $query = Part::with(['category', 'unit'])->orderBy('name');

        if ($q !== null && $q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%' . $q . '%')
                        ->orWhere('model', 'like', '%' . $q . '%');
            });
        }

        $parts = $query->paginate(8)->withQueryString();

        if ($request->ajax()) {
            return view('parts._search_results', compact('parts'));
        }

        $categories = \App\Models\Category::orderBy('name')->get();
        return view('parts.index', compact('parts', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('parts.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'unit_id' => 'nullable|exists:units,id',
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

        Part::create($validated);

        return redirect()->route('parts.index')->with('success', 'Part created successfully.');
    }

    public function show(Part $part)
    {
        $part->load(['category', 'unit', 'createdBy', 'updatedBy']);

        return view('parts.show', compact('part'));
    }

    public function edit(Part $part)
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('parts.edit', compact('part', 'categories', 'units'));
    }

    public function update(Request $request, Part $part)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'unit_id' => 'nullable|exists:units,id',
            'min_qty' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($part->image && Storage::disk('public')->exists($part->image)) {
                Storage::disk('public')->delete($part->image);
            }
            $validated['image'] = $request->file('image')->store('parts', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = $request->user()->id;

        $part->update($validated);

        return redirect()->route('parts.index', $request->query())->with('success', 'Part updated successfully.');
    }

    public function destroy(Request $request, Part $part)
    {
        if ($part->image && Storage::disk('public')->exists($part->image)) {
            Storage::disk('public')->delete($part->image);
        }

        $part->delete();

        return redirect()->route('parts.index', $request->query())->with('success', 'Part deleted successfully.');
    }
}
