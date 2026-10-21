@extends('layouts.app')

@section('title', 'Edit Role - ' . $role->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-xl-6">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2 text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Roles
                </a>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Role: {{ $role->name }}
                </h4>
            </div>
            <div>
                <a href="{{ route('roles.permissions', $role->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Permissions Matrix
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4 p-3 mb-4 shadow-sm">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('roles.update', $role->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Role Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="text-muted small">
                            @if($role->is_system)
                                <i class="bi bi-shield-lock me-1"></i> System Protected Role
                            @else
                                Custom Role
                            @endif
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
