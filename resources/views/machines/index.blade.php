@extends('layouts.app')

@section('content')

    <div class="container-fluid px-4">

        <div class="card shadow-sm w-100">

            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <h5 class="mb-0">Machine List</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('machines.import') }}" class="btn btn-sm btn-light">Import Excel</a>
                        @endif
                        <a href="{{ route('machines.create') }}" class="btn btn-sm btn-light">+ Add New Machine</a>
                    </div>
                </div>
            </div>

            <div class="card-body">

                @if (session("info"))
                    <div class="alert alert-success">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="mb-4">
                    <form method="GET" action="{{ route('machines.index') }}" class="row gx-3 gy-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label visually-hidden" for="filter-control_no">Control No.</label>
                            <input id="filter-control_no" type="text" name="control_no" value="{{ request('control_no') }}" class="form-control" placeholder="Control No.">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label visually-hidden" for="filter-name">Name</label>
                            <input id="filter-name" type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Name">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label visually-hidden" for="filter-status">Status</label>
                            <select id="filter-status" name="status_id" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label visually-hidden" for="filter-plant">Plant</label>
                            <select id="filter-plant" name="plant_id" class="form-select">
                                <option value="">All Plants</option>
                                @foreach($plants as $plant)
                                    <option value="{{ $plant->id }}" {{ request('plant_id') == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="row gx-2">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('machines.index') }}" class="btn btn-secondary w-100">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive" style="max-height: 600px">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Control No.</th>
                                <th scope="col">Name</th>
                                <th scope="col">Image</th>
                                <th scope="col">Model</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Plant</th>
                                <th scope="col">Location</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($machines as $machine)
                                <tr>
                                    <td class="text-center align-middle">{{ $machines->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $machine->control_no }}</td>
                                    <td class="align-middle">{{ $machine->name }}</td>
                                    <td class="align-middle text-center">
                                        <img src="{{ $machine->image_url }}" alt="{{ $machine->name }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 0.35rem;">
                                    </td>
                                    <td class="align-middle">{{ $machine->model }}</td>
                                    <td class="align-middle">{{ $machine->brand }}</td>
                                    <td class="align-middle">
                                        @if($machine->status)
                                            @php
                                                $badgeClass = match($machine->status->name) {
                                                    'Operational' => 'bg-success',
                                                    'Out of Service' => 'bg-danger',
                                                    'Not in Use' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $machine->status->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">{{ $machine->plant->name }}</td>
                                    <td class="align-middle">{{ $machine->location }}</td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('machines.show', ['machine' => $machine->id] + request()->query()) }}" class="btn btn-sm btn-warning">Detail</a>
                                            <a href="{{ route('machines.edit', ['machine' => $machine->id] + request()->query()) }}" class="btn btn-sm btn-info">Edit</a>
                                            <form action="{{ route('machines.destroy', ['machine' => $machine->id] + request()->query()) }}" method="POST" onsubmit="return confirm('Delete this machine?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No machines found.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $machines->withQueryString()->links() }}
                </div>
            </div>

            
        </div>

    </div>
    
@endsection
