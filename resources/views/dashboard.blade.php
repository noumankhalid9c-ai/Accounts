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
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <div class="hero-date-badge mb-0">
                    <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::now()->format('l, F j, Y') }}
                </div>
                <div class="hero-date-badge mb-0" style="background: rgba(255, 255, 255, 0.2); font-weight: 700;">
                    <i class="bi bi-building"></i> Madina Shareef Travels & Tours
                </div>
            </div>
            <h2 class="fw-bold mb-2">Good afternoon, {{ Auth::user()->name ?? 'Nouman Khalid' }}! 👋</h2>
            <p class="text-white-50 mb-0">Welcome to Madina Shareef Travels & Tours &bull; Financial & Operations Management Suite.</p>
        </div>
        <div class="d-flex gap-2 mt-4 mt-md-0 flex-wrap">
            <a href="{{ route('clients.create') }}" class="btn hero-btn text-decoration-none"><i class="bi bi-person-plus me-2"></i>Add Client</a>
            <a href="{{ route('bookings.create') }}" class="btn hero-btn text-decoration-none"><i class="bi bi-calendar-plus me-2"></i>New Booking</a>
            <a href="{{ route('petty_cash.daily') }}" class="btn hero-btn text-decoration-none"><i class="bi bi-plus-circle me-2"></i>Daily Cash / Expense</a>
    </div>
</div>

@php
    $canSeeFinancial = Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->canDo('dashboard.financial'));
@endphp

@if($canSeeFinancial)
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
            <h3 class="fw-bold mb-3">PKR {{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Total Invoiced</span>
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
            <h3 class="fw-bold mb-3">PKR {{ number_format($stats['total_expenses'] ?? 0, 2) }}</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Operating costs</span>
                <span class="badge badge-soft-danger rounded-pill px-2 py-1"><i class="bi bi-dash"></i> Overhead & Ops</span>
            </div>
        </div>
    </div>

    <!-- Gross Profit / Flow -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Net Cash Flow</div>
                <div class="stat-icon-wrapper" style="background-color: #d1fae5; color: #10b981;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <h3 class="fw-bold {{ ($stats['gross_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} mb-3">PKR {{ number_format($stats['gross_profit'] ?? 0, 2) }}</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Collected: <strong>PKR {{ number_format($stats['total_collected'] ?? 0, 0) }}</strong></span>
                <span class="badge badge-soft-success rounded-pill px-2 py-1"><i class="bi bi-check-circle"></i> Flow</span>
            </div>
        </div>
    </div>

    <!-- Outstanding Receivables -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Outstanding Balance</div>
                <div class="stat-icon-wrapper" style="background-color: #fef3c7; color: #f59e0b;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <h3 class="fw-bold text-warning mb-3">PKR {{ number_format($stats['total_outstanding'] ?? 0, 2) }}</h3>
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="text-muted small">Pending collection</span>
                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1"><i class="bi bi-hourglass-split"></i> In Pipeline</span>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Secondary Operational Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('airline-tickets.index') }}" class="text-decoration-none">
            <div class="small-stat-card d-flex align-items-center p-3 shadow-sm h-100 transition-hover">
                <div class="small-stat-icon me-3" style="background-color: #eef2ff; color: #4f46e5;">
                    <i class="bi bi-ticket-detailed-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_tickets'] ?? 0) }}</h4>
                    <div class="text-muted small">Airline Tickets</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('travel-groups.index') }}" class="text-decoration-none">
            <div class="small-stat-card d-flex align-items-center p-3 shadow-sm h-100 transition-hover">
                <div class="small-stat-icon me-3" style="background-color: #e0f2fe; color: #0284c7;">
                    <i class="bi bi-airplane-engines-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_groups'] ?? 0) }}</h4>
                    <div class="text-muted small">Travel Groups</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('travel-groups.index') }}" class="text-decoration-none">
            <div class="small-stat-card d-flex align-items-center p-3 shadow-sm h-100 transition-hover">
                <div class="small-stat-icon me-3" style="background-color: #fef3c7; color: #d97706;">
                    <i class="bi bi-file-earmark-check-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_vouchers'] ?? 0) }}</h4>
                    <div class="text-muted small">Issued Vouchers</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('clients.index') }}" class="text-decoration-none">
            <div class="small-stat-card d-flex align-items-center p-3 shadow-sm h-100 transition-hover">
                <div class="small-stat-icon me-3" style="background-color: #d1fae5; color: #059669;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_clients'] ?? 0) }}</h4>
                    <div class="text-muted small">Clients & Mutamers</div>
                </div>
            </div>
        </a>
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
                <h5 class="fw-bold mb-1"><i class="bi bi-pie-chart-fill text-info me-2"></i>Operations Portfolio</h5>
                <div class="text-muted small">Distribution across Travel Modules</div>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; width: 220px; height: 220px;">
                    <canvas id="invoiceChart"></canvas>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 w-100 small">
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #6366f1;"></span> Tickets: {{ $stats['total_tickets'] ?? 0 }}</div>
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #0284c7;"></span> Groups: {{ $stats['total_groups'] ?? 0 }}</div>
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #f59e0b;"></span> Vouchers: {{ $stats['total_vouchers'] ?? 0 }}</div>
                    <div class="d-flex align-items-center"><span class="badge rounded-circle p-1 me-1" style="background-color: #10b981;"></span> Clients: {{ $stats['total_clients'] ?? 0 }}</div>
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
                    <h5 class="fw-bold mb-1"><i class="bi bi-receipt text-primary me-2"></i>Recent Invoices & Transactions</h5>
                    <div class="text-muted small">Latest issued airline tickets and operational billings</div>
                </div>
                <a href="{{ route('ticket-invoices.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">View All Invoices <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0 table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Reference #</th>
                                <th>Customer / Passenger</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th class="pe-4">Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets as $ticket)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('airline-tickets.invoice', $ticket->id) }}" class="text-primary fw-bold text-decoration-none">
                                        <i class="bi bi-airplane text-muted me-1"></i>{{ $ticket->ticket_number }}
                                    </a>
                                </td>
                                <td>{{ $ticket->customer->name ?? 'Direct Passenger' }}</td>
                                <td class="text-muted">{{ $ticket->ticket_date ? \Carbon\Carbon::parse($ticket->ticket_date)->format('M d, Y') : '—' }}</td>
                                <td class="fw-bold">PKR {{ number_format($ticket->total_fare, 2) }}</td>
                                <td class="pe-4">
                                    @if($ticket->payment_status === 'Paid')
                                        <span class="badge badge-soft-success rounded-pill px-3 py-1.5">Paid</span>
                                    @elseif($ticket->payment_status === 'Partial')
                                        <span class="badge badge-soft-warning rounded-pill px-3 py-1.5">Partial</span>
                                    @else
                                        <span class="badge badge-soft-danger rounded-pill px-3 py-1.5">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            @endforelse

                            @forelse($recentPolios as $polio)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('polio-invoices.show', $polio->id) }}" class="text-info fw-bold text-decoration-none">
                                        <i class="bi bi-shield-check text-muted me-1"></i>{{ $polio->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $polio->customer_name ?: 'Polio Pax (' . $polio->persons . ')' }}</td>
                                <td class="text-muted">{{ $polio->invoice_date ? \Carbon\Carbon::parse($polio->invoice_date)->format('M d, Y') : '—' }}</td>
                                <td class="fw-bold">PKR {{ number_format($polio->total_amount, 2) }}</td>
                                <td class="pe-4">
                                    @if($polio->payment_status === 'Paid')
                                        <span class="badge badge-soft-success rounded-pill px-3 py-1.5">Paid</span>
                                    @elseif($polio->payment_status === 'Partially Paid')
                                        <span class="badge badge-soft-warning rounded-pill px-3 py-1.5">Partial</span>
                                    @else
                                        <span class="badge badge-soft-danger rounded-pill px-3 py-1.5">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            @endforelse

                            @if(($recentTickets->count() + $recentPolios->count()) === 0)
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No recent transactions recorded.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Flight & Operations Overview -->
    <div class="col-12 col-xl-4 d-flex flex-column gap-4">
        <!-- Flight Operations Snapshot -->
        <div class="card table-card">
            <div class="card-header bg-white border-0 p-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-airplane-fill text-primary me-2"></i>Flight Operations</h5>
                    <div class="text-muted small">Real-time schedule snapshot</div>
                </div>
                <a href="{{ route('airline-tickets.upcoming') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Schedule</a>
            </div>
            <div class="card-body p-4 pt-2">
                <a href="{{ route('airline-tickets.today') }}" class="text-decoration-none d-block">
                    <div class="d-flex align-items-center p-3 mb-3" style="background-color: #f0fdf4; border-radius: 12px; border: 1px solid #dcfce7;">
                        <div class="stat-icon-wrapper bg-white text-success me-3 shadow-sm" style="width: 40px; height: 40px;">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark">Departing Today</div>
                            <div class="small text-muted">{{ \Carbon\Carbon::now()->format('d M Y') }}</div>
                        </div>
                        <div class="badge bg-success rounded-pill px-3 py-2 fs-6">{{ $todayFlights->count() }}</div>
                    </div>
                </a>
                <a href="{{ route('airline-tickets.upcoming') }}" class="text-decoration-none d-block">
                    <div class="d-flex align-items-center p-3 mb-3" style="background-color: #eff6ff; border-radius: 12px; border: 1px solid #dbeafe;">
                        <div class="stat-icon-wrapper bg-white text-primary me-3 shadow-sm" style="width: 40px; height: 40px;">
                            <i class="bi bi-calendar-week-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark">Departing in Next 7 Days</div>
                            <div class="small text-muted">Upcoming flight departures</div>
                        </div>
                        <div class="badge bg-primary rounded-pill px-3 py-2 fs-6">{{ $upcomingFlights->count() }}</div>
                    </div>
                </a>
                <a href="{{ route('travel-groups.index') }}" class="text-decoration-none d-block">
                    <div class="d-flex align-items-center p-3" style="background-color: #fffbeb; border-radius: 12px; border: 1px solid #fef3c7;">
                        <div class="stat-icon-wrapper bg-white text-warning me-3 shadow-sm" style="width: 40px; height: 40px;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark">Active Travel Groups</div>
                            <div class="small text-muted">Umrah & Tour delegations</div>
                        </div>
                        <div class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">{{ $stats['total_groups'] ?? 0 }}</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Operations Center -->
        <div class="card table-card flex-grow-1">
            <div class="card-header bg-white border-0 p-4 pb-2">
                <h5 class="fw-bold mb-1"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions</h5>
                <div class="text-muted small">Direct operational shortcuts</div>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="d-grid gap-2">
                    <a href="{{ route('airline-tickets.create') }}" class="btn btn-outline-primary btn-sm rounded-pill py-2 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-ticket-perforated me-2"></i>Issue Airline Ticket</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('travel-groups.create') }}" class="btn btn-outline-success btn-sm rounded-pill py-2 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-people me-2"></i>Create Travel Group</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('polio-invoices.create') }}" class="btn btn-outline-info btn-sm rounded-pill py-2 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-file-earmark-medical me-2"></i>New Polio Certificate Invoice</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('monthly_expenses.create') }}" class="btn btn-outline-secondary btn-sm rounded-pill py-2 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-cash-stack me-2"></i>Add Monthly / Overhead Expense</span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
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

<!-- Monthly Expenses & Utility Bills Section (New Module Integration) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header bg-white border-0 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                            <i class="bi bi-wallet2 me-1"></i> Recurring & Utilities
                        </span>
                        <span class="text-muted small">&bull; {{ \Carbon\Carbon::now()->format('F Y') }}</span>
                    </div>
                    <h5 class="fw-bold mb-1 mt-1 text-dark">Monthly Expenses & Utility Bills</h5>
                    <div class="text-muted small">Company overhead, recurring subscriptions, and utility bill tracking</div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('monthly_expenses.reports') }}" class="btn btn-primary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center shadow-sm">
                        <i class="bi bi-file-earmark-text me-1.5"></i> View Monthly Expense Report
                    </a>
                    <a href="{{ route('monthly_expenses.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center">
                        <i class="bi bi-speedometer2 me-1.5"></i> Expense Dashboard
                    </a>
                </div>
            </div>

            <div class="card-body px-4 pt-0 pb-4">
                <!-- 4 Metrics Row -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Current Month Total</div>
                            <div class="fs-5 fw-bold text-dark mt-1">{{ number_format($monthlyExpenseStats['expenses'] ?? 0, 2) }} <span class="text-muted small fs-6">PKR</span></div>
                            <div class="text-muted" style="font-size: 0.75rem;">Budgeted & incurred overhead</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-success small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Total Paid</div>
                            <div class="fs-5 fw-bold text-success mt-1">{{ number_format($monthlyExpenseStats['paid'] ?? 0, 2) }} <span class="text-muted small fs-6">PKR</span></div>
                            <div class="text-muted" style="font-size: 0.75rem;">Settled payments</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-warning small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Pending Balance</div>
                            <div class="fs-5 fw-bold text-warning mt-1">{{ number_format($monthlyExpenseStats['pending'] ?? 0, 2) }} <span class="text-muted small fs-6">PKR</span></div>
                            <div class="text-muted" style="font-size: 0.75rem;">Awaiting clearance</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-danger small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Overdue Bills</div>
                            <div class="fs-5 fw-bold text-danger mt-1">{{ $monthlyExpenseStats['overdue_count'] ?? 0 }} <span class="text-muted small fs-6">bills ({{ number_format($monthlyExpenseStats['overdue_amount'] ?? 0, 0) }} PKR)</span></div>
                            <div class="text-muted" style="font-size: 0.75rem;">Past due date</div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bill Due Dates mini-table -->
                <div class="border rounded-3 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="bi bi-clock-history text-primary me-1"></i> Upcoming Bill Due Dates
                        </div>
                        <a href="{{ route('monthly_expenses.utility_bills') }}" class="small text-decoration-none text-primary">Manage Utility Bills &rarr;</a>
                    </div>

                    @if(isset($monthlyExpenseStats['upcoming']) && count($monthlyExpenseStats['upcoming']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Expense / Bill</th>
                                        <th>Category</th>
                                        <th>Provider</th>
                                        <th class="text-end">Amount (PKR)</th>
                                        <th class="text-end">Balance Due</th>
                                        <th>Due Date</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyExpenseStats['upcoming'] as $bill)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $bill->expense_title }}
                                            @if($bill->consumer_number)
                                                <span class="text-muted small d-block">A/C: {{ $bill->consumer_number }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $bill->category->name ?? 'Expense' }}</span></td>
                                        <td>{{ $bill->provider_name ?? '—' }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($bill->amount, 2) }}</td>
                                        <td class="text-end text-danger fw-semibold">{{ number_format($bill->remaining_amount, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $bill->due_date && $bill->due_date->isPast() ? 'bg-danger' : 'bg-secondary' }}">
                                                {{ $bill->due_date ? $bill->due_date->format('d M Y') : '—' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($bill->payment_status === 'Paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($bill->payment_status === 'Partially Paid')
                                                <span class="badge bg-warning text-dark">Partial</span>
                                            @elseif($bill->payment_status === 'Overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('monthly_expenses.show', $bill->id) }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.75rem;">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted small">
                            <i class="bi bi-check-circle text-success fs-5 d-block mb-1"></i>
                            No pending or upcoming bills due for this period!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($userSecurityStats) && $userSecurityStats)
<!-- User & Security Overview (Visible Only to Authorized Admins) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header bg-white border-0 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-check me-1"></i> Security & RBAC Monitor
                        </span>
                        <span class="text-muted small">&bull; Administrative Privileges</span>
                    </div>
                    <h5 class="fw-bold mb-1 mt-1 text-dark">User Management & Security Overview</h5>
                    <div class="text-muted small">Account activity, recent authentications, failed login defenses, and account status metrics</div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('users.index') }}" class="btn btn-primary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center shadow-sm">
                        <i class="bi bi-people-fill me-1.5"></i> All Users
                    </a>
                    <a href="{{ route('login-history.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center">
                        <i class="bi bi-clock-history me-1.5"></i> Login History
                    </a>
                    <a href="{{ route('security-settings.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm d-inline-flex align-items-center">
                        <i class="bi bi-gear me-1.5"></i> Security Policies
                    </a>
                </div>
            </div>

            <div class="card-body px-4 pt-0 pb-4">
                <!-- 4 Metrics Row -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Total Accounts</div>
                            <div class="fs-4 fw-bold text-dark mt-1">{{ $userSecurityStats['total_users'] }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Registered system accounts</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-success small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Active Users</div>
                            <div class="fs-4 fw-bold text-success mt-1">{{ $userSecurityStats['active_users'] }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Normal system access</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Inactive Accounts</div>
                            <div class="fs-4 fw-bold text-secondary mt-1">{{ $userSecurityStats['inactive_users'] }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Deactivated users</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                            <div class="text-danger small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Suspended / Locked</div>
                            <div class="fs-4 fw-bold text-danger mt-1">{{ $userSecurityStats['suspended_users'] }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Blocked / failed lockouts</div>
                        </div>
                    </div>
                </div>

                <!-- Sub-grids: Recent Logins & Recent Users -->
                <div class="row g-4">
                    <!-- Recent Logins & Failed Attempts -->
                    <div class="col-12 col-lg-7">
                        <div class="border rounded-3 p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold small text-secondary text-uppercase mb-0" style="letter-spacing: 0.5px;">
                                    <i class="bi bi-clock-history text-primary me-1"></i> Recent Authentication Activity
                                </h6>
                                <a href="{{ route('login-history.index') }}" class="small text-decoration-none text-primary">View Full Audit &rarr;</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Account</th>
                                            <th>IP Address</th>
                                            <th>Status</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($userSecurityStats['recent_logins']->merge($userSecurityStats['recent_failed'])->sortByDesc('created_at')->take(5) as $lh)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $lh->user ? $lh->user->name : $lh->identifier }}</span>
                                            </td>
                                            <td><code>{{ $lh->ip_address }}</code></td>
                                            <td>
                                                @if($lh->status === 'Success')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 rounded-pill">Success</span>
                                                @elseif($lh->status === 'Locked')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5 rounded-pill">Locked</span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-0.5 rounded-pill">Failed</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">{{ $lh->created_at ? $lh->created_at->diffForHumans() : '—' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">No recent login records.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recently Created Users -->
                    <div class="col-12 col-lg-5">
                        <div class="border rounded-3 p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold small text-secondary text-uppercase mb-0" style="letter-spacing: 0.5px;">
                                    <i class="bi bi-person-plus text-primary me-1"></i> Recently Added Users
                                </h6>
                                <a href="{{ route('users.create') }}" class="small text-decoration-none text-primary">+ Add User</a>
                            </div>

                            <div class="list-group list-group-flush">
                                @forelse($userSecurityStats['recent_users'] as $ru)
                                <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-2 border-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $ru->avatar_url }}" alt="{{ $ru->name }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                        <div>
                                            <a href="{{ route('users.show', $ru->id) }}" class="fw-bold text-dark text-decoration-none small d-block lh-sm">
                                                {{ $ru->name }}
                                            </a>
                                            <span class="text-muted" style="font-size: 0.72rem;">{{ $ru->role_name }}</span>
                                        </div>
                                    </div>
                                    <span class="badge {{ $ru->status === 'Active' ? 'bg-success' : 'bg-secondary' }} rounded-pill" style="font-size: 0.7rem;">
                                        {{ $ru->status }}
                                    </span>
                                </div>
                                @empty
                                <div class="text-center py-3 text-muted small">No users found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Financial Overview Bar Chart
    const ctxBar = document.getElementById('financialChart');
    if (ctxBar) {
        new Chart(ctxBar.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Total Revenue', 'Operating Expenses', 'Net Flow', 'Outstanding Balance'],
                datasets: [{
                    label: 'Amount (PKR)',
                    data: [{{ $stats['total_revenue'] ?? 0 }}, {{ $stats['total_expenses'] ?? 0 }}, {{ max(0, $stats['gross_profit'] ?? 0) }}, {{ $stats['total_outstanding'] ?? 0 }}],
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

    // Operations Portfolio Doughnut Chart
    const ctxDoughnut = document.getElementById('invoiceChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Airline Tickets', 'Travel Groups', 'Issued Vouchers', 'Registered Clients'],
                datasets: [{
                    data: [{{ $stats['total_tickets'] ?? 0 }}, {{ $stats['total_groups'] ?? 0 }}, {{ $stats['total_vouchers'] ?? 0 }}, {{ $stats['total_clients'] ?? 0 }}],
                    backgroundColor: [
                        '#6366f1', // Tickets - Indigo
                        '#0284c7', // Groups - Sky Blue
                        '#f59e0b', // Vouchers - Amber
                        '#10b981'  // Clients - Emerald
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
