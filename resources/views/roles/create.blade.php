@extends('layouts.app')

@section('title', 'Create Custom Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-xl-6">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2 text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Roles
                </a>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-shield-plus text-primary me-2"></i>Create Custom Role
                </h4>
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
                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Visa Processing Agent" value="{{ old('name') }}" required autofocus>
                        <div class="form-text small text-muted">A clear, recognizable title for the role.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Role Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Briefly describe the operational responsibilities of this role...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('roles.index') }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-arrow-right-circle me-1"></i> Continue to Permission Matrix
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
