@extends('layouts.app')

@section('title', 'Monthly Expense Dashboard')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-calendar2-week-fill text-primary me-2"></i>Monthly Expense Dashboard
        </h1>
        <p class="page-header-subtitle text-muted mb-0">
            Overview of recurring company costs, office operations, and utility bills.
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('monthly_expenses.create') }}" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> Add Expense
        </a>
        <a href="{{ route('monthly_expenses.recurring') }}" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-arrow-repeat me-1"></i> Recurring Templates
        </a>
        <a href="{{ route('monthly_expenses.reports', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> View Report
        </a>
    </div>
</div>

<!-- Month / Year Selector Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('monthly_expenses.dashboard') }}" class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="fw-bold text-dark small"><i class="bi bi-filter me-1"></i>Selected Period:</span>
            </div>
            <div class="col-auto">
                <select name="month" class="form-select form-select-sm border-light bg-light rounded-pill px-3 fw-semibold">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="year" class="form-select form-select-sm border-light bg-light rounded-pill px-3 fw-semibold">
                    @for($y = date('Y') - 3; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="bi bi-arrow-clockwise me-1"></i> Update
                </button>
            </div>
            <div class="col-auto ms-auto text-muted small">
                Showing data for: <strong class="text-primary">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</strong>
            </div>
        </form>
    </div>
</div>

<!-- Key Financial KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Total Monthly Expenses -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-light opacity-75" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Monthly Expenses</span>
                <span class="badge bg-light bg-opacity-25 rounded-pill"><i class="bi bi-wallet2"></i></span>
            </div>
            <h3 class="fw-bold mb-1">PKR {{ number_format($totalExpenses, 2) }}</h3>
            <span class="text-light opacity-75 small">{{ $totalRecords }} expense record(s)</span>
        </div>
    </div>

    <!-- Total Utility Bills -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-light opacity-75" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Utility Bills</span>
                <span class="badge bg-light bg-opacity-25 rounded-pill"><i class="bi bi-lightning-charge-fill"></i></span>
            </div>
            <h3 class="fw-bold mb-1">PKR {{ number_format($totalUtilityBills, 2) }}</h3>
            <span class="text-light opacity-75 small">Power, Gas, Telecom, Net</span>
        </div>
    </div>

    <!-- Total Paid -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-light opacity-75" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Paid</span>
                <span class="badge bg-light bg-opacity-25 rounded-pill"><i class="bi bi-check-circle-fill"></i></span>
            </div>
            <h3 class="fw-bold mb-1">PKR {{ number_format($totalPaid, 2) }}</h3>
            <span class="text-light opacity-75 small">Settled expenses</span>
        </div>
    </div>

    <!-- Total Pending & Overdue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-bold text-light opacity-75" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Pending / Due</span>
                <span class="badge bg-light bg-opacity-25 rounded-pill"><i class="bi bi-clock-history"></i></span>
            </div>
            <h3 class="fw-bold mb-1">PKR {{ number_format($totalPending, 2) }}</h3>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-light opacity-75 small">Overdue: <strong>PKR {{ number_format($totalOverdue, 2) }}</strong></span>
                @if($totalOverdue > 0)
                    <span class="badge bg-danger rounded-pill px-2">Urgent</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-arrow-repeat fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Recurring Templates</div>
                    <h4 class="fw-bold mb-0 text-dark">{{ $applicableRecurringCount }} Active</h4>
                    <span class="text-muted small">Applicable for {{ \Carbon\Carbon::create($year, $month, 1)->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Overdue Amount</div>
                    <h4 class="fw-bold mb-0 text-danger">PKR {{ number_format($totalOverdue, 2) }}</h4>
                    <span class="text-muted small">Passed due date</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info">
                    <i class="bi bi-collection-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Number of Records</div>
                    <h4 class="fw-bold mb-0 text-dark">{{ $totalRecords }} Items</h4>
                    <span class="text-muted small">Total entries logged</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Breakdown Row -->
<div class="row g-4 mb-4">
    <!-- Category Breakdown Chart -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pie-chart-fill text-primary me-2"></i>Expense Breakdown by Category
                </h5>
                <span class="badge bg-light text-muted border px-2 py-1">PKR Total</span>
            </div>
            <div class="card-body p-4">
                @if($categoryBreakdown->count() > 0)
                    <div class="row align-items-center">
                        <div class="col-md-7 text-center">
                            <canvas id="categoryChart" style="max-height: 240px;"></canvas>
                        </div>
                        <div class="col-md-5">
                            <div class="overflow-auto" style="max-height: 240px;">
                                <ul class="list-group list-group-flush small">
                                    @foreach($categoryBreakdown as $catName => $catTotal)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-light">
                                            <span class="text-truncate me-2" title="{{ $catName }}">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; color: #4f46e5;"></i>
                                                {{ $catName ?? 'Uncategorized' }}
                                            </span>
                                            <span class="fw-bold text-dark text-nowrap">PKR {{ number_format($catTotal) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-pie-chart fs-1 opacity-25 d-block mb-2"></i>
                        <p class="mb-0">No expense records found for this period.</p>
                        <a href="{{ route('monthly_expenses.create', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-outline-primary rounded-pill mt-3">
                            <i class="bi bi-plus-circle me-1"></i> Add First Expense
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Status Distribution & Upcoming Bills -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bell-fill text-warning me-2"></i>Upcoming Due Dates
                </h5>
            </div>
            <div class="card-body p-4">
                @if($upcomingBills->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingBills as $bill)
                            <div class="list-group-item px-0 py-3 border-light d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">{{ $bill->expense_title }}</div>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar-event me-1"></i> Due: {{ $bill->due_date ? $bill->due_date->format('d M Y') : 'N/A' }}
                                        @if($bill->provider_name)
                                            <span class="ms-1">| {{ $bill->provider_name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-danger">PKR {{ number_format($bill->remaining_amount, 2) }}</div>
                                    <a href="{{ route('monthly_expenses.show', $bill->id) }}" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2" style="font-size: 0.75rem;">
                                        Pay Bill
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-2 opacity-50 d-block mb-2"></i>
                        <p class="mb-0">No pending upcoming due dates for this month!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Expenses Table -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-clock-history text-secondary me-2"></i>Recent Monthly Expenses
            </h5>
            <p class="text-muted small mb-0">Latest expense entries recorded for this period.</p>
        </div>
        <a href="{{ route('monthly_expenses.index', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-light border rounded-pill px-3">
            View All Expenses <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0 pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Code / Date</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Title & Category</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Provider / Bill #</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Amount</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Paid</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Due Date</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExpenses as $exp)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border fw-medium">{{ $exp->expense_code }}</span>
                            <div class="text-muted small mt-1">{{ $exp->expense_date ? $exp->expense_date->format('d M Y') : '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">
                                <a href="{{ route('monthly_expenses.show', $exp->id) }}" class="text-decoration-none text-dark">
                                    {{ $exp->expense_title }}
                                </a>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0" style="font-size: 0.72rem;">
                                {{ $exp->category->name ?? 'General' }}
                            </span>
                            @if($exp->is_utility_bill)
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-0" style="font-size: 0.72rem;">
                                    <i class="bi bi-lightning-charge"></i> {{ $exp->utility_type ?? 'Utility' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $exp->provider_name ?? '-' }}</div>
                            @if($exp->bill_reference)
                                <span class="text-muted small">Ref: {{ $exp->bill_reference }}</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">
                            PKR {{ number_format($exp->amount, 2) }}
                        </td>
                        <td class="text-success fw-semibold">
                            PKR {{ number_format($exp->paid_amount, 2) }}
                        </td>
                        <td>
                            {{ $exp->due_date ? $exp->due_date->format('d M Y') : '-' }}
                        </td>
                        <td>
                            @if($exp->payment_status === 'Paid')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Paid
                                </span>
                            @elseif($exp->payment_status === 'Partially Paid')
                                <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                    <i class="bi bi-dash-circle-fill me-1"></i> Partial
                                </span>
                            @elseif($exp->payment_status === 'Overdue')
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Overdue
                                </span>
                            @elseif($exp->payment_status === 'Cancelled')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                    Cancelled
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                    <i class="bi bi-clock-fill me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('monthly_expenses.show', $exp->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-primary">
                                <i class="bi bi-eye"></i> Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-3 opacity-25"></i>
                            <p class="mb-0">No expenses recorded for this month.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($categoryBreakdown->count() > 0)
    const ctx = document.getElementById('categoryChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryBreakdown->keys()->toArray()) !!},
                datasets: [{
                    data: {!! json_encode($categoryBreakdown->values()->toArray()) !!},
                    backgroundColor: [
                        '#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                        '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#84cc16'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '65%'
            }
        });
    }
    @endif
});
</script>
@endpush
@endsection
