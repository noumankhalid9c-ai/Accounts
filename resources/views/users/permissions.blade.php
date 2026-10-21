@extends('layouts.app')

@section('title', 'User Custom Permissions - ' . $user->name)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2 text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to User Profile
        </a>
        <h4 class="fw-bold mb-0 text-dark">
            <i class="bi bi-sliders text-primary me-2"></i>Custom Permissions: {{ $user->name }}
        </h4>
        <p class="text-muted small mb-0">Base Role: <strong class="text-primary">{{ $user->role_name }}</strong>. Customize specific module actions for this user.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" onclick="resetAllToInherit()">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset All to Role Default
        </button>
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold shadow-sm" onclick="document.getElementById('userPermsForm').submit()">
            <i class="bi bi-check2-circle me-1"></i> Save Custom Permissions
        </button>
    </div>
</div>

<form id="userPermsForm" method="POST" action="{{ route('users.permissions.update', $user->id) }}">
    @csrf

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge bg-light text-muted border px-2.5 py-1.5 me-2">
                    <span class="text-primary fw-bold">Inherit:</span> Default from Role
                </span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 me-2">
                    <i class="bi bi-check-circle me-1"></i> <span class="fw-bold">Grant:</span> Explicitly Allowed
                </span>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5">
                    <i class="bi bi-x-circle me-1"></i> <span class="fw-bold">Deny:</span> Explicitly Forbidden
                </span>
            </div>
            <div class="text-muted small">
                Hover over options for details.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th class="text-start ps-4" style="min-width: 200px;">Module</th>
                        @foreach($actions as $actKey => $actLabel)
                            <th style="min-width: 110px;">{{ $actLabel }}</th>
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
                                <div class="btn-group btn-group-xs" role="group">
                                    <button type="button" class="btn btn-outline-secondary py-0 px-1" style="font-size: 0.68rem;" onclick="setRowState('{{ $modKey }}', 'inherit')" title="Set {{ $modLabel }} to Inherit">
                                        Inherit
                                    </button>
                                    <button type="button" class="btn btn-outline-success py-0 px-1" style="font-size: 0.68rem;" onclick="setRowState('{{ $modKey }}', 'grant')" title="Grant all {{ $modLabel }}">
                                        Grant
                                    </button>
                                    <button type="button" class="btn btn-outline-danger py-0 px-1" style="font-size: 0.68rem;" onclick="setRowState('{{ $modKey }}', 'deny')" title="Deny all {{ $modLabel }}">
                                        Deny
                                    </button>
                                </div>
                            </div>
                        </td>
                        @foreach($actions as $actKey => $actLabel)
                            @php
                                $slug = "{$modKey}.{$actKey}";
                                $perm = $allPermissions->get($slug);
                                $roleHas = in_array($slug, $rolePermissionSlugs);
                                $override = $userOverrides->get($slug);
                                $currentState = 'inherit';
                                if ($override) {
                                    $currentState = $override->pivot->is_granted ? 'grant' : 'deny';
                                }
                            @endphp
                            <td>
                                @if($perm)
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Inherit -->
                                        <input type="radio" class="btn-check perm-radio-inherit row-{{ $modKey }}-inherit" name="permissions[{{ $perm->id }}]" id="p_{{ $perm->id }}_inherit" value="inherit" {{ $currentState === 'inherit' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary py-0.5 px-1.5" for="p_{{ $perm->id }}_inherit" title="Role Default ({{ $roleHas ? 'Allowed' : 'Not Allowed' }})">
                                            @if($roleHas)
                                                <i class="bi bi-dash text-success" style="font-size: 0.75rem;"></i>
                                            @else
                                                <i class="bi bi-dash text-muted" style="font-size: 0.75rem;"></i>
                                            @endif
                                        </label>

                                        <!-- Grant -->
                                        <input type="radio" class="btn-check row-{{ $modKey }}-grant" name="permissions[{{ $perm->id }}]" id="p_{{ $perm->id }}_grant" value="grant" {{ $currentState === 'grant' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success py-0.5 px-1.5" for="p_{{ $perm->id }}_grant" title="Explicitly Grant">
                                            <i class="bi bi-check"></i>
                                        </label>

                                        <!-- Deny -->
                                        <input type="radio" class="btn-check row-{{ $modKey }}-deny" name="permissions[{{ $perm->id }}]" id="p_{{ $perm->id }}_deny" value="deny" {{ $currentState === 'deny' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger py-0.5 px-1.5" for="p_{{ $perm->id }}_deny" title="Explicitly Deny">
                                            <i class="bi bi-x"></i>
                                        </label>
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

        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-end gap-2">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Save Custom Permissions
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
function resetAllToInherit() {
    const inherits = document.querySelectorAll('.perm-radio-inherit');
    inherits.forEach(r => r.checked = true);
}

function setRowState(modKey, state) {
    const radios = document.querySelectorAll('.row-' + modKey + '-' + state);
    radios.forEach(r => r.checked = true);
}
</script>
@endpush
@endsection
