<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Slip - {{ $transaction->id }}</title>
    <!-- Use bootstrap from layout or CDN, assuming CDN for pure print view simplicity -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { font-size: 14px; }
            .no-print { display: none !important; }
            .print-container { width: 100%; border: none; box-shadow: none; padding: 0; }
        }
        .print-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .receipt-header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .receipt-footer { border-top: 1px solid #ddd; padding-top: 15px; margin-top: 30px; font-size: 0.85rem; color: #555; text-align: center; }
        .signature-box { border-top: 1px solid #333; width: 200px; text-align: center; margin-top: 50px; padding-top: 5px; }
    </style>
</head>
<body class="bg-light">

<div class="container">
    <div class="text-end mb-3 mt-3 no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Print Slip</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="print-container">
        <div class="receipt-header text-center">
            <h2 class="fw-bold mb-1">Travel & Tour Company</h2>
            <h4 class="text-uppercase text-secondary mb-0">
                {{ $transaction->transaction_type == 'Expense' ? 'Expense Payment Slip' : 'Cash Receipt Slip' }}
            </h4>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <strong>Voucher No:</strong> #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}<br>
                <strong>Added By:</strong> {{ $transaction->creator->name ?? 'System' }}
            </div>
            <div class="col-6 text-end">
                <span class="badge {{ $transaction->transaction_type == 'Expense' ? 'bg-danger' : 'bg-success' }} fs-6">
                    {{ $transaction->transaction_type }}
                </span><br>
                <strong>Payment Method:</strong> {{ $transaction->payment_method }}
            </div>
        </div>

        <table class="table table-bordered border-dark">
            <thead class="table-light">
                <tr>
                    <th>Description</th>
                    <th class="text-end" style="width: 150px;">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-4">
                        @if($transaction->category)
                            <span class="fw-bold d-block mb-1">Category: {{ $transaction->category->name }}</span>
                        @endif
                        {{ $transaction->description }}
                        
                        @if($transaction->paid_to)
                            <div class="mt-2 text-muted">
                                {{ $transaction->transaction_type == 'Expense' ? 'Paid To:' : 'Received From:' }} <strong>{{ $transaction->paid_to }}</strong>
                            </div>
                        @endif
                    </td>
                    <td class="text-end py-4 fw-bold fs-5">
                        {{ number_format($transaction->amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-6">
                <div class="signature-box">
                    Prepared By
                </div>
            </div>
            <div class="col-6 d-flex justify-content-end">
                <div class="signature-box">
                    Authorized Signatory
                </div>
            </div>
        </div>

        <div class="receipt-footer">
            <p class="mb-0">This is a computer-generated document. Printed on {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</div>

<script>
    // Automatically trigger print dialog on page load
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
