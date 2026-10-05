@extends('layouts.app')

@section('title', 'Team / Employees')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Team / Employees</h1>
        <p class="page-header-subtitle text-muted mb-0">Manage your team members and their base profiles.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        <i class="bi bi-person-plus me-2"></i> Add Employee
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">EMP ID</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Name</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Designation</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Base Salary</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Joining Date</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $employee->employee_id ?? '-' }}</td>
                        <td class="fw-bold text-dark">
                            <a href="{{ route('employees.show', $employee->id) }}" class="text-decoration-none text-dark">{{ $employee->name }}</a>
                        </td>
                        <td>{{ $employee->designation ?? '-' }}</td>
                        <td class="text-primary fw-semibold">PKR {{ number_format($employee->basic_salary) }}</td>
                        <td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : '-' }}</td>
                        <td>
                            @if($employee->status == 'Active')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 border border-secondary border-opacity-25">Inactive</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-primary me-2"><i class="bi bi-eye"></i></a>
                            <button class="btn btn-sm btn-light border rounded-pill px-3 text-secondary" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $employee->id }}"><i class="bi bi-pencil"></i></button>
                        </td>
                    </tr>

                    <!-- Edit Employee Modal -->
                    <div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Employee</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Employee ID</label>
                                                <input type="text" name="employee_id" class="form-control" value="{{ $employee->employee_id }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $employee->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Designation</label>
                                                <input type="text" name="designation" class="form-control" value="{{ $employee->designation }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Joining Date</label>
                                                <input type="date" name="joining_date" class="form-control" value="{{ $employee->joining_date }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Default Base Salary</label>
                                                <input type="number" name="basic_salary" class="form-control" value="{{ $employee->basic_salary }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Contact</label>
                                                <input type="text" name="contact" class="form-control" value="{{ $employee->contact }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="Active" {{ $employee->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Inactive" {{ $employee->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-12 mt-4 text-end">
                                                <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
                            <p class="mb-0">No employees found. Add your first team member.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" required placeholder="e.g. EMP-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Joining Date</label>
                            <input type="date" name="joining_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Base Salary</label>
                            <input type="number" name="basic_salary" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Save Employee</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
