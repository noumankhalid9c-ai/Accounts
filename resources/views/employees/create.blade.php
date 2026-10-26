@extends('layouts.app')
@section('title', 'Add New Employee')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="bi bi-person-plus text-primary me-2"></i> Add New Employee
            </h1>
            <p class="text-muted small mb-0">Register a new team member and set their basic salary details.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Employees
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0">Employee Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('employees.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" name="employee_id" class="form-control" placeholder="e.g. EMP-001" value="{{ old('employee_id') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Nouman Khalid" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Designation</label>
                                <input type="text" name="designation" class="form-control" placeholder="e.g. Travel Consultant" value="{{ old('designation') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Contact / Phone</label>
                                <input type="text" name="contact" class="form-control" placeholder="e.g. 03001234567" value="{{ old('contact') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Basic Salary (PKR) <span class="text-danger">*</span></label>
                                <input type="number" step="any" name="basic_salary" class="form-control" placeholder="e.g. 50000" value="{{ old('basic_salary') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Joining Date</label>
                                <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12 text-end pt-3">
                                <a href="{{ route('employees.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Save Employee
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
