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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-primary: 'Lato', sans-serif;
            --font-heading: 'Playfair Display', serif;
            --color-dark: #121212;
            /* Deep Charcoal */
            --color-gold: #c5a059;
            /* Muted Gold */
            --color-gold-hover: #b08d4b;
            --color-light: #f8f9fa;
        }

        body {
            font-family: var(--font-primary);
            background-color: #fcfcfc;
            color: #333;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand,
        .font-heading {
            font-family: var(--font-heading);
            letter-spacing: -0.01em;
        }

        /* Navbar Redesign */
        .navbar {
            background-color: var(--color-dark) !important;
            background: linear-gradient(180deg, #1a1a1a 0%, #121212 100%);
            border-bottom: 3px solid var(--color-gold);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        }

        .navbar .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Sidebar */
        .sidebar-wrapper {
            background: #fff;
            border-right: 1px solid #eaeaea;
        }

        /* Refined Interactions */
        .btn {
            border-radius: 2px !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--color-dark);
            border-color: var(--color-dark);
        }

        .btn-primary:hover {
            background-color: var(--color-gold);
            border-color: var(--color-gold);
            color: #121212;
        }

        .dropdown-menu {
            border-radius: 0 !important;
            border: 1px solid #eee;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }

        /* Inputs */
        .form-control,
        .form-select {
            border-radius: 2px !important;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--color-gold);
            box-shadow: none;
        }

        /* Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Audio Fix */
        #notification-audio,
        #success-audio,
        #error-audio {
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
                            <li>
                                <hr class="dropdown-divider">
                            </li>
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

            <!-- GLOBAL CONFIRM MODAL -->
            <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                        <!-- Modal Header with Gradient -->
                        <div class="modal-header text-white position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 1.75rem 2rem;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                                    <i class="bi bi-question-circle-fill" style="font-size: 1.5rem;"></i>
                                </div>
                                <h5 class="modal-title fw-bold mb-0" id="globalConfirmModalLabel" style="font-size: 1.25rem; letter-spacing: -0.5px;">Confirm Action</h5>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.8;"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body p-4" style="background: #f8f9fa;">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 text-dark" id="globalConfirmModalBody" style="font-size: 1rem; line-height: 1.6;">
                                        Are you sure you want to proceed?
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer border-0 bg-white p-4 gap-2" style="justify-content: flex-end;">
                            <button type="button" class="btn btn-light px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="border-radius: 10px; border: 1px solid #dee2e6;">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancel
                            </button>
                            <button type="button" class="btn text-white px-4 py-2 fw-semibold shadow-sm" id="globalConfirmModalConfirmBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px;">
                                <i class="bi bi-check-circle me-1"></i>
                                Yes, Proceed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

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

        // Global Confirm Modal Logic
        let confirmCallback = null;
        const globalConfirmModalEl = document.getElementById('globalConfirmModal');
        let globalConfirmModal = null;

        function getGlobalConfirmModal() {
            if (!globalConfirmModal && globalConfirmModalEl) {
                globalConfirmModal = new bootstrap.Modal(globalConfirmModalEl);
            }
            return globalConfirmModal;
        }

        window.showConfirmModal = function(message = "Are you sure?", onConfirm = null) {
            document.getElementById('globalConfirmModalBody').innerHTML = message;
            confirmCallback = onConfirm;
            const modal = getGlobalConfirmModal();
            if (modal) modal.show();
        };

        document.getElementById('globalConfirmModalConfirmBtn')?.addEventListener('click', function() {
            const modal = getGlobalConfirmModal();
            if (modal) modal.hide();
            if (typeof confirmCallback === "function") {
                confirmCallback();
            }
            confirmCallback = null;
        });

        // Logout Confirmation
        $(document).on('submit', '.logout-form', function(e) {
            e.preventDefault();
            const form = this;
            showConfirmModal("Are you sure you want to logout?", () => form.submit());
        });

        // Modal Backdrop Cleanup
        $(document).on('hidden.bs.modal', '.modal', function() {
            if ($('.modal:visible').length === 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'overflow': '',
                    'padding-right': ''
                });
            }
        });
    </script>

    <!-- Flash Messages (Data Attributes) -->
    <div id="flash-messages"
        data-success="{{ session('success') }}"
        data-error="{{ session('error') }}"
        data-errors='@json($errors->all())'>
    </div>

    <script>
        $(document).ready(function() {
            const flash = $('#flash-messages');
            const success = flash.data('success');
            const error = flash.data('error');
            const errors = flash.data('errors');

            if (success) toastr.success(success);
            if (error) toastr.error(error);
            if (errors && errors.length > 0) {
                errors.forEach(err => toastr.error(err));
            }
        });
    </script>

    <script src="{{ asset('js/notifications.js') }}"></script>
    @stack('scripts')
</body>

</html>