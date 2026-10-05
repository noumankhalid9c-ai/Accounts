@extends('layouts.app')

@section('title', 'Team Salary')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Team Salary</h1>
        <p class="page-header-subtitle text-muted mb-0">Manage monthly payroll, generate slips, and track advance salaries.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#generateSalaryModal">
            <i class="bi bi-magic me-2"></i> Generate Monthly Salary
        </button>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addSalaryModal">
            <i class="bi bi-plus-lg me-2"></i> Add Salary
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
            <div class="card-body p-4">
                <div class="text-white-50 small text-uppercase fw-bold mb-2">Total Employees</div>
                <h2 class="display-6 fw-bold mb-0">{{ $summary['total_employees'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-muted small text-uppercase fw-bold mb-2">Total Net Salary</div>
                <h2 class="fs-3 fw-bold mb-0 text-dark">PKR {{ number_format($summary['total_net_salary']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-success small text-uppercase fw-bold mb-2">Total Paid</div>
                <h2 class="fs-3 fw-bold mb-0 text-success">PKR {{ number_format($summary['total_paid']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-warning small text-uppercase fw-bold mb-2">Total Pending</div>
                <h2 class="fs-3 fw-bold mb-0 text-warning">PKR {{ number_format($summary['total_pending']) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-3 d-flex flex-wrap justify-content-around text-center small">
        <div class="px-3 border-end">
            <span class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Basic Salary</span>
            <span class="fw-bold">PKR {{ number_format($summary['total_basic_salary']) }}</span>
        </div>
        <div class="px-3 border-end">
            <span class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Commissions</span>
            <span class="fw-bold text-success">+PKR {{ number_format($summary['total_commission']) }}</span>
        </div>
        <div class="px-3 border-end">
            <span class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Incentives</span>
            <span class="fw-bold text-success">+PKR {{ number_format($summary['total_incentive']) }}</span>
        </div>
        <div class="px-3 border-end">
            <span class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Advances</span>
            <span class="fw-bold text-danger">-PKR {{ number_format($summary['total_advance']) }}</span>
        </div>
        <div class="px-3">
            <span class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Deductions</span>
            <span class="fw-bold text-danger">-PKR {{ number_format($summary['total_deduction']) }}</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('salary.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Month</label>
                <select name="month" class="form-select border-0 bg-light">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Year</label>
                <select name="year" class="form-select border-0 bg-light">
                    @for($i=date('Y')-2; $i<=date('Y')+1; $i++)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Employee</label>
                <select name="employee_id" class="form-select border-0 bg-light">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $employee_id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Status</label>
                <select name="status" class="form-select border-0 bg-light">
                    <option value="">All</option>
                    <option value="Paid" {{ $status == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-2 d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('salary.index') }}" class="btn btn-light px-3">Reset</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Employee Name</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Basic Salary</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Sales Commission</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Incentive</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Advance Salary</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Deduction</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Net Salary</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end" style="letter-spacing: 0.5px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $salary)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark">
                            <a href="{{ route('employees.show', $salary->employee_id) }}" class="text-decoration-none text-dark">{{ $salary->employee->name }}</a>
                        </td>
                        <td>{{ number_format($salary->basic_salary) }}</td>
                        <td class="text-success">+{{ number_format($salary->sales_commission) }}</td>
                        <td class="text-success">+{{ number_format($salary->incentive) }}</td>
                        <td class="text-danger">-{{ number_format($salary->advance_salary) }}</td>
                        <td class="text-danger">-{{ number_format($salary->deduction) }}</td>
                        <td class="fw-bold text-primary">{{ number_format($salary->net_salary) }}</td>
                        <td>
                            @if($salary->status == 'Paid')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 border border-success border-opacity-25"><i class="bi bi-check-circle me-1"></i> Paid</span>
                                <div class="small text-muted mt-1" style="font-size:0.7rem;">{{ \Carbon\Carbon::parse($salary->payment_date)->format('d M Y') }}</div>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 border border-warning border-opacity-25"><i class="bi bi-clock me-1"></i> Pending</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('salary.slip', $salary->id) }}" target="_blank" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="View Slip">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                <button class="btn btn-sm btn-light border text-secondary" data-bs-toggle="modal" data-bs-target="#editSalaryModal{{ $salary->id }}" data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($salary->status == 'Pending')
                                <form action="{{ route('salary.markPaid', $salary->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light border text-success" data-bs-toggle="tooltip" title="Mark as Paid">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('salary.markPending', $salary->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light border text-warning" data-bs-toggle="tooltip" title="Mark as Pending" onclick="return confirm('Are you sure you want to mark this back as Pending?')">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('salary.destroy', $salary->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this salary record?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editSalaryModal{{ $salary->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Salary: {{ $salary->employee->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('salary.update', $salary->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Basic Salary</label>
                                                <input type="number" name="basic_salary" class="form-control" value="{{ $salary->basic_salary }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Sales Commission</label>
                                                <input type="number" name="sales_commission" class="form-control" value="{{ $salary->sales_commission }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Incentive</label>
                                                <input type="number" name="incentive" class="form-control" value="{{ $salary->incentive }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-danger">Advance Salary</label>
                                                <input type="number" name="advance_salary" class="form-control" value="{{ $salary->advance_salary }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-danger">Deduction</label>
                                                <input type="number" name="deduction" class="form-control" value="{{ $salary->deduction }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Notes</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $salary->notes }}</textarea>
                                            </div>
                                            <div class="col-12 mt-4 text-end">
                                                <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-4">Update Salary</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-wallet2 fs-1 d-block mb-3 opacity-50"></i>
                            <p class="mb-0">No salary records found for this month.</p>
                            <button class="btn btn-link text-decoration-none mt-2" data-bs-toggle="modal" data-bs-target="#generateSalaryModal">Generate Salaries Now</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Salary Modal -->
<div class="modal fade" id="addSalaryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Add Manual Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('salary.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required id="add_employee_id" onchange="updateBasicSalary()">
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-basic="{{ $emp->basic_salary }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Month</label>
                            <select name="salary_month" class="form-select" required>
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Year</label>
                            <input type="number" name="salary_year" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Basic Salary</label>
                            <input type="number" name="basic_salary" id="add_basic_salary" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sales Commission</label>
                            <input type="number" name="sales_commission" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Incentive</label>
                            <input type="number" name="incentive" class="form-control" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-danger">Advance Salary</label>
                            <input type="number" name="advance_salary" class="form-control" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-danger">Deduction</label>
                            <input type="number" name="deduction" class="form-control" value="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Save Salary</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Generate Salary Modal -->
<div class="modal fade" id="generateSalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Generate Monthly Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">This will generate default pending salary records for all active employees for the selected month.</p>
                <form action="{{ route('salary.generate') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select" required>
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Generate Now</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateBasicSalary() {
    var select = document.getElementById('add_employee_id');
    var basic = select.options[select.selectedIndex].getAttribute('data-basic');
    document.getElementById('add_basic_salary').value = basic ? basic : 0;
}
</script>
@endsection
