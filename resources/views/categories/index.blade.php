@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Categories</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('categories.export') }}" class="btn btn-sm btn-light">Download Excel</a>
                    <a href="{{ route('categories.create') }}" class="btn btn-sm btn-light">+ Add New Category</a>
                </div>
            </div>

            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('categories.index') }}" class="row gx-3 gy-3 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label visually-hidden" for="filter-plant">Plant</label>
                        <select id="filter-plant" name="plant_id" class="form-select">
                            @if($plants->count() > 1)
                                <option value="">All plants</option>
                            @endif
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}" {{ request('plant_id', $defaultPlantId) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="row gx-2">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col" class="text-center">Plant</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Updated By</th>
                                <th scope="col">Updated At</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="text-center align-middle">{{ $categories->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $category->name }}</td>
                                    <td class="text-center align-middle">{{ $category->plant?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $category->createdBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $category->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="align-middle">{{ $category->updatedBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $category->updated_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ route('categories.edit', $category->id) }}{{ request()->getQueryString() ? ('?' . request()->getQueryString()) : '' }}" class="btn btn-sm p-0 border-0 bg-transparent text-warning" title="Edit"><i class="bi bi-pencil-square fs-5"></i></a>
                                            <form action="{{ route('categories.destroy', $category->id) }}{{ request()->getQueryString() ? ('?' . request()->getQueryString()) : '' }}" method="POST" onsubmit="return confirm('Delete this category?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent text-danger" title="Delete"><i class="bi bi-trash fs-5"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No categories found.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $categories->withQueryString()->links() }}
                </div>
            </div>

        </div>

    </div>

@endsection
