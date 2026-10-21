<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Forgot Password - {{ config('app.name') }}</title>
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
            max-width: 440px;
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
                    <img src="{{ asset('MADINA-LOGO-3.png') }}" onerror="this.onerror=null; this.src='https://placehold.co/200x52?text=Logo';" alt="{{ config('app.name') }}" style="height: 52px; max-width: 200px; object-fit: contain;">
                </div>
                <h4 class="fw-bold text-dark mb-1">Reset Password</h4>
                <p class="text-muted small">Enter your account email to receive a secure password reset link.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4 py-2 small">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if (session('demo_reset_url'))
                <div class="alert alert-info py-2 small mb-4">
                    <div class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i>Local Test Password Reset Link:</div>
                    <a href="{{ session('demo_reset_url') }}" class="text-break text-decoration-underline">{{ session('demo_reset_url') }}</a>
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

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-dark">Registered Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" placeholder="name@company.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="form-text small text-muted">A single-use, secure link valid for 60 minutes will be generated.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold rounded-pill">
                    <i class="bi bi-send-fill me-1"></i> Send Password Reset Link
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <a href="{{ route('login') }}" class="small text-decoration-none text-muted fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Return to Sign In
                </a>
            </div>
        </div>
    </div>
</body>
</html>
