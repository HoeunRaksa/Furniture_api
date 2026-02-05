<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Furniture Admin | @yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- MDB UI Kit -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Alpine -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>

    @stack('styles')

    <style>
        .navbar {
            background: linear-gradient(90deg, #1e293b, #0f172a);
        }
        .sidebar-wrapper {
            width: 260px;
            transition: all 0.3s;
        }
        .page-content {
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="bg-light" x-data="{ mobileSidebarOpen: false }">

    <div class="d-flex flex-column w-100 min-vh-100">

        <nav class="navbar navbar-expand-lg px-4 py-3 shadow-sm sticky-top">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="btn btn-link text-white p-0 me-3 d-lg-none">
                        <i class="bi bi-list fs-2"></i>
                    </button>
                    <a href="{{ route('home') }}" class="text-white fw-bold fs-5 mb-0 text-decoration-none">
                        Furniture Admin
                    </a>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-transparent d-flex align-items-center gap-2 text-white" data-bs-toggle="dropdown">
                            <span class="small">{{ auth()->user()->name ?? 'Guest' }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="d-flex flex-grow-1 position-relative overflow-hidden">
            <div class="sidebar-wrapper" :class="mobileSidebarOpen ? '' : 'd-none d-lg-block'">
                @include('layouts.sidebar')
            </div>

            <main class="flex-grow-1 page-content p-4 overflow-auto">
                @yield('content')
            </main>
        </div>

    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    @include('includes.js')
    @stack('scripts')
</body>
</html>
