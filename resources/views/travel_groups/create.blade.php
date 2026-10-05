@extends('layouts.app')

@section('title', 'Create Travel Group')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Create Travel Group</h1>
    </div>
    <div>
        <a href="{{ route('travel-groups.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <x-ui.card icon="bi-plus-circle" title="Group Details">
            @if ($errors->any())
                <x-ui.alert type="danger" :dismissible="false">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('travel-groups.store') }}">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <x-form.input name="group_id" label="Group ID *" placeholder="e.g. UMR-0001" required="true" value="{{ old('group_id') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="group_name" label="Group Name *" placeholder="e.g. Umrah Group #001" required="true" value="{{ old('group_name') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.select name="service_type" label="Service Type *" :options="['Umrah' => 'Umrah', 'Hajj' => 'Hajj', 'Tour' => 'Tour', 'Ziyarat' => 'Ziyarat', 'Other' => 'Other']" selected="{{ old('service_type', 'Umrah') }}" required="true" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="destination" label="Destination" placeholder="e.g. Makkah / Madinah" value="{{ old('destination') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input type="date" name="departure_date" label="Departure Date" value="{{ old('departure_date') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input type="date" name="return_date" label="Return Date" value="{{ old('return_date') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.select name="status" label="Status *" :options="['Draft' => 'Draft', 'Confirmed' => 'Confirmed', 'In Progress' => 'In Progress', 'Travel Completed' => 'Travel Completed', 'Cancelled' => 'Cancelled']" selected="{{ old('status', 'Draft') }}" required="true" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.input name="group_leader" label="Group Leader / Guide" value="{{ old('group_leader') }}" />
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <a href="{{ route('travel-groups.index') }}" class="btn btn-light border px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Create Group</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</div>
@endsection
