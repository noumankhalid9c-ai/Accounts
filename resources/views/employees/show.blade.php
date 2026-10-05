@extends('layouts.app')

@section('title', 'Employee Profile')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">{{ $employee->name }}</h1>
        <p class="page-header-subtitle text-muted mb-0">
            Employee ID: <strong>{{ $employee->employee_id ?? 'N/A' }}</strong> &nbsp;|&nbsp; 
            Designation: <strong>{{ $employee->designation ?? 'N/A' }}</strong> &nbsp;|&nbsp; 
            Default Basic Salary: <strong>PKR {{ number_format($employee->basic_salary) }}</strong>
        </p>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Back to Team
    </a>
</div>

<!-- Salary Summary Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
            <div class="card-body p-4 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-2">Total Salary Paid</div>
                <h3 class="fw-bold mb-0 text-success">PKR {{ number_format($salaries->where('status', 'Paid')->sum('net_salary')) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
            <div class="card-body p-4 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-2">Total Commissions</div>
                <h3 class="fw-bold mb-0 text-primary">PKR {{ number_format($salaries->sum('sales_commission')) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
            <div class="card-body p-4 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-2">Total Advances</div>
                <h3 class="fw-bold mb-0 text-danger">PKR {{ number_format($salaries->sum('advance_salary')) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
            <div class="card-body p-4 text-center">
                <div class="text-muted small text-uppercase fw-bold mb-2">Pending Salary</div>
                <h3 class="fw-bold mb-0 text-warning">PKR {{ number_format($salaries->where('status', 'Pending')->sum('net_salary')) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Salary History -->
<h4 class="mb-3 fw-bold">Salary History</h4>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Month</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Basic</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Commission</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Incentive</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Advance</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Deduction</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Net Salary</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $salary)
                    <tr>
                        <td class="ps-4 fw-bold">{{ date('M Y', mktime(0,0,0,$salary->salary_month,1,$salary->salary_year)) }}</td>
                        <td>{{ number_format($salary->basic_salary) }}</td>
                        <td class="text-success">{{ number_format($salary->sales_commission) }}</td>
                        <td class="text-success">{{ number_format($salary->incentive) }}</td>
                        <td class="text-danger">{{ number_format($salary->advance_salary) }}</td>
                        <td class="text-danger">{{ number_format($salary->deduction) }}</td>
                        <td class="fw-bold text-primary">{{ number_format($salary->net_salary) }}</td>
                        <td>
                            @if($salary->status == 'Paid')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Paid</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">Pending</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('salary.slip', $salary->id) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 text-secondary">
                                <i class="bi bi-receipt me-1"></i> Slip
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No salary history available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
