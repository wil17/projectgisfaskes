<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Data Puskesmas di Kota Banjarmasin</title>
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
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('map') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('data.layanan') }}">Data Layanan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Puskesmas</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 col-sm-12">
            <h1 class="mb-2"><i class="fas fa-hospital fa-2x text-danger"></i> Data Puskesmas</h1>
            <p class="lead text-muted">Informasi lengkap tentang puskesmas yang ada di Kota Banjarmasin</p>
        </div>
        <div class="col-md-6 col-sm-12 d-flex align-items-center justify-content-md-end gap-2 mt-3 mt-md-0">
            <!-- Lihat di Peta Button -->
            <a href="{{ route('map') }}?focus=puskesmas" class="btn btn-outline-primary">
                <i class="fas fa-map-marked-alt me-2"></i>Lihat di Peta
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow h-100 border-start border-danger border-5">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="fas fa-hospital fa-3x text-danger"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ $puskesmas->total() }}</h3>
                            <p class="text-muted mb-0">Total Puskesmas</p>
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
                    <h5 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>Cari Puskesmas</h5>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <span class="badge bg-primary rounded-pill">
                        <i class="fas fa-hospital me-1"></i>Total: {{ $puskesmas->total() }} Puskesmas
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3 mb-md-0">
                    <form action="{{ route('data.layanan.puskesmas') }}" method="GET" id="searchForm">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                placeholder="Cari berdasarkan nama atau alamat..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('data.layanan.puskesmas') }}" method="GET" id="kecamatanForm">
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

    <!-- Puskesmas Cards -->
    <div class="row">
        @forelse($puskesmas as $index => $item)
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100 hospital-card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                            <h5 class="mb-2 mb-sm-0 fw-bold text-danger">
                                <i class="fas fa-hospital fa-1x text-danger"></i>
                                {{ $item->nama ?? 'Puskesmas ' . $item->id }}
                            </h5>
                            <span class="badge bg-primary">{{ $item->kecamatan }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <!-- Responsive column structure for mobile -->
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <strong>Alamat:</strong>
                                </p>
                                <p class="ms-4 mb-2">{{ $item->alamat }}</p>
                                
                                <p class="mb-1">
                                    <i class="fas fa-location-dot text-success me-2"></i>
                                    <strong>Kelurahan:</strong>
                                </p>
                                <p class="ms-4 mb-0">{{ $item->kelurahan }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-user-md text-info me-2"></i>
                                    <strong>Kepala Puskesmas:</strong>
                                </p>
                                <p class="ms-4 mb-2">{{ $item->kepala_puskesmas ?? 'Data tidak tersedia' }}</p>
                                
                                <p class="mb-1">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <strong>Jam Operasional:</strong>
                                </p>
                                <p class="ms-4 mb-0">{{ $item->jam_operasional ?? 'Data tidak tersedia' }}</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                                <a href="{{ route('data.layanan.puskesmas.detail', $item->id) }}" class="btn btn-info w-100 w-sm-auto">
                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                </a>
                                <a href="{{ route('map') }}?focus=puskesmas&id={{ $item->id }}" class="btn btn-outline-primary w-100 w-sm-auto">
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
                        <h4>Tidak ada data puskesmas yang ditemukan</h4>
                        <p class="text-muted">Coba gunakan kata kunci pencarian yang berbeda</p>
                        <a href="{{ route('data.layanan.puskesmas') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-sync-alt me-1"></i> Reset Pencarian
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($puskesmas->hasPages())
        <div class="mt-4 pagination-container">
            {{ $puskesmas->links() }}
        </div>
    @endif
</div>

@include('partials.footer')

<!-- Script Section -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-submit when kecamatan selection changes
    document.getElementById('kecamatanSelect').addEventListener('change', function() {
        document.getElementById('kecamatanForm').submit();
    });
</script>
</body>
</html>