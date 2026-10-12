@extends('layouts.app')

@section('title', 'All Monthly Expenses')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-receipt-cutoff text-primary me-2"></i>All Monthly Expenses
        </h1>
        <p class="page-header-subtitle text-muted mb-0">Browse, filter, search, and manage all company expense records.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('monthly_expenses.create') }}" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> Add Monthly Expense
        </a>
        <a href="{{ route('monthly_expenses.reports') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Reports
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('monthly_expenses.index') }}" class="row g-3 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">Month</label>
                <select name="month" class="form-select border-0 bg-light">
                    <option value="all" {{ $month === 'all' ? 'selected' : '' }}>All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($month != 'all' && (int)$month == $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">Year</label>
                <select name="year" class="form-select border-0 bg-light">
                    <option value="all" {{ $year === 'all' ? 'selected' : '' }}>All Years</option>
                    @for($y = date('Y') - 3; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ ($year != 'all' && (int)$year == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted">Category</label>
                <select name="category_id" class="form-select border-0 bg-light">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->group }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">Status</label>
                <select name="status" class="form-select border-0 bg-light">
                    <option value="">All Statuses</option>
                    <option value="Paid" {{ $status == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Partially Paid" {{ $status == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Overdue" {{ $status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="Cancelled" {{ $status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted">Search Keyword</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Title, provider, ref..." value="{{ $search }}">
                    <button type="submit" class="btn btn-primary px-3">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request()->hasAny(['month', 'year', 'category_id', 'status', 'search', 'utility_only']))
                        <a href="{{ route('monthly_expenses.index') }}" class="btn btn-light border px-2" title="Clear Filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Expense Code</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Date</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Title & Category</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Provider / Reference</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Amount</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Paid</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Balance</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Due Date</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border fw-medium">{{ $exp->expense_code }}</span>
                        </td>
                        <td class="small text-muted">
                            {{ $exp->expense_date ? $exp->expense_date->format('d M Y') : '-' }}
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
                            @if($exp->consumer_number)
                                <span class="text-muted small d-block">A/C: {{ $exp->consumer_number }}</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">
                            PKR {{ number_format($exp->amount, 2) }}
                        </td>
                        <td class="text-success fw-semibold">
                            PKR {{ number_format($exp->paid_amount, 2) }}
                        </td>
                        <td class="fw-bold {{ $exp->remaining_amount > 0 ? 'text-danger' : 'text-muted' }}">
                            PKR {{ number_format($exp->remaining_amount, 2) }}
                        </td>
                        <td class="small">
                            @if($exp->due_date)
                                <span class="{{ $exp->is_overdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $exp->due_date->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
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
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('monthly_expenses.show', $exp->id) }}" class="btn btn-light border" title="View Details">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                                <a href="{{ route('monthly_expenses.edit', $exp->id) }}" class="btn btn-light border" title="Edit">
                                    <i class="bi bi-pencil text-secondary"></i>
                                </a>
                                @if(auth()->user() && auth()->user()->role_id == 1)
                                    <form action="{{ route('monthly_expenses.destroy', $exp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Move this expense to trash?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                            <p class="mb-0">No monthly expenses match the criteria.</p>
                            <a href="{{ route('monthly_expenses.create') }}" class="btn btn-sm btn-outline-primary rounded-pill mt-3">
                                <i class="bi bi-plus-circle me-1"></i> Record New Expense
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
