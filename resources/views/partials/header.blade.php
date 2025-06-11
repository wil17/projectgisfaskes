<nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container">
        <div class="d-flex align-items-center">
            <button class="navbar-toggler me-2 border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" id="hamburgerBtn">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="{{ route('map') }}">
                <img src="{{ asset('images/logo-banjarmasin.png') }}" alt="Logo Banjarmasin" style="width: 50px; height: 50px;" class="me-2">
                <span>SIG Fasilitas Kesehatan Banjarmasin</span>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('map') ? 'active' : '' }}" aria-current="page" href="{{ route('map') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('statistik') ? 'active' : '' }}" href="{{ route('statistik') }}">Statistik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('data.layanan*') ? 'active' : '' }}" href="{{ route('data.layanan')}}">Data Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.login') ? 'active' : '' }}" href="{{ route('admin.login') }}">Masuk</a>
                </li>
            </ul>
        </div>
    </div>
</nav>