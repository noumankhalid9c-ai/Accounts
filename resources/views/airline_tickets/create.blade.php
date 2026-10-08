@extends('layouts.app')

@section('title', 'New Airline Ticket')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-header-title fs-3 fw-bold mb-1">New Airline Ticket</h1>
    <p class="page-header-subtitle text-muted mb-0">Record a new ticket, flight segments, and passenger details.</p>
</div>

<form action="{{ route('airline-tickets.store') }}" method="POST" id="ticketForm">
    @csrf
    
    <div class="row g-4">
        <!-- Main Ticket Details -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">1. Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Client Type *</label>
                            <select id="clientType" class="form-select" onchange="toggleClientFields()" required>
                                <option value="retail">Retail Customer</option>
                                <option value="b2b">B2B Agent</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="retailCustomerDiv">
                            <label class="form-label">Retail Customer *</label>
                            <select name="customer_id" id="customer_id" class="form-select">
                                <option value="">Select Customer...</option>
                                @foreach(\App\Models\Customer::all() as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-none" id="b2bAgentDiv">
                            <label class="form-label">B2B Agent *</label>
                            <select name="b2b_agent_id" id="b2b_agent_id" class="form-select">
                                <option value="">Select Agent...</option>
                                @foreach(\App\Models\B2bAgent::all() as $a)
                                    <option value="{{ $a->id }}">{{ $a->company_name ?? $a->agent_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">PNR (Record Locator) *</label>
                            <input type="text" name="pnr" class="form-control text-uppercase" placeholder="e.g. A3B89X" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Issue Date *</label>
                            <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Ticket Status *</label>
                            <select name="ticket_status" class="form-select" required>
                                <option value="Issued">Issued</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flight Segments -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">2. Flight Segments</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSegment()">
                        <i class="bi bi-plus-circle"></i> Add Segment
                    </button>
                </div>
                <div class="card-body">
                    <div id="segmentsContainer">
                        <!-- First Segment -->
                        <div class="segment-row mb-3 p-3 border rounded bg-light position-relative">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label small">Airline *</label>
                                    <select name="segments[0][airline_id]" class="form-select form-select-sm" required>
                                        <option value="">Select...</option>
                                        @foreach(\App\Models\Airline::where('status', true)->get() as $a)
                                            <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Flight # *</label>
                                    <input type="text" name="segments[0][flight_number]" class="form-control form-control-sm text-uppercase" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">From (Airport) *</label>
                                    <select name="segments[0][departure_airport_id]" class="form-select form-select-sm" required>
                                        <option value="">Select...</option>
                                        @foreach(\App\Models\Airport::where('status', true)->get() as $ap)
                                            <option value="{{ $ap->id }}">{{ $ap->iata_code }} - {{ $ap->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">To (Airport) *</label>
                                    <select name="segments[0][arrival_airport_id]" class="form-select form-select-sm" required>
                                        <option value="">Select...</option>
                                        @foreach(\App\Models\Airport::where('status', true)->get() as $ap)
                                            <option value="{{ $ap->id }}">{{ $ap->iata_code }} - {{ $ap->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Dep Date & Time *</label>
                                    <input type="date" name="segments[0][departure_date]" class="form-control form-control-sm mb-1" required>
                                    <input type="time" name="segments[0][departure_time]" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Arr Date & Time *</label>
                                    <input type="date" name="segments[0][arrival_date]" class="form-control form-control-sm mb-1" required>
                                    <input type="time" name="segments[0][arrival_time]" class="form-control form-control-sm" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Passengers -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">3. Passengers</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPassenger()">
                        <i class="bi bi-person-plus"></i> Add Passenger
                    </button>
                </div>
                <div class="card-body">
                    <div id="passengersContainer">
                        <!-- First Passenger -->
                        <div class="passenger-row mb-2 p-2 border rounded d-flex gap-2 align-items-start bg-light">
                            <div class="flex-grow-1">
                                <label class="form-label small">Passenger Name (As per passport) *</label>
                                <input type="text" name="passengers[0][passenger_name]" class="form-control form-control-sm text-uppercase" placeholder="SURNAME / GIVEN NAME" required>
                            </div>
                            <div style="width: 150px;">
                                <label class="form-label small">Type *</label>
                                <select name="passengers[0][passenger_type]" class="form-select form-select-sm" required>
                                    <option value="Adult">Adult</option>
                                    <option value="Child">Child</option>
                                    <option value="Infant">Infant</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label small">Ticket Number (Optional)</label>
                                <input type="text" name="passengers[0][ticket_number]" class="form-control form-control-sm text-uppercase">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financials -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">4. Financials</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Basic Fare</label>
                            <input type="number" step="0.01" name="basic_fare" id="basic_fare" class="form-control" value="0" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Taxes</label>
                            <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" value="0" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier Total (Net)</label>
                            <input type="number" step="0.01" name="supplier_total" id="supplier_total" class="form-control" value="0" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-primary">Selling Price (Total) *</label>
                            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="0" oninput="calculateTotals()" required>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="col-md-4">
                            <label class="form-label">Amount Paid Received</label>
                            <input type="number" step="0.01" name="amount_paid" class="form-control" value="0">
                            <div class="form-text">Advance payment received today.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit Card">Credit Card</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Profit (Est.)</label>
                            <input type="number" step="0.01" id="est_profit" class="form-control" value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 mb-5">
        <a href="{{ route('airline-tickets.index') }}" class="btn btn-light border">Cancel</a>
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Ticket & Open Invoice</button>
    </div>
</form>

<!-- Templates for JS -->
<template id="segmentTemplate">
    <div class="segment-row mb-3 p-3 border rounded bg-light position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.closest('.segment-row').remove()"></button>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-2">
                <label class="form-label small">Airline *</label>
                <select name="segments[INDEX][airline_id]" class="form-select form-select-sm" required>
                    <option value="">Select...</option>
                    @foreach(\App\Models\Airline::where('status', true)->get() as $a)
                        <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Flight # *</label>
                <input type="text" name="segments[INDEX][flight_number]" class="form-control form-control-sm text-uppercase" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">From *</label>
                <select name="segments[INDEX][departure_airport_id]" class="form-select form-select-sm" required>
                    <option value="">Select...</option>
                    @foreach(\App\Models\Airport::where('status', true)->get() as $ap)
                        <option value="{{ $ap->id }}">{{ $ap->iata_code }} - {{ $ap->city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">To *</label>
                <select name="segments[INDEX][arrival_airport_id]" class="form-select form-select-sm" required>
                    <option value="">Select...</option>
                    @foreach(\App\Models\Airport::where('status', true)->get() as $ap)
                        <option value="{{ $ap->id }}">{{ $ap->iata_code }} - {{ $ap->city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Dep Date & Time *</label>
                <input type="date" name="segments[INDEX][departure_date]" class="form-control form-control-sm mb-1" required>
                <input type="time" name="segments[INDEX][departure_time]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Arr Date & Time *</label>
                <input type="date" name="segments[INDEX][arrival_date]" class="form-control form-control-sm mb-1" required>
                <input type="time" name="segments[INDEX][arrival_time]" class="form-control form-control-sm" required>
            </div>
        </div>
    </div>
</template>

<template id="passengerTemplate">
    <div class="passenger-row mb-2 p-2 border rounded d-flex gap-2 align-items-start bg-light position-relative pe-5">
        <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" onclick="this.closest('.passenger-row').remove()"></button>
        <div class="flex-grow-1">
            <label class="form-label small">Passenger Name *</label>
            <input type="text" name="passengers[INDEX][passenger_name]" class="form-control form-control-sm text-uppercase" placeholder="SURNAME / GIVEN NAME" required>
        </div>
        <div style="width: 150px;">
            <label class="form-label small">Type *</label>
            <select name="passengers[INDEX][passenger_type]" class="form-select form-select-sm" required>
                <option value="Adult">Adult</option>
                <option value="Child">Child</option>
                <option value="Infant">Infant</option>
            </select>
        </div>
        <div class="flex-grow-1">
            <label class="form-label small">Ticket Number (Optional)</label>
            <input type="text" name="passengers[INDEX][ticket_number]" class="form-control form-control-sm text-uppercase">
        </div>
    </div>
</template>

<script>
    let segmentIndex = 1;
    let passengerIndex = 1;

    function toggleClientFields() {
        const type = document.getElementById('clientType').value;
        const retailDiv = document.getElementById('retailCustomerDiv');
        const b2bDiv = document.getElementById('b2bAgentDiv');
        const customerId = document.getElementById('customer_id');
        const agentId = document.getElementById('b2b_agent_id');
        
        if (type === 'retail') {
            retailDiv.classList.remove('d-none');
            b2bDiv.classList.add('d-none');
            customerId.required = true;
            agentId.required = false;
            agentId.value = '';
        } else {
            retailDiv.classList.add('d-none');
            b2bDiv.classList.remove('d-none');
            customerId.required = false;
            agentId.required = true;
            customerId.value = '';
        }
    }

    function addSegment() {
        const template = document.getElementById('segmentTemplate').innerHTML;
        const html = template.replace(/INDEX/g, segmentIndex++);
        document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', html);
    }

    function addPassenger() {
        const template = document.getElementById('passengerTemplate').innerHTML;
        const html = template.replace(/INDEX/g, passengerIndex++);
        document.getElementById('passengersContainer').insertAdjacentHTML('beforeend', html);
    }

    function calculateTotals() {
        const basic = parseFloat(document.getElementById('basic_fare').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const total = parseFloat(document.getElementById('total_amount').value) || 0;
        
        const supplier = basic + tax;
        document.getElementById('supplier_total').value = supplier.toFixed(2);
        
        const profit = total - supplier;
        document.getElementById('est_profit').value = profit.toFixed(2);
    }
</script>
@endsection
