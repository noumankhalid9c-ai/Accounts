@extends('layouts.app')

@section('title', 'Ticket Details: ' . $ticket->pnr)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Airline Ticket: {{ $ticket->pnr }}</h1>
        <p class="text-muted mb-0">View complete details of the ticket and segments.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('airline-tickets.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="{{ route('airline-tickets.print', $ticket->id) }}" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-printer"></i> Print Ticket
        </a>
        <a href="{{ route('airline-tickets.invoice', $ticket->id) }}" class="btn btn-primary">
            <i class="bi bi-receipt"></i> Invoice
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <!-- Ticket Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Ticket Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Ticket ID</div>
                    <div class="col-sm-8 fw-bold">#{{ $ticket->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">PNR</div>
                    <div class="col-sm-8 fw-bold">{{ $ticket->pnr }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Status</div>
                    <div class="col-sm-8">
                        @if($ticket->ticket_status == 'Confirmed' || $ticket->ticket_status == 'Issued')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">{{ $ticket->ticket_status }}</span>
                        @elseif($ticket->ticket_status == 'Cancelled' || $ticket->ticket_status == 'Void')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">{{ $ticket->ticket_status }}</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">{{ $ticket->ticket_status }}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Issue Date</div>
                    <div class="col-sm-8">{{ \Carbon\Carbon::parse($ticket->issue_date)->format('d M Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Client / B2B Agent</div>
                    <div class="col-sm-8">
                        @if($ticket->customer_id)
                            <a href="{{ route('crm.customers.show', $ticket->customer_id) }}">{{ $ticket->customer->name ?? '' }}</a>
                            <span class="badge bg-info bg-opacity-10 text-info ms-2">Retail</span>
                        @elseif($ticket->b2b_agent_id)
                            <a href="#">{{ $ticket->b2bAgent->company_name ?? '' }}</a>
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">B2B</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Passengers -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Passengers</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Ticket Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ticket->passengers as $pax)
                            <tr>
                                <td class="fw-bold">{{ $pax->passenger_name }}</td>
                                <td>{{ $pax->passenger_type }}</td>
                                <td>{{ $pax->ticket_number ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Flight Segments -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Flight Itinerary</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Airline</th>
                                <th>Flight</th>
                                <th>Departure</th>
                                <th>Arrival</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ticket->segments as $seg)
                            <tr>
                                <td>
                                    @if($seg->airline)
                                        <div class="fw-bold">{{ $seg->airline->name }}</div>
                                        <div class="small text-muted">{{ $seg->airline->code }}</div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $seg->flight_number }}</td>
                                <td>
                                    <div class="fw-bold">{{ $seg->departureAirport->iata_code ?? '' }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($seg->departure_date)->format('d M Y') }} at {{ \Carbon\Carbon::parse($seg->departure_time)->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $seg->arrivalAirport->iata_code ?? '' }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($seg->arrival_date)->format('d M Y') }} at {{ \Carbon\Carbon::parse($seg->arrival_time)->format('H:i') }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Financials Sidebar -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Financial Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Basic Fare</span>
                    <span>Rs {{ number_format($ticket->basic_fare, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Taxes</span>
                    <span>Rs {{ number_format($ticket->tax_amount, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Net Cost (Supplier)</span>
                    <span class="fw-bold">Rs {{ number_format($ticket->supplier_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Selling Price</span>
                    <span class="fw-bold text-primary">Rs {{ number_format($ticket->total_amount, 2) }}</span>
                </div>
                
                @if(Auth::user()->role === 'Admin' || Auth::user()->role === 'Manager')
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Estimated Profit</span>
                    <span class="fw-bold text-success">Rs {{ number_format($ticket->estimated_profit, 2) }}</span>
                </div>
                @endif
                
                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Amount Paid</span>
                    <span class="fw-bold text-success">Rs {{ number_format($ticket->amount_paid, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Balance Due</span>
                    <span class="fw-bold {{ $ticket->amount_due > 0 ? 'text-danger' : 'text-success' }}">
                        Rs {{ number_format($ticket->amount_due, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Payments -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Payments</h5>
                @if($ticket->amount_due > 0)
                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-plus"></i> Add
                </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            @forelse($ticket->payments as $payment)
                            <tr>
                                <td>
                                    <div class="small fw-bold">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</div>
                                    <div class="small text-muted">{{ $payment->payment_method }}</div>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rs {{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted small py-3">No payments recorded.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('airline-tickets.payments.store', $ticket->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ $ticket->amount_due }}" max="{{ $ticket->amount_due }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference (Optional)</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Check/Transaction ID">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
