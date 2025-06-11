//maps.js
document.addEventListener('DOMContentLoaded', function() {
    // Variabel untuk menyimpan kecamatan dan kelurahan yang terpilih
    window.selectedKecamatanList = [];
    window.selectedKelurahanList = [];
// Make sure clearAllMarkers is called during initialization
if (typeof clearAllMarkers === 'function') {
    clearAllMarkers();
}
    // Initialize the map
    const map = L.map('map', {
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft'
        },
        zoomControl: false
    }).setView([-3.314494, 114.592972], 12); // Zoom Center ke Banjarmasin

    window.mapInstance = map;

    L.control.zoom({
        position: 'topleft'
    }).addTo(map);


    const openStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; OpenStreetMap contributors'
    });

    const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 20,
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri'
    });

    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
    });
    

    // Add the base layers to the map
    openStreetMap.addTo(map);

    // Layer control
    const baseMaps = {
        "OpenStreetMap": openStreetMap,
        "ESRI Satellite": esriSatellite,
        "Google Maps": googleStreets
    };

    L.control.layers(baseMaps).addTo(map);

    // Variables for user location and accuracy
    let userLocationMarker = null;
    let userLocationCircle = null;
    let accuracyCircle = null;
    let nearbyMarkersLayer = L.layerGroup().addTo(map);

    // City boundary variables
    let cityBoundaryLayer = null;
    let cityBoundaryMarkers = [];
    
    // Variabel untuk menyimpan koordinat batas kota dalam format desimal
    const cityBoundaries = {
        north: { lat: -3.279444, lng: 114.59275, label: 'UTARA<br>3°16\'46"' },
        east: { lat: -3.31449, lng: 114.665278, label: 'TIMUR<br>114°39\'55"' },
        south: { lat: -3.381667, lng: 114.59275, label: 'SELATAN<br>3°22\'54"' },
        west: { lat: -3.31449, lng: 114.527778, label: 'BARAT<br>114°31\'40"' }
    };

// Define marker icons for different facilities
const icons = {
    'Apotek': L.divIcon({
        html: '<div style="background-color: #2196F3; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-pills" style="font-size: 16px;"></i></div>',
        className: 'facility-marker',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    }),
    'Klinik': L.divIcon({
        html: '<div style="background-color: #4CAF50; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-stethoscope" style="font-size: 16px;"></i></div>',
        className: 'facility-marker',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    }),
    'Rumah Sakit': L.divIcon({
        html: '<div style="background-color: #F44336; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-hospital" style="font-size: 16px;"></i></div>',
        className: 'facility-marker',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    }),
    'Puskesmas': L.divIcon({
        html: '<div style="background-color: #FF9800; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-first-aid" style="font-size: 16px;"></i></div>',
        className: 'facility-marker',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    })
};

// Membuat icons bisa diakses secara global
window.icons = icons;

    // User location icon
    const userLocationIcon = L.divIcon({
        html: '<div style="background-color: #FF1744; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-map-marker-alt" style="font-size: 16px;"></i></div>',
        className: 'user-location-marker',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    });

    // Layer groups for facilities
    const facilityLayers = {
        'Apotek': L.layerGroup().addTo(map),
        'Klinik': L.layerGroup().addTo(map),
        'Rumah Sakit': L.layerGroup().addTo(map),
        'Puskesmas': L.layerGroup().addTo(map)
    };

    // Make facilityLayers globally accessible
    window.facilityLayers = facilityLayers;

    // Stores all markers for quick reference
    let allMarkers = [];
    window.allMarkers = allMarkers;

   // Fungsi untuk membuat dan menampilkan batas kota
function createCityBoundary() {
    // Buat array koordinat polygon untuk membentuk persegi panjang batas kota
    const boundaryCoords = [
        [cityBoundaries.north.lat, cityBoundaries.north.lng],
        [cityBoundaries.north.lat, cityBoundaries.east.lng],
        [cityBoundaries.south.lat, cityBoundaries.east.lng],
        [cityBoundaries.south.lat, cityBoundaries.west.lng],
        [cityBoundaries.north.lat, cityBoundaries.west.lng],
        [cityBoundaries.north.lat, cityBoundaries.north.lng]
    ];

    // Buat layer grup untuk batas kota
    cityBoundaryLayer = L.layerGroup();
    
    // Buat polygon dengan pengaturan warna dan gaya
    const boundaryPolygon = L.polygon(boundaryCoords, {
        color: '#FF6B35',       // Warna garis
        weight: 3,              // Ketebalan garis
        opacity: 0.8,           // Transparansi garis
        fillOpacity: 0.1,       // Transparansi isi polygon
        dashArray: '10, 5'      // Garis putus-putus
    }).addTo(cityBoundaryLayer);

        // Tambahkan marker dengan label di setiap titik batas mata angin
    Object.entries(cityBoundaries).forEach(([direction, coords]) => {
        const marker = L.marker([coords.lat, coords.lng], {
            icon: L.divIcon({
                html: `<div style="background-color: #FF6B35; color: white; padding: 8px 12px; 
                       border-radius: 20px; font-weight: bold; font-size: 12px; text-align: center; 
                       border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); 
                       white-space: nowrap;">${coords.label}</div>`,
                className: 'city-boundary-marker',
                iconSize: [80, 40],
                iconAnchor: [40, 20]
            })
        }).addTo(cityBoundaryLayer);
        
        cityBoundaryMarkers.push(marker);
    });

    // Tambahkan popup informasi saat polygon diklik
    boundaryPolygon.bindPopup(`
        <div style="text-align: center;">
            <strong>🏙️ Batas Kota Banjarmasin</strong><br><br>
            <small>
                <strong>Utara:</strong> 3°16'46"<br>
                <strong>Timur:</strong> 114°39'55"<br>
                <strong>Selatan:</strong> 3°22'54"<br>
                <strong>Barat:</strong> 114°31'40"
            </small>
        </div>
    `);
}

    // Make createCityBoundary globally accessible
    window.createCityBoundary = createCityBoundary;

   // Fungsi untuk menampilkan atau menyembunyikan batas kota
window.toggleCityBoundary = function(show) {
    if (show) {
        // Jika batas kota belum dibuat, buat terlebih dahulu
        if (!cityBoundaryLayer) {
            createCityBoundary();
        }
        map.addLayer(cityBoundaryLayer);
    } else {
        // Sembunyikan batas kota jika ada
        if (cityBoundaryLayer) {
            map.removeLayer(cityBoundaryLayer);
        }
    }
};

    // Notification system
    window.showNotification = function(message, type = 'info') {
        const notificationArea = document.getElementById('notification-area');
        if (!notificationArea) return;

        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 2000; max-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        notificationArea.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification && notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    };

    // GPS Location Control Class
    const GPSLocationControl = L.Control.extend({
        options: {
            position: 'topleft'
        },
        
        onAdd: function(map) {
            const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-gps');
            container.style.marginTop = '10px'; // Add space below zoom control
            
            const button = L.DomUtil.create('a', '', container);
            button.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
            button.href = '#';
            button.title = 'Cari Lokasi Saya';
            
            // Add click event
            L.DomEvent.on(button, 'click', function(e) {
                L.DomEvent.stopPropagation(e);
                L.DomEvent.preventDefault(e);
                getCurrentLocation(true);
            });
            
            return container;
        }
    });

    // Fungsi untuk mengkonversi derajat desimal ke DMS (Degree, Minute, Second)
    function degreeToDMS(decimal, isLat) {
        const absolute = Math.abs(decimal);
        const degrees = Math.floor(absolute);
        const minutesNotTruncated = (absolute - degrees) * 60;
        const minutes = Math.floor(minutesNotTruncated);
        const seconds = Math.floor((minutesNotTruncated - minutes) * 60);
        
        const arah = isLat ? (decimal >= 0 ? "U" : "S") : (decimal >= 0 ? "T" : "B");
        
        return `${degrees}° ${minutes}' ${seconds}" ${arah}`;
    }

    // Add GPS control to map
    const gpsControl = new GPSLocationControl();
    map.addControl(gpsControl);

    // Function to calculate distance between two points using Haversine formula
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in kilometers
        const dLat = (lat2 - lat1) * (Math.PI / 180);
        const dLon = (lon2 - lon1) * (Math.PI / 180);
        const a = 
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = R * c;
        return distance;
    }

// Fungsi untuk mendapatkan lokasi pengguna dengan akurasi maksimal
function getCurrentLocation(zoomToLocation = false) {
    if (navigator.geolocation) {
        // Tampilkan indikator loading pada tombol GPS
        const gpsControlElement = document.querySelector('.leaflet-control-gps');
        if (gpsControlElement) {
            gpsControlElement.classList.add('locating');
        }

        // Opsi untuk akurasi tinggi
        const options = {
            enableHighAccuracy: true,    // Aktifkan akurasi tinggi
            timeout: 30000,              // Timeout 30 detik
            maximumAge: 0                // Jangan gunakan cache lokasi
        };

        // Sistem percobaan bertingkat untuk meningkatkan akurasi
        let attempts = 0;
        const maxAttempts = 3;
        let bestPosition = null;
        let bestAccuracy = Infinity;

        function tryGetLocation() {
            attempts++;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    
                    // Simpan posisi terbaik berdasarkan akurasi
                    if (accuracy < bestAccuracy) {
                        bestAccuracy = accuracy;
                        bestPosition = position;
                    }
                    
                    // Gunakan posisi jika akurasi sudah cukup baik atau sudah maksimal percobaan
                    if (accuracy <= 20 || attempts >= maxAttempts) {
                        usePosition(bestPosition);
                    } else if (attempts < maxAttempts) {
                        // Coba lagi untuk mendapatkan akurasi lebih baik
                        setTimeout(tryGetLocation, 1000);
                    } else {
                        usePosition(bestPosition);
                    }
                },
                function(error) {
                    // Tangani error
                    if (attempts < maxAttempts) {
                        setTimeout(tryGetLocation, 1000);
                    } else {
                        handleLocationError(error);
                    }
                },
                options
            );
        }

        function usePosition(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;
            
            // Hapus marker dan lingkaran sebelumnya
            if (userLocationMarker) map.removeLayer(userLocationMarker);
            if (userLocationCircle) map.removeLayer(userLocationCircle);
            if (accuracyCircle) map.removeLayer(accuracyCircle);
            
            // Tambahkan marker lokasi pengguna
            userLocationMarker = L.marker([lat, lng], { icon: userLocationIcon })
                .addTo(map)
                .bindPopup(`
                    <div style="text-align: center;">
                        <strong>📍 Lokasi Anda</strong><br>
                        <small>Lat: ${lat.toFixed(6)}<br>
                        Lng: ${lng.toFixed(6)}<br>
                        Akurasi: ±${Math.round(accuracy)}m</small>
                    </div>
                `);
            
            // Tambahkan lingkaran akurasi
            accuracyCircle = L.circle([lat, lng], {
                color: '#4285F4',
                fillColor: '#4285F4',
                fillOpacity: 0.05,
                opacity: 0.7,
                radius: accuracy,
                className: 'accuracy-circle'
            }).addTo(map);
            
            // Tambahkan lingkaran radius 3km untuk pencarian fasilitas
            userLocationCircle = L.circle([lat, lng], {
                color: '#FF1744',
                fillColor: '#FF1744',
                fillOpacity: 0.05,
                radius: 3000,
                dashArray: '5, 5'
            }).addTo(map);
            
            // Zoom ke lokasi pengguna dengan level zoom berdasarkan akurasi
            if (zoomToLocation) {
                let zoomLevel = 15;
                if (accuracy <= 10) zoomLevel = 17;       // Akurasi sangat tinggi
                else if (accuracy <= 50) zoomLevel = 16;  // Akurasi tinggi
                else if (accuracy <= 100) zoomLevel = 15; // Akurasi sedang
                else zoomLevel = 14;                      // Akurasi rendah
                
                map.setView([lat, lng], zoomLevel);
            }
            
            // Reset status tombol GPS
            const gpsControlElement = document.querySelector('.leaflet-control-gps');
            if (gpsControlElement) {
                gpsControlElement.classList.remove('locating');
            }
            
            // Tampilkan notifikasi berdasarkan tingkat akurasi
            let accuracyText = '';
            if (accuracy <= 10) accuracyText = 'Akurasi Sangat Tinggi';
            else if (accuracy <= 50) accuracyText = 'Akurasi Tinggi';
            else if (accuracy <= 100) accuracyText = 'Akurasi Sedang';
            else accuracyText = 'Akurasi Rendah';
            
            showNotification(`✅ Lokasi ditemukan! ${accuracyText} (±${Math.round(accuracy)}m)`, 'success');
            
            // Muat otomatis fasilitas kesehatan dalam radius 3km
            loadNearbyFacilities(lat, lng);
        }

            function handleLocationError(error) {
                let errorMessage = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = '❌ Izin lokasi ditolak. Silakan aktifkan GPS dan berikan izin.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = '❌ Informasi lokasi tidak tersedia. Pastikan GPS aktif.';
                        break;
                    case error.TIMEOUT:
                        errorMessage = '❌ Timeout mencari lokasi. Coba lagi di area dengan sinyal GPS lebih baik.';
                        break;
                    default:
                        errorMessage = '❌ Error tidak dikenal: ' + error.message;
                        break;
                }
                
                showNotification(errorMessage, 'error');
                
                // Reset GPS button state
                const gpsControlElement = document.querySelector('.leaflet-control-gps');
                if (gpsControlElement) {
                    gpsControlElement.classList.remove('locating');
                }
            }

            // Tampilkan notifikasi pencarian lokasi
        showNotification('🔍 Mencari lokasi GPS Anda...', 'info');
        
        // Mulai proses pencarian lokasi
        tryGetLocation();
        
    } else {
        showNotification('❌ Geolocation tidak didukung browser ini.', 'error');
    }
}

// Fungsi untuk memuat fasilitas kesehatan dalam radius 3km
async function loadNearbyFacilities(userLat, userLng) {
    try {
        // Bersihkan marker sebelumnya
        nearbyMarkersLayer.clearLayers();
        
        // Ambil data fasilitas dari API
        const response = await fetch('/api/faskes/nearby?lat=' + userLat + '&lng=' + userLng + '&radius=3');
        const facilities = await response.json();
        
        // Bersihkan marker yang ada
        allMarkers.forEach(marker => marker.remove());
        allMarkers = [];
        Object.values(facilityLayers).forEach(layer => layer.clearLayers());
        
        // Tambahkan marker untuk setiap fasilitas
        facilities.forEach(facility => {
            if (facility.longitude && facility.latitude) {
                const icon = icons[facility.fasilitas] || icons['Apotek'];
                
                const marker = L.marker([facility.latitude, facility.longitude], { icon: icon })
                    .bindPopup(`
                        <strong>${facility.nama}</strong><br>
                        <i class="fas fa-hospital"></i> ${facility.fasilitas}<br>
                        <i class="fas fa-map-marker-alt"></i> ${facility.alamat}<br>
                        <i class="fas fa-city"></i> Kec. ${facility.kecamatan}<br>
                        <i class="fas fa-map-pin"></i> Kel. ${facility.kelurahan || 'N/A'}<br>
                        <i class="fas fa-route"></i> Jarak: ${facility.distance} km
                    `);
                
                // Tambahkan marker ke layer yang sesuai
                if (facilityLayers[facility.fasilitas]) {
                    facilityLayers[facility.fasilitas].addLayer(marker);
                }
                
                allMarkers.push(marker);
            }
        });
        
        // Tampilkan notifikasi hasil pencarian
        if (facilities.length === 0) {
            showNotification('ℹ️ Tidak ada fasilitas kesehatan dalam radius 3km dari lokasi Anda.', 'warning');
        } else {
            showNotification(`✅ Ditemukan ${facilities.length} fasilitas kesehatan dalam radius 3km.`, 'success');
        }
        
    } catch (error) {
        console.error('Error loading nearby facilities:', error);
        showNotification('❌ Terjadi kesalahan saat memuat fasilitas kesehatan terdekat.', 'error');
    }
}

    // Expose loadNearbyFacilities to window
    window.loadNearbyFacilities = loadNearbyFacilities;

window.loadFacilities = async function(filters = {}) {
    try {
        console.log('=== LOAD FACILITIES ===');
        console.log('Memuat fasilitas dengan filter:', filters);
        
        // Check if any kecamatan is selected or "all kecamatan" is selected
        const selectAllKecamatan = document.getElementById('select-all-kecamatan');
        const anyKecamatanSelected = (filters.kecamatan && filters.kecamatan.length > 0) || 
                                     (selectAllKecamatan && selectAllKecamatan.checked);
        
        // If no kecamatan is selected, clear all markers and return without loading data
        if (!anyKecamatanSelected) {
            console.log('No kecamatan selected, clearing all markers');
            clearAllMarkers();
            return;
        }
        
        // Bersihkan marker yang ada
        clearAllMarkers();

        // Pastikan icons tersedia
        if (!window.icons) {
            console.error('Error: window.icons tidak tersedia');
            return;
        }

        // Buat parameter query dengan benar
        const queryParams = new URLSearchParams();
        
        // Tangani multiple kecamatan - PENTING: hanya kirim jika ada yang dipilih secara spesifik
        if (filters.kecamatan && Array.isArray(filters.kecamatan) && filters.kecamatan.length > 0) {
            console.log('Menambahkan filter kecamatan:', filters.kecamatan);
            filters.kecamatan.forEach(kec => {
                queryParams.append('kecamatan[]', kec);
            });
        }
        
        // Tangani multiple kelurahan - PENTING: hanya kirim jika ada yang dipilih secara spesifik
        if (filters.kelurahan && Array.isArray(filters.kelurahan) && filters.kelurahan.length > 0) {
            console.log('Menambahkan filter kelurahan:', filters.kelurahan);
            filters.kelurahan.forEach(kel => {
                queryParams.append('kelurahan[]', kel);
            });
        }
        
        // Tangani pencarian
        if (filters.search && filters.search.trim() !== '') {
            queryParams.append('search', filters.search.trim());
            console.log('Menambahkan filter pencarian:', filters.search.trim());
        }
        
        // Tangani jenis fasilitas
        if (filters.fasilitas && Array.isArray(filters.fasilitas) && filters.fasilitas.length > 0) {
            console.log('Menambahkan filter fasilitas:', filters.fasilitas);
            filters.fasilitas.forEach(facility => {
                queryParams.append('fasilitas[]', facility);
            });
        }

        // Debug: Tampilkan URL API yang digunakan
        const apiUrl = `/api/faskes?${queryParams.toString()}`;
        console.log('Mengambil data dari API:', apiUrl);
        console.log('Query parameters:', queryParams.toString());

        // Ambil data dari API dengan error handling yang lebih baik
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error:', response.status, response.statusText, errorText);
            throw new Error(`HTTP error ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log(`Ditemukan ${data.length} fasilitas dari API`);

        // Proses data dan tambahkan marker
        let addedMarkers = 0;
        data.forEach(item => {
            if (item.longitude && item.latitude) {
                // Pastikan koordinat valid
                const lat = parseFloat(item.latitude);
                const lng = parseFloat(item.longitude);
                
                if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                    // Pastikan icons tersedia dan tipe fasilitas valid
                    if (!window.icons[item.fasilitas]) {
                        console.warn(`Icon untuk tipe fasilitas "${item.fasilitas}" tidak ditemukan, menggunakan default`);
                    }
                    
                    const icon = window.icons[item.fasilitas] || window.icons['Apotek']; // Default ke ikon Apotek
                    
                    const marker = L.marker([lat, lng], { icon: icon })
                        .bindPopup(`
                            <div style="min-width: 200px;">
                                <strong style="color: #2c3e50; font-size: 14px;">${item.nama}</strong><br><br>
                                <div style="margin: 5px 0;">
                                    <i class="fas fa-hospital" style="color: #3498db; width: 16px;"></i> 
                                    <span style="color: #34495e;">${item.fasilitas}</span>
                                </div>
                                <div style="margin: 5px 0;">
                                    <i class="fas fa-map-marker-alt" style="color: #e74c3c; width: 16px;"></i> 
                                    <span style="color: #34495e;">${item.alamat}</span>
                                </div>
                                <div style="margin: 5px 0;">
                                    <i class="fas fa-city" style="color: #f39c12; width: 16px;"></i> 
                                    <span style="color: #34495e;">Kec. ${item.kecamatan}</span>
                                </div>
                                <div style="margin: 5px 0;">
                                    <i class="fas fa-map-pin" style="color: #27ae60; width: 16px;"></i> 
                                    <span style="color: #34495e;">Kel. ${item.kelurahan || 'N/A'}</span>
                                </div>
                            </div>
                        `);
                    
                    // Tambahkan marker ke layer yang sesuai
                    if (window.facilityLayers && window.facilityLayers[item.fasilitas]) {
                        window.facilityLayers[item.fasilitas].addLayer(marker);
                        addedMarkers++;
                    } else {
                        console.warn(`Layer untuk fasilitas "${item.fasilitas}" tidak ditemukan`);
                    }
                    
                    if (window.allMarkers) {
                        window.allMarkers.push(marker);
                    }
                } else {
                    console.warn('Koordinat tidak valid untuk:', item.nama, lat, lng);
                }
            } else {
                console.warn('Data tidak memiliki koordinat:', item.nama);
            }
        });
        
        console.log(`Berhasil menambahkan ${addedMarkers} marker ke peta`);
        
        // Tampilkan notifikasi
        if (window.allMarkers && window.allMarkers.length === 0) {
            if (window.showNotification) {
                window.showNotification('ℹ️ Tidak ada fasilitas kesehatan yang ditemukan dengan filter yang diterapkan.', 'warning');
            }
        } else {
            if (window.showNotification) {
                window.showNotification(`✅ Menampilkan ${window.allMarkers.length} fasilitas kesehatan.`, 'success');
            }
        }
        
        console.log('=== END LOAD FACILITIES ===');

    } catch (error) {
        console.error('Error loading facilities:', error);
        if (window.showNotification) {
            window.showNotification('❌ Terjadi kesalahan saat memuat data fasilitas kesehatan.', 'error');
        }
    }
};

// Add this function to clear all markers
function clearAllMarkers() {
    if (window.allMarkers && Array.isArray(window.allMarkers)) {
        window.allMarkers.forEach(marker => {
            if (marker && marker.remove) {
                marker.remove();
            }
        });
    }
    window.allMarkers = [];
    
    if (window.facilityLayers) {
        Object.values(window.facilityLayers).forEach(layer => {
            if (layer) {
                layer.clearLayers();
            }
        });
    }
}

// Make the function globally available
window.clearAllMarkers = clearAllMarkers;


    // GeoJSON style functions with more distinct colors
    function getKecamatanColor(kecamatan) {
        // More distinct colors for each kecamatan
        const colors = {
            'Banjarmasin Utara': '#3498db',    // Clear blue
            'Banjarmasin Selatan': '#27ae60',  // Green
            'Banjarmasin Tengah': '#9b59b6',   // Purple
            'Banjarmasin Timur': '#e67e22',    // Orange
            'Banjarmasin Barat': '#e74c3c'     // Red
        };
        return colors[kecamatan] || '#95a5a6';  // Gray default
    }
    
    window.getKecamatanColor = getKecamatanColor;

    function getKelurahanColor(kecamatan) {
        // Generate variations of the kecamatan color for kelurahan
        const baseColor = getKecamatanColor(kecamatan);
        
        // Convert hex to RGB
        const r = parseInt(baseColor.slice(1, 3), 16);
        const g = parseInt(baseColor.slice(3, 5), 16);
        const b = parseInt(baseColor.slice(5, 7), 16);
        
        // Create lighter shade for kelurahan (increase brightness)
        const newR = Math.min(255, Math.round(r + (255 - r) * 0.3));
        const newG = Math.min(255, Math.round(g + (255 - g) * 0.3));
        const newB = Math.min(255, Math.round(b + (255 - b) * 0.3));
        
        // Convert back to hex
        return `#${newR.toString(16).padStart(2, '0')}${newG.toString(16).padStart(2, '0')}${newB.toString(16).padStart(2, '0')}`;
    }
    
    window.getKelurahanColor = getKelurahanColor;

    function styleKecamatan(feature) {
        return {
            fillColor: getKecamatanColor(feature.properties.NAMOBJ),
            weight: 2,
            opacity: 1,
            color: '#2c3e50',
            dashArray: '',
            fillOpacity: 0.4
        };
    }

    function styleKelurahan(feature) {
        return {
            fillColor: getKelurahanColor(feature.properties.WADMKC),
            weight: 1,
            opacity: 1,
            color: '#34495e',
            dashArray: '',
            fillOpacity: 0.3
        };
    }

    // Variables to store GeoJSON layers
    let kecamatanLayer;
    let kelurahanLayer;
    let kecamatanPolygons = {}; // To store kecamatan polygons for zooming
    let kelurahanPolygons = {}; // To store kelurahan polygons for zooming
    let kelurahanByKecamatan = {}; // Store kelurahan grouped by kecamatan
    let allKelurahanNames = []; // Store all unique kelurahan names

    // Make these variables available globally
    window.kecamatanPolygons = kecamatanPolygons;
    window.kelurahanPolygons = kelurahanPolygons;
    window.kelurahanByKecamatan = kelurahanByKecamatan;
    window.allKelurahanNames = allKelurahanNames;

    // Function to filter kelurahan based on kecamatan
    window.filterKelurahan = function(selectedKecamatan, kelurahanSelect) {
        // Reset kelurahan selection
        kelurahanSelect.value = '';
        
        // Clear all options except the first one
        while (kelurahanSelect.children.length > 1) {
            kelurahanSelect.removeChild(kelurahanSelect.lastChild);
        }
        
        // Add kelurahan options for selected kecamatan
        if (selectedKecamatan && kelurahanByKecamatan[selectedKecamatan]) {
            kelurahanByKecamatan[selectedKecamatan].forEach(kelurahan => {
                const option = document.createElement('option');
                option.value = kelurahan;
                option.textContent = kelurahan;
                option.dataset.kecamatan = selectedKecamatan;
                kelurahanSelect.appendChild(option);
            });
        }
    };

    async function loadBoundaries() {
        try {
            const kecamatanResponse = await fetch('/batas_kecamatan.geojson');
            const kecamatanData = await kecamatanResponse.json();
            
            kecamatanLayer = L.geoJSON(kecamatanData, {
                style: styleKecamatan,
                onEachFeature: function(feature, layer) {
                    if (feature.properties && feature.properties.NAMOBJ) {
                        const kecamatanName = feature.properties.NAMOBJ;
                        layer.bindTooltip(kecamatanName);
                
                        kecamatanPolygons[kecamatanName] = layer;
                    
                        layer.on('click', function() {
                           
                            setTimeout(() => {
                                map.fitBounds(layer.getBounds(), { padding: [20, 20] });
                            }, 100);
                        });
                    }
                }
            }).addTo(map);
            
            // Load Kelurahan boundaries
            const kelurahanResponse = await fetch('/batas_kelurahan.geojson');
            const kelurahanData = await kelurahanResponse.json();
            
            // Group kelurahan by kecamatan and collect all unique kelurahan names
            kelurahanData.features.forEach(feature => {
                const kecamatan = feature.properties.WADMKC;
                const kelurahan = feature.properties.WADMKD;
                
                if (!kelurahanByKecamatan[kecamatan]) {
                    kelurahanByKecamatan[kecamatan] = [];
                }
                
                if (!kelurahanByKecamatan[kecamatan].includes(kelurahan)) {
                    kelurahanByKecamatan[kecamatan].push(kelurahan);
                }
                
                if (!allKelurahanNames.includes(kelurahan)) {
                    allKelurahanNames.push(kelurahan);
                }
            });
            
            // Sort kelurahan names
            allKelurahanNames.sort();
            
            kelurahanLayer = L.geoJSON(kelurahanData, {
                style: styleKelurahan,
                onEachFeature: function(feature, layer) {
                    if (feature.properties && feature.properties.WADMKD) {
                        const kelurahanName = feature.properties.WADMKD;
                        const kecamatanName = feature.properties.WADMKC;
                        
                        layer.bindTooltip(kelurahanName);
                        
                        // Store polygon for later use with compound key
                        kelurahanPolygons[`${kecamatanName}-${kelurahanName}`] = layer;
                        
                        // Add click event to zoom to kelurahan
                        layer.on('click', function() {
                            // Modif untuk multi select
                            // Nanti diubah sesuaikan dengan map.js untuk multiselect
                            
                            // Zoom to this kelurahan
                            setTimeout(() => {
                                map.fitBounds(layer.getBounds(), { padding: [20, 20] });
                            }, 100);
                        });
                    }
                }
            }).addTo(map);
            
            // Create legend after boundaries are loaded
            createMapLegend();
            
            // Initialize map filters after boundaries are loaded
            if (typeof initializeMapFilters === 'function') {
                initializeMapFilters();
            }
        } catch (error) {
            console.error('Error loading boundaries:', error);
            showNotification('❌ Terjadi kesalahan saat memuat peta batas wilayah.', 'error');
        }
    }

    // Create map legend (facility icons, kecamatan, and full kelurahan list)
    function createMapLegend() {
        // Add map legend container if it doesn't exist
        if (!document.getElementById('map-legend')) {
            const legendDiv = document.createElement('div');
            legendDiv.id = 'map-legend';
            legendDiv.className = 'map-legend';
            document.getElementById('map').appendChild(legendDiv);
            
            // Create facility legend
            const facilityLegendHTML = `
                <div class="legend-section">
                    <div class="legend-title">Fasilitas Kesehatan</div>
                    <div class="legend-item">
                        <div style="background-color: #2196F3; color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid white; margin-right: 8px;"><i class="fas fa-pills" style="font-size: 10px;"></i></div>
                        <span>Apotek</span>
                    </div>
                    <div class="legend-item">
                        <div style="background-color: #4CAF50; color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid white; margin-right: 8px;"><i class="fas fa-stethoscope" style="font-size: 10px;"></i></div>
                        <span>Klinik</span>
                    </div>
                    <div class="legend-item">
                        <div style="background-color: #F44336; color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid white; margin-right: 8px;"><i class="fas fa-hospital" style="font-size: 10px;"></i></div>
                        <span>Rumah Sakit</span>
                    </div>
                    <div class="legend-item">
                        <div style="background-color: #FF9800; color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid white; margin-right: 8px;"><i class="fas fa-first-aid" style="font-size: 10px;"></i></div>
                        <span>Puskesmas</span>
                    </div>
                    <div class="legend-item">
                        <div style="background-color: #FF1744; color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid white; margin-right: 8px;"><i class="fas fa-map-marker-alt" style="font-size: 10px;"></i></div>
                        <span>Lokasi Pengguna</span>
                    </div>
                    <div class="legend-item">
                        <div style="background-color: #FF6B35; color: white; padding: 4px 8px; border-radius: 10px; font-weight: bold; font-size: 8px; border: 1px solid white; margin-right: 8px;">BATAS</div>
                        <span>Batas Kota</span>
                    </div>
                </div>
            `;
            
            // Create kecamatan legend
            const kecamatanNames = [
                'Banjarmasin Utara',
                'Banjarmasin Selatan', 
                'Banjarmasin Tengah',
                'Banjarmasin Timur',
                'Banjarmasin Barat'
            ];
            
            let kecamatanLegendHTML = `
                <div class="legend-section">
                    <div class="legend-title">Kecamatan</div>
            `;
            
            kecamatanNames.forEach(kecamatan => {
                const color = getKecamatanColor(kecamatan);
                kecamatanLegendHTML += `
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: ${color}; width: 16px; height: 16px; margin-right: 8px; border: 1px solid #ccc;"></div>
                        <span style="font-size: 0.8rem;">${kecamatan}</span>
                    </div>
                `;
            });
            
            kecamatanLegendHTML += '</div>';
            
            // Create full kelurahan legend
            let kelurahanLegendHTML = `
                <div class="legend-section legend-collapse">
                    <div class="legend-title legend-clickable" onclick="toggleKelurahanLegend()">
                        <span>Kelurahan</span>
                        <i id="kelurahan-toggle-icon" class="fas fa-chevron-down" style="float: right; font-size: 0.7rem;"></i>
                    </div>
                    <div id="kelurahan-list" class="legend-kelurahan-list" style="display: none; max-height: 200px; overflow-y: auto;">
            `;
            
            // Show ALL kelurahan grouped by kecamatan
            Object.entries(kelurahanByKecamatan).forEach(([kecamatan, kelurahanList]) => {
                const kecamatanColor = getKecamatanColor(kecamatan);
                const kelurahanColor = getKelurahanColor(kecamatan);
                
                kelurahanLegendHTML += `
                    <div class="legend-subcategory" style="margin-bottom: 6px;">
                        <div style="font-size: 0.7rem; font-weight: 600; color: ${kecamatanColor}; margin-bottom: 3px;">${kecamatan}</div>
                `;
                
                // Show ALL kelurahan, not just first 3
                kelurahanList.forEach(kelurahan => {
                    kelurahanLegendHTML += `
                        <div class="legend-item" style="margin-left: 12px; margin-bottom: 2px;">
                            <div class="legend-color" style="background-color: ${kelurahanColor}; width: 10px; height: 10px; margin-right: 5px; border: 1px solid #ccc;"></div>
                            <span style="font-size: 0.65rem;">${kelurahan}</span>
                        </div>
                    `;
                });
                
                kelurahanLegendHTML += '</div>';
            });
            
            kelurahanLegendHTML += '</div></div>';
            
            // Combine all legends
            legendDiv.innerHTML = facilityLegendHTML + kecamatanLegendHTML + kelurahanLegendHTML;
            
            // Add style for custom scrollbar in kelurahan list
            const style = document.createElement('style');
            style.textContent = `
                .legend-kelurahan-list::-webkit-scrollbar {
                    width: 4px;
                }
                .legend-kelurahan-list::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }
                .legend-kelurahan-list::-webkit-scrollbar-thumb {
                    background: #888;
                    border-radius: 2px;
                }
                .legend-kelurahan-list::-webkit-scrollbar-thumb:hover {
                    background: #555;
                }
                .legend-clickable {
                    cursor: pointer;
                }
                .legend-clickable:hover {
                    background-color: #f5f5f5;
                    padding: 2px;
                    border-radius: 3px;
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Function to toggle kelurahan legend
    window.toggleKelurahanLegend = function() {
        const kelurahanList = document.getElementById('kelurahan-list');
        const toggleIcon = document.getElementById('kelurahan-toggle-icon');
        
        if (kelurahanList.style.display === 'none') {
            kelurahanList.style.display = 'block';
            toggleIcon.className = 'fas fa-chevron-up';
        } else {
            kelurahanList.style.display = 'none';
            toggleIcon.className = 'fas fa-chevron-down';
        }
    };

    // Initialize map
    loadBoundaries();
    loadFacilities();
    
    // Handle window resize for responsiveness
    window.addEventListener('resize', function() {
        map.invalidateSize();
    });

    // Tambahkan fungsi-fungsi berikut untuk mengelola tampilan polygon kecamatan dan kelurahan
    
    // Fungsi untuk menampilkan polygon kecamatan berdasarkan filter
    window.updateKecamatanPolygons = function(selectedKecamatanList = []) {
        console.log('Memperbarui polygon kecamatan:', selectedKecamatanList);
        
        // Jika tidak ada kecamatan yang dipilih atau semua kecamatan dipilih (selectAllKecamatan = true)
        if (selectedKecamatanList.length === 0) {
            // Tampilkan semua polygon kecamatan
            if (kecamatanLayer) {
                map.addLayer(kecamatanLayer);
            }
        } else {
            // Hapus layer kecamatan dan tampilkan hanya yang dipilih
            if (kecamatanLayer) {
                map.removeLayer(kecamatanLayer);
            }
            
            // Hapus layer kecamatan kustom yang mungkin sudah ada sebelumnya
            if (window.currentKecamatanLayer) {
                map.removeLayer(window.currentKecamatanLayer);
            }
            
            // Buat layer baru untuk kecamatan yang dipilih
            const selectedKecamatanLayer = L.layerGroup();
            
            // Tambahkan polygon untuk kecamatan yang dipilih
            selectedKecamatanList.forEach(kecamatanName => {
                if (kecamatanPolygons[kecamatanName]) {
                    const polygon = kecamatanPolygons[kecamatanName];
                    selectedKecamatanLayer.addLayer(polygon);
                }
            });
            
            // Tambahkan layer kecamatan yang dipilih ke peta
            selectedKecamatanLayer.addTo(map);
            
            // Simpan layer yang baru dibuat untuk digunakan nanti
            window.currentKecamatanLayer = selectedKecamatanLayer;
        }
    };

    // Fungsi untuk menampilkan polygon kelurahan berdasarkan filter
    window.updateKelurahanPolygons = function(selectedKelurahanList = [], selectedKecamatanList = []) {
        console.log('Memperbarui polygon kelurahan:', selectedKelurahanList);
        console.log('Kecamatan yang dipilih:', selectedKecamatanList);
        
        // Jika tidak ada kelurahan yang dipilih atau semua kelurahan dipilih
        if (selectedKelurahanList.length === 0) {
            // Jika semua kelurahan dipilih, tampilkan kelurahan berdasarkan kecamatan yang dipilih
            if (kelurahanLayer) {
                // Jika semua kecamatan juga dipilih, tampilkan semua kelurahan
                if (selectedKecamatanList.length === 0) {
                    map.addLayer(kelurahanLayer);
                } else {
                    // Hapus layer kelurahan yang ada
                    map.removeLayer(kelurahanLayer);
                    
                    // Hapus layer kelurahan kustom yang mungkin sudah ada sebelumnya
                    if (window.currentKelurahanLayer) {
                        map.removeLayer(window.currentKelurahanLayer);
                    }
                    
                    // Buat layer baru untuk kelurahan dari kecamatan yang dipilih
                    const filteredKelurahanLayer = L.layerGroup();
                    
                    // Iterasi setiap kecamatan yang dipilih
                    selectedKecamatanList.forEach(kecamatanName => {
                        // Dapatkan semua kelurahan untuk kecamatan ini
                        const kelurahanInKecamatan = kelurahanByKecamatan[kecamatanName] || [];
                        
                        // Tambahkan polygon untuk kelurahan dalam kecamatan ini
                        kelurahanInKecamatan.forEach(kelurahanName => {
                            const key = `${kecamatanName}-${kelurahanName}`;
                            if (kelurahanPolygons[key]) {
                                filteredKelurahanLayer.addLayer(kelurahanPolygons[key]);
                            }
                        });
                    });
                    
                    // Tambahkan layer kelurahan yang difilter ke peta
                    filteredKelurahanLayer.addTo(map);
                    
                    // Simpan layer yang baru dibuat untuk digunakan nanti
                    window.currentKelurahanLayer = filteredKelurahanLayer;
                }
            }
        } else {
            // Kelurahan tertentu dipilih, tampilkan hanya kelurahan yang dipilih
            if (kelurahanLayer) {
                map.removeLayer(kelurahanLayer);
            }
            
            // Hapus layer kelurahan kustom yang mungkin sudah ada sebelumnya
            if (window.currentKelurahanLayer) {
                map.removeLayer(window.currentKelurahanLayer);
            }
            
            // Buat layer baru untuk kelurahan yang dipilih
            const selectedKelurahanLayer = L.layerGroup();
            
            // Cari polygon untuk setiap kelurahan yang dipilih
            selectedKelurahanList.forEach(kelurahanName => {
                // Kita perlu menemukan kecamatan untuk kelurahan ini
                // Karena kunci polygon kelurahan menggunakan format "kecamatan-kelurahan"
                Object.keys(kelurahanByKecamatan).forEach(kecamatanName => {
                    if (kelurahanByKecamatan[kecamatanName].includes(kelurahanName)) {
                        const key = `${kecamatanName}-${kelurahanName}`;
                        if (kelurahanPolygons[key]) {
                            selectedKelurahanLayer.addLayer(kelurahanPolygons[key]);
                        }
                    }
                });
            });
            
            // Tambahkan layer kelurahan yang dipilih ke peta
            selectedKelurahanLayer.addTo(map);
            
            // Simpan layer yang baru dibuat untuk digunakan nanti
            window.currentKelurahanLayer = selectedKelurahanLayer;
        }
    };

    // Tambahkan variabel untuk menyimpan layer kustom
    window.currentKecamatanLayer = null;
    window.currentKelurahanLayer = null;
});