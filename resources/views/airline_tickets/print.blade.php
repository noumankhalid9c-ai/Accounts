<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Ticket - {{ $ticket->pnr }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .print-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .print-container { box-shadow: none; margin: 0; padding: 20px; max-width: 100%; }
            .d-print-none { display: none !important; }
        }
        .ticket-header {
            border-bottom: 2px solid #2b2c68;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .flight-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .flight-card-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
        }
        .flight-card-body {
            padding: 15px;
        }
        .pax-table th { background-color: #f8f9fa !important; }
    </style>
</head>
<body>

<div class="print-container">
    <div class="d-print-none mb-4 text-end">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-2"></i> Print Document</button>
        <button onclick="window.close()" class="btn btn-outline-secondary ms-2">Close</button>
    </div>

    <div class="ticket-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="{{ asset('MADINA-LOGO-3.png') }}" alt="Company Logo" class="me-3" style="max-height: 60px; object-fit: contain;">
            <div>
                <h2 class="fw-bold mb-0" style="color: #2b2c68;">MACT Travel & Tours</h2>
                <div class="text-muted small">Electronic Ticket / Itinerary Receipt</div>
            </div>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0 text-primary">PNR: {{ $ticket->pnr }}</h4>
            <div class="text-muted small">Issued: {{ \Carbon\Carbon::parse($ticket->issue_date)->format('d M Y') }}</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="text-uppercase text-muted fw-bold mb-2">Agency Info</h6>
            <div><strong>MACT Travel & Tours</strong></div>
            <div>Phone: +1 234 567 8900</div>
            <div>Email: info@macttravel.com</div>
        </div>
        <div class="col-6">
            <h6 class="text-uppercase text-muted fw-bold mb-2">Customer Info</h6>
            @if($ticket->customer_id)
                <div><strong>{{ $ticket->customer->name ?? '' }}</strong></div>
                <div>Phone: {{ $ticket->customer->phone ?? '' }}</div>
            @elseif($ticket->b2b_agent_id)
                <div><strong>{{ $ticket->b2bAgent->company_name ?? '' }} (B2B Agent)</strong></div>
                <div>Phone: {{ $ticket->b2bAgent->phone ?? '' }}</div>
            @endif
        </div>
    </div>

    <h6 class="text-uppercase text-muted fw-bold mb-3">Passenger Information</h6>
    <table class="table table-bordered pax-table mb-4">
        <thead>
            <tr>
                <th>Passenger Name</th>
                <th>Type</th>
                <th>Ticket Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticket->passengers as $pax)
            <tr>
                <td class="fw-bold">{{ $pax->passenger_name }}</td>
                <td>{{ $pax->passenger_type }}</td>
                <td>{{ $pax->ticket_number ?? 'TBA' }}</td>
                <td>{{ $ticket->ticket_status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h6 class="text-uppercase text-muted fw-bold mb-3">Flight Itinerary</h6>
    @foreach($ticket->segments as $seg)
    <div class="flight-card">
        <div class="flight-card-header d-flex justify-content-between">
            <div>
                <i class="bi bi-airplane-engines me-2"></i> {{ $seg->airline->name ?? '' }} - Flight {{ $seg->flight_number }}
            </div>
            <div>
                <span class="badge bg-success">Confirmed</span>
            </div>
        </div>
        <div class="flight-card-body">
            <div class="row align-items-center text-center">
                <div class="col-4">
                    <div class="fs-4 fw-bold">{{ \Carbon\Carbon::parse($seg->departure_time)->format('H:i') }}</div>
                    <div class="fw-bold text-primary">{{ $seg->departureAirport->iata_code ?? '' }}</div>
                    <div class="small text-muted">{{ $seg->departureAirport->city ?? '' }}</div>
                    <div class="small fw-bold mt-1">{{ \Carbon\Carbon::parse($seg->departure_date)->format('D, d M Y') }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted small"><i class="bi bi-clock me-1"></i> Flight</div>
                    <div style="height: 2px; background-color: #dee2e6; margin: 10px 0; position: relative;">
                        <i class="bi bi-airplane-fill" style="position: absolute; top: -10px; right: 0; color: #6c757d;"></i>
                    </div>
                </div>
                <div class="col-4">
                    <div class="fs-4 fw-bold">{{ \Carbon\Carbon::parse($seg->arrival_time)->format('H:i') }}</div>
                    <div class="fw-bold text-primary">{{ $seg->arrivalAirport->iata_code ?? '' }}</div>
                    <div class="small text-muted">{{ $seg->arrivalAirport->city ?? '' }}</div>
                    <div class="small fw-bold mt-1">{{ \Carbon\Carbon::parse($seg->arrival_date)->format('D, d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="row mt-5">
        <div class="col-8">
            <div class="small text-muted border p-3 rounded bg-light">
                <strong>Important Information:</strong>
                <ul class="mb-0 ps-3 mt-2">
                    <li>Please check-in at least 3 hours before departure.</li>
                    <li>Valid passport and required visas are the responsibility of the passenger.</li>
                    <li>Ticket is subject to airline rules regarding changes and cancellations.</li>
                </ul>
            </div>
        </div>
        <div class="col-4 text-center">
            @php
                $verifyUrl = route('airline-tickets.verify', $ticket->qr_token);
            @endphp
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($verifyUrl) }}" alt="QR Code" width="100" height="100">
            <div class="small text-muted mt-2">Scan to verify ticket</div>
        </div>
    </div>

</div>

</body>
</html>
