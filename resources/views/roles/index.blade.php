@extends('layouts.app')

@section('title', 'Role Permissions')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Role Permissions</h2>
            <p class="text-muted mb-0">Manage granular permissions for each user role.</p>
        </div>
    </div>

    <div class="row">
        @foreach($roles as $role)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-uppercase mb-0 tracking-wide 
                            {{ $role === 'admin' ? 'text-danger' : ($role === 'staff' ? 'text-primary' : 'text-success') }}">
                            {{ ucfirst($role) }}
                        </h5>
                        @if($role === 'admin')
                        <span class="badge bg-danger rounded-pill">Super Admin</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <form class="role-permission-form" action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        <div class="list-group list-group-flush">
                            @foreach($permissions as $group => $perms)
                            <div class="list-group-item bg-light fw-bold text-uppercase small text-muted py-2 px-4">
                                {{ $group }}
                            </div>
                            @foreach($perms as $perm)
                            @php
                            $hasPerm = $rolePermissions->get($role)?->contains('permission_id', $perm->id);
                            $isChecked = $hasPerm || $role === 'admin';

                            // User Logic: Disable if they can't manage roles OR if the role itself is admin (locked)
                            $canManage = auth()->user()->hasPermission('manage_roles');
                            $isDisabled = !$canManage || $role === 'admin';
                            @endphp
                            <label class="list-group-item px-4 py-3 d-flex align-items-center justify-content-between cursor-pointer permission-row">
                                <span class="d-flex align-items-center">
                                    <i class="bi bi-shield-lock me-3 text-muted"></i>
                                    {{ $perm->label }}
                                </span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        {{ $isDisabled ? 'disabled' : '' }}>
                                </div>
                            </label>
                            @endforeach
                            @endforeach
                        </div>

                        @if($role !== 'admin')
                        @if(auth()->user()->hasPermission('manage_roles'))
                        <div class="p-4 border-top bg-light">
                            <button type="submit" class="btn btn-dark w-100 py-2 rounded-3 shadow-sm">
                                Save {{ ucfirst($role) }} Permissions
                            </button>
                        </div>
                        @else
                        <div class="p-4 border-top bg-light text-center">
                            <div class="alert alert-warning mb-0 border-0 fs-6">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                You have <strong>read-only</strong> access. You cannot modify permissions.
                            </div>
                        </div>
                        @endif
                        @else
                        <div class="p-4 border-top bg-light text-center">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Admin has full access by default.</small>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .permission-row:hover {
        background-color: #f8f9fa;
    }

    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
</style>
@endsection