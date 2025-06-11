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
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('map') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('data.layanan') }}">Data Layanan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Klinik</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 col-sm-12">
            <h1 class="mb-2">Data Klinik di Kota Banjarmasin</h1>
            <p class="lead text-muted">Informasi lengkap tentang klinik yang ada di Kota Banjarmasin</p>
        </div>
        <div class="col-md-4 col-sm-12 d-flex align-items-center justify-content-md-end gap-2 mt-3 mt-md-0">
            <!-- Lihat di Peta Button -->
            <a href="{{ route('map') }}?focus=klinik" class="btn btn-outline-success">
                <i class="fas fa-map-marked-alt me-2"></i>Lihat di Peta
            </a>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="fas fa-search me-2 text-success"></i>Cari Klinik</h5>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <span class="badge bg-success rounded-pill">
                        <i class="fas fa-clinic-medical me-1"></i>Total: {{ $klinik->total() }} Klinik
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3 mb-md-0">
                    <form action="{{ route('data.layanan.klinik') }}" method="GET" id="searchForm">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="fas fa-search text-success"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                placeholder="Cari berdasarkan nama atau alamat..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-success">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('data.layanan.klinik') }}" method="GET" id="kecamatanForm">
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

    <!-- Data Table Card -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 responsive-card-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>
                                <a href="{{ route('data.layanan.klinik', ['sort' => 'nama_klinik', 'direction' => request('sort') == 'nama_klinik' && request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'kecamatan' => request('kecamatan')]) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    Nama Klinik
                                    @if(request('sort') == 'nama_klinik')
                                        @if(request('direction') == 'asc')
                                            <i class="fas fa-sort-up ms-1 text-success"></i>
                                        @else
                                            <i class="fas fa-sort-down ms-1 text-success"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Alamat</th>
                            <th>
                                <a href="{{ route('data.layanan.klinik', ['sort' => 'kecamatan', 'direction' => request('sort') == 'kecamatan' && request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'kecamatan' => request('kecamatan')]) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    Kecamatan
                                    @if(request('sort') == 'kecamatan')
                                        @if(request('direction') == 'asc')
                                            <i class="fas fa-sort-up ms-1 text-success"></i>
                                        @else
                                            <i class="fas fa-sort-down ms-1 text-success"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Skala Usaha</th>
                            <th class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($klinik as $index => $item)
                            <tr class="focus-trigger">
                                <!-- Header row untuk mobile - hanya tampil di mobile -->
                                <div class="card-header-row d-md-none">
                                    <div class="number-badge">{{ $klinik->firstItem() + $index }}</div>
                                    <div class="fw-medium">{{ $item->nama_klinik ?? 'Klinik ' . $item->id_klinik }}</div>
                                </div>
                                
                                <!-- Baris data normal -->
                                <td data-label="No" class="d-none d-md-table-cell">{{ $klinik->firstItem() + $index }}</td>
                                <td data-label="Nama Klinik">
                                    <div class="cell-content focus-target">{{ $item->nama_klinik ?? 'Klinik ' . $item->id_klinik }}</div>
                                </td>
                                <td data-label="Alamat">
                                    <div class="cell-content"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $item->alamat }}</div>
                                </td>
                                <td data-label="Kecamatan">
                                    <div class="cell-content">{{ $item->kecamatan }}</div>
                                </td>
                                <td data-label="Skala Usaha">
                                    <div class="cell-content">
                                        @if($item->skala_usaha == 'Besar')
                                            <span class="badge bg-success">{{ $item->skala_usaha }}</span>
                                        @elseif($item->skala_usaha == 'Menengah')
                                            <span class="badge bg-primary">{{ $item->skala_usaha }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $item->skala_usaha }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Detail" class="text-center">
                                    <button type="button" class="btn btn-sm btn-info view-detail" 
                                            data-id="{{ $item->id_klinik }}"
                                            data-nama="{{ $item->nama_klinik ?? 'Klinik ' . $item->id_klinik }}"
                                            data-alamat="{{ $item->alamat }}"
                                            data-kota="{{ $item->kota }}"
                                            data-kecamatan="{{ $item->kecamatan }}"
                                            data-kelurahan="{{ $item->kelurahan }}"
                                            data-skala="{{ $item->skala_usaha }}"
                                            data-berdiri="{{ $item->tgl_berdiri }}"
                                            data-tenaga="{{ $item->tenaga_kerja }}"
                                            data-long="{{ $item->longitude }}"
                                            data-lat="{{ $item->latitude }}">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="py-3">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5>Tidak ada data klinik yang ditemukan</h5>
                                        <p class="text-muted">Coba gunakan kata kunci pencarian yang berbeda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($klinik->hasPages())
        <div class="mt-4 pagination-container">
            {{ $klinik->links() }}
        </div>
    @endif
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Klinik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-row">
                            <div class="detail-label">Nama Klinik</div>
                            <div id="modal-nama" class="fw-medium fs-5"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Alamat</div>
                            <div id="modal-alamat"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Kota</div>
                            <div id="modal-kota"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Kecamatan</div>
                            <div id="modal-kecamatan"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Kelurahan</div>
                            <div id="modal-kelurahan"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-row">
                            <div class="detail-label">Skala Usaha</div>
                            <div id="modal-skala"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Tanggal Berdiri</div>
                            <div id="modal-berdiri"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Jumlah Tenaga Kerja</div>
                            <div id="modal-tenaga"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Koordinat</div>
                            <div id="modal-koordinat"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Mini Map -->
                <div id="modalMap"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="lihatPeta" class="btn btn-success">
                    <i class="fas fa-map-marked-alt me-2"></i>Lihat di Peta Utama
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
                const kota = this.getAttribute('data-kota');
                const kecamatan = this.getAttribute('data-kecamatan');
                const kelurahan = this.getAttribute('data-kelurahan');
                const skala = this.getAttribute('data-skala');
                const berdiri = this.getAttribute('data-berdiri');
                const tenaga = this.getAttribute('data-tenaga');
                const longitude = this.getAttribute('data-long');
                const latitude = this.getAttribute('data-lat');
                
                // Set data to modal elements
                document.getElementById('modal-nama').textContent = nama;
                document.getElementById('modal-alamat').textContent = alamat || 'Tidak ada data';
                document.getElementById('modal-kota').textContent = kota || 'Tidak ada data';
                document.getElementById('modal-kecamatan').textContent = kecamatan || 'Tidak ada data';
                document.getElementById('modal-kelurahan').textContent = kelurahan || 'Tidak ada data';
                
                // Set badge for skala usaha
                let skalaBadge = '';
                if (skala === 'Besar') {
                    skalaBadge = '<span class="badge bg-success">' + skala + '</span>';
                } else if (skala === 'Menengah') {
                    skalaBadge = '<span class="badge bg-primary">' + skala + '</span>';
                } else {
                    skalaBadge = '<span class="badge bg-warning text-dark">' + skala + '</span>';
                }
                document.getElementById('modal-skala').innerHTML = skalaBadge;
                
                document.getElementById('modal-berdiri').textContent = berdiri || 'Tidak ada data';
                document.getElementById('modal-tenaga').textContent = tenaga || 'Tidak ada data';
                document.getElementById('modal-koordinat').textContent = 
                    (longitude && latitude && longitude !== 'null' && latitude !== 'null') ? 
                    `${latitude}, ${longitude}` : 'Tidak ada data';
                
                // Set link to map
                document.getElementById('lihatPeta').href = `{{ route('map') }}?focus=klinik&id=${id}`;
                
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