@extends('layouts.app')

@section('title', 'Yearly Expense Report - ' . $year)

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-calendar-range-fill text-primary me-2"></i>Yearly Expense Report ({{ $year }})
        </h1>
        <p class="page-header-subtitle text-muted mb-0">Annual overview, utility cost breakdown, and monthly trajectory.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('monthly_expenses.reports') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Monthly Report
        </a>
    </div>
</div>

<!-- Year Selector Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('monthly_expenses.reports.yearly') }}" class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="fw-bold text-dark small"><i class="bi bi-calendar3 me-1"></i>Select Fiscal Year:</span>
            </div>
            <div class="col-auto">
                <select name="year" class="form-select form-select-sm border-light bg-light rounded-pill px-3 fw-semibold">
                    @for($y = date('Y') - 4; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">Change Year</button>
            </div>
        </form>
    </div>
</div>

<!-- Key Yearly Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <span class="small text-uppercase fw-bold opacity-75">Total Yearly Expenses</span>
            <h3 class="fw-bold mb-0">PKR {{ number_format($yearlyTotal, 2) }}</h3>
            <span class="small opacity-75">12-Month Operating Total</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <span class="small text-uppercase fw-bold opacity-75">Total Utility Bills</span>
            <h3 class="fw-bold mb-0">PKR {{ number_format($yearlyUtility, 2) }}</h3>
            <span class="small opacity-75">Power, Gas, Phone, Net, Water</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
            <span class="small text-uppercase fw-bold opacity-75">Total Amount Paid</span>
            <h3 class="fw-bold mb-0">PKR {{ number_format($yearlyPaid, 2) }}</h3>
            <span class="small opacity-75">Disbursed funds</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
            <span class="small text-uppercase fw-bold opacity-75">Total Pending / Balance</span>
            <h3 class="fw-bold mb-0">PKR {{ number_format($yearlyPending, 2) }}</h3>
            <span class="small opacity-75">Payable liabilities</span>
        </div>
    </div>
</div>

<!-- Trend Chart -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-graph-up text-primary me-2"></i>Monthly Expense Trends (Jan - Dec {{ $year }})
        </h5>
        <span class="badge bg-light text-muted border px-2 py-1">PKR Monthly</span>
    </div>
    <div class="card-body p-4">
        <div style="height: 280px;">
            <canvas id="yearlyTrendChart"></canvas>
        </div>
    </div>
</div>

<!-- 12-Month Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <h5 class="fw-bold mb-0 text-dark">Month-by-Month Financial Summary</h5>
    </div>
    <div class="card-body p-0 pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Month</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">Monthly Expense Total (PKR)</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">Utility Bills (PKR)</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">Paid Amount (PKR)</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">Pending Amount (PKR)</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $mIndex => $mRow)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">
                            {{ $mRow['month_name'] }}
                        </td>
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($mRow['total'], 2) }}
                        </td>
                        <td class="text-end fw-semibold text-info">
                            {{ number_format($mRow['utility'], 2) }}
                        </td>
                        <td class="text-end fw-semibold text-success">
                            {{ number_format($mRow['paid'], 2) }}
                        </td>
                        <td class="text-end fw-semibold {{ $mRow['pending'] > 0 ? 'text-danger' : 'text-muted' }}">
                            {{ number_format($mRow['pending'], 2) }}
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('monthly_expenses.reports', ['month' => $mIndex, 'year' => $year]) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                View Month <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light border-top-2">
                    <tr>
                        <th class="ps-4 py-3 fw-bold text-dark text-uppercase small">Yearly Grand Total:</th>
                        <th class="py-3 text-end fw-bold text-dark fs-6">PKR {{ number_format($yearlyTotal, 2) }}</th>
                        <th class="py-3 text-end fw-bold text-info fs-6">PKR {{ number_format($yearlyUtility, 2) }}</th>
                        <th class="py-3 text-end fw-bold text-success fs-6">PKR {{ number_format($yearlyPaid, 2) }}</th>
                        <th class="py-3 text-end fw-bold text-danger fs-6">PKR {{ number_format($yearlyPending, 2) }}</th>
                        <th class="pe-4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('yearlyTrendChart');
    if (ctx) {
        const labels = {!! json_encode(array_column($monthlyData, 'month_name')) !!};
        const totals = {!! json_encode(array_column($monthlyData, 'total')) !!};
        const utilities = {!! json_encode(array_column($monthlyData, 'utility')) !!};
        const paids = {!! json_encode(array_column($monthlyData, 'paid')) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Expenses',
                        data: totals,
                        backgroundColor: '#4f46e5',
                        borderRadius: 6,
                    },
                    {
                        label: 'Utility Bills',
                        data: utilities,
                        backgroundColor: '#0ea5e9',
                        borderRadius: 6,
                    },
                    {
                        label: 'Paid Amount',
                        data: paids,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'PKR ' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
