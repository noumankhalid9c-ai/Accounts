<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Expense Report - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .company-name {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .company-subtitle {
            font-size: 11px;
            color: #64748b;
        }
        .report-title {
            text-align: right;
        }
        .report-title h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
            color: #0284c7;
        }
        .meta-text {
            font-size: 10px;
            color: #64748b;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 3px;
        }
        .summary-val {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .text-emerald { color: #059669; }
        .text-amber { color: #d97706; }
        .text-rose { color: #e11d48; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            padding: 7px 8px;
            font-size: 11px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
        }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-pending { background: #f1f5f9; color: #475569; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }

        .tfoot-total {
            background-color: #f8fafc;
            font-weight: 700;
            border-top: 2px solid #cbd5e1;
        }
        .footer-note {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .no-print {
            margin-bottom: 15px;
            padding: 10px 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #0284c7;
            color: white;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <strong>Print Preview:</strong> Press Print or Ctrl+P to print this document.
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">Print Report</button>
            <button class="btn-print" style="background:#64748b; margin-left:6px;" onclick="window.close()">Close</button>
        </div>
    </div>

    <div class="header">
        <div>
            <div class="company-name">{{ config('app.name', 'Madina Shareef Travels & Tours') }}</div>
            <div class="company-subtitle">Office Monthly Expenses & Utility Bills Statement</div>
        </div>
        <div class="report-title">
            <h2>MONTHLY EXPENSES REPORT</h2>
            <div class="meta-text"><strong>Period:</strong> {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</div>
            <div class="meta-text">Generated on: {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Monthly Expenses</div>
            <div class="summary-val">{{ number_format($totalExpenses, 2) }} PKR</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Paid</div>
            <div class="summary-val text-emerald">{{ number_format($totalPaid, 2) }} PKR</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Pending</div>
            <div class="summary-val text-amber">{{ number_format($totalPending, 2) }} PKR</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Overdue</div>
            <div class="summary-val text-rose">{{ number_format($totalOverdue, 2) }} PKR</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 70px;">Date</th>
                <th>Category</th>
                <th>Expense Title / Bill Ref</th>
                <th>Provider</th>
                <th class="text-right" style="width: 80px;">Amount (PKR)</th>
                <th class="text-right" style="width: 80px;">Paid (PKR)</th>
                <th style="width: 75px;">Due Date</th>
                <th style="width: 70px; text-align:center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $exp)
            <tr>
                <td>{{ $exp->expense_date ? $exp->expense_date->format('d M') : '—' }}</td>
                <td><strong>{{ $exp->category->name ?? 'Uncategorized' }}</strong></td>
                <td>
                    {{ $exp->expense_title }}
                    @if($exp->bill_reference)
                        <div style="font-size:9px; color:#64748b;">Ref: {{ $exp->bill_reference }}</div>
                    @endif
                </td>
                <td>{{ $exp->provider_name ?? '—' }}</td>
                <td class="text-right">{{ number_format($exp->amount, 2) }}</td>
                <td class="text-right text-emerald">{{ number_format($exp->paid_amount, 2) }}</td>
                <td>{{ $exp->due_date ? $exp->due_date->format('d M Y') : '—' }}</td>
                <td style="text-align:center;">
                    @if($exp->payment_status === 'Paid')
                        <span class="badge badge-paid">Paid</span>
                    @elseif($exp->payment_status === 'Partially Paid')
                        <span class="badge badge-partial">Partial</span>
                    @elseif($exp->payment_status === 'Overdue')
                        <span class="badge badge-overdue">Overdue</span>
                    @else
                        <span class="badge badge-pending">Pending</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; padding: 20px; color:#64748b;">No expense records found for this period.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="tfoot-total">
                <td colspan="4" style="text-transform:uppercase;">Grand Totals ({{ count($expenses) }} records)</td>
                <td class="text-right">{{ number_format($totalExpenses, 2) }}</td>
                <td class="text-right text-emerald">{{ number_format($totalPaid, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        <div>Madina Shareef Travels & Tours &bull; Confidential Financial Statement</div>
        <div>Page 1 of 1</div>
    </div>

</body>
</html>
