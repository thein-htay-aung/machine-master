@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Stock Adjustments</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('stock-adjustments.export', request()->query()) }}" class="btn btn-sm btn-light">Download Excel</a>
                    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-sm btn-light">+ Add Adjustment</a>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('stock-adjustments.index') }}" class="row gx-3 gy-3 align-items-end mb-3">
                    <div class="col-md-3">
                        <input type="text" name="adjustment_no" value="{{ request('adjustment_no') }}" class="form-control" placeholder="Search adjustment no">
                    </div>
                    <div class="col-md-3">
                        <select name="part_id" class="form-select">
                            <option value="">All parts</option>
                            @foreach($parts as $part)
                                <option value="{{ $part->id }}" {{ request('part_id') == $part->id ? 'selected' : '' }}>{{ $part->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" value="{{ request('date_from', $dateFrom) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" value="{{ request('date_to', $dateTo) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <div class="row gx-2">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Adjustment No</th>
                                <th scope="col">Part Name</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Model</th>
                                <th scope="col" class="text-center">Symbol</th>
                                <th scope="col" class="text-end">Qty</th>
                                <th scope="col" class="text-end">Price</th>
                                <th scope="col" class="text-end">Amount</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Adjusted Date</th>
                                <th scope="col">Adjusted By</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($adjustments as $adjustment)
                                @php
                                    $price = $adjustment->part?->currentStock?->last_purchase_price ?? 0;
                                    $amount = $adjustment->qty * $price;
                                @endphp
                                <tr>
                                    <td class="text-center align-middle">{{ $adjustments->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $adjustment->adjustment_no }}</td>
                                    <td class="align-middle">{{ $adjustment->part?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $adjustment->part?->brand ?? '-' }}</td>
                                    <td class="align-middle">{{ $adjustment->part?->model ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $adjustment->symbol === '+' ? 'bg-success' : 'bg-danger' }}">{{ $adjustment->symbol }}</span>
                                    </td>
                                    <td class="text-end align-middle">{{ number_format($adjustment->qty) }}</td>
                                    <td class="text-end align-middle">{{ number_format($price) }}</td>
                                    <td class="text-end align-middle">{{ number_format($amount) }}</td>
                                    <td class="align-middle">{{ $adjustment->reason }}</td>
                                    <td class="align-middle">{{ $adjustment->adjusted_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="align-middle">{{ $adjustment->adjusted_by }}</td>
                                    <td class="align-middle">{{ $adjustment->createdBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $adjustment->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">No stock adjustments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $adjustments->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
