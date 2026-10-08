@extends('layouts.app')

@section('title', 'Ticket Invoice: ' . $ticket->pnr)

@section('content')
<style>
    .invoice-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .invoice-header {
        border-bottom: 2px solid #2b2c68;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }
    @media print {
        body { background-color: white; }
        .navbar, .sidebar, .btn, footer { display: none !important; }
        .invoice-card { box-shadow: none; border: none; }
        main { padding: 0 !important; margin: 0 !important; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Invoice: INV-TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('airline-tickets.show', $ticket->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Ticket
        </a>
        <a href="{{ route('airline-tickets.invoice.pdf', $ticket->id) }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Download PDF
        </a>
        <button onclick="window.print()" class="btn btn-success">
            <i class="bi bi-printer"></i> Print
        </button>
    </div>
</div>

<div class="card invoice-card mb-4">
    <div class="card-body p-5">
        <!-- Header -->
        <div class="row invoice-header align-items-center">
            <div class="col-sm-6">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('MADINA-LOGO-3.png') }}" alt="Company Logo" class="me-3" style="max-height: 80px; object-fit: contain;">
                    <div>
                        <h2 class="fw-bold mb-1" style="color: #2b2c68;">MACT Travel & Tours</h2>
                        <div class="text-muted">
                            123 Business Avenue, Block A<br>
                            City, Country<br>
                            Phone: +1 234 567 8900<br>
                            Email: info@macttravel.com
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                <h3 class="fw-bold" style="color: #484baf;">INVOICE / E-TICKET</h3>
                <div class="text-muted mt-2">
                    <strong>Invoice #:</strong> INV-TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($ticket->issue_date)->format('d M Y') }}<br>
                    <strong>PNR:</strong> <span class="badge bg-light text-dark fs-6">{{ $ticket->pnr }}</span>
                </div>
            </div>
        </div>

        <!-- Billing & Info -->
        <div class="row mb-5">
            <div class="col-sm-6">
                <h6 class="text-uppercase text-muted fw-bold mb-3">Billed To:</h6>
                @if($ticket->customer_id)
                    <h5 class="fw-bold mb-1">{{ $ticket->customer->name ?? '' }}</h5>
                    <div class="text-muted">
                        Phone: {{ $ticket->customer->phone ?? 'N/A' }}<br>
                        Email: {{ $ticket->customer->email ?? 'N/A' }}<br>
                        Address: {{ $ticket->customer->address ?? 'N/A' }}
                    </div>
                @elseif($ticket->b2b_agent_id)
                    <h5 class="fw-bold mb-1">{{ $ticket->b2bAgent->company_name ?? '' }}</h5>
                    <div class="text-muted">
                        Agent: {{ $ticket->b2bAgent->agent_name ?? '' }}<br>
                        Phone: {{ $ticket->b2bAgent->phone ?? 'N/A' }}<br>
                        Email: {{ $ticket->b2bAgent->email ?? 'N/A' }}
                    </div>
                @endif
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-uppercase text-muted fw-bold mb-3">Payment Status:</h6>
                @if($ticket->payment_status == 'Paid')
                    <h4 class="text-success fw-bold mb-1">PAID IN FULL</h4>
                @elseif($ticket->payment_status == 'Partial')
                    <h4 class="text-warning fw-bold mb-1">PARTIAL PAYMENT</h4>
                @else
                    <h4 class="text-danger fw-bold mb-1">UNPAID</h4>
                @endif
                <div class="text-muted mt-2">
                    Amount Paid: Rs {{ number_format($ticket->amount_paid, 2) }}<br>
                    Balance Due: <strong>Rs {{ number_format($ticket->amount_due, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Passengers -->
        <h6 class="text-uppercase text-muted fw-bold mb-3">Passenger Details</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Passenger Name</th>
                        <th>Type</th>
                        <th>Ticket Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticket->passengers as $pax)
                    <tr>
                        <td class="fw-bold">{{ $pax->passenger_name }}</td>
                        <td>{{ $pax->passenger_type }}</td>
                        <td>{{ $pax->ticket_number ?? 'TBA' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Flight Itinerary -->
        <h6 class="text-uppercase text-muted fw-bold mb-3">Flight Itinerary</h6>
        <div class="table-responsive mb-5">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Flight</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticket->segments as $seg)
                    <tr>
                        <td>
                            <strong>{{ $seg->airline->name ?? '' }}</strong><br>
                            Flight: {{ $seg->flight_number }}
                        </td>
                        <td>
                            <div class="fw-bold">{{ $seg->departureAirport->iata_code ?? '' }} - {{ $seg->departureAirport->city ?? '' }}</div>
                            <div class="small">{{ \Carbon\Carbon::parse($seg->departure_date)->format('d M Y') }} at {{ \Carbon\Carbon::parse($seg->departure_time)->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $seg->arrivalAirport->iata_code ?? '' }} - {{ $seg->arrivalAirport->city ?? '' }}</div>
                            <div class="small">{{ \Carbon\Carbon::parse($seg->arrival_date)->format('d M Y') }} at {{ \Carbon\Carbon::parse($seg->arrival_time)->format('H:i') }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Financial Totals -->
        <div class="row">
            <div class="col-sm-6">
                <!-- QR Code verification link -->
                <div class="mt-4 p-3 bg-light rounded text-center d-inline-block">
                    <p class="small text-muted mb-2">Scan to verify this ticket's authenticity</p>
                    @php
                        $verifyUrl = route('airline-tickets.verify', $ticket->qr_token);
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($verifyUrl) }}" alt="QR Code" width="120" height="120">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-end"><strong>Ticket Total Amount:</strong></td>
                                <td class="text-end" style="width: 150px;">Rs {{ number_format($ticket->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Amount Paid:</strong></td>
                                <td class="text-end">Rs {{ number_format($ticket->amount_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr class="my-1"></td>
                            </tr>
                            <tr>
                                <td class="text-end fs-5"><strong>Balance Due:</strong></td>
                                <td class="text-end fs-5 text-danger"><strong>Rs {{ number_format($ticket->amount_due, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center text-muted small border-top pt-4">
                This is a computer generated invoice/e-ticket and does not require a physical signature.<br>
                Thank you for choosing MACT Travel & Tours!
            </div>
        </div>
    </div>
</div>
@endsection
