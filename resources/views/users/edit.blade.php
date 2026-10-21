@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2 text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to User List
                </a>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit User: {{ $user->name }}
                </h4>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-eye me-1"></i> View Profile
                </a>
                <span class="badge bg-light text-primary border px-3 py-2 rounded-pill font-monospace fw-bold fs-6">
                    {{ $user->user_code ?? 'USR-' . $user->id }}
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4 p-3 mb-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-2 fw-bold">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i> Please resolve the following errors:
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
                <h6 class="fw-bold mb-0 text-dark">User Account Information</h6>
            </div>

            <div class="card-body p-4 pt-2">
                <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <!-- Profile Avatar Upload with Live Preview -->
                        <div class="col-12 col-md-3 text-center border-end pe-md-4">
                            <label class="form-label small fw-semibold text-muted d-block mb-3">Profile Picture</label>
                            <div class="position-relative d-inline-block mb-3">
                                <img id="avatarPreview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                                <label for="profilePictureInput" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow" style="width: 34px; height: 34px; cursor: pointer;" title="Change photo">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                            <input type="file" name="profile_picture" id="profilePictureInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                            <div class="form-text small text-muted">Click the camera icon to upload new avatar.</div>
                        </div>

                        <!-- Basic Information -->
                        <div class="col-12 col-md-9 ps-md-4">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Username <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">@</span>
                                        <input type="text" name="username" class="form-control border-start-0" value="{{ old('username', $user->username) }}" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Mobile / Phone Number</label>
                                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Role, Status & Department -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Assigned Role <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-select" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Active" {{ old('status', $user->status) === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status', $user->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="Suspended" {{ old('status', $user->status) === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="Locked" {{ old('status', $user->status) === 'Locked' ? 'selected' : '' }}>Locked</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Department</label>
                            <input type="text" name="department" class="form-control" list="deptOptions" value="{{ old('department', $user->department) }}">
                            <datalist id="deptOptions">
                                @foreach($departments as $d)
                                    <option value="{{ $d }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Designation / Title</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation', $user->designation) }}">
                        </div>

                        <div class="col-12 col-md-8 d-flex align-items-center">
                            <div class="form-check pt-3">
                                <input class="form-check-input" type="checkbox" name="must_change_password" id="mustChangeEditCheck" value="1" {{ old('must_change_password', $user->must_change_password) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold text-dark" for="mustChangeEditCheck">
                                    Flag account to require password change upon next sign-in
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="text-muted small">
                            Created on {{ $user->created_at ? $user->created_at->format('d M, Y') : '—' }}
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-light border rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Update User Profile
                            </button>
                        </div>
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
</script>
@endpush
@endsection
