@extends('layouts.app')

@section('title', 'Edit Polio Invoice: ' . $polioInvoice->invoice_number)

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Polio Certificate Invoice
        </h1>
        <p class="page-header-subtitle text-muted mb-0">
            Invoice: <span class="fw-bold text-dark">{{ $polioInvoice->invoice_number }}</span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('polio-invoices.show', $polioInvoice->id) }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Invoice
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
        <form action="{{ route('polio-invoices.update', $polioInvoice->id) }}" method="POST" id="editPolioInvoiceForm">
            @csrf
            @method('PUT')

            <!-- Top Meta Info -->
            <div class="row g-3 mb-4 pb-3 border-bottom align-items-center">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Invoice Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-primary fw-bold"><i class="bi bi-hash"></i></span>
                        <input type="text" class="form-control bg-light fw-bold text-primary fs-5" value="{{ $polioInvoice->invoice_number }}" readonly>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Invoice Date <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $polioInvoice->invoice_date ? $polioInvoice->invoice_date->format('Y-m-d') : '') }}" required>
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
                    <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $polioInvoice->client_name) }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $polioInvoice->phone) }}" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-muted">Passport Number <span class="text-muted fw-normal">(Optional)</span></label>
                    <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number', $polioInvoice->passport_number) }}">
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
                        <input type="number" name="persons" id="editPersonsInput" class="form-control" min="1" step="1" value="{{ old('persons', $polioInvoice->persons) }}" required>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Cost Per Person (PKR) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">PKR</span>
                        <input type="number" name="cost_per_person" id="editCostPerPersonInput" class="form-control" min="0" step="0.01" value="{{ old('cost_per_person', $polioInvoice->cost_per_person) }}" required>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Total Amount (PKR)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-primary fw-bold">PKR</span>
                        <input type="text" id="editTotalAmountDisplay" class="form-control bg-light fw-bold text-primary fs-5" value="{{ number_format($polioInvoice->total_amount, 2) }}" readonly>
                    </div>
                </div>
            </div>

            <!-- Settlement Read-only info -->
            <div class="row g-3 mb-4 pb-3 border-bottom">
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="text-muted small fw-semibold text-uppercase">Total Paid Recorded</div>
                        <div class="fs-5 fw-bold text-success mt-1">PKR {{ number_format($polioInvoice->amount_paid, 2) }}</div>
                        <div class="small text-muted">Managed via payment transactions history</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="text-muted small fw-semibold text-uppercase">Current Status</div>
                        <div class="mt-1">
                            @if($polioInvoice->payment_status === 'Paid')
                                <span class="badge bg-success px-3 py-1.5 fs-6 rounded-pill">Paid</span>
                            @elseif($polioInvoice->payment_status === 'Partially Paid')
                                <span class="badge bg-warning text-dark px-3 py-1.5 fs-6 rounded-pill">Partially Paid</span>
                            @else
                                <span class="badge bg-secondary px-3 py-1.5 fs-6 rounded-pill">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Notes / Remarks</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes', $polioInvoice->notes) }}">
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('polio-invoices.show', $polioInvoice->id) }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                    <i class="bi bi-check2-circle me-1.5"></i> Update Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const personsInput = document.getElementById('editPersonsInput');
    const costPerPersonInput = document.getElementById('editCostPerPersonInput');
    const totalAmountDisplay = document.getElementById('editTotalAmountDisplay');

    function recalculate() {
        const persons = parseFloat(personsInput.value) || 0;
        const costPerPerson = parseFloat(costPerPersonInput.value) || 0;
        const totalAmount = Math.round((persons * costPerPerson) * 100) / 100;
        totalAmountDisplay.value = totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    personsInput.addEventListener('input', recalculate);
    costPerPersonInput.addEventListener('input', recalculate);
});
</script>
@endsection
