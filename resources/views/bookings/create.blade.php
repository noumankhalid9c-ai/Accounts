@extends('layouts.app')

@section('title', 'New Booking')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-header-title fs-3 fw-bold mb-1">New Client Booking</h1>
    <p class="page-header-subtitle text-muted mb-0">Record a new visa, ticket, or tour package.</p>
</div>

<div class="row">
    <div class="col-12 col-xl-8">
        <x-ui.card class="border-0 shadow-sm">
            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    
                    <div class="col-md-6">
                        <label class="form-label">Select Client *</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">Choose a client...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Service Type *</label>
                        <select name="service_type" class="form-select" required>
                            <option value="Visa">Visa</option>
                            <option value="Ticket">Ticket</option>
                            <option value="Tour Package">Tour Package</option>
                            <option value="Hotel">Hotel</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Booking Date *</label>
                        <input type="date" name="booking_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Travel Date</label>
                        <input type="date" name="travel_date" class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Package Details</label>
                        <textarea name="package_details" class="form-control" rows="2" placeholder="E.g., 14 Days Umrah Package, 5 Star Hotel"></textarea>
                    </div>

                    <hr class="my-4">
                    
                    <h5 class="fw-bold mb-0">Financials & Payments</h5>

                    <div class="col-md-6">
                        <label class="form-label">Total Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs</span>
                            <input type="number" step="0.01" name="total_amount" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Advance Payment Received</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs</span>
                            <input type="number" step="0.01" name="advance_payment" class="form-control" value="0">
                        </div>
                        <div class="form-text">Remaining balance will be auto-calculated.</div>
                    </div>

                    <hr class="my-4">
                    
                    <h5 class="fw-bold mb-0">B2B Agent & Team (Optional)</h5>

                    <div class="col-md-6">
                        <label class="form-label">B2B Agent</label>
                        <select name="b2b_agent_id" class="form-select">
                            <option value="">None</option>
                            @foreach($agents as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">B2B Commission (Rs)</label>
                        <input type="number" step="0.01" name="b2b_commission" class="form-control" value="0">
                        <div class="form-text">Company Share will be auto-calculated.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assigned Sales Executive</label>
                        <select name="assigned_sales_executive_id" class="form-select">
                            <option value="">None</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}">{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('bookings.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Booking & Open Invoice</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</div>
@endsection
