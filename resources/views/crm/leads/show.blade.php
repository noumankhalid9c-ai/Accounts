@extends('layouts.app')
@section('title', 'Lead Details - ' . $lead->customer_name)

@section('content')
<div class="container-fluid py-3">
    <!-- Header Breadcrumb & Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('crm.leads') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Inquiries & Leads
                </a>
                <span class="text-muted small">/</span>
                <span class="text-dark small fw-semibold">Lead #{{ $lead->id }}</span>
            </div>
            <h2 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span>{{ $lead->customer_name }}</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-1 rounded-pill">
                    {{ $lead->inquiry_type ?? 'Inquiry' }}
                </span>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-6 px-3 py-1 rounded-pill">
                    {{ $lead->stage }}
                </span>
            </h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('crm.pipeline') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold">
                <i class="bi bi-kanban me-1"></i> Pipeline Board
            </a>
            <a href="{{ route('crm.leads') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold">
                <i class="bi bi-list-ul me-1"></i> All Leads
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Customer & Lead Profile -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i> Lead Profile
                    </h5>
                    <span class="badge bg-light text-muted border px-2 py-1 small">
                        Ref: {{ $lead->lead_code ?? ('LD-' . str_pad($lead->id, 5, '0', STR_PAD_LEFT)) }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-person me-1"></i> Customer Name</span>
                            <span class="fw-semibold text-dark">{{ $lead->customer_name }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-telephone me-1"></i> Phone / Mobile</span>
                            <span class="fw-semibold text-dark">
                                <a href="tel:{{ $lead->phone }}" class="text-decoration-none text-dark">{{ $lead->phone ?? 'N/A' }}</a>
                            </span>
                        </li>
                        @if($lead->whatsapp)
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp</span>
                            <span class="fw-semibold text-dark">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->whatsapp) }}" target="_blank" class="text-success text-decoration-none">
                                    {{ $lead->whatsapp }} <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            </span>
                        </li>
                        @endif
                        @if($lead->email)
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-envelope me-1"></i> Email</span>
                            <span class="fw-semibold text-dark">{{ $lead->email }}</span>
                        </li>
                        @endif
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i> Destination</span>
                            <span class="fw-semibold text-dark">{{ $lead->destination ?? 'N/A' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-calendar-event me-1"></i> Travel Date</span>
                            <span class="fw-semibold text-dark">
                                {{ $lead->travel_date ? \Carbon\Carbon::parse($lead->travel_date)->format('d M Y') : 'N/A' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-people me-1"></i> Pax (Travelers)</span>
                            <span class="fw-semibold text-dark">{{ $lead->pax ?? 1 }} Person(s)</span>
                        </li>
                        @if($lead->budget)
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-cash-stack me-1"></i> Budget</span>
                            <span class="fw-bold text-success">PKR {{ number_format((float)$lead->budget, 0) }}</span>
                        </li>
                        @endif
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-diagram-3 me-1"></i> Source</span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                {{ $lead->source ?? 'Direct' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-person-check me-1"></i> Assigned Sales</span>
                            <span class="fw-semibold text-dark">{{ $lead->assignedTo->name ?? 'Unassigned' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted small"><i class="bi bi-clock-history me-1"></i> Created On</span>
                            <span class="text-muted small">{{ $lead->created_at ? $lead->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                        </li>
                    </ul>

                    @if($lead->notes)
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="fw-semibold text-dark small mb-1"><i class="bi bi-sticky me-1 text-warning"></i> Initial Notes:</div>
                        <div class="text-muted small lh-sm">{!! nl2br(e($lead->notes)) !!}</div>
                    </div>
                    @endif

                    <!-- Quick Stage Update -->
                    <div class="p-3 border rounded-3 bg-light">
                        <label class="form-label fw-bold text-dark small mb-2">
                            <i class="bi bi-shuffle text-primary me-1"></i> Change Pipeline Stage
                        </label>
                        <form action="{{ route('crm.leads.stage', $lead->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <select name="stage" class="form-select form-select-sm">
                                    @foreach(['New Inquiry', 'Contacted', 'Follow-up', 'Quotation Sent', 'Negotiation', 'Confirmed', 'Converted', 'Lost'] as $opt)
                                        <option value="{{ $opt }}" {{ $lead->stage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Comments History Loop & Additional CRM Tools -->
        <div class="col-lg-8">
            <!-- Comments & Discussion Loop Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-chat-left-dots-fill text-primary"></i>
                        </div>
                        <div>
                            <h5 class="card-title fw-bold text-dark mb-0">Comments & History Loop</h5>
                            <span class="text-muted small">Record remarks, client discussions, and sales updates</span>
                        </div>
                    </div>
                    <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold">
                        {{ $lead->comments->count() }} {{ Str::plural('Comment', $lead->comments->count()) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <!-- New Comment Form -->
                    <form action="{{ route('crm.leads.comments.store', $lead->id) }}" method="POST" class="mb-4 pb-3 border-bottom">
                        @csrf
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="user-avatar" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="small text-muted">
                                Adding comment as <strong class="text-dark">{{ Auth::user()->name ?? 'User' }}</strong> 
                                <span class="badge bg-light text-primary border ms-1">{{ Auth::user()->role_name ?? 'Sales' }}</span>
                            </span>
                        </div>

                        <div class="mb-2">
                            <textarea name="comment" class="form-control rounded-3" rows="3" 
                                placeholder="Type your comment, inquiry update, discussion note, or quotation remark here..." 
                                required></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small" style="font-size: 0.8rem;">
                                <i class="bi bi-info-circle me-1"></i> Comments are tracked in this lead's permanent timeline.
                            </span>
                            <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Post Comment
                            </button>
                        </div>
                    </form>

                    <!-- Comments Loop -->
                    <div class="comments-loop-container" style="max-height: 520px; overflow-y: auto;">
                        @forelse($lead->comments as $comment)
                            <div class="comment-card p-3 mb-3 rounded-3 bg-light border transition-hover position-relative">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($comment->user && $comment->user->profile_picture)
                                            <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;">
                                        @else
                                            <div class="user-avatar" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                                {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                                                <span>{{ $comment->user->name ?? 'System User' }}</span>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.7rem; font-weight: 500;">
                                                    {{ $comment->user->role_name ?? 'Sales' }}
                                                </span>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $comment->created_at->format('d M Y, h:i A') }} 
                                                <span class="ms-1 text-secondary">({{ $comment->created_at->diffForHumans() }})</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($comment->user_id === Auth::id() || Auth::user()->isSuperAdmin() || Auth::user()->hasRole('admin'))
                                    <form action="{{ route('crm.leads.comments.destroy', [$lead->id, $comment->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0 ms-2" title="Delete comment" style="text-decoration: none; font-size: 0.85rem;">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                <div class="comment-body text-dark ps-2" style="font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap;">{{ $comment->comment }}</div>
                            </div>
                        @empty
                            <div class="text-center py-5 bg-white border border-dashed rounded-3">
                                <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-muted">
                                    <i class="bi bi-chat-square-text fs-2"></i>
                                </div>
                                <h6 class="fw-semibold text-dark mb-1">No comments posted yet</h6>
                                <p class="text-muted small mb-0">Every sales person or admin can write updates and build a conversation history loop above.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Follow-ups & Activity Logs Sub-Tabs -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <ul class="nav nav-pills card-header-pills gap-2" id="leadTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-3 py-1 fw-semibold small" id="followups-tab" data-bs-toggle="pill" data-bs-target="#followups" type="button" role="tab">
                                <i class="bi bi-calendar-check me-1"></i> Follow-ups History ({{ $lead->followups->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-1 fw-semibold small" id="schedule-tab" data-bs-toggle="pill" data-bs-target="#schedule" type="button" role="tab">
                                <i class="bi bi-plus-circle me-1"></i> Schedule Follow-up
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-1 fw-semibold small" id="activities-tab" data-bs-toggle="pill" data-bs-target="#activities" type="button" role="tab">
                                <i class="bi bi-activity me-1"></i> Activity Timeline ({{ $lead->activities->count() }})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="leadTabsContent">
                        <!-- Followups List Tab -->
                        <div class="tab-pane fade show active" id="followups" role="tabpanel">
                            @if($lead->followups->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-calendar2-x fs-3 d-block mb-1"></i>
                                    No follow-ups recorded. Use the "Schedule Follow-up" tab to set a reminder.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Medium</th>
                                                <th>Result / Status</th>
                                                <th>Notes</th>
                                                <th>By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            @foreach($lead->followups->sortByDesc('followup_date') as $fup)
                                                <tr>
                                                    <td class="fw-semibold">
                                                        {{ \Carbon\Carbon::parse($fup->followup_date)->format('d M Y') }}
                                                        <span class="text-muted">{{ $fup->followup_time }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">{{ $fup->type }}</span>
                                                    </td>
                                                    <td>{{ $fup->result ?? '-' }}</td>
                                                    <td>{{ $fup->notes ?? '-' }}</td>
                                                    <td>{{ $fup->assignedTo->name ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Schedule Follow-up Tab -->
                        <div class="tab-pane fade" id="schedule" role="tabpanel">
                            <form action="{{ route('crm.leads.followup', $lead->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Follow-up Date <span class="text-danger">*</span></label>
                                        <input type="date" name="followup_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Time</label>
                                        <input type="time" name="followup_time" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Type <span class="text-danger">*</span></label>
                                        <select name="type" class="form-select form-select-sm" required>
                                            <option value="Phone Call">Phone Call</option>
                                            <option value="WhatsApp">WhatsApp</option>
                                            <option value="Email">Email</option>
                                            <option value="Office Meeting">Office Meeting</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Result / Expected Outcome</label>
                                        <input type="text" name="result" class="form-control form-control-sm" placeholder="e.g. Call back after 2pm, Quotation discussion">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Next Action Date</label>
                                        <input type="date" name="next_followup_date" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Follow-up Details</label>
                                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Add specific discussion topic or action required...">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold rounded-pill">
                                            <i class="bi bi-calendar-plus me-1"></i> Save Follow-up
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Activities Tab -->
                        <div class="tab-pane fade" id="activities" role="tabpanel">
                            <ul class="list-group list-group-flush small">
                                @forelse($lead->activities->sortByDesc('created_at') as $activity)
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge bg-secondary-subtle text-secondary border me-2">{{ $activity->activity_type }}</span>
                                            <span class="text-dark">{{ $activity->description }}</span>
                                            @if($activity->old_value || $activity->new_value)
                                                <div class="text-muted small mt-1">Changed from <code>{{ $activity->old_value }}</code> &rarr; <code>{{ $activity->new_value }}</code></div>
                                            @endif
                                        </div>
                                        <div class="text-muted text-nowrap" style="font-size: 0.75rem;">
                                            {{ $activity->created_at->format('d M, h:i A') }}
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted py-3">No activity logs recorded.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
