@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-slate-800">Dashboard Overview</h2>
            <p class="text-muted">Welcome back, <span class="text-primary fw-semibold">{{ auth()->user()->username }}</span>! Here's what's happening today.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 hover-lift">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-cart shadow-sm text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small text-uppercase fw-bold">Total Orders</h6>
                        <h3 class="fw-bold mb-0 text-slate-800">{{ number_format($metrics['total_orders']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 hover-lift">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-clock-history shadow-sm text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small text-uppercase fw-bold">Pending</h6>
                        <h3 class="fw-bold mb-0 text-slate-800">{{ number_format($metrics['pending_orders']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 hover-lift">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-currency-dollar shadow-sm text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small text-uppercase fw-bold">Revenue (Month)</h6>
                        <h3 class="fw-bold mb-0 text-slate-800">${{ number_format($metrics['revenue_this_month'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 hover-lift">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="bi bi-people shadow-sm text-info fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small text-uppercase fw-bold">New Customers</h6>
                        <h3 class="fw-bold mb-0 text-slate-800">{{ number_format($metrics['new_customers']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Activity -->
    <div class="row">
        <div class="col-lg-8">
            <x-widget title="Sales Activity (Last 7 Days)">
                <div style="height: 350px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </x-widget>
        </div>
        <div class="col-lg-4">
            <x-widget title="Top Categories">
                <div class="list-group list-group-flush mt-2">
                    @forelse($topCategories as $category)
                    <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light p-2 me-3">
                                <i class="bi bi-grid text-primary"></i>
                            </div>
                            <span class="fw-semibold text-slate-700">{{ $category->name }}</span>
                        </div>
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3">{{ $category->products_count }} Products</span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-muted small italic">No categories available</p>
                    </div>
                    @endforelse
                </div>
            </x-widget>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($salesData);

        const labels = salesData.map(item => item.date);
        const totals = salesData.map(item => item.total);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales Revenue ($)',
                    data: totals,
                    backgroundColor: 'rgba(197, 160, 89, 0.8)',
                    borderColor: 'rgba(197, 160, 89, 1)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush