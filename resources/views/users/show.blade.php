@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Details</h5>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-light">Back to List</a>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="badge bg-{{ $user->hasVerifiedEmail() ? 'success' : 'warning' }}">
                            {{ $user->hasVerifiedEmail() ? 'Verified' : 'Not Verified' }}
                        </span>
                        <span class="badge bg-{{ $user->isEnabled() ? 'success' : 'danger' }} ms-2">
                            {{ $user->isEnabled() ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div>
                        @if(!$user->isSuperAdmin())
                            <form action="{{ route('users.sendEmail', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Send Email</button>
                            </form>
                            @if($user->isEnabled())
                                <form action="{{ route('users.disable', $user->id) }}" method="POST" onsubmit="return confirm('Disable this user?');" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Disable</button>
                                </form>
                            @else
                                <form action="{{ route('users.enable', $user->id) }}" method="POST" onsubmit="return confirm('Enable this user?');" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Enable</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Name</strong>
                    <p>{{ $user->name }}</p>
                </div>
                <div class="mb-3">
                    <strong>Email</strong>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="mb-3">
                    <strong>Role</strong>
                    <p>{{ $user->role->name }}</p>
                </div>
                <div class="mb-3">
                    <strong>Department</strong>
                    <p>{{ $user->department?->name ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <strong>Created At</strong>
                    <p>{{ $user->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
