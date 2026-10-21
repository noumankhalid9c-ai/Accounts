<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Mandatory Password Change - {{ config('app.name') }}</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('MADINA-LOGO-3.png') }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #090e1a 0%, #1e1b4b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .auth-card {
            max-width: 460px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-2 rounded-pill fs-6 fw-bold">
                        <i class="bi bi-shield-exclamation me-1"></i> Temporary Password Notice
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-1">Set Your Permanent Password</h4>
                <p class="text-muted small">You are logged in using a temporary password. Company security policy requires setting your personal password before continuing.</p>
            </div>

            @if (session('warning'))
                <div class="alert alert-warning py-2 small mb-4">
                    <i class="bi bi-info-circle me-1"></i> {{ session('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 py-2 small">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.force_change.post') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Current / Temporary Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                        <input type="password" name="current_password" class="form-control border-start-0" placeholder="••••••••" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="Minimum 8 characters" required>
                    </div>
                    <div class="form-text small text-muted">Must be at least 8 characters and differ from temporary password.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-dark">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                        <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Re-enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold rounded-pill">
                    <i class="bi bi-check-circle-fill me-1"></i> Save Password & Enter System
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger text-decoration-none small p-0">
                        <i class="bi bi-box-arrow-right me-1"></i> Sign Out Instead
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
