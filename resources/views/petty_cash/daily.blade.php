@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    
    <!-- Header with Date Picker -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-dark">
            <i class="bi bi-calendar2-day text-primary me-2"></i> Daily Cash Entry 
            <span class="text-muted ms-2 fs-5">| {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
        </h2>
        <form method="GET" class="d-flex bg-white p-1 rounded shadow-sm border">
            <input type="date" name="date" value="{{ $date }}" class="form-control border-0 bg-transparent fw-bold text-primary" onchange="this.form.submit()" style="cursor: pointer;">
        </form>
    </div>

    @if(session('success')) <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div> @endif

    <!-- Daily Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem;"><i class="bi bi-door-open me-1"></i> OPENING CASH</p>
                    <h4 class="mb-0 fw-bold text-dark">PKR {{ number_format($day->opening_cash, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem;"><i class="bi bi-arrow-down-circle text-success me-1"></i> RECEIVED</p>
                    <h4 class="mb-0 fw-bold text-success">+ PKR {{ number_format($day->cash_received, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem;"><i class="bi bi-arrow-up-circle text-danger me-1"></i> EXPENSES</p>
                    <h4 class="mb-0 fw-bold text-danger">- PKR {{ number_format($day->cash_expenses, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                <div class="card-body">
                    <p class="text-white-50 fw-bold mb-1" style="font-size: 0.85rem;"><i class="bi bi-safe2 me-1"></i> EXPECTED CLOSING</p>
                    <h4 class="mb-0 fw-bold text-white">PKR {{ number_format($day->closing_cash, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 d-flex justify-content-center align-items-center bg-light">
                <div class="text-center">
                    <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem;">STATUS</p>
                    <span class="badge rounded-pill bg-{{ $day->status == 'Open' ? 'success' : 'secondary' }} fs-6 px-3 py-2">{{ $day->status }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Transaction Form -->
    @if($day->status == 'Open')
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
            <h5 class="fw-bold text-primary"><i class="bi bi-plus-circle-dotted me-2"></i>Add New Entry</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('petty_cash.transactions.store', $day->id) }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold small">Transaction Type</label>
                        <select name="transaction_type" class="form-select bg-light border-0" required>
                            <option value="Expense">Expense (Money Out)</option>
                            <option value="Cash Received">Cash Received (Money In)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold small">Category</label>
                        <select name="category_id" class="form-select bg-light border-0">
                            <option value="">(No Category)</option>
                            @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold small">Paid To / Received From</label>
                        <input type="text" name="paid_to" class="form-control bg-light border-0" placeholder="e.g. Careem, Metro...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold small">Payment Method</label>
                        <select name="payment_method" class="form-select bg-light border-0" required>
                            <option value="Cash">Cash (Physical Cash)</option>
                            <option value="Bank">Bank Transfer</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label text-muted fw-bold small">Description (Required)</label>
                        <input type="text" name="description" class="form-control bg-light border-0" placeholder="Detail of expense..." required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted fw-bold small">Amount (PKR)</label>
                        <input type="number" step="0.01" name="amount" class="form-control bg-white border border-primary fw-bold text-primary" placeholder="0.00" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-save me-1"></i> Save Entry</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-list-ul text-primary me-2"></i> Today's Transactions</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted" style="font-size: 0.85rem;">
                    <tr>
                        <th class="ps-4">TYPE</th>
                        <th>CATEGORY</th>
                        <th>DESCRIPTION</th>
                        <th>ENTITY</th>
                        <th>METHOD</th>
                        <th>ADDED BY</th>
                        <th class="text-end pe-4">AMOUNT</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($transactions as $t)
                    <tr>
                        <td class="ps-4">
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
                        <td class="text-muted small">{{ $t->creator->name ?? 'Unknown' }}</td>
                        <td class="text-end pe-4 fw-bold {{ $t->transaction_type == 'Expense' ? 'text-danger' : 'text-success' }}">
                            {{ $t->transaction_type == 'Expense' ? '-' : '+' }} PKR {{ number_format($t->amount, 2) }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('petty_cash.transactions.print', $t->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print Slip">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No transactions added today.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Closing Panel -->
    @if($day->status == 'Open')
    <div class="card border-0 shadow-sm rounded-3 bg-light">
        <div class="card-body p-4">
            <form action="{{ route('petty_cash.days.close', $day->id) }}" method="POST" class="row align-items-center g-3">
                @csrf
                <div class="col-md-6">
                    <h5 class="fw-bold text-danger mb-1"><i class="bi bi-lock-fill me-2"></i> End of Day Closure</h5>
                    <p class="text-muted small mb-0">Count the physical cash in the box and enter the exact amount below to close the day. Once closed, no more transactions can be added.</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-bold small">Actual Physical Cash Counted</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-danger text-danger fw-bold">PKR</span>
                        <input type="number" step="0.01" name="actual_cash" class="form-control border-danger form-control-lg" placeholder="0.00" required>
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-danger btn-lg w-100 fw-bold" onclick="return confirm('Are you sure you want to CLOSE this day? No more edits will be allowed.')">
                        Close Day
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm rounded-3 bg-light">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold text-secondary mb-1"><i class="bi bi-shield-lock-fill me-2"></i> Day is Closed</h5>
                    <p class="text-muted small mb-0">This day was closed by user ID: {{ $day->closed_by }} at {{ \Carbon\Carbon::parse($day->closed_at)->format('d M Y, h:i A') }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-inline-block bg-white p-3 rounded shadow-sm border border-secondary border-opacity-25 text-start me-3">
                        <div class="text-muted small fw-bold mb-1">ACTUAL CASH</div>
                        <h4 class="mb-0 fw-bold">PKR {{ number_format($day->actual_cash, 2) }}</h4>
                    </div>
                    <div class="d-inline-block bg-white p-3 rounded shadow-sm border {{ $day->cash_difference < 0 ? 'border-danger' : ($day->cash_difference > 0 ? 'border-success' : 'border-secondary') }} border-opacity-50 text-start">
                        <div class="text-muted small fw-bold mb-1">DIFFERENCE</div>
                        <h4 class="mb-0 fw-bold {{ $day->cash_difference < 0 ? 'text-danger' : ($day->cash_difference > 0 ? 'text-success' : 'text-secondary') }}">
                            {{ $day->cash_difference > 0 ? '+' : '' }}PKR {{ number_format($day->cash_difference, 2) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection