@extends('layouts.app')

@section('title', 'Generate Voucher')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Generate Voucher</h1>
        <p class="text-muted mb-0">For Group: <strong>{{ $travelGroup->group_name }}</strong> ({{ $travelGroup->group_id }})</p>
    </div>
    <div>
        <a href="{{ route('travel-groups.show', $travelGroup->id) }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Group</span>
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <x-ui.card icon="bi-file-earmark-text" title="Voucher Details">
            
            <div class="alert alert-info d-flex gap-3 mb-4">
                <i class="bi bi-info-circle-fill fs-4"></i>
                <div>
                    <strong>Snapshot Note:</strong> When you create this voucher, all current clients, flights, hotels, and transport of this group will be captured and saved permanently in the voucher. Future changes to the group will not alter this generated voucher unless you create a new one.
                </div>
            </div>

            @if ($errors->any())
                <x-ui.alert type="danger" :dismissible="false">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('travel-vouchers.store', $travelGroup->id) }}">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <x-form.input name="voucher_number" label="Voucher Number *" placeholder="e.g. VOU-{{ date('Ymd') }}-{{ rand(100,999) }}" required="true" value="{{ old('voucher_number', 'VOU-' . date('Ymd') . '-' . rand(100,999)) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.select name="voucher_type" label="Voucher Type *" :options="['Umrah' => 'Umrah Voucher', 'Hajj' => 'Hajj Voucher', 'Tour' => 'Tour Voucher', 'Transport' => 'Transport Only']" selected="{{ old('voucher_type', $travelGroup->service_type) }}" required="true" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input type="date" name="voucher_date" label="Voucher Date *" value="{{ old('voucher_date', date('Y-m-d')) }}" required="true" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="group_head" label="Group Head / Main Pax" value="{{ old('group_head') }}" placeholder="Leave blank to use Group Leader" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="package_number" label="Package Number / Name" value="{{ old('package_number') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input type="number" name="pax" label="Total Pax" value="{{ old('pax', $travelGroup->clients->count()) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="whatsapp" label="WhatsApp Contact" value="{{ old('whatsapp') }}" />
                    </div>

                    <!-- Flight Information Section -->
                    <div class="col-12">
                        <div class="card border rounded-3 p-3 my-2" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-airplane-engines me-2"></i>Flight Numbers & Schedule</h6>
                                    <p class="text-muted small mb-0">Flight numbers below will be permanently captured and printed on this travel voucher.</p>
                                </div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                                    {{ $travelGroup->flights->count() }} Flight(s) Attached
                                </span>
                            </div>

                            @if($travelGroup->flights->count() > 0)
                            <div class="table-responsive bg-white rounded border mb-3">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Flight Number</th>
                                            <th>Sector</th>
                                            <th>Departure Date & Time</th>
                                            <th>Arrival Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($travelGroup->flights as $flt)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $flt->flight_type }}</span></td>
                                            <td class="fw-bold text-dark">{{ $flt->flight_number ?? '-' }}</td>
                                            <td>{{ $flt->sector ?? '-' }}</td>
                                            <td>{{ $flt->departure_date ? $flt->departure_date->format('d M Y') : '-' }} {{ $flt->departure_time }}</td>
                                            <td>{{ $flt->arrival_date ? $flt->arrival_date->format('d M Y') : '-' }} {{ $flt->arrival_time }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            @php
                                $existingDep = $travelGroup->flights->firstWhere('flight_type', 'Departure');
                                $existingRet = $travelGroup->flights->whereIn('flight_type', ['Return', 'Arrival'])->first() ?? $travelGroup->flights->where('flight_type', '!=', 'Departure')->first();
                            @endphp

                            <div class="row g-3">
                                <!-- Departure Flight Details -->
                                <div class="col-12 col-md-6 border-end">
                                    <div class="fw-bold text-secondary mb-2 small text-uppercase"><i class="bi bi-arrow-up-right-circle text-success me-1"></i> Departure Flight</div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-medium">Departure Flight No</label>
                                        <input type="text" name="dep_flight_number" class="form-control form-control-sm" placeholder="e.g. SV-735 or PK-759" value="{{ old('dep_flight_number', $existingDep->flight_number ?? '') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-medium">Departure Sector</label>
                                        <input type="text" name="dep_sector" class="form-control form-control-sm" placeholder="e.g. LHE - JED" value="{{ old('dep_sector', $existingDep->sector ?? '') }}">
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <label class="form-label small fw-medium">Date</label>
                                            <input type="date" name="dep_date" class="form-control form-control-sm" value="{{ old('dep_date', $existingDep && $existingDep->departure_date ? $existingDep->departure_date->format('Y-m-d') : '') }}">
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label small fw-medium">Time</label>
                                            <input type="text" name="dep_time" class="form-control form-control-sm" placeholder="e.g. 14:30" value="{{ old('dep_time', $existingDep->departure_time ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Return Flight Details -->
                                <div class="col-12 col-md-6">
                                    <div class="fw-bold text-secondary mb-2 small text-uppercase"><i class="bi bi-arrow-down-left-circle text-danger me-1"></i> Return Flight</div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-medium">Return Flight No</label>
                                        <input type="text" name="ret_flight_number" class="form-control form-control-sm" placeholder="e.g. SV-736 or PK-760" value="{{ old('ret_flight_number', $existingRet->flight_number ?? '') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-medium">Return Sector</label>
                                        <input type="text" name="ret_sector" class="form-control form-control-sm" placeholder="e.g. JED - LHE" value="{{ old('ret_sector', $existingRet->sector ?? '') }}">
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <label class="form-label small fw-medium">Date</label>
                                            <input type="date" name="ret_date" class="form-control form-control-sm" value="{{ old('ret_date', $existingRet && $existingRet->departure_date ? $existingRet->departure_date->format('Y-m-d') : '') }}">
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label small fw-medium">Time</label>
                                            <input type="text" name="ret_time" class="form-control form-control-sm" placeholder="e.g. 01:06" value="{{ old('ret_time', $existingRet->departure_time ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Voucher Comments Box -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-chat-left-text-fill text-primary me-1"></i> Voucher Comments / Remarks</span>
                                <span class="badge bg-light text-muted border">Printed on Voucher</span>
                            </label>
                            <textarea name="comments" class="form-control" rows="3" placeholder="Enter comments or remarks for this voucher (e.g. Special client handling, transport coordinator contacts, hotel notes)...">{{ old('comments') }}</textarea>
                            <div class="form-text text-muted">This comment box will be prominently displayed on the voucher and printed documents.</div>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small">Special Instructions (Client / Border Control)</label>
                            <textarea name="special_instructions" class="form-control" rows="2" placeholder="Any border control, airport, or immigration notes for the group...">{{ old('special_instructions') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <a href="{{ route('travel-groups.show', $travelGroup->id) }}" class="btn btn-light border px-3">Cancel</a>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-magic me-1"></i> Generate & Save Voucher</button>
                </div>
            </form>

        </x-ui.card>
    </div>
</div>
@endsection
