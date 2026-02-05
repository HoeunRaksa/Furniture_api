<aside class="h-100 bg-white shadow-sm py-4 overflow-y-auto border-end">
    <nav class="px-3">
        <div class="list-group list-group-flush">
            <a href="{{ route('home') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            
            <div class="mt-4 mb-2 ps-3 text-uppercase small fw-bold text-muted">Management</div>
            
            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam me-2"></i> Products
            </a>
            <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags me-2"></i> Categories
            </a>
            <a href="{{ route('attributes.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ request()->routeIs('attributes.*') ? 'active' : '' }}">
                <i class="bi bi-sliders me-2"></i> Attributes
            </a>
            <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart me-2"></i> Orders
            </a>
            <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Users
            </a>
            <a href="{{ route('business.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ request()->routeIs('business.*') ? 'active' : '' }}">
                <i class="bi bi-gear me-2"></i> Business Settings
            </a>
        </div>
    </nav>
</aside>
