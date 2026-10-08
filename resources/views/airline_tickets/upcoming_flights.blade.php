@extends('layouts.app')

@section('title', 'Upcoming Flights')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Upcoming Flights</h1>
        <p class="text-muted mb-0">Flights departing from {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>
    <div>
        <a href="{{ route('airline-tickets.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('airline-tickets.upcoming') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date & Time</th>
                        <th>Flight Info</th>
                        <th>Route</th>
                        <th>Passenger(s)</th>
                        <th>PNR</th>
                        <th>Ticket Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flights as $segment)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($segment->departure_date)->format('D, d M Y') }}</div>
                            <div class="text-primary fw-bold">{{ \Carbon\Carbon::parse($segment->departure_time)->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $segment->airline->name ?? '' }}</div>
                            <div class="small text-muted">{{ $segment->flight_number }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $segment->departureAirport->city ?? $segment->departureAirport->iata_code ?? '' }} <i class="bi bi-arrow-right text-muted mx-1"></i> {{ $segment->arrivalAirport->city ?? $segment->arrivalAirport->iata_code ?? '' }}</div>
                            <div class="small text-muted">{{ $segment->departureAirport->iata_code ?? '' }} - {{ $segment->arrivalAirport->iata_code ?? '' }}</div>
                        </td>
                        <td>
                            @if($segment->ticket && $segment->ticket->passengers->count() > 0)
                                <div class="fw-bold">{{ $segment->ticket->passengers->first()->passenger_name }}</div>
                                @if($segment->ticket->passengers->count() > 1)
                                    <div class="small text-muted">+{{ $segment->ticket->passengers->count() - 1 }} more</div>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $segment->ticket->pnr ?? 'N/A' }}</span></td>
                        <td>
                            @if($segment->ticket)
                                @if($segment->ticket->ticket_status == 'Confirmed' || $segment->ticket->ticket_status == 'Issued')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">{{ $segment->ticket->ticket_status }}</span>
                                @elseif($segment->ticket->ticket_status == 'Cancelled' || $segment->ticket->ticket_status == 'Void')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">{{ $segment->ticket->ticket_status }}</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">{{ $segment->ticket->ticket_status }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            @if($segment->ticket)
                            <a href="{{ route('airline-tickets.show', $segment->ticket->id) }}" class="btn btn-sm btn-light border text-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-25"></i>
                            No upcoming flights found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
