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
    <link href="{{ asset('assets/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        .app-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .sidebar-toggle-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            padding: 0;
            border-color: rgba(255, 255, 255, .25);
            line-height: 1;
        }

        .sidebar-toggle-btn:hover,
        .sidebar-toggle-btn:focus {
            background-color: rgba(255, 255, 255, .12);
            border-color: rgba(255, 255, 255, .45);
        }

        .sidebar-nav-icon {
            width: 1.25rem;
            display: inline-flex;
            justify-content: center;
            margin-right: .55rem;
            font-size: 1rem;
        }

        .sidebar-nav-text {
            min-width: 0;
        }

        #sidebarMenu .nav-link {
            display: flex;
            align-items: center;
            gap: .1rem;
            white-space: nowrap;
        }

        @media (min-width: 992px) {
            #sidebarMenu {
                position: sticky;
                top: 56px;
                height: calc(100vh - 56px);
                overflow-y: auto;
            }

            body.sidebar-hidden #sidebarMenu {
                display: none !important;
            }

            body.sidebar-hidden #appMain {
                flex: 0 0 auto;
                width: 100%;
            }
        }

        @media (max-width: 991.98px) {
            .navbar .container-fluid {
                padding-left: .75rem !important;
                padding-right: .75rem !important;
            }

            .navbar-brand {
                min-width: 0;
                max-width: calc(100vw - 132px);
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            #navbarUserMenu .d-flex {
                align-items: stretch !important;
                gap: .5rem !important;
                padding-top: .75rem;
            }

            #sidebarMenu {
                position: fixed;
                top: 56px;
                left: 0;
                width: min(82vw, 300px);
                max-width: 300px;
                height: calc(100vh - 56px);
                overflow-y: auto;
                z-index: 1050;
                background-color: #212529;
                box-shadow: 0 .75rem 2rem rgba(0, 0, 0, .35);
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

        @media (max-width: 575.98px) {
            .app-logo {
                width: 28px;
                height: 28px;
            }

            .navbar-brand {
                font-size: 1rem;
                max-width: calc(100vw - 124px);
            }

            main {
                padding-left: .25rem;
                padding-right: .25rem;
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
            @auth
                <button id="sidebarToggle" class="btn btn-outline-light sidebar-toggle-btn me-2" type="button" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Show side menu">
                    <i class="bi bi-list fs-5" aria-hidden="true"></i>
                </button>
            @endauth
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
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid px-0">
        <div class="row g-0">
            @auth
                <aside id="sidebarMenu" class="collapse d-lg-block col-lg-2 bg-dark text-white border-end border-secondary" style="min-width:140px; z-index: 1050;">
                    <div class="p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white fw-semibold">Menu</span>
                            <button class="btn btn-sm btn-outline-light sidebar-close-btn" type="button" aria-controls="sidebarMenu" aria-expanded="true" aria-label="Hide side menu">
                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                            </button>
                        </div>
                        {{-- <h6 class="text-uppercase text-white-50 mb-3">Menu</h6> --}}
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white-50' }}" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 sidebar-nav-icon" aria-hidden="true"></i>
                                    <span class="sidebar-nav-text">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('machines.*') ? 'active' : 'text-white-50' }}" href="{{ route('machines.index') }}">
                                    <i class="bi bi-gear-wide-connected sidebar-nav-icon" aria-hidden="true"></i>
                                    <span class="sidebar-nav-text">Machines</span>
                                </a>
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
                                    <span class="d-flex align-items-center min-w-0">
                                        <i class="bi bi-tools sidebar-nav-icon" aria-hidden="true"></i>
                                        <span class="sidebar-nav-text">Parts</span>
                                    </span>
                                </a>
                                <div class="collapse {{ $partsOpen ? 'show' : '' }}" id="partsSubmenu">
                                    <ul class="nav flex-column ms-3 mt-2">
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : 'text-white-50' }}" href="{{ route('units.index') }}">
                                                <i class="bi bi-rulers sidebar-nav-icon" aria-hidden="true"></i>
                                                <span class="sidebar-nav-text">Unit Master</span>
                                            </a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : 'text-white-50' }}" href="{{ route('categories.index') }}">
                                                <i class="bi bi-tags sidebar-nav-icon" aria-hidden="true"></i>
                                                <span class="sidebar-nav-text">Category Master</span>
                                            </a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('parts.*') ? 'active' : 'text-white-50' }}" href="{{ route('parts.index') }}">
                                                <i class="bi bi-nut sidebar-nav-icon" aria-hidden="true"></i>
                                                <span class="sidebar-nav-text">Part Master</span>
                                            </a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : 'text-white-50' }}" href="{{ route('purchases.index') }}">
                                                <i class="bi bi-cart-check sidebar-nav-icon" aria-hidden="true"></i>
                                                <span class="sidebar-nav-text">Purchase</span>
                                            </a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('issues.*') ? 'active' : 'text-white-50' }}" href="{{ route('issues.index') }}">
                                                <i class="bi bi-box-arrow-up sidebar-nav-icon" aria-hidden="true"></i>
                                                <span class="sidebar-nav-text">Issue</span>
                                            </a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : 'text-white-50' }}" href="{{ route('stock-adjustments.index') }}">
                                                <i class="bi bi-sliders sidebar-nav-icon" aria-hidden="true"></i>
                                                <span class="sidebar-nav-text">Stock Adjustment</span>
                                            </a>
                                        </li>
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : 'text-white-50' }}" href="{{ route('stocks.index') }}">
                                                <i class="bi bi-box-seam sidebar-nav-icon" aria-hidden="true"></i>
                                                <span class="sidebar-nav-text">Stock</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            @if(auth()->user()->isSuperAdmin())
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : 'text-white-50' }}" href="{{ route('users.index') }}">
                                        <i class="bi bi-people sidebar-nav-icon" aria-hidden="true"></i>
                                        <span class="sidebar-nav-text">Users</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </aside>
                <div id="sidebarBackdrop" class="d-lg-none" aria-controls="sidebarMenu" aria-label="Close sidebar"></div>
                <main id="appMain" class="col-12 col-lg-10 py-2">
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
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var sidebar = document.getElementById('sidebarMenu');
                var toggle = document.getElementById('sidebarToggle');
                var closeButtons = document.querySelectorAll('.sidebar-close-btn, #sidebarBackdrop');
                var desktopQuery = window.matchMedia('(min-width: 992px)');

                if (!sidebar || !toggle) {
                    return;
                }

                var sidebarCollapse = bootstrap.Collapse.getOrCreateInstance(sidebar, { toggle: false });

                function isDesktop() {
                    return desktopQuery.matches;
                }

                function setToggleState(isOpen) {
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    toggle.setAttribute('aria-label', isOpen ? 'Hide side menu' : 'Show side menu');
                    toggle.querySelector('i').className = isOpen ? 'bi bi-x-lg small' : 'bi bi-list fs-5';
                }

                function syncForViewport() {
                    if (isDesktop()) {
                        sidebar.classList.add('show');
                        document.body.classList.remove('sidebar-hidden');
                        setToggleState(true);
                    } else {
                        sidebar.classList.remove('show');
                        document.body.classList.remove('sidebar-hidden');
                        setToggleState(false);
                    }
                }

                toggle.addEventListener('click', function () {
                    if (isDesktop()) {
                        var willHide = !document.body.classList.contains('sidebar-hidden');
                        document.body.classList.toggle('sidebar-hidden', willHide);
                        setToggleState(!willHide);
                    } else {
                        sidebarCollapse.toggle();
                    }
                });

                closeButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        if (isDesktop()) {
                            document.body.classList.add('sidebar-hidden');
                            setToggleState(false);
                        } else {
                            sidebarCollapse.hide();
                        }
                    });
                });

                sidebar.addEventListener('shown.bs.collapse', function () {
                    setToggleState(true);
                });

                sidebar.addEventListener('hidden.bs.collapse', function () {
                    if (!isDesktop()) {
                        setToggleState(false);
                    }
                });

                if (desktopQuery.addEventListener) {
                    desktopQuery.addEventListener('change', syncForViewport);
                } else {
                    desktopQuery.addListener(syncForViewport);
                }

                syncForViewport();
            });
        </script>
    @endauth
    @stack('scripts')
</body>
</html>
