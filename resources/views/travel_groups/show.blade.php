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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($travelGroup->clients as $client)
                                    <tr>
                                        <td class="fw-medium">{{ $client->client_name }}</td>
                                        <td>{{ $client->passport_number ?? '-' }}</td>
                                        <td>{{ $client->pax_type }}</td>
                                        <td>{{ $client->room_type ?? '-' }}</td>
                                        <td>{{ $client->booking_status }}</td>
                                        <td>{{ $client->payment_status }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No clients added.</td></tr>
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
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No flights added.</td></tr>
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
                                        <td>{{ $hotel->room_type }} <br><small class="text-muted">{{ $hotel->meal }}</small></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No hotels added.</td></tr>
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
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No transport added.</td></tr>
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
                                        <th>Date</th>
                                        <th>Pax</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($travelGroup->vouchers as $voucher)
                                    <tr>
                                        <td class="fw-bold">{{ $voucher->voucher_number }}</td>
                                        <td><span class="badge bg-secondary">{{ $voucher->voucher_type }}</span></td>
                                        <td>{{ $voucher->voucher_date ? $voucher->voucher_date->format('d M Y') : '-' }}</td>
                                        <td>{{ $voucher->pax }}</td>
                                        <td>
                                            <a href="{{ route('travel-vouchers.show', $voucher->id) }}" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i> View</a>
                                            <a href="{{ route('travel-vouchers.print', $voucher->id) }}" target="_blank" class="btn btn-sm btn-light border"><i class="bi bi-printer"></i> Print</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No vouchers generated yet.</td></tr>
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
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add Client / Mutamer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('travel-groups.clients.store', $travelGroup->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><x-form.input name="client_name" label="Client Name *" required="true" /></div>
                        <div class="col-md-6"><x-form.input name="passport_number" label="Passport Number" /></div>
                        <div class="col-md-6"><x-form.select name="pax_type" label="Pax Type *" :options="['Adult'=>'Adult', 'Child'=>'Child', 'Infant'=>'Infant']" selected="Adult" required="true" /></div>
                        <div class="col-md-6"><x-form.input name="room_type" label="Room Type (e.g. Double, Triple)" /></div>
                        <div class="col-md-6"><x-form.select name="booking_status" label="Booking Status *" :options="['Pending'=>'Pending', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled']" selected="Pending" required="true" /></div>
                        <div class="col-md-6"><x-form.select name="payment_status" label="Payment Status *" :options="['Pending'=>'Pending', 'Partial'=>'Partial', 'Paid'=>'Paid']" selected="Pending" required="true" /></div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Flight Modal -->
<div class="modal fade" id="addFlightModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add Flight</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('travel-groups.flights.store', $travelGroup->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><x-form.select name="flight_type" label="Flight Type *" :options="['Departure'=>'Departure', 'Return'=>'Return', 'Internal'=>'Internal']" selected="Departure" required="true" /></div>
                        <div class="col-md-6"><x-form.input name="flight_number" label="Flight Number" placeholder="e.g. SV-735" /></div>
                        <div class="col-md-12"><x-form.input name="sector" label="Sector / Route" placeholder="e.g. LHE - JED" /></div>
                        <div class="col-md-6"><x-form.input type="date" name="departure_date" label="Departure Date" /></div>
                        <div class="col-md-6"><x-form.input type="time" name="departure_time" label="Departure Time" /></div>
                        <div class="col-md-6"><x-form.input type="date" name="arrival_date" label="Arrival Date" /></div>
                        <div class="col-md-6"><x-form.input type="time" name="arrival_time" label="Arrival Time" /></div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Flight</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Hotel Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add Hotel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('travel-groups.hotels.store', $travelGroup->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><x-form.select name="city" label="City" :options="['Makkah'=>'Makkah', 'Madinah'=>'Madinah', 'Jeddah'=>'Jeddah', 'Taif'=>'Taif']" selected="Makkah" /></div>
                        <div class="col-md-6"><x-form.input name="hotel_name" label="Hotel Name" placeholder="e.g. Swissotel Makkah" /></div>
                        <div class="col-md-6"><x-form.input type="date" name="check_in" label="Check In Date" /></div>
                        <div class="col-md-6"><x-form.input type="date" name="check_out" label="Check Out Date" /></div>
                        <div class="col-md-4"><x-form.input type="number" name="nights" label="Nights" /></div>
                        <div class="col-md-4"><x-form.input name="room_type" label="Room Details" /></div>
                        <div class="col-md-4"><x-form.input name="meal" label="Meal Plan" placeholder="e.g. Room Only, BB" /></div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Hotel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Transport Modal -->
<div class="modal fade" id="addTransportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add Transport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('travel-groups.transports.store', $travelGroup->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><x-form.input type="date" name="travel_date" label="Travel Date" /></div>
                        <div class="col-md-6"><x-form.input name="transporter" label="Transporter Name / Company" /></div>
                        <div class="col-md-6"><x-form.input name="type" label="Vehicle Type (e.g. Bus, GMC, Coaster)" /></div>
                        <div class="col-md-6"><x-form.input name="description" label="Service Description (e.g. Full Route)" /></div>
                        <div class="col-md-6"><x-form.input name="pickup_location" label="Pickup Location (e.g. JED Airport)" /></div>
                        <div class="col-md-6"><x-form.input name="drop_location" label="Drop Location (e.g. Makkah Hotel)" /></div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Transport</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
