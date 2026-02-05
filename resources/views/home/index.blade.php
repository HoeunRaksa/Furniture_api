@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Dashboard Overview</h2>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-cart shadow-sm text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Orders</h6>
                        <h3 class="fw-bold mb-0">{{ $metrics['total_orders'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-clock-history shadow-sm text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Pending</h6>
                        <h3 class="fw-bold mb-0">{{ $metrics['pending_orders'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-currency-dollar shadow-sm text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Revenue</h6>
                        <h3 class="fw-bold mb-0">${{ number_format($metrics['revenue_this_month']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="bi bi-people shadow-sm text-info fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Customers</h6>
                        <h3 class="fw-bold mb-0">{{ $metrics['new_customers'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder for Charts or Recent Activity -->
    <div class="row">
        <div class="col-lg-8">
            <x-widget title="Sales Activity">
                <div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="height: 300px;">
                    <p class="text-muted italic">Sales chart will appear here</p>
                </div>
            </x-widget>
        </div>
        <div class="col-lg-4">
            <x-widget title="Top Categories">
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span>Living Room</span>
                        <span class="badge bg-primary rounded-pill">45%</span>
                    </div>
                    <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span>Bedroom</span>
                        <span class="badge bg-primary rounded-pill">30%</span>
                    </div>
                    <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span>Office</span>
                        <span class="badge bg-primary rounded-pill">15%</span>
                    </div>
                    <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span>Other</span>
                        <span class="badge bg-primary rounded-pill">10%</span>
                    </div>
                </div>
            </x-widget>
        </div>
    </div>
</div>
@endsection
