@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Machine</h5>
                <a href="{{ route('machines.index', request()->query()) }}" class="btn btn-sm btn-light">Back to List</a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('machines.update', $machine->id) }}{{ request()->getQueryString() ? ('?' . request()->getQueryString()) : '' }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Control No.</label>
                            <input type="text" name="control_no" value="{{ old('control_no', $machine->control_no) }}" class="form-control @error('control_no') is-invalid @enderror">
                            @error('control_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Machine Name</label>
                            <input type="text" name="name" value="{{ old('name', $machine->name) }}" class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand', $machine->brand) }}" class="form-control @error('brand') is-invalid @enderror">
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" value="{{ old('model', $machine->model) }}" class="form-control @error('model') is-invalid @enderror">
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Serial No.</label>
                            <input type="text" name="serial_no" value="{{ old('serial_no', $machine->serial_no) }}" class="form-control @error('serial_no') is-invalid @enderror">
                            @error('serial_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" name="supplier" value="{{ old('supplier', $machine->supplier) }}" class="form-control @error('supplier') is-invalid @enderror">
                            @error('supplier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Machine Image</label>
                            <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Arrived Date</label>
                            <input type="date" name="arrived_date" value="{{ old('arrived_date', $machine->arrived_date?->format('Y-m-d')) }}" class="form-control @error('arrived_date') is-invalid @enderror">
                            @error('arrived_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" value="{{ old('location', $machine->location) }}" class="form-control @error('location') is-invalid @enderror">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status_id" class="form-select @error('status_id') is-invalid @enderror">
                                <option value="">Select status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id', $machine->status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                            @error('status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Plant</label>
                            <select name="plant_id" class="form-select @error('plant_id') is-invalid @enderror">
                                <option value="">Select Plant</option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}" {{ old('plant_id', $machine->plant_id) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                @endforeach
                            </select>
                            @error('plant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Dimension</label>
                            <input type="text" name="dimension" value="{{ old('dimension', $machine->dimension) }}" class="form-control @error('dimension') is-invalid @enderror">
                            @error('dimension')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Weight</label>
                            <input type="text" name="weight" value="{{ old('weight', $machine->weight) }}" class="form-control @error('weight') is-invalid @enderror">
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Electrical</label>
                            <input type="text" name="electrical" value="{{ old('electrical', $machine->electrical) }}" class="form-control @error('electrical') is-invalid @enderror">
                            @error('electrical')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Currency</label>
                            <select name="currency" class="form-select @error('currency') is-invalid @enderror">
                                <option value="">Select currency</option>
                                @foreach(['MMK', 'USD', 'SGD', 'JPY', 'CNY'] as $currency)
                                    <option value="{{ $currency }}" {{ old('currency', $machine->currency) === $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                @endforeach
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Unit Price</label>
                            <input type="number" name="unit_price" step="0.01" min="0" value="{{ old('unit_price', $machine->unit_price) }}" class="form-control @error('unit_price') is-invalid @enderror">
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remark</label>
                            <textarea name="remark" rows="3" class="form-control @error('remark') is-invalid @enderror">{{ old('remark', $machine->remark) }}</textarea>
                            @error('remark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($machine->image)
                            <div class="col-12">
                                <label class="form-label">Current Image</label>
                                <div>
                                    <img src="{{ $machine->image_url }}" alt="Machine image" class="img-fluid rounded" style="max-height: 220px; object-fit: cover;">
                                </div>
                            </div>
                        @endif

                        <div class="col-12 d-flex align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_fixed_asset" id="is_fixed_asset" value="1" {{ old('is_fixed_asset', $machine->is_fixed_asset) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_fixed_asset">Fixed Asset</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Machine</button>
                            <a href="{{ route('machines.index', request()->query()) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
