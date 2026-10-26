<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Polio Certificate Invoice - {{ $polioInvoice->invoice_number }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 10px;
            color: #64748b;
            line-height: 1.4;
        }
        .invoice-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
            margin-bottom: 6px;
        }
        .invoice-meta {
            font-size: 11px;
            text-align: right;
            color: #334155;
            line-height: 1.5;
        }
        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px 0;
            margin-bottom: 20px;
        }
        .info-cell {
            width: 50%;
            vertical-align: top;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
        }
        .info-cell-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .info-row {
            margin-bottom: 4px;
            font-size: 11px;
        }
        .info-label {
            color: #64748b;
            display: inline-block;
            width: 40%;
        }
        .info-val {
            font-weight: bold;
            color: #0f172a;
            display: inline-block;
            width: 58%;
        }
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .cost-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
            letter-spacing: 0.5px;
        }
        .cost-table td {
            padding: 9px 10px;
            font-size: 11px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .summary-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-table td {
            vertical-align: top;
        }
        .notes-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 10px;
            color: #475569;
            line-height: 1.4;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 8px;
            font-size: 11px;
        }
        .totals-table .grand-row td {
            font-size: 13px;
            font-weight: bold;
            border-top: 2px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-pending { background: #f1f5f9; color: #475569; }

        .signature-table {
            width: 100%;
            margin-top: 50px;
        }
        .signature-cell {
            width: 35%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 10px;
            color: #475569;
            font-weight: bold;
        }
        .footer-note {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 75px; vertical-align: middle;">
                            @if(extension_loaded('gd') && file_exists(public_path('MADINA-LOGO-3.png')))
                                <img src="{{ public_path('MADINA-LOGO-3.png') }}" style="max-height: 55px; max-width: 70px; object-fit: contain;" alt="Logo">
                            @else
                                <div style="background: #2563eb; color: #ffffff; font-weight: bold; font-size: 14px; padding: 6px 10px; border-radius: 4px; text-align: center; letter-spacing: 0.5px;">
                                    MST
                                </div>
                            @endif
                        </td>
                        <td style="vertical-align: middle; padding-left: 10px;">
                            <div class="company-name">{{ config('app.name', 'Madina Shareef Travels & Tours') }}</div>
                            <div class="company-info">
                                211A- GT Road Opposite Brains Baghbanpura, Lahore<br>
                                Phone: +92 300 1234567 &bull; Email: info@madinashareeftravels.pk
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="invoice-title">POLIO CERTIFICATE INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $polioInvoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $polioInvoice->invoice_date ? $polioInvoice->invoice_date->format('d M Y') : '—' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Section Table -->
    <table class="info-table">
        <tr>
            <!-- Client Details -->
            <td class="info-cell">
                <div class="info-cell-title">Billed To / Client Details</div>
                <div class="info-row">
                    <span class="info-label">Client Name:</span>
                    <span class="info-val">{{ $polioInvoice->client_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-val">{{ $polioInvoice->phone }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Passport No:</span>
                    <span class="info-val">{{ $polioInvoice->passport_number ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Persons:</span>
                    <span class="info-val">{{ $polioInvoice->persons }}</span>
                </div>
            </td>

            <!-- Settlement Status -->
            <td class="info-cell">
                <div class="info-cell-title">Settlement & Status</div>
                <div class="info-row">
                    <span class="info-label">Payment Status:</span>
                    <span class="info-val">
                        @if($polioInvoice->payment_status === 'Paid')
                            <span class="badge badge-paid">Paid</span>
                        @elseif($polioInvoice->payment_status === 'Partially Paid')
                            <span class="badge badge-partial">Partially Paid</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-val">{{ $polioInvoice->payment_method ?? 'Cash' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Recorded By:</span>
                    <span class="info-val">{{ $polioInvoice->creator->name ?? 'System' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Installments:</span>
                    <span class="info-val">{{ $polioInvoice->payments->count() }} Recorded</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Cost Details Table -->
    <table class="cost-table">
        <thead>
            <tr>
                <th style="width: 52%;">Description</th>
                <th class="text-center" style="width: 14%;">Quantity</th>
                <th class="text-right" style="width: 17%;">Unit Cost</th>
                <th class="text-right" style="width: 17%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Polio Certificate Service</strong>
                    <div style="font-size: 9px; color: #64748b;">Official polio vaccination certificate issuance processing</div>
                </td>
                <td class="text-center">{{ $polioInvoice->persons }}</td>
                <td class="text-right">PKR {{ number_format($polioInvoice->cost_per_person, 2) }}</td>
                <td class="text-right" style="font-weight: bold; color: #0f172a;">PKR {{ number_format($polioInvoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Table -->
    <table class="summary-table">
        <tr>
            <td style="width: 55%; padding-right: 20px;">
                <div class="notes-box">
                    <strong>Notes / Terms:</strong><br>
                    {{ $polioInvoice->notes ?? 'Official invoice for polio vaccination certificate service. Please keep this receipt for verification.' }}
                </div>
            </td>
            <td style="width: 45%;">
                <table class="totals-table">
                    <tr>
                        <td style="color: #64748b;">Subtotal:</td>
                        <td class="text-right" style="font-weight: bold;">PKR {{ number_format($polioInvoice->total_amount, 2) }}</td>
                    </tr>
                    <tr class="grand-row">
                        <td style="color: #2563eb;">Total Amount:</td>
                        <td class="text-right" style="color: #2563eb;">PKR {{ number_format($polioInvoice->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color: #16a34a; font-weight: bold;">Amount Paid:</td>
                        <td class="text-right" style="color: #16a34a; font-weight: bold;">PKR {{ number_format($polioInvoice->amount_paid, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color: #dc2626; font-weight: bold;">Balance Due:</td>
                        <td class="text-right" style="color: #dc2626; font-weight: bold;">PKR {{ number_format($polioInvoice->balance_due, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signature Table -->
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <div class="signature-line"></div>
                <div class="signature-label">Received By</div>
            </td>
            <td style="width: 30%;"></td>
            <td class="signature-cell">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Thank you for choosing {{ config('app.name', 'Madina Shareef Travels & Tours') }} &bull; Confidential Invoice
    </div>

</body>
</html>
