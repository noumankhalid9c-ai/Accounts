<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noodp, noydir">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <title>Login - {{ config('app.name') }}</title>
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
        }
        .login-card {
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
    <div class="login-card">
        <div class="p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <img src="{{ asset('MADINA-LOGO-3.png') }}" onerror="this.onerror=null; this.src='https://placehold.co/200x52?text=Logo';" alt="{{ config('app.name') }} Logo" style="height: 52px; max-width: 200px; object-fit: contain;">
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ config('app.name') }}</h4>
                <p class="text-muted small">Sign in to your financial management portal</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 py-2 small">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email or Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="login" class="form-control border-start-0" placeholder="Enter your email or username" value="{{ old('login') }}" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <!-- Security Verification CAPTCHA -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="captcha-input" class="form-label mb-0 small fw-semibold text-dark">
                            <i class="bi bi-shield-check text-primary me-1"></i>Security Verification
                        </label>
                        <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary" onclick="refreshCaptcha()" title="Generate new question">
                            <i class="bi bi-arrow-clockwise"></i> New question
                        </button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-dark fw-bold border-end-0 user-select-none" id="captcha-question" style="font-size: 1.05rem; letter-spacing: 1px; min-width: 95px; justify-content: center;">
                            {{ session('captcha_num1', rand(2, 9)) }} + {{ session('captcha_num2', rand(1, 9)) }} = ?
                        </span>
                        <input type="number" name="captcha" id="captcha-input" class="form-control border-start-0" placeholder="Answer" required autocomplete="off">
                    </div>
                    <div class="form-text small text-muted">Solve this simple math challenge to protect against bot attacks.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fs-6">Sign In</button>
            </form>

            <div class="text-center text-muted small mt-4 pt-3 border-top">
                IT Company Financial & Resource Management Suite
            </div>
        </div>
    </div>

    <script>
    function refreshCaptcha() {
        const qSpan = document.getElementById('captcha-question');
        const input = document.getElementById('captcha-input');
        qSpan.innerText = '...';
        fetch('{{ route('captcha.refresh') }}')
            .then(res => res.json())
            .then(data => {
                qSpan.innerText = data.question;
                input.value = '';
                input.focus();
            })
            .catch(() => {
                // Ignore error, keep old text
            });
    }
    </script>
</body>
</html>
