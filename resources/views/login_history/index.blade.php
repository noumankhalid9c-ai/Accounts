@extends('layouts.app')

@section('title', 'Login History & Security Audit')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-clock-history text-primary me-2"></i>Login History & Authentication Log
        </h4>
        <p class="text-muted small mb-0">Monitor authentication traffic, successful logins, failed attempts, and security lockout events.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm">
            <i class="bi bi-journal-text me-1"></i> Activity Logs
        </a>
        <a href="{{ route('security-settings.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm">
            <i class="bi bi-shield-check me-1"></i> Security Settings
        </a>
    </div>
</div>

<!-- Metrics Overview -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Total Authentication Events</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $metrics['total'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="bi bi-fingerprint fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-success small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Successful Logins</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">{{ $metrics['success'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-warning small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Failed Attempts</span>
                    <h3 class="fw-bold text-warning mt-1 mb-0">{{ $metrics['failed'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-danger small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Lockout Events</span>
                    <h3 class="fw-bold text-danger mt-1 mb-0">{{ $metrics['locked'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="bi bi-lock-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('login-history.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search identifier, IP address, failure reason..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="Success" {{ request('status') === 'Success' ? 'selected' : '' }}>Success</option>
                    <option value="Failed" {{ request('status') === 'Failed' ? 'selected' : '' }}>Failed</option>
                    <option value="Locked" {{ request('status') === 'Locked' ? 'selected' : '' }}>Locked</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" title="From Date" value="{{ request('from_date') }}">
            </div>
            <div class="col-6 col-md-1">
                <input type="date" name="to_date" class="form-control form-control-sm" title="To Date" value="{{ request('to_date') }}">
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 fw-semibold">
                    <i class="bi bi-filter"></i>
                </button>
                @if(request()->anyFilled(['search', 'status', 'user_id', 'from_date', 'to_date']))
                    <a href="{{ route('login-history.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Clear">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">
            Showing {{ $histories->total() }} Logins
        </span>
        <span class="text-muted small">IPs & User Agents logged for audit integrity.</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Timestamp</th>
                    <th>User / Account</th>
                    <th>Identifier Used</th>
                    <th>Status</th>
                    <th>Failure Reason</th>
                    <th>IP Address</th>
                    <th class="pe-4">Client User-Agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $h)
                <tr>
                    <td class="ps-4 text-muted small" style="white-space: nowrap;">
                        <i class="bi bi-clock me-1 text-muted"></i>
                        {{ $h->created_at ? $h->created_at->format('d M Y, H:i:s') : '—' }}
                    </td>
                    <td>
                        @if($h->user)
                            <a href="{{ route('users.show', $h->user->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $h->user->name }}
                            </a>
                        @else
                            <span class="text-muted fst-italic">Unregistered</span>
                        @endif
                    </td>
                    <td>
                        <code class="text-muted">{{ $h->identifier }}</code>
                    </td>
                    <td>
                        @if($h->status === 'Success')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Success
                            </span>
                        @elseif($h->status === 'Locked')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">
                                <i class="bi bi-lock-fill me-1"></i> Locked
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                <i class="bi bi-exclamation-triangle me-1"></i> Failed
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($h->failure_reason)
                            <span class="text-danger small">{{ $h->failure_reason }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <code class="text-primary">{{ $h->ip_address }}</code>
                    </td>
                    <td class="pe-4 text-muted small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $h->user_agent }}">
                        {{ $h->user_agent }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                        No login records found matching criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($histories->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ $histories->firstItem() }} to {{ $histories->lastItem() }} of {{ $histories->total() }} records</span>
            <div>{{ $histories->links() }}</div>
        </div>
    @endif
</div>
@endsection
