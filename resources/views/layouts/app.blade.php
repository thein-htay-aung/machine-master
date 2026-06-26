<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        .app-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        @media (min-width: 768px) {
            #sidebarMenu {
                position: sticky;
                top: 56px;
                height: calc(100vh - 56px);
                overflow-y: auto;
            }
        }

        @media (max-width: 767.98px) {
            #sidebarMenu {
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                width: 100%;
                height: calc(100vh - 56px);
                overflow-y: auto;
                z-index: 1050;
                background-color: #212529;
            }

            #sidebarMenu.show + #sidebarBackdrop,
            #sidebarMenu.collapsing + #sidebarBackdrop {
                display: block;
            }

            #sidebarMenu .sidebar-close-btn {
                display: block;
            }

            #sidebarBackdrop {
                position: fixed;
                inset: 56px 0 0 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1045;
                display: none;
            }

            body {
                padding-bottom: 0;
            }
        }

        .parts-toggle {
            position: relative;
        }

        .parts-toggle::after {
            content: '+';
            margin-left: auto;
        }

        .parts-toggle:not(.collapsed)::after {
            content: '-';
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <button class="btn btn-outline-light d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle sidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Machine Master') }} logo" class="app-logo me-2">
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
                <aside id="sidebarMenu" class="collapse d-md-block col-md-2 col-lg-2 bg-dark text-white border-end border-secondary" style="min-width:140px; z-index: 1050;">
                    <div class="p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 d-md-none">
                            <span class="text-white fw-semibold">Menu</span>
                            <button class="btn btn-sm btn-outline-light sidebar-close-btn" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="true" aria-label="Close sidebar">
                                &times;
                            </button>
                        </div>
                        {{-- <h6 class="text-uppercase text-white-50 mb-3">Menu</h6> --}}
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white-50' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('machines.*') ? 'active' : 'text-white-50' }}" href="{{ route('machines.index') }}">Machines</a>
                            </li>
                            <li class="nav-item mb-1">
                                @php
                                    $partsOpen = request()->routeIs('units.*')
                                        || request()->routeIs('categories.*')
                                        || request()->routeIs('parts.*')
                                        || request()->routeIs('purchases.*')
                                        || request()->routeIs('issues.*')
                                        || request()->routeIs('stock-adjustments.*')
                                        || request()->routeIs('stocks.*');
                                @endphp
                                <a class="nav-link text-white-50 parts-toggle {{ $partsOpen ? '' : 'collapsed' }} d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#partsSubmenu" role="button" aria-expanded="{{ $partsOpen ? 'true' : 'false' }}" aria-controls="partsSubmenu">
                                    <span>Parts</span>
                                </a>
                                <div class="collapse {{ $partsOpen ? 'show' : '' }}" id="partsSubmenu">
                                    <ul class="nav flex-column ms-3 mt-2">
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : 'text-white-50' }}" href="{{ route('units.index') }}">Unit Master</a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : 'text-white-50' }}" href="{{ route('categories.index') }}">Category Master</a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('parts.*') ? 'active' : 'text-white-50' }}" href="{{ route('parts.index') }}">Part Master</a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : 'text-white-50' }}" href="{{ route('purchases.index') }}">Purchase</a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('issues.*') ? 'active' : 'text-white-50' }}" href="{{ route('issues.index') }}">Issue</a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : 'text-white-50' }}" href="{{ route('stock-adjustments.index') }}">Stock Adjustment</a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : 'text-white-50' }}" href="{{ route('stocks.index') }}">Stock</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            @if(auth()->user()->isSuperAdmin())
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : 'text-white-50' }}" href="{{ route('users.index') }}">Users</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </aside>
                <div id="sidebarBackdrop" class="d-md-none" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-label="Close sidebar"></div>
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

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
