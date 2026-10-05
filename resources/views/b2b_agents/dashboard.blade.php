@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">B2B Agent Management Dashboard</h1>
            <p class="text-muted mb-0">Overview of agents, commissions, and transactions.</p>
        </div>
        <div>
            <a href="{{ route('b2b-agents.index') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-people"></i> Manage Agents
            </a>
            <a href="{{ route('b2b-agents.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add New Agent
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Agents -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 border-start border-4 border-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Active Agents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAgents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-briefcase fa-2x text-gray-300 fs-2 text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Received -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 border-start border-4 border-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Payment Received</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($totalReceived, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fs-2 text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Commission -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 border-start border-4 border-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Agent Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($totalCommission, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-percent fs-2 text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Amount -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 border-start border-4 border-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Company Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($companyAmount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-building fs-2 text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Commission Paid -->
        <div class="col-xl-6 col-md-6">
            <div class="card border-0 shadow-sm h-100 py-3 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Commission Paid</h5>
                    <h3 class="text-success fw-bold">PKR {{ number_format($commissionPaid, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Commission Pending -->
        <div class="col-xl-6 col-md-6">
            <div class="card border-0 shadow-sm h-100 py-3 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Commission Pending</h5>
                    <h3 class="text-danger fw-bold">PKR {{ number_format($commissionPending, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info shadow-sm border-0 border-start border-4 border-info">
        <i class="bi bi-info-circle me-2"></i> <strong>Note:</strong> Module construction is currently ongoing. To manage agents, add transactions, and generate invoices, please click "Manage Agents" above to access the Agent lists.
    </div>

</div>
@endsection
