<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIG Fasilitas Kesehatan Banjarmasin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <style>
        :root {
            --primary-color: #0B5345;
            --secondary-color: #117A65;
            --accent-color: rgba(17, 122, 101, 0.1);
            --text-light: #ffffff;
            --text-dark: #333333;
            --border-radius-lg: 20px;
            --border-radius-md: 12px;
            --border-radius-sm: 8px;
            --shadow-standard: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Arial', sans-serif;
            padding: 1.5rem 0;
        }

        .login-container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-standard);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 2rem 1.5rem;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
        }

        .login-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
            transition: all 0.3s ease;
        }

        .form-floating {
            margin-bottom: 1.25rem;
        }

        .form-control {
            border-radius: var(--border-radius-sm);
            border: 2px solid #e0e0e0;
            padding: 0.75rem;
            height: calc(3.5rem + 2px);
            transition: all 0.3s ease;
        }

        .form-floating label {
            padding: 1rem 0.75rem;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(17, 122, 101, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 122, 101, 0.4);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .alert {
            border-radius: var(--border-radius-sm);
            border: none;
            margin-bottom: 1.25rem;
        }

        .back-link {
            color: #6c757d;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
            padding: 0.5rem;
            border-radius: var(--border-radius-sm);
        }

        .back-link:hover {
            color: var(--secondary-color);
            background-color: rgba(17, 122, 101, 0.05);
        }

        #map {
            width: 100%;
            height: 100%;
            min-height: 450px;
            border-radius: var(--border-radius-lg) 0 0 var(--border-radius-lg);
            z-index: 1;
        }

        .map-card {
            padding: 0;
            overflow: hidden;
            position: relative;
        }

        .map-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(11, 83, 69, 0.6);
            color: white;
            z-index: 2;
            text-align: center;
            padding: 1rem;
            border-radius: var(--border-radius-lg) 0 0 var(--border-radius-lg);
        }

        .map-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: var(--shadow-standard);
            transition: all 0.3s ease;
        }

        .form-section {
            padding: 2rem 1.75rem;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: var(--accent-color);
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 15%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 80%;
            right: 20%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 40%;
            right: 15%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Responsive Styles */
        /* Extra Small (xs) - Mobile Phones */
        @media (max-width: 575.98px) {
            body {
                padding: 1rem 0;
            }
            
            .login-container {
                padding: 0 10px;
            }
            
            .login-card {
                margin: 0 5px;
            }
            
            .login-header {
                padding: 1.5rem 1rem;
            }
            
            .login-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .map-logo {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            
            #map {
                min-height: 180px;
                border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            }
            
            .map-overlay {
                border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            }
            
            .form-section {
                padding: 1.5rem 1.25rem;
            }
        }

        /* Small (sm) - Tablets */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .login-container {
                max-width: 500px;
            }
            
            #map {
                min-height: 220px;
                border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            }
            
            .map-overlay {
                border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            }
        }

        /* Medium (md) - Small Laptops */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .login-container {
                max-width: 700px;
            }
            
            #map {
                min-height: 250px;
                border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            }
            
            .map-overlay {
                border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            }
        }

        /* Large (lg) and above - Desktops */
        @media (min-width: 992px) {
            .login-icon {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
            
            .map-logo {
                width: 100px;
                height: 100px;
                font-size: 3rem;
            }
            
            .form-section {
                padding: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container-fluid">
        <div class="login-container">
            <div class="row g-0 login-card">
                <!-- Map Section -->
                <div class="col-lg-6 map-card">
                    <div id="map"></div>
                    <div class="map-overlay">
                        <div class="map-logo">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <h2 class="mb-2">SIG Fasilitas Kesehatan</h2>
                        <p class="mb-0">Kota Banjarmasin</p>
                    </div>
                </div>
                
                <!-- Login Form Section -->
                <div class="col-lg-6">
                    <div class="login-header">
                        <div class="login-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="mb-2">Admin Login</h3>
                        <p class="mb-0 opacity-75">SIG Fasilitas Kesehatan Banjarmasin</p>
                    </div>
                    
                    <div class="form-section">
                        @if($errors->has('login'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ $errors->first('login') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.login.submit') }}" method="POST">
                            @csrf
                            <div class="form-floating">
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" placeholder="Username" 
                                       value="{{ old('username') }}" required>
                                <label for="username">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Password" required>
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-login w-100 text-white mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('map') }}" class="back-link">
                                <i class="fas fa-arrow-left me-2"></i>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <script>
        // Initialize the map focused on Banjarmasin
        const map = L.map('map').setView([-3.3186, 114.5944], 13);
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Disable map zoom and drag to make it static for display purposes
        map.dragging.disable();
        map.touchZoom.disable();
        map.doubleClickZoom.disable();
        map.scrollWheelZoom.disable();
        map.boxZoom.disable();
        map.keyboard.disable();
        if (map.tap) map.tap.disable();

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Add loading state to login button
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-login');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            btn.disabled = true;
        });
        
        // Handle window resize to adjust map container
        function adjustMapHeight() {
            // Check if we're in mobile view
            if (window.innerWidth < 992) {
                const mapElement = document.getElementById('map');
                // Set map height proportional to width for better aspect ratio on mobile
                const mapWidth = mapElement.offsetWidth;
                const mapHeight = Math.min(mapWidth * 0.6, 250); // 60% of width but max 250px
                mapElement.style.minHeight = mapHeight + 'px';
            } else {
                // Reset for desktop view
                document.getElementById('map').style.minHeight = '450px';
            }
            
            // Invalidate map size to trigger resize
            if (map) {
                map.invalidateSize();
            }
        }
        
        // Run on page load and resize
        window.addEventListener('load', adjustMapHeight);
        window.addEventListener('resize', adjustMapHeight);
    </script>
</body>
</html>