@extends('layouts.app')

@section('title', 'Client Data Record (Bookings)')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Client Data Record</h1>
        <p class="page-header-subtitle text-muted mb-0">Manage bookings, visas, and tour packages.</p>
    </div>
    <div>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>New Booking</span>
        </a>
    </div>
</div>

<x-ui.card class="border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Booking Date</th>
                    <th>Total Amt</th>
                    <th>Remaining</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td><a href="{{ route('bookings.show', $booking->id) }}" class="fw-bold text-decoration-none">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</a></td>
                    <td>
                        <div class="fw-bold text-dark">{{ $booking->client->name ?? 'N/A' }}</div>
                    </td>
                    <td>{{ $booking->service_type }}</td>
                    <td class="small">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</td>
                    <td class="fw-bold text-dark">Rs {{ number_format($booking->total_amount, 2) }}</td>
                    <td class="fw-bold text-danger">
                        @php
                            $received = $booking->payments_sum_amount ?? 0;
                            $rem = max(0, $booking->total_amount - $received);
                        @endphp
                        Rs {{ number_format($rem, 2) }}
                    </td>
                    <td>
                        @php
                            $badge = match($booking->payment_status) {
                                'Paid' => 'success',
                                'Partially Paid' => 'warning',
                                'Pending' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }} rounded-pill">{{ $booking->payment_status }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-light border">Manage Payment & Invoice</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No client records found. <a href="{{ route('bookings.create') }}">Create one now</a>.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 d-flex justify-content-center">
        {{ $bookings->links() }}
    </div>
</x-ui.card>
@endsection
