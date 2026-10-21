@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-shield-lock-fill text-primary me-2"></i>Roles & Permissions
        </h4>
        <p class="text-muted small mb-0">Define system roles and configure fine-grained module & action-level permission matrices.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm">
            <i class="bi bi-people me-1"></i> Back to Users
        </a>
        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Create Custom Role
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">Available Roles ({{ $roles->count() }})</h6>
        <span class="text-muted small">Super Admin has unrestricted system-wide access.</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Role Name</th>
                    <th>Identifier / Slug</th>
                    <th>Description</th>
                    <th>Assigned Users</th>
                    <th>Type</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            @if($role->id === 1)
                                <i class="bi bi-shield-fill-check text-danger fs-5"></i>
                            @elseif($role->is_system)
                                <i class="bi bi-shield-check text-primary fs-5"></i>
                            @else
                                <i class="bi bi-shield text-secondary fs-5"></i>
                            @endif
                            {{ $role->name }}
                        </div>
                    </td>
                    <td>
                        <code class="text-muted">{{ $role->slug ?? Str::slug($role->name) }}</code>
                    </td>
                    <td class="text-muted small" style="max-width: 320px;">
                        {{ $role->description ?? '—' }}
                    </td>
                    <td>
                        <a href="{{ route('users.index', ['role_id' => $role->id]) }}" class="badge bg-light text-primary border rounded-pill px-3 py-1.5 text-decoration-none fw-semibold">
                            <i class="bi bi-person-fill me-1"></i> {{ $role->users_count }} Users
                        </a>
                    </td>
                    <td>
                        @if($role->is_system || $role->id === 1)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2.5 py-1 rounded-pill">
                                System Role
                            </span>
                        @else
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1 rounded-pill">
                                Custom Role
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1.5">
                            <a href="{{ route('roles.permissions', $role->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" title="Configure matrix">
                                <i class="bi bi-grid-3x3-gap-fill me-1"></i> Permissions Matrix
                            </a>
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-light border rounded-pill px-2.5" title="Edit Role">
                                <i class="bi bi-pencil-square text-secondary"></i>
                            </a>
                            @if(!$role->is_system && $role->id !== 1 && $role->users_count === 0)
                                <form method="POST" action="{{ route('roles.destroy', $role->id) }}" class="d-inline" onsubmit="return confirm('Delete custom role \'{{ $role->name }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2.5 text-danger" title="Delete Role">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
