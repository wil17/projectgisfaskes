<nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container">
        <!-- Mobile view - hamburger and logo -->
        <div class="d-flex align-items-center">
            <button class="navbar-toggler me-2 border-0 p-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" id="hamburgerBtn">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="{{ route('map') }}">
                <img src="{{ asset('images/logo-banjarmasin.png') }}" alt="Logo Banjarmasin" class="me-2" 
                     style="width: 40px; height: 40px;">
                <span>SIG Fasilitas Kesehatan Banjarmasin</span>
            </a>
        </div>

        <!-- Desktop navigation - right aligned -->
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

<style>
/* Media query untuk tampilan mobile */
@media (max-width: 991.98px) {
    .navbar .container {
        padding-left: 1rem;
        padding-right: 1rem;
        width: 100%;
    }
    
    .navbar-brand img {
        width: 35px !important;
        height: 35px !important;
    }
    
    .navbar-brand span {
        font-size: 0.9rem;
    }
}
</style>