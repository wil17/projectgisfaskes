@extends('layouts.admin')

@section('page-title', 'Edit Klinik')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.klinik.index') }}">Manajemen Klinik</a></li>
    <li class="breadcrumb-item active">Edit Klinik</li>
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
                        <h1><i class="fas fa-edit"></i> Edit Klinik</h1>
                        <p class="subtitle">Perbarui informasi klinik {{ $klinik->nama }}</p>
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

                        <form action="{{ route('admin.klinik.update', $klinik->id) }}" method="POST" id="editKlinikForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_klinik" class="form-label">Nama Klinik <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_klinik" name="nama_klinik" 
                                               value="{{ old('nama_klinik', $klinik->nama) }}" required placeholder="Masukkan nama klinik">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="skala_usaha" class="form-label">Skala Usaha <span class="text-danger">*</span></label>
                                        <select class="form-select" id="skala_usaha" name="skala_usaha" required>
                                            <option value="">Pilih Skala Usaha</option>
                                            <option value="Kecil" {{ old('skala_usaha', $klinik->skala_usaha) == 'Kecil' ? 'selected' : '' }}>Kecil</option>
                                            <option value="Mikro" {{ old('skala_usaha', $klinik->skala_usaha) == 'Mikro' ? 'selected' : '' }}>Mikro</option>
                                            <option value="Besar" {{ old('skala_usaha', $klinik->skala_usaha) == 'Besar' ? 'selected' : '' }}>Besar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required placeholder="Masukkan alamat lengkap klinik">{{ old('alamat', $klinik->alamat) }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kecamatan" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kecamatan" name="kecamatan" required>
                                            <option value="">Pilih Kecamatan</option>
                                            @foreach($kecamatans as $kecamatan)
                                                <option value="{{ $kecamatan }}" {{ old('kecamatan', $klinik->kecamatan) == $kecamatan ? 'selected' : '' }}>{{ $kecamatan }}</option>
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
                                                <option value="{{ $kelurahan }}" {{ old('kelurahan', $klinik->kelurahan) == $kelurahan ? 'selected' : '' }}>{{ $kelurahan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kota" name="kota" 
                                               value="{{ old('kota', $klinik->kota ?? 'Banjarmasin') }}" required readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tgl_berdiri" class="form-label">Tanggal Berdiri <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tgl_berdiri" name="tgl_berdiri" 
                                               value="{{ old('tgl_berdiri', $klinik->tgl_berdiri) }}" required placeholder="ex: 2020-01-15">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tenaga_kerja" class="form-label">Jumlah Tenaga Kerja <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="tenaga_kerja" name="tenaga_kerja" 
                                               value="{{ old('tenaga_kerja', $klinik->tenaga_kerja) }}" required min="1" placeholder="Jumlah karyawan">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Map Section -->
                            <div class="map-section">
                                <h6><i class="fas fa-map-marker-alt"></i> Lokasi Klinik</h6>
                                
                                <div class="map-instruction">
                                    <p><i class="fas fa-hand-pointer"></i> Klik pada peta untuk mengubah lokasi klinik</p>
                                </div>
                                
                                <div id="map-container"></div>
                                
                                <div class="coordinate-display has-coordinates" id="coordinateDisplay">
                                    <p class="coordinate-text" id="coordinateText">
                                        @if($klinik->latitude && $klinik->longitude)
                                            <i class="fas fa-check-circle"></i> 
                                            Koordinat saat ini: {{ $klinik->latitude }}, {{ $klinik->longitude }}
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
                                                value="{{ old('latitude', $klinik->latitude) }}" required placeholder="Masukkan latitude koordinat">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="longitude" name="longitude" 
                                                value="{{ old('longitude', $klinik->longitude) }}" required placeholder="Masukkan longitude koordinat">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <a href="{{ route('admin.klinik.index') }}" class="btn btn-secondary">
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
        const currentLat = parseFloat('{{ $klinik->latitude }}') || -3.3194374;
        const currentLng = parseFloat('{{ $klinik->longitude }}') || 114.5900474;
        
        // Initialize map with current location
        map = L.map('map-container').setView([currentLat, currentLng], 15);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add marker if coordinates exist
        if ('{{ $klinik->latitude }}' && '{{ $klinik->longitude }}') {
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