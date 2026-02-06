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

        /* Classic DataTable Button Styling */
        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-button {
            background: #fff !important;
            border: 2px solid #2c3e50 !important;
            color: #2c3e50 !important;
            padding: 8px 16px !important;
            margin-right: 6px !important;
            border-radius: 4px !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            transition: all 0.2s ease !important;
            box-shadow: none !important;
        }

        .dt-button:hover {
            background: #2c3e50 !important;
            color: #fff !important;
            border-color: #2c3e50 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(44, 62, 80, 0.2) !important;
        }

        .dt-button:active,
        .dt-button:focus {
            background: #1a252f !important;
            color: #fff !important;
            outline: none !important;
            box-shadow: 0 1px 2px rgba(44, 62, 80, 0.3) !important;
        }

        /* Column visibility dropdown */
        .dt-button-collection {
            border: 2px solid #2c3e50 !important;
            border-radius: 4px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
        }

        .dt-button-collection .dt-button {
            border: none !important;
            border-radius: 0 !important;
            margin: 0 !important;
            text-align: left !important;
        }

        .dt-button-collection .dt-button:hover {
            background: #f8f9fa !important;
            color: #2c3e50 !important;
        }

        .dt-button-collection .dt-button.active {
            background: #e9ecef !important;
            color: #2c3e50 !important;
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
                    <div class="hidden md:block text-sm px-3 py-1.5 rounded-lg border-2 shadow-sm" style="border-color: #c5a059 !important; color: #c5a059 !important; background: rgba(197, 160, 89, 0.1);">
                        <i class="bi bi-calendar3 me-2" style="color: #c5a059 !important;"></i>
                        <span class="fw-bold">{{ now()->format('D, M d Y') }}</span>
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

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="d-flex align-items-center flex-nowrap gap-3 text-white btn btn-link text-decoration-none border-0 p-1 px-2 rounded-pill hover:bg-white hover:bg-opacity-10 transition-all duration-300" data-bs-toggle="dropdown" style="display: flex !important; flex-direction: row !important; align-items: center !important;">
                            <!-- Name (Left) -->
                            <span class="hidden md:inline text-sm font-bold tracking-wide truncate" style="max-width: 150px; text-decoration: none !important; margin-right: 0.5rem;">{{ auth()->user()->username ?? 'Admin' }}</span>

                            <!-- Profile Image (Absolute Right/End) -->
                            <div class="position-relative" style="order: 2;">
                                <div class="w-10 h-10 rounded-full bg-slate-700 d-flex align-items-center justify-center border-2 border-slate-500 overflow-hidden shadow-lg shrink-0" style="width: 40px; height: 40px;">
                                    @if (auth()->user() && auth()->user()->profile_image)
                                    <img src="{{ asset(auth()->user()->profile_image) }}" class="w-full h-full object-cover">
                                    @else
                                    <i class="bi bi-person text-white"></i>
                                    @endif
                                </div>
                                <div class="position-absolute bottom-0 end-0 bg-success border border-white rounded-full" style="width: 10px; height: 10px; bottom: 2px; right: 2px;"></div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 rounded-xl mt-3">
                            <li class="px-4 py-2 bg-light border-bottom mb-2">
                                <div class="small text-muted fw-bold uppercase tracking-wider" style="font-size: 0.65rem;">Signed in as</div>
                                <div class="text-dark fw-bold">{{ auth()->user()->full_name }}</div>
                            </li>
                            <li><a class="dropdown-item py-2 px-4 flex items-center gap-2 edit-self" href="javascript:void(0)" data-id="{{ auth()->id() }}"><i class="bi bi-person text-slate-400"></i> My Profile</a></li>
                            @if(auth()->user() && auth()->user()->role === 'admin')
                            <li><a class="dropdown-item py-2 px-4 flex items-center gap-2" href="{{ route('business.index') }}"><i class="bi bi-gear text-slate-400"></i> Settings</a></li>
                            @endif
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

            <!-- GLOBAL EDIT USER MODAL -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow">
                        <!-- Profile View Section -->
                        <div id="profileViewSection">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold" id="editUserModalLabel">User Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" data-mdb-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-4 text-center">
                                <img id="view_avatar" src="" alt="Profile" class="rounded-circle border shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                <h4 id="view_username" class="fw-bold mb-1 text-dark"></h4>
                                <p id="view_email" class="text-muted mb-3"></p>
                                <span id="view_role" class="badge rounded-pill bg-primary px-3 py-2 mb-4"></span>

                                <div class="d-grid gap-2 px-4">
                                    <button type="button" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm" id="switchToEditBtn">
                                        <i class="bi bi-pencil-square me-2"></i> Edit Profile
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Edit Section (Hidden by default) -->
                        <div id="profileEditSection" style="display: none;">
                            <form id="editUserForm" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="edit_user_id" name="id">
                                <input type="hidden" id="edit_current_role">
                                <div class="modal-header border-0 pb-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-link text-dark p-0" id="switchToViewBtn">
                                            <i class="bi bi-arrow-left fs-5"></i>
                                        </button>
                                        <h5 class="modal-title fw-bold">Edit Profile</h5>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <div class="mb-3 text-center">
                                        <div class="position-relative d-inline-block">
                                            <img id="edit_avatar_preview" src="" alt="Preview" class="rounded-circle border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                                            <label for="edit_profile_image" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-camera-fill" style="font-size: 0.8rem;"></i>
                                            </label>
                                        </div>
                                        <input type="file" id="edit_profile_image" name="profile_image" class="d-none" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Username</label>
                                        <input type="text" id="edit_username" name="username" class="form-control rounded-3" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Email</label>
                                        <input type="email" id="edit_email" name="email" class="form-control rounded-3" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Password <small class="text-muted text-xs ms-1">(leave blank to keep current)</small></label>
                                        <input type="password" id="edit_password" name="password" class="form-control rounded-3" placeholder="••••••••">
                                    </div>

                                    @if(auth()->user() && auth()->user()->role === 'admin')
                                    <div class="mb-3" id="role_selection_container">
                                        <label class="form-label fw-bold small text-muted">Role</label>
                                        <select id="edit_role" name="role" class="form-select rounded-3">
                                            <option value="admin">Admin</option>
                                            <option value="staff">Staff</option>
                                            <option value="user">User</option>
                                        </select>
                                    </div>
                                    @else
                                    <input type="hidden" id="edit_role" name="role" value="{{ auth()->user()->role ?? 'user' }}">
                                    @endif
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" id="cancelEditBtn">Back</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" id="editUserSubmitBtn">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GLOBAL CONFIRM MODAL -->
            <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                        <!-- Modal Header with Gradient -->
                        <div class="modal-header bg-white border-bottom" style="border-bottom: 2px solid #2c3e50 !important; padding: 1.5rem 2rem;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #2c3e50; border-radius: 50%;">
                                    <i class="bi bi-exclamation-circle text-white" style="font-size: 1.25rem;"></i>
                                </div>
                                <h5 class="modal-title fw-bold mb-0 text-dark" id="globalConfirmModalLabel" style="font-size: 1.125rem; letter-spacing: 0.5px;">CONFIRMATION</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" data-mdb-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body bg-white p-4">
                            <p class="mb-0 text-secondary" id="globalConfirmModalBody" style="font-size: 0.95rem; line-height: 1.7; font-weight: 400;">
                                Are you sure you want to proceed?
                            </p>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer bg-light border-top p-3" style="border-top: 1px solid #dee2e6 !important;">
                            <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal" data-mdb-dismiss="modal" style="border-radius: 4px; border-width: 2px;">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-dark px-4 fw-medium" id="globalConfirmModalConfirmBtn" style="background: #2c3e50; border: none; border-radius: 4px;">
                                Proceed
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

        // Global User Edit Logic
        $(document).on('click', '.edit-user, .edit-self', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const userId = $btn.data('id');
            const isSelfLink = $btn.hasClass('edit-self');
            const isSelfId = userId == "{{ auth()->id() }}";

            // IF clicking "My Profile" (edit-self), show View Section first
            // ELSE (clicking edit-user in list), go DIRECTLY to Edit Section
            if (isSelfLink) {
                $('#profileViewSection').show();
                $('#profileEditSection').hide();
            } else {
                $('#profileViewSection').hide();
                $('#profileEditSection').show();
            }

            if (isSelfId && "{{ auth()->user()->role }}" !== 'admin') {
                $('#role_selection_container').addClass('d-none');
            } else {
                $('#role_selection_container').removeClass('d-none');
            }

            $.ajax({
                url: `/users/${userId}/edit`,
                method: 'GET',
                success: function(res) {
                    if (res.success) {
                        // Populate View Section
                        $('#view_username').text(res.user.username);
                        $('#view_email').text(res.user.email);
                        $('#view_role').text(res.user.role.charAt(0).toUpperCase() + res.user.role.slice(1));

                        // Populate Edit Section fields
                        $('#edit_user_id').val(res.user.id);
                        $('#edit_username').val(res.user.username);
                        $('#edit_email').val(res.user.email);
                        if ($('#edit_role').length) $('#edit_role').val(res.user.role);
                        $('#edit_password').val('');

                        const avatarUrl = res.user.profile_image ?
                            '{{ asset("") }}' + res.user.profile_image :
                            '{{ asset("images/default-avatar.png") }}';

                        $('#view_avatar').attr('src', avatarUrl);
                        $('#edit_avatar_preview').attr('src', avatarUrl);

                        $('#editUserModal').modal('show');
                    }
                }
            });
        });

        // Toggle Buttons
        $('#switchToEditBtn').on('click', function() {
            $('#profileViewSection').hide();
            $('#profileEditSection').fadeIn();
        });

        $('#switchToViewBtn, #cancelEditBtn').on('click', function() {
            $('#profileEditSection').hide();
            $('#profileViewSection').fadeIn();
        });

        $('#edit_profile_image').on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#edit_avatar_preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            const userId = $('#edit_user_id').val();
            const $btn = $('#editUserSubmitBtn');
            const originalText = $btn.text();

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');

            $.ajax({
                url: `/users/${userId}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.msg);
                        $('#editUserModal').modal('hide');
                        if (typeof table !== 'undefined' && table.ajax) table.ajax.reload();
                        if (userId == "{{ auth()->id() }}") {
                            setTimeout(() => location.reload(), 1000);
                        }
                    } else {
                        toastr.error(res.msg);
                    }
                    $btn.prop('disabled', false).text(originalText);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.msg || 'Error updating profile');
                    $btn.prop('disabled', false).text(originalText);
                }
            });
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