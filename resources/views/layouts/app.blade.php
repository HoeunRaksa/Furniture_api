<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ session('business.name', 'Business Name') }} | @yield('title', 'Admin Panel')</title>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
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
        /* Modern Layout Styles */
        .navbar {
            animation: slideDown 0.4s ease-out forwards;
            background: linear-gradient(90deg, #1e293b, #0f172a);
            z-index: 1040;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .navbar .dropdown-menu {
            animation: dropdownFadeIn 0.2s ease-out;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border-radius: 12px;
            overflow: hidden;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .navbar .dropdown-item {
            transition: all 0.2s ease;
            padding: 10px 20px;
        }

        .navbar .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            padding-left: 25px;
        }

        /* Notification badge animation */
        .navbar .badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Sidebar Wrapper Fixes */
        .sidebar-wrapper {
            transition: all 0.3s ease-in-out;
            z-index: 1030;
            width: 260px;
        }

        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
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

        /* Custom Audio Fix */
        #notification-audio, #success-audio, #error-audio {
            display: none;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans" x-data="{ mobileSidebarOpen: false }">

    <div class="flex flex-col w-full min-h-screen">

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg px-4 py-3 shadow-lg sticky-top">
            <div class="container-fluid flex justify-between items-center">

                <!-- LEFT: Toggle + Logo -->
                <div class="flex items-center gap-4">
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="text-white lg:hidden btn btn-link p-0">
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

                <!-- RIGHT: Metrics + Notifications + User -->
                <div class="flex items-center gap-4">
                    
                    <!-- Date Badge -->
                    <div class="hidden md:block text-white text-opacity-75 text-sm px-3 py-1.5 rounded-lg border border-white border-opacity-10 bg-white bg-opacity-5">
                        {{ now()->format('D, M d Y') }}
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown" id="notification-dropdown">
                        <button class="btn btn-sm btn-link text-white p-0 position-relative no-underline" data-bs-toggle="dropdown">
                            <i class="bi bi-bell text-2xl"></i>
                            <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-full bg-danger border border-white d-none" style="font-size: 0.6rem; padding: 0.35em 0.5em;">
                                0
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 rounded-2xl mt-3 p-0" style="min-width: 320px; max-height: 480px; overflow: hidden;">
                            <li class="bg-light px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Recent Activity</span>
                                <span class="badge bg-primary rounded-pill">Orders</span>
                            </li>
                            <div id="notification-items" style="max-height: 380px; overflow-y: auto;">
                                <!-- Polled items will appear here -->
                                <li class="text-center py-5">
                                    <div class="spinner-border spinner-border-sm text-primary opacity-50 mb-2"></div>
                                    <div class="small text-muted">Checking for orders...</div>
                                </li>
                            </div>
                            <li class="p-2 border-top bg-light">
                                <a href="{{ route('orders.index') }}" class="dropdown-item text-center text-primary fw-bold rounded-pill">
                                    Browse All Orders
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Audio Elements -->
                    <audio id="notification-audio" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
                    <audio id="success-audio" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
                    <audio id="error-audio" src="{{ asset('sounds/error.mp3') }}" preload="auto"></audio>

                    <!-- User Profile -->
                    <div class="dropdown">
                        <button class="flex items-center gap-2 text-white btn btn-link no-underline p-0" data-bs-toggle="dropdown">
                            <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center border border-slate-600 overflow-hidden shadow-inner">
                                @if (auth()->user() && auth()->user()->profile_image)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="bi bi-person text-white"></i>
                                @endif
                            </div>
                            <span class="hidden md:inline text-sm font-semibold tracking-wide">{{ auth()->user()->username ?? 'Admin' }}</span>
                            <i class="bi bi-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 rounded-xl mt-3">
                            <li class="px-4 py-2 bg-light border-bottom mb-2">
                                <div class="small text-muted fw-bold">SIGNED IN AS</div>
                                <div class="text-dark fw-bold">{{ auth()->user()->full_name }}</div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('users.index') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('business.index') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2 px-4 flex items-center gap-2 font-semibold">
                                        <i class="bi bi-box-arrow-right"></i> Sign Out
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
            <div class="sidebar-wrapper lg:block" :class="mobileSidebarOpen ? 'show' : ''">
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
        // Sound & Toastr Bridge
        function playAudio(id) {
            const audio = document.getElementById(id);
            if (audio) {
                audio.pause();
                audio.currentTime = 0;
                audio.play().catch(e => console.log('Audio playback prevented'));
            }
        }

        // Bridge toastr with sounds
        (function() {
            const wrap = (original, sound) => (...args) => {
                playAudio(sound);
                original.apply(toastr, args);
            };
            toastr.success = wrap(toastr.success, 'success-audio');
            toastr.error = wrap(toastr.error, 'error-audio');
            toastr.warning = wrap(toastr.warning, 'error-audio');
            toastr.info = wrap(toastr.info, 'success-audio');
        })();
    </script>

    @if(session('success'))
    <script>
        $(document).ready(function() { toastr.success("{{ session('success') }}"); });
    </script>
    @endif

    @if(session('error'))
    <script>
        $(document).ready(function() { toastr.error("{{ session('error') }}"); });
    </script>
    @endif

    @if($errors->any())
    <script>
        $(document).ready(function() {
            @foreach($errors->all() as $error) toastr.error("{{ $error }}"); @endforeach
        });
    </script>
    @endif

    <script src="{{ asset('js/notifications.js') }}"></script>
    @include('includes.js')
    @stack('scripts')
</body>
</html>
