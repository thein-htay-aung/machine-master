<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <button class="btn btn-outline-light d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle sidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">
                {{ config('app.name', 'Machine Master') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUserMenu" aria-controls="navbarUserMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarUserMenu">
                @auth
                    <div class="d-flex ms-auto align-items-center gap-3">
                        <span class="text-white-50 small">{{ auth()->user()->name }}</span>
                        <a href="{{ route('account.password.edit') }}" class="btn btn-outline-light btn-sm">Change Password</a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid px-0">
        <div class="row g-0">
            @auth
                <aside id="sidebarMenu" class="collapse d-md-block col-md-2 col-lg-2 bg-dark text-white border-end border-secondary min-vh-100" style="min-width:140px;">
                    <div class="p-2">
                        {{-- <h6 class="text-uppercase text-white-50 mb-3">Menu</h6> --}}
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white-50' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('machines.*') ? 'active' : 'text-white-50' }}" href="{{ route('machines.index') }}">Machines</a>
                            </li>
                            @if(auth()->user()->isSuperAdmin())
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : 'text-white-50' }}" href="{{ route('users.index') }}">Users</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </aside>
                <main class="col-12 col-md-10 col-lg-10 py-2">
                    @yield('content')
                </main>
            @else
                <main class="col-12 py-4">
                    @yield('content')
                </main>
            @endauth
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
