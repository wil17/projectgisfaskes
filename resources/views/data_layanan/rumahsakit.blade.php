<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Data Rumah Sakit di Kota Banjarmasin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-minimap/3.6.0/Control.MiniMap.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.min.css" />
    <link href="{{ asset('css/data-layanan.css') }}" rel="stylesheet">
    <style>
        /* Tambahan style untuk card poliklinik dan dokter */
        .poli-dokter-card {
            border-left: 4px solid #4285f4;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .poli-dokter-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .poli-dokter-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 500;
        }
        .dokter-list {
            padding: 10px 15px;
        }
        .dokter-item {
            display: flex;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.05);
        }
        .dokter-item:last-child {
            border-bottom: none;
        }
        .dokter-icon {
            width: 30px;
            height: 30px;
            background-color: #e6f2ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .dokter-icon i {
            color: #4285f4;
            font-size: 0.9rem;
        }
        .poliklinik-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            background-color: #e6f2ff;
            color: #4285f4;
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .no-dokter-info {
            color: #6c757d;
            font-style: italic;
            padding: 5px 0;
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
                    <li class="breadcrumb-item active" aria-current="page">Rumah Sakit</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 col-sm-12">
            <h1 class="mb-2"><i class="fas fa-heartbeat fa-2x text-warning"></i>Data Rumah Sakit</h1>
            <p class="lead text-muted">Informasi lengkap tentang rumah sakit yang ada di Kota Banjarmasin</p>
        </div>
        <div class="col-md-4 col-sm-12 d-flex align-items-center justify-content-md-end gap-2 mt-3 mt-md-0">
            <!-- Lihat di Peta Button -->
            <a href="{{ route('map') }}?focus=rumahsakit" class="btn btn-outline-primary">
                <i class="fas fa-map-marked-alt me-2"></i>Lihat di Peta
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow h-100 border-start border-primary border-5">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="fas fa-heartbeat fa-3x text-warning"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ $rumahSakit->total() }}</h3>
                            <p class="text-muted mb-0">Total Rumah Sakit</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow h-100 border-start border-success border-5">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 text-center">
                                <i class="fas fa-stethoscope fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ $kecamatanList->count() }}</h3>
                            <p class="text-muted mb-0">Kecamatan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow h-100 border-start border-info border-5">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 text-center">
                                <i class="fas fa-user-md fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">
                                @if(request('kecamatan'))
                                    {{ request('kecamatan') }}
                                @else
                                    Banjarmasin
                                @endif
                            </h3>
                            <p class="text-muted mb-0">Area Tampilan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>Cari Rumah Sakit</h5>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <span class="badge bg-primary rounded-pill">
                        <i class="fas fa-hospital me-1"></i>Total: {{ $rumahSakit->total() }} Rumah Sakit
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3 mb-md-0">
                    <form action="{{ route('data.layanan.rumahsakit') }}" method="GET" id="searchForm">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                placeholder="Cari berdasarkan nama, alamat, poliklinik, atau dokter..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('data.layanan.rumahsakit') }}" method="GET" id="kecamatanForm">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                            </span>
                            <select name="kecamatan" id="kecamatanSelect" class="form-select border-start-0">
                                <option value="">Semua Kecamatan</option>
                                @foreach($kecamatanList as $kecamatan)
                                    <option value="{{ $kecamatan }}" {{ request('kecamatan') == $kecamatan ? 'selected' : '' }}>
                                        {{ $kecamatan }}
                                    </option>
                                @endforeach
                            </select>
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hospitals Cards -->
    <div class="row">
        @forelse($rumahSakit as $index => $item)
            <div class="col-12 mb-4">
                <div class="card shadow h-100 hospital-card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                            <h5 class="mb-2 mb-sm-0 fw-bold text-primary">
                                <i class="fas fa-heartbeat fa-1x text-warning me-2"></i>
                                {{ $item->nama_rs }}
                            </h5>
                            <span class="badge bg-primary">{{ $item->kecamatan }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <!-- Informasi umum rumah sakit -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Umum</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                            <strong>Alamat:</strong>
                                            <span class="ms-1">{{ $item->alamat }}</span>
                                        </p>
                                        
                                        <p class="mb-2">
                                            <i class="fas fa-city text-success me-2"></i>
                                            <strong>Kota:</strong>
                                            <span class="ms-1">{{ $item->kota ?? 'Banjarmasin' }}</span>
                                        </p>
                                        
                                        <p class="mb-2">
                                            <i class="fas fa-map text-primary me-2"></i>
                                            <strong>Kecamatan:</strong>
                                            <span class="ms-1">{{ $item->kecamatan }}</span>
                                        </p>
                                        
                                        <p class="mb-0">
                                            <i class="fas fa-street-view text-info me-2"></i>
                                            <strong>Kelurahan:</strong>
                                            <span class="ms-1">{{ $item->kelurahan }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Poliklinik dan Dokter -->
                            <div class="col-md-8">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="fas fa-stethoscope me-2 text-success"></i>Poliklinik & Dokter</h6>
                                    </div>
                                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                        @php
                                            // Parse data poliklinik dan dokter dari accessor poliklinik_dokter_array
                                            $poliDokterMap = $item->poliklinik_dokter_array;
                                        @endphp

                                        @if(count($poliDokterMap) > 0)
                                            @foreach($poliDokterMap as $poli => $dokterList)
                                                <div class="poli-dokter-card">
                                                    <div class="poli-dokter-header">
                                                        <i class="fas fa-circle-check text-success me-2"></i>
                                                        {{ $poli }}
                                                    </div>
                                                    <div class="dokter-list">
                                                        @if(count($dokterList) > 0)
                                                            @foreach($dokterList as $dokter)
                                                                <div class="dokter-item">
                                                                    <div class="dokter-icon">
                                                                        <i class="fas fa-user-md"></i>
                                                                    </div>
                                                                    <div>{{ $dokter }}</div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="no-dokter-info">
                                                                <i class="fas fa-info-circle me-1"></i> Data dokter tidak tersedia
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="p-3 text-center text-muted">
                                                <i class="fas fa-info-circle me-1"></i> 
                                                Tidak ada data poliklinik yang tersedia
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                                <button type="button" class="btn btn-info view-detail w-100 w-sm-auto" 
                                        data-id="{{ $item->id_rs }}"
                                        data-nama="{{ $item->nama_rs }}"
                                        data-alamat="{{ $item->alamat }}"
                                        data-poliklinik-dokter="{{ json_encode($item->poliklinik_dokter_array) }}"
                                        data-kota="{{ $item->kota ?? 'Banjarmasin' }}"
                                        data-kecamatan="{{ $item->kecamatan }}"
                                        data-kelurahan="{{ $item->kelurahan }}"
                                        data-long="{{ $item->longitude }}"
                                        data-lat="{{ $item->latitude }}">
                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                </button>
                                <a href="{{ route('map') }}?focus=rumahsakit&id={{ $item->id_rs }}" class="btn btn-outline-primary w-100 w-sm-auto">
                                    <i class="fas fa-map-marker-alt me-1"></i> Lihat di Peta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h4>Tidak ada data rumah sakit yang ditemukan</h4>
                        <p class="text-muted">Coba gunakan kata kunci pencarian yang berbeda</p>
                        <a href="{{ route('data.layanan.rumahsakit') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-sync-alt me-1"></i> Reset Pencarian
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($rumahSakit->hasPages())
        <div class="mt-4 pagination-container">
            {{ $rumahSakit->links() }}
        </div>
    @endif
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-hospital-alt me-2"></i>Detail Rumah Sakit
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 id="modal-nama" class="text-center text-primary mb-3"></h4>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Umum</h6>
                            </div>
                            <div class="card-body">
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-map-marker-alt text-danger me-2"></i>Alamat</div>
                                    <div id="modal-alamat" class="ps-4 pt-1 pb-2"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-city text-primary me-2"></i>Kota</div>
                                    <div id="modal-kota" class="ps-4 pt-1 pb-2"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-map text-success me-2"></i>Kecamatan</div>
                                    <div id="modal-kecamatan" class="ps-4 pt-1 pb-2"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-street-view text-info me-2"></i>Kelurahan</div>
                                    <div id="modal-kelurahan" class="ps-4 pt-1 pb-2"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label"><i class="fas fa-location-arrow text-warning me-2"></i>Koordinat</div>
                                    <div id="modal-koordinat" class="ps-4 pt-1 pb-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="poliklinik-tab" data-bs-toggle="tab" 
                                        data-bs-target="#poliklinik-tab-pane" type="button" role="tab" 
                                        aria-controls="poliklinik-tab-pane" aria-selected="true">
                                    <i class="fas fa-stethoscope me-1"></i> Poliklinik
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="dokter-tab" data-bs-toggle="tab" 
                                        data-bs-target="#dokter-tab-pane" type="button" role="tab" 
                                        aria-controls="dokter-tab-pane" aria-selected="false">
                                    <i class="fas fa-user-md me-1"></i> Dokter
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="poliklinik-tab-pane" role="tabpanel" 
                                 aria-labelledby="poliklinik-tab" tabindex="0">
                                <div class="card card-body border-top-0 rounded-0 rounded-bottom">
                                    <div id="modal-poliklinik-list" class="overflow-auto" style="max-height: 250px;"></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="dokter-tab-pane" role="tabpanel" 
                                 aria-labelledby="dokter-tab" tabindex="0">
                                <div class="card card-body border-top-0 rounded-0 rounded-bottom">
                                    <div id="modal-dokter-list" class="overflow-auto" style="max-height: 250px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mini Map -->
                <div id="modalMap" class="rounded shadow-sm border"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <a href="#" id="lihatPeta" class="btn btn-primary">
                    <i class="fas fa-map-marked-alt me-1"></i>Lihat di Peta Utama
                </a>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

<!-- Script Section -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Auto-submit when kecamatan selection changes
    document.getElementById('kecamatanSelect').addEventListener('change', function() {
        document.getElementById('kecamatanForm').submit();
    });
    
    // Modal detail functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Get all detail buttons
        const detailButtons = document.querySelectorAll('.view-detail');
        let map = null;
        let marker = null;
        
        detailButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get data from button attributes
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const alamat = this.getAttribute('data-alamat');
                const poliklinikDokter = this.getAttribute('data-poliklinik-dokter');
                const kota = this.getAttribute('data-kota');
                const kecamatan = this.getAttribute('data-kecamatan');
                const kelurahan = this.getAttribute('data-kelurahan');
                const longitude = this.getAttribute('data-long');
                const latitude = this.getAttribute('data-lat');
                
                // Set data to modal elements
                document.getElementById('modal-nama').textContent = nama;
                document.getElementById('modal-alamat').textContent = alamat || 'Tidak ada data';
                document.getElementById('modal-kota').textContent = kota || 'Tidak ada data';
                document.getElementById('modal-kecamatan').textContent = kecamatan || 'Tidak ada data';
                document.getElementById('modal-kelurahan').textContent = kelurahan || 'Tidak ada data';
                document.getElementById('modal-koordinat').textContent = 
                    (longitude && latitude && longitude !== 'null' && latitude !== 'null') ? 
                    `${latitude}, ${longitude}` : 'Tidak ada data';
                
                // Parse poliklinik_dokter data
                const poliklinikList = document.getElementById('modal-poliklinik-list');
                const dokterList = document.getElementById('modal-dokter-list');
                
                // Clear previous content
                poliklinikList.innerHTML = '';
                dokterList.innerHTML = '';
                
                if (poliklinikDokter && poliklinikDokter !== 'null') {
                    try {
                        // Parse the JSON string back to object
                        const poliDokterData = JSON.parse(poliklinikDokter);
                        
                        // Generate HTML for polikliniks
                        if (Object.keys(poliDokterData).length > 0) {
                            let poliHtml = '<ul class="list-group list-group-flush">';
                            Object.keys(poliDokterData).forEach(poli => {
                                poliHtml += `
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="fas fa-circle-check text-success me-2"></i>
                                        <span>${poli}</span>
                                    </li>
                                `;
                            });
                            poliHtml += '</ul>';
                            poliklinikList.innerHTML = poliHtml;
                            
                            // Generate HTML for dokters
                            let dokterHtml = '<ul class="list-group list-group-flush">';
                            let allDokters = [];
                            
                            // Collect all doctors from all polyclinics
                            Object.values(poliDokterData).forEach(dokters => {
                                dokters.forEach(dokter => {
                                    if (!allDokters.includes(dokter)) {
                                        allDokters.push(dokter);
                                    }
                                });
                            });
                            
                            if (allDokters.length > 0) {
                                allDokters.forEach(dokter => {
                                    dokterHtml += `
                                        <li class="list-group-item d-flex align-items-center">
                                            <i class="fas fa-user-md text-primary me-2"></i>
                                            <span>${dokter}</span>
                                        </li>
                                    `;
                                });
                            } else {
                                dokterHtml = '<p class="text-center text-muted my-3">Tidak ada data dokter</p>';
                            }
                            dokterHtml += '</ul>';
                            dokterList.innerHTML = dokterHtml;
                        } else {
                            poliklinikList.innerHTML = '<p class="text-center text-muted my-3">Tidak ada data poliklinik</p>';
                            dokterList.innerHTML = '<p class="text-center text-muted my-3">Tidak ada data dokter</p>';
                        }
                    } catch (e) {
                        console.error("Error parsing poliklinik_dokter data:", e);
                        poliklinikList.innerHTML = '<p class="text-center text-muted my-3">Error memproses data poliklinik</p>';
                        dokterList.innerHTML = '<p class="text-center text-muted my-3">Error memproses data dokter</p>';
                    }
                } else {
                    poliklinikList.innerHTML = '<p class="text-center text-muted my-3">Tidak ada data poliklinik</p>';
                    dokterList.innerHTML = '<p class="text-center text-muted my-3">Tidak ada data dokter</p>';
                }
                
                // Set link to map
                document.getElementById('lihatPeta').href = `{{ route('map') }}?focus=rumahsakit&id=${id}`;
                
                // Initialize modal map
                const hasCoordinates = longitude && latitude && longitude !== 'null' && latitude !== 'null';
                
                if (hasCoordinates) {
                    // Initialize map after modal is shown
                    const detailModal = document.getElementById('detailModal');
                    detailModal.addEventListener('shown.bs.modal', function() {
                        if (map) {
                            map.remove();
                        }
                        
                        map = L.map('modalMap').setView([latitude, longitude], 15);
                        
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);
                        
                        marker = L.marker([latitude, longitude]).addTo(map)
                            .bindPopup(`<b>${nama}</b><br>${alamat}`).openPopup();
                        
                        // Fix map display issue by invalidating size
                        setTimeout(function() {
                            map.invalidateSize();
                        }, 100);
                    });
                } else {
                    // If no coordinates, hide the map container
                    document.getElementById('modalMap').style.display = 'none';
                }
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                modal.show();
            });
        });
        
        // Fix map display issue when modal reopened
        document.getElementById('detailModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('modalMap').style.display = 'block';
        });
    });
</script>
</body>
</html>