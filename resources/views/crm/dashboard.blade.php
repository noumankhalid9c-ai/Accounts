@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i> CRM Marketing & Sales Dashboard</h2>
        <div>
            <a href="{{ route('crm.leads') }}" class="btn btn-outline-primary me-2"><i class="bi bi-funnel"></i> View All Leads</a>
            <a href="{{ route('crm.pipeline') }}" class="btn btn-primary"><i class="bi bi-kanban me-1"></i> Open Pipeline</a>
        </div>
    </div>

    <!-- Top KPI Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #3b82f6 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Total Customers</span>
                        <i class="bi bi-people fs-4 text-primary opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalCustomers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">New Inquiries</span>
                        <i class="bi bi-inbox fs-4 text-warning opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $newInquiries }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Converted Deals</span>
                        <i class="bi bi-check-circle fs-4 text-success opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $convertedLeads }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-uppercase text-white-50" style="font-size: 0.8rem;">Follow-ups Today</span>
                        <i class="bi bi-telephone-outbound fs-4 text-white-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-white">{{ $followupsToday }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Leads by Stage -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-steps text-primary me-2"></i> Sales Funnel (By Stage)</h6>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                        @forelse($leadsByStage as $stage => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-light">
                            <span class="text-secondary fw-bold small">{{ $stage }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                        </li>
                        @empty
                        <li class="list-group-item px-0 border-0 text-muted small">No leads found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Leads by Source -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-magnet text-info me-2"></i> Marketing Sources</h6>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                        @forelse($leadsBySource as $source => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-light">
                            <span class="text-secondary fw-bold small">{{ $source }}</span>
                            <span class="badge bg-info rounded-pill">{{ $count }}</span>
                        </li>
                        @empty
                        <li class="list-group-item px-0 border-0 text-muted small">No source data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Leads by Service -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-briefcase text-success me-2"></i> Top Services / Inquiries</h6>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                        @forelse($leadsByService as $service => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-light">
                            <span class="text-secondary fw-bold small">{{ $service }}</span>
                            <span class="badge bg-success rounded-pill">{{ $count }}</span>
                        </li>
                        @empty
                        <li class="list-group-item px-0 border-0 text-muted small">No service data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Upcoming Follow-ups -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0"><i class="bi bi-calendar-event text-danger me-2"></i> Upcoming Follow-ups</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingFollowups as $f)
                            <a href="{{ route('crm.leads.show', $f->lead_id) }}" class="list-group-item list-group-item-action py-3 border-light">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $f->lead->customer->name ?? 'Unknown Customer' }}</h6>
                                    <small class="text-danger fw-bold"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($f->followup_date)->format('d M, Y') }} {{ $f->followup_time ? \Carbon\Carbon::parse($f->followup_time)->format('h:i A') : '' }}</small>
                                </div>
                                <p class="mb-1 text-muted small">{{ Str::limit($f->notes, 60) }}</p>
                                <small class="text-primary fw-bold"><i class="bi bi-headset me-1"></i>{{ $f->type }}</small>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted small">
                                <i class="bi bi-emoji-smile fs-3 d-block mb-2 opacity-50"></i>
                                No upcoming follow-ups scheduled.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0"><i class="bi bi-activity text-primary me-2"></i> Recent Team Activities</h6>
                </div>
                <div class="card-body">
                    <div class="timeline" style="border-left: 2px solid #e2e8f0; margin-left: 10px; padding-left: 20px;">
                        @forelse($recentActivities as $activity)
                            <div class="position-relative mb-4">
                                <span class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -27px; top: 5px;"></span>
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0 fw-bold small text-dark">{{ $activity->activity_type }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-secondary small">{{ $activity->description }}</p>
                                <small class="text-muted" style="font-size: 0.7rem;">By: <strong>{{ $activity->user->name ?? 'System' }}</strong> for Lead #{{ $activity->lead_id }}</small>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-4">
                                No recent activity logged.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection