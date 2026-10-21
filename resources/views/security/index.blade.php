@extends('layouts.app')

@section('title', 'Security Settings')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-shield-check text-primary me-2"></i>Security Settings & Policies
        </h4>
        <p class="text-muted small mb-0">Configure authentication rate-limiting, lockout defense parameters, password complexity requirements, and session lifetimes.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('login-history.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm">
            <i class="bi bi-clock-history me-1"></i> View Login History
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark">System Security Parameters</h6>
            </div>

            <div class="card-body p-4 pt-2">
                <form method="POST" action="{{ route('security-settings.update') }}">
                    @csrf

                    <!-- Account Lockout Defenses -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-lock-fill text-danger fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark">Account Lockout & Brute Force Defense</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Max Failed Login Attempts</label>
                                <input type="number" name="max_login_attempts" class="form-control" min="3" max="20" value="{{ old('max_login_attempts', $settings['max_login_attempts']->value ?? 5) }}" required>
                                <div class="form-text small text-muted">Accounts will be automatically locked after this threshold.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Lockout Duration (Minutes)</label>
                                <input type="number" name="lockout_minutes" class="form-control" min="1" max="1440" value="{{ old('lockout_minutes', $settings['lockout_minutes']->value ?? 15) }}" required>
                                <div class="form-text small text-muted">How long locked accounts must wait before next attempt.</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Password Policy -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-key-fill text-warning fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark">Password Complexity Standards</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Minimum Password Length</label>
                                <input type="number" name="password_min_length" class="form-control" min="6" max="64" value="{{ old('password_min_length', $settings['password_min_length']->value ?? 8) }}" required>
                                <div class="form-text small text-muted">Recommended: at least 8 characters.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Force Password Rotation (Days)</label>
                                <input type="number" name="force_password_change_days" class="form-control" min="0" max="365" value="{{ old('force_password_change_days', $settings['force_password_change_days']->value ?? 90) }}" required>
                                <div class="form-text small text-muted">Set 0 to disable periodic rotation prompts.</div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="password_require_uppercase" id="reqUpperCheck" value="1" {{ ($settings['password_require_uppercase']->value ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark" for="reqUpperCheck">
                                            Require at least one uppercase letter (A-Z)
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="password_require_numbers" id="reqNumbersCheck" value="1" {{ ($settings['password_require_numbers']->value ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark" for="reqNumbersCheck">
                                            Require at least one numeric digit (0-9)
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="password_require_symbols" id="reqSymbolsCheck" value="1" {{ ($settings['password_require_symbols']->value ?? '0') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark" for="reqSymbolsCheck">
                                            Require at least one special symbol (!@#$%^&*)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Session Lifetimes -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-hourglass-split text-primary fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark">Session Management & Inactivity Lifetimes</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark">Session Inactivity Timeout (Minutes)</label>
                                <input type="number" name="session_lifetime_minutes" class="form-control" min="15" max="1440" value="{{ old('session_lifetime_minutes', $settings['session_lifetime_minutes']->value ?? 120) }}" required>
                                <div class="form-text small text-muted">Sessions will be invalidated after this idle period.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> Update Security Policies
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Security Posture Summary -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h6 class="fw-bold mb-3 text-dark">
                <i class="bi bi-shield-shaded text-success me-1"></i> Active Security Hardening
            </h6>

            <div class="list-group list-group-flush small">
                <div class="list-group-item d-flex align-items-start gap-2.5 px-0 py-2 border-0">
                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                    <div>
                        <strong class="d-block text-dark">Bcrypt Password Hashing</strong>
                        <span class="text-muted">Passwords are never stored or logged in plain text.</span>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-start gap-2.5 px-0 py-2 border-0">
                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                    <div>
                        <strong class="d-block text-dark">Anti-Brute Force Lockout</strong>
                        <span class="text-muted">Blocks IP and accounts after 5 consecutive failed attempts.</span>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-start gap-2.5 px-0 py-2 border-0">
                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                    <div>
                        <strong class="d-block text-dark">Math CAPTCHA Challenge</strong>
                        <span class="text-muted">Prevents automated bot attacks on login endpoints.</span>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-start gap-2.5 px-0 py-2 border-0">
                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                    <div>
                        <strong class="d-block text-dark">Super Admin Self-Defense</strong>
                        <span class="text-muted">Prevents accidental self-deletion or deactivation of the last Super Admin.</span>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-start gap-2.5 px-0 py-2 border-0">
                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                    <div>
                        <strong class="d-block text-dark">Backend Permission Gateways</strong>
                        <span class="text-muted">Enforces action-level permissions directly on backend middleware.</span>
                    </div>
                </div>
                <div class="list-group-item d-flex align-items-start gap-2.5 px-0 py-2 border-0">
                    <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                    <div>
                        <strong class="d-block text-dark">Expiring Single-Use Reset Tokens</strong>
                        <span class="text-muted">Tokens automatically expire after 60 minutes and cannot be reused.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
