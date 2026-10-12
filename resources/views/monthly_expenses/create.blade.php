@extends('layouts.app')

@section('title', 'Add Monthly Expense')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">
            <i class="bi bi-plus-circle-fill text-primary me-2"></i>Add Monthly Expense
        </h1>
        <p class="page-header-subtitle text-muted mb-0">Record office recurring bills, operational expenses, and utilities.</p>
    </div>
    <a href="{{ route('monthly_expenses.index') }}" class="btn btn-light border rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to All Expenses
    </a>
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

                <form action="{{ route('monthly_expenses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Section 1: Basic Information -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        <h5 class="fw-bold mb-0 text-dark">Expense Particulars</h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Expense Title *</label>
                            <input type="text" name="expense_title" class="form-control" placeholder="e.g. Office Electricity Bill, Office Rent" value="{{ old('expense_title') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Expense Category *</label>
                            <select name="category_id" id="category_select" class="form-select" required onchange="handleCategoryChange(this)">
                                <option value="">Select Category...</option>
                                @php
                                    $grouped = $categories->groupBy('group');
                                @endphp
                                @foreach($grouped as $grp => $cats)
                                    <optgroup label="{{ $grp ?? 'Other' }}">
                                        @foreach($cats as $c)
                                            <option value="{{ $c->id }}" data-group="{{ $c->group }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
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
                                    <option value="{{ $m }}" {{ (old('expense_month', request('month', date('n'))) == $m) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Expense Year *</label>
                            <input type="number" name="expense_year" class="form-control" value="{{ old('expense_year', request('year', date('Y'))) }}" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Expense Date *</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <!-- Section 2: Financials & Provider -->
                    <hr class="my-4 text-muted opacity-25">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        <h5 class="fw-bold mb-0 text-dark">Financial & Provider Details</h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Total Bill / Expense Amount (PKR) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                <input type="number" step="0.01" name="amount" id="expense_amount" class="form-control" placeholder="0.00" value="{{ old('amount') }}" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Initial Payment Amount (PKR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                <input type="number" step="0.01" name="initial_payment" class="form-control" placeholder="Leave 0 if Pending" value="{{ old('initial_payment', 0) }}">
                            </div>
                            <div class="form-text">Leave 0 if unpaid. Remaining balance will update automatically.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Provider / Vendor / Paid To</label>
                            <input type="text" name="provider_name" class="form-control" placeholder="e.g. LESCO, PTCL, Landlord, StormFiber, Shell" value="{{ old('provider_name') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Bill / Invoice Reference #</label>
                            <input type="text" name="bill_reference" class="form-control" placeholder="e.g. ELEC-1026, INV-8821" value="{{ old('bill_reference') }}">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Payment Date (if paid)</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date') }}">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small text-muted">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Online / Card" {{ old('payment_method') == 'Online / Card' ? 'selected' : '' }}>Online / Card</option>
                                <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 3: Utility Bill Specific Details (Toggleable) -->
                    <hr class="my-4 text-muted opacity-25">
                    <div class="p-3 bg-light rounded-4 mb-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_utility_bill" id="is_utility_bill" value="1" {{ old('is_utility_bill', $isUtility) ? 'checked' : '' }} onchange="toggleUtilityFields()">
                            <label class="form-check-label fw-bold text-dark" for="is_utility_bill">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> This is a Utility Bill (Power, Gas, Telecom, Internet, Water)
                            </label>
                        </div>

                        <div id="utility_fields" style="{{ old('is_utility_bill', $isUtility) ? 'display: block;' : 'display: none;' }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Utility Type</label>
                                    <select name="utility_type" id="utility_type_select" class="form-select">
                                        <option value="">Select Type...</option>
                                        <option value="Electricity" {{ old('utility_type', $utilityType) == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                        <option value="Gas" {{ old('utility_type', $utilityType) == 'Gas' ? 'selected' : '' }}>Gas</option>
                                        <option value="Telephone" {{ old('utility_type', $utilityType) == 'Telephone' ? 'selected' : '' }}>Telephone</option>
                                        <option value="Mobile" {{ old('utility_type', $utilityType) == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                        <option value="Internet" {{ old('utility_type', $utilityType) == 'Internet' ? 'selected' : '' }}>Internet</option>
                                        <option value="Water" {{ old('utility_type', $utilityType) == 'Water' ? 'selected' : '' }}>Water</option>
                                        <option value="Other" {{ old('utility_type', $utilityType) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Consumer / Account #</label>
                                    <input type="text" name="consumer_number" class="form-control" placeholder="e.g. 14 Digit Consumer ID" value="{{ old('consumer_number') }}">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Bill Issue Date</label>
                                    <input type="date" name="bill_issue_date" class="form-control" value="{{ old('bill_issue_date') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Notes and Document Attachment -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">Notes / Details</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes or details about this expense...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-muted">
                                <i class="bi bi-paperclip me-1"></i> Bill / Receipt Attachment
                            </label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB). Stored securely.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('monthly_expenses.index') }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-circle me-1"></i> Save Monthly Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleUtilityFields() {
    const chk = document.getElementById('is_utility_bill');
    const box = document.getElementById('utility_fields');
    box.style.display = chk.checked ? 'block' : 'none';
}

function handleCategoryChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const group = selectedOption.getAttribute('data-group');
    const chk = document.getElementById('is_utility_bill');
    const uSelect = document.getElementById('utility_type_select');

    if (group === 'Utilities') {
        chk.checked = true;
        toggleUtilityFields();
        const text = selectedOption.text.trim();
        if (text.includes('Electricity')) uSelect.value = 'Electricity';
        else if (text.includes('Gas')) uSelect.value = 'Gas';
        else if (text.includes('Telephone')) uSelect.value = 'Telephone';
        else if (text.includes('Mobile')) uSelect.value = 'Mobile';
        else if (text.includes('Internet')) uSelect.value = 'Internet';
        else if (text.includes('Water')) uSelect.value = 'Water';
    }
}
</script>
@endpush
@endsection
