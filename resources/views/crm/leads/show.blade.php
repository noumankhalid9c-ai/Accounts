@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Lead Details: {{ $lead->customer_name }}</h1>
        <a href="{{ route('crm.pipeline') }}" class="btn btn-secondary">Back to Pipeline</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Main Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Customer & Lead Profile</div>
                <div class="card-body">
                    <p><strong>Phone:</strong> {{ $lead->phone }}</p>
                    <p><strong>Inquiry Type:</strong> {{ $lead->inquiry_type }}</p>
                    <p><strong>Destination:</strong> {{ $lead->destination }}</p>
                    <p><strong>Travel Date:</strong> {{ $lead->travel_date }}</p>
                    <p><strong>Pax:</strong> {{ $lead->pax }}</p>
                    <p><strong>Source:</strong> {{ $lead->source }}</p>
                    <p><strong>Stage:</strong> <span class="badge bg-warning text-dark">{{ $lead->stage }}</span></p>
                    <p><strong>Assigned To:</strong> {{ $lead->assignedTo->name ?? 'None' }}</p>
                    <p><strong>Notes:</strong> {{ $lead->notes }}</p>
                    
                    <hr>
                    <form action="{{ route('crm.leads.stage', $lead->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label>Update Pipeline Stage</label>
                            <select name="stage" class="form-select">
                                @foreach(['New Inquiry', 'Contacted', 'Follow-up', 'Quotation Sent', 'Negotiation', 'Confirmed', 'Converted', 'Lost'] as $opt)
                                    <option value="{{ $opt }}" {{ $lead->stage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Stage</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Follow-ups & Activities -->
        <div class="col-md-8">
            <!-- Add Follow-up -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Add Follow-up</div>
                <div class="card-body">
                    <form action="{{ route('crm.leads.followup', $lead->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label>Date</label>
                                <input type="date" name="followup_date" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Time</label>
                                <input type="time" name="followup_time" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="Phone Call">Phone Call</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Email">Email</option>
                                    <option value="Meeting">Meeting</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Result/Status</label>
                                <input type="text" name="result" class="form-control" placeholder="e.g. Interested, Call Back">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-10">
                                <input type="text" name="notes" class="form-control" placeholder="Follow-up notes...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab">Activity Timeline</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="followups-tab" data-bs-toggle="tab" data-bs-target="#followups" type="button" role="tab">Follow-ups History</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <!-- Activities -->
                <div class="tab-pane fade show active p-3 border border-top-0 bg-white" id="activities" role="tabpanel">
                    <ul class="list-group list-group-flush">
                        @foreach($lead->activities->sortByDesc('created_at') as $activity)
                            <li class="list-group-item">
                                <strong>{{ $activity->created_at->format('d M Y H:i') }}</strong> 
                                - <span class="badge bg-secondary">{{ $activity->activity_type }}</span>
                                - <em>{{ $activity->description }}</em>
                                @if($activity->old_value || $activity->new_value)
                                    <br><small class="text-muted">Changed from [{{ $activity->old_value }}] to [{{ $activity->new_value }}]</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <!-- Followups -->
                <div class="tab-pane fade p-3 border border-top-0 bg-white" id="followups" role="tabpanel">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Type</th>
                                <th>Result</th>
                                <th>Notes</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lead->followups->sortByDesc('followup_date') as $fup)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($fup->followup_date)->format('d M Y') }} {{ $fup->followup_time }}</td>
                                    <td>{{ $fup->type }}</td>
                                    <td>{{ $fup->result }}</td>
                                    <td>{{ $fup->notes }}</td>
                                    <td>{{ $fup->assignedTo->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
