@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Total Machines</p>
                        <h2 class="mb-0">{{ $totalMachines }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Fixed Assets</p>
                        <h2 class="mb-0">{{ $fixedAssetsCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Total Parts Qty</p>
                        <h2 class="mb-0">{{ $totalPartsQuantity }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">Machines by Status</div>
                    <div class="card-body">
                        @forelse($machinesByStatus as $status => $count)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $status ?? 'N/A' }}</span>
                                <span class="badge bg-primary">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No machine data available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">Machines by Plant</div>
                    <div class="card-body">
                        @forelse($machinesByPlant as $plant => $count)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $plant ?? 'N/A' }}</span>
                                <span class="badge bg-success">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No plant data available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-dark text-white">Recent Machines</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Control No.</th>
                                <th>Name</th>
                                <th>Plant</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMachines as $machine)
                                <tr>
                                    <td>{{ $machine->control_no }}</td>
                                    <td>{{ $machine->name }}</td>
                                    <td>{{ $machine->plant?->name ?? 'N/A' }}</td>
                                    <td>{{ $machine->status?->name ?? 'N/A' }}</td>
                                    <td>{{ $machine->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No machines found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
