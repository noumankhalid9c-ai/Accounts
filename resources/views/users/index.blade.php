@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-people-fill text-primary me-2"></i>User Management
        </h4>
        <p class="text-muted small mb-0">Manage system user accounts, roles, access permissions, and authentication security.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm fw-semibold shadow-sm">
            <i class="bi bi-shield-lock me-1"></i> Roles & Permissions
        </a>
        <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill px-3 py-2 btn-sm fw-semibold shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Add New User
        </a>
    </div>
</div>

<!-- Generated Temporary Password Alert Banner (Shown only once right after admin generates) -->
@if (session('temp_password_generated'))
    @php $gen = session('temp_password_generated'); @endphp
    <div class="alert alert-warning border-warning border-2 p-3 rounded-4 mb-4 shadow-sm">
        <div class="d-flex align-items-start gap-3">
            <div class="bg-warning text-dark p-2 rounded-circle fs-4 lh-1">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="fw-bold text-dark mb-1">Temporary Password Generated!</h5>
                <p class="small text-muted mb-2">
                    A temporary password was generated for <strong>{{ $gen['user'] }}</strong> ({{ $gen['email'] }}).
                    The user will be required to change this password immediately upon their first sign-in.
                </p>
                <div class="d-inline-flex align-items-center gap-2 bg-white px-3 py-2 rounded-3 border">
                    <span class="text-muted small">Generated Password:</span>
                    <code class="fs-5 fw-bold text-primary" id="tempPassCode">{{ $gen['password'] }}</code>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="navigator.clipboard.writeText('{{ $gen['password'] }}'); this.innerText='Copied!';">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Overview Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Total Accounts</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $metrics['total'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-success small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Active Users</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">{{ $metrics['active'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-person-check-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Inactive Users</span>
                    <h3 class="fw-bold text-secondary mt-1 mb-0">{{ $metrics['inactive'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
                    <i class="bi bi-person-dash-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-danger small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Suspended / Locked</span>
                    <h3 class="fw-bold text-danger mt-1 mb-0">{{ $metrics['suspended'] }}</h3>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="bi bi-person-x-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search Toolbar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, username, code..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="role_id" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="Locked" {{ request('status') === 'Locked' ? 'selected' : '' }}>Locked</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="department" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill flex-grow-1 fw-semibold">
                    <i class="bi bi-filter"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'role_id', 'status', 'department', 'from_date', 'to_date']))
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Reset Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions Form & Users Table -->
<form id="bulkForm" method="POST" action="{{ route('users.bulk_action') }}">
    @csrf
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-2 py-1.5 fw-semibold">
                    Total: {{ $users->total() }} Users
                </span>
                <div class="input-group input-group-sm" style="max-width: 250px;">
                    <select name="bulk_action" class="form-select form-select-sm" id="bulkActionSelect">
                        <option value="">Bulk Actions...</option>
                        <option value="activate">Activate Selected</option>
                        <option value="deactivate">Deactivate Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="submitBulkAction()">Apply</button>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label class="small text-muted me-1 mb-0">Show:</label>
                <select class="form-select form-select-sm" style="width: 75px;" onchange="location = this.value;">
                    <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                        </th>
                        <th>User ID</th>
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="form-check-input user-check">
                            @else
                                <i class="bi bi-shield-fill-check text-muted" title="Protected account"></i>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $user->user_code ?? 'USR-' . $user->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;">
                                <div>
                                    <a href="{{ route('users.show', $user->id) }}" class="fw-bold text-dark text-decoration-none d-block lh-sm">
                                        {{ $user->name }}
                                    </a>
                                    <span class="text-muted small" style="font-size: 0.75rem;">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-secondary fw-semibold">
                            <code>{{ $user->username ?? '—' }}</code>
                        </td>
                        <td>
                            @if($user->role_id === 1)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 rounded-pill fw-semibold">
                                    <i class="bi bi-shield-shaded me-1"></i> {{ $user->role_name }}
                                </span>
                            @elseif($user->role_id === 2)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> {{ $user->role_name }}
                                </span>
                            @elseif($user->role_id === 5)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-semibold">
                                    <i class="bi bi-wallet2 me-1"></i> {{ $user->role_name }}
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2.5 py-1 rounded-pill fw-semibold">
                                    {{ $user->role_name }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $user->department ?? 'General' }}</span>
                        </td>
                        <td>
                            @if($user->last_login_at)
                                <span class="text-dark d-block fw-semibold" style="font-size: 0.8rem;">
                                    {{ $user->last_login_at->format('d M, Y H:i') }}
                                </span>
                                <span class="text-muted" style="font-size: 0.72rem;">
                                    {{ $user->last_login_at->diffForHumans() }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Never</span>
                            @endif
                        </td>
                        <td>
                            @if($user->isLocked())
                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1" title="Account locked due to failed attempts">
                                    <i class="bi bi-lock-fill me-1"></i> Locked
                                </span>
                            @elseif($user->status === 'Active')
                                <span class="badge bg-success text-white rounded-pill px-2.5 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Active
                                </span>
                            @elseif($user->status === 'Suspended')
                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">
                                    <i class="bi bi-slash-circle-fill me-1"></i> Suspended
                                </span>
                            @else
                                <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1">
                                    <i class="bi bi-pause-circle-fill me-1"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-pill px-2.5 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="font-size: 0.85rem; border-radius: 12px;">
                                    <li>
                                        <a class="dropdown-item py-1.5" href="{{ route('users.show', $user->id) }}">
                                            <i class="bi bi-person-lines-fill text-primary me-2"></i> View Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1.5" href="{{ route('users.edit', $user->id) }}">
                                            <i class="bi bi-pencil-square text-info me-2"></i> Edit Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1.5" href="{{ route('users.permissions', $user->id) }}">
                                            <i class="bi bi-shield-lock text-warning me-2"></i> Custom Permissions
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5" onclick="openResetPasswordModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}')">
                                            <i class="bi bi-key-fill text-warning me-2"></i> Reset Password...
                                        </button>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('users.revoke_sessions', $user->id) }}" onsubmit="return confirm('Revoke active sessions and remember cookies for {{ $user->name }}?');">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5">
                                                <i class="bi bi-box-arrow-right text-secondary me-2"></i> Revoke Active Sessions
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    @if($user->status !== 'Active')
                                        <li>
                                            <form method="POST" action="{{ route('users.toggle_status', $user->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="Active">
                                                <button type="submit" class="dropdown-item py-1.5 text-success">
                                                    <i class="bi bi-check-circle-fill me-2"></i> Activate Account
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <form method="POST" action="{{ route('users.toggle_status', $user->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="Inactive">
                                                <button type="submit" class="dropdown-item py-1.5 text-secondary">
                                                    <i class="bi bi-pause-circle me-2"></i> Deactivate Account
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('users.toggle_status', $user->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="Suspended">
                                                <button type="submit" class="dropdown-item py-1.5 text-warning">
                                                    <i class="bi bi-slash-circle me-2"></i> Suspend Account
                                                </button>
                                            </form>
                                        </li>
                                    @endif

                                    @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 text-danger" onclick="confirmDeleteUser('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                                                <i class="bi bi-trash3-fill me-2"></i> Delete Account
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                            No users matched your filter criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</form>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-lock-fill text-warning me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resetPasswordForm" method="POST" action="">
                @csrf
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3">
                        Choose a password management action for user: <strong id="resetModalUserName" class="text-dark"></strong> (<span id="resetModalUserEmail"></span>)
                    </p>

                    <div class="list-group mb-3">
                        <label class="list-group-item d-flex gap-3 py-3 rounded-3 mb-2 border">
                            <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="reset_type" value="generate_temp" checked onchange="toggleResetCustomInput(false)">
                            <div>
                                <span class="fw-bold d-block text-dark">Generate Strong Temporary Password</span>
                                <small class="text-muted">Generates a one-time random password and forces the user to choose their own on next sign-in.</small>
                            </div>
                        </label>
                        <label class="list-group-item d-flex gap-3 py-3 rounded-3 mb-2 border">
                            <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="reset_type" value="send_link" onchange="toggleResetCustomInput(false)">
                            <div>
                                <span class="fw-bold d-block text-dark">Send Expiring Password Reset Link</span>
                                <small class="text-muted">Dispatches a 60-minute secure reset token link directly to the user's email address.</small>
                            </div>
                        </label>
                        <label class="list-group-item d-flex gap-3 py-3 rounded-3 mb-2 border">
                            <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="reset_type" value="force_change" onchange="toggleResetCustomInput(false)">
                            <div>
                                <span class="fw-bold d-block text-dark">Flag Account to Force Password Change</span>
                                <small class="text-muted">Keeps current password active until user's next login, then prompts mandatory update.</small>
                            </div>
                        </label>
                        <label class="list-group-item d-flex gap-3 py-3 rounded-3 border">
                            <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="reset_type" value="custom_password" onchange="toggleResetCustomInput(true)">
                            <div>
                                <span class="fw-bold d-block text-dark">Manually Set New Password</span>
                                <small class="text-muted">Specify an explicit password immediately.</small>
                            </div>
                        </label>
                    </div>

                    <div id="customPasswordContainer" class="d-none mt-3 p-3 bg-light rounded-3 border">
                        <label class="form-label small fw-semibold text-dark">New Password Value</label>
                        <div class="input-group">
                            <input type="text" name="new_password" id="customNewPassInput" class="form-control form-control-sm" placeholder="Minimum 8 characters">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="generateRandomPass('customNewPassInput')">Generate</button>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="force_change_after" id="forceChangeAfterCheck" value="1" checked>
                            <label class="form-check-label small text-muted" for="forceChangeAfterCheck">Require user to change this on next login</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Execute Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete User Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteUserForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body py-3">
                    <p class="mb-2">Are you sure you want to delete user account <strong id="deleteModalUserName" class="text-dark"></strong>?</p>
                    <div class="alert alert-danger py-2 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> This user account will be soft-deleted. The user will be immediately blocked from signing in. All historical audit records, invoices, and bookings will be retained.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-semibold">
                        <i class="bi bi-trash3-fill me-1"></i> Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSelectAll(master) {
    const checks = document.querySelectorAll('.user-check');
    checks.forEach(c => c.checked = master.checked);
}

function submitBulkAction() {
    const action = document.getElementById('bulkActionSelect').value;
    if (!action) {
        alert('Please choose an action from the dropdown.');
        return;
    }
    const anyChecked = document.querySelectorAll('.user-check:checked').length > 0;
    if (!anyChecked) {
        alert('Please check at least one user to perform bulk actions.');
        return;
    }
    if (confirm('Are you sure you want to execute bulk action [' + action + '] on selected users?')) {
        document.getElementById('bulkForm').submit();
    }
}

function openResetPasswordModal(userId, userName, userEmail) {
    document.getElementById('resetPasswordForm').action = '/users/' + userId + '/reset-password';
    document.getElementById('resetModalUserName').innerText = userName;
    document.getElementById('resetModalUserEmail').innerText = userEmail;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

function toggleResetCustomInput(show) {
    const container = document.getElementById('customPasswordContainer');
    if (show) {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
}

function generateRandomPass(inputId) {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%';
    let res = '';
    for (let i = 0; i < 12; i++) {
        res += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById(inputId).value = res;
}

function confirmDeleteUser(userId, userName) {
    document.getElementById('deleteUserForm').action = '/users/' + userId;
    document.getElementById('deleteModalUserName').innerText = userName;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>
@endpush
@endsection
