@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Machine Details</h5>
                <a href="{{ route('machines.index', request()->query()) }}" class="btn btn-sm btn-light">Back to List</a>
            </div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="{{ $machine->image_url }}" alt="Machine image" class="img-fluid rounded" style="max-height: 360px; object-fit: cover; width: auto; max-width: 100%;">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th class="w-25">Control No.</th>
                                <td>{{ $machine->control_no }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $machine->name }}</td>
                            </tr>
                            <tr>
                                <th>Brand</th>
                                <td>{{ $machine->brand ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td>{{ $machine->model ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Serial No.</th>
                                <td>{{ $machine->serial_no ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>{{ $machine->supplier ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Arrived Date</th>
                                <td>{{ $machine->arrived_date ? $machine->arrived_date->format('Y-m-d') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td>{{ $machine->location ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $machine->status?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Plant</th>
                                <td>{{ $machine->plant->name }}</td>
                            </tr>

                            <tr>
                                <th>Currency</th>
                                <td>{{ $machine->currency ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Unit Price</th>
                                <td>{{ $machine->unit_price !== null ? number_format($machine->unit_price, 2) : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dimension</th>
                                <td>{{ $machine->dimension ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <td>{{ $machine->weight ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Electrical</th>
                                <td>{{ $machine->electrical ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Fixed Asset</th>
                                <td>{{ $machine->is_fixed_asset ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <th>Remark</th>
                                <td>{{ $machine->remark ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td>{{ $machine->createdBy?->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $machine->created_at ? $machine->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Updated By</th>
                                <td>{{ $machine->updatedBy?->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $machine->updated_at ? $machine->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
