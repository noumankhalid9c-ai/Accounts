@extends('layouts.app')
@section('title', 'Inquiries / Leads')

@section('content')
<div class="container-fluid py-3">
    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">
                <i class="bi bi-person-lines-fill text-primary me-2"></i> Inquiries & Leads
            </h2>
            <p class="text-muted small mb-0">Track inquiries, customer requests, remarks, and sales team communication history.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('crm.pipeline') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-kanban me-1"></i> Pipeline Board
            </a>
            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#newInquiryCollapse" aria-expanded="false" aria-controls="newInquiryCollapse">
                <i class="bi bi-plus-circle me-1"></i> New Inquiry
            </button>
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

    <!-- Quick Add Inquiry Collapsible Form -->
    <div class="collapse mb-4 {{ $errors->any() ? 'show' : '' }}" id="newInquiryCollapse">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                <h5 class="card-title fw-bold mb-0">
                    <i class="bi bi-plus-circle-fill me-2"></i> Add New Lead / Inquiry
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="collapse" data-bs-target="#newInquiryCollapse"></button>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('crm.leads.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Full Name" value="{{ old('customer_name') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Phone / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control form-control-sm" placeholder="e.g. 03001234567" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Inquiry Type <span class="text-danger">*</span></label>
                            <select name="inquiry_type" class="form-select form-select-sm" required>
                                <option value="">Select Type</option>
                                <option value="Umrah" {{ old('inquiry_type') == 'Umrah' ? 'selected' : '' }}>Umrah</option>
                                <option value="Hajj" {{ old('inquiry_type') == 'Hajj' ? 'selected' : '' }}>Hajj</option>
                                <option value="Airline Ticket" {{ old('inquiry_type') == 'Airline Ticket' ? 'selected' : '' }}>Airline Ticket</option>
                                <option value="Visa Service" {{ old('inquiry_type') == 'Visa Service' ? 'selected' : '' }}>Visa Service</option>
                                <option value="Hotel Booking" {{ old('inquiry_type') == 'Hotel Booking' ? 'selected' : '' }}>Hotel Booking</option>
                                <option value="Tour Package" {{ old('inquiry_type') == 'Tour Package' ? 'selected' : '' }}>Tour Package</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" class="form-control form-control-sm" placeholder="e.g. Makkah / Madinah" value="{{ old('destination') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Travel Date <span class="text-danger">*</span></label>
                            <input type="date" name="travel_date" class="form-control form-control-sm" value="{{ old('travel_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Pax (Travelers) <span class="text-danger">*</span></label>
                            <input type="number" name="pax" class="form-control form-control-sm" min="1" value="{{ old('pax', 1) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Lead Source <span class="text-danger">*</span></label>
                            <select name="source" class="form-select form-select-sm" required>
                                <option value="Direct Call" {{ old('source') == 'Direct Call' ? 'selected' : '' }}>Direct Call</option>
                                <option value="WhatsApp" {{ old('source') == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="Walk-in" {{ old('source') == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                                <option value="Facebook" {{ old('source') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="Referral" {{ old('source') == 'Referral' ? 'selected' : '' }}>Referral</option>
                                <option value="Website" {{ old('source') == 'Website' ? 'selected' : '' }}>Website</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Budget (PKR)</label>
                            <input type="number" step="any" name="budget" class="form-control form-control-sm" placeholder="e.g. 250000" value="{{ old('budget') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Initial Notes / Requirements</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Specific client requirements, hotel preferences, budget constraints..." value="{{ old('notes') }}">
                        </div>

                        <div class="col-12 text-end pt-2">
                            <button type="button" class="btn btn-light btn-sm rounded-pill px-4 me-2" data-bs-toggle="collapse" data-bs-target="#newInquiryCollapse">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Save Lead
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Leads Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark fs-6">All Inquiries</span>
                <span class="badge bg-primary rounded-pill px-3">{{ $leads->count() }} Total</span>
            </div>
            <div class="text-muted small">
                <i class="bi bi-chat-quote-fill text-primary me-1"></i> Click <strong>Comments</strong> on any lead to view or add notes.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Lead / Customer</th>
                        <th>Contact</th>
                        <th>Inquiry Details</th>
                        <th>Stage</th>
                        <th>Assigned Sales</th>
                        <th class="text-center">Comments Loop</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width: 38px; height: 38px; font-size: 0.9rem; background-color: #4f46e5; color: white;">
                                    {{ strtoupper(substr($lead->customer_name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('crm.leads.show', $lead->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                        {{ $lead->customer_name }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Ref: {{ $lead->lead_code ?? ('LD-' . str_pad($lead->id, 5, '0', STR_PAD_LEFT)) }}
                                        &middot; {{ $lead->created_at ? $lead->created_at->diffForHumans() : '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium text-dark small">
                                <i class="bi bi-telephone me-1 text-muted"></i>{{ $lead->phone }}
                            </div>
                            @if($lead->whatsapp)
                                <div style="font-size: 0.75rem;">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->whatsapp) }}" target="_blank" class="text-success text-decoration-none">
                                        <i class="bi bi-whatsapp me-1"></i>{{ $lead->whatsapp }}
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span class="badge bg-light text-primary border border-primary-subtle fw-semibold" style="font-size: 0.75rem;">
                                    {{ $lead->inquiry_type }}
                                </span>
                                <span class="text-muted small">&bull; {{ $lead->pax ?? 1 }} Pax</span>
                            </div>
                            <div class="text-muted small" style="font-size: 0.8rem;">
                                <i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $lead->destination }}
                                @if($lead->travel_date)
                                    &middot; <i class="bi bi-calendar3 ms-1 text-secondary"></i> {{ \Carbon\Carbon::parse($lead->travel_date)->format('d M') }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $stageBadgeMap = [
                                    'New Inquiry' => 'bg-info-subtle text-info border-info-subtle',
                                    'Contacted' => 'bg-primary-subtle text-primary border-primary-subtle',
                                    'Follow-up' => 'bg-warning-subtle text-warning border-warning-subtle',
                                    'Quotation Sent' => 'bg-purple-subtle text-purple border-purple-subtle',
                                    'Negotiation' => 'bg-orange-subtle text-orange border-orange-subtle',
                                    'Confirmed' => 'bg-success-subtle text-success border-success-subtle',
                                    'Converted' => 'bg-success text-white border-success',
                                    'Lost' => 'bg-danger-subtle text-danger border-danger-subtle',
                                ];
                                $badgeClass = $stageBadgeMap[$lead->stage] ?? 'bg-light text-dark border';
                            @endphp
                            <span class="badge {{ $badgeClass }} border px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                {{ $lead->stage }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-medium text-dark">
                                <i class="bi bi-person me-1 text-muted"></i>{{ $lead->assignedTo->name ?? 'Unassigned' }}
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                Source: {{ $lead->source ?? 'Direct' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <!-- Comments Trigger Button -->
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-xs position-relative" 
                                    data-bs-toggle="modal" data-bs-target="#commentsModal-{{ $lead->id }}"
                                    title="View conversation loop and add comments">
                                <i class="bi bi-chat-left-dots me-1"></i> Comments
                                <span class="badge {{ $lead->comments->count() > 0 ? 'bg-primary text-white' : 'bg-light text-muted border' }} rounded-pill ms-1">
                                    {{ $lead->comments->count() }}
                                </span>
                            </button>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1 align-items-center">
                                <a href="{{ route('crm.leads.show', $lead->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary" title="View Full Lead Details">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>

                                @can('delete', $lead)
                                <form action="{{ route('crm.leads.destroy', $lead->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this inquiry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Delete Lead">
                                        <i class="bi bi-trash3" style="font-size: 0.8rem;"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    <!-- ======================================================== -->
                    <!-- Comments Loop Modal for Lead #{{ $lead->id }} -->
                    <!-- ======================================================== -->
                    <div class="modal fade" id="commentsModal-{{ $lead->id }}" tabindex="-1" aria-labelledby="commentsModalLabel-{{ $lead->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header bg-light border-bottom py-3 px-4">
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="commentsModalLabel-{{ $lead->id }}">
                                            <i class="bi bi-chat-left-dots-fill text-primary"></i>
                                            <span>Comments History: {{ $lead->customer_name }}</span>
                                        </h5>
                                        <div class="text-muted small mt-1">
                                            <span>{{ $lead->inquiry_type }} &middot; {{ $lead->destination }} &middot; Pax: {{ $lead->pax ?? 1 }}</span>
                                            <span class="badge bg-light text-dark border ms-2">{{ $lead->stage }}</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-4">
                                    <!-- Comment Add Form Inside Modal -->
                                    <div class="p-3 bg-light rounded-3 mb-4 border">
                                        <form action="{{ route('crm.leads.comments.store', $lead->id) }}" method="POST">
                                            @csrf
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="user-avatar" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="small text-muted">
                                                    Posting comment as <strong class="text-dark">{{ Auth::user()->name }}</strong> 
                                                    <span class="badge bg-white text-primary border ms-1">{{ Auth::user()->role_name }}</span>
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <textarea name="comment" class="form-control rounded-3" rows="3" 
                                                    placeholder="Write your note, update, quotation detail, or customer remark..." required></textarea>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Saved to lead conversation timeline</small>
                                                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-sm">
                                                    <i class="bi bi-send-fill me-1"></i> Post Comment
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Comments Stream / History Loop -->
                                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-clock-history me-1 text-primary"></i> Conversation History Loop</span>
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill">{{ $lead->comments->count() }} {{ Str::plural('Entry', $lead->comments->count()) }}</span>
                                    </h6>

                                    <div class="comments-modal-scroll" style="max-height: 380px; overflow-y: auto;">
                                        @forelse($lead->comments as $comment)
                                            <div class="p-3 mb-2 rounded-3 bg-white border shadow-xs">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($comment->user && $comment->user->profile_picture)
                                                            <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                                                        @else
                                                            <div class="user-avatar" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                                {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.88rem;">
                                                                <span>{{ $comment->user->name ?? 'User' }}</span>
                                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.68rem;">
                                                                    {{ $comment->user->role_name ?? 'Sales' }}
                                                                </span>
                                                            </div>
                                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                                <i class="bi bi-clock me-1"></i>{{ $comment->created_at->format('d M Y, h:i A') }} ({{ $comment->created_at->diffForHumans() }})
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($comment->user_id === Auth::id() || Auth::user()->isSuperAdmin() || Auth::user()->hasRole('admin'))
                                                    <form action="{{ route('crm.leads.comments.destroy', [$lead->id, $comment->id]) }}" method="POST" onsubmit="return confirm('Delete this comment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0" title="Delete comment" style="text-decoration: none; font-size: 0.8rem;">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                                <div class="comment-text text-dark ps-2" style="font-size: 0.88rem; line-height: 1.5; white-space: pre-wrap;">{{ $comment->comment }}</div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 bg-light border border-dashed rounded-3">
                                                <i class="bi bi-chat-square-dots text-muted fs-3 d-block mb-1"></i>
                                                <p class="text-muted small mb-0">No comments posted yet for this lead. Be the first to add a note above!</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="modal-footer bg-light border-top py-2 px-4 justify-content-between">
                                    <a href="{{ route('crm.leads.show', $lead->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-arrow-up-right-square me-1"></i> Open Full Lead Page
                                    </a>
                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Comments Modal -->

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                                <h6 class="fw-bold text-dark">No Inquiries Found</h6>
                                <p class="text-muted small mb-3">Add your first inquiry above using "+ New Inquiry" to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection