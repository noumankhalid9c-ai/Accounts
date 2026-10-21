@extends('layouts.app')

@section('title', 'Expense Details - ' . $expense->expense_code)

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-dark rounded-pill px-3 py-1">{{ $expense->expense_code }}</span>
            @if($expense->payment_status === 'Paid')
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-check-circle-fill me-1"></i> Paid
                </span>
            @elseif($expense->payment_status === 'Partially Paid')
                <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-dash-circle-fill me-1"></i> Partially Paid
                </span>
            @elseif($expense->payment_status === 'Overdue')
                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Overdue
                </span>
            @elseif($expense->payment_status === 'Cancelled')
                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                    Cancelled
                </span>
            @else
                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-clock-fill me-1"></i> Pending
                </span>
            @endif
        </div>
        <h1 class="page-header-title fs-3 fw-bold mb-0">{{ $expense->expense_title }}</h1>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($expense->remaining_amount > 0 && $expense->payment_status !== 'Cancelled')
            <button type="button" class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="bi bi-cash-stack me-1"></i> Record Payment
            </button>
        @endif
        <a href="{{ route('monthly_expenses.edit', $expense->id) }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('monthly_expenses.index') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Financial Status Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Bill / Amount</div>
            <h2 class="fw-bold text-dark mb-0">PKR {{ number_format($expense->amount, 2) }}</h2>
            <span class="text-muted small">Total amount due</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="text-success small fw-bold text-uppercase mb-1">Total Paid</div>
            <h2 class="fw-bold text-success mb-0">PKR {{ number_format($expense->paid_amount, 2) }}</h2>
            <span class="text-muted small">{{ $expense->payments->count() }} payment installment(s)</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="small fw-bold text-uppercase mb-1 {{ $expense->remaining_amount > 0 ? 'text-danger' : 'text-muted' }}">
                Remaining Balance
            </div>
            <h2 class="fw-bold mb-0 {{ $expense->remaining_amount > 0 ? 'text-danger' : 'text-muted' }}">
                PKR {{ number_format($expense->remaining_amount, 2) }}
            </h2>
            <span class="text-muted small">
                @if($expense->remaining_amount <= 0)
                    Fully Settled
                @elseif($expense->due_date)
                    Due on {{ $expense->due_date->format('d M Y') }}
                @else
                    Payable
                @endif
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Expense Particulars & Details -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>Expense Particulars
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">Category</span>
                        <strong class="text-dark">{{ $expense->category->name ?? 'General' }}</strong>
                        <span class="badge bg-light text-muted border px-1" style="font-size: 0.7rem;">{{ $expense->category->group ?? 'General' }}</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">Billing Period</span>
                        <strong class="text-dark">{{ \Carbon\Carbon::create($expense->expense_year, $expense->expense_month, 1)->format('F Y') }}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">Expense Date</span>
                        <strong class="text-dark">{{ $expense->expense_date ? $expense->expense_date->format('d M Y') : 'N/A' }}</strong>
                    </div>

                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">Provider / Vendor</span>
                        <strong class="text-dark">{{ $expense->provider_name ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">Bill Reference #</span>
                        <strong class="text-dark">{{ $expense->bill_reference ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">Due Date</span>
                        <strong class="{{ $expense->is_overdue ? 'text-danger' : 'text-dark' }}">
                            {{ $expense->due_date ? $expense->due_date->format('d M Y') : 'N/A' }}
                        </strong>
                    </div>

                    @if($expense->is_utility_bill)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Utility Information
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">Utility Type</span>
                                    <strong>{{ $expense->utility_type ?? 'Utility' }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">Consumer / Account #</span>
                                    <strong>{{ $expense->consumer_number ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">Bill Issue Date</span>
                                    <strong>{{ $expense->bill_issue_date ? $expense->bill_issue_date->format('d M Y') : 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($expense->recurring_expense_id)
                    <div class="col-12">
                        <div class="alert alert-info border-0 rounded-3 mb-0 small">
                            <i class="bi bi-arrow-repeat me-1"></i> Generated from Recurring Template: 
                            <strong>{{ $expense->recurringExpense->expense_name ?? 'Template' }}</strong>
                        </div>
                    </div>
                    @endif

                    @if($expense->notes)
                    <div class="col-12">
                        <span class="text-muted small d-block">Notes</span>
                        <div class="p-3 bg-light rounded-3 text-dark small">{{ $expense->notes }}</div>
                    </div>
                    @endif

                    <div class="col-12 pt-2 border-top text-muted small d-flex justify-content-between">
                        <span>Recorded by: <strong>{{ $expense->creator->name ?? 'System' }}</strong></span>
                        <span>Created: {{ $expense->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bill / Receipt Attachment -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-paperclip text-primary me-2"></i>Attached Bill / Document
                </h5>
            </div>
            <div class="card-body p-4">
                @if($expense->attachment_path)
                    <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                            <div>
                                <div class="fw-bold text-dark">{{ $expense->attachment_name ?? 'Bill Attachment' }}</div>
                                <span class="text-muted small">Protected storage document</span>
                            </div>
                        </div>
                        <a href="{{ route('monthly_expenses.attachment', $expense->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View / Download
                        </a>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-file-earmark-x fs-2 opacity-50 d-block mb-1"></i>
                        <span>No bill or receipt attached to this expense record.</span>
                        <div class="mt-2">
                            <a href="{{ route('monthly_expenses.edit', $expense->id) }}" class="btn btn-xs btn-outline-secondary rounded-pill">
                                Upload Attachment
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Installments History -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-clock-history text-success me-2"></i>Payment History
                    </h5>
                    <p class="text-muted small mb-0">Recorded payments and payment slips.</p>
                </div>
                @if($expense->remaining_amount > 0 && $expense->payment_status !== 'Cancelled')
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                        <i class="bi bi-plus-lg"></i> Pay
                    </button>
                @endif
            </div>
            <div class="card-body p-4">
                @if($expense->payments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($expense->payments as $payment)
                            <div class="list-group-item px-0 py-3 border-light">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="fw-bold text-success mb-0">PKR {{ number_format($payment->amount, 2) }}</h6>
                                        <span class="badge bg-light text-muted border px-2 py-0" style="font-size: 0.72rem;">{{ $payment->payment_method }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted small">{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</span>
                                        @if(auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->canDo('monthly_expenses.delete')))
                                            <form action="{{ route('monthly_expenses.payments.destroy', $payment->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Delete this payment installment? Balance will be recalculated.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs text-danger p-0 border-0" title="Delete Payment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    @if($payment->transaction_reference)
                                        <span>Ref: {{ $payment->transaction_reference }} | </span>
                                    @endif
                                    <span>Paid by: {{ $payment->paid_by ?? 'N/A' }}</span>
                                </div>
                                @if($payment->notes)
                                    <div class="small text-secondary mt-1 bg-light p-2 rounded">{{ $payment->notes }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-hourglass-split fs-2 opacity-50 d-block mb-2"></i>
                        <p class="mb-0">No payments recorded yet for this expense.</p>
                        @if($expense->payment_status !== 'Cancelled')
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill mt-3 px-3" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                                Record First Payment
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="recordPaymentModalLabel">
                    <i class="bi bi-cash-stack text-success me-2"></i>Record Expense Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('monthly_expenses.payments.store', $expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Remaining Balance:</span>
                        <strong class="text-danger fs-5">PKR {{ number_format($expense->remaining_amount, 2) }}</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Payment Amount (PKR) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $expense->remaining_amount }}" max="{{ $expense->remaining_amount }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Payment Date *</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Payment Method *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Online / Card">Online / Card</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Transaction Reference / Cheque #</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. Bank Ref #, Receipt #">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Paid By (Payer Name)</label>
                        <input type="text" name="paid_by" class="form-control" value="{{ auth()->user()->name ?? 'Finance' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Payment Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check-circle me-1"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
