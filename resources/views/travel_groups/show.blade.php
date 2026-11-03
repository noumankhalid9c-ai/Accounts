@extends('layouts.app')

@section('title', 'Group Details - ' . $travelGroup->group_id)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">{{ $travelGroup->group_name }}</h1>
        <p class="page-header-subtitle text-muted mb-0">ID: {{ $travelGroup->group_id }} | Service: {{ $travelGroup->service_type }} | Status: <span class="badge bg-primary">{{ $travelGroup->status }}</span></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('travel-groups.index') }}" class="btn btn-light border">Back</a>
        <a href="{{ route('travel-vouchers.create', $travelGroup->id) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-richtext"></i> Create Voucher
        </a>
    </div>
</div>

@if (session('success'))
<x-ui.alert type="success" :dismissible="true">
    {{ session('success') }}
</x-ui.alert>
@endif
@if ($errors->any())
<x-ui.alert type="danger" :dismissible="false">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</x-ui.alert>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <ul class="nav nav-tabs card-header-tabs" id="groupTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-medium px-4 py-3" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients" type="button" role="tab">Clients/Pax ({{ $travelGroup->clients->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4 py-3" id="flights-tab" data-bs-toggle="tab" data-bs-target="#flights" type="button" role="tab">Flights ({{ $travelGroup->flights->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4 py-3" id="hotels-tab" data-bs-toggle="tab" data-bs-target="#hotels" type="button" role="tab">Hotels ({{ $travelGroup->hotels->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4 py-3" id="transports-tab" data-bs-toggle="tab" data-bs-target="#transports" type="button" role="tab">Transport ({{ $travelGroup->transports->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium px-4 py-3" id="vouchers-tab" data-bs-toggle="tab" data-bs-target="#vouchers" type="button" role="tab">Vouchers ({{ $travelGroup->vouchers->count() }})</button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4 bg-light">
                <div class="tab-content" id="groupTabsContent">
                    
                    <!-- Clients Tab -->
                    <div class="tab-pane fade show active" id="clients" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Clients & Mutamers</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal"><i class="bi bi-plus-lg"></i> Add Client</button>
                        </div>
                        <div class="table-responsive bg-white rounded-3 border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Passport</th>
                                        <th>Pax Type</th>
                                        <th>Room</th>
                                        <th>Booking Status</th>
                                        <th>Payment</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($travelGroup->clients as $client)
                                    <tr>
                                        <td class="fw-medium">{{ $client->client_name }}</td>
                                        <td>{{ $client->passport_number ?? '-' }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $client->pax_type }}</span></td>
                                        <td>{{ $client->room_type ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $client->booking_status === 'Confirmed' ? 'bg-success' : ($client->booking_status === 'Cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                {{ $client->booking_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $client->payment_status === 'Paid' ? 'bg-success' : ($client->payment_status === 'Partial' ? 'bg-info text-dark' : 'bg-secondary') }}">
                                                {{ $client->payment_status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary border" data-bs-toggle="modal" data-bs-target="#editClientModal_{{ $client->id }}" title="Edit Client">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form action="{{ route('travel-groups.clients.destroy', [$travelGroup->id, $client->id]) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to remove client {{ addslashes($client->client_name) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border" title="Delete Client">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No clients added.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Flights Tab -->
                    <div class="tab-pane fade" id="flights" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Flights Details</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFlightModal"><i class="bi bi-plus-lg"></i> Add Flight</button>
                        </div>
                        <div class="table-responsive bg-white rounded-3 border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Flight Number</th>
                                        <th>Sector</th>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($travelGroup->flights as $flight)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $flight->flight_type }}</span></td>
                                        <td class="fw-medium">{{ $flight->flight_number }}</td>
                                        <td>{{ $flight->sector }}</td>
                                        <td>{{ $flight->departure_date ? $flight->departure_date->format('d M Y') : '-' }} {{ $flight->departure_time }}</td>
                                        <td>{{ $flight->arrival_date ? $flight->arrival_date->format('d M Y') : '-' }} {{ $flight->arrival_time }}</td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary border" data-bs-toggle="modal" data-bs-target="#editFlightModal_{{ $flight->id }}" title="Edit Flight">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form action="{{ route('travel-groups.flights.destroy', [$travelGroup->id, $flight->id]) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to remove flight {{ addslashes($flight->flight_number ?: $flight->flight_type) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border" title="Delete Flight">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No flights added.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hotels Tab -->
                    <div class="tab-pane fade" id="hotels" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Accommodation</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addHotelModal"><i class="bi bi-plus-lg"></i> Add Hotel</button>
                        </div>
                        <div class="table-responsive bg-white rounded-3 border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>City</th>
                                        <th>Hotel Name</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Nights</th>
                                        <th>Room/Meal</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($travelGroup->hotels as $hotel)
                                    <tr>
                                        <td class="fw-medium">{{ $hotel->city }}</td>
                                        <td>{{ $hotel->hotel_name }}</td>
                                        <td>{{ $hotel->check_in ? $hotel->check_in->format('d M Y') : '-' }}</td>
                                        <td>{{ $hotel->check_out ? $hotel->check_out->format('d M Y') : '-' }}</td>
                                        <td>{{ $hotel->nights }}</td>
                                        <td>
                                            {{ $hotel->room_type }}
                                            @if($hotel->meal) <br><small class="text-muted">{{ $hotel->meal }}</small> @endif
                                            @if($hotel->view) <br><small class="text-secondary"><i class="bi bi-eye"></i> {{ $hotel->view }}</small> @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary border" data-bs-toggle="modal" data-bs-target="#editHotelModal_{{ $hotel->id }}" title="Edit Hotel">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form action="{{ route('travel-groups.hotels.destroy', [$travelGroup->id, $hotel->id]) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to remove hotel {{ addslashes($hotel->hotel_name ?: $hotel->city) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border" title="Delete Hotel">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No hotels added.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Transports Tab -->
                    <div class="tab-pane fade" id="transports" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Transport Arrangements</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTransportModal"><i class="bi bi-plus-lg"></i> Add Transport</button>
                        </div>
                        <div class="table-responsive bg-white rounded-3 border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Transporter</th>
                                        <th>Type</th>
                                        <th>Route / Sector</th>
                                        <th>Notes</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($travelGroup->transports as $transport)
                                    <tr>
                                        <td>{{ $transport->travel_date ? $transport->travel_date->format('d M Y') : '-' }}</td>
                                        <td class="fw-medium">{{ $transport->transporter }}</td>
                                        <td>{{ $transport->type }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <small class="text-success"><i class="bi bi-geo-alt"></i> {{ $transport->pickup_location }}</small>
                                                <small class="text-danger"><i class="bi bi-geo"></i> {{ $transport->drop_location }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $transport->notes }}</td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary border" data-bs-toggle="modal" data-bs-target="#editTransportModal_{{ $transport->id }}" title="Edit Transport">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form action="{{ route('travel-groups.transports.destroy', [$travelGroup->id, $transport->id]) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to remove this transport arrangement?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border" title="Delete Transport">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No transport added.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Vouchers Tab -->
                    <div class="tab-pane fade" id="vouchers" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Generated Vouchers</h5>
                            <a href="{{ route('travel-vouchers.create', $travelGroup->id) }}" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-richtext"></i> Create Voucher</a>
                        </div>
                        <div class="table-responsive bg-white rounded-3 border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Voucher No</th>
                                        <th>Type</th>
                                        <th>Flight No(s)</th>
                                        <th>Date</th>
                                        <th>Pax</th>
                                        <th>Comments</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($travelGroup->vouchers as $voucher)
                                    <tr>
                                        <td class="fw-bold">{{ $voucher->voucher_number }}</td>
                                        <td><span class="badge bg-secondary">{{ $voucher->voucher_type }}</span></td>
                                        <td><span class="badge bg-primary-subtle text-primary border">{{ $voucher->flight_numbers_summary }}</span></td>
                                        <td>{{ $voucher->voucher_date ? $voucher->voucher_date->format('d M Y') : '-' }}</td>
                                        <td>{{ $voucher->pax }}</td>
                                        <td><small class="text-muted">{{ Str::limit($voucher->comments ?: $voucher->special_instructions, 25) }}</small></td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-start align-items-center">
                                                <a href="{{ route('travel-vouchers.show', $voucher->id) }}" class="btn btn-sm btn-light border" title="View Voucher"><i class="bi bi-eye"></i> View</a>
                                                <a href="{{ route('travel-vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-outline-primary border" title="Edit Voucher"><i class="bi bi-pencil"></i> Edit</a>
                                                <a href="{{ route('travel-vouchers.print', $voucher->id) }}" target="_blank" class="btn btn-sm btn-light border text-primary" title="Print PDF"><i class="bi bi-printer"></i> Print</a>
                                                @if(auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->hasRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || auth()->user()->canDo('travel_vouchers.delete')))
                                                <form action="{{ route('travel-vouchers.destroy', $voucher->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete Voucher {{ $voucher->voucher_number }}? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border" title="Delete Voucher">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No vouchers generated yet.</td></tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus text-primary me-2"></i>Add Client / Mutamer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('travel-groups.clients.store', $travelGroup->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Father / Husband Name</label>
                            <input type="text" name="father_name" class="form-control" placeholder="Father or Husband Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pax Type <span class="text-danger">*</span></label>
                            <select name="pax_type" class="form-select" required>
                                <option value="Adult" selected>Adult</option>
                                <option value="Child">Child</option>
                                <option value="Infant">Infant</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phone / Mobile</label>
                            <input type="text" name="phone" class="form-control" placeholder="+92 ...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Passport Number</label>
                            <input type="text" name="passport_number" class="form-control" placeholder="Passport No">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Passport Expiry Date</label>
                            <input type="date" name="passport_expiry" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Package Name</label>
                            <input type="text" name="package" class="form-control" placeholder="e.g. 15 Days Economy">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Room Type</label>
                            <input type="text" name="room_type" class="form-control" placeholder="e.g. Double, Triple, Quad">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bed Allocation</label>
                            <input type="text" name="bed" class="form-control" placeholder="e.g. Bed 1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Group / Batch No</label>
                            <input type="text" name="group_number" class="form-control" placeholder="Batch No">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Visa / E-Visa No</label>
                            <input type="text" name="visa_number" class="form-control" placeholder="Visa No">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Airline PNR</label>
                            <input type="text" name="pnr" class="form-control" placeholder="PNR">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Booking Status <span class="text-danger">*</span></label>
                            <select name="booking_status" class="form-select" required>
                                <option value="Pending" selected>Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select" required>
                                <option value="Pending" selected>Pending</option>
                                <option value="Partial">Partial</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Special Requests</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any special requirements..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modals -->
@foreach($travelGroup->clients as $client)
<div class="modal fade" id="editClientModal_{{ $client->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Client / Pax: {{ $client->client_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('travel-groups.clients.update', [$travelGroup->id, $client->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $client->client_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Father / Husband Name</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $client->father_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Male" {{ old('gender', $client->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $client->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $client->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pax Type <span class="text-danger">*</span></label>
                            <select name="pax_type" class="form-select" required>
                                <option value="Adult" {{ old('pax_type', $client->pax_type) == 'Adult' ? 'selected' : '' }}>Adult</option>
                                <option value="Child" {{ old('pax_type', $client->pax_type) == 'Child' ? 'selected' : '' }}>Child</option>
                                <option value="Infant" {{ old('pax_type', $client->pax_type) == 'Infant' ? 'selected' : '' }}>Infant</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phone / Mobile</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Passport Number</label>
                            <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number', $client->passport_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Passport Expiry Date</label>
                            <input type="date" name="passport_expiry" class="form-control" value="{{ old('passport_expiry', optional($client->passport_expiry)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Package Name</label>
                            <input type="text" name="package" class="form-control" value="{{ old('package', $client->package) }}" placeholder="e.g. 15 Days Economy">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Room Type</label>
                            <input type="text" name="room_type" class="form-control" value="{{ old('room_type', $client->room_type) }}" placeholder="e.g. Double, Triple, Quad">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bed Allocation</label>
                            <input type="text" name="bed" class="form-control" value="{{ old('bed', $client->bed) }}" placeholder="e.g. Bed 1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Group / Batch No</label>
                            <input type="text" name="group_number" class="form-control" value="{{ old('group_number', $client->group_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Visa / E-Visa No</label>
                            <input type="text" name="visa_number" class="form-control" value="{{ old('visa_number', $client->visa_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Airline PNR</label>
                            <input type="text" name="pnr" class="form-control" value="{{ old('pnr', $client->pnr) }}" placeholder="e.g. 6-character PNR">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Booking Status <span class="text-danger">*</span></label>
                            <select name="booking_status" class="form-select" required>
                                <option value="Pending" {{ old('booking_status', $client->booking_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Confirmed" {{ old('booking_status', $client->booking_status) == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="Cancelled" {{ old('booking_status', $client->booking_status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select" required>
                                <option value="Pending" {{ old('payment_status', $client->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Partial" {{ old('payment_status', $client->payment_status) == 'Partial' ? 'selected' : '' }}>Partial</option>
                                <option value="Paid" {{ old('payment_status', $client->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Special Requests</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any special requirements...">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Client</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Flight Modal -->
<div class="modal fade" id="addFlightModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-airplane text-primary me-2"></i>Add Flight</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('travel-groups.flights.store', $travelGroup->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Flight Type <span class="text-danger">*</span></label>
                            <select name="flight_type" class="form-select" required>
                                <option value="Departure" selected>Departure</option>
                                <option value="Return">Return</option>
                                <option value="Internal">Internal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Flight Number</label>
                            <input type="text" name="flight_number" class="form-control" placeholder="e.g. SV-735">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Sector / Route</label>
                            <input type="text" name="sector" class="form-control" placeholder="e.g. LHE - JED">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departure Date</label>
                            <input type="date" name="departure_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departure Time</label>
                            <input type="time" name="departure_time" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Arrival Date</label>
                            <input type="date" name="arrival_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Arrival Time</label>
                            <input type="time" name="arrival_time" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Baggage allowance, terminal details"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Flight</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Flight Modals -->
@foreach($travelGroup->flights as $flight)
<div class="modal fade" id="editFlightModal_{{ $flight->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Flight: {{ $flight->flight_number ?: $flight->flight_type }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('travel-groups.flights.update', [$travelGroup->id, $flight->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Flight Type <span class="text-danger">*</span></label>
                            <select name="flight_type" class="form-select" required>
                                <option value="Departure" {{ old('flight_type', $flight->flight_type) == 'Departure' ? 'selected' : '' }}>Departure</option>
                                <option value="Return" {{ old('flight_type', $flight->flight_type) == 'Return' ? 'selected' : '' }}>Return</option>
                                <option value="Internal" {{ old('flight_type', $flight->flight_type) == 'Internal' ? 'selected' : '' }}>Internal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Flight Number</label>
                            <input type="text" name="flight_number" class="form-control" value="{{ old('flight_number', $flight->flight_number) }}" placeholder="e.g. SV-735">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Sector / Route</label>
                            <input type="text" name="sector" class="form-control" value="{{ old('sector', $flight->sector) }}" placeholder="e.g. LHE - JED">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departure Date</label>
                            <input type="date" name="departure_date" class="form-control" value="{{ old('departure_date', optional($flight->departure_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departure Time</label>
                            <input type="time" name="departure_time" class="form-control" value="{{ old('departure_time', $flight->departure_time) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Arrival Date</label>
                            <input type="date" name="arrival_date" class="form-control" value="{{ old('arrival_date', optional($flight->arrival_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Arrival Time</label>
                            <input type="time" name="arrival_time" class="form-control" value="{{ old('arrival_time', $flight->arrival_time) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Baggage allowance, terminal details">{{ old('notes', $flight->notes) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Flight</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Hotel Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-building text-primary me-2"></i>Add Hotel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('travel-groups.hotels.store', $travelGroup->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City</label>
                            <select name="city" class="form-select">
                                <option value="Makkah" selected>Makkah</option>
                                <option value="Madinah">Madinah</option>
                                <option value="Jeddah">Jeddah</option>
                                <option value="Taif">Taif</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hotel Name</label>
                            <input type="text" name="hotel_name" class="form-control" placeholder="e.g. Swissotel Makkah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check In Date</label>
                            <input type="date" name="check_in" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check Out Date</label>
                            <input type="date" name="check_out" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nights</label>
                            <input type="number" name="nights" class="form-control" placeholder="e.g. 7">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Room Type</label>
                            <input type="text" name="room_type" class="form-control" placeholder="e.g. Double, Triple, Quad">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Meal Plan</label>
                            <input type="text" name="meal" class="form-control" placeholder="e.g. Room Only, BB">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room View</label>
                            <input type="text" name="view" class="form-control" placeholder="e.g. Haram View, City View">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmation Number (BRN)</label>
                            <input type="text" name="confirmation_number" class="form-control" placeholder="e.g. BRN-847291">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Special floor preference, booking notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Hotel Modals -->
@foreach($travelGroup->hotels as $hotel)
<div class="modal fade" id="editHotelModal_{{ $hotel->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Hotel: {{ $hotel->hotel_name ?: $hotel->city }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('travel-groups.hotels.update', [$travelGroup->id, $hotel->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City</label>
                            <select name="city" class="form-select">
                                <option value="Makkah" {{ old('city', $hotel->city) == 'Makkah' ? 'selected' : '' }}>Makkah</option>
                                <option value="Madinah" {{ old('city', $hotel->city) == 'Madinah' ? 'selected' : '' }}>Madinah</option>
                                <option value="Jeddah" {{ old('city', $hotel->city) == 'Jeddah' ? 'selected' : '' }}>Jeddah</option>
                                <option value="Taif" {{ old('city', $hotel->city) == 'Taif' ? 'selected' : '' }}>Taif</option>
                                <option value="Other" {{ !in_array(old('city', $hotel->city), ['Makkah', 'Madinah', 'Jeddah', 'Taif']) ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hotel Name</label>
                            <input type="text" name="hotel_name" class="form-control" value="{{ old('hotel_name', $hotel->hotel_name) }}" placeholder="e.g. Swissotel Makkah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check In Date</label>
                            <input type="date" name="check_in" class="form-control" value="{{ old('check_in', optional($hotel->check_in)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check Out Date</label>
                            <input type="date" name="check_out" class="form-control" value="{{ old('check_out', optional($hotel->check_out)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nights</label>
                            <input type="number" name="nights" class="form-control" value="{{ old('nights', $hotel->nights) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Room Type</label>
                            <input type="text" name="room_type" class="form-control" value="{{ old('room_type', $hotel->room_type) }}" placeholder="e.g. Double, Triple, Quad">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Meal Plan</label>
                            <input type="text" name="meal" class="form-control" value="{{ old('meal', $hotel->meal) }}" placeholder="e.g. Room Only, BB">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room View</label>
                            <input type="text" name="view" class="form-control" value="{{ old('view', $hotel->view) }}" placeholder="e.g. Haram View, City View">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmation Number (BRN)</label>
                            <input type="text" name="confirmation_number" class="form-control" value="{{ old('confirmation_number', $hotel->confirmation_number) }}" placeholder="e.g. BRN-847291">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Special floor preference, booking notes">{{ old('notes', $hotel->notes) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Transport Modal -->
<div class="modal fade" id="addTransportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-bus-front text-primary me-2"></i>Add Transport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('travel-groups.transports.store', $travelGroup->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Travel Date</label>
                            <input type="date" name="travel_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Transporter / Company Name</label>
                            <input type="text" name="transporter" class="form-control" placeholder="e.g. Al-Safwa Transport">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vehicle Type</label>
                            <input type="text" name="type" class="form-control" placeholder="e.g. Bus, GMC, Coaster, HiAce">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Service Description</label>
                            <input type="text" name="description" class="form-control" placeholder="e.g. Full Route, Airport Transfer">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pickup Location</label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="e.g. JED Airport Terminal 1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Drop Location</label>
                            <input type="text" name="drop_location" class="form-control" placeholder="e.g. Makkah Hotel">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Driver Details</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Driver name, phone, bus plate number..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Transport</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Transport Modals -->
@foreach($travelGroup->transports as $transport)
<div class="modal fade" id="editTransportModal_{{ $transport->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Transport: {{ $transport->transporter ?: $transport->type }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('travel-groups.transports.update', [$travelGroup->id, $transport->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Travel Date</label>
                            <input type="date" name="travel_date" class="form-control" value="{{ old('travel_date', optional($transport->travel_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Transporter / Company Name</label>
                            <input type="text" name="transporter" class="form-control" value="{{ old('transporter', $transport->transporter) }}" placeholder="e.g. Al-Safwa Transport">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vehicle Type</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type', $transport->type) }}" placeholder="e.g. Bus, GMC, Coaster, HiAce">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Service Description</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description', $transport->description) }}" placeholder="e.g. Full Route, Airport Transfer">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pickup Location</label>
                            <input type="text" name="pickup_location" class="form-control" value="{{ old('pickup_location', $transport->pickup_location) }}" placeholder="e.g. JED Airport Terminal 1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Drop Location</label>
                            <input type="text" name="drop_location" class="form-control" value="{{ old('drop_location', $transport->drop_location) }}" placeholder="e.g. Makkah Hotel">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / Driver Details</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Driver name, phone, bus plate number...">{{ old('notes', $transport->notes) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Transport</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
