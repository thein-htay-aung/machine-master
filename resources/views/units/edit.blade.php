@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Unit</h5>
                <a href="{{ route('units.index') }}" class="btn btn-sm btn-light">Back to List</a>
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

                <form action="{{ route('units.update', $unit->id) }}{{ request()->getQueryString() ? ('?' . request()->getQueryString()) : '' }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Unit Name</label>
                        <input type="text" name="name" value="{{ old('name', $unit->name) }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Plant</label>
                        <select name="plant_id" class="form-select @error('plant_id') is-invalid @enderror">
                            @if($plants->count() > 1)
                                <option value="">Select Plant</option>
                            @endif
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}" {{ old('plant_id', $defaultPlantId) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                            @endforeach
                        </select>
                        @error('plant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Unit</button>
                        <a href="{{ route('units.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
