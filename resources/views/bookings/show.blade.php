@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="page-header-title fs-3 fw-bold mb-1">Booking #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <p class="page-header-subtitle text-muted mb-0">Client: <a href="{{ route('clients.show', $booking->client_id) }}">{{ $booking->client->name ?? 'N/A' }}</a></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('bookings.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Back to Bookings</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Left: Booking Info -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-bold text-dark"><i class="bi bi-info-circle text-primary me-2"></i>Booking Information</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase">Service Type</label>
                        <div class="fw-bold">{{ $booking->service_type }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase">Booking Date</label>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase">Travel Date</label>
                        <div class="fw-bold">{{ $booking->travel_date ? \Carbon\Carbon::parse($booking->travel_date)->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase">Package Details</label>
                        <div class="fw-bold">{{ $booking->package_details ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase">Payment Status</label>
                        <div>
                            @php
                                $badge = match($booking->payment_status) {
                                    'Paid' => 'success',
                                    'Partially Paid' => 'warning',
                                    'Pending' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }} rounded-pill">{{ $booking->payment_status }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold text-uppercase">Assigned Executive</label>
                        <div class="fw-bold">{{ $booking->salesExecutive->name ?? 'None' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- B2B Agent Info -->
        @if($booking->b2b_agent_id)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-bold text-dark"><i class="bi bi-diagram-3 text-info me-2"></i>B2B Agent Details</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <label class="text-muted small fw-semibold text-uppercase">Agent Name</label>
                        <div class="fw-bold">{{ $booking->agent->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small fw-semibold text-uppercase">B2B Commission</label>
                        <div class="fw-bold text-dark">Rs {{ number_format($booking->b2b_commission, 2) }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small fw-semibold text-uppercase">Company Share</label>
                        <div class="fw-bold text-success">Rs {{ number_format($booking->company_share, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right: CLIENT PAYMENT & INVOICE -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm mb-4 border-top border-primary border-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>CLIENT PAYMENT & INVOICE</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold">Total Amount</span>
                    <span class="fs-5 fw-bold text-dark">Rs {{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold">Advance / Received</span>
                    <span class="fs-5 fw-bold text-success">Rs {{ number_format($totalReceived, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted fw-semibold">Remaining / Pending</span>
                    <span class="fs-4 fw-bold text-danger">Rs {{ number_format($remaining, 2) }}</span>
                </div>
                
                <div class="d-grid gap-2">
                    <!-- Trigger Modal to Add Payment -->
                    @if($remaining > 0)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="bi bi-cash me-2"></i> Add Payment (Auto Calculate)
                        </button>
                    @else
                        <button type="button" class="btn btn-success" disabled>
                            <i class="bi bi-check-circle me-2"></i> Fully Paid
                        </button>
                    @endif
                    
                    <a href="{{ route('bookings.invoice', $booking->id) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Generate Invoice
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Payment History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <span class="fw-bold text-dark"><i class="bi bi-clock-history text-secondary me-2"></i>Payment History</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase" style="font-size: 0.75rem;">
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booking->payments as $payment)
                        <tr>
                            <td class="small">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td class="fw-bold text-success">Rs {{ number_format($payment->amount, 2) }}</td>
                            <td class="small">{{ $payment->payment_method }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted small py-3">No payments recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('bookings.add_payment', $booking->id) }}" method="POST">
          @csrf
          <div class="modal-header bg-light">
            <h5 class="modal-title fs-5" id="addPaymentModalLabel">Add Client Payment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="alert alert-info py-2 small mb-3">
                  <i class="bi bi-info-circle me-1"></i> Remaining Balance: <strong>Rs {{ number_format($remaining, 2) }}</strong>
              </div>
              <div class="mb-3">
                  <label class="form-label">Payment Amount</label>
                  <div class="input-group">
                      <span class="input-group-text">Rs</span>
                      <input type="number" step="0.01" name="amount" class="form-control" value="{{ $remaining }}" max="{{ $remaining }}" required>
                  </div>
              </div>
              <div class="mb-3">
                  <label class="form-label">Payment Date</label>
                  <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Payment Method</label>
                  <select name="payment_method" class="form-select">
                      <option value="Cash">Cash</option>
                      <option value="Bank Transfer">Bank Transfer</option>
                      <option value="Cheque">Cheque</option>
                      <option value="Online">Online</option>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label">Notes (Optional)</label>
                  <textarea name="notes" class="form-control" rows="2"></textarea>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save Payment</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
