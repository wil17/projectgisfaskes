@extends('layouts.admin')

@section('page-title', 'Tambah Rumah Sakit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.rumahsakit.index') }}">Manajemen Rumah Sakit</a></li>
    <li class="breadcrumb-item active">Tambah Rumah Sakit</li>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminapotek.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    /* Loading indicator style */
    .loading-overlay {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border-left-color: #09f;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    #map-container {
        position: relative;
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .coordinate-display {
        background-color: #f8f9fa;
        padding: 8px 15px;
        border-radius: 6px;
        margin: 10px 0;
        border: 1px solid #dee2e6;
    }
    
    .coordinate-display.has-coordinates {
        background-color: #e8f4ff;
        border-color: #b8daff;
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
                        <h1><i class="fas fa-hospital-alt"></i> Tambah Rumah Sakit Baru</h1>
                        <p class="subtitle">Isi informasi rumah sakit dengan lengkap dan akurat</p>
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

                        <form action="{{ route('admin.rumahsakit.store') }}" method="POST" id="createRumahSakitForm">
                            @csrf
                            
                            <div class="form-group">
                                <label for="nama_rs" class="form-label">Nama Rumah Sakit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_rs" name="nama_rs" 
                                       value="{{ old('nama_rs') }}" required placeholder="Masukkan nama rumah sakit">
                            </div>
                            
                            <div class="form-group">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required placeholder="Masukkan alamat lengkap rumah sakit">{{ old('alamat') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="poliklinik_dokter" class="form-label">Poliklinik & Dokter <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="poliklinik_dokter" name="poliklinik_dokter" rows="5" required placeholder="Masukkan daftar poliklinik dan dokter">{{ old('poliklinik_dokter') }}</textarea>
                                <small class="text-muted">Format: Nama Poliklinik: Dokter1, Dokter2; Nama Poliklinik2: Dokter3, Dokter4;</small>
                                <small class="d-block text-muted">Contoh: Poli Jantung: dr. Putri Kusuma Dewi Sp.JP, dr. Dinarsari Hayuning; Poli Gigi: drg. Ahmad, drg. Budi;</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kecamatan" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kecamatan" name="kecamatan" required>
                                            <option value="">Pilih Kecamatan</option>
                                            @foreach($kecamatans as $kecamatan)
                                                <option value="{{ $kecamatan }}" {{ old('kecamatan') == $kecamatan ? 'selected' : '' }}>{{ $kecamatan }}</option>
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
                                                <option value="{{ $kelurahan }}" {{ old('kelurahan') == $kelurahan ? 'selected' : '' }}>{{ $kelurahan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kota" name="kota" 
                                               value="{{ old('kota', 'Banjarmasin') }}" required readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Map Section -->
                            <div class="map-section">
                                <h6><i class="fas fa-map-marker-alt"></i> Lokasi Rumah Sakit</h6>
                                
                                <div class="map-instruction">
                                    <p><i class="fas fa-info-circle"></i> Peta akan otomatis menunjukkan lokasi sesuai kecamatan dan kelurahan yang dipilih</p>
                                    <p><i class="fas fa-hand-pointer"></i> Anda juga dapat mengklik pada peta untuk menentukan lokasi yang lebih spesifik</p>
                                </div>
                                
                                <div id="map-container">
                                    <div class="loading-overlay" id="mapLoading">
                                        <div class="spinner"></div>
                                    </div>
                                </div>
                                
                                <div class="coordinate-display" id="coordinateDisplay">
                                    <p class="coordinate-text" id="coordinateText">Belum ada koordinat dipilih</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="latitude" name="latitude" 
                                                   value="{{ old('latitude') }}" required placeholder="Latitude koordinat">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="longitude" name="longitude" 
                                                   value="{{ old('longitude') }}" required placeholder="Longitude koordinat">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <a href="{{ route('admin.rumahsakit.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Rumah Sakit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    let map;
    let marker;
    // Koordinat untuk kecamatan di Banjarmasin (dari GeoJSON)
    const kecamatanCoordinates = {
        'Banjarmasin Barat': [-3.3259, 114.5855],
        'Banjarmasin Selatan': [-3.3364, 114.5900],
        'Banjarmasin Tengah': [-3.3194, 114.5900],
        'Banjarmasin Timur': [-3.3167, 114.6091],
        'Banjarmasin Utara': [-3.2923, 114.5900]
    };
    
    // Koordinat untuk kelurahan di Banjarmasin (sebagian, dari GeoJSON)
    const kelurahanCoordinates = {
        // Banjarmasin Utara
        'Alalak Selatan': [-3.2823, 114.5725],
        'Alalak Tengah': [-3.2748, 114.5783],
        'Alalak Utara': [-3.2665, 114.5829],
        // Banjarmasin Barat
        'Pelambuan': [-3.3281, 114.5746],
        'Telaga Biru': [-3.3259, 114.5795],
        'Telawang': [-3.3259, 114.5855],
        // Banjarmasin Selatan
        'Kelayan Barat': [-3.3364, 114.5900],
        'Kelayan Timur': [-3.3364, 114.5950],
        'Pemurus Baru': [-3.3364, 114.6000],
        // Banjarmasin Tengah
        'Kertak Baru Ulu': [-3.3194, 114.5855],
        'Teluk Dalam': [-3.3194, 114.5900],
        'Seberang Mesjid': [-3.3194, 114.5950],
        // Banjarmasin Timur
        'Kuripan': [-3.3167, 114.6091],
        'Banua Anyar': [-3.3167, 114.6150],
        'Pengambangan': [-3.3167, 114.6200]
    };
    
    $(document).ready(function() {
        // Initialize map
        initializeMap();
        
        // Event listener untuk dropdown kecamatan
        $('#kecamatan').on('change', function() {
            const selectedKecamatan = $(this).val();
            if (selectedKecamatan) {
                // Tampilkan loading
                $('#mapLoading').css('display', 'flex');
                
                // Pindahkan peta ke kecamatan yang dipilih
                if (kecamatanCoordinates[selectedKecamatan]) {
                    moveMapToLocation(kecamatanCoordinates[selectedKecamatan]);
                    $('#mapLoading').css('display', 'none');
                } else {
                    // Jika tidak ada koordinat hardcoded, coba ambil dari API
                    getLocationCoordinates(selectedKecamatan, '');
                }
                
                // Filter kelurahan berdasarkan kecamatan yang dipilih
                getKelurahans(selectedKecamatan);
            }
        });
        
        // Event listener untuk dropdown kelurahan
        $('#kelurahan').on('change', function() {
            const selectedKelurahan = $(this).val();
            const selectedKecamatan = $('#kecamatan').val();
            
            if (selectedKelurahan) {
                // Tampilkan loading
                $('#mapLoading').css('display', 'flex');
                
                // Pindahkan peta ke kelurahan yang dipilih
                if (kelurahanCoordinates[selectedKelurahan]) {
                    moveMapToLocation(kelurahanCoordinates[selectedKelurahan]);
                    $('#mapLoading').css('display', 'none');
                } else {
                    // Jika tidak ada koordinat hardcoded, coba ambil dari API
                    getLocationCoordinates(selectedKecamatan, selectedKelurahan);
                }
            }
        });
    });
    
    // Fungsi untuk memfilter kelurahan berdasarkan kecamatan
    function getKelurahans(kecamatan) {
        $.ajax({
            url: '{{ route("admin.get-rumahsakit-kelurahans") }}',
            type: 'GET',
            data: { kecamatan: kecamatan },
            success: function(response) {
                // Reset dropdown kelurahan
                let options = '<option value="">Pilih Kelurahan</option>';
                
                if (response.success && response.kelurahans && response.kelurahans.length > 0) {
                    response.kelurahans.forEach(function(kelurahan) {
                        options += `<option value="${kelurahan}">${kelurahan}</option>`;
                    });
                }
                
                $('#kelurahan').html(options);
            },
            error: function(xhr) {
                console.error('Error loading kelurahans:', xhr.responseText);
            }
        });
    }
    
    // Fungsi untuk mendapatkan koordinat lokasi dari API
    function getLocationCoordinates(kecamatan, kelurahan) {
        $.ajax({
            url: '{{ route("admin.get-rumahsakit-location-coordinates") }}',
            type: 'GET',
            data: { 
                kecamatan: kecamatan,
                kelurahan: kelurahan
            },
            success: function(response) {
                // Sembunyikan loading
                $('#mapLoading').css('display', 'none');
                
                if (response.success && response.coordinates) {
                    // Set view ke koordinat yang dipilih
                    const lat = response.coordinates.lat;
                    const lng = response.coordinates.lng;
                    
                    // Pindahkan peta ke lokasi
                    map.setView([lat, lng], 15);
                    
                    // Update marker dan nilai input
                    updateMarkerPosition(lat, lng);
                } else {
                    console.warn('Koordinat tidak ditemukan:', response.message);
                }
            },
            error: function(xhr) {
                // Sembunyikan loading
                $('#mapLoading').css('display', 'none');
                console.error('Error getting coordinates:', xhr.responseText);
            }
        });
    }
    
    function initializeMap() {
        // Set default coordinates to Banjarmasin center
        const defaultLat = -3.3194374;
        const defaultLng = 114.5900474;
        
        // Initialize map
        map = L.map('map-container').setView([defaultLat, defaultLng], 13);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add click event to map
        map.on('click', function(e) {
            updateMarkerPosition(e.latlng.lat, e.latlng.lng);
        });
    }
    
    // Fungsi untuk memindahkan peta ke lokasi tertentu
    function moveMapToLocation(coordinates) {
        if (!coordinates || !Array.isArray(coordinates) || coordinates.length !== 2) {
            console.error('Invalid coordinates provided:', coordinates);
            return;
        }
        
        const [lat, lng] = coordinates;
        
        // Set view ke koordinat yang dipilih
        map.setView([lat, lng], 15);
        
        // Update marker dan field input
        updateMarkerPosition(lat, lng);
    }
    
    // Fungsi untuk memperbarui posisi marker dan nilai input
    function updateMarkerPosition(lat, lng) {
        $('#latitude').val(lat.toFixed(6));
        $('#longitude').val(lng.toFixed(6));
        
        // Update coordinate display
        updateCoordinateDisplay(lat, lng);
        
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
</script>
@endsection