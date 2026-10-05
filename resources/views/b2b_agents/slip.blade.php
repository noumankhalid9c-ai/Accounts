<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Slip - {{ $payment->payment_slip_number }}</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .slip-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-top: 5px solid #198754; /* Success green for payment */
        }
        .company-logo {
            max-height: 80px;
            object-fit: contain;
        }
        .company-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 0;
        }
        .slip-header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .slip-title {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #198754;
            margin-top: 15px;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }
        .info-label {
            font-size: 12px;
            color: #777;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
        }
        .table td {
            vertical-align: middle;
            font-size: 15px;
        }
        .amount-row {
            background-color: #e8f5e9 !important; /* light green */
            font-weight: bold;
            font-size: 18px;
            color: #198754;
        }
        .footer-signatures {
            margin-top: 80px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 10px;
            font-weight: bold;
            margin: 0 auto;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .slip-container {
                box-shadow: none;
                margin: 0 auto;
                padding: 20px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Print Button -->
    <div class="text-center mt-3 mb-2 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-2" viewBox="0 0 16 16">
              <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
              <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Print Slip
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 shadow-sm ms-2">Close</button>
    </div>

    <div class="slip-container">
        <!-- Header -->
        <div class="slip-header">
            <!-- You can dynamically replace the src with asset('logo.png') if you have one -->
            <h1 class="company-title">MADINA SHAREEF TRAVELS</h1>
            <p class="company-address">211A- GT Road Opposite Brains Baghbanpura, Lahore</p>
            <p class="company-address">Email: info@madinashareeftravels.pk</p>
            
            <div class="slip-title">COMMISSION PAYMENT SLIP</div>
        </div>

        <div class="row info-box">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <p class="info-label">Payment To (Agent)</p>
                <p class="info-value">{{ $payment->b2bAgent->company_name ?? $payment->b2bAgent->name }}</p>
                <p class="text-muted small mb-0">{{ $payment->b2bAgent->contact_person }} | {{ $payment->b2bAgent->contact }}</p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <p class="info-label">Slip Number</p>
                <p class="info-value text-primary">{{ $payment->payment_slip_number }}</p>
                <p class="info-label mt-2">Payment Date</p>
                <p class="info-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Payment Details Table -->
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th style="width: 40%">Description</th>
                    <th style="width: 60%">Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold">Payment Method</td>
                    <td>{{ $payment->payment_method }}</td>
                </tr>
                @if($payment->transaction_reference)
                <tr>
                    <td class="fw-bold">Transaction Ref / Cheque No</td>
                    <td>{{ $payment->transaction_reference }}</td>
                </tr>
                @endif
                @if($payment->notes)
                <tr>
                    <td class="fw-bold">Remarks</td>
                    <td>{{ $payment->notes }}</td>
                </tr>
                @endif
                <tr class="amount-row">
                    <td>Amount Paid</td>
                    <td>PKR {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary text -->
        <div class="alert alert-light border text-center mb-5">
            <p class="mb-0">This is a system generated commission payment slip and does not require a physical signature unless printed for official records.</p>
        </div>

        <!-- Signatures -->
        <div class="row footer-signatures">
            <div class="col-6">
                <div class="signature-line">
                    Agent Signature
                </div>
            </div>
            <div class="col-6">
                <div class="signature-line">
                    Authorized Signatory
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
