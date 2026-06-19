@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Import Machines from Excel</h5>
                <a href="{{ route('machines.index', request()->query()) }}" class="btn btn-sm btn-light">Back to List</a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('machines.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="import_file" class="form-label">Excel File</label>
                        <input type="file" name="import_file" id="import_file" accept=".xlsx,.csv" class="form-control @error('import_file') is-invalid @enderror">
                        @error('import_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <p class="small text-muted mb-1">Upload a file with the following columns:</p>
                        <ul class="small mb-0">
                            <li>A - No.</li>
                            <li>B - Control No.</li>
                            <li>C - Asset Name</li>
                            <li>D - Brand</li>
                            <li>E - Model</li>
                            <li>F - Serial No.</li>
                            <li>G - Supplier</li>
                            <li>H - Arrival Date</li>
                            <li>I - Currency (MMK, USD, SGD, JPY, CNY)</li>
                            <li>J - Unit Price</li>
                            <li>K - Location</li>
                            <li>L - Remarks</li>
                        </ul>
                        <p class="small text-muted mb-0">Row 1 is a header row; data begins at row 2. All imported rows will use <strong>plant_id = 1</strong>.</p>
                    </div>

                    <button type="submit" class="btn btn-primary">Import</button>
                    <a href="{{ route('machines.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
