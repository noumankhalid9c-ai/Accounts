@extends('layouts.app')

@section('title', 'Create Polio Certificate Invoice')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-file-earmark-plus text-primary me-2"></i>Create Polio Certificate Invoice
        </h1>
        <p class="page-header-subtitle text-muted mb-0">
            Generate a service invoice for client polio vaccination certificates.
        </p>
    </div>
    <div>
        <a href="{{ route('polio-invoices.index') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Invoices
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('polio-invoices.store') }}" method="POST" id="polioInvoiceForm">
            @csrf

            <!-- Top Meta Info -->
            <div class="row g-3 mb-4 pb-3 border-bottom align-items-center">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Invoice Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-primary fw-bold"><i class="bi bi-hash"></i></span>
                        <input type="text" class="form-control bg-light fw-bold text-primary fs-5" value="{{ $nextInvoiceNumber }}" readonly>
                    </div>
                    <div class="form-text">Invoice numbers are automatically generated sequentially.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Invoice Date <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $defaultDate) }}" required>
                    </div>
                </div>
            </div>

            <!-- Client Details Section -->
            <h5 class="fw-bold mb-3 text-dark">
                <i class="bi bi-person text-primary me-2"></i>Client Details
            </h5>
            <div class="row g-3 mb-4 pb-3 border-bottom">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-bold text-muted">Client Name <span class="text-danger">*</span></label>
                    <input type="text" name="client_name" class="form-control" placeholder="e.g. Ahmed Khan" value="{{ old('client_name') }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g. 03001234567" value="{{ old('phone') }}" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-muted">Passport Number <span class="text-muted fw-normal">(Optional)</span></label>
                    <input type="text" name="passport_number" class="form-control" placeholder="e.g. AB1234567" value="{{ old('passport_number') }}">
                </div>
            </div>

            <!-- Service Cost Calculation Section -->
            <h5 class="fw-bold mb-3 text-dark">
                <i class="bi bi-calculator text-primary me-2"></i>Cost & Pricing
            </h5>
            <div class="row g-3 mb-4 pb-3 border-bottom">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Number of Persons <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-people"></i></span>
                        <input type="number" name="persons" id="personsInput" class="form-control" min="1" step="1" value="{{ old('persons', 1) }}" required>
                    </div>
                    <div class="form-text">Default: 1 person</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Cost Per Person (PKR) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">PKR</span>
                        <input type="number" name="cost_per_person" id="costPerPersonInput" class="form-control" min="0" step="0.01" placeholder="e.g. 3000" value="{{ old('cost_per_person') }}" required>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Total Amount (PKR)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-primary fw-bold">PKR</span>
                        <input type="text" id="totalAmountDisplay" class="form-control bg-light fw-bold text-primary fs-5" value="0.00" readonly>
                    </div>
                    <div class="form-text">Auto-calculated: Persons &times; Cost per person</div>
                </div>
            </div>

            <!-- Payment & Settlement Section -->
            <h5 class="fw-bold mb-3 text-dark">
                <i class="bi bi-wallet2 text-primary me-2"></i>Payment Details
            </h5>
            <div class="row g-3 mb-4 pb-3 border-bottom">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Amount Paid (PKR)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-success fw-bold">PKR</span>
                        <input type="number" name="amount_paid" id="amountPaidInput" class="form-control" min="0" step="0.01" placeholder="0.00" value="{{ old('amount_paid', 0) }}">
                    </div>
                    <div class="form-text">Enter initial payment or leave 0 for Pending.</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Balance Due (PKR)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-danger fw-bold">PKR</span>
                        <input type="text" id="balanceDueDisplay" class="form-control bg-light fw-bold text-danger fs-5" value="0.00" readonly>
                    </div>
                    <div class="form-text">Auto-calculated: Total Amount &minus; Amount Paid</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Payment Status</label>
                    <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between" style="min-height: 48px;">
                        <span id="paymentStatusBadge" class="badge bg-secondary px-3 py-2 fs-6 rounded-pill">Pending</span>
                        <small class="text-muted">Auto-determined</small>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted">Payment Method <span class="text-muted fw-normal">(If paid)</span></label>
                    <select name="payment_method" class="form-select">
                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Online / Raast" {{ old('payment_method') == 'Online / Raast' ? 'selected' : '' }}>Online / Raast / JazzCash / EasyPaisa</option>
                        <option value="Card" {{ old('payment_method') == 'Card' ? 'selected' : '' }}>Credit / Debit Card</option>
                        <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted">Notes / Reference <span class="text-muted fw-normal">(Optional)</span></label>
                    <input type="text" name="notes" class="form-control" placeholder="Any remarks, clinic info or transaction details..." value="{{ old('notes') }}">
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('polio-invoices.index') }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                    <i class="bi bi-check2-circle me-1.5"></i> Save & Generate Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const personsInput = document.getElementById('personsInput');
    const costPerPersonInput = document.getElementById('costPerPersonInput');
    const amountPaidInput = document.getElementById('amountPaidInput');
    const totalAmountDisplay = document.getElementById('totalAmountDisplay');
    const balanceDueDisplay = document.getElementById('balanceDueDisplay');
    const statusBadge = document.getElementById('paymentStatusBadge');

    function recalculate() {
        const persons = parseFloat(personsInput.value) || 0;
        const costPerPerson = parseFloat(costPerPersonInput.value) || 0;
        const amountPaid = parseFloat(amountPaidInput.value) || 0;

        const totalAmount = Math.round((persons * costPerPerson) * 100) / 100;
        const balanceDue = Math.max(0, Math.round((totalAmount - amountPaid) * 100) / 100);

        totalAmountDisplay.value = totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        balanceDueDisplay.value = balanceDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (totalAmount > 0 && amountPaid >= totalAmount) {
            statusBadge.className = 'badge bg-success px-3 py-2 fs-6 rounded-pill';
            statusBadge.textContent = 'Paid';
        } else if (amountPaid > 0) {
            statusBadge.className = 'badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill';
            statusBadge.textContent = 'Partially Paid';
        } else {
            statusBadge.className = 'badge bg-secondary px-3 py-2 fs-6 rounded-pill';
            statusBadge.textContent = 'Pending';
        }
    }

    personsInput.addEventListener('input', recalculate);
    costPerPersonInput.addEventListener('input', recalculate);
    amountPaidInput.addEventListener('input', recalculate);

    recalculate();
});
</script>
@endsection
