<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify E-Ticket | MACT Travel & Tours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .verification-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: none;
            overflow: hidden;
        }
        .verification-header {
            background: linear-gradient(135deg, #2b2c68 0%, #484baf 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .verified-badge {
            width: 80px;
            height: 80px;
            background-color: white;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .ticket-info-item {
            padding: 15px 0;
            border-bottom: 1px solid #edf2f9;
        }
        .ticket-info-item:last-child {
            border-bottom: none;
        }
        .company-logo {
            font-weight: bold;
            font-size: 24px;
            color: #2b2c68;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="text-center mb-4">
                <div class="company-logo">MACT Travel & Tours</div>
                <div class="text-muted small mt-1">E-Ticket Verification Portal</div>
            </div>

            <div class="card verification-card">
                <div class="verification-header">
                    <div class="verified-badge">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h2 class="h4 fw-bold mb-1">Authentic Ticket Verified</h2>
                    <p class="mb-0 text-white-50">This document was issued by MACT Travel & Tours</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold mb-4 text-center">Ticket Information</h5>
                    
                    <div class="ticket-info-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="bi bi-hash me-2"></i>Booking Reference (PNR)</span>
                        <strong class="fs-5">{{ $ticket->pnr }}</strong>
                    </div>
                    
                    <div class="ticket-info-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="bi bi-calendar me-2"></i>Issue Date</span>
                        <strong>{{ \Carbon\Carbon::parse($ticket->issue_date)->format('d M Y') }}</strong>
                    </div>
                    
                    <div class="ticket-info-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="bi bi-info-circle me-2"></i>Status</span>
                        @if($ticket->ticket_status == 'Confirmed' || $ticket->ticket_status == 'Issued')
                            <span class="badge bg-success px-3 py-2 rounded-pill">{{ $ticket->ticket_status }}</span>
                        @elseif($ticket->ticket_status == 'Cancelled' || $ticket->ticket_status == 'Void')
                            <span class="badge bg-danger px-3 py-2 rounded-pill">{{ $ticket->ticket_status }}</span>
                        @else
                            <span class="badge bg-warning px-3 py-2 rounded-pill">{{ $ticket->ticket_status }}</span>
                        @endif
                    </div>
                    
                    <h5 class="fw-bold mt-5 mb-3">Passengers</h5>
                    <ul class="list-group list-group-flush mb-4">
                        @foreach($ticket->passengers as $pax)
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person text-muted me-2"></i>
                                <strong>{{ $pax->passenger_name }}</strong>
                                <span class="badge bg-light text-secondary ms-2">{{ $pax->passenger_type }}</span>
                            </div>
                            <span class="small text-muted">{{ $pax->ticket_number ?? '' }}</span>
                        </li>
                        @endforeach
                    </ul>
                    
                    <h5 class="fw-bold mt-4 mb-3">Flight Itinerary</h5>
                    <div class="itinerary-timeline">
                        @foreach($ticket->segments as $seg)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-primary">{{ $seg->airline->name ?? '' }} ({{ $seg->flight_number }})</span>
                                <span class="small text-muted">{{ \Carbon\Carbon::parse($seg->departure_date)->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fs-4 fw-bold">{{ \Carbon\Carbon::parse($seg->departure_time)->format('H:i') }}</div>
                                    <div class="small fw-bold">{{ $seg->departureAirport->iata_code ?? '' }}</div>
                                </div>
                                <div class="text-center px-2 flex-grow-1">
                                    <div class="text-muted small"><i class="bi bi-airplane text-muted"></i></div>
                                    <div style="height: 1px; background-color: #ddd; width: 100%; margin: 5px 0;"></div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-4 fw-bold">{{ \Carbon\Carbon::parse($seg->arrival_time)->format('H:i') }}</div>
                                    <div class="small fw-bold">{{ $seg->arrivalAirport->iata_code ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="card-footer bg-white text-center p-4 border-top">
                    <p class="text-muted small mb-0">For questions regarding this ticket, please contact MACT Travel & Tours directly.</p>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted small">
                &copy; {{ date('Y') }} MACT Travel & Tours. All rights reserved.
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
