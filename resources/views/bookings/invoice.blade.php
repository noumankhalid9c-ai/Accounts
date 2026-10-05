<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Booking #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .invoice-container { max-width: 800px; margin: 40px auto; background: #fff; padding: 40px; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .invoice-header { border-bottom: 2px solid #0d6efd; padding-bottom: 20px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
                <img src="{{ asset('MADINA-LOGO-3.png') }}" alt="Logo" style="height: 60px;">
            </div>
            <div class="text-end">
                <h2 class="text-primary fw-bold mb-0">INVOICE</h2>
                <div class="text-muted">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="small">Date: {{ date('M d, Y') }}</div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-sm-6">
                <h6 class="text-muted fw-bold text-uppercase mb-2">Billed To:</h6>
                <div class="fw-bold fs-5">{{ $booking->client->name ?? 'N/A' }}</div>
                <div>{{ $booking->client->phone ?? '' }}</div>
                <div>{{ $booking->client->city ?? '' }}</div>
                <div>Passport/CNIC: {{ $booking->client->cnic ?? '' }}</div>
            </div>
            <div class="col-sm-6 text-end">
                <h6 class="text-muted fw-bold text-uppercase mb-2">Service Details:</h6>
                <div><strong>Type:</strong> {{ $booking->service_type }}</div>
                <div><strong>Travel Date:</strong> {{ $booking->travel_date ? \Carbon\Carbon::parse($booking->travel_date)->format('M d, Y') : 'N/A' }}</div>
                <div><strong>Package:</strong> {{ $booking->package_details ?? 'Standard' }}</div>
            </div>
        </div>

        <table class="table table-bordered mb-4">
            <thead class="table-light">
                <tr>
                    <th>Description</th>
                    <th class="text-end" style="width: 150px;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-bold">{{ $booking->service_type }} Package</div>
                        <div class="small text-muted">{{ $booking->package_details }}</div>
                    </td>
                    <td class="text-end align-middle">Rs {{ number_format($booking->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="row justify-content-end mb-5">
            <div class="col-sm-6 col-md-5">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fw-bold">Total Amount:</span>
                    <span class="fw-bold">Rs {{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fw-bold">Advance / Received:</span>
                    <span class="text-success fw-bold">- Rs {{ number_format($totalReceived, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fs-5 fw-bold text-dark">Remaining:</span>
                    <span class="fs-5 fw-bold text-danger">Rs {{ number_format($remaining, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="text-center text-muted small mt-5 pt-3 border-top">
            <p class="mb-1">Thank you for traveling with us!</p>
            <p>If you have any questions concerning this invoice, please contact support.</p>
        </div>
        
        <div class="text-center mt-4 d-print-none">
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-2"></i> Print Invoice</button>
        </div>
    </div>
</body>
</html>
