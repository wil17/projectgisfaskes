<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Peta Fasilitas Kesehatan di Kota Banjarmasin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-minimap/3.6.0/Control.MiniMap.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.min.css" />
    <link href="{{ asset('css/data-layanan.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.header')

<div class="container mt-5 mb-5 fade-in">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">Data Layanan Fasilitas Kesehatan</h1>
            <div class="text-center mb-5">
                <p class="lead">Pilih kategori fasilitas kesehatan yang ingin Anda lihat</p>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 shadow category-card">
                <div class="card-body">
                    <div class="category-icon bg-light-blue">
                        <i class="fas fa-pills fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title mt-3">Apotek</h5>
                    <p class="card-text text-muted">Data lengkap apotek yang tersedia di Kota Banjarmasin</p>
                    <a href="{{ route('data.layanan.apotek') }}" class="btn btn-primary">Lihat Data</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 shadow category-card">
                <div class="card-body">
                    <div class="category-icon bg-light-green">
                        <i class="fas fa-clinic-medical fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title mt-3">Klinik</h5>
                    <p class="card-text text-muted">Data lengkap klinik yang tersedia di Kota Banjarmasin</p>
                    <a href="{{ route('data.layanan.klinik') }}" class="btn btn-primary">Lihat Data</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 shadow category-card">
                <div class="card-body">
                    <div class="category-icon bg-light-red">
                        <i class="fas fa-hospital fa-3x text-danger"></i>
                    </div>
                    <h5 class="card-title mt-3">Puskesmas</h5>
                    <p class="card-text text-muted">Data lengkap puskesmas yang tersedia di Kota Banjarmasin</p>
                    <a href="{{ route('data.layanan.puskesmas') }}" class="btn btn-primary">Lihat Data</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 shadow category-card">
                <div class="card-body">
                    <div class="category-icon bg-light-yellow">
                        <i class="fas fa-heartbeat fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title mt-3">Rumah Sakit</h5>
                    <p class="card-text text-muted">Data lengkap rumah sakit yang tersedia di Kota Banjarmasin</p>
                    <a href="{{ route('data.layanan.rumahsakit') }}" class="btn btn-primary">Lihat Data</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-map-marked-alt fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-1">Lihat Lokasi Fasilitas di Peta</h5>
                        <p class="mb-0">Anda juga dapat melihat lokasi fasilitas kesehatan pada peta interaktif. <a href="{{ route('map') }}" class="alert-link">Buka Peta Interaktif <i class="fas fa-arrow-right ms-1"></i></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5 counters">
        <div class="col-12 mb-4">
            <h2 class="text-center">Statistik Fasilitas Kesehatan</h2>
            <p class="text-center text-muted">Data fasilitas kesehatan yang tersedia di Kota Banjarmasin</p>
        </div>
        
        <div class="col-md-3 col-6 mb-4">
            <div class="card text-center h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-pills fa-2x text-primary mb-3"></i>
                    <h3 class="counter" data-target="176">0</h3>
                    <p class="card-text">Apotek</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-4">
            <div class="card text-center h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-clinic-medical fa-2x text-success mb-3"></i>
                    <h3 class="counter" data-target="132">0</h3>
                    <p class="card-text">Klinik</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-4">
            <div class="card text-center h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-hospital fa-2x text-danger mb-3"></i>
                    <h3 class="counter" data-target="28">0</h3>
                    <p class="card-text">Puskesmas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-4">
            <div class="card text-center h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-heartbeat fa-2x text-warning mb-3"></i>
                    <h3 class="counter" data-target="11">0</h3>
                    <p class="card-text">Rumah Sakit</p>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.footer')

<!-- JavaScript for counter animation and Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pastikan hamburger menu berfungsi
        let navbarToggler = document.querySelector('.navbar-toggler');
        if(navbarToggler) {
            // Memeriksa apakah tombol hamburger sudah memiliki event listener
            let navbarCollapse = document.getElementById('navbarNav');
            
            // Hapus event listener default dan tambahkan yang baru
            navbarToggler.addEventListener('click', function(e) {
                if(navbarCollapse) {
                    if(navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                    } else {
                        navbarCollapse.classList.add('show');
                    }
                }
                e.stopPropagation(); // Hentikan event propagasi
            });
        }
        
        // Counter animation
        const counters = document.querySelectorAll('.counter');
        const speed = 200;
        
        counters.forEach(counter => {
            const animate = () => {
                const value = +counter.innerText;
                const data = +counter.getAttribute('data-target');
                const time = data / speed;
                
                if (value < data) {
                    counter.innerText = Math.ceil(value + time);
                    setTimeout(animate, 1);
                } else {
                    counter.innerText = data;
                }
            }
            
            animate();
        });
    });
</script>
</body>
</html>