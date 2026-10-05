@extends('layouts.app')

@section('title', 'Client Details - ' . ($client->company_name ?: $client->name))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded" style="width: 48px; height: 48px; font-size: 1.5rem;">
            <i class="bi bi-person-badge"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="page-header-title fs-3 fw-bold mb-0">{{ $client->company_name ?: $client->name }}</h1>
                @php
                    $badge = match($client->status) {
                        'Active' => 'success',
                        'Prospect' => 'info',
                        'Suspended' => 'danger',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }} rounded-pill">{{ $client->status }}</span>
            </div>
            <p class="page-header-subtitle text-muted mb-0">Contact Person: <strong>{{ $client->contact_person }}</strong> &bull; Client since {{ \Carbon\Carbon::parse($client->created_at)->format('M Y') }}</p>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
            <i class="bi bi-pencil"></i>
            <span>Edit Profile</span>
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>All Clients</span>
        </a>
    </div>
</div>

@php
    $totalInvoiced = 0;
    $totalPaid = 0;
    $outstanding = 0;
@endphp

<!-- Financial Stats -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-4">
        <x-ui.card class="h-100 border-0 shadow-sm">
            <div class="text-muted fw-semibold text-uppercase small mb-2" style="letter-spacing: 0.05em;">Total Invoiced</div>
            <div class="fs-2 fw-bold text-dark">Rs {{ number_format($totalInvoiced, 2) }}</div>
            <div class="text-muted small">{{ $client->invoices->count() }} total invoices issued</div>
        </x-ui.card>
    </div>
    <div class="col-12 col-sm-4">
        <x-ui.card class="h-100 border-0 shadow-sm">
            <div class="text-muted fw-semibold text-uppercase small mb-2" style="letter-spacing: 0.05em;">Total Paid</div>
            <div class="fs-2 fw-bold text-success">Rs {{ number_format($totalPaid, 2) }}</div>
            <div class="text-muted small">Cleared via payments</div>
        </x-ui.card>
    </div>
    <div class="col-12 col-sm-4">
        <x-ui.card class="h-100 border-0 shadow-sm">
            <div class="text-muted fw-semibold text-uppercase small mb-2" style="letter-spacing: 0.05em;">Outstanding Balance</div>
            <div class="fs-2 fw-bold {{ $outstanding > 0 ? 'text-warning' : 'text-dark' }}">Rs {{ number_format($outstanding, 2) }}</div>
            <div class="text-muted small">{{ $outstanding > 0 ? 'Payment pending' : 'All clear' }}</div>
        </x-ui.card>
    </div>
</div>

<!-- Main Details Grid -->
<div class="row g-4 mb-4">
    <!-- Left: Contact & Information -->
    <div class="col-12 col-lg-4">
        <x-ui.card icon="bi-info-circle" title="Contact Information" class="h-100">
            <div class="mb-3">
                <div class="text-muted small fw-semibold">Email</div>
                <div><a href="mailto:{{ $client->email }}" class="text-decoration-none fw-medium">{{ $client->email }}</a></div>
            </div>
            <div class="mb-3">
                <div class="text-muted small fw-semibold">Phone</div>
                <div class="fw-medium text-dark">{{ $client->phone ?: 'N/A' }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small fw-semibold">Mobile</div>
                <div class="fw-medium text-dark">{{ $client->mobile ?: 'N/A' }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small fw-semibold">Reference Source</div>
                <div class="fw-medium text-dark">
                    {{ $client->reference_type ?: 'Direct' }}
                    @if($client->reference_type === 'Vendor' && $client->vendor_name)
                        <span class="text-muted">({{ $client->vendor_name }})</span>
                    @endif
                </div>
            </div>
            <div class="mb-3">
                <div class="text-muted small fw-semibold">Location / Country</div>
                <div class="fw-medium text-dark">{{ $client->country ?: 'N/A' }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small fw-semibold">Website</div>
                <div>
                    @if ($client->website)
                        <a href="{{ $client->website }}" target="_blank" class="text-decoration-none fw-medium">{{ $client->website }} <i class="bi bi-box-arrow-up-right small"></i></a>
                    @else
                        <span class="text-muted">None</span>
                    @endif
                </div>
            </div>
            <div class="mb-3">
                <div class="text-muted small fw-semibold">Address</div>
                <div class="text-dark small">{!! nl2br(e($client->address ?: 'No address provided')) !!}</div>
            </div>
            @if ($client->notes)
                <div class="mt-4 p-3 bg-light rounded">
                    <div class="text-muted small fw-bold mb-1">INTERNAL NOTES</div>
                    <div class="small text-secondary">{!! nl2br(e($client->notes)) !!}</div>
                </div>
            @endif
        </x-ui.card>
    </div>

    <!-- Right: Bookings History -->
    <div class="col-12 col-lg-8">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-briefcase text-primary me-2"></i>Bookings History</span>
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-light border">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        <tr>
                            <th>Service</th>
                            <th>Booking Date</th>
                            <th>Total</th>
                            <th>Pending</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">No bookings recorded for this client.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
