document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    const map = L.map('map', {
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft'
        }
    }).setView([-3.314494, 114.592972], 13); // Centered on Banjarmasin

    // Base layers
    const openStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    });

    const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });

    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
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

    // Add zoom control
    L.control.zoom({
        position: 'topleft'
    }).addTo(map);

    // Define marker icons for different facilities
    const icons = {
        'Apotek': L.icon({
            iconUrl: '/images/markers/apotek-marker.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        }),
        'Klinik': L.icon({
            iconUrl: '/images/markers/klinik-marker.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        }),
        'Rumah Sakit': L.icon({
            iconUrl: '/images/markers/rumahsakit-marker.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        }),
        'Puskesmas': L.icon({
            iconUrl: '/images/markers/puskesmas-marker.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        })
    };

    // Layer groups for facilities
    const facilityLayers = {
        'Apotek': L.layerGroup(),
        'Klinik': L.layerGroup(),
        'Rumah Sakit': L.layerGroup(),
        'Puskesmas': L.layerGroup()
    };

    // Add facility layers to map
    Object.values(facilityLayers).forEach(layer => layer.addTo(map));

    // Stores all markers for quick reference
    let allMarkers = [];

    // Function to load facility data
    async function loadFacilities(filters = {}) {
        try {
            // Clear existing markers
            allMarkers.forEach(marker => marker.remove());
            allMarkers = [];
            Object.values(facilityLayers).forEach(layer => layer.clearLayers());

            // Build query parameters
            const queryParams = new URLSearchParams();
            if (filters.kecamatan) queryParams.append('kecamatan', filters.kecamatan);
            if (filters.kelurahan) queryParams.append('kelurahan', filters.kelurahan);
            if (filters.search) queryParams.append('search', filters.search);
            if (filters.fasilitas && filters.fasilitas.length > 0) {
                filters.fasilitas.forEach(facility => {
                    queryParams.append('fasilitas[]', facility);
                });
            }

            // Fetch data from API
            const response = await fetch(`/api/faskes?${queryParams.toString()}`);
            const data = await response.json();

            // Process data and add markers
            data.forEach(item => {
                if (item.longitude && item.latitude) {
                    const icon = icons[item.fasilitas] || icons['Apotek']; // Default to Apotek icon
                    
                    const marker = L.marker([item.latitude, item.longitude], { icon: icon })
                        .bindPopup(`
                            <strong>${item.nama}</strong><br>
                            ${item.fasilitas}<br>
                            Alamat: ${item.alamat}<br>
                            Kecamatan: ${item.kecamatan}<br>
                            Kelurahan: ${item.kelurahan || 'N/A'}
                        `);
                    
                    // Add marker to appropriate layer
                    if (facilityLayers[item.fasilitas]) {
                        facilityLayers[item.fasilitas].addLayer(marker);
                    }
                    
                    allMarkers.push(marker);
                }
            });
            
            // If no markers were added, show an alert
            if (allMarkers.length === 0) {
                alert('Tidak ada fasilitas kesehatan yang ditemukan dengan filter yang diterapkan.');
            } else if (filters.kecamatan || filters.kelurahan) {
                // If filtering by area, fit bounds to visible markers
                const visibleMarkers = allMarkers.filter(marker => map.hasLayer(marker));
                if (visibleMarkers.length > 0) {
                    const group = L.featureGroup(visibleMarkers);
                    map.fitBounds(group.getBounds(), { padding: [50, 50] });
                }
            }
        } catch (error) {
            console.error('Error loading facilities:', error);
            alert('Terjadi kesalahan saat memuat data fasilitas kesehatan.');
        }
    }

    // GeoJSON style functions
    function getKecamatanColor(kecamatan) {
        // Generate colors for each kecamatan
        const colors = {
            'Banjarmasin Utara': '#FF5733',
            'Banjarmasin Selatan': '#33FF57',
            'Banjarmasin Tengah': '#3357FF',
            'Banjarmasin Timur': '#F033FF',
            'Banjarmasin Barat': '#FF9033'
        };
        return colors[kecamatan] || '#CCCCCC';
    }

    function getKelurahanColor(properties) {
        // Generate a slightly different color for each kelurahan within its kecamatan
        const baseColor = getKecamatanColor(properties.WADMKC);
        
        // Convert hex to RGB
        const r = parseInt(baseColor.slice(1, 3), 16);
        const g = parseInt(baseColor.slice(3, 5), 16);
        const b = parseInt(baseColor.slice(5, 7), 16);
        
        // Adjust brightness based on kelurahan name length (just for variation)
        const nameLengthFactor = properties.NAMOBJ ? properties.NAMOBJ.length % 5 : 0;
        const variationFactor = 0.15 * nameLengthFactor;
        
        // Calculate new RGB values
        const newR = Math.min(255, Math.max(0, Math.round(r * (1 + variationFactor))));
        const newG = Math.min(255, Math.max(0, Math.round(g * (1 + variationFactor))));
        const newB = Math.min(255, Math.max(0, Math.round(b * (1 + variationFactor))));
        
        // Convert back to hex
        return `#${newR.toString(16).padStart(2, '0')}${newG.toString(16).padStart(2, '0')}${newB.toString(16).padStart(2, '0')}`;
    }

    function styleKecamatan(feature) {
        return {
            fillColor: getKecamatanColor(feature.properties.NAMOBJ),
            weight: 2,
            opacity: 1,
            color: '#666',
            dashArray: '3',
            fillOpacity: 0.4
        };
    }

    function styleKelurahan(feature) {
        return {
            fillColor: getKelurahanColor(feature.properties),
            weight: 1,
            opacity: 1,
            color: '#666',
            dashArray: '2',
            fillOpacity: 0.3
        };
    }

    // Variables to store GeoJSON layers
    let kecamatanLayer;
    let kelurahanLayer;

    // Load GeoJSON boundaries
    async function loadBoundaries() {
        try {
            // Load Kecamatan boundaries
            const kecamatanResponse = await fetch('/pembagianwilayah.geojson');
            const kecamatanData = await kecamatanResponse.json();
            
            kecamatanLayer = L.geoJSON(kecamatanData, {
                style: styleKecamatan,
                onEachFeature: function(feature, layer) {
                    if (feature.properties && feature.properties.NAMOBJ) {
                        layer.bindTooltip(feature.properties.NAMOBJ);
                        
                        // Add click event to zoom to kecamatan
                        layer.on('click', function() {
                            const kecamatanName = feature.properties.NAMOBJ;
                            document.getElementById('kecamatan-select').value = kecamatanName;
                            document.getElementById('kelurahan-select').value = '';
                            
                            // Apply filter
                            applyFilters();
                            
                            // Zoom to this kecamatan
                            map.fitBounds(layer.getBounds());
                        });
                    }
                }
            }).addTo(map);
            
            // Load Kelurahan boundaries
            const kelurahanResponse = await fetch('/batas_kelurahan.geojson');
            const kelurahanData = await kelurahanResponse.json();
            
            kelurahanLayer = L.geoJSON(kelurahanData, {
                style: styleKelurahan,
                onEachFeature: function(feature, layer) {
                    if (feature.properties && feature.properties.WADMKD) {
                        layer.bindTooltip(feature.properties.WADMKD);
                        
                        // Add click event to zoom to kelurahan
                        layer.on('click', function() {
                            const kelurahanName = feature.properties.WADMKD;
                            const kecamatanName = feature.properties.WADMKC;
                            
                            document.getElementById('kecamatan-select').value = kecamatanName;
                            document.getElementById('kelurahan-select').value = kelurahanName;
                            
                            // Apply filter
                            applyFilters();
                            
                            // Zoom to this kelurahan
                            map.fitBounds(layer.getBounds());
                        });
                    }
                }
            }).addTo(map);
            
            // Create kecamatan legend
            createKecamatanLegend();
        } catch (error) {
            console.error('Error loading boundaries:', error);
            alert('Terjadi kesalahan saat memuat peta batas wilayah.');
        }
    }

    // Create kecamatan legend
    function createKecamatanLegend() {
        const legendContainer = document.getElementById('kecamatan-legend');
        legendContainer.innerHTML = '<h6>Kecamatan</h6>';
        
        const kecamatanNames = [
            'Banjarmasin Utara',
            'Banjarmasin Selatan',
            'Banjarmasin Tengah',
            'Banjarmasin Timur',
            'Banjarmasin Barat'
        ];
        
        kecamatanNames.forEach(kecamatan => {
            const color = getKecamatanColor(kecamatan);
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';
            legendItem.innerHTML = `
                <div class="legend-color" style="background-color: ${color};"></div>
                <span>${kecamatan}</span>
            `;
            legendContainer.appendChild(legendItem);
        });
    }

    // Filter facilities
    function applyFilters() {
        const kecamatan = document.getElementById('kecamatan-select').value;
        const kelurahan = document.getElementById('kelurahan-select').value;
        const searchTerm = document.getElementById('search-faskes').value;
        
        const selectedFacilities = [];
        if (document.getElementById('apotek-filter').checked) selectedFacilities.push('Apotek');
        if (document.getElementById('klinik-filter').checked) selectedFacilities.push('Klinik');
        if (document.getElementById('rumahsakit-filter').checked) selectedFacilities.push('Rumah Sakit');
        if (document.getElementById('puskesmas-filter').checked) selectedFacilities.push('Puskesmas');
        
        // Hide/show facility layers based on checkboxes
        Object.entries(facilityLayers).forEach(([facilityType, layer]) => {
            if (selectedFacilities.includes(facilityType)) {
                map.addLayer(layer);
            } else {
                map.removeLayer(layer);
            }
        });
        
        // Load facilities with filters
        loadFacilities({
            kecamatan: kecamatan,
            kelurahan: kelurahan,
            search: searchTerm,
            fasilitas: selectedFacilities
        });
    }

    // Link kelurahan dropdown to kecamatan selection
    document.getElementById('kecamatan-select').addEventListener('change', function() {
        const selectedKecamatan = this.value;
        const kelurahanSelect = document.getElementById('kelurahan-select');
        
        // Reset kelurahan selection
        kelurahanSelect.value = '';
        
        // Hide/show kelurahan options based on selected kecamatan
        Array.from(kelurahanSelect.options).forEach(option => {
            if (option.value === '') return; // Skip the "All" option
            
            if (!selectedKecamatan || option.dataset.kecamatan === selectedKecamatan) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        applyFilters();
    });

    // Event listeners
    document.getElementById('kelurahan-select').addEventListener('change', applyFilters);
    document.getElementById('search-button').addEventListener('click', applyFilters);
    document.getElementById('search-faskes').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            applyFilters();
        }
    });
    
    // Facility checkbox event listeners
    ['apotek', 'klinik', 'rumahsakit', 'puskesmas'].forEach(facility => {
        document.getElementById(`${facility}-filter`).addEventListener('change', applyFilters);
    });
    
    // Reset filters
    document.getElementById('reset-filter').addEventListener('click', function() {
        document.getElementById('kecamatan-select').value = '';
        document.getElementById('kelurahan-select').value = '';
        document.getElementById('search-faskes').value = '';
        
        // Reset checkboxes
        ['apotek', 'klinik', 'rumahsakit', 'puskesmas'].forEach(facility => {
            document.getElementById(`${facility}-filter`).checked = true;
        });
        
        // Reset map view
        map.setView([-3.314494, 114.592972], 13);
        
        // Apply reset filters
        applyFilters();
    });

    // Initialize map
    loadBoundaries();
    loadFacilities();
});