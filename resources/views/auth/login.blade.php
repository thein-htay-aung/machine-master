@extends('layouts.auth')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mx-auto">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Login</h5>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" name="password" class="form-control" required>
                            </div>

                            <div class="form-check mb-3">
                                <input id="remember" type="checkbox" name="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">Remember me</label>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
@endsection
