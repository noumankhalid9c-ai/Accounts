@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Agent Ledger & Statement</h1>
            <p class="text-muted mb-0">{{ $agent->company_name ?? $agent->name }} | {{ $agent->contact_person }} | {{ $agent->phone }}</p>
        </div>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#payCommissionModal">
                <i class="bi bi-cash-stack"></i> Pay Commission
            </button>
            <a href="{{ route('b2b-agents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-white-50 text-uppercase mb-1">Total Payment Received</h6>
                    <h3 class="mb-0 fw-bold">PKR {{ number_format($totalReceived, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-white-50 text-uppercase mb-1">Total Commission Due</h6>
                    <h3 class="mb-0 fw-bold">PKR {{ number_format($totalCommission, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-white-50 text-uppercase mb-1">Commission Paid</h6>
                    <h3 class="mb-0 fw-bold">PKR {{ number_format($commissionPaid, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 {{ $commissionPending > 0 ? 'bg-danger text-white' : 'bg-light text-dark' }}">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1 {{ $commissionPending > 0 ? 'text-white-50' : 'text-muted' }}">Commission Pending</h6>
                    <h3 class="mb-0 fw-bold">PKR {{ number_format($commissionPending, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <!-- Agent Transactions / Bookings -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-database"></i> Agent Transactions & Bookings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Service</th>
                                    <th>Client / Ref</th>
                                    <th>Total Payment</th>
                                    <th>Commission Type</th>
                                    <th>Comm. Amount</th>
                                    <th>Company Share</th>
                                    <th>B2B Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agent->bookings as $booking)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                    <td>{{ $booking->service_taken }}</td>
                                    <td>
                                        <strong>{{ $booking->client->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $booking->package_details }}</small>
                                    </td>
                                    <td>PKR {{ number_format($booking->total_amount, 2) }}</td>
                                    <td>
                                        @if($booking->commission_type == 'Percentage')
                                            {{ $booking->commission_percentage }}%
                                        @else
                                            Fixed
                                        @endif
                                    </td>
                                    <td class="text-primary fw-bold">PKR {{ number_format($booking->b2b_commission, 2) }}</td>
                                    <td class="text-success fw-bold">PKR {{ number_format($booking->company_share, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $booking->b2b_commission_status == 'Paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $booking->b2b_commission_status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No transactions found for this agent.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Payment History -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-success"><i class="bi bi-cash-coin"></i> Commission Payment History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Slip No</th>
                                    <th>Amount Paid</th>
                                    <th>Method</th>
                                    <th>Transaction Ref</th>
                                    <th>Notes</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agent->commissionPayments as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $payment->payment_slip_number }}</span></td>
                                    <td class="text-success fw-bold">PKR {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>{{ $payment->transaction_reference ?? '-' }}</td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('commission-payments.slip', $payment->id) }}" class="btn btn-sm btn-light border text-primary" target="_blank">
                                            <i class="bi bi-receipt"></i> Slip
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No commission payments made yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pay Commission Modal -->
<div class="modal fade" id="payCommissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('commission-payments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="b2b_agent_id" value="{{ $agent->id }}">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title"><i class="bi bi-cash-stack"></i> Pay Agent Commission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger bg-danger bg-opacity-10 text-danger border-0">
                        <strong>Pending Commission:</strong> PKR {{ number_format($commissionPending, 2) }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" max="{{ $commissionPending > 0 ? $commissionPending : '' }}" required placeholder="E.g. 5000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Online Transfer">Online Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Reference (Optional)</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="Cheque no or transaction ID">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Payment remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Record Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
