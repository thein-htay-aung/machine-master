@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Categories</h5>
                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-light">+ Add New Category</a>
            </div>

            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
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
                                    <td class="align-middle">{{ $category->createdBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $category->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="align-middle">{{ $category->updatedBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $category->updated_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-info">Edit</a>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete this category?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No categories found.</td>
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
