@extends('layouts.app')

@section('title', 'Utility Bills Management')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Utility Bills Management
        </h1>
        <p class="page-header-subtitle text-muted mb-0">
            Track and manage office electricity, gas, water, internet, and telecom utility bills.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('monthly_expenses.create', ['utility' => 1]) }}" class="btn btn-warning text-dark fw-semibold rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> Add Utility Bill
        </a>
        <a href="{{ route('monthly_expenses.dashboard') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
        </a>
    </div>
</div>

<!-- Utility Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-uppercase fw-bold opacity-75">Total Utility Bills</span>
                <i class="bi bi-lightning-charge fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">PKR {{ number_format($totalUtilityAmount, 2) }}</h3>
            <span class="small opacity-75">All utility costs for period</span>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-uppercase fw-bold opacity-75">Total Paid</span>
                <i class="bi bi-check-circle fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">PKR {{ number_format($totalUtilityPaid, 2) }}</h3>
            <span class="small opacity-75">Paid bills</span>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-uppercase fw-bold opacity-75">Pending / Overdue</span>
                <i class="bi bi-exclamation-triangle fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">PKR {{ number_format($totalUtilityPending, 2) }}</h3>
            <span class="small opacity-75">Outstanding balance</span>
        </div>
    </div>
</div>

<!-- Type breakdown badges -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 d-flex flex-wrap gap-2 align-items-center">
        <span class="fw-bold text-dark small me-2"><i class="bi bi-tag-fill text-primary me-1"></i>Type Breakdown:</span>
        @foreach($utilityTypes as $type)
            @php $amt = $typeBreakdown[$type] ?? 0; @endphp
            <a href="{{ route('monthly_expenses.utility_bills', array_merge(request()->query(), ['utility_type' => ($utilityType == $type ? '' : $type)])) }}" 
               class="badge text-decoration-none rounded-pill px-3 py-2 {{ $utilityType == $type ? 'bg-primary text-white' : 'bg-light text-dark border' }}" 
               style="font-size: 0.8rem;">
                {{ $type }}: <strong>PKR {{ number_format($amt) }}</strong>
            </a>
        @endforeach
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('monthly_expenses.utility_bills') }}" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="month" class="form-select form-select-sm border-light bg-light rounded-pill px-3 fw-semibold">
                    <option value="all" {{ $month === 'all' ? 'selected' : '' }}>All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($month != 'all' && (int)$month == $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="year" class="form-select form-select-sm border-light bg-light rounded-pill px-3 fw-semibold">
                    <option value="all" {{ $year === 'all' ? 'selected' : '' }}>All Years</option>
                    @for($y = date('Y') - 3; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ ($year != 'all' && (int)$year == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="utility_type" class="form-select form-select-sm border-light bg-light rounded-pill px-3 fw-semibold">
                    <option value="">All Utility Types</option>
                    @foreach($utilityTypes as $ut)
                        <option value="{{ $ut }}" {{ $utilityType == $ut ? 'selected' : '' }}>{{ $ut }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm border-light bg-light rounded-pill px-3 fw-semibold">
                    <option value="">All Statuses</option>
                    <option value="Paid" {{ $status == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Partially Paid" {{ $status == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Overdue" {{ $status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['month', 'year', 'utility_type', 'status']))
                    <a href="{{ route('monthly_expenses.utility_bills') }}" class="btn btn-sm btn-light border rounded-pill px-2">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bills Table Card -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Utility Type</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Provider / A/C #</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Bill Month</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Due Date</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Amount</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Paid</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Remaining</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Bill File</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1 fw-bold">
                                <i class="bi bi-lightning-charge me-1 text-warning"></i>{{ $bill->utility_type ?? 'Utility' }}
                            </span>
                            <div class="small text-muted mt-1">{{ $bill->expense_code }}</div>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $bill->provider_name ?? 'Provider' }}</strong>
                            @if($bill->consumer_number)
                                <div class="text-muted small">A/C: <span class="fw-semibold text-dark">{{ $bill->consumer_number }}</span></div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-medium text-dark">{{ \Carbon\Carbon::create($bill->expense_year, $bill->expense_month, 1)->format('M Y') }}</span>
                            @if($bill->bill_reference)
                                <div class="text-muted small">Ref: {{ $bill->bill_reference }}</div>
                            @endif
                        </td>
                        <td>
                            @if($bill->due_date)
                                <span class="{{ $bill->is_overdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $bill->due_date->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">
                            PKR {{ number_format($bill->amount, 2) }}
                        </td>
                        <td class="text-success fw-semibold">
                            PKR {{ number_format($bill->paid_amount, 2) }}
                        </td>
                        <td class="fw-bold {{ $bill->remaining_amount > 0 ? 'text-danger' : 'text-muted' }}">
                            PKR {{ number_format($bill->remaining_amount, 2) }}
                        </td>
                        <td>
                            @if($bill->payment_status === 'Paid')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Paid
                                </span>
                            @elseif($bill->payment_status === 'Partially Paid')
                                <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                    Partial
                                </span>
                            @elseif($bill->payment_status === 'Overdue')
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                    Overdue
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($bill->attachment_path)
                                <a href="{{ route('monthly_expenses.attachment', $bill->id) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1" style="font-size: 0.75rem;" title="View Attached Bill">
                                    <i class="bi bi-paperclip"></i> View Bill
                                </a>
                            @else
                                <span class="text-muted small">No File</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('monthly_expenses.show', $bill->id) }}" class="btn btn-light border" title="Details & Pay">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                                <a href="{{ route('monthly_expenses.edit', $bill->id) }}" class="btn btn-light border" title="Edit">
                                    <i class="bi bi-pencil text-secondary"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-lightning-charge fs-1 d-block mb-3 opacity-25"></i>
                            <p class="mb-0">No utility bills found matching the selection.</p>
                            <a href="{{ route('monthly_expenses.create', ['utility' => 1]) }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill mt-3">
                                Add Utility Bill Now
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bills->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $bills->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
