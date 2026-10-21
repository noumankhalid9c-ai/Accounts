@extends('layouts.app')

@section('title', 'My Profile & Account Settings')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-person-circle text-primary me-2"></i>My Profile & Account Security
        </h4>
        <p class="text-muted small mb-0">Manage your profile details, personal contact information, password, and active sessions.</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger rounded-4 p-3 mb-4 shadow-sm">
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: User Summary Card -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4 bg-white">
            <div class="position-relative d-inline-block mx-auto mb-3">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
            </div>

            <h5 class="fw-bold text-dark mb-0">{{ $user->name }}</h5>
            <div class="text-secondary small mb-2"><code>{{ '@' . ($user->username ?? 'user') }}</code></div>

            <div class="mb-3">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> {{ $user->role_name }}
                </span>
            </div>

            <div class="border-top pt-3 text-start small">
                <div class="d-flex justify-content-between py-1.5 border-bottom border-light">
                    <span class="text-muted">Account Code:</span>
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
                    <span class="text-muted">Last Login:</span>
                    <span class="fw-semibold text-dark">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Now' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1.5">
                    <span class="text-muted">Last IP Address:</span>
                    <span class="fw-semibold text-dark font-monospace">{{ $user->last_login_ip ?? request()->ip() }}</span>
                </div>
            </div>

            <div class="border-top pt-3 mt-3">
                <form method="POST" action="{{ route('profile.revoke_sessions') }}" onsubmit="return confirm('Invalidate all other logged-in sessions?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100 fw-semibold">
                        <i class="bi bi-shield-x me-1"></i> Revoke Other Active Sessions
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Profile Edit & Change Password Forms -->
    <div class="col-12 col-xl-8">
        <!-- Personal Information Edit Form -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i>Personal Profile Information
                </h6>
            </div>
            <div class="card-body p-4 pt-2">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Mobile / Phone Number</label>
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Update Profile Photo</label>
                            <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Save Profile Details
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-shield-lock-fill text-warning me-2"></i>Change Password
                </h6>
            </div>
            <div class="card-body p-4 pt-2">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="current_password" class="form-control border-start-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="Minimum 8 characters" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Re-enter password" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold shadow-sm text-dark">
                            <i class="bi bi-arrow-repeat me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
