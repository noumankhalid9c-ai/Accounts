@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    /* Custom styles for the new dashboard */
    .hero-card {
        background: linear-gradient(135deg, #2b2c68 0%, #484baf 100%);
        color: white;
        border-radius: 16px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .hero-date-badge {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .hero-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 20px;
        padding: 0.5rem 1.25rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .hero-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .stat-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    .chart-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    
    .table-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    
    .table-custom th {
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #edf2f9;
        padding: 1rem;
    }
    .table-custom td {
        vertical-align: middle;
        padding: 1rem;
        border-bottom: 1px solid #edf2f9;
        font-size: 0.9rem;
    }
    
    .badge-soft-success {
        background-color: #d1fae5;
        color: #059669;
    }
    .badge-soft-warning {
        background-color: #fef3c7;
        color: #d97706;
    }
    .badge-soft-danger {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .small-stat-card {
        border-radius: 12px;
        border: 1px solid #edf2f9;
        background: white;
    }
    .small-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    
    .progress-thin {
        height: 6px;
        border-radius: 3px;
        margin-top: 0.5rem;
    }
</style>

<!-- Hero Section -->
<div class="hero-card mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end">
        <div>
            <div class="hero-date-badge">
                <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::now()->format('l, F j, Y') }}
            </div>
            <h2 class="fw-bold mb-2">Good afternoon, {{ Auth::user()->name ?? 'Nouman Khalid' }}! 👋</h2>
            <p class="text-white-50 mb-0">Here is your financial and business performance overview for today.</p>
        </div>
        <div class="d-flex gap-2 mt-4 mt-md-0 flex-wrap">
            <button class="btn hero-btn"><i class="bi bi-person-plus me-2"></i>Add Client</button>
            <button class="btn hero-btn"><i class="bi bi-file-earmark-plus me-2"></i>Create Invoice</button>
            <button class="btn hero-btn"><i class="bi bi-plus-circle me-2"></i>Add Expense</button>
        </div>
    </div>
</div>

<!-- Main Stats Row -->
<div class="row g-4 mb-4">
    <!-- Total Revenue -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Total Revenue</div>
                <div class="stat-icon-wrapper" style="background-color: #eef2ff; color: #4f46e5;">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-3">PKR 235,898.00</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Total collected</span>
                <span class="badge badge-soft-success rounded-pill px-2 py-1"><i class="bi bi-arrow-up-right"></i> Active</span>
            </div>
        </div>
    </div>

    <!-- Total Expenses -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Total Expenses</div>
                <div class="stat-icon-wrapper" style="background-color: #fee2e2; color: #ef4444;">
                    <i class="bi bi-pie-chart"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-3">PKR 99,280.00</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Operating costs</span>
                <span class="badge badge-soft-danger rounded-pill px-2 py-1"><i class="bi bi-dash"></i> Direct & Ops</span>
            </div>
        </div>
    </div>

    <!-- Gross Profit -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Gross Profit</div>
                <div class="stat-icon-wrapper" style="background-color: #d1fae5; color: #10b981;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <h3 class="fw-bold text-success mb-3">PKR 136,618.00</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Margin: <strong class="text-dark">57.9%</strong></span>
                <span class="badge badge-soft-success rounded-pill px-2 py-1"><i class="bi bi-check-circle"></i> Net Flow</span>
            </div>
        </div>
    </div>

    <!-- Outstanding Invoices -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Outstanding Invoices</div>
                <div class="stat-icon-wrapper" style="background-color: #fef3c7; color: #f59e0b;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <h3 class="fw-bold text-warning mb-3">PKR 10,000.00</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Unpaid balance</span>
                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1"><i class="bi bi-hourglass-split"></i> In Pipeline</span>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="small-stat-card d-flex align-items-center p-3 shadow-sm">
            <div class="small-stat-icon me-3" style="background-color: #eef2ff; color: #4f46e5;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">11</h4>
                <div class="text-muted small">Active Clients</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="small-stat-card d-flex align-items-center p-3 shadow-sm">
            <div class="small-stat-icon me-3" style="background-color: #e0f2fe; color: #0284c7;">
                <i class="bi bi-briefcase-fill"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">1</h4>
                <div class="text-muted small">Active Services</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="small-stat-card d-flex align-items-center p-3 shadow-sm">
            <div class="small-stat-icon me-3" style="background-color: #fef3c7; color: #d97706;">
                <i class="bi bi-globe"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">0</h4>
                <div class="text-muted small">Active Domains</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="small-stat-card d-flex align-items-center p-3 shadow-sm">
            <div class="small-stat-icon me-3" style="background-color: #d1fae5; color: #059669;">
                <i class="bi bi-hdd-network-fill"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">0</h4>
                <div class="text-muted small">Hosting Plans</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-8">
        <div class="card chart-card h-100">
            <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Financial Overview</h5>
                    <div class="text-muted small">Revenue vs Expenses Performance</div>
                </div>
                <span class="badge bg-light text-secondary border px-2 py-1">Realtime</span>
            </div>
            <div class="card-body p-4 pt-2">
                <canvas id="financialChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card chart-card h-100">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold mb-1"><i class="bi bi-pie-chart-fill text-info me-2"></i>Invoices Status</h5>
                <div class="text-muted small">Distribution of Billings</div>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; width: 220px; height: 220px;">
                    <canvas id="invoiceChart"></canvas>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 w-100 small">
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #10b981;"></span> Paid: 10</div>
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #f59e0b;"></span> Pending: 1</div>
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #ef4444;"></span> Overdue: 0</div>
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #6b7280;"></span> Draft: 0</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-4 mb-4">
    <!-- Recent Invoices -->
    <div class="col-12 col-xl-8">
        <div class="card table-card h-100">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-receipt text-primary me-2"></i>Recent Invoices</h5>
                    <div class="text-muted small">Latest client invoices issued</div>
                </div>
                <a href="#" class="btn btn-sm btn-light border rounded-pill px-3">View All <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Client</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4"><a href="#" class="text-primary fw-bold text-decoration-none">INV-00101</a></td>
                                <td>TechNova Systems</td>
                                <td class="text-muted">Oct 14, 2026</td>
                                <td class="fw-bold">PKR 124,500.00</td>
                                <td class="pe-4"><span class="badge badge-soft-success rounded-pill px-3 py-2">Paid</span></td>
                            </tr>
                            <tr>
                                <td class="ps-4"><a href="#" class="text-primary fw-bold text-decoration-none">INV-00102</a></td>
                                <td>Global Imports LLC</td>
                                <td class="text-muted">Oct 15, 2026</td>
                                <td class="fw-bold">PKR 89,200.00</td>
                                <td class="pe-4"><span class="badge badge-soft-success rounded-pill px-3 py-2">Paid</span></td>
                            </tr>
                            <tr>
                                <td class="ps-4"><a href="#" class="text-primary fw-bold text-decoration-none">INV-00103</a></td>
                                <td>Skyline Architects</td>
                                <td class="text-muted">Oct 16, 2026</td>
                                <td class="fw-bold">PKR 45,650.00</td>
                                <td class="pe-4"><span class="badge badge-soft-warning rounded-pill px-3 py-2">Partially Paid</span></td>
                            </tr>
                            <tr>
                                <td class="ps-4"><a href="#" class="text-primary fw-bold text-decoration-none">INV-00104</a></td>
                                <td>Omega Marketing</td>
                                <td class="text-muted">Oct 18, 2026</td>
                                <td class="fw-bold">PKR 210,000.00</td>
                                <td class="pe-4"><span class="badge badge-soft-success rounded-pill px-3 py-2">Paid</span></td>
                            </tr>
                            <tr>
                                <td class="ps-4"><a href="#" class="text-primary fw-bold text-decoration-none">INV-00105</a></td>
                                <td>NextGen Logistics</td>
                                <td class="text-muted">Oct 20, 2026</td>
                                <td class="fw-bold">PKR 35,400.00</td>
                                <td class="pe-4"><span class="badge badge-soft-success rounded-pill px-3 py-2">Paid</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Renewals & Capital Allocation -->
    <div class="col-12 col-xl-4 d-flex flex-column gap-4">
        <!-- Upcoming Renewals -->
        <div class="card table-card">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-arrow-repeat text-warning me-2"></i>Upcoming Renewals</h5>
                    <div class="text-muted small">Domains expiring soon</div>
                </div>
                <a href="#" class="btn btn-sm btn-outline-warning rounded-pill px-3">Manage</a>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center p-3 mb-3" style="background-color: #fef2f2; border-radius: 12px; border: 1px solid #fee2e2;">
                    <div class="stat-icon-wrapper bg-white text-danger me-3 shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-alarm-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark">Expiring in 7 Days</div>
                        <div class="small text-muted">High priority renewals</div>
                    </div>
                    <div class="badge bg-danger rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">0</div>
                </div>
                <div class="d-flex align-items-center p-3" style="background-color: #fffbeb; border-radius: 12px; border: 1px solid #fef3c7;">
                    <div class="stat-icon-wrapper bg-white text-warning me-3 shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark">Expiring in 30 Days</div>
                        <div class="small text-muted">Upcoming renewals</div>
                    </div>
                    <div class="badge bg-warning rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: #fff;">0</div>
                </div>
            </div>
        </div>

        <!-- Capital Allocation -->
        <div class="card table-card flex-grow-1">
            <div class="card-header bg-white border-0 p-4 pb-2">
                <h5 class="fw-bold mb-1"><i class="bi bi-pie-chart text-success me-2"></i>Capital Allocation</h5>
                <div class="text-muted small">Target 33% / 33% / 34% Distribution</div>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Resources & Infrastructure (33%)</span>
                        <span class="fw-bold">PKR 77,846.34</span>
                    </div>
                    <div class="progress progress-thin bg-light">
                        <div class="progress-bar" role="progressbar" style="width: 33%; background-color: #3b82f6;"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Company Savings & Reserves (33%)</span>
                        <span class="fw-bold">PKR 77,846.34</span>
                    </div>
                    <div class="progress progress-thin bg-light">
                        <div class="progress-bar" role="progressbar" style="width: 33%; background-color: #10b981;"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Operating Expenses (34%)</span>
                        <span class="fw-bold">PKR 80,205.32</span>
                    </div>
                    <div class="progress progress-thin bg-light">
                        <div class="progress-bar" role="progressbar" style="width: 34%; background-color: #ef4444;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Airline Tickets Section -->
<div class="row g-4 mb-4">
    <!-- Today's Flights -->
    <div class="col-12 col-xl-12">
        <div class="card table-card h-100">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-airplane text-primary me-2"></i>Today's Flights</h5>
                    <div class="text-muted small">Flights departing today ({{ \Carbon\Carbon::now()->format('d M Y') }})</div>
                </div>
                <a href="{{ route('airline-tickets.today') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All Today's Flights <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0 table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Time</th>
                                <th>Flight</th>
                                <th>Airline</th>
                                <th>Route</th>
                                <th>Passenger/Pax</th>
                                <th>PNR</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayFlights as $segment)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ \Carbon\Carbon::parse($segment->departure_time)->format('H:i') }}</td>
                                <td>{{ $segment->flight_number }}</td>
                                <td>{{ $segment->airline->name ?? '' }}</td>
                                <td>{{ $segment->departureAirport->iata_code ?? '' }} <i class="bi bi-arrow-right text-muted mx-1"></i> {{ $segment->arrivalAirport->iata_code ?? '' }}</td>
                                <td>
                                    @if($segment->ticket && $segment->ticket->passengers->count() > 0)
                                        {{ $segment->ticket->passengers->first()->passenger_name }}
                                        @if($segment->ticket->passengers->count() > 1)
                                            <span class="badge bg-light text-secondary ms-1">+{{ $segment->ticket->passengers->count() - 1 }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $segment->ticket->pnr ?? 'N/A' }}</span></td>
                                <td class="pe-4">
                                    @if($segment->ticket)
                                        @if($segment->ticket->ticket_status == 'Confirmed' || $segment->ticket->ticket_status == 'Issued')
                                            <span class="badge badge-soft-success rounded-pill px-3 py-2">{{ $segment->ticket->ticket_status }}</span>
                                        @elseif($segment->ticket->ticket_status == 'Cancelled')
                                            <span class="badge badge-soft-danger rounded-pill px-3 py-2">{{ $segment->ticket->ticket_status }}</span>
                                        @else
                                            <span class="badge badge-soft-warning rounded-pill px-3 py-2">{{ $segment->ticket->ticket_status }}</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No flights departing today.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Upcoming Flights -->
    <div class="col-12 col-xl-12">
        <div class="card table-card h-100">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-calendar-event text-info me-2"></i>Upcoming Flights</h5>
                    <div class="text-muted small">Flights departing in the next 7 days</div>
                </div>
                <a href="{{ route('airline-tickets.upcoming') }}" class="btn btn-sm btn-outline-info rounded-pill px-3">View All Upcoming <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0 table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Time</th>
                                <th>Flight</th>
                                <th>Route</th>
                                <th>Pax</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingFlights->take(5) as $segment)
                            <tr>
                                <td class="ps-4 fw-bold">{{ \Carbon\Carbon::parse($segment->departure_date)->format('d M Y') }}</td>
                                <td class="text-primary">{{ \Carbon\Carbon::parse($segment->departure_time)->format('H:i') }}</td>
                                <td>{{ $segment->flight_number }} <span class="text-muted small">({{ $segment->airline->code ?? '' }})</span></td>
                                <td>{{ $segment->departureAirport->iata_code ?? '' }} <i class="bi bi-arrow-right text-muted mx-1"></i> {{ $segment->arrivalAirport->iata_code ?? '' }}</td>
                                <td>{{ $segment->ticket ? $segment->ticket->passengers->count() : 0 }}</td>
                                <td class="pe-4">
                                    @if($segment->ticket)
                                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $segment->ticket->ticket_status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No upcoming flights found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Financial Overview Bar Chart
    const ctxBar = document.getElementById('financialChart');
    if (ctxBar) {
        new Chart(ctxBar.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Total Revenue', 'Operating Expenses', 'Gross Profit', 'Outstanding Balance'],
                datasets: [{
                    label: 'Amount (PKR)',
                    data: [235898, 99280, 136618, 10000],
                    backgroundColor: [
                        '#6366f1', // Indigo
                        '#ef4444', // Red
                        '#10b981', // Emerald
                        '#f59e0b'  // Amber
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'PKR ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return 'PKR ' + (value / 1000) + 'k';
                            },
                            color: '#9ca3af'
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    }

    // Invoices Status Doughnut Chart
    const ctxDoughnut = document.getElementById('invoiceChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Pending', 'Overdue', 'Draft'],
                datasets: [{
                    data: [10, 1, 0, 0],
                    backgroundColor: [
                        '#10b981', // Paid - Emerald
                        '#f59e0b', // Pending - Amber
                        '#ef4444', // Overdue - Red
                        '#9ca3af'  // Draft - Gray
                    ],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
