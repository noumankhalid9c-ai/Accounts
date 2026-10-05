<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f0f2f5; }
        .verify-card { max-width: 600px; margin: 4rem auto; border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .verify-header { background: #198754; color: white; padding: 2rem; text-align: center; }
        .verify-header i { font-size: 4rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="card verify-card">
        <div class="verify-header bg-success">
            <i class="bi bi-check-circle-fill mb-2 d-inline-block"></i>
            <h2 class="fw-bold mb-0">Voucher Verified!</h2>
            <p class="mb-0 mt-1 opacity-75">This is a valid and authentic document.</p>
        </div>
        <div class="card-body p-4 p-md-5 bg-white">
            <h5 class="fw-bold text-center text-primary mb-4">{{ $voucher->voucher_type }} VOUCHER</h5>
            
            <ul class="list-group list-group-flush fs-5">
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <span class="text-muted">Voucher No</span>
                    <span class="fw-bold">{{ $voucher->voucher_number }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <span class="text-muted">Issue Date</span>
                    <span class="fw-medium">{{ $voucher->voucher_date->format('d M Y') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <span class="text-muted">Group / Package</span>
                    <span class="fw-medium">{{ $voucher->package_number ?? $voucher->travelGroup->group_name }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <span class="text-muted">Lead Pax</span>
                    <span class="fw-medium">{{ $voucher->group_head ?? $voucher->travelGroup->group_leader ?? 'N/A' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <span class="text-muted">Total Passengers</span>
                    <span class="fw-bold text-primary">{{ $voucher->pax }}</span>
                </li>
            </ul>
        </div>
        <div class="card-footer bg-light text-center py-3 text-muted small border-0">
            Verified by Mact Services System &bull; {{ date('Y') }}
        </div>
    </div>
</div>

</body>
</html>
