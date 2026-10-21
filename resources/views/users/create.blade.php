@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2 text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to User List
                </a>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-plus-fill text-primary me-2"></i>Create New User Account
                </h4>
            </div>
            <div>
                <span class="badge bg-light text-primary border px-3 py-2 rounded-pill font-monospace fw-bold fs-6">
                    <i class="bi bi-qr-code me-1"></i> {{ $autoUserCode }}
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4 p-3 mb-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-2 fw-bold">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i> Please resolve the following validation errors:
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">User Account Credentials & Profile</h6>
                        <span class="text-muted small">All fields marked with an asterisk (*) are mandatory.</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 pt-2">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" name="user_code" value="{{ $autoUserCode }}">

                    <div class="row g-3 mb-4">
                        <!-- Profile Avatar Upload with Live Preview -->
                        <div class="col-12 col-md-3 text-center border-end pe-md-4">
                            <label class="form-label small fw-semibold text-muted d-block mb-3">Profile Picture (Optional)</label>
                            <div class="position-relative d-inline-block mb-3">
                                <img id="avatarPreview" src="https://ui-avatars.com/api/?name=New+User&background=4f46e5&color=fff&size=128" alt="Avatar Preview" class="rounded-circle border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                                <label for="profilePictureInput" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow" style="width: 34px; height: 34px; cursor: pointer;" title="Upload photo">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                            <input type="file" name="profile_picture" id="profilePictureInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                            <div class="form-text small text-muted">JPG, PNG or WEBP (Max 2MB).</div>
                        </div>

                        <!-- Basic Information -->
                        <div class="col-12 col-md-9 ps-md-4">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="name" id="fullNameInput" class="form-control border-start-0" placeholder="e.g. Nouman Khalid" value="{{ old('name') }}" required autofocus oninput="suggestUsername(this.value)">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Username <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">@</span>
                                        <input type="text" name="username" id="usernameInput" class="form-control border-start-0" placeholder="e.g. nouman.khalid" value="{{ old('username') }}" required>
                                    </div>
                                    <div class="form-text small text-muted">Used for login. Alphanumeric, dots and dashes allowed.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0" placeholder="user@madina.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Mobile / Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                                        <input type="text" name="mobile" class="form-control border-start-0" placeholder="+92 300 1234567" value="{{ old('mobile') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Role, Status & Department -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Assigned Role <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                                <select name="role_id" class="form-select border-start-0" required>
                                    <option value="">Select Role...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }} ({{ $role->description }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-toggle-on text-muted"></i></span>
                                <select name="status" class="form-select border-start-0" required>
                                    <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active (Normal Access)</option>
                                    <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive (Deactivated)</option>
                                    <option value="Suspended" {{ old('status') === 'Suspended' ? 'selected' : '' }}>Suspended (Blocked)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Department (Optional)</label>
                            <input type="text" name="department" class="form-control" list="deptOptions" placeholder="e.g. Sales / CRM" value="{{ old('department') }}">
                            <datalist id="deptOptions">
                                @foreach($departments as $d)
                                    <option value="{{ $d }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Designation / Title (Optional)</label>
                            <input type="text" name="designation" class="form-control" placeholder="e.g. Ticketing Officer" value="{{ old('designation') }}">
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Security & Credentials -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="bi bi-key-fill text-warning me-1"></i>Security & Initial Password
                                </h6>
                                <span class="text-muted small">Passwords are hashed using industry-standard Bcrypt.</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="generateStrongPassword()">
                                <i class="bi bi-dice-5-fill me-1"></i> Generate Strong Password
                            </button>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" id="passwordInput" class="form-control border-start-0 border-end-0" placeholder="Minimum 8 characters" required>
                                <button type="button" class="btn btn-light border border-start-0 text-muted" onclick="togglePasswordVisibility('passwordInput')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" name="password_confirmation" id="passwordConfirmInput" class="form-control border-start-0 border-end-0" placeholder="Re-enter password" required>
                                <button type="button" class="btn btn-light border border-start-0 text-muted" onclick="togglePasswordVisibility('passwordConfirmInput')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="must_change_password" id="mustChangeCheck" value="1" {{ old('must_change_password', true) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-dark" for="mustChangeCheck">
                                        <i class="bi bi-shield-check text-primary me-1"></i> Require user to change temporary password on first sign-in (Recommended)
                                    </label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="send_welcome_email" id="welcomeEmailCheck" value="1" {{ old('send_welcome_email', true) ? 'checked' : '' }}>
                                    <label class="form-check-label small text-muted" for="welcomeEmailCheck">
                                        <i class="bi bi-envelope-check text-success me-1"></i> Send welcome email with sign-in instructions
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('users.index') }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> Save User Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function suggestUsername(name) {
    const usernameInput = document.getElementById('usernameInput');
    if (!usernameInput.dataset.manualEdited) {
        usernameInput.value = name.toLowerCase().replace(/[^a-z0-9]/g, '.').replace(/\.+/g, '.').replace(/^\.|\.$/g, '');
    }
}

document.getElementById('usernameInput')?.addEventListener('change', function() {
    this.dataset.manualEdited = 'true';
});

function generateStrongPassword() {
    const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const lower = 'abcdefghijkmnpqrstuvwxyz';
    const numbers = '23456789';
    const symbols = '!@#$%^&*';
    let pass = '';
    pass += upper.charAt(Math.floor(Math.random() * upper.length));
    pass += lower.charAt(Math.floor(Math.random() * lower.length));
    pass += numbers.charAt(Math.floor(Math.random() * numbers.length));
    pass += symbols.charAt(Math.floor(Math.random() * symbols.length));
    const all = upper + lower + numbers + symbols;
    for (let i = 0; i < 8; i++) {
        pass += all.charAt(Math.floor(Math.random() * all.length));
    }
    // Shuffle
    pass = pass.split('').sort(() => 0.5 - Math.random()).join('');
    
    document.getElementById('passwordInput').value = pass;
    document.getElementById('passwordInput').type = 'text';
    document.getElementById('passwordConfirmInput').value = pass;
    document.getElementById('passwordConfirmInput').type = 'text';

    alert('Generated Strong Password: ' + pass + '\n(Remember to keep it confidential!)');
}

function togglePasswordVisibility(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
@endsection
