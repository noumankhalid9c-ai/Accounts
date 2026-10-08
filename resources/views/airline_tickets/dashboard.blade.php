@extends('layouts.app')

@section('title', 'Ticket Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Airline Tickets Dashboard</h1>
        <p class="text-muted mb-0">Overview of ticket sales, revenue, and upcoming flights.</p>
    </div>
    <div>
        <a href="{{ route('airline-tickets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Issue New Ticket
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Today's Sales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 4px solid #4e73df !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Today's Ticket Sales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($todaySales, 2) }}</div>
                        <div class="small text-muted mt-2">{{ $todayTickets }} tickets issued today</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-day fs-2 text-gray-300" style="color: #dddfeb;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 4px solid #1cc88a !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Monthly Revenue (Tickets)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($monthRevenue, 2) }}</div>
                        <div class="small text-muted mt-2">{{ $monthTickets }} tickets this month</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fs-2 text-gray-300" style="color: #dddfeb;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Pending -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 4px solid #f6c23e !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Today's Pending Payments</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($todayPending, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-hourglass-split fs-2 text-gray-300" style="color: #dddfeb;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Today's Flights -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Today's Flight Departures</h6>
                <a href="{{ route('airline-tickets.today') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Flight</th>
                                <th>Airline</th>
                                <th>Route</th>
                                <th>PNR</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayFlights as $segment)
                            <tr>
                                <td class="fw-bold text-primary">{{ \Carbon\Carbon::parse($segment->departure_time)->format('H:i') }}</td>
                                <td>{{ $segment->flight_number }}</td>
                                <td>{{ $segment->airline->name ?? '' }}</td>
                                <td>{{ $segment->departureAirport->iata_code ?? '' }} <i class="bi bi-arrow-right mx-1 text-muted"></i> {{ $segment->arrivalAirport->iata_code ?? '' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $segment->ticket->pnr ?? 'N/A' }}</span></td>
                                <td>
                                    @if($segment->ticket)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">{{ $segment->ticket->ticket_status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No flights departing today.</td>
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
