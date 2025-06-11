@extends('layouts.admin')

@section('page-title', 'Edit Puskesmas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.index') }}">Manajemen Puskesmas</a></li>
    <li class="breadcrumb-item active">Edit Puskesmas</li>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminapotek.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e1e5e9;
        border-radius: 10px;
        padding: 6px 8px;
        min-height: 42px;
    }
    .select2-container--default .select2-selection--multiple:focus {
        border-color: #667eea;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #667eea;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #667eea;
        border: none;
        color: white;
        border-radius: 4px;
        padding: 3px 8px;
        margin-top: 4px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    
    .feature-card {
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        background: white;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .feature-card .icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 20px;
    }
    
    .feature-card h6 {
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .feature-card p {
        flex-grow: 1;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="apotek-form-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-card">
                    <div class="form-header">
                        <h1><i class="fas fa-edit"></i> Edit Puskesmas</h1>
                        <p class="subtitle">Perbarui informasi puskesmas {{ $puskesmas->nama_puskesmas }}</p>
                    </div>
                    
                    <div class="form-body">
                        @if ($errors->any())
                            <div class="alert alert-danger fade show">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.puskesmas.update', $puskesmas->id_puskesmas) }}" method="POST" id="editPuskesmasForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="nama_puskesmas" class="form-label">Nama Puskesmas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_puskesmas" name="nama_puskesmas" 
                                       value="{{ old('nama_puskesmas', $puskesmas->nama_puskesmas) }}" required placeholder="Masukkan nama puskesmas">
                            </div>
                            
                            <div class="form-group">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required placeholder="Masukkan alamat lengkap puskesmas">{{ old('alamat', $puskesmas->alamat) }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="kepala_puskesmas" class="form-label">Kepala Puskesmas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kepala_puskesmas" name="kepala_puskesmas" 
                                       value="{{ old('kepala_puskesmas', $puskesmas->kepala_puskesmas) }}" required placeholder="Masukkan nama kepala puskesmas">
                            </div>

                            <div class="form-group">
                                <label for="jam_operasional" class="form-label">Jam Operasional <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="jam_operasional" name="jam_operasional" 
                                       value="{{ old('jam_operasional', $puskesmas->jam_operasional) }}" required placeholder="Masukkan jam operasional">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kecamatan" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kecamatan" name="kecamatan" required>
                                            <option value="">Pilih Kecamatan</option>
                                            @foreach($kecamatans as $kecamatan)
                                                <option value="{{ $kecamatan }}" {{ old('kecamatan', $puskesmas->kecamatan) == $kecamatan ? 'selected' : '' }}>{{ $kecamatan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kelurahan" class="form-label">Kelurahan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kelurahan" name="kelurahan" required>
                                            <option value="">Pilih Kelurahan</option>
                                            @foreach($kelurahans as $kelurahan)
                                                <option value="{{ $kelurahan }}" {{ old('kelurahan', $puskesmas->kelurahan) == $kelurahan ? 'selected' : '' }}>{{ $kelurahan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kota" name="kota" 
                                               value="{{ old('kota', $puskesmas->kota ?? 'Banjarmasin') }}" required readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="wilayah_kerja" class="form-label">Wilayah Kerja (Kelurahan)</label>
                                <select class="form-select select2-multiple" id="wilayah_kerja" name="wilayah_kerja[]" multiple>
                                    @foreach($kelurahans as $kelurahan)
                                        <option value="{{ $kelurahan }}" {{ in_array($kelurahan, old('wilayah_kerja', $wilayahKerja)) ? 'selected' : '' }}>
                                            {{ $kelurahan }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih satu atau lebih kelurahan yang menjadi wilayah kerja puskesmas</small>
                            </div>
                            
                            <!-- Map Section -->
                            <div class="map-section">
                                <h6><i class="fas fa-map-marker-alt"></i> Lokasi Puskesmas</h6>
                                
                                <div class="map-instruction">
                                    <p><i class="fas fa-hand-pointer"></i> Klik pada peta untuk mengubah lokasi puskesmas</p>
                                </div>
                                
                                <div id="map-container"></div>
                                
                                <div class="coordinate-display has-coordinates" id="coordinateDisplay">
                                    <p class="coordinate-text" id="coordinateText">
                                        @if($puskesmas->latitude && $puskesmas->longitude)
                                            <i class="fas fa-check-circle"></i> 
                                            Koordinat saat ini: {{ $puskesmas->latitude }}, {{ $puskesmas->longitude }}
                                        @else
                                            Belum ada koordinat dipilih
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="latitude" name="latitude" 
                                                   value="{{ old('latitude', $puskesmas->latitude) }}" required placeholder="Masukkan latitude koordinat">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="longitude" name="longitude" 
                                                   value="{{ old('longitude', $puskesmas->longitude) }}" required placeholder="Masukkan longitude koordinat">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <a href="{{ route('admin.puskesmas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Pengaturan Tambahan -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0 text-primary">
                                    <i class="fas fa-cogs me-2"></i> Pengaturan Tambahan
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="icon-wrapper bg-primary">
                                                <i class="fas fa-layer-group text-white"></i>
                                            </div>
                                            <h6>Kelola Klaster</h6>
                                            <p class="text-muted small">Mengelola klaster dan penanggung jawab setiap klaster pada puskesmas</p>
                                            <a href="{{ route('admin.puskesmas.klaster.index', $puskesmas->id_puskesmas) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-arrow-right me-1"></i> Kelola
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="icon-wrapper bg-warning">
                                                <i class="fas fa-map-marked-alt text-white"></i>
                                            </div>
                                            <h6>Peta Lokasi</h6>
                                            <p class="text-muted small">Melihat lokasi puskesmas pada peta</p>
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#mapModal">
                                                <i class="fas fa-map-marker-alt me-1"></i> Lihat Peta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="mapModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i> Lokasi {{ $puskesmas->nama_puskesmas }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-map-container" style="height: 400px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let map;
    let marker;
    
    $(document).ready(function() {
        // Initialize Select2 for wilayah kerja
        $('.select2-multiple').select2({
            placeholder: "Pilih wilayah kerja...",
            allowClear: true,
            width: '100%'
        });
        
        // Initialize map with existing coordinates
        initializeMap();
        
        // Add event listeners for manual coordinate input
        $('#latitude, #longitude').on('change', function() {
            updateMapFromInputs();
        });
    });
    
    function initializeMap() {
        // Get existing coordinates or use default
        const currentLat = parseFloat('{{ $puskesmas->latitude }}') || -3.3194374;
        const currentLng = parseFloat('{{ $puskesmas->longitude }}') || 114.5900474;
        
        // Initialize map with current location
        map = L.map('map-container').setView([currentLat, currentLng], 15);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add marker if coordinates exist
        if ('{{ $puskesmas->latitude }}' && '{{ $puskesmas->longitude }}') {
            marker = L.marker([currentLat, currentLng]).addTo(map);
        }
        
        // Add click event to map
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            $('#latitude').val(lat.toFixed(6));
            $('#longitude').val(lng.toFixed(6));
            
            // Update coordinate display
            updateCoordinateDisplay(lat, lng);
            
            // Add or update marker
            updateMarker(lat, lng);
        });
    }
    
    function updateMapFromInputs() {
        const lat = parseFloat($('#latitude').val());
        const lng = parseFloat($('#longitude').val());
        
        if (!isNaN(lat) && !isNaN(lng)) {
            // Update the map view
            map.setView([lat, lng], 15);
            
            // Add or update marker
            updateMarker(lat, lng);
            
            // Update coordinate display
            updateCoordinateDisplay(lat, lng);
        }
    }
    
    function updateMarker(lat, lng) {
        // Add or update marker
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
    }
    
    function updateCoordinateDisplay(lat, lng) {
        const coordinateDisplay = $('#coordinateDisplay');
        const coordinateText = $('#coordinateText');
        
        coordinateDisplay.addClass('has-coordinates');
        coordinateText.html(`
            <i class="fas fa-check-circle"></i> 
            Koordinat dipilih: ${lat.toFixed(6)}, ${lng.toFixed(6)}
        `);
    }
    
    // Initialize modal map
    $('#mapModal').on('shown.bs.modal', function () {
        const lat = parseFloat('{{ $puskesmas->latitude }}') || -3.3194374;
        const lng = parseFloat('{{ $puskesmas->longitude }}') || 114.5900474;
        
        const modalMap = L.map('modal-map-container').setView([lat, lng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(modalMap);
        
        L.marker([lat, lng]).addTo(modalMap)
            .bindPopup('{{ $puskesmas->nama_puskesmas }}')
            .openPopup();
            
        setTimeout(function() {
            modalMap.invalidateSize();
        }, 100);
    });

    $('#mapModal').on('hidden.bs.modal', function () {
        // Clean up map instance when modal is closed
        $('#modal-map-container').empty();
    });
</script>
@endsection