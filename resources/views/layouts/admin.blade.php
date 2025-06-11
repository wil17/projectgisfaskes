<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - SIG Fasilitas Kesehatan Banjarmasin')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Nunito -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <!-- Additional page-specific styles -->
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar - Removed "collapse" class -->
            <div class="col-md-2 col-lg-2 d-md-block sidebar">
                <div class="navbar-brand">
                    <img src="{{ asset('images/logo-banjarmasin.png') }}" alt="Logo" width="40" height="40" class="d-inline-block align-text-top">
                    <span>SIG Faskes</span>
                </div>
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> 
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <!-- Updated Dropdown Menu for Facility Management -->
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#faskes-collapse" aria-expanded="{{ request()->routeIs('admin.faskes*') || request()->routeIs('admin.apotek*') || request()->routeIs('admin.klinik*') ? 'true' : 'false' }}">
                                <i class="fas fa-hospital"></i>
                                <span>Manajemen Faskes</span>
                                <i class="fas fa-angle-down ms-auto arrow-icon"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.faskes*') || request()->routeIs('admin.apotek*') || request()->routeIs('admin.klinik*') ? 'show' : '' }}" id="faskes-collapse">
                                <ul class="nav flex-column ms-3 submenu">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.apotek*') ? 'active' : '' }}" href="{{ route('admin.apotek.index') }}">
                                            <i class="fas fa-pills"></i> 
                                            <span>Apotek</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.klinik*') ? 'active' : '' }}" href="{{ route('admin.klinik.index') }}">
                                            <i class="fas fa-stethoscope"></i> 
                                            <span>Klinik</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.puskesmas*') ? 'active' : '' }}" href="{{ route('admin.puskesmas.index') }}">
                                            <i class="fas fa-clinic-medical"></i>
                                            <span>Puskesmas</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.rumahsakit*') ? 'active' : '' }}" href="{{ route('admin.rumahsakit.index') }}">
                                            <i class="fas fa-hospital-alt"></i> 
                                            <span>Rumah Sakit</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                <!-- Top Navigation Bar -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        <!-- Updated Hamburger Menu Button - Removed Bootstrap data attributes -->
                        <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="d-flex justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <h5 class="my-2 text-primary">@yield('page-title', 'Dashboard')</h5>
                                <nav aria-label="breadcrumb" class="ms-3 d-none d-lg-block">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        @yield('breadcrumb')
                                    </ol>
                                </nav>
                            </div>
                            <div class="d-flex align-items-center">
                                <!-- Admin Dropdown -->
                                <div class="admin-dropdown dropdown">
                                    <button class="dropdown-toggle d-flex align-items-center" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 35px; height: 35px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ session('admin_username') }}</div>
                                                <div class="small text-muted">Administrator</div>
                                            </div>
                                            <i class="fas fa-chevron-down ms-2 small"></i>
                                        </div>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <div class="dropdown-header">
                                                <strong>{{ session('admin_username') }}</strong>
                                                <div class="small text-muted">{{ session('admin_email') }}</div>
                                            </div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-user-cog me-2"></i>
                                                Profile Settings
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-sign-out-alt me-2"></i>
                                                    Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
                
                <!-- Footer -->
                <footer class="footer mt-auto py-3 text-center text-muted">
                    <div class="container">
                        <span>&copy; {{ date('Y') }} SIG Fasilitas Kesehatan Banjarmasin. Semua hak dilindungi.</span>
                    </div>
                </footer>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Leaflet JS for maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Chart JS -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Admin JS -->
    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    <!-- Page-specific scripts -->
    @yield('scripts')
</body>
</html>