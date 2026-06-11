@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Machine Details</h5>
                <a href="{{ route('machines.index') }}" class="btn btn-sm btn-light">Back to List</a>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 text-center">
                        <img src="{{ $machine->image_url }}" alt="Machine image" class="img-fluid rounded mb-4" style="max-height: 320px; object-fit: cover;">
                    </div>
                    <div class="col-md-6">
                        <strong>Control No.</strong>
                        <p>{{ $machine->control_no }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Name</strong>
                        <p>{{ $machine->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Brand</strong>
                        <p>{{ $machine->brand ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Model</strong>
                        <p>{{ $machine->model ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Serial No.</strong>
                        <p>{{ $machine->serial_no ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Supplier</strong>
                        <p>{{ $machine->supplier ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Arrived Date</strong>
                        <p>{{ $machine->arrived_date ? $machine->arrived_date->format('Y-m-d') : '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Location</strong>
                        <p>{{ $machine->location ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status</strong>
                        <p>{{ $machine->status?->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Plant</strong>
                        <p>{{ $machine->plant->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Currency</strong>
                        <p>{{ $machine->currency ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Unit Price</strong>
                        <p>{{ $machine->unit_price !== null ? number_format($machine->unit_price, 2) : '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Dimension</strong>
                        <p>{{ $machine->dimension ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Weight</strong>
                        <p>{{ $machine->weight ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Electrical</strong>
                        <p>{{ $machine->electrical ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Fixed Asset</strong>
                        <p>{{ $machine->is_fixed_asset ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="col-12">
                        <strong>Remark</strong>
                        <p>{{ $machine->remark ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
