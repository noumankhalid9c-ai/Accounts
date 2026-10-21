@extends('layouts.app')

@section('title', 'Voucher Details - ' . $travelVoucher->voucher_number)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Voucher: {{ $travelVoucher->voucher_number }}</h1>
        <p class="text-muted mb-0">Generated on {{ $travelVoucher->voucher_date->format('d M Y') }} for Group: <strong>{{ $travelVoucher->travelGroup->group_name }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('travel-groups.show', $travelVoucher->travelGroup->id) }}" class="btn btn-light border"><i class="bi bi-arrow-left"></i> Back to Group</a>
        <a href="{{ route('travel-vouchers.edit', $travelVoucher->id) }}" class="btn btn-outline-primary"><i class="bi bi-pencil-square"></i> Edit Voucher</a>
        <a href="{{ route('travel-vouchers.print', $travelVoucher->id) }}" target="_blank" class="btn btn-primary"><i class="bi bi-printer"></i> Print / Download PDF</a>
    </div>
</div>

@if (session('success'))
<x-ui.alert type="success" :dismissible="true">
    {{ session('success') }}
</x-ui.alert>
@endif

<div class="row g-4">
    <div class="col-12 col-md-4">
        <x-ui.card icon="bi-info-circle" title="Voucher Summary">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Type</span>
                    <strong>{{ $travelVoucher->voucher_type }}</strong>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Flight No(s)</span>
                    <span class="badge bg-primary fs-6">{{ $travelVoucher->flight_numbers_summary }}</span>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Total Pax</span>
                    <strong>{{ $travelVoucher->pax }}</strong>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Group Head</span>
                    <strong>{{ $travelVoucher->group_head ?? 'N/A' }}</strong>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Package</span>
                    <strong>{{ $travelVoucher->package_number ?? 'N/A' }}</strong>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">WhatsApp</span>
                    <strong>{{ $travelVoucher->whatsapp ?? 'N/A' }}</strong>
                </li>
            </ul>

            <!-- Comments Box in Summary -->
            <div class="card border rounded-3 p-3 mb-3 bg-light">
                <div class="fw-bold text-dark small mb-1"><i class="bi bi-chat-left-text-fill text-primary me-1"></i> Comments / Remarks:</div>
                <div class="text-muted small" style="white-space: pre-line;">
                    {{ $travelVoucher->comments ?: 'No comments provided.' }}
                </div>
            </div>

            @if($travelVoucher->special_instructions)
            <div class="card border rounded-3 p-3 mb-3 bg-light">
                <div class="fw-bold text-dark small mb-1"><i class="bi bi-info-circle-fill text-info me-1"></i> Special Instructions:</div>
                <div class="text-muted small" style="white-space: pre-line;">
                    {{ $travelVoucher->special_instructions }}
                </div>
            </div>
            @endif

            <div class="bg-light p-3 rounded">
                <strong>QR Verification URL:</strong><br>
                <a href="{{ route('travel-vouchers.verify', $travelVoucher->qr_token) }}" target="_blank" class="small text-break">{{ route('travel-vouchers.verify', $travelVoucher->qr_token) }}</a>
            </div>
        </x-ui.card>
    </div>

    <div class="col-12 col-md-8">
        <x-ui.card icon="bi-clock-history" title="Snapshot Data">
            <p class="text-muted small mb-3">This is the read-only data that was captured when the voucher was generated. This ensures the voucher remains authentic even if the group data changes later.</p>
            
            <h6 class="fw-bold mt-4 border-bottom pb-2">Clients ({{ count($travelVoucher->snapshot_data['clients'] ?? []) }})</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light"><tr><th>Name</th><th>Passport</th><th>Pax Type</th></tr></thead>
                    <tbody>
                        @forelse($travelVoucher->snapshot_data['clients'] ?? [] as $client)
                            <tr><td>{{ $client['client_name'] }}</td><td>{{ $client['passport_number'] ?? '-' }}</td><td>{{ $client['pax_type'] }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No clients in snapshot</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h6 class="fw-bold mt-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-airplane-engines me-2"></i>Flights ({{ count($travelVoucher->flights) }})</span>
                <span class="badge bg-secondary">Flight No(s): {{ $travelVoucher->flight_numbers_summary }}</span>
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Flight No</th>
                            <th>Sector</th>
                            <th>Departure Date & Time</th>
                            <th>Arrival Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($travelVoucher->flights as $flight)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $flight['flight_type'] ?? '-' }}</span></td>
                                <td class="fw-bold text-primary fs-6">{{ $flight['flight_number'] ?? '-' }}</td>
                                <td class="fw-medium">{{ $flight['sector'] ?? '-' }}</td>
                                <td>{{ !empty($flight['departure_date']) ? substr($flight['departure_date'], 0, 10) : '-' }} {{ $flight['departure_time'] ?? '' }}</td>
                                <td>{{ !empty($flight['arrival_date']) ? substr($flight['arrival_date'], 0, 10) : '-' }} {{ $flight['arrival_time'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-3">No flights registered for this voucher</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <h6 class="fw-bold mt-4 border-bottom pb-2">Hotels ({{ count($travelVoucher->snapshot_data['hotels'] ?? []) }})</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light"><tr><th>City</th><th>Hotel Name</th><th>Check In</th><th>Nights</th></tr></thead>
                    <tbody>
                        @forelse($travelVoucher->snapshot_data['hotels'] ?? [] as $hotel)
                            <tr><td>{{ $hotel['city'] ?? '-' }}</td><td>{{ $hotel['hotel_name'] ?? '-' }}</td>
                            <td>{{ substr($hotel['check_in'], 0, 10) }}</td><td>{{ $hotel['nights'] ?? '-' }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No hotels in snapshot</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </x-ui.card>
    </div>
</div>
@endsection
