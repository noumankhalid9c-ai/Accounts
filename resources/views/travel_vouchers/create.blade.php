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
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small">Special Instructions</label>
                            <textarea name="special_instructions" class="form-control" rows="3" placeholder="Any notes for the client or border control...">{{ old('special_instructions') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-magic"></i> Generate & Save</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</div>
@endsection
