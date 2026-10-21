@extends('layouts.app')

@section('title', 'User Profile - ' . $user->name)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2 text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to User List
        </a>
        <h4 class="fw-bold mb-0 text-dark">
            <i class="bi bi-person-circle text-primary me-2"></i>{{ $user->name }}
            <span class="badge bg-light text-muted border font-monospace fs-6 ms-2">{{ $user->user_code ?? 'USR-' . $user->id }}</span>
        </h4>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('users.permissions', $user->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-semibold">
            <i class="bi bi-shield-lock me-1"></i> Manage Permissions
        </a>
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-pencil-square me-1"></i> Edit Profile
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: User Summary Card -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4 bg-white">
            <div class="position-relative d-inline-block mx-auto mb-3">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle border shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
                <span class="position-absolute bottom-0 end-0 badge p-2 rounded-circle border border-white {{ $user->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                    <span class="visually-hidden">status</span>
                </span>
            </div>

            <h5 class="fw-bold text-dark mb-0">{{ $user->name }}</h5>
            <div class="text-secondary small mb-2"><code>{{ '@' . ($user->username ?? 'user') }}</code></div>

            <div class="mb-3">
                @if($user->role_id === 1)
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold">
                        <i class="bi bi-shield-shaded me-1"></i> {{ $user->role_name }}
                    </span>
                @else
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold">
                        <i class="bi bi-shield-check me-1"></i> {{ $user->role_name }}
                    </span>
                @endif
            </div>

            <div class="d-flex justify-content-center gap-2 mb-4">
                @if($user->status === 'Active')
                    <span class="badge badge-soft-success px-3 py-1 rounded-pill">Active Account</span>
                @elseif($user->status === 'Suspended')
                    <span class="badge badge-soft-warning px-3 py-1 rounded-pill">Suspended</span>
                @elseif($user->status === 'Locked')
                    <span class="badge badge-soft-danger px-3 py-1 rounded-pill">Temporarily Locked</span>
                @else
                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill">Inactive</span>
                @endif

                @if($user->must_change_password)
                    <span class="badge bg-warning bg-opacity-25 text-dark border px-2.5 py-1 rounded-pill" title="Must change password on next login">
                        <i class="bi bi-exclamation-circle me-1"></i> Temp Pass
                    </span>
                @endif
            </div>

            <div class="border-top pt-3 text-start small">
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">User Code:</span>
                    <span class="fw-semibold text-dark font-monospace">{{ $user->user_code ?? 'USR-' . $user->id }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Email:</span>
                    <span class="fw-semibold text-dark">{{ $user->email }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Mobile:</span>
                    <span class="fw-semibold text-dark">{{ $user->mobile ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Department:</span>
                    <span class="fw-semibold text-dark">{{ $user->department ?? 'General' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Designation:</span>
                    <span class="fw-semibold text-dark">{{ $user->designation ?? 'Staff' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Joined Date:</span>
                    <span class="fw-semibold text-dark">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Last Login:</span>
                    <span class="fw-semibold text-dark">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Last IP:</span>
                    <span class="fw-semibold text-dark font-monospace">{{ $user->last_login_ip ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5">
                    <span class="text-muted">Failed Logins:</span>
                    <span class="fw-semibold {{ $user->failed_login_attempts > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $user->failed_login_attempts }} attempt(s)
                    </span>
                </div>
            </div>

            <!-- Quick Account Actions -->
            <div class="border-top pt-3 mt-3 d-grid gap-2">
                <form method="POST" action="{{ route('users.revoke_sessions', $user->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Revoke Active Sessions
                    </button>
                </form>

                <form method="POST" action="{{ route('users.reset_password', $user->id) }}">
                    @csrf
                    <input type="hidden" name="reset_type" value="generate_temp">
                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill w-100 fw-semibold" onclick="return confirm('Generate a new temporary password for {{ $user->name }}?');">
                        <i class="bi bi-key me-1"></i> Generate Temp Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Tabs (Effective Permissions, Recent Activity, Login History) -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 p-3 p-md-4 pb-0">
                <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold small text-uppercase" id="perms-tab" data-bs-toggle="tab" data-bs-target="#permsTabPane" type="button" role="tab" aria-selected="true">
                            <i class="bi bi-shield-check me-1"></i> Effective Permissions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small text-uppercase" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activityTabPane" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-journal-text me-1"></i> Recent Activity ({{ $user->activities->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small text-uppercase" id="logins-tab" data-bs-toggle="tab" data-bs-target="#loginsTabPane" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-clock-history me-1"></i> Login History ({{ $user->loginHistories->count() }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="userTabsContent">
                    <!-- Effective Permissions Tab -->
                    <div class="tab-pane fade show active" id="permsTabPane" role="tabpanel" aria-labelledby="perms-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">
                                Showing authorized operations for role <strong>{{ $user->role_name }}</strong>.
                                Direct user overrides are highlighted.
                            </span>
                            <a href="{{ route('users.permissions', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-sliders me-1"></i> Edit Overrides
                            </a>
                        </div>

                        @if($user->isSuperAdmin())
                            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 py-2 small mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <strong>Super Admin Account:</strong> This user has unrestricted, full access to all modules and system actions.
                            </div>
                        @endif

                        <div class="row g-2">
                            @php
                                $grouped = $allPermissions->groupBy('module');
                            @endphp
                            @foreach($grouped as $mod => $perms)
                                <div class="col-12 col-md-6">
                                    <div class="p-3 bg-light rounded-3 border h-100">
                                        <div class="fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px;">
                                            <i class="bi bi-folder2-open text-primary me-1"></i> {{ ucwords(str_replace('_', ' ', $mod)) }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($perms as $p)
                                                @php $has = $user->canDo($p->slug); @endphp
                                                @if($has)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded" style="font-size: 0.75rem;">
                                                        <i class="bi bi-check me-0.5"></i>{{ ucfirst($p->action) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border px-2 py-1 rounded opacity-50" style="font-size: 0.75rem;">
                                                        {{ ucfirst($p->action) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recent Activity Tab -->
                    <div class="tab-pane fade" id="activityTabPane" role="tabpanel" aria-labelledby="activity-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Action</th>
                                        <th>Module</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->activities as $act)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $act->action }}</span>
                                        </td>
                                        <td>{{ ucfirst($act->module ?? 'system') }}</td>
                                        <td class="text-secondary">{{ $act->description ?? '—' }}</td>
                                        <td><code>{{ $act->ip_address ?? '—' }}</code></td>
                                        <td class="text-muted">{{ $act->created_at ? $act->created_at->format('d M Y, H:i:s') : '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No activity records recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Login History Tab -->
                    <div class="tab-pane fade" id="loginsTabPane" role="tabpanel" aria-labelledby="logins-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>IP Address</th>
                                        <th>Browser / Device</th>
                                        <th>Failure Reason</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->loginHistories as $lh)
                                    <tr>
                                        <td>
                                            @if($lh->status === 'Success')
                                                <span class="badge bg-success">Success</span>
                                            @elseif($lh->status === 'Locked')
                                                <span class="badge bg-danger">Locked</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Failed</span>
                                            @endif
                                        </td>
                                        <td><code>{{ $lh->ip_address }}</code></td>
                                        <td class="text-muted small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $lh->user_agent }}
                                        </td>
                                        <td class="text-danger small">{{ $lh->failure_reason ?? '—' }}</td>
                                        <td class="text-muted">{{ $lh->created_at ? $lh->created_at->format('d M Y, H:i') : '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No sign-in records found.</td>
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
</div>
@endsection
