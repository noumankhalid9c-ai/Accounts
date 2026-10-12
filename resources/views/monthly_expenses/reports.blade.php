@extends('layouts.app')

@section('title', 'Monthly Expense Report')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-file-earmark-spreadsheet-fill text-primary me-2"></i>Monthly Expense Report
        </h1>
        <p class="page-header-subtitle text-muted mb-0">
            Audit-ready statement for {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} with category breakdown and export capabilities.
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('monthly_expenses.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-outline-success rounded-pill px-3">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('monthly_expenses.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-filetype-csv me-1"></i> Export CSV
        </a>
        <a href="{{ route('monthly_expenses.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-outline-danger rounded-pill px-3">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('monthly_expenses.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-printer me-1"></i> Print Report
        </a>
    </div>
</div>

<!-- Period & Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('monthly_expenses.reports') }}" class="row g-3 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">Select Month</label>
                <select name="month" class="form-select border-0 bg-light">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">Select Year</label>
                <select name="year" class="form-select border-0 bg-light">
                    @for($y = date('Y') - 4; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted">Filter Category</label>
                <select name="category_id" class="form-select border-0 bg-light">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">Payment Status</label>
                <select name="status" class="form-select border-0 bg-light">
                    <option value="">All Statuses</option>
                    <option value="Paid" {{ $status == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Partially Paid" {{ $status == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Overdue" {{ $status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted">Provider / Vendor</label>
                <div class="input-group">
                    <input type="text" name="provider" class="form-control border-0 bg-light" placeholder="e.g. LESCO, PTCL" value="{{ $provider }}">
                    <button type="submit" class="btn btn-primary px-3">Filter</button>
                    <a href="{{ route('monthly_expenses.reports') }}" class="btn btn-light border px-2" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Metric Ribbon -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-dark">
            <span class="text-muted small fw-bold text-uppercase">Total Expenses</span>
            <h4 class="fw-bold mb-0 text-dark">PKR {{ number_format($totalExpenses, 2) }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
            <span class="text-success small fw-bold text-uppercase">Total Paid</span>
            <h4 class="fw-bold mb-0 text-success">PKR {{ number_format($totalPaid, 2) }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
            <span class="text-warning small fw-bold text-uppercase">Total Pending</span>
            <h4 class="fw-bold mb-0 text-warning">PKR {{ number_format($totalPending, 2) }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-danger">
            <span class="text-danger small fw-bold text-uppercase">Total Overdue</span>
            <h4 class="fw-bold mb-0 text-danger">PKR {{ number_format($totalOverdue, 2) }}</h4>
        </div>
    </div>
</div>

<!-- Statement Report Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1 text-dark">Expense Itemized Breakdown</h5>
            <span class="text-muted small">Period: {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('monthly_expenses.reports.comparison') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                <i class="bi bi-arrows-expand me-1"></i> Month Comparison
            </a>
            <a href="{{ route('monthly_expenses.reports.yearly', ['year' => $year]) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="bi bi-calendar-range me-1"></i> Yearly Trend
            </a>
        </div>
    </div>
    <div class="card-body p-0 pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Category</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Expense Title</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Provider</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">Amount (PKR)</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Due Date</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-center">Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark">
                            {{ $exp->expense_date ? $exp->expense_date->format('d M') : '-' }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 fw-medium">
                                {{ $exp->category->name ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('monthly_expenses.show', $exp->id) }}" class="fw-bold text-decoration-none text-dark">
                                {{ $exp->expense_title }}
                            </a>
                            @if($exp->bill_reference)
                                <span class="text-muted small ms-1">(#{{ $exp->bill_reference }})</span>
                            @endif
                        </td>
                        <td class="text-muted">
                            {{ $exp->provider_name ?? '—' }}
                        </td>
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($exp->amount, 2) }}
                        </td>
                        <td class="text-muted">
                            {{ $exp->due_date ? $exp->due_date->format('d M') : '—' }}
                        </td>
                        <td class="pe-4 text-center">
                            @if($exp->payment_status === 'Paid')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                    Paid
                                </span>
                            @elseif($exp->payment_status === 'Partially Paid')
                                <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                    Partially Paid
                                </span>
                            @elseif($exp->payment_status === 'Overdue')
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                    Overdue
                                </span>
                            @elseif($exp->payment_status === 'Cancelled')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                    Cancelled
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                    Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2 opacity-25"></i>
                            <p class="mb-0">No expenses recorded for this period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <!-- Bottom Totals Summary -->
                <tfoot class="bg-light border-top-2">
                    <tr>
                        <th colspan="4" class="ps-4 py-3 fw-bold text-dark text-uppercase small">Grand Total:</th>
                        <th class="py-3 text-end fw-bold text-dark fs-6">
                            PKR {{ number_format($totalExpenses, 2) }}
                        </th>
                        <th colspan="2" class="pe-4 py-3 text-end small">
                            <span class="text-success fw-bold me-3">Paid: PKR {{ number_format($totalPaid, 2) }}</span>
                            <span class="text-warning fw-bold me-3">Pending: PKR {{ number_format($totalPending, 2) }}</span>
                            <span class="text-danger fw-bold">Overdue: PKR {{ number_format($totalOverdue, 2) }}</span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
