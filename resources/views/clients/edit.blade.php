@extends('layouts.app')

@section('title', 'Edit Client - ' . $client->company_name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Edit Client: {{ $client->company_name }}</h1>
        <p class="page-header-subtitle text-muted mb-0">Update client profile and contact information.</p>
    </div>
    <div>
        <a href="{{ route('clients.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Clients</span>
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <x-ui.card icon="bi-pencil-square" title="Client Profile Details">
            @if ($errors->any())
                <x-ui.alert type="danger" :dismissible="false">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('clients.update', $client->id) }}">
                @csrf
                @method('PUT')

                <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="letter-spacing: 0.05em;">Primary Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <x-form.input name="company_name" label="Company Name" value="{{ old('company_name', $client->company_name) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="name" label="Client Name" value="{{ old('name', $client->name) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="contact_person" label="Contact Person *" required="true" value="{{ old('contact_person', $client->contact_person) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input type="email" name="email" label="Email Address *" required="true" value="{{ old('email', $client->email) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="phone" label="Phone Number" value="{{ old('phone', $client->phone) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="mobile" label="Mobile Number" value="{{ old('mobile', $client->mobile) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.select name="reference_type" label="Reference / Source" :options="['Direct' => 'Direct Client', 'Vendor' => 'From Vendor']" selected="{{ old('reference_type', $client->reference_type ?? 'Direct') }}" />
                    </div>
                    <div class="col-12 col-md-6" id="vendorNameContainer" style="{{ old('reference_type', $client->reference_type) == 'Vendor' ? '' : 'display:none;' }}">
                        <x-form.select name="vendor_name" label="Select Vendor *" :options="$vendors" selected="{{ old('vendor_name', $client->vendor_name) }}" />
                    </div>
                </div>

                <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="letter-spacing: 0.05em;">Location & Online Presence</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <x-form.input name="country" label="Country" value="{{ old('country', $client->country) }}" />
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="address" class="form-label fw-medium text-dark small">Office Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $client->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="letter-spacing: 0.05em;">Settings & Notes</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <x-form.select name="status" label="Account Status" :options="['Active' => 'Active (Full Service)', 'Prospect' => 'Prospect (Lead)', 'Inactive' => 'Inactive', 'Suspended' => 'Suspended']" selected="{{ old('status', $client->status) }}" />
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-medium text-dark small">Internal Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <a href="{{ route('clients.index') }}" class="btn btn-light border px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Update Client</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refSelect = document.getElementById('reference_type');
        const vendorContainer = document.getElementById('vendorNameContainer');
        
        if (refSelect) {
            const vendorSelect = vendorContainer.querySelector('select');
            
            if (refSelect.value === 'Vendor' && vendorSelect) {
                vendorSelect.required = true;
            }

            refSelect.addEventListener('change', function() {
                if (this.value === 'Vendor') {
                    vendorContainer.style.display = 'block';
                    if (vendorSelect) vendorSelect.required = true;
                } else {
                    vendorContainer.style.display = 'none';
                    if (vendorSelect) {
                        vendorSelect.value = '';
                        vendorSelect.required = false;
                    }
                }
            });
        }
    });
</script>
@endpush
