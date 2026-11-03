@extends('layouts.app')

@section('title', 'Edit Voucher - ' . $travelVoucher->voucher_number)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Edit Voucher: {{ $travelVoucher->voucher_number }}</h1>
        <p class="text-muted mb-0">For Group: <strong>{{ $travelGroup->group_name }}</strong> ({{ $travelGroup->group_id }})</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('travel-vouchers.show', $travelVoucher->id) }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Voucher</span>
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <x-ui.card icon="bi-pencil-square" title="Edit Voucher Details">

            @if ($errors->any())
                <x-ui.alert type="danger" :dismissible="false">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form id="voucherEditForm" method="POST" action="{{ route('travel-vouchers.update', $travelVoucher->id) }}">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium text-dark small">Voucher Number</label>
                        <input type="text" class="form-control bg-light" value="{{ $travelVoucher->voucher_number }}" readonly>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.select name="voucher_type" label="Voucher Type *" :options="['Umrah' => 'Umrah Voucher', 'Hajj' => 'Hajj Voucher', 'Tour' => 'Tour Voucher', 'Transport' => 'Transport Only']" selected="{{ old('voucher_type', $travelVoucher->voucher_type) }}" required="true" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input type="date" name="voucher_date" label="Voucher Date *" value="{{ old('voucher_date', $travelVoucher->voucher_date ? $travelVoucher->voucher_date->format('Y-m-d') : date('Y-m-d')) }}" required="true" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="group_head" label="Group Head / Main Pax" value="{{ old('group_head', $travelVoucher->group_head) }}" placeholder="Leave blank to use Group Leader" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="package_number" label="Package Number / Name" value="{{ old('package_number', $travelVoucher->package_number) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input type="number" name="pax" label="Total Pax" value="{{ old('pax', $travelVoucher->pax) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="whatsapp" label="WhatsApp Contact" value="{{ old('whatsapp', $travelVoucher->whatsapp) }}" />
                    </div>

                    <!-- Flight Information Section -->
                    <div class="col-12">
                        <div class="card border rounded-3 p-3 my-2" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-airplane-engines me-2"></i>Flight Numbers & Schedule</h6>
                                    <p class="text-muted small mb-0">Update flight numbers and departure/arrival times for this voucher.</p>
                                </div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                                    {{ count($travelVoucher->flights) }} Flight(s) Registered
                                </span>
                            </div>

                            @php
                                $flights = $travelVoucher->flights;
                                $existingDep = null;
                                $existingRet = null;
                                foreach ($flights as $f) {
                                    $t = strtolower($f['flight_type'] ?? '');
                                    if (str_contains($t, 'dep') && !$existingDep) {
                                        $existingDep = (object)$f;
                                    } elseif ((str_contains($t, 'ret') || str_contains($t, 'arr')) && !$existingRet) {
                                        $existingRet = (object)$f;
                                    }
                                }
                                if (!$existingDep && !empty($flights)) {
                                    $existingDep = (object)$flights[0];
                                }
                                if (!$existingRet && count($flights) > 1) {
                                    $existingRet = (object)$flights[1];
                                }
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
                                            <input type="date" name="dep_date" class="form-control form-control-sm" value="{{ old('dep_date', isset($existingDep->departure_date) ? substr($existingDep->departure_date, 0, 10) : '') }}">
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
                                            <input type="date" name="ret_date" class="form-control form-control-sm" value="{{ old('ret_date', isset($existingRet->departure_date) ? substr($existingRet->departure_date, 0, 10) : '') }}">
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
                            <textarea name="comments" class="form-control" rows="3" placeholder="Enter voucher comments, passenger instructions, or internal remarks to display on the voucher...">{{ old('comments', $travelVoucher->comments) }}</textarea>
                            <div class="form-text text-muted">This comment box will be displayed clearly on the voucher and printed documents.</div>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small">Special Instructions (Client / Border Control)</label>
                            <textarea name="special_instructions" class="form-control" rows="2" placeholder="Any border control, airport, or immigration notes for the group...">{{ old('special_instructions', $travelVoucher->special_instructions) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
                    <a href="{{ route('travel-vouchers.show', $travelVoucher->id) }}" class="btn btn-light border px-3">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary px-3 shadow-sm" id="btnOpenEditReview">
                            <i class="bi bi-clipboard-check me-1"></i> Review Changes
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>
</div>

<!-- Edit Voucher Review Modal -->
<div class="modal fade" id="reviewEditModal" tabindex="-1" aria-labelledby="reviewEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2"><i class="bi bi-clipboard-check fs-5"></i></span>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="reviewEditModalLabel">Review Voucher Changes</h5>
                        <small class="text-muted">Review updated voucher information before saving.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm rounded-3 mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-secondary mb-1" id="edit_rev_voucher_type">Umrah</span>
                                <h5 class="fw-bold mb-0 text-dark">{{ $travelVoucher->voucher_number }}</h5>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small d-block">Voucher Date</span>
                                <strong class="text-dark" id="edit_rev_voucher_date">-</strong>
                            </div>
                        </div>
                        <div class="row g-3 small">
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Group Name</span>
                                <strong class="text-dark">{{ $travelGroup->group_name }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Group Head</span>
                                <strong class="text-dark" id="edit_rev_group_head">-</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Total Pax</span>
                                <span class="badge bg-primary px-2" id="edit_rev_pax">0</span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">WhatsApp</span>
                                <strong class="text-dark" id="edit_rev_whatsapp">-</strong>
                            </div>
                            <div class="col-6 col-md-6">
                                <span class="text-muted d-block">Package Number</span>
                                <strong class="text-dark" id="edit_rev_package_number">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flight Schedule -->
                <div class="card border-0 shadow-sm rounded-3 mb-3">
                    <div class="card-header bg-white py-2 px-3 fw-bold small text-uppercase text-secondary border-bottom">
                        <i class="bi bi-airplane-engines text-primary me-1"></i> Updated Flight Schedule
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3 small">
                            <div class="col-12 col-md-6 border-end">
                                <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">Departure Flight</span>
                                <div class="fw-bold text-dark fs-6" id="edit_rev_dep_flight">-</div>
                                <div class="text-muted" id="edit_rev_dep_sector">-</div>
                                <div class="text-secondary small mt-1" id="edit_rev_dep_datetime">-</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1">Return Flight</span>
                                <div class="fw-bold text-dark fs-6" id="edit_rev_ret_flight">-</div>
                                <div class="text-muted" id="edit_rev_ret_sector">-</div>
                                <div class="text-secondary small mt-1" id="edit_rev_ret_datetime">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments & Instructions -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-body p-3 small">
                        <div class="mb-2">
                            <strong class="text-dark d-block"><i class="bi bi-chat-left-text-fill text-primary me-1"></i> Comments / Remarks:</strong>
                            <div class="text-muted mt-1 p-2 rounded bg-light" id="edit_rev_comments" style="white-space: pre-line;">No comments entered.</div>
                        </div>
                        <div class="border-top pt-2 mt-2">
                            <strong class="text-dark d-block"><i class="bi bi-info-circle-fill text-info me-1"></i> Special Instructions:</strong>
                            <div class="text-muted mt-1 p-2 rounded bg-light" id="edit_rev_special_instructions" style="white-space: pre-line;">No special instructions entered.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                    <i class="bi bi-pencil-square me-1"></i> Back to Editing
                </button>
                <button type="button" class="btn btn-primary px-4" id="confirmSaveEditBtn">
                    <i class="bi bi-check-circle-fill me-1"></i> Confirm & Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('voucherEditForm');
    const reviewBtn = document.getElementById('btnOpenEditReview');
    const confirmBtn = document.getElementById('confirmSaveEditBtn');
    const modalEl = document.getElementById('reviewEditModal');
    let reviewModal = null;
    if (modalEl && typeof bootstrap !== 'undefined') {
        reviewModal = new bootstrap.Modal(modalEl);
    }

    if (reviewBtn) {
        reviewBtn.addEventListener('click', function() {
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const getVal = (name) => {
                const el = form.querySelector(`[name="${name}"]`);
                return el ? el.value.trim() : '';
            };

            document.getElementById('edit_rev_voucher_type').textContent = getVal('voucher_type') || 'Voucher';
            document.getElementById('edit_rev_voucher_date').textContent = getVal('voucher_date') || 'N/A';
            document.getElementById('edit_rev_group_head').textContent = getVal('group_head') || 'Group Leader / N/A';
            document.getElementById('edit_rev_pax').textContent = getVal('pax') || '0';
            document.getElementById('edit_rev_whatsapp').textContent = getVal('whatsapp') || 'N/A';
            document.getElementById('edit_rev_package_number').textContent = getVal('package_number') || 'N/A';

            // Departure
            const depNo = getVal('dep_flight_number');
            const depSec = getVal('dep_sector');
            const depDate = getVal('dep_date');
            const depTime = getVal('dep_time');
            document.getElementById('edit_rev_dep_flight').textContent = depNo || 'No flight entered';
            document.getElementById('edit_rev_dep_sector').textContent = depSec ? `Sector: ${depSec}` : 'No sector';
            document.getElementById('edit_rev_dep_datetime').textContent = (depDate || depTime) ? `Date: ${depDate} ${depTime}` : '';

            // Return
            const retNo = getVal('ret_flight_number');
            const retSec = getVal('ret_sector');
            const retDate = getVal('ret_date');
            const retTime = getVal('ret_time');
            document.getElementById('edit_rev_ret_flight').textContent = retNo || 'No flight entered';
            document.getElementById('edit_rev_ret_sector').textContent = retSec ? `Sector: ${retSec}` : 'No sector';
            document.getElementById('edit_rev_ret_datetime').textContent = (retDate || retTime) ? `Date: ${retDate} ${retTime}` : '';

            // Comments
            document.getElementById('edit_rev_comments').textContent = getVal('comments') || 'No comments entered.';
            document.getElementById('edit_rev_special_instructions').textContent = getVal('special_instructions') || 'No special instructions entered.';

            if (reviewModal) {
                reviewModal.show();
            }
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving Changes...';
            form.submit();
        });
    }
});
</script>
@endsection
