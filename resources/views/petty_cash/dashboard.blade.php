@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i> Petty Cash Dashboard</h2>
        <div>
            <a href="{{ route('petty_cash.daily') }}" class="btn btn-primary px-4"><i class="bi bi-plus-circle me-1"></i> Add Transaction</a>
        </div>
    </div>
    
    <!-- This Month Section (More Prominent as Requested) -->
    <h5 class="fw-bold text-secondary mb-3 text-uppercase" style="letter-spacing: 0.5px;"><i class="bi bi-calendar-month me-2"></i> This Month ({{ \Carbon\Carbon::now()->format('F Y') }})</h5>
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6366f1 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Total Expenses</span>
                        <i class="bi bi-wallet2 fs-4 text-primary opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">PKR {{ number_format($monthExpenses, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Cash Expenses</span>
                        <i class="bi bi-cash fs-4 text-danger opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">PKR {{ number_format($monthCashExpenses, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Bank Expenses</span>
                        <i class="bi bi-bank fs-4 text-warning opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">PKR {{ number_format($monthBankExpenses, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #06b6d4 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.8rem;">Card / Online Expenses</span>
                        <i class="bi bi-credit-card fs-4 text-info opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">PKR {{ number_format($monthCardExpenses, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Section -->
    <h5 class="fw-bold text-secondary mb-3 text-uppercase" style="letter-spacing: 0.5px;"><i class="bi bi-clock-history me-2"></i> Today's Cash Flow</h5>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-door-open fs-1 text-secondary mb-2 d-block"></i>
                    <p class="text-muted fw-bold mb-1">Opening Cash</p>
                    <h4 class="fw-bold text-dark">PKR {{ number_format($day->opening_cash, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-arrow-down-circle fs-1 text-success mb-2 d-block"></i>
                    <p class="text-muted fw-bold mb-1">Cash Received</p>
                    <h4 class="fw-bold text-success">+ PKR {{ number_format($day->cash_received, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-arrow-up-circle fs-1 text-danger mb-2 d-block"></i>
                    <p class="text-muted fw-bold mb-1">Cash Expenses</p>
                    <h4 class="fw-bold text-danger">- PKR {{ number_format($day->cash_expenses, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                <div class="card-body text-center py-4 text-white">
                    <i class="bi bi-safe2 fs-1 mb-2 d-block text-white-50"></i>
                    <p class="fw-bold mb-1 text-white-50">Current Physical Cash</p>
                    <h3 class="fw-bold text-white mb-0">PKR {{ number_format($day->closing_cash, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection