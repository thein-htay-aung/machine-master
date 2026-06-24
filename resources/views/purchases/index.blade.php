@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Purchases</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('purchases.export', request()->query()) }}" class="btn btn-sm btn-light">Download Excel</a>
                    <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-light">+ Add Purchase</a>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('purchases.index') }}" class="row gx-3 gy-3 align-items-end mb-3">
                    <div class="col-md-3">
                        <input type="text" name="invoice" value="{{ request('invoice') }}" class="form-control" placeholder="Search invoice">
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
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Invoice</th>
                                <th scope="col">Par Name</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Model</th>
                                <th scope="col" class="text-end">Price</th>
                                <th scope="col" class="text-end">Qty</th>
                                <th scope="col" class="text-end">Amount</th>
                                <th scope="col">Remark</th>
                                <th scope="col">Purchased Date</th>
                                <th scope="col">Purchase By</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td class="text-center align-middle">{{ $purchases->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $purchase->invoice }}</td>
                                    <td class="align-middle">{{ $purchase->part?->name ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->part?->brand ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->part?->model ?? '-' }}</td>
                                    <td class="text-end align-middle">{{ number_format($purchase->price, 2) }}</td>
                                    <td class="text-end align-middle">{{ number_format($purchase->qty) }}</td>
                                    <td class="text-end align-middle">{{ number_format($purchase->amount, 2) }}</td>
                                    <td class="align-middle">{{ $purchase->remark ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->purchased_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->purchase_by ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->createdBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $purchase->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">No purchases found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $purchases->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
