<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $salary->employee->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 40px; background: #f8f9fa; }
        .slip-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { margin: 0; font-size: 24px; color: #1e1e2f; }
        .company-info p { margin: 5px 0 0; color: #6c757d; font-size: 14px; }
        .slip-title { text-align: right; }
        .slip-title h2 { margin: 0; font-size: 22px; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
        .slip-title p { margin: 5px 0 0; font-weight: bold; font-size: 16px; }
        .emp-details { display: flex; flex-wrap: wrap; margin-bottom: 30px; background: #f8f9fa; padding: 20px; border-radius: 6px; }
        .emp-details > div { width: 50%; margin-bottom: 10px; font-size: 15px; }
        .emp-details span { font-weight: bold; display: inline-block; width: 130px; color: #495057; }
        .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .salary-table th { background: #4f46e5; color: #fff; text-align: left; padding: 12px 15px; font-weight: 600; }
        .salary-table td { padding: 12px 15px; border-bottom: 1px solid #e9ecef; }
        .salary-table .amount { text-align: right; font-weight: bold; }
        .salary-table .total-row { background: #f1f3f5; font-size: 18px; color: #1e1e2f; }
        .salary-table .total-row td { border-bottom: none; font-weight: bold; padding: 15px; }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { width: 250px; text-align: center; border-top: 1px solid #dee2e6; padding-top: 10px; margin-top: 60px; color: #6c757d; font-size: 14px; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        @media print {
            body { background: #fff; padding: 0; }
            .slip-container { box-shadow: none; padding: 0; }
            .status-badge { border: 1px solid #ccc; color: #000; }
            .header { border-bottom: 2px solid #000; }
            .salary-table th { background: #f1f3f5 !important; color: #000 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <div class="header">
            <div style="display: flex; align-items: center; gap: 20px;">
                <img src="{{ asset('MADINA-LOGO-3.png') }}" alt="Madina Shareef Travels Logo" style="height: 65px; width: auto;">
                <div class="company-info">
                    <h1>Madina Shareef Travels</h1>
                    <p>211A- GT Road Opposite Brains Baghbanpura, Lahore</p>
                    <p>Email: info@madinashareeftravels.pk</p>
                </div>
            </div>
            <div class="slip-title">
                <h2>Salary Slip</h2>
                <p>{{ date('F Y', mktime(0, 0, 0, $salary->salary_month, 1, $salary->salary_year)) }}</p>
            </div>
        </div>

        <div class="emp-details">
            <div><span>Employee Name:</span> {{ $salary->employee->name }}</div>
            <div><span>Employee ID:</span> {{ $salary->employee->employee_id ?? 'N/A' }}</div>
            <div><span>Designation:</span> {{ $salary->employee->designation ?? 'N/A' }}</div>
            <div>
                <span>Payment Status:</span> 
                @if($salary->status == 'Paid')
                    <span class="status-badge status-paid">Paid on {{ \Carbon\Carbon::parse($salary->payment_date)->format('d M Y') }}</span>
                @else
                    <span class="status-badge status-pending">Pending</span>
                @endif
            </div>
        </div>

        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width: 50%">Earnings</th>
                    <th class="amount" style="width: 50%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="amount">PKR {{ number_format($salary->basic_salary) }}</td>
                </tr>
                @if($salary->sales_commission > 0)
                <tr>
                    <td>Sales Commission</td>
                    <td class="amount text-success">+ PKR {{ number_format($salary->sales_commission) }}</td>
                </tr>
                @endif
                @if($salary->incentive > 0)
                <tr>
                    <td>Incentive</td>
                    <td class="amount text-success">+ PKR {{ number_format($salary->incentive) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2" style="background:#f8f9fa; font-weight:bold; padding: 10px 15px;">Deductions</td>
                </tr>
                @if($salary->advance_salary > 0)
                <tr>
                    <td>Advance Salary</td>
                    <td class="amount text-danger">- PKR {{ number_format($salary->advance_salary) }}</td>
                </tr>
                @endif
                @if($salary->deduction > 0)
                <tr>
                    <td>Other Deductions</td>
                    <td class="amount text-danger">- PKR {{ number_format($salary->deduction) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Net Salary</td>
                    <td class="amount">PKR {{ number_format($salary->net_salary) }}</td>
                </tr>
            </tbody>
        </table>

        @if($salary->notes)
        <div style="margin-bottom: 30px; font-size: 14px; color: #6c757d;">
            <strong>Notes:</strong> {{ $salary->notes }}
        </div>
        @endif

        <div class="footer">
            <div class="signature-box">Employee Signature</div>
            <div class="signature-box">Authorized Signature</div>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;" class="no-print">
                Print Salary Slip
            </button>
            <style>
                @media print { .no-print { display: none; } }
            </style>
        </div>
    </div>
</body>
</html>
