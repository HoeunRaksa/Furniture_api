<div style="color: #333;" class="mb-4 flex flex-col flex-grow widget-animate">
    <!-- Widget Header -->
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h2 class="font-bold mb-0 animate-fade-in-left" style="color: #1e293b; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.025em;">
            {{ $title }}
        </h2>
        <div class="widget-header-line flex-grow-1 mx-2" style="height: 1px; background: #e2e8f0; border-radius: 2px;"></div>
    </div>

    <!-- Content / Activity -->
    <div class="card-body text-gray-800 p-2 shadow-sm bg-white overflow-auto rounded-lg border-0" 
         style="border-radius: 8px; transition: all 0.3s ease;">
        {{ $slot }}
    </div>
</div>
