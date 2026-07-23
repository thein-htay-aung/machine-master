@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="mb-1">Dashboard</h4>
                <p class="text-muted mb-0">Stock, parts, and machine overview for {{ now()->format('Y-m-d') }}.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-end">
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2">
                    <label class="visually-hidden" for="dashboard-plant">Plant</label>
                    <select id="dashboard-plant" name="plant_id" class="form-select form-select-sm">
                        @if($plants->count() > 1)
                            <option value="">All Plants</option>
                        @endif
                        @foreach($plants as $plant)
                            <option value="{{ $plant->id }}" {{ $selectedPlantId == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">View</button>
                </form>
                <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary">+ Purchase</a>
                <a href="{{ route('issues.create') }}" class="btn btn-sm btn-outline-primary">+ Issue</a>
                <a href="{{ route('stock-adjustments.create') }}" class="btn btn-sm btn-outline-secondary">+ Adjustment</a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">Current Stock Qty</div>
                        <div class="display-6 fw-semibold">{{ number_format($currentStockQty) }}</div>
                        <div class="small text-muted">Total quantity on hand</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">Parts</div>
                        <div class="display-6 fw-semibold">{{ number_format($totalParts) }}</div>
                        <div class="small text-muted">Part master records</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">Low Stock</div>
                        <div class="display-6 fw-semibold text-danger">{{ number_format($lowStockCount) }}</div>
                        <div class="small text-muted">At or below minimum qty</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">Machines</div>
                        <div class="display-6 fw-semibold">{{ number_format($totalMachines) }}</div>
                        <div class="small text-muted">{{ number_format($fixedAssetsCount) }} fixed assets</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small">Today Purchases</div>
                        <h3 class="mb-0 text-success">{{ number_format($todayPurchaseQty) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small">Today Issues</div>
                        <h3 class="mb-0 text-danger">{{ number_format($todayIssueQty) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small">Adjustment In</div>
                        <h3 class="mb-0 text-success">{{ number_format($todayAdjustmentInQty) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small">Adjustment Out</div>
                        <h3 class="mb-0 text-danger">{{ number_format($todayAdjustmentOutQty) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                        <span>Low Stock Items</span>
                        <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-secondary">View Stock</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Part</th>
                                        <th>Category</th>
                                        <th class="text-end">Stock</th>
                                        <th class="text-end">Min</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lowStocks as $stock)
                                        <tr>
                                            <td>{{ $stock->item?->name ?? '-' }}</td>
                                            <td>{{ $stock->item?->category?->name ?? '-' }}</td>
                                            <td class="text-end text-danger fw-semibold">{{ number_format($stock->qty) }}</td>
                                            <td class="text-end">{{ number_format($stock->item?->min_qty ?? 0) }}</td>
                                            <td>{{ $stock->item?->unit?->name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No low stock items.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">Recent Stock Activity</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <h6 class="text-success">Purchases</h6>
                                @forelse($recentPurchases as $purchase)
                                    <div class="border-bottom py-2">
                                        <div class="fw-semibold">{{ $purchase->part?->name ?? '-' }}</div>
                                        <div class="small text-muted">{{ $purchase->invoice }} · Qty {{ number_format($purchase->qty) }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No purchases yet.</p>
                                @endforelse
                            </div>
                            <div class="col-lg-4">
                                <h6 class="text-danger">Issues</h6>
                                @forelse($recentIssues as $issue)
                                    <div class="border-bottom py-2">
                                        <div class="fw-semibold">{{ $issue->part?->name ?? '-' }}</div>
                                        <div class="small text-muted">{{ $issue->issue_no }} · Qty {{ number_format($issue->qty) }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No issues yet.</p>
                                @endforelse
                            </div>
                            <div class="col-lg-4">
                                <h6 class="text-secondary">Adjustments</h6>
                                @forelse($recentAdjustments as $adjustment)
                                    <div class="border-bottom py-2">
                                        <div class="fw-semibold">{{ $adjustment->part?->name ?? '-' }}</div>
                                        <div class="small text-muted">{{ $adjustment->adjustment_no }} · {{ $adjustment->symbol }}{{ number_format($adjustment->qty) }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No adjustments yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">Machines by Status</div>
                    <div class="card-body">
                        @forelse($machinesByStatus as $status => $count)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $status ?: 'N/A' }}</span>
                                <span class="badge bg-primary">{{ number_format($count) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No machine data available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">Machines by Plant</div>
                    <div class="card-body">
                        @forelse($machinesByPlant as $plant => $count)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $plant ?: 'N/A' }}</span>
                                <span class="badge bg-success">{{ number_format($count) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No plant data available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">Recent Machines</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Control No.</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentMachines as $machine)
                                        <tr>
                                            <td>{{ $machine->control_no }}</td>
                                            <td>{{ $machine->name }}</td>
                                            <td>{{ $machine->status?->name ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No machines found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
