@extends('layouts.app')

@section('title', 'Edit Monthly Expense: ' . $expense->expense_code)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Expense: {{ $expense->expense_code }}
        </h1>
        <p class="page-header-subtitle text-muted mb-0">Update expense particulars, amount, or bill attachment.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('monthly_expenses.show', $expense->id) }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-eye me-1"></i> View Details
        </a>
        <a href="{{ route('monthly_expenses.index') }}" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-4 mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('monthly_expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Section 1: Basic Information -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        <h5 class="fw-bold mb-0 text-dark">Expense Particulars</h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Expense Title *</label>
                            <input type="text" name="expense_title" class="form-control" value="{{ old('expense_title', $expense->expense_title) }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Expense Category *</label>
                            <select name="category_id" class="form-select" required>
                                @php
                                    $grouped = $categories->groupBy('group');
                                @endphp
                                @foreach($grouped as $grp => $cats)
                                    <optgroup label="{{ $grp ?? 'Other' }}">
                                        @foreach($cats as $c)
                                            <option value="{{ $c->id }}" {{ old('category_id', $expense->category_id) == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Expense Month *</label>
                            <select name="expense_month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('expense_month', $expense->expense_month) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Expense Year *</label>
                            <input type="number" name="expense_year" class="form-control" value="{{ old('expense_year', $expense->expense_year) }}" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Expense Date *</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '') }}" required>
                        </div>
                    </div>

                    <!-- Section 2: Financials & Status -->
                    <hr class="my-4 text-muted opacity-25">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        <h5 class="fw-bold mb-0 text-dark">Financial & Status Information</h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Total Expense Amount (PKR) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Payment Status *</label>
                            <select name="payment_status" class="form-select" required>
                                <option value="Pending" {{ old('payment_status', $expense->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Partially Paid" {{ old('payment_status', $expense->payment_status) == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                                <option value="Paid" {{ old('payment_status', $expense->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Overdue" {{ old('payment_status', $expense->payment_status) == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="Cancelled" {{ old('payment_status', $expense->payment_status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Provider / Vendor / Paid To</label>
                            <input type="text" name="provider_name" class="form-control" value="{{ old('provider_name', $expense->provider_name) }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Bill / Invoice Reference #</label>
                            <input type="text" name="bill_reference" class="form-control" value="{{ old('bill_reference', $expense->bill_reference) }}">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $expense->due_date ? $expense->due_date->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $expense->payment_date ? $expense->payment_date->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="Bank Transfer" {{ old('payment_method', $expense->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Cash" {{ old('payment_method', $expense->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Online / Card" {{ old('payment_method', $expense->payment_method) == 'Online / Card' ? 'selected' : '' }}>Online / Card</option>
                                <option value="Cheque" {{ old('payment_method', $expense->payment_method) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Other" {{ old('payment_method', $expense->payment_method) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 3: Utility Bill Specific Details -->
                    <hr class="my-4 text-muted opacity-25">
                    <div class="p-3 bg-light rounded-4 mb-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_utility_bill" id="is_utility_bill_edit" value="1" {{ old('is_utility_bill', $expense->is_utility_bill) ? 'checked' : '' }} onchange="toggleUtilityFieldsEdit()">
                            <label class="form-check-label fw-bold text-dark" for="is_utility_bill_edit">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Mark as Utility Bill
                            </label>
                        </div>

                        <div id="utility_fields_edit" style="{{ old('is_utility_bill', $expense->is_utility_bill) ? 'display: block;' : 'display: none;' }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Utility Type</label>
                                    <select name="utility_type" class="form-select">
                                        <option value="">Select Type...</option>
                                        @foreach(['Electricity', 'Gas', 'Telephone', 'Mobile', 'Internet', 'Water', 'Other'] as $ut)
                                            <option value="{{ $ut }}" {{ old('utility_type', $expense->utility_type) == $ut ? 'selected' : '' }}>{{ $ut }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Consumer / Account #</label>
                                    <input type="text" name="consumer_number" class="form-control" value="{{ old('consumer_number', $expense->consumer_number) }}">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Bill Issue Date</label>
                                    <input type="date" name="bill_issue_date" class="form-control" value="{{ old('bill_issue_date', $expense->bill_issue_date ? $expense->bill_issue_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Notes and Document Attachment -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Notes / Details</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-paperclip me-1"></i> Replace Bill / Receipt Attachment
                            </label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            @if($expense->attachment_path)
                                <div class="mt-2 small text-muted">
                                    <i class="bi bi-file-earmark-check text-success me-1"></i> Current file:
                                    <a href="{{ route('monthly_expenses.attachment', $expense->id) }}" target="_blank" class="fw-bold text-decoration-none">
                                        {{ $expense->attachment_name ?? 'View Attachment' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('monthly_expenses.show', $expense->id) }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-circle me-1"></i> Update Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleUtilityFieldsEdit() {
    const chk = document.getElementById('is_utility_bill_edit');
    const box = document.getElementById('utility_fields_edit');
    box.style.display = chk.checked ? 'block' : 'none';
}
</script>
@endpush
@endsection
