@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Current Stock</h5>
                <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-light">+ Add Purchase</a>
            </div>

            <div class="card-body">

                <form method="GET" action="{{ route('stocks.daily.export') }}" class="row gx-3 gy-1 align-items-center mb-3 border rounded p-3 bg-light">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-nowrap">Daily Stock From</label>
                            <input type="date" name="date_from" value="{{ request('date_from', now()->format('Y-m-d')) }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-nowrap">Daily Stock To</label>
                            <input type="date" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">Download Daily Stock Excel</button>
                    </div>
                </form>
                
                <form method="GET" action="{{ route('stocks.index') }}" class="row gx-3 gy-3 align-items-end mb-4">
                    <div class="col-md-4">
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Part name or model">
                    </div>
                    <div class="col-md-4">
                        <div class="row gx-2">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('stocks.index') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Part Name</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Model</th>
                                <th scope="col">Category</th>
                                <th scope="col" class="text-center">Plant</th>
                                <th scope="col">Unit</th>
                                <th scope="col" class="text-end">Current Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                <tr>
                                    <td class="text-center align-middle">{{ $stocks->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $stock->item?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $stock->item?->brand ?? '-' }}</td>
                                    <td class="align-middle">{{ $stock->item?->model ?? '-' }}</td>
                                    <td class="align-middle">{{ $stock->item?->category?->name ?? '-' }}</td>
                                    <td class="text-center align-middle">{{ $stock->item?->plant?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $stock->item?->unit?->name ?? '-' }}</td>
                                    <td class="text-end align-middle">{{ number_format($stock->qty) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No current stock found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $stocks->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
