@extends('layouts.app')

@section('title', 'User Activity Logs')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-journal-text text-primary me-2"></i>User Activity Logs
        </h4>
        <p class="text-muted small mb-0">Immutable system audit trail tracking administrative operations, security events, and record modifications.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('login-history.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm">
            <i class="bi bi-clock-history me-1"></i> Login History
        </a>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search description, action, IP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->user_code ?? 'USR-' . $u->id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="module" class="form-select form-select-sm">
                    <option value="">All Modules</option>
                    @foreach($modules as $m)
                        <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" title="From Date" value="{{ request('from_date') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" title="To Date" value="{{ request('to_date') }}">
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 fw-semibold">
                    <i class="bi bi-filter"></i>
                </button>
                @if(request()->anyFilled(['search', 'user_id', 'module', 'action', 'from_date', 'to_date']))
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Clear">
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
            Total {{ $logs->total() }} Log Entries
        </span>
        <span class="text-muted small">Logs are tamper-proof and retained permanently.</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Timestamp</th>
                    <th>User</th>
                    <th>Module</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th class="text-end pe-4">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="ps-4 text-muted small" style="white-space: nowrap;">
                        <i class="bi bi-clock me-1 text-muted"></i>
                        {{ $log->created_at ? $log->created_at->format('d M Y, H:i:s') : '—' }}
                    </td>
                    <td>
                        @if($log->user)
                            <a href="{{ route('users.show', $log->user->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $log->user->name }}
                            </a>
                        @else
                            <span class="text-muted fst-italic">System / Guest</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ ucfirst($log->module ?? 'system') }}</span>
                    </td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="text-dark">
                        {{ $log->description ?? ($log->action . ' on ' . ($log->table_name ?? 'record') . ' #' . $log->record_id) }}
                    </td>
                    <td>
                        <code class="text-muted">{{ $log->ip_address ?? '—' }}</code>
                    </td>
                    <td class="text-end pe-4">
                        @if($log->old_value || $log->new_value)
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1" onclick="viewLogDiff('{{ $log->id }}', '{{ addslashes($log->action) }}', {{ json_encode($log->old_value) }}, {{ json_encode($log->new_value) }})">
                                <i class="bi bi-file-diff text-primary me-1"></i> Diff
                            </button>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-journal fs-1 d-block mb-2 text-secondary"></i>
                        No audit log entries found matching criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} records</span>
            <div>{{ $logs->links() }}</div>
        </div>
    @endif
</div>

<!-- Diff Modal -->
<div class="modal fade" id="logDiffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-diff-fill text-primary me-2"></i>Change Inspector: <span id="diffModalAction"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-danger mb-2 small text-uppercase">Previous Values (Before)</h6>
                            <pre id="diffOldValue" class="bg-white p-3 rounded border text-muted small mb-0 font-monospace overflow-auto" style="max-height: 280px;"></pre>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-bold text-success mb-2 small text-uppercase">New Values (After)</h6>
                            <pre id="diffNewValue" class="bg-white p-3 rounded border text-dark small mb-0 font-monospace overflow-auto" style="max-height: 280px;"></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewLogDiff(id, action, oldVal, newVal) {
    document.getElementById('diffModalAction').innerText = action;
    try {
        const parsedOld = typeof oldVal === 'string' ? JSON.parse(oldVal) : oldVal;
        document.getElementById('diffOldValue').innerText = parsedOld ? JSON.stringify(parsedOld, null, 2) : '(none)';
    } catch(e) {
        document.getElementById('diffOldValue').innerText = oldVal || '(none)';
    }

    try {
        const parsedNew = typeof newVal === 'string' ? JSON.parse(newVal) : newVal;
        document.getElementById('diffNewValue').innerText = parsedNew ? JSON.stringify(parsedNew, null, 2) : '(none)';
    } catch(e) {
        document.getElementById('diffNewValue').innerText = newVal || '(none)';
    }

    new bootstrap.Modal(document.getElementById('logDiffModal')).show();
}
</script>
@endpush
@endsection
