@extends('layouts.app')

@section('title', 'Month-to-Month Comparison')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-arrows-expand text-primary me-2"></i>Month-to-Month Expense Comparison
        </h1>
        <p class="page-header-subtitle text-muted mb-0">
            Compare category costs, utility spikes, and operational trends across any two periods.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('monthly_expenses.reports') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Monthly Report
        </a>
    </div>
</div>

<!-- Comparison Period Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('monthly_expenses.reports.comparison') }}" class="row g-3 align-items-end">
            <!-- Period 1 -->
            <div class="col-12 col-md-5">
                <label class="form-label small fw-bold text-muted">Baseline Month (Month 1)</label>
                <div class="row g-2">
                    <div class="col-7">
                        <select name="m1" class="form-select border-0 bg-light">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m1 == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-5">
                        <select name="y1" class="form-select border-0 bg-light">
                            @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $y1 == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-1 text-center d-none d-md-block pb-2">
                <span class="badge bg-secondary rounded-circle p-2"><i class="bi bi-arrow-left-right"></i></span>
            </div>

            <!-- Period 2 -->
            <div class="col-12 col-md-5">
                <label class="form-label small fw-bold text-muted">Comparison Month (Month 2)</label>
                <div class="row g-2">
                    <div class="col-7">
                        <select name="m2" class="form-select border-0 bg-light">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m2 == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-5">
                        <select name="y2" class="form-select border-0 bg-light">
                            @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $y2 == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-1">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">Compare</button>
            </div>
        </form>
    </div>
</div>

@php
    $m1Name = \Carbon\Carbon::create($y1, $m1, 1)->format('F Y');
    $m2Name = \Carbon\Carbon::create($y2, $m2, 1)->format('F Y');
@endphp

<!-- Comparison Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <span class="text-muted small fw-bold text-uppercase">{{ $m1Name }} Total</span>
            <h3 class="fw-bold mb-0 text-dark">PKR {{ number_format($total1, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <span class="text-muted small fw-bold text-uppercase">{{ $m2Name }} Total</span>
            <h3 class="fw-bold mb-0 text-dark">PKR {{ number_format($total2, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Net Variance</span>
            <h3 class="fw-bold mb-0 {{ $grandDiff > 0 ? 'text-danger' : ($grandDiff < 0 ? 'text-success' : 'text-muted') }}">
                {{ $grandDiff >= 0 ? '+' : '' }}PKR {{ number_format($grandDiff, 2) }}
                <span class="fs-6 fw-semibold">({{ number_format($grandPct, 1) }}%)</span>
            </h3>
        </div>
    </div>
</div>

<!-- Comparison Data Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <h5 class="fw-bold mb-0 text-dark">Category Variance Table</h5>
        <span class="text-muted small">Comparing {{ $m1Name }} against {{ $m2Name }}</span>
    </div>
    <div class="card-body p-0 pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Category</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Group</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">{{ $m1Name }} (PKR)</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">{{ $m2Name }} (PKR)</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">Difference (PKR)</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">% Change</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comparison as $row)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">
                            {{ $row['category'] }}
                        </td>
                        <td>
                            <span class="badge bg-light text-muted border px-2 py-1 small">{{ $row['group'] ?? 'General' }}</span>
                        </td>
                        <td class="text-end fw-semibold text-muted">
                            {{ number_format($row['amount1'], 2) }}
                        </td>
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($row['amount2'], 2) }}
                        </td>
                        <td class="text-end fw-bold {{ $row['difference'] > 0 ? 'text-danger' : ($row['difference'] < 0 ? 'text-success' : 'text-muted') }}">
                            {{ $row['difference'] >= 0 ? '+' : '' }}{{ number_format($row['difference'], 2) }}
                        </td>
                        <td class="pe-4 text-end">
                            @if($row['difference'] > 0)
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1">
                                    <i class="bi bi-arrow-up-right me-1"></i>+{{ number_format($row['percentage'], 1) }}%
                                </span>
                            @elseif($row['difference'] < 0)
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                                    <i class="bi bi-arrow-down-right me-1"></i>{{ number_format($row['percentage'], 1) }}%
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">
                                    0.0%
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-arrows-expand fs-1 d-block mb-2 opacity-25"></i>
                            <p class="mb-0">No expenses recorded for either period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light border-top-2">
                    <tr>
                        <th colspan="2" class="ps-4 py-3 fw-bold text-dark text-uppercase small">Total:</th>
                        <th class="py-3 text-end fw-bold text-muted">PKR {{ number_format($total1, 2) }}</th>
                        <th class="py-3 text-end fw-bold text-dark">PKR {{ number_format($total2, 2) }}</th>
                        <th class="py-3 text-end fw-bold {{ $grandDiff > 0 ? 'text-danger' : ($grandDiff < 0 ? 'text-success' : 'text-muted') }}">
                            {{ $grandDiff >= 0 ? '+' : '' }}PKR {{ number_format($grandDiff, 2) }}
                        </th>
                        <th class="pe-4 py-3 text-end fw-bold {{ $grandDiff > 0 ? 'text-danger' : ($grandDiff < 0 ? 'text-success' : 'text-muted') }}">
                            {{ $grandDiff >= 0 ? '+' : '' }}{{ number_format($grandPct, 1) }}%
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
