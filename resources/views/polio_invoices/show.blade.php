@extends('layouts.app')

@section('title', 'Polio Invoice: ' . $polioInvoice->invoice_number)

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-semibold">
                Polio Certificate Service
            </span>
            <span class="text-muted small">&bull; {{ $polioInvoice->invoice_date ? $polioInvoice->invoice_date->format('d M Y') : '—' }}</span>
        </div>
        <h1 class="page-header-title fs-3 fw-bold mb-1 mt-1 text-dark">
            Invoice: {{ $polioInvoice->invoice_number }}
        </h1>
        <div class="text-muted small">
            Created on {{ $polioInvoice->created_at->format('d M Y, h:i A') }}
            @if($polioInvoice->creator)
                by {{ $polioInvoice->creator->name }}
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('polio-invoices.print', $polioInvoice->id) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
            <i class="bi bi-printer me-1.5"></i> Print Invoice
        </a>
        <a href="{{ route('polio-invoices.pdf', $polioInvoice->id) }}" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
            <i class="bi bi-file-earmark-pdf me-1.5"></i> Download PDF
        </a>
        <a href="{{ route('polio-invoices.edit', $polioInvoice->id) }}" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-pencil me-1.5"></i> Edit
        </a>
        <a href="{{ route('polio-invoices.index') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1.5"></i> Back to Listing
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4 mb-4">
    <!-- Client & Invoice Info Card -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-vcard text-primary me-2"></i>Client Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-person-fill fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $polioInvoice->client_name }}</h4>
                        <div class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $polioInvoice->phone }}</div>
                    </div>
                </div>

                <ul class="list-group list-group-flush border-top">
                    <li class="list-group-item px-0 py-2.5 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-muted small">Invoice Number</span>
                        <span class="fw-bold text-primary font-monospace">{{ $polioInvoice->invoice_number }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2.5 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-muted small">Invoice Date</span>
                        <span class="fw-semibold">{{ $polioInvoice->invoice_date ? $polioInvoice->invoice_date->format('d M Y') : '—' }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2.5 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-muted small">Passport Number</span>
                        <span class="fw-semibold">{{ $polioInvoice->passport_number ?? 'Not Provided' }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2.5 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-muted small">Initial Payment Method</span>
                        <span class="fw-semibold">{{ $polioInvoice->payment_method ?? '—' }}</span>
                    </li>
                    @if($polioInvoice->notes)
                    <li class="list-group-item px-0 py-2.5 bg-transparent">
                        <span class="text-muted small d-block mb-1">Notes / Remarks</span>
                        <span class="small bg-light p-2 rounded d-block border">{{ $polioInvoice->notes }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Settlement Overview Card -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-cash-stack text-success me-2"></i>Financial Summary
                </h5>
                <div>
                    @if($polioInvoice->payment_status === 'Paid')
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fs-6 fw-bold">PAID IN FULL</span>
                    @elseif($polioInvoice->payment_status === 'Partially Paid')
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fs-6 fw-bold">PARTIALLY PAID</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 fs-6 fw-bold">PENDING / UNPAID</span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50 text-center">
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Billed</div>
                            <div class="fs-4 fw-bold text-dark mt-1">PKR {{ number_format($polioInvoice->total_amount, 2) }}</div>
                            <div class="text-muted small">{{ $polioInvoice->persons }} &times; {{ number_format($polioInvoice->cost_per_person, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50 text-center">
                            <div class="text-success small fw-semibold text-uppercase" style="font-size: 0.7rem;">Amount Paid</div>
                            <div class="fs-4 fw-bold text-success mt-1">PKR {{ number_format($polioInvoice->amount_paid, 2) }}</div>
                            <div class="text-muted small">{{ $polioInvoice->payments->count() }} {{ Str::plural('receipt', $polioInvoice->payments->count()) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50 text-center">
                            <div class="text-danger small fw-semibold text-uppercase" style="font-size: 0.7rem;">Balance Due</div>
                            <div class="fs-4 fw-bold text-danger mt-1">PKR {{ number_format($polioInvoice->balance_due, 2) }}</div>
                            <div class="text-muted small">{{ $polioInvoice->balance_due > 0 ? 'Remaining' : 'Settled' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Itemized Cost Table -->
                <div class="table-responsive rounded-3 border mb-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-uppercase text-muted">
                                <th class="ps-3">Service Description</th>
                                <th class="text-center">Persons (Qty)</th>
                                <th class="text-end">Unit Cost (PKR)</th>
                                <th class="text-end pe-3">Total (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 fw-semibold">
                                    Polio Certificate Service
                                    <div class="text-muted small">Official polio vaccination certificate issuance processing</div>
                                </td>
                                <td class="text-center fw-bold">{{ $polioInvoice->persons }}</td>
                                <td class="text-end">{{ number_format($polioInvoice->cost_per_person, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-primary">{{ number_format($polioInvoice->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end ps-3">Total Amount:</th>
                                <th class="text-end pe-3 text-dark">PKR {{ number_format($polioInvoice->total_amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end ps-3 text-success">Total Paid:</th>
                                <th class="text-end pe-3 text-success">PKR {{ number_format($polioInvoice->amount_paid, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end ps-3 text-danger">Balance Due:</th>
                                <th class="text-end pe-3 text-danger">PKR {{ number_format($polioInvoice->balance_due, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($polioInvoice->balance_due > 0)
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="bi bi-plus-circle me-1.5"></i> Record Payment Installment
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment History Table Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>Payment Installments History
            </h5>
            <div class="text-muted small">Itemized chronological record of all payments received against this invoice</div>
        </div>
        @if($polioInvoice->balance_due > 0)
        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="bi bi-plus-circle me-1"></i> Add Payment
        </button>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-uppercase text-muted">
                        <th class="ps-4">#</th>
                        <th>Payment Date</th>
                        <th class="text-end">Amount Paid</th>
                        <th>Payment Method</th>
                        <th>Reference / Transaction ID</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($polioInvoice->payments as $index => $pay)
                    <tr>
                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                        <td class="fw-semibold">{{ $pay->payment_date ? $pay->payment_date->format('d M Y') : '—' }}</td>
                        <td class="text-end fw-bold text-success">PKR {{ number_format($pay->amount, 2) }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $pay->payment_method ?? 'Cash' }}</span></td>
                        <td>{{ $pay->transaction_reference ?? '—' }}</td>
                        <td>{{ $pay->creator->name ?? 'System' }}</td>
                        <td><small class="text-muted">{{ $pay->notes ?? '—' }}</small></td>
                        <td class="text-end pe-4">
                            @if(auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->canDo('polio_invoices.delete')))
                            <form action="{{ route('polio-invoices.payments.destroy', $pay->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment installment? Balance will be recalculated.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger border-0 p-1" title="Delete Payment">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-hourglass text-warning fs-3 d-block mb-1"></i>
                            No payments recorded yet. Status is pending.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Record Payment Installment -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addPaymentModalLabel">
                    <i class="bi bi-credit-card text-success me-2"></i>Record Payment Installment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('polio-invoices.payments.store', $polioInvoice->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Invoice Amount:</span>
                            <span class="fw-bold">PKR {{ number_format($polioInvoice->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Previously Paid:</span>
                            <span class="text-success fw-bold">PKR {{ number_format($polioInvoice->amount_paid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-1 mt-1">
                            <span class="text-danger fw-bold small">Current Remaining Balance:</span>
                            <span class="text-danger fw-bold">PKR {{ number_format($polioInvoice->balance_due, 2) }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Payment Amount (PKR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-success">PKR</span>
                            <input type="number" name="amount" class="form-control" min="0.01" max="{{ $polioInvoice->balance_due }}" step="0.01" value="{{ $polioInvoice->balance_due }}" required>
                        </div>
                        <div class="form-text">Maximum payable: PKR {{ number_format($polioInvoice->balance_due, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Online / Raast">Online / Raast / JazzCash / EasyPaisa</option>
                            <option value="Card">Credit / Debit Card</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Transaction Reference <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. Bank Ref #, Receipt #">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Notes <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="notes" class="form-control" placeholder="Any remarks...">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
