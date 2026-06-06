@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Users</h5>
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-light">+ Add New User</a>
            </div>

            <div class="card-body">

                @if (session("success"))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Department</th>
                                <th scope="col" class="text-center">Verified</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center align-middle">{{ $users->firstItem() + $loop->index }}</td>
                                    <td class="align-middle">{{ $user->name }}</td>
                                    <td class="align-middle">{{ $user->email }}</td>
                                    <td class="align-middle">{{ $user->role->name }}</td>
                                    <td class="align-middle">{{ $user->department?->name ?? '-' }}</td>
                                    <td class="text-center align-middle">
                                        @if($user->hasVerifiedEmail())
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-warning">No</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($user->isEnabled())
                                            <span class="badge bg-success">Enabled</span>
                                        @else
                                            <span class="badge bg-danger">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-warning">Show</a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info">Edit</a>
                                            @if(!$user->isSuperAdmin())
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user?');" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No users found.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>

            
        </div>

    </div>
    
@endsection
