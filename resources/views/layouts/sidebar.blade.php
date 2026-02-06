<aside class="w-full h-full bg-white shadow-xl rounded-r-2xl py-6 sidebar-animate flex flex-col border-r border-slate-100 font-sans">

    <nav class="px-3 space-y-2 flex-grow overflow-y-auto">

        <!-- Home -->
        <a href="{{ route('home') }}"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 ease-in-out
           {{ request()->routeIs('home')
               ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm ring-1 ring-blue-100'
               : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-grid-1x2 text-xl transition-transform group-hover:scale-110 {{ request()->routeIs('home') ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-500' }}"></i>
            <span class="tracking-wide text-sm">Dashboard</span>
            @if (request()->routeIs('home'))
            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-500 shadow-sm"></span>
            @endif
        </a>

        <!-- Section Label -->
        <div class="px-4 mt-8 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-none">Management</div>

        <!-- Inventory Dropdown -->
        @php
        $inventoryActive =
        request()->routeIs('products.*') ||
        request()->routeIs('categories.*') ||
        request()->routeIs('attributes.*');
        @endphp

        <div x-data="{ open: {{ $inventoryActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group
                {{ $inventoryActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <i class="bi bi-box-seam text-xl transition-transform group-hover:scale-110 {{ $inventoryActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}"></i>
                    <span class="tracking-wide text-sm">Inventory</span>
                </div>
                <i class="bi bi-chevron-down text-[10px] transition-transform duration-300"
                    :class="open ? 'rotate-180 text-indigo-500' : 'text-slate-400'"></i>
            </button>

            <div x-show="open" x-collapse
                class="mt-1 space-y-1 pl-4 ml-6 border-l-2 border-slate-100">

                <a href="{{ route('products.index') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-colors relative
                   {{ request()->routeIs('products.index') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                    Products
                </a>
                <a href="{{ route('products.create') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-colors relative
                   {{ request()->routeIs('products.create') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                    Add Product
                </a>
                <a href="{{ route('attributes.index') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-colors relative
                   {{ request()->routeIs('attributes.index') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                    Attributes
                </a>
                <a href="{{ route('categories.index') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-colors relative
                   {{ request()->routeIs('categories.index') ? 'text-indigo-600 font-medium bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                    Categories
                </a>
            </div>
        </div>

        <!-- Sales Orders -->
        @php
        $salesActive = request()->routeIs('orders.*');
        @endphp

        <div x-data="{ open: {{ $salesActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group
                {{ $salesActive ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm ring-1 ring-emerald-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <i class="bi bi-cart3 text-xl transition-transform group-hover:scale-110 {{ $salesActive ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-500' }}"></i>
                    <span class="tracking-wide text-sm">Sales & Orders</span>
                </div>
                <i class="bi bi-chevron-down text-[10px] transition-transform duration-300"
                    :class="open ? 'rotate-180 text-emerald-500' : 'text-slate-400'"></i>
            </button>

            <div x-show="open" x-collapse
                class="mt-1 space-y-1 pl-4 ml-6 border-l-2 border-slate-100">
                <a href="{{ route('orders.index') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-colors relative
                   {{ request()->routeIs('orders.index') ? 'text-emerald-600 font-medium bg-emerald-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                    All Orders
                </a>
            </div>
        </div>

        <!-- User Management -->
        @if(auth()->user() && auth()->user()->role === 'admin')
        @php
        $userActive = request()->routeIs('users.*');
        @endphp

        <div x-data="{ open: {{ $userActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group
                {{ $userActive ? 'bg-purple-50 text-purple-700 font-semibold shadow-sm ring-1 ring-purple-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <i class="bi bi-people text-xl transition-transform group-hover:scale-110 {{ $userActive ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-500' }}"></i>
                    <span class="tracking-wide text-sm">Users</span>
                </div>
                <i class="bi bi-chevron-down text-[10px] transition-transform duration-300"
                    :class="open ? 'rotate-180 text-purple-500' : 'text-slate-400'"></i>
            </button>

            <div x-show="open" x-collapse
                class="mt-1 space-y-1 pl-4 ml-6 border-l-2 border-slate-100">
                <a href="{{ route('users.index') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-colors relative
                   {{ request()->routeIs('users.index') ? 'text-purple-600 font-medium bg-purple-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                    User List
                </a>
            </div>
        </div>
        @endif


        @if(auth()->user() && auth()->user()->role === 'admin')
        <!-- Section Label -->
        <div class="px-4 mt-8 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-none">Configuration</div>

        <a href="{{ route('business.index') }}"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 ease-in-out
           {{ request()->routeIs('business.index')
               ? 'bg-amber-50 text-amber-700 font-semibold shadow-sm ring-1 ring-amber-100'
               : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-gear text-xl transition-transform group-hover:rotate-90 {{ request()->routeIs('business.index') ? 'text-amber-600' : 'text-slate-400 group-hover:text-amber-500' }}"></i>
            <span class="tracking-wide text-sm">Business Settings</span>
        </a>
        @endif

    </nav>

    <!-- User Profile at Bottom -->
    <div class="mt-auto px-3 pt-6 border-t border-slate-100">
        <div class="dropdown">
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 hover:bg-slate-50 group no-underline text-left" data-bs-toggle="dropdown">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 overflow-hidden shadow-inner shrink-0">
                    @if (auth()->user() && auth()->user()->profile_image)
                    <img src="{{ asset(auth()->user()->profile_image) }}" class="w-full h-full object-cover">
                    @else
                    <i class="bi bi-person text-slate-500"></i>
                    @endif
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->username ?? 'Admin' }}</span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ auth()->user()->role ?? 'Staff' }}</span>
                </div>
                <i class="bi bi-chevron-up ms-auto text-[10px] text-slate-400 transition-transform group-hover:text-slate-600"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark shadow-xl border-0 rounded-xl mb-2 w-[calc(100%-1.5rem)] ml-3">
                <li class="px-4 py-2 bg-slate-800 border-bottom border-slate-700 mb-2 rounded-t-xl">
                    <div class="text-[10px] text-slate-400 fw-bold uppercase tracking-wider">Signed in as</div>
                    <div class="text-white text-sm fw-bold truncate">{{ auth()->user()->email }}</div>
                </li>
                <li><a class="dropdown-item py-2 px-4 flex items-center gap-2" href="{{ route('users.index') }}"><i class="bi bi-person text-slate-400"></i> My Profile</a></li>
                @if(auth()->user() && auth()->user()->role === 'admin')
                <li><a class="dropdown-item py-2 px-4 flex items-center gap-2" href="{{ route('business.index') }}"><i class="bi bi-gear text-slate-400"></i> Settings</a></li>
                @endif
                <li>
                    <hr class="dropdown-divider border-slate-700 opacity-50">
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
</aside>