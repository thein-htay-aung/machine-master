@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Parts</h5>
                <a href="{{ route('parts.create') }}" class="btn btn-sm btn-light">+ Add New Part</a>
            </div>

            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('parts.index') }}" class="row gx-3 gy-3 align-items-end mb-3">
                    <div class="col-md-3">
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Search name">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">All categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="is_active" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="row gx-2">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('parts.index') }}" class="btn btn-secondary w-100">Reset</a>
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
                                <th scope="col">Unit</th>
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
                                    <td class="align-middle">{{ $part->unit?->name ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $part->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $part->is_active ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <a href="{{ route('parts.show', $part->id) }}" class="btn btn-sm btn-warning">Detail</a>
                                            <a href="{{ route('parts.edit', $part->id) }}" class="btn btn-sm btn-info">Edit</a>
                                            <form action="{{ route('parts.destroy', $part->id) }}" method="POST" onsubmit="return confirm('Delete this part?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No parts found.</td>
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
