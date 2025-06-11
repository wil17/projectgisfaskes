@extends('layouts.admin')

@section('page-title', 'Edit Rumah Sakit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.rumahsakit.index') }}">Manajemen Rumah Sakit</a></li>
    <li class="breadcrumb-item active">Edit Rumah Sakit</li>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminapotek.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
<div class="apotek-form-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-card">
                    <div class="form-header">
                        <h1><i class="fas fa-edit"></i> Edit Rumah Sakit</h1>
                        <p class="subtitle">Perbarui informasi rumah sakit {{ $rumahsakit->nama_rs }}</p>
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

                        <form action="{{ route('admin.rumahsakit.update', $rumahsakit->id_rs) }}" method="POST" id="editRumahSakitForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="nama_rs" class="form-label">Nama Rumah Sakit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_rs" name="nama_rs" 
                                       value="{{ old('nama_rs', $rumahsakit->nama_rs) }}" required placeholder="Masukkan nama rumah sakit">
                            </div>
                            
                            <div class="form-group">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required placeholder="Masukkan alamat lengkap rumah sakit">{{ old('alamat', $rumahsakit->alamat) }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="poliklinik_dokter" class="form-label">Poliklinik & Dokter <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="poliklinik_dokter" name="poliklinik_dokter" rows="5" required placeholder="Masukkan daftar poliklinik dan dokter">{{ old('poliklinik_dokter', $rumahsakit->poliklinik_dokter) }}</textarea>
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
                                                <option value="{{ $kecamatan }}" {{ old('kecamatan', $rumahsakit->kecamatan) == $kecamatan ? 'selected' : '' }}>{{ $kecamatan }}</option>
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
                                                <option value="{{ $kelurahan }}" {{ old('kelurahan', $rumahsakit->kelurahan) == $kelurahan ? 'selected' : '' }}>{{ $kelurahan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kota" name="kota" 
                                               value="{{ old('kota', $rumahsakit->kota ?? 'Banjarmasin') }}" required readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Map Section -->
                            <div class="map-section">
                                <h6><i class="fas fa-map-marker-alt"></i> Lokasi Rumah Sakit</h6>
                                
                                <div class="map-instruction">
                                    <p><i class="fas fa-hand-pointer"></i> Klik pada peta untuk mengubah lokasi rumah sakit</p>
                                </div>
                                
                                <div id="map-container"></div>
                                
                                <div class="coordinate-display has-coordinates" id="coordinateDisplay">
                                    <p class="coordinate-text" id="coordinateText">
                                        @if($rumahsakit->latitude && $rumahsakit->longitude)
                                            <i class="fas fa-check-circle"></i> 
                                            Koordinat saat ini: {{ $rumahsakit->latitude }}, {{ $rumahsakit->longitude }}
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
                                                   value="{{ old('latitude', $rumahsakit->latitude) }}" required placeholder="Masukkan latitude koordinat">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="longitude" name="longitude" 
                                                   value="{{ old('longitude', $rumahsakit->longitude) }}" required placeholder="Masukkan longitude koordinat">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <a href="{{ route('admin.rumahsakit.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
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
    
    $(document).ready(function() {
        // Initialize map with existing coordinates
        initializeMap();
        
        // Add event listeners for manual coordinate input
        $('#latitude, #longitude').on('change', function() {
            updateMapFromInputs();
        });
    });
    
    function initializeMap() {
        // Get existing coordinates or use default
        const currentLat = parseFloat('{{ $rumahsakit->latitude }}') || -3.3194374;
        const currentLng = parseFloat('{{ $rumahsakit->longitude }}') || 114.5900474;
        
        // Initialize map with current location
        map = L.map('map-container').setView([currentLat, currentLng], 15);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add marker if coordinates exist
        if ('{{ $rumahsakit->latitude }}' && '{{ $rumahsakit->longitude }}') {
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
</script>
@endsection