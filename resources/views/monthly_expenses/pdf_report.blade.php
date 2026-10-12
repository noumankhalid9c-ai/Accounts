<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Expense Report - {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e1b4b;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            text-align: right;
        }
        .metric-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .metric-box {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 4px;
        }
        .metric-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }
        .metric-val {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 6px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        .data-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-overdue { background: #fee2e2; color: #b91c1c; }
        .badge-partial { background: #e0f2fe; color: #0369a1; }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="company-title">Madina Shareef Travels</div>
                <div style="color: #64748b; font-size: 10px; margin-top: 3px;">
                    Monthly Operating Expenses & Utility Bills Audit
                </div>
            </td>
            <td style="vertical-align: top;" class="text-right">
                <div class="report-title">MONTHLY EXPENSE REPORT</div>
                <div style="color: #0f172a; font-weight: bold; font-size: 12px; margin-top: 3px;">
                    {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                </div>
                <div style="color: #64748b; font-size: 9px;">
                    Generated on: {{ date('d M Y, h:i A') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Financial KPI Summary -->
    <table class="metric-table">
        <tr>
            <td style="width: 25%; padding-right: 6px;">
                <div class="metric-box">
                    <div class="metric-label">Total Expenses</div>
                    <div class="metric-val" style="color: #0f172a;">PKR {{ number_format($totalExpenses, 2) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 6px;">
                <div class="metric-box">
                    <div class="metric-label">Total Paid</div>
                    <div class="metric-val" style="color: #15803d;">PKR {{ number_format($totalPaid, 2) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 6px;">
                <div class="metric-box">
                    <div class="metric-label">Pending Balance</div>
                    <div class="metric-val" style="color: #b45309;">PKR {{ number_format($totalPending, 2) }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="metric-box">
                    <div class="metric-label">Overdue Amount</div>
                    <div class="metric-val" style="color: #b91c1c;">PKR {{ number_format($totalOverdue, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Itemized Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%;">Date</th>
                <th style="width: 18%;">Category</th>
                <th style="width: 27%;">Expense Title</th>
                <th style="width: 15%;">Provider / Ref</th>
                <th style="width: 12%;" class="text-right">Amount (PKR)</th>
                <th style="width: 10%;">Due Date</th>
                <th style="width: 8%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $exp)
            <tr>
                <td>{{ $exp->expense_date ? $exp->expense_date->format('d M Y') : '-' }}</td>
                <td>{{ $exp->category->name ?? 'General' }}</td>
                <td>
                    <strong>{{ $exp->expense_title }}</strong>
                    @if($exp->consumer_number)
                        <div style="font-size: 8px; color: #64748b;">A/C: {{ $exp->consumer_number }}</div>
                    @endif
                </td>
                <td>
                    {{ $exp->provider_name ?? '—' }}
                    @if($exp->bill_reference)
                        <div style="font-size: 8px; color: #64748b;">#{{ $exp->bill_reference }}</div>
                    @endif
                </td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($exp->amount, 2) }}</td>
                <td>{{ $exp->due_date ? $exp->due_date->format('d M Y') : '—' }}</td>
                <td class="text-center">
                    @if($exp->payment_status === 'Paid')
                        <span class="badge badge-paid">PAID</span>
                    @elseif($exp->payment_status === 'Partially Paid')
                        <span class="badge badge-partial">PARTIAL</span>
                    @elseif($exp->payment_status === 'Overdue')
                        <span class="badge badge-overdue">OVERDUE</span>
                    @else
                        <span class="badge badge-pending">PENDING</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">
                    No expense records found for this period.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="4" style="border-top: 2px solid #cbd5e1; text-transform: uppercase;">Grand Total:</td>
                <td class="text-right" style="border-top: 2px solid #cbd5e1; font-size: 11px;">
                    PKR {{ number_format($totalExpenses, 2) }}
                </td>
                <td colspan="2" style="border-top: 2px solid #cbd5e1;"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Category Summary Breakdown -->
    @if(isset($categoryTotals) && $categoryTotals->count() > 0)
    <div style="margin-top: 15px;">
        <strong style="font-size: 11px; color: #1e1b4b; text-transform: uppercase;">Category Breakdown:</strong>
        <table style="width: 100%; margin-top: 6px; border-collapse: collapse;">
            @foreach($categoryTotals->chunk(3) as $chunk)
                <tr>
                    @foreach($chunk as $cName => $cAmt)
                        <td style="width: 33.3%; padding: 4px; border: 1px solid #e2e8f0; font-size: 9px;">
                            {{ $cName }}: <strong>PKR {{ number_format($cAmt, 2) }}</strong>
                        </td>
                    @endforeach
                    @for($k = count($chunk); $k < 3; $k++)
                        <td style="width: 33.3%; border: none;"></td>
                    @endfor
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    <div class="footer">
        This is a computer-generated monthly expense report. For internal auditing & finance management.
    </div>
</body>
</html>
