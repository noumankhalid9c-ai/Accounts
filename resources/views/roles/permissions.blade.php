@extends('layouts.app')

@section('title', 'Permissions Matrix - ' . $role->name)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2 text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Roles
        </a>
        <h4 class="fw-bold mb-0 text-dark">
            <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Permissions Matrix: {{ $role->name }}
        </h4>
        <p class="text-muted small mb-0">Toggle authorized actions across modules. Changes are enforced on backend requests.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="toggleAllMatrix(true)">
            <i class="bi bi-check-all me-1"></i> Select All
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="toggleAllMatrix(false)">
            <i class="bi bi-dash me-1"></i> Deselect All
        </button>
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold shadow-sm" onclick="document.getElementById('rolePermsForm').submit()">
            <i class="bi bi-save2 me-1"></i> Save Matrix
        </button>
    </div>
</div>

@if($role->id === 1)
    <div class="alert alert-info border-info border-2 rounded-4 p-3 mb-4 shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-fill-check fs-4 text-primary"></i>
            <div>
                <strong>Super Administrator Role:</strong> Has comprehensive, permanent authorization to execute all current and future actions throughout the entire suite.
            </div>
        </div>
    </div>
@endif

<form id="rolePermsForm" method="POST" action="{{ route('roles.permissions.update', $role->id) }}">
    @csrf

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">
                    <span id="selectedCountDisplay">{{ count($activePermissionIds) }}</span> of {{ $allPermissions->count() }} Actions Permitted
                </span>
            </div>
            <div class="text-muted small">
                Click column headers or row badges to toggle entire sections.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th class="text-start ps-4" style="min-width: 210px;">Module / Domain</th>
                        @foreach($actions as $actKey => $actLabel)
                            <th style="min-width: 105px;">
                                <button type="button" class="btn btn-link p-0 text-decoration-none fw-bold text-dark small" onclick="toggleColumn('{{ $actKey }}')">
                                    {{ $actLabel }}
                                    <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.7rem;"></i>
                                </button>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $modKey => $modLabel)
                    <tr>
                        <td class="text-start ps-4 fw-semibold text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-folder2 text-primary me-1.5"></i> {{ $modLabel }}
                                </span>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-1.5 rounded-pill text-muted" style="font-size: 0.7rem;" onclick="toggleRow('{{ $modKey }}')">
                                    All
                                </button>
                            </div>
                        </td>
                        @foreach($actions as $actKey => $actLabel)
                            @php
                                $slug = "{$modKey}.{$actKey}";
                                $perm = $allPermissions->get($slug);
                                $isActive = $perm && (in_array($perm->id, $activePermissionIds) || $role->id === 1);
                            @endphp
                            <td>
                                @if($perm)
                                    <div class="form-check d-flex justify-content-center m-0">
                                        <input class="form-check-input perm-check mod-{{ $modKey }} act-{{ $actKey }}" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="chk_{{ $perm->id }}" {{ $isActive ? 'checked' : '' }} {{ $role->id === 1 ? 'disabled checked' : '' }} onchange="updateSelectedCount()">
                                    </div>
                                @else
                                    <span class="text-muted opacity-25">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <span class="text-muted small">Permissions are cached and applied instantaneously on route navigation.</span>
            <div class="d-flex gap-2">
                <a href="{{ route('roles.index') }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                    <i class="bi bi-save2 me-1"></i> Save Permissions Matrix
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function toggleAllMatrix(state) {
    const checks = document.querySelectorAll('.perm-check:not(:disabled)');
    checks.forEach(c => c.checked = state);
    updateSelectedCount();
}

function toggleRow(modKey) {
    const checks = document.querySelectorAll('.mod-' + modKey + ':not(:disabled)');
    if (checks.length === 0) return;
    const allChecked = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !allChecked);
    updateSelectedCount();
}

function toggleColumn(actKey) {
    const checks = document.querySelectorAll('.act-' + actKey + ':not(:disabled)');
    if (checks.length === 0) return;
    const allChecked = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !allChecked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.perm-check:checked').length;
    document.getElementById('selectedCountDisplay').innerText = count;
}
</script>
@endpush
@endsection
