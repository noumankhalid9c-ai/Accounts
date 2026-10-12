@extends('layouts.app')

@section('title', 'Recurring Expenses & Generation')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-arrow-repeat text-primary me-2"></i>Recurring Expenses
        </h1>
        <p class="page-header-subtitle text-muted mb-0">
            Define recurring company templates (Rent, Internet, Servers, Subscriptions) and batch generate them monthly.
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#generateModal">
            <i class="bi bi-magic me-1"></i> Generate for Selected Month
        </button>
        <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addRecurringModal">
            <i class="bi bi-plus-circle me-1"></i> Add Recurring Template
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Templates Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1 text-dark">Active Recurring Templates</h5>
            <p class="text-muted small mb-0">{{ $templates->count() }} recurring cost template(s) defined.</p>
        </div>
    </div>
    <div class="card-body p-0 pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Expense Name</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Category</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Default Amount</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Provider</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Frequency</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Due Day</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Start Period</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $tmpl)
                    <tr>
                        <td class="ps-4">
                            <strong class="text-dark">{{ $tmpl->expense_name }}</strong>
                            @if($tmpl->is_utility_bill)
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-0 ms-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-lightning-charge"></i> {{ $tmpl->utility_type ?? 'Utility' }}
                                </span>
                            @endif
                            @if($tmpl->consumer_number)
                                <div class="text-muted small">A/C: {{ $tmpl->consumer_number }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                {{ $tmpl->category->name ?? 'General' }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">
                            PKR {{ number_format($tmpl->default_amount, 2) }}
                        </td>
                        <td>{{ $tmpl->provider_name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 fw-medium">
                                {{ $tmpl->frequency }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold text-secondary">Day {{ $tmpl->due_day ?? 10 }}</span>
                        </td>
                        <td class="small text-muted">
                            {{ \Carbon\Carbon::create($tmpl->start_year, $tmpl->start_month, 1)->format('M Y') }}
                            @if($tmpl->end_year && $tmpl->end_month)
                                &rarr; {{ \Carbon\Carbon::create($tmpl->end_year, $tmpl->end_month, 1)->format('M Y') }}
                            @endif
                        </td>
                        <td>
                            @if($tmpl->status === 'Active')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <button type="button" class="btn btn-sm btn-light border text-secondary" data-bs-toggle="modal" data-bs-target="#editTemplateModal{{ $tmpl->id }}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(auth()->user() && auth()->user()->role_id == 1)
                                <form action="{{ route('monthly_expenses.recurring.destroy', $tmpl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this recurring expense template?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>

                    <!-- Edit Template Modal -->
                    <div class="modal fade" id="editTemplateModal{{ $tmpl->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content border-0 shadow rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Recurring Template</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('monthly_expenses.recurring.update', $tmpl->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-muted">Expense Name *</label>
                                                <input type="text" name="expense_name" class="form-control" value="{{ $tmpl->expense_name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-muted">Category *</label>
                                                <select name="category_id" class="form-select" required>
                                                    @foreach($categories as $c)
                                                        <option value="{{ $c->id }}" {{ $tmpl->category_id == $c->id ? 'selected' : '' }}>
                                                            {{ $c->name }} ({{ $c->group }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-semibold text-muted">Default Amount (PKR) *</label>
                                                <input type="number" step="0.01" name="default_amount" class="form-control" value="{{ $tmpl->default_amount }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-semibold text-muted">Frequency *</label>
                                                <select name="frequency" class="form-select" required>
                                                    <option value="Monthly" {{ $tmpl->frequency == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                    <option value="Quarterly" {{ $tmpl->frequency == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                    <option value="Yearly" {{ $tmpl->frequency == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-semibold text-muted">Due Day of Month</label>
                                                <input type="number" name="due_day" class="form-control" value="{{ $tmpl->due_day ?? 10 }}" min="1" max="31">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-muted">Provider / Vendor</label>
                                                <input type="text" name="provider_name" class="form-control" value="{{ $tmpl->provider_name }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-muted">Preferred Payment Method</label>
                                                <select name="payment_method" class="form-select">
                                                    <option value="Bank Transfer" {{ $tmpl->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                    <option value="Cash" {{ $tmpl->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                    <option value="Online / Card" {{ $tmpl->payment_method == 'Online / Card' ? 'selected' : '' }}>Online / Card</option>
                                                    <option value="Cheque" {{ $tmpl->payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-semibold text-muted">Start Month</label>
                                                <select name="start_month" class="form-select">
                                                    @for($m = 1; $m <= 12; $m++)
                                                        <option value="{{ $m }}" {{ $tmpl->start_month == $m ? 'selected' : '' }}>
                                                            {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-semibold text-muted">Start Year</label>
                                                <input type="number" name="start_year" class="form-control" value="{{ $tmpl->start_year }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-muted">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="Active" {{ $tmpl->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Inactive" {{ $tmpl->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold text-muted">Notes</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $tmpl->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-arrow-repeat fs-1 d-block mb-3 opacity-25"></i>
                            <p class="mb-0">No recurring expense templates defined yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Generate for Selected Month -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-magic text-primary me-2"></i>Generate Recurring Expenses
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('monthly_expenses.recurring.generate') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i> This loads active recurring templates and generates <strong>Pending</strong> expense entries for the selected month. Existing entries for that period are skipped to prevent duplicates.
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Target Month *</label>
                            <select name="generate_month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Target Year *</label>
                            <input type="number" name="generate_year" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-circle me-1"></i> Generate Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Recurring Template -->
<div class="modal fade" id="addRecurringModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-plus-circle text-primary me-2"></i>New Recurring Expense Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('monthly_expenses.recurring.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Expense Name *</label>
                            <input type="text" name="expense_name" class="form-control" placeholder="e.g. Office Rent, PTCL Internet, Adobe Suite" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category...</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->group }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Default Amount (PKR) *</label>
                            <input type="number" step="0.01" name="default_amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Frequency *</label>
                            <select name="frequency" class="form-select" required>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Due Day (1-31)</label>
                            <input type="number" name="due_day" class="form-control" value="10" min="1" max="31">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Provider / Vendor Name</label>
                            <input type="text" name="provider_name" class="form-control" placeholder="e.g. Landlord, PTCL, AWS">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Online / Card">Online / Card</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Start Month</label>
                            <select name="start_month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Start Year</label>
                            <input type="number" name="start_year" class="form-control" value="{{ date('Y') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Status</label>
                            <select name="status" class="form-select">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_utility_bill" value="1" id="rec_is_utility">
                                <label class="form-check-label fw-bold text-dark small" for="rec_is_utility">
                                    Utility Bill Template (specify utility type & consumer number)
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
