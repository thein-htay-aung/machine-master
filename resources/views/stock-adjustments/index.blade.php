@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Stock Adjustments</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('stock-adjustments.export', request()->query()) }}" class="btn btn-sm btn-light">Download Excel</a>
                    @if(auth()->user()->canEditRecords())
                        <a href="{{ route('stock-adjustments.create') }}" class="btn btn-sm btn-light">+ Add Adjustment</a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('stock-adjustments.index') }}" class="row gx-2 gy-2 align-items-end mb-3">
                    <div class="col-md-6 col-xl-2">
                        <input type="text" name="adjustment_no" value="{{ request('adjustment_no') }}" class="form-control" placeholder="Search adjustment no">
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <input type="text" name="part_name" value="{{ request('part_name') }}" class="form-control" placeholder="Part name or model">
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-xl-1">
                        <select name="plant_id" class="form-select">
                            @if($plants->count() > 1)
                                <option value="">All Plants</option>
                            @endif
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}" {{ request('plant_id', $defaultPlantId) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <input type="date" name="date_from" value="{{ request('date_from', $dateFrom) }}" class="form-control">
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <input type="date" name="date_to" value="{{ request('date_to', $dateTo) }}" class="form-control">
                    </div>
                    <div class="col-md-6 col-xl-1">
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary btn-sm flex-fill">Reset</a>
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
                                <th scope="col">Category</th>
                                <th scope="col">Model</th>
                                <th scope="col" class="text-center">Symbol</th>
                                <th scope="col" class="text-end">Qty</th>
                                <th scope="col" class="text-end">Price</th>
                                <th scope="col" class="text-end">Amount</th>
                                <th scope="col">Reason</th>
                                <th scope="col" class="text-center">Adjusted Date</th>
                                <th scope="col">Adjusted By</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($adjustments as $adjustment)
                                <tr>
                                    <td class="text-center align-middle">{{ $adjustments->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $adjustment->adjustment_no }}</td>
                                    <td class="align-middle">{{ $adjustment->part?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $adjustment->part?->brand ?? '-' }}</td>
                                    <td class="align-middle">{{ $adjustment->part?->category?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $adjustment->part?->model ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $adjustment->symbol === '+' ? 'bg-success' : 'bg-danger' }}">{{ $adjustment->symbol }}</span>
                                    </td>
                                    <td class="text-end align-middle">{{ number_format($adjustment->qty) }}</td>
                                    <td class="text-end align-middle">{{ number_format($adjustment->price) }}</td>
                                    <td class="text-end align-middle">{{ number_format($adjustment->amount) }}</td>
                                    <td class="align-middle">{{ $adjustment->reason }}</td>
                                    <td class="text-center align-middle">{{ $adjustment->adjusted_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="align-middle">{{ $adjustment->adjusted_by }}</td>
                                    <td class="align-middle">{{ $adjustment->createdBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $adjustment->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center">No stock adjustments found.</td>
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
