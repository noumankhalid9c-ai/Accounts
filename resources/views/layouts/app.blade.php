<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noodp, noydir">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <title>@yield('title', config('app.name', 'App'))</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('MADINA-LOGO-3.png') }}">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <x-ui.sidebar />

        <!-- Page Content -->
        <div id="content" class="bg-light">
            <!-- Top Navbar -->
            <nav class="top-navbar mb-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light border d-md-none rounded-circle p-2 d-flex align-items-center justify-content-center" id="menu-toggle" style="width: 38px; height: 38px;">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="d-none d-sm-block">
                        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-medium">
                            <i class="bi bi-shield-check text-success me-1"></i> System Online
                        </span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="user-pill">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block text-start lh-sm me-1">
                            <div class="fw-bold text-dark small mb-0">{{ Auth::user()->name ?? 'User' }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ Auth::user()->role ?? 'Admin' }}</div>
                        </div>
                    </div>
                    
                    <a href="{{ route('profile') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary" title="Account Settings">
                        <i class="bi bi-gear me-1"></i> Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold" title="Sign Out">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="container-fluid px-3 px-md-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div> <!-- End Container Fluid -->
        </div> <!-- End Page Content -->
    </div> <!-- End Wrapper -->

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Mobile menu toggle
        document.getElementById("menu-toggle")?.addEventListener("click", function(e) {
            e.preventDefault();
            const sidebar = document.getElementById("sidebar");
            if (sidebar) {
                sidebar.classList.toggle("show");
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
