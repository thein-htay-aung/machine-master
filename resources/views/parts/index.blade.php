@extends('layouts.app')

@section('content')

    <div class="container-fluid px-4">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Parts</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('parts.export', request()->query()) }}" class="btn btn-sm btn-light">Download Excel</a>
                    @if(auth()->user()->canEditRecords())
                        <a href="{{ route('parts.create') }}" class="btn btn-sm btn-light">+ Add New Part</a>
                    @endif
                </div>
            </div>

            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('parts.index') }}" class="row gx-3 gy-3 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label visually-hidden" for="filter-name">Name</label>
                        <input id="filter-name" type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label visually-hidden" for="filter-category">Category</label>
                        <select id="filter-category" name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label visually-hidden" for="filter-plant">Plant</label>
                        <select id="filter-plant" name="plant_id" class="form-select">
                            @if($plants->count() > 1)
                                <option value="">All Plants</option>
                            @endif
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}" {{ request('plant_id', $defaultPlantId) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label visually-hidden" for="filter-status">Status</label>
                        <select id="filter-status" name="is_active" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="row gx-2">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('parts.index') }}" class="btn btn-secondary w-100">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive" style="max-height: 600px">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>
                                <th scope="col">Name</th>
                                <th scope="col">Model</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Category</th>
                                <th scope="col" class="text-center">Plant</th>
                                <th scope="col" class="text-center">Location</th>
                                <th scope="col" class="text-center">Unit</th>
                                <th scope="col" class="text-center">Min Qty</th>
                                <th scope="col" class="text-center">Active</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($parts as $part)
                                <tr>
                                    <td class="text-center align-middle">{{ $parts->firstItem() + $loop->index }}</td>

                                    <td class="align-middle text-center">
                                        <img src="{{ $part->image_url }}"  style="width: 36px; height: 36px; object-fit: cover; border-radius: 0.35rem;">
                                    </td>

                                    <td class="align-middle">{{ $part->name }}</td>
                                    <td class="align-middle">{{ $part->model ?? '-' }}</td>
                                    <td class="align-middle">{{ $part->brand ?? '-' }}</td>
                                    <td class="align-middle">{{ $part->category?->name ?? '-' }}</td>
                                    <td class="align-middle text-center">{{ $part->plant?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $part->location }}</td>
                                    <td class="align-middle text-center">{{ $part->unit?->name ?? '-' }}</td>
                                    <td class="align-middle text-center">{{ $part->min_qty ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $part->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $part->is_active ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ route('parts.show', ['part' => $part->id] + request()->query()) }}" class="btn btn-sm p-0 border-0 bg-transparent text-info" title="Detail"><i class="bi bi-eye fs-5"></i></a>
                                            @if(auth()->user()->canEditRecords())
                                                <a href="{{ route('parts.edit', $part->id) }}{{ request()->getQueryString() ? ('?' . request()->getQueryString()) : '' }}" class="btn btn-sm p-0 border-0 bg-transparent text-warning" title="Edit"><i class="bi bi-pencil-square fs-5"></i></a>
                                            @endif
                                            @if(auth()->user()->canDeleteRecords())
                                                <form action="{{ route('parts.destroy', $part->id) }}{{ request()->getQueryString() ? ('?' . request()->getQueryString()) : '' }}" method="POST" onsubmit="return confirm('Delete this part?');" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent text-danger" title="Delete"><i class="bi bi-trash fs-5"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">No parts found.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $parts->withQueryString()->links() }}
                </div>
            </div>

        </div>

    </div>

@endsection
