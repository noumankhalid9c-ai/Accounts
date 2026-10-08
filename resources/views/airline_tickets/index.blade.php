@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Airline Tickets</h1>
            <p class="text-muted mb-0">Manage all your airline tickets, flight segments, and passenger details.</p>
        </div>
        <div>
            <a href="{{ route('airline-tickets.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-pie-chart"></i> Ticket Dashboard
            </a>
            <a href="{{ route('airline-tickets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Ticket
            </a>
        </div>
    </div>

    <!-- Filters could go here -->
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-4">All Tickets Directory</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket ID</th>
                            <th>Date Issued</th>
                            <th>Client / Customer</th>
                            <th>PNR</th>
                            <th>Pax Count</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($ticket->ticket_date)->format('d M Y') }}</td>
                            <td class="fw-bold">
                                @if($ticket->customer_id)
                                    {{ $ticket->customer->name ?? 'N/A' }} <span class="badge bg-info bg-opacity-10 text-info">Retail</span>
                                @elseif($ticket->b2b_agent_id)
                                    {{ $ticket->b2bAgent->company_name ?? 'N/A' }} <span class="badge bg-primary bg-opacity-10 text-primary">B2B</span>
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $ticket->pnr ?? 'N/A' }}</span></td>
                            <td>{{ $ticket->passengers_count ?? $ticket->passengers->count() }}</td>
                            <td class="fw-bold">{{ number_format($ticket->total_fare, 2) }}</td>
                            <td>
                                @if($ticket->ticket_status == 'Confirmed' || $ticket->ticket_status == 'Issued')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">{{ $ticket->ticket_status }}</span>
                                @elseif($ticket->ticket_status == 'Cancelled' || $ticket->ticket_status == 'Void')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">{{ $ticket->ticket_status }}</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">{{ $ticket->ticket_status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('airline-tickets.show', $ticket->id) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('airline-tickets.invoice', $ticket->id) }}" class="btn btn-sm btn-light border text-info" data-bs-toggle="tooltip" title="Invoice">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                <a href="{{ route('airline-tickets.edit', $ticket->id) }}" class="btn btn-sm btn-light border text-secondary" data-bs-toggle="tooltip" title="Edit Ticket">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(Auth::user()->role !== 'Sales')
                                <form action="{{ route('airline-tickets.destroy', $ticket->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Are you sure you want to delete this ticket?');" data-bs-toggle="tooltip" title="Delete Ticket">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No airline tickets found. Click "Add New Ticket" to create one.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
