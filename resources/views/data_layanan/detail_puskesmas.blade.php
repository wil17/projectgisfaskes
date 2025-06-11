<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Puskesmas - {{ $puskesmas->nama_puskesmas }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="{{ asset('css/data-layanan.css') }}" rel="stylesheet">
    <style>
        .layanan-list li {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.75rem 0;
        }
        .layanan-list li:last-child {
            border-bottom: none;
        }
        .klaster-tab {
            border-radius: 0;
            padding: 1rem 1.5rem;
            font-weight: 500;
            color: #495057;
            transition: all 0.3s ease;
        }
        .klaster-tab.active {
            color: #fff;
            background-color: #dc3545;
            font-weight: 600;
        }
        .klaster-tab:hover:not(.active) {
            background-color: #f8f9fa;
        }
        .layanan-card {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        .layanan-card:hover {
            border-left: 3px solid #dc3545;
            transform: translateX(5px);
        }
        #mapPuskesmas {
            height: 400px;
            width: 100%;
            border-radius: 10px;
        }
        .detail-section {
            margin-bottom: 2rem;
        }
        .info-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 10px 10px 0 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .info-content {
            padding: 1rem;
            background-color: #fff;
            border-radius: 0 0 10px 10px;
        }
        .wilayah-item {
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 5px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .wilayah-item:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
        }
    </style>
</head>
<body>

@include('partials.header')

<div class="container mt-5 mb-5 fade-in">
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('map') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('data.layanan') }}">Data Layanan</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('data.layanan.puskesmas') }}">Puskesmas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $puskesmas->nama_puskesmas }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2 text-danger">
                <i class="fas fa-hospital me-2"></i>{{ $puskesmas->nama_puskesmas }}
            </h1>
            <p class="lead text-muted">{{ $puskesmas->alamat }}, {{ $puskesmas->kelurahan }}, {{ $puskesmas->kecamatan }}</p>
        </div>
        <div class="col-md-4 d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
            <a href="{{ route('map') }}?focus=puskesmas&id={{ $puskesmas->id_puskesmas }}" class="btn btn-primary">
                <i class="fas fa-map-marked-alt me-2"></i>Lihat di Peta
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column - Informasi Dasar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-danger">
                        <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <strong>Alamat Lengkap:</strong>
                        </p>
                        <p class="ms-4 mb-3">{{ $puskesmas->alamat }}</p>
                        
                        <p class="mb-1">
                            <i class="fas fa-user-md text-primary me-2"></i>
                            <strong>Kepala Puskesmas:</strong>
                        </p>
                        <p class="ms-4 mb-3">{{ $puskesmas->kepala_puskesmas ?? 'Data tidak tersedia' }}</p>
                        
                        <p class="mb-1">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <strong>Jam Operasional:</strong>
                        </p>
                        <p class="ms-4 mb-3">{{ $puskesmas->jam_operasional ?? 'Data tidak tersedia' }}</p>
                        
                        <p class="mb-1">
                            <i class="fas fa-city text-success me-2"></i>
                            <strong>Kota:</strong>
                        </p>
                        <p class="ms-4 mb-3">{{ $puskesmas->kota ?? 'Banjarmasin' }}</p>
                        
                        <p class="mb-1">
                            <i class="fas fa-map text-info me-2"></i>
                            <strong>Kecamatan:</strong>
                        </p>
                        <p class="ms-4 mb-3">{{ $puskesmas->kecamatan }}</p>
                        
                        <p class="mb-1">
                            <i class="fas fa-street-view text-secondary me-2"></i>
                            <strong>Kelurahan:</strong>
                        </p>
                        <p class="ms-4 mb-0">{{ $puskesmas->kelurahan }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Middle Column - Wilayah Kerja -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-success">
                        <i class="fas fa-map-marked me-2"></i>Wilayah Kerja
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($wilayahKerja) > 0)
                        <div class="mb-3">
                            <p class="mb-3 fw-bold">
                                <i class="fas fa-check-circle text-success me-2"></i>Kelurahan dalam Wilayah Kerja:
                            </p>
                            <div class="wilayah-list">
                                @foreach($wilayahKerja as $wilayah)
                                    <div class="wilayah-item">
                                        <i class="fas fa-map-pin text-danger me-2"></i>{{ $wilayah->kelurahan }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-map-marked fa-3x text-muted mb-3"></i>
                            <p>Tidak ada data wilayah kerja</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right Column - Map -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-map me-2"></i>Lokasi Puskesmas
                    </h5>
                </div>
                <div class="card-body">
                    <div id="mapPuskesmas" class="mb-3"></div>
                    @if($puskesmas->latitude && $puskesmas->longitude)
                        <div class="text-center mt-2">
                            <p class="mb-0 text-muted">
                                <i class="fas fa-location-arrow me-1"></i>Koordinat: {{ $puskesmas->latitude }}, {{ $puskesmas->longitude }}
                            </p>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-muted">Data koordinat tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Klaster dan Layanan Section -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-layer-group me-2"></i>Klaster dan Layanan
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($klaster) > 0)
                        <!-- Klaster Tabs -->
                        <ul class="nav nav-tabs mb-4" id="klasterTab" role="tablist">
                            @foreach($klaster as $index => $k)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link klaster-tab {{ $index === 0 ? 'active' : '' }}" 
                                            id="klaster-{{ $k->id_klaster }}-tab" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#klaster-{{ $k->id_klaster }}" 
                                            type="button" role="tab" 
                                            aria-controls="klaster-{{ $k->id_klaster }}" 
                                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                        <i class="fas fa-folder me-2"></i>{{ $k->nama_klaster }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="klasterTabContent">
                            @foreach($klaster as $index => $k)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                     id="klaster-{{ $k->id_klaster }}" role="tabpanel" 
                                     aria-labelledby="klaster-{{ $k->id_klaster }}-tab">
                                    
                                    <!-- Klaster Info -->
                                    <div class="mb-4">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h5 class="mb-3">{{ $k->nama_klaster }}</h5>
                                                <p class="mb-1">
                                                    <i class="fas fa-user-tie text-primary me-2"></i>
                                                    <strong>Penanggung Jawab:</strong> {{ $k->penanggung_jawab ?? 'Data tidak tersedia' }}
                                                </p>
                                                <p class="mb-3">
                                                    <i class="fas fa-hashtag text-secondary me-2"></i>
                                                    <strong>Kode Klaster:</strong> {{ $k->kode_klaster }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 text-md-end">
                                                <span class="badge bg-danger py-2 px-3">
                                                    <i class="fas fa-clipboard-list me-1"></i>
                                                    {{ isset($layananPerKlaster[$k->id_klaster]) ? count($layananPerKlaster[$k->id_klaster]) : 0 }} Layanan
                                                </span>
                                            </div>
                                        </div>
                                        <hr class="my-3">
                                    </div>
                                    
                                    <!-- Layanan List -->
                                    @if(isset($layananPerKlaster[$k->id_klaster]) && count($layananPerKlaster[$k->id_klaster]) > 0)
                                        <div class="row">
                                            @foreach($layananPerKlaster[$k->id_klaster] as $layanan)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card layanan-card h-100">
                                                        <div class="card-body">
                                                            <h6 class="card-title text-danger">
                                                                <i class="fas fa-heartbeat me-2"></i>{{ $layanan->nama_layanan }}
                                                            </h6>
                                                            <p class="card-text text-muted small">
                                                                {{ $layanan->deskripsi_layanan ?? 'Tidak ada deskripsi' }}
                                                            </p>
                                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                                <span class="badge bg-info">
                                                                    <i class="fas fa-users me-1"></i>{{ $layanan->jumlah_petugas }} Petugas
                                                                </span>
                                                                <span class="text-muted small">ID: {{ $layanan->id_layanan }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <p>Tidak ada data layanan untuk klaster ini</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-layer-group fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Tidak ada data klaster yang tersedia</h4>
                            <p class="text-muted">Silakan hubungi Puskesmas untuk informasi lebih lanjut</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back Button -->
    <div class="row">
        <div class="col-12 text-center">
            <a href="{{ route('data.layanan.puskesmas') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Puskesmas
            </a>
        </div>
    </div>
</div>

@include('partials.footer')

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map if coordinates are available
        const latitude = {{ $puskesmas->latitude ?? 'null' }};
        const longitude = {{ $puskesmas->longitude ?? 'null' }};
        
        if (latitude && longitude) {
            const map = L.map('mapPuskesmas').setView([latitude, longitude], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            const marker = L.marker([latitude, longitude]).addTo(map)
                .bindPopup(`<b>{{ $puskesmas->nama_puskesmas }}</b><br>{{ $puskesmas->alamat }}`).openPopup();
        } else {
            document.getElementById('mapPuskesmas').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-map-marked fa-4x text-muted mb-3"></i>
                    <p>Tidak ada data koordinat untuk menampilkan peta</p>
                </div>
            `;
        }
    });
</script>
</body>
</html>