<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket / Invoice - {{ $ticket->pnr }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #2b2c68;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2b2c68;
        }
        .company-details {
            font-size: 11px;
            color: #666;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            color: #484baf;
        }
        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 8px;
        }
        .box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2b2c68;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #2b2c68;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        .totals-table .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            font-size: 10px;
            text-align: center;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td width="60%">
                    <img src="{{ public_path('MADINA-LOGO-3.png') }}" style="max-height: 60px; float: left; margin-right: 15px;" alt="Logo">
                    <div style="float: left;">
                        <div class="company-name">MACT Travel & Tours</div>
                        <div class="company-details">
                            123 Business Avenue, Block A<br>
                            City, Country<br>
                            Phone: +1 234 567 8900 | Email: info@macttravel.com
                        </div>
                    </div>
                </td>
                <td width="40%" style="text-align: right;">
                    <div class="doc-title">E-TICKET / INVOICE</div>
                    <div style="margin-top: 5px;">
                        <strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}<br>
                        <strong>Invoice #:</strong> INV-TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}<br>
                        <strong>Status:</strong> {{ $ticket->ticket_status }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="50%" class="box">
                    <strong>Billed To:</strong><br>
                    @if($ticket->customer_id)
                        {{ $ticket->customer->name ?? '' }}<br>
                        Phone: {{ $ticket->customer->phone ?? '' }}
                    @elseif($ticket->b2b_agent_id)
                        {{ $ticket->b2bAgent->company_name ?? '' }} (B2B)<br>
                        Phone: {{ $ticket->b2bAgent->phone ?? '' }}
                    @endif
                </td>
                <td width="50%" class="box" style="margin-left: 10px;">
                    <strong>Booking Reference:</strong><br>
                    <span style="font-size: 20px; font-weight: bold;">{{ $ticket->pnr }}</span><br>
                    <strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($ticket->issue_date)->format('d M Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">PASSENGER DETAILS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Passenger Name</th>
                <th>Type</th>
                <th>Ticket Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticket->passengers as $pax)
            <tr>
                <td><strong>{{ $pax->passenger_name }}</strong></td>
                <td>{{ $pax->passenger_type }}</td>
                <td>{{ $pax->ticket_number ?? 'TBA' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">FLIGHT ITINERARY</div>
    <table class="data-table">
        <thead>
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
                    <strong>{{ $seg->departureAirport->iata_code ?? '' }} - {{ $seg->departureAirport->city ?? '' }}</strong><br>
                    Date: {{ \Carbon\Carbon::parse($seg->departure_date)->format('d M Y') }}<br>
                    Time: {{ \Carbon\Carbon::parse($seg->departure_time)->format('H:i') }}
                </td>
                <td>
                    <strong>{{ $seg->arrivalAirport->iata_code ?? '' }} - {{ $seg->arrivalAirport->city ?? '' }}</strong><br>
                    Date: {{ \Carbon\Carbon::parse($seg->arrival_date)->format('d M Y') }}<br>
                    Time: {{ \Carbon\Carbon::parse($seg->arrival_time)->format('H:i') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">FINANCIAL SUMMARY</div>
    <table style="width: 100%;">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <div class="qr-code">
                    <p style="font-size: 11px; color: #666; margin-bottom: 5px;">Scan to Verify Ticket</p>
                    @php
                        $verifyUrl = route('airline-tickets.verify', $ticket->qr_token);
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($verifyUrl) }}" alt="QR Code" width="120" height="120">
                </div>
            </td>
            <td width="50%">
                <table class="totals-table">
                    <tr>
                        <td>Ticket Total Amount:</td>
                        <td align="right">PKR {{ number_format($ticket->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Amount Paid:</td>
                        <td align="right">PKR {{ number_format($ticket->amount_paid, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Balance Due:</td>
                        <td align="right" style="color: {{ $ticket->amount_due > 0 ? '#cc0000' : '#009900' }}">
                            PKR {{ number_format($ticket->amount_due, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        This is a computer generated invoice/e-ticket and does not require a physical signature.<br>
        Thank you for choosing MACT Travel & Tours!
    </div>

</body>
</html>
