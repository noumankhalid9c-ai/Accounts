@extends('layouts.app')

@section('title', 'Ticket Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Ticket Sales & Reports</h1>
        <p class="text-muted mb-0">Analytics and performance of airline ticket sales.</p>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('ticket-reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Generate Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Overview Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 4px solid #4e73df;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tickets Sold</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalTickets }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 4px solid #1cc88a;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sales Revenue</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($totalSales, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 4px solid #36b9cc;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Estimated Profit</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($totalProfit, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Top Airlines -->
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Airlines Used</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topAirlines as $airline)
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <span class="fw-bold">{{ $airline->name }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $airline->segment_count }} segments</span>
                    </li>
                    @empty
                    <li class="list-group-item p-3 text-center text-muted">No data available for this period.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Daily Sales Table -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daily Sales Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Date</th>
                                <th>Tickets Issued</th>
                                <th class="text-end pe-4">Sales Total (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailySales as $day)
                            <tr>
                                <td class="fw-bold">{{ \Carbon\Carbon::parse($day->issue_date)->format('d M Y') }}</td>
                                <td>{{ $day->ticket_count }}</td>
                                <td class="text-end pe-4 text-success fw-bold">{{ number_format($day->daily_total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No sales data for this period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
