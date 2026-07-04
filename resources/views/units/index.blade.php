@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Units</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('units.export') }}" class="btn btn-sm btn-light">Download Excel</a>
                    @if(auth()->user()->canEditRecords())
                        <a href="{{ route('units.create') }}" class="btn btn-sm btn-light">+ Add New Unit</a>
                    @endif
                </div>
            </div>

            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
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
                            @forelse ($units as $unit)
                                <tr>
                                    <td class="text-center align-middle">{{ $units->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $unit->name }}</td>
                                    <td class="align-middle">{{ $unit->createdBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $unit->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="align-middle">{{ $unit->updatedBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $unit->updated_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            @if(auth()->user()->canEditRecords())
                                                <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-sm p-0 border-0 bg-transparent text-warning" title="Edit"><i class="bi bi-pencil-square fs-5"></i></a>
                                            @endif
                                            @if(auth()->user()->canDeleteRecords())
                                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Delete this unit?');" class="m-0">
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
                                    <td colspan="7" class="text-center">No units found.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $units->withQueryString()->links() }}
                </div>
            </div>

        </div>

    </div>

@endsection
