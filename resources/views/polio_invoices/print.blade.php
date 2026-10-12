<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polio Certificate Invoice - {{ $polioInvoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .no-print {
            margin-bottom: 20px;
            padding: 12px 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #2563eb;
            color: white;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-close-window {
            background: #64748b;
            color: white;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 8px;
        }
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 30px;
            background: #fff;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }
        .logo-box {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-box img {
            max-height: 65px;
            width: auto;
            object-fit: contain;
        }
        .company-name {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            margin: 0 0 6px 0;
            font-size: 18px;
            color: #2563eb;
            letter-spacing: -0.5px;
        }
        .inv-meta {
            font-size: 12px;
            color: #334155;
            line-height: 1.6;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .info-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 12px;
        }
        .info-label {
            color: #64748b;
        }
        .info-val {
            font-weight: 600;
            color: #0f172a;
        }
        table.cost-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.cost-table th, table.cost-table td {
            padding: 10px 12px;
            font-size: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.cost-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .summary-wrap {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 30px;
            gap: 20px;
        }
        .notes-box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            font-size: 11px;
            color: #475569;
        }
        .totals-table {
            width: 320px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
            font-size: 12px;
        }
        .totals-table tr.grand-total {
            border-top: 2px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            font-weight: 800;
            font-size: 14px;
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
        }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-pending { background: #f1f5f9; color: #475569; }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 20px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-bottom: 6px;
        }
        .signature-label {
            font-size: 11px;
            color: #475569;
            font-weight: 600;
        }
        .footer-note {
            margin-top: 35px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: #fff; }
            .invoice-box { border: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <strong>Polio Certificate Service Invoice:</strong> Press Print or Ctrl+P to print.
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">Print Invoice</button>
            <button class="btn-close-window" onclick="window.close()">Close</button>
        </div>
    </div>

    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <div class="logo-box">
                <img src="{{ asset('MADINA-LOGO-3.png') }}" alt="Logo">
                <div>
                    <div class="company-name">{{ config('app.name', 'Madina Shareef Travels') }}</div>
                    <div class="company-info">
                        211A- GT Road Opposite Brains Baghbanpura, Lahore<br>
                        Phone: +92 300 1234567 &bull; Email: info@madinashareeftravels.pk
                    </div>
                </div>
            </div>
            <div class="invoice-title">
                <h2>POLIO CERTIFICATE INVOICE</h2>
                <div class="inv-meta">
                    <strong>Invoice #:</strong> {{ $polioInvoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $polioInvoice->invoice_date ? $polioInvoice->invoice_date->format('d M Y') : '—' }}
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Client Details -->
            <div class="info-card">
                <div class="info-title">Billed To / Client Details</div>
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
                    <span class="info-label">Number of Persons:</span>
                    <span class="info-val">{{ $polioInvoice->persons }}</span>
                </div>
            </div>

            <!-- Invoice Status -->
            <div class="info-card">
                <div class="info-title">Payment & Settlement Status</div>
                <div class="info-row">
                    <span class="info-label">Payment Status:</span>
                    <span class="info-val">
                        @if($polioInvoice->payment_status === 'Paid')
                            <span class="badge badge-paid">Paid in Full</span>
                        @elseif($polioInvoice->payment_status === 'Partially Paid')
                            <span class="badge badge-partial">Partially Paid</span>
                        @else
                            <span class="badge badge-pending">Pending / Unpaid</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-val">{{ $polioInvoice->payment_method ?? 'Cash' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Issued By:</span>
                    <span class="info-val">{{ $polioInvoice->creator->name ?? 'System' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Installments:</span>
                    <span class="info-val">{{ $polioInvoice->payments->count() }} Recorded</span>
                </div>
            </div>
        </div>

        <!-- Cost Details Table -->
        <table class="cost-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-center" style="width: 15%;">Quantity</th>
                    <th class="text-right" style="width: 17%;">Unit Cost</th>
                    <th class="text-right" style="width: 18%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Polio Certificate Service</strong>
                        <div style="font-size: 11px; color: #64748b;">Official polio vaccination certificate issuance and verification processing</div>
                    </td>
                    <td class="text-center">{{ $polioInvoice->persons }}</td>
                    <td class="text-right">PKR {{ number_format($polioInvoice->cost_per_person, 2) }}</td>
                    <td class="text-right" style="font-weight: 700; color: #0f172a;">PKR {{ number_format($polioInvoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary & Notes -->
        <div class="summary-wrap">
            <div class="notes-box">
                <strong>Notes & Instructions:</strong>
                <p style="margin: 5px 0 0 0; line-height: 1.4;">
                    {{ $polioInvoice->notes ?? 'Official invoice for polio vaccination certificate service. Keep this receipt for verification and record.' }}
                </p>
            </div>

            <table class="totals-table">
                <tr>
                    <td class="info-label">Subtotal:</td>
                    <td class="text-right font-weight-bold">PKR {{ number_format($polioInvoice->total_amount, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Amount:</td>
                    <td class="text-right" style="color: #2563eb;">PKR {{ number_format($polioInvoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="info-label" style="color: #16a34a; font-weight: 600;">Amount Paid:</td>
                    <td class="text-right" style="color: #16a34a; font-weight: 700;">PKR {{ number_format($polioInvoice->amount_paid, 2) }}</td>
                </tr>
                <tr>
                    <td class="info-label" style="color: #dc2626; font-weight: 600;">Balance Due:</td>
                    <td class="text-right" style="color: #dc2626; font-weight: 700;">PKR {{ number_format($polioInvoice->balance_due, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Received By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature</div>
            </div>
        </div>

        <div class="footer-note">
            Thank you for choosing {{ config('app.name', 'Madina Shareef Travels') }} &bull; For inquiries, contact info@madinashareeftravels.pk
        </div>
    </div>

</body>
</html>
