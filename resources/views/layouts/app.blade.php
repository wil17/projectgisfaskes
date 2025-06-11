<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGisFaskes - Sistem Apotek</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            padding-top: 20px;
        }
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">WebGisFaskes</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('apotek.index') ? 'active' : '' }}" href="{{ route('apotek.index') }}">Apotek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('upload.apotek') ? 'active' : '' }}" href="{{ route('upload.apotek') }}">Import Data</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('map.index') ? 'active' : '' }}" href="{{ route('map.index') }}">Peta</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')

        <footer class="mt-5 pt-3 pb-3 text-center text-muted border-top">
            <p>&copy; {{ date('Y') }} WebGisFaskes</p>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>