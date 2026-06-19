@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Part Detail</h5>
                <a href="{{ route('parts.index', request()->query()) }}" class="btn btn-sm btn-light">Back to List</a>
            </div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="{{ $part->image_url }}" alt="Part image" class="img-fluid rounded" style="max-height: 240px; object-fit: cover; width: auto; max-width: 100%;">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th class="w-25">Name</th>
                                <td>{{ $part->name }}</td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td>{{ $part->model ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Brand</th>
                                <td>{{ $part->brand ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td>{{ $part->location ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $part->category?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td>{{ $part->unit?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Minimum Quantity</th>
                                <td>{{ $part->min_qty ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Active</th>
                                <td>{{ $part->is_active ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td>{{ $part->createdBy?->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $part->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Updated By</th>
                                <td>{{ $part->updatedBy?->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $part->updated_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
