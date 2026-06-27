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

                <form method="GET" action="{{ route('units.index') }}" class="row gx-3 gy-3 align-items-end mb-3">
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
                                <a href="{{ route('units.index') }}" class="btn btn-secondary w-100">Reset</a>
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
                            @forelse ($units as $unit)
                                <tr>
                                    <td class="text-center align-middle">{{ $units->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $unit->name }}</td>
                                    <td class="text-center align-middle">{{ $unit->plant?->name ?? '-' }}</td>
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
                                    <td colspan="8" class="text-center">No units found.</td>
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
