<div class="mb-5 flex flex-col flex-grow widget-animate">
    <!-- Widget Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="font-heading mb-0 animate__animated animate__fadeInLeft" style="font-size: 1.25rem; font-weight: 700; color: var(--color-dark);">
            {!! $title !!}
        </h2>
        <div class="widget-header-line flex-grow-1 ms-3" style="height: 1px; background: #e0e0e0;"></div>
    </div>

    <!-- Content -->
    <div class="bg-white p-0 border-0" style="background: transparent;">
        {{ $slot }}
    </div>
</div>