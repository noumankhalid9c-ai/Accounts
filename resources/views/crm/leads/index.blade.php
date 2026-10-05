@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1>Inquiries / Leads</h1>
    <!-- Quick Add Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>+ New Inquiry</h4>
            <form action="{{ route('crm.leads.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3"><input type="text" name="customer_name" class="form-control" placeholder="Customer Name" required></div>
                    <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Phone / WhatsApp" required></div>
                    <div class="col-md-2">
                        <select name="inquiry_type" class="form-control" required>
                            <option value="">Type</option>
                            <option value="Umrah">Umrah</option>
                            <option value="Hajj">Hajj</option>
                            <option value="Tour">Tour</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input type="text" name="destination" class="form-control" placeholder="Destination" required></div>
                    <div class="col-md-2"><input type="date" name="travel_date" class="form-control" required></div>
                    <div class="col-md-1"><input type="number" name="pax" class="form-control" placeholder="Pax" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <select name="source" class="form-control" required>
                            <option value="">Source</option>
                            <option value="WhatsApp">WhatsApp</option>
                            <option value="Facebook">Facebook</option>
                        </select>
                    </div>
                    <div class="col-md-7"><input type="text" name="notes" class="form-control" placeholder="Notes"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Save</button></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Destination</th>
                        <th>Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr>
                        <td>{{ $lead->customer_name }}</td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->inquiry_type }}</td>
                        <td>{{ $lead->destination }}</td>
                        <td>{{ $lead->stage }}</td>
                        <td>
                            <!-- No delete button for sales -->
                            @can('delete', $lead)
                            <form action="{{ route('crm.leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection