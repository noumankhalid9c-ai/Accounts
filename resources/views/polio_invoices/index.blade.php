@extends('layouts.app')

@section('title', 'Polio Certificate Invoices')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-gray-800">
            <i class="bi bi-shield-check text-primary me-2"></i>Polio Certificate Invoices
        </h1>
        <p class="text-muted mb-0">Manage polio vaccination certificate service billing and payments.</p>
    </div>
    <div>
        <a href="{{ route('polio-invoices.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-circle me-1.5"></i> + Create Polio Invoice
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Invoices</span>
                    <h3 class="fw-bold mb-0 mt-1 text-dark">{{ number_format($totalInvoicesCount) }}</h3>
                </div>
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Billed</span>
                    <h3 class="fw-bold mb-0 mt-1 text-primary">PKR {{ number_format($totalAmountSum, 2) }}</h3>
                </div>
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Paid</span>
                    <h3 class="fw-bold mb-0 mt-1 text-success">PKR {{ number_format($totalPaidSum, 2) }}</h3>
                </div>
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Balance Due</span>
                    <h3 class="fw-bold mb-0 mt-1 text-danger">PKR {{ number_format($totalBalanceSum, 2) }}</h3>
                </div>
                <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('polio-invoices.index') }}" class="row g-3 align-items-end">
            <!-- Search Keyword -->
            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-muted">Search Invoice / Client / Phone / Passport</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="e.g. POLIO-INV-00001, Ahmed, 0300..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Payment Status -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">Payment Status</label>
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Partially Paid" {{ request('status') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <!-- Date From -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">From Date</label>
                <input type="date" name="from_date" class="form-control bg-light" value="{{ request('from_date') }}">
            </div>

            <!-- Date To -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted">To Date</label>
                <input type="date" name="to_date" class="form-control bg-light" value="{{ request('to_date') }}">
            </div>

            <!-- Buttons -->
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('polio-invoices.index') }}" class="btn btn-light border rounded-pill px-3" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                        <th class="ps-4">Invoice No.</th>
                        <th>Date</th>
                        <th>Client Name</th>
                        <th>Phone</th>
                        <th class="text-center">Persons</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <a href="{{ route('polio-invoices.show', $inv->id) }}" class="text-decoration-none text-primary">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td>{{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : '—' }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $inv->client_name }}</div>
                            @if($inv->passport_number)
                                <div class="text-muted small"><i class="bi bi-card-text me-1"></i>{{ $inv->passport_number }}</div>
                            @endif
                        </td>
                        <td>{{ $inv->phone }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                {{ $inv->persons }} {{ Str::plural('Pax', $inv->persons) }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            PKR {{ number_format($inv->total_amount, 2) }}
                        </td>
                        <td class="text-end fw-semibold text-success">
                            PKR {{ number_format($inv->amount_paid, 2) }}
                        </td>
                        <td class="text-end fw-semibold {{ $inv->balance_due > 0 ? 'text-danger' : 'text-muted' }}">
                            PKR {{ number_format($inv->balance_due, 2) }}
                        </td>
                        <td class="text-center">
                            @if($inv->payment_status === 'Paid')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 fw-semibold">Paid</span>
                            @elseif($inv->payment_status === 'Partially Paid')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1.5 fw-semibold">Partially Paid</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1.5 fw-semibold">Pending</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <!-- View -->
                                <a href="{{ route('polio-invoices.show', $inv->id) }}" class="btn btn-sm btn-light border text-primary" title="View Invoice">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <!-- Edit -->
                                <a href="{{ route('polio-invoices.edit', $inv->id) }}" class="btn btn-sm btn-light border text-secondary" title="Edit Invoice">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <!-- Print -->
                                <a href="{{ route('polio-invoices.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-light border text-dark" title="Print Invoice">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <!-- PDF -->
                                <a href="{{ route('polio-invoices.pdf', $inv->id) }}" class="btn btn-sm btn-light border text-danger" title="Download PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <!-- Delete (Admin Only) -->
                                @if(auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->canDo('polio_invoices.delete')))
                                <form action="{{ route('polio-invoices.destroy', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete invoice {{ $inv->invoice_number }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete Invoice">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No polio certificate invoices found.</p>
                            <a href="{{ route('polio-invoices.create') }}" class="btn btn-sm btn-primary rounded-pill mt-2">
                                <i class="bi bi-plus-circle me-1"></i> Create First Polio Invoice
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
