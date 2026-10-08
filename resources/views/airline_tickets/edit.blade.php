@extends('layouts.app')

@section('title', 'Edit Ticket: ' . $ticket->pnr)

@section('content')
<div class="page-header mb-4">
    <h1 class="page-header-title fs-3 fw-bold mb-1">Edit Airline Ticket</h1>
    <p class="page-header-subtitle text-muted mb-0">Update ticket #{{ $ticket->id }} - PNR: {{ $ticket->pnr }}</p>
</div>

<form action="{{ route('airline-tickets.update', $ticket->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row g-4">
        <!-- Main Ticket Details -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ticket Status *</label>
                            <select name="ticket_status" class="form-select" required>
                                <option value="Reserved" {{ $ticket->ticket_status == 'Reserved' ? 'selected' : '' }}>Reserved</option>
                                <option value="Issued" {{ $ticket->ticket_status == 'Issued' ? 'selected' : '' }}>Issued</option>
                                <option value="Confirmed" {{ $ticket->ticket_status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="On Hold" {{ $ticket->ticket_status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Cancelled" {{ $ticket->ticket_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="Void" {{ $ticket->ticket_status == 'Void' ? 'selected' : '' }}>Void</option>
                                <option value="Reissued" {{ $ticket->ticket_status == 'Reissued' ? 'selected' : '' }}>Reissued</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">PNR (Record Locator) *</label>
                            <input type="text" name="pnr" class="form-control text-uppercase" value="{{ $ticket->pnr }}" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Issue Date *</label>
                            <input type="date" name="ticket_date" class="form-control" value="{{ \Carbon\Carbon::parse($ticket->ticket_date)->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financials -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Financials</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Basic Fare</label>
                            <input type="number" step="0.01" name="basic_fare" id="basic_fare" class="form-control" value="{{ $ticket->basic_fare }}" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Taxes</label>
                            <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" value="{{ $ticket->tax_amount }}" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier Total (Net)</label>
                            <input type="number" step="0.01" name="supplier_total" id="supplier_total" class="form-control" value="{{ $ticket->supplier_total }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-primary">Selling Price (Total) *</label>
                            <input type="number" step="0.01" name="total_fare" id="total_fare" class="form-control" value="{{ $ticket->total_fare }}" oninput="calculateTotals()" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 mb-5">
        <a href="{{ route('airline-tickets.show', $ticket->id) }}" class="btn btn-light border">Cancel</a>
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Update Ticket</button>
    </div>
</form>

<script>
    function calculateTotals() {
        const basic = parseFloat(document.getElementById('basic_fare').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        
        const supplier = basic + tax;
        document.getElementById('supplier_total').value = supplier.toFixed(2);
    }
</script>
@endsection
