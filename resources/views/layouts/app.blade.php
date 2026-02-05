<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ session('business.name', 'Furniture Admin') }} | @yield('title', 'Admin Panel')</title>
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        
        .navbar {
            z-index: 1040;
            background: linear-gradient(90deg, #1e293b, #0f172a);
        }

        .sidebar-wrapper {
            transition: all 0.3s ease-in-out;
            z-index: 1030;
        }

        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 280px;
                transform: translateX(-100%);
            }
            .sidebar-wrapper.show {
                transform: translateX(0);
            }
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(4px);
                z-index: 1025;
            }
        }
        
        .page-content {
            background-color: #f1f5f9;
            min-height: calc(100vh - 72px);
        }
    </style>
</head>

<body class="bg-slate-50 font-sans" x-data="{ mobileSidebarOpen: false }">

    <div class="flex flex-col w-full min-h-screen">

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg px-4 py-3 shadow-lg sticky-top">
            <div class="container-fluid flex justify-between items-center">

                <div class="flex items-center gap-4">
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="text-white lg:hidden">
                        <i class="bi bi-list text-3xl"></i>
                    </button>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center overflow-hidden">
                            @if(session('business.logo'))
                                <img src="{{ session('business.logo') }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <i class="bi bi-shop text-slate-800 text-xl"></i>
                            @endif
                        </div>
                        <a href="{{ route('home') }}" class="text-white font-bold text-xl no-underline tracking-tight">
                            {{ session('business.name', 'Furniture Admin') }}
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="dropdown">
                        <button class="flex items-center gap-2 text-white btn btn-link no-underline p-0" data-bs-toggle="dropdown">
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center border border-slate-600">
                                <i class="bi bi-person text-white"></i>
                            </div>
                            <span class="hidden md:inline text-sm font-medium">{{ auth()->user()->username ?? 'Admin' }}</span>
                            <i class="bi bi-chevron-down text-[10px]"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 rounded-xl mt-2">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2 px-4 flex items-center gap-2 font-medium">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex flex-grow relative overflow-hidden">
            
            <!-- Mobile Overlay -->
            <div x-show="mobileSidebarOpen" x-cloak @click="mobileSidebarOpen = false" class="mobile-overlay lg:hidden"></div>

            <!-- Sidebar -->
            <div class="sidebar-wrapper w-64 lg:block" :class="mobileSidebarOpen ? 'show' : ''">
                @include('layouts.sidebar')
            </div>

            <!-- Page Content -->
            <main class="flex-grow page-content p-6 overflow-y-auto">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
        </div>

    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        $(document).ready(function() {
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>

    @include('includes.js')
    @stack('scripts')
</body>
</html>
