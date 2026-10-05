@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-spreadsheet-fill text-primary me-2"></i> Petty Cash Reports</h2>
        <div>
            <a href="{{ route('petty_cash.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success px-4">
                <i class="bi bi-file-earmark-excel me-1"></i> Export to CSV
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('petty_cash.reports') }}" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted fw-bold small">Start Date</label>
                    <input type="date" name="start_date" class="form-control bg-light border-0" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted fw-bold small">End Date</label>
                    <input type="date" name="end_date" class="form-control bg-light border-0" value="{{ $endDate }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-search me-1"></i> Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem;">TOTAL RECEIVED (Selected Period)</p>
                        <h3 class="mb-0 fw-bold text-success">PKR {{ number_format($totalReceived, 2) }}</h3>
                    </div>
                    <i class="bi bi-arrow-down-circle fs-1 text-success opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem;">TOTAL EXPENSES (Selected Period)</p>
                        <h3 class="mb-0 fw-bold text-danger">PKR {{ number_format($totalExpenses, 2) }}</h3>
                    </div>
                    <i class="bi bi-arrow-up-circle fs-1 text-danger opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-list-ul text-primary me-2"></i> Transactions</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted" style="font-size: 0.85rem;">
                    <tr>
                        <th class="ps-4">DATE</th>
                        <th>TYPE</th>
                        <th>CATEGORY</th>
                        <th>DESCRIPTION</th>
                        <th>ENTITY</th>
                        <th>METHOD</th>
                        <th>ADDED BY</th>
                        <th class="text-end pe-4">AMOUNT</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($transactions as $t)
                    <tr>
                        <td class="ps-4 text-muted small fw-bold">{{ \Carbon\Carbon::parse($t->date)->format('d M Y') }}</td>
                        <td>
                            @if($t->transaction_type == 'Expense')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="bi bi-arrow-up-right me-1"></i>Expense</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-arrow-down-left me-1"></i>Received</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary rounded-pill fw-normal">{{ $t->category->name ?? 'None' }}</span></td>
                        <td class="text-dark">{{ $t->description }}</td>
                        <td class="text-muted">{{ $t->paid_to ?? '-' }}</td>
                        <td><span class="text-muted small"><i class="bi {{ $t->payment_method == 'Cash' ? 'bi-cash' : 'bi-bank' }} me-1"></i>{{ $t->payment_method }}</span></td>
                        <td class="text-muted small">{{ $t->creator->name ?? 'System' }}</td>
                        <td class="text-end pe-4 fw-bold {{ $t->transaction_type == 'Expense' ? 'text-danger' : 'text-success' }}">
                            {{ $t->transaction_type == 'Expense' ? '-' : '+' }} PKR {{ number_format($t->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>No transactions found for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
