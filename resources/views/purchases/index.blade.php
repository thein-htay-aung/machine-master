@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Purchases</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('purchases.export', request()->query()) }}" class="btn btn-sm btn-light">Download Excel</a>
                    @if(auth()->user()->canEditRecords())
                        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-light">+ Add Purchase</a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('purchases.index') }}" class="row gx-2 gy-2 align-items-end mb-3">
                    <div class="col-md-6 col-xl-2">
                        <input type="text" name="invoice" value="{{ request('invoice') }}" class="form-control" placeholder="Search invoice">
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <input type="text" name="part_name" value="{{ request('part_name') }}" class="form-control" placeholder="Part name or model">
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <select name="category_id" class="form-select">
                            <option value="">All categories</option>
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
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm flex-fill">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Invoice</th>
                                <th scope="col">Part Name</th>
                                <th scope="col">Model</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Category</th>
                                <th scope="col" class="text-end">Price</th>
                                <th scope="col" class="text-end">Qty</th>
                                <th scope="col" class="text-end">Amount</th>
                                <th scope="col">Remark</th>
                                <th scope="col" class="text-center">Purchased Date</th>
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
                                    <td class="align-middle">{{ $purchase->part?->model ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->part?->brand ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->part?->category?->name ?? '-' }}</td>
                                    <td class="text-end align-middle">{{ number_format($purchase->price) }}</td>
                                    <td class="text-end align-middle">{{ number_format($purchase->qty) }}</td>
                                    <td class="text-end align-middle">{{ number_format($purchase->amount) }}</td>
                                    <td class="align-middle">{{ $purchase->remark ?? '-' }}</td>
                                    <td class="text-center align-middle">{{ $purchase->purchased_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->purchase_by ?? '-' }}</td>
                                    <td class="align-middle">{{ $purchase->createdBy?->name ?? 'System' }}</td>
                                    <td class="align-middle">{{ $purchase->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">No purchases found.</td>
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
