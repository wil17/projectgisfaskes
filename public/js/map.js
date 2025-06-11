// map.js - Inisialisasi filter peta dan interaksi UI
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inisialisasi filter peta...');
    initializeMapFilters();
});

// Fungsi untuk menerapkan filter ke peta
function applyFilters() {
    console.log('=== APPLY FILTERS ===');
    
     // Buat objek filter untuk dikirim ke API
    const filterObject = {};
    
    // Filter kecamatan (hanya jika tidak semua dipilih)
    if (window.selectedKecamatanList.length > 0) {
        filterObject.kecamatan = window.selectedKecamatanList;
    }
    
    // Filter kelurahan (hanya jika tidak semua dipilih)
    if (window.selectedKelurahanList.length > 0) {
        filterObject.kelurahan = window.selectedKelurahanList;
    }
    
    // Filter jenis fasilitas kesehatan
    const selectedFacilities = [];
    const facilityMapping = {
        'apotek-filter': ['Apotek'],
        'klinik-filter': ['Klinik'],
        'rumahsakit-filter': ['Rumah Sakit', 'RS', 'Hospital'],
        'puskesmas-filter': ['Puskesmas', 'PKM']
    };
    
     // Periksa status checkbox fasilitas
    Object.keys(facilityMapping).forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement && filterElement.checked) {
            selectedFacilities.push(...facilityMapping[filterId]);
        }
    });
    
    filterObject.fasilitas = selectedFacilities;
    
    // Filter pencarian berdasarkan input user
    const searchInput = document.getElementById('search-faskes');
    const searchTerm = searchInput ? searchInput.value.trim() : '';
    if (searchTerm) {
        filterObject.search = searchTerm;
    }
    
    // Debug: Tampilkan filter yang diterapkan
    console.log('=== FILTER DEBUG ===');
    console.log('- Kecamatan:', window.selectedKecamatanList);
    console.log('- Kelurahan:', window.selectedKelurahanList);
    console.log('- Fasilitas dipilih:', selectedFacilities);
    console.log('- Kata kunci:', searchTerm);
    console.log('- Status checkbox fasilitas:');
    Object.keys(facilityMapping).forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        console.log(`  - ${filterId}: ${filterElement ? filterElement.checked : 'element not found'}`);
    });
    console.log('=== END FILTER DEBUG ===');
    
    // Log informasi filter untuk debugging
    console.log('Filter object untuk API:', filterObject);
    
    // Panggil fungsi untuk memperbarui marker peta
    if (window.loadFacilities && typeof window.loadFacilities === 'function') {
        window.loadFacilities(filterObject);
    } else {
        console.error('Error: window.loadFacilities tidak tersedia');
    }
    
    // Update tampilan polygon kecamatan
    if (window.updateKecamatanPolygons && typeof window.updateKecamatanPolygons === 'function') {
        window.updateKecamatanPolygons(window.selectedKecamatanList);
    } else {
        console.error('Error: window.updateKecamatanPolygons tidak tersedia');
    }
    
    // Update tampilan polygon kelurahan
    if (window.updateKelurahanPolygons && typeof window.updateKelurahanPolygons === 'function') {
        window.updateKelurahanPolygons(window.selectedKelurahanList, window.selectedKecamatanList);
    } else {
        console.error('Error: window.updateKelurahanPolygons tidak tersedia');
    }
    
    // Zoom ke area yang dipilih setelah filter diterapkan
    zoomToSelectedAreas(window.selectedKecamatanList, window.selectedKelurahanList);
    
    console.log('=== END APPLY FILTERS ===');
}

// Fungsi baru untuk zoom ke area yang dipilih
function zoomToSelectedAreas(kecamatanList, kelurahanList) {
    console.log('Zoom to selected areas:', kecamatanList, kelurahanList);
    
    // Jika tidak ada yang dipilih, tidak perlu melakukan zoom
    if ((kecamatanList.length === 0 && kelurahanList.length === 0) || 
        !window.mapInstance) {
        return;
    }
    
    // Prioritaskan kelurahan jika ada yang dipilih
    if (kelurahanList.length > 0) {
        // Cari bounds untuk kelurahan yang dipilih
        const bounds = [];
        
        kelurahanList.forEach(kelurahanName => {
            // Cari kelurahan di semua kecamatan
            Object.keys(window.kelurahanByKecamatan || {}).forEach(kecamatanName => {
                if (window.kelurahanByKecamatan[kecamatanName].includes(kelurahanName)) {
                    const key = `${kecamatanName}-${kelurahanName}`;
                    if (window.kelurahanPolygons && window.kelurahanPolygons[key]) {
                        const polygon = window.kelurahanPolygons[key];
                        bounds.push(polygon.getBounds());
                    }
                }
            });
        });
        
        if (bounds.length > 0) {
            // Gabungkan semua bounds dan zoom ke bounds gabungan
            let combinedBounds = bounds[0];
            for (let i = 1; i < bounds.length; i++) {
                combinedBounds = combinedBounds.extend(bounds[i]);
            }
            
            window.mapInstance.fitBounds(combinedBounds, {
                padding: [50, 50],
                maxZoom: 15
            });
            return;
        }
    }
    
    // Jika tidak ada kelurahan yang ditemukan atau dipilih, zoom ke kecamatan
    if (kecamatanList.length > 0) {
        const bounds = [];
        
        kecamatanList.forEach(kecamatanName => {
            if (window.kecamatanPolygons && window.kecamatanPolygons[kecamatanName]) {
                const polygon = window.kecamatanPolygons[kecamatanName];
                bounds.push(polygon.getBounds());
            }
        });
        
        if (bounds.length > 0) {
            let combinedBounds = bounds[0];
            for (let i = 1; i < bounds.length; i++) {
                combinedBounds = combinedBounds.extend(bounds[i]);
            }
            
            window.mapInstance.fitBounds(combinedBounds, {
                padding: [50, 50],
                maxZoom: 14
            });
        }
    }
}

// Inisialisasi filter peta dan interaksi UI
function initializeMapFilters() {
    // Inisialisasi variabel untuk menyimpan pilihan kecamatan dan kelurahan
    window.selectedKecamatanList = [];
    window.selectedKelurahanList = [];
    
    // ========== DESKTOP FILTER HANDLERS ==========
    
    // Handler untuk checkbox "Semua Kecamatan"
    const selectAllKecamatan = document.getElementById('select-all-kecamatan');
    if (selectAllKecamatan) {
        // Ensure it's unchecked by default
        selectAllKecamatan.checked = false;
        
        selectAllKecamatan.addEventListener('change', function() {
            console.log('handleKecamatanSelection dipanggil');
            console.log('Select All Kecamatan checked:', this.checked);
            
            // Update semua checkbox kecamatan sesuai dengan pilihan "Semua Kecamatan"
            const kecamatanCheckboxes = document.querySelectorAll('.kecamatan-checkbox');
            kecamatanCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            // Update selectedKecamatanList
            if (this.checked) {
                // Jika "Semua Kecamatan" dipilih, kosongkan list (menandakan semua dipilih)
                window.selectedKecamatanList = [];
                console.log('Semua kecamatan dipilih - selectedKecamatanList:', window.selectedKecamatanList);
            } else {
                // Jika "Semua Kecamatan" tidak dipilih, tidak ada kecamatan yang dipilih
                window.selectedKecamatanList = [];
                console.log('Tidak ada kecamatan dipilih - selectedKecamatanList:', window.selectedKecamatanList);
                
                // Clear all markers when unchecked
                clearAllMarkers();
            }
            
            // Update kelurahan checkboxes berdasarkan kecamatan yang dipilih
            updateKelurahanCheckboxes();
            
            // Terapkan filter
            applyFilters();
        });
    }
    
    // Initialize all kecamatan checkboxes to unchecked
    const kecamatanCheckboxes = document.querySelectorAll('.kecamatan-checkbox');
    kecamatanCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        checkbox.addEventListener('change', function() {
            handleIndividualKecamatanSelection();
        });
    });
    
    // Handler untuk checkbox "Semua Kelurahan"
    const selectAllKelurahan = document.getElementById('select-all-kelurahan');
    if (selectAllKelurahan) {
        selectAllKelurahan.addEventListener('change', function() {
            console.log('handleKelurahanSelection dipanggil');
            console.log('Select All Kelurahan checked:', this.checked);
            
            // Update semua checkbox kelurahan sesuai dengan pilihan "Semua Kelurahan"
            const kelurahanCheckboxes = document.querySelectorAll('.kelurahan-checkbox');
            kelurahanCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            // Update selectedKelurahanList
            if (this.checked) {
                // Jika "Semua Kelurahan" dipilih, kosongkan list (menandakan semua dipilih)
                window.selectedKelurahanList = [];
                console.log('Semua kelurahan dipilih - selectedKelurahanList:', window.selectedKelurahanList);
            } else {
                // Jika "Semua Kelurahan" tidak dipilih, tidak ada kelurahan yang dipilih
                window.selectedKelurahanList = [];
                console.log('Tidak ada kelurahan dipilih - selectedKelurahanList:', window.selectedKelurahanList);
            }
            
            // Terapkan filter
            applyFilters();
        });
    }
    
    // Handler untuk setiap checkbox kelurahan yang akan dibuat dinamis
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('kelurahan-checkbox')) {
            handleIndividualKelurahanSelection();
        }
    });
    
    // ========== MOBILE FILTER HANDLERS ==========
    
    // Handler untuk checkbox "Semua Kecamatan" di mobile
    const mobileSelectAllKecamatan = document.getElementById('mobile-select-all-kecamatan');
    if (mobileSelectAllKecamatan) {
        // Ensure it's unchecked by default
        mobileSelectAllKecamatan.checked = false;
        
        mobileSelectAllKecamatan.addEventListener('change', function() {
            // Update semua checkbox kecamatan mobile sesuai dengan pilihan "Semua Kecamatan"
            const mobileKecamatanCheckboxes = document.querySelectorAll('.mobile-kecamatan-checkbox');
            mobileKecamatanCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            // Update kelurahan checkboxes mobile
            updateMobileKelurahanCheckboxes();
        });
    }
    
    // Handler untuk checkbox "Semua Kelurahan" di mobile
    const mobileSelectAllKelurahan = document.getElementById('mobile-select-all-kelurahan');
    if (mobileSelectAllKelurahan) {
        mobileSelectAllKelurahan.addEventListener('change', function() {
            // Update semua checkbox kelurahan mobile sesuai dengan pilihan "Semua Kelurahan"
            const mobileKelurahanCheckboxes = document.querySelectorAll('.mobile-kelurahan-checkbox');
            mobileKelurahanCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Initialize all mobile kecamatan checkboxes to unchecked
    document.querySelectorAll('.mobile-kecamatan-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.addEventListener('change', function() {
            console.log('Mobile Individual Kecamatan checkbox changed:', this.value, this.checked);
            
            // Jika ada individual checkbox yang di-uncheck, uncheck "Select All"
            const mobileSelectAllKecamatan = document.getElementById('mobile-select-all-kecamatan');
            if (!this.checked && mobileSelectAllKecamatan && mobileSelectAllKecamatan.checked) {
                mobileSelectAllKecamatan.checked = false;
            }
            
            // Update kelurahan checkboxes mobile berdasarkan kecamatan yang dipilih
            updateMobileKelurahanCheckboxes();
        });
    });
    
    // Sinkronisasi filter desktop dan mobile saat modal ditutup
    const mobileFilterModal = document.getElementById('mobile-filter-modal');
    if (mobileFilterModal) {
        mobileFilterModal.addEventListener('hidden.bs.modal', function() {
            // Sinkronkan pilihan kecamatan
            syncMobileToDesktopKecamatan();
            
            // Sinkronkan pilihan kelurahan
            syncMobileToDesktopKelurahan();
            
            // Sinkronkan pilihan fasilitas
            syncMobileToDesktopFacilities();
            
            // Sinkronkan pilihan batas kota
            const cityBoundaryFilter = document.getElementById('city-boundary-filter');
            const mobileCityBoundaryFilter = document.getElementById('mobile-city-boundary-filter');
            if (cityBoundaryFilter && mobileCityBoundaryFilter) {
                cityBoundaryFilter.checked = mobileCityBoundaryFilter.checked;
            }
            
            // Sinkronkan pencarian
            const searchFaskes = document.getElementById('search-faskes');
            const mobileSearchFaskes = document.getElementById('mobile-search-faskes');
            if (searchFaskes && mobileSearchFaskes) {
                searchFaskes.value = mobileSearchFaskes.value;
            }
            
            // Terapkan filter
            applyFilters();
        });
    }
    
    // Button untuk membuka modal filter mobile
    const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
    if (mobileFilterToggle) {
        mobileFilterToggle.addEventListener('click', function() {
            // Sinkronkan pilihan desktop ke mobile sebelum modal dibuka
            syncDesktopToMobileKecamatan();
            syncDesktopToMobileKelurahan();
            syncDesktopToMobileFacilities();
            
            const cityBoundaryFilter = document.getElementById('city-boundary-filter');
            const mobileCityBoundaryFilter = document.getElementById('mobile-city-boundary-filter');
            if (cityBoundaryFilter && mobileCityBoundaryFilter) {
                mobileCityBoundaryFilter.checked = cityBoundaryFilter.checked;
            }
            
            const searchFaskes = document.getElementById('search-faskes');
            const mobileSearchFaskes = document.getElementById('mobile-search-faskes');
            if (searchFaskes && mobileSearchFaskes) {
                mobileSearchFaskes.value = searchFaskes.value;
            }
            
            // Buka modal
            const mobileFilterModal = new bootstrap.Modal(document.getElementById('mobile-filter-modal'));
            mobileFilterModal.show();
        });
    }
    
    // ========== FACILITY FILTER HANDLERS ==========
    
    // Handler untuk filter fasilitas (desktop)
    const facilityFilters = ['apotek-filter', 'klinik-filter', 'rumahsakit-filter', 'puskesmas-filter'];
    facilityFilters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', applyFilters);
        }
    });
    
    // Handler untuk filter batas kota
    const cityBoundaryFilter = document.getElementById('city-boundary-filter');
    if (cityBoundaryFilter) {
        cityBoundaryFilter.addEventListener('change', function() {
            if (typeof window.toggleCityBoundary === 'function') {
                window.toggleCityBoundary(this.checked);
            }
        });
    }
    
    // Handler untuk search box
    const searchInput = document.getElementById('search-faskes');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            // Terapkan filter saat user menekan Enter
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
        
        // Real-time search dengan debounce
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 300);
        });
    }
    
    // Mobile search dengan debounce
    const mobileSearchInput = document.getElementById('mobile-search-faskes');
    if (mobileSearchInput) {
        let mobileSearchTimeout;
        mobileSearchInput.addEventListener('input', function() {
            clearTimeout(mobileSearchTimeout);
            mobileSearchTimeout = setTimeout(() => {
                // Sync search to desktop and apply
                const desktopSearch = document.getElementById('search-faskes');
                if (desktopSearch) {
                    desktopSearch.value = this.value;
                }
                applyFilters();
            }, 300);
        });
    }
    
    // Handler untuk tombol reset filter
    const resetFilter = document.getElementById('reset-filter');
    if (resetFilter) {
        resetFilter.addEventListener('click', resetFilters);
    }
    
    const mobileResetFilter = document.getElementById('mobile-reset-filter');
    if (mobileResetFilter) {
        mobileResetFilter.addEventListener('click', resetMobileFilters);
    }
    
    // Event listeners untuk checkbox jenis fasilitas mobile
    facilityFilters.forEach(filterId => {
        const mobileFilterId = 'mobile-' + filterId;
        const mobileFilterElement = document.getElementById(mobileFilterId);
        if (mobileFilterElement) {
            mobileFilterElement.addEventListener('change', function() {
                // Sinkronkan dengan desktop
                const desktopFilterElement = document.getElementById(filterId);
                if (desktopFilterElement) {
                    desktopFilterElement.checked = this.checked;
                }
                applyFilters();
            });
        }
    });
    
    // City boundary toggle event listener mobile
    const mobileCityBoundaryFilter = document.getElementById('mobile-city-boundary-filter');
    if (mobileCityBoundaryFilter) {
        mobileCityBoundaryFilter.addEventListener('change', function() {
            if (typeof window.toggleCityBoundary === 'function') {
                window.toggleCityBoundary(this.checked);
            }
            // Sync to desktop
            const desktopCityBoundary = document.getElementById('city-boundary-filter');
            if (desktopCityBoundary) {
                desktopCityBoundary.checked = this.checked;
            }
        });
    }
    
 // ========== POPULATE KELURAHAN CHECKBOXES ==========
    
    // Inisialisasi tampilan kelurahan
    updateKelurahanCheckboxes();
    
    // Clear all markers initially
    if (typeof window.clearAllMarkers === 'function') {
        window.clearAllMarkers();
    }
    
    console.log('Filter peta berhasil diinisialisasi');
}

// Function to clear all markers
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

// Fungsi untuk menangani pilihan kecamatan individual
function handleIndividualKecamatanSelection() {
    // Ambil semua checkbox kecamatan
    const kecamatanCheckboxes = document.querySelectorAll('.kecamatan-checkbox');
    
    // Reset dan isi ulang kecamatan yang dipilih
    window.selectedKecamatanList = [];
    
    // Periksa status semua checkbox
    let allChecked = true;
    let noneChecked = true;
    
    // Periksa status masing-masing checkbox
    kecamatanCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            noneChecked = false;
            // Tambahkan ke list hanya jika tidak dipilih semua
            window.selectedKecamatanList.push(checkbox.value);
        } else {
            allChecked = false;
        }
    });
    
    // Update status checkbox "Semua Kecamatan" secara otomatis
    const selectAllKecamatan = document.getElementById('select-all-kecamatan');
    if (selectAllKecamatan) {
        selectAllKecamatan.checked = allChecked;
    }
    
    // Jika semua dipilih, kosongkan list untuk menandakan "semua"
    if (allChecked) {
        window.selectedKecamatanList = [];
    }
    
    // Jika tidak ada yang dipilih, kosongkan list dan bersihkan markers
    if (noneChecked) {
        window.selectedKecamatanList = [];
        // Kosongkan semua markers jika tidak ada kecamatan yang dipilih 
        clearAllMarkers();
    }
    
    console.log('selectedKecamatanList setelah perubahan:', window.selectedKecamatanList);
    
    // Update kelurahan checkbox berdasarkan kecamatan yang dipilih
    updateKelurahanCheckboxes();
    
    // Terapkan filter
    applyFilters();
}

// Fungsi untuk menangani perubahan pada checkbox kelurahan individu
function handleIndividualKelurahanSelection() {
    // Ambil semua checkbox kelurahan yang ditampilkan
    const kelurahanCheckboxes = document.querySelectorAll('.kelurahan-checkbox');
    
    // Reset dan isi ulang selectedKelurahanList
    window.selectedKelurahanList = [];
    
    // Cek apakah semua checkbox dipilih
    let allChecked = true;
    let noneChecked = true;
    
    // Periksa status masing-masing checkbox yang ditampilkan
    kelurahanCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            noneChecked = false;
            // Tambahkan ke list hanya jika tidak dipilih semua
            window.selectedKelurahanList.push(checkbox.value);
        } else {
            allChecked = false;
        }
    });
    
    // Update checkbox "Semua Kelurahan" secara otomatis
    const selectAllKelurahan = document.getElementById('select-all-kelurahan');
    if (selectAllKelurahan) {
        selectAllKelurahan.checked = allChecked;
    }
    
    // Jika semua dipilih, kosongkan list untuk menandakan "semua"
    if (allChecked) {
        window.selectedKelurahanList = [];
    }
    
    // Jika tidak ada yang dipilih, kosongkan list
    if (noneChecked) {
        window.selectedKelurahanList = [];
    }
    
    console.log('selectedKelurahanList setelah perubahan:', window.selectedKelurahanList);
    
    // Terapkan filter
    applyFilters();
}

// Fungsi untuk memperbarui daftar kelurahan berdasarkan kecamatan yang dipilih
function updateKelurahanCheckboxes() {
    console.log('Memperbarui checkbox kelurahan...');
    console.log('selectedKecamatanList saat ini:', window.selectedKecamatanList);
    
    // Ambil container untuk checkbox kelurahan
    const kelurahanContainer = document.getElementById('kelurahan-checkbox-container');
    const mobileKelurahanContainer = document.getElementById('mobile-kelurahan-checkbox-container');
    
    if (!kelurahanContainer || !mobileKelurahanContainer) {
        console.error('Container kelurahan tidak ditemukan');
        return;
    }
    
    // Kosongkan container
    kelurahanContainer.innerHTML = '';
    mobileKelurahanContainer.innerHTML = '';
    
    // Cek apakah semua kecamatan dipilih (list kosong) atau kecamatan tertentu dipilih
    const selectAllKecamatan = document.getElementById('select-all-kecamatan');
    const selectAllKelurahan = document.getElementById('select-all-kelurahan');
    const mobileSelectAllKelurahan = document.getElementById('mobile-select-all-kelurahan');
    
    if (window.selectedKecamatanList.length === 0) {
        // Jika semua kecamatan dipilih atau tidak ada yang dipilih
        if (selectAllKecamatan && selectAllKecamatan.checked) {
            console.log('Menampilkan semua kelurahan dari semua kecamatan');
            
            // Ambil semua kelurahan dari seluruh kecamatan
            const allKelurahan = window.allKelurahanNames || [];
            
            // Tampilkan semua kelurahan
            console.log('Kelurahan yang akan ditampilkan:', allKelurahan);
            
            // Buat checkbox untuk setiap kelurahan
            allKelurahan.forEach(kelurahan => {
                // Buat checkbox untuk desktop
                const checkboxDiv = document.createElement('div');
                checkboxDiv.className = 'filter-checkbox';
                checkboxDiv.innerHTML = `
                    <input type="checkbox" id="kelurahan-${kelurahan.replace(/\s+/g, '-')}" 
                           class="kelurahan-checkbox" value="${kelurahan}" ${selectAllKelurahan && selectAllKelurahan.checked ? 'checked' : ''}>
                    <label for="kelurahan-${kelurahan.replace(/\s+/g, '-')}">${kelurahan}</label>
                `;
                kelurahanContainer.appendChild(checkboxDiv);
                
                // Buat checkbox untuk mobile
                const mobileCheckboxDiv = document.createElement('div');
                mobileCheckboxDiv.className = 'filter-checkbox';
                mobileCheckboxDiv.innerHTML = `
                    <input type="checkbox" id="mobile-kelurahan-${kelurahan.replace(/\s+/g, '-')}" 
                           class="mobile-kelurahan-checkbox" value="${kelurahan}" ${mobileSelectAllKelurahan && mobileSelectAllKelurahan.checked ? 'checked' : ''}>
                    <label for="mobile-kelurahan-${kelurahan.replace(/\s+/g, '-')}">${kelurahan}</label>
                `;
                mobileKelurahanContainer.appendChild(mobileCheckboxDiv);
            });
        } else {
            // Tidak ada kecamatan yang dipilih, tampilkan pesan
            kelurahanContainer.innerHTML = '<div class="text-muted">Pilih minimal satu kecamatan</div>';
            mobileKelurahanContainer.innerHTML = '<div class="text-muted">Pilih minimal satu kecamatan</div>';
        }
    } else {
        // Kecamatan tertentu dipilih, tampilkan kelurahan dari kecamatan yang dipilih
        console.log('Menampilkan kelurahan dari kecamatan yang dipilih:', window.selectedKecamatanList);
        
        // Kumpulkan kelurahan dari kecamatan yang dipilih
        let filteredKelurahan = [];
        
        window.selectedKecamatanList.forEach(kecamatan => {
            if (window.kelurahanByKecamatan && window.kelurahanByKecamatan[kecamatan]) {
                filteredKelurahan = filteredKelurahan.concat(window.kelurahanByKecamatan[kecamatan]);
            }
        });
        
        // Hapus duplikat
        filteredKelurahan = [...new Set(filteredKelurahan)];
        
        // Urutkan
        filteredKelurahan.sort();
        
        console.log('Kelurahan yang akan ditampilkan:', filteredKelurahan);
        
        // Buat checkbox untuk setiap kelurahan yang difilter
        filteredKelurahan.forEach(kelurahan => {
            // Buat checkbox untuk desktop
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'filter-checkbox';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="kelurahan-${kelurahan.replace(/\s+/g, '-')}" 
                       class="kelurahan-checkbox" value="${kelurahan}" ${selectAllKelurahan && selectAllKelurahan.checked ? 'checked' : ''}>
                <label for="kelurahan-${kelurahan.replace(/\s+/g, '-')}">${kelurahan}</label>
            `;
            kelurahanContainer.appendChild(checkboxDiv);
            
            // Buat checkbox untuk mobile
            const mobileCheckboxDiv = document.createElement('div');
            mobileCheckboxDiv.className = 'filter-checkbox';
            mobileCheckboxDiv.innerHTML = `
                <input type="checkbox" id="mobile-kelurahan-${kelurahan.replace(/\s+/g, '-')}" 
                       class="mobile-kelurahan-checkbox" value="${kelurahan}" ${mobileSelectAllKelurahan && mobileSelectAllKelurahan.checked ? 'checked' : ''}>
                <label for="mobile-kelurahan-${kelurahan.replace(/\s+/g, '-')}">${kelurahan}</label>
            `;
            mobileKelurahanContainer.appendChild(mobileCheckboxDiv);
            });
    }
    
    // Tambahkan event listener untuk checkbox kelurahan yang baru dibuat
    const kelurahanCheckboxes = document.querySelectorAll('.kelurahan-checkbox');
    kelurahanCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', handleIndividualKelurahanSelection);
    });
    
    // Tambahkan event listener untuk checkbox kelurahan mobile yang baru dibuat
    const mobileKelurahanCheckboxes = document.querySelectorAll('.mobile-kelurahan-checkbox');
    mobileKelurahanCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Ini hanya untuk sinkronisasi UI, filter akan diterapkan saat modal ditutup
        });
    });
    
    console.log('Checkbox kelurahan berhasil diperbarui');
}

// Fungsi untuk menyinkronkan pilihan kecamatan dari desktop ke mobile
function syncDesktopToMobileKecamatan() {
    const selectAllDesktop = document.getElementById('select-all-kecamatan');
    const mobileSelectAllKecamatan = document.getElementById('mobile-select-all-kecamatan');
    
    if (selectAllDesktop && mobileSelectAllKecamatan) {
        mobileSelectAllKecamatan.checked = selectAllDesktop.checked;
    }
    
    const kecamatanCheckboxes = document.querySelectorAll('.kecamatan-checkbox');
    kecamatanCheckboxes.forEach(checkbox => {
        const mobileCheckbox = document.getElementById('mobile-' + checkbox.id);
        if (mobileCheckbox) {
            mobileCheckbox.checked = checkbox.checked;
        }
    });
}

// Fungsi untuk menyinkronkan pilihan kecamatan dari mobile ke desktop
function syncMobileToDesktopKecamatan() {
    const selectAllMobile = document.getElementById('mobile-select-all-kecamatan');
    const selectAllDesktop = document.getElementById('select-all-kecamatan');
    
    if (selectAllMobile && selectAllDesktop) {
        selectAllDesktop.checked = selectAllMobile.checked;
    }
    
    const mobileKecamatanCheckboxes = document.querySelectorAll('.mobile-kecamatan-checkbox');
    
    // Reset dan update selectedKecamatanList
    window.selectedKecamatanList = [];
    
    mobileKecamatanCheckboxes.forEach(mobileCheckbox => {
        const desktopCheckboxId = mobileCheckbox.id.replace('mobile-', '');
        const desktopCheckbox = document.getElementById(desktopCheckboxId);
        
        if (desktopCheckbox) {
            desktopCheckbox.checked = mobileCheckbox.checked;
            
            // Update selectedKecamatanList jika checkbox dipilih
            if (mobileCheckbox.checked && !(selectAllMobile && selectAllMobile.checked)) {
                window.selectedKecamatanList.push(mobileCheckbox.value);
            }
        }
    });
    
    // Jika semua dipilih, kosongkan list
    if (selectAllMobile && selectAllMobile.checked) {
        window.selectedKecamatanList = [];
    }
    
    // Update kelurahan berdasarkan kecamatan yang dipilih
    updateKelurahanCheckboxes();
}

// Fungsi untuk menyinkronkan pilihan kelurahan dari desktop ke mobile
function syncDesktopToMobileKelurahan() {
    const selectAllDesktop = document.getElementById('select-all-kelurahan');
    const mobileSelectAllKelurahan = document.getElementById('mobile-select-all-kelurahan');
    
    if (selectAllDesktop && mobileSelectAllKelurahan) {
        mobileSelectAllKelurahan.checked = selectAllDesktop.checked;
    }
    
    const kelurahanCheckboxes = document.querySelectorAll('.kelurahan-checkbox');
    kelurahanCheckboxes.forEach(checkbox => {
        const mobileCheckbox = document.getElementById('mobile-' + checkbox.id);
        if (mobileCheckbox) {
            mobileCheckbox.checked = checkbox.checked;
        }
    });
}

// Fungsi untuk menyinkronkan pilihan kelurahan dari mobile ke desktop
function syncMobileToDesktopKelurahan() {
    const selectAllMobile = document.getElementById('mobile-select-all-kelurahan');
    const selectAllDesktop = document.getElementById('select-all-kelurahan');
    
    if (selectAllMobile && selectAllDesktop) {
        selectAllDesktop.checked = selectAllMobile.checked;
    }
    
    const mobileKelurahanCheckboxes = document.querySelectorAll('.mobile-kelurahan-checkbox');
    
    // Reset dan update selectedKelurahanList
    window.selectedKelurahanList = [];
    
    mobileKelurahanCheckboxes.forEach(mobileCheckbox => {
        const desktopCheckboxId = mobileCheckbox.id.replace('mobile-', '');
        const desktopCheckbox = document.getElementById(desktopCheckboxId);
        
        if (desktopCheckbox) {
            desktopCheckbox.checked = mobileCheckbox.checked;
            
            // Update selectedKelurahanList jika checkbox dipilih
            if (mobileCheckbox.checked && !(selectAllMobile && selectAllMobile.checked)) {
                window.selectedKelurahanList.push(mobileCheckbox.value);
            }
        }
    });
    
    // Jika semua dipilih, kosongkan list
    if (selectAllMobile && selectAllMobile.checked) {
        window.selectedKelurahanList = [];
    }
}

// Fungsi untuk menyinkronkan pilihan fasilitas dari desktop ke mobile
function syncDesktopToMobileFacilities() {
    const facilityFilters = ['apotek-filter', 'klinik-filter', 'rumahsakit-filter', 'puskesmas-filter'];
    
    facilityFilters.forEach(filterId => {
        const desktopFilter = document.getElementById(filterId);
        const mobileFilter = document.getElementById('mobile-' + filterId);
        
        if (desktopFilter && mobileFilter) {
            mobileFilter.checked = desktopFilter.checked;
        }
    });
}

// Fungsi untuk menyinkronkan pilihan fasilitas dari mobile ke desktop
function syncMobileToDesktopFacilities() {
    const facilityFilters = ['apotek-filter', 'klinik-filter', 'rumahsakit-filter', 'puskesmas-filter'];
    
    facilityFilters.forEach(filterId => {
        const desktopFilter = document.getElementById(filterId);
        const mobileFilter = document.getElementById('mobile-' + filterId);
        
        if (desktopFilter && mobileFilter) {
            desktopFilter.checked = mobileFilter.checked;
        }
    });
}

// Fungsi untuk mereset semua filter ke default
function resetFilters() {
    console.log('Reset filters dipanggil');
    
    // Reset daftar yang dipilih
    window.selectedKecamatanList = [];
    window.selectedKelurahanList = [];
    
    // Reset checkbox 'Semua' - set to unchecked by default
    const selectAllKecamatan = document.getElementById('select-all-kecamatan');
    const selectAllKelurahan = document.getElementById('select-all-kelurahan');
    
    if (selectAllKecamatan) selectAllKecamatan.checked = false;
    if (selectAllKelurahan) selectAllKelurahan.checked = false;
    
    // Reset semua checkbox kecamatan - set to unchecked by default
    const kecamatanCheckboxes = document.querySelectorAll('.kecamatan-checkbox');
    kecamatanCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Update kelurahan checkboxes dan reset
    updateKelurahanCheckboxes();
    
    // Reset input pencarian
    const searchFaskes = document.getElementById('search-faskes');
    if (searchFaskes) searchFaskes.value = '';
    
    // Reset fasilitas checkbox - set to unchecked by default (changed from checked)
    const facilityFilters = ['apotek-filter', 'klinik-filter', 'rumahsakit-filter', 'puskesmas-filter'];
    facilityFilters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) filterElement.checked = false;
    });
    
    // Reset batas kota
    const cityBoundaryFilter = document.getElementById('city-boundary-filter');
    if (cityBoundaryFilter) {
        cityBoundaryFilter.checked = false;
        if (typeof window.toggleCityBoundary === 'function') {
            window.toggleCityBoundary(false);
        }
    }
    
    // Reset map view jika tersedia
    if (window.mapInstance && typeof window.mapInstance.setView === 'function') {
        window.mapInstance.setView([-3.314494, 114.592972], 12);
    }
    
    // Reset polygon kecamatan (tampilkan semua)
    if (window.currentKecamatanLayer && typeof window.mapInstance !== 'undefined') {
        window.mapInstance.removeLayer(window.currentKecamatanLayer);
        window.currentKecamatanLayer = null;
    }
    if (typeof kecamatanLayer !== 'undefined' && kecamatanLayer && typeof window.mapInstance !== 'undefined') {
        window.mapInstance.addLayer(kecamatanLayer);
    }
    
    // Reset polygon kelurahan (tampilkan semua)
    if (window.currentKelurahanLayer && typeof window.mapInstance !== 'undefined') {
        window.mapInstance.removeLayer(window.currentKelurahanLayer);
        window.currentKelurahanLayer = null;
    }
    if (typeof kelurahanLayer !== 'undefined' && kelurahanLayer && typeof window.mapInstance !== 'undefined') {
        window.mapInstance.addLayer(kelurahanLayer);
    }
    
    // Clear all markers
    clearAllMarkers();
    
    // Skip applying filters to avoid loading new markers
    // Instead of calling applyFilters() directly
    
    // Tampilkan notifikasi jika tersedia
    if (typeof window.showNotification === 'function') {
        window.showNotification('✅ Filter berhasil direset ke default', 'success');
    }
    
    console.log('Filter berhasil direset');
}
// Fungsi untuk mereset filter mobile
function resetMobileFilters() {
    console.log('Reset mobile filters dipanggil');
    
    // Reset checkbox 'Semua' mobile - set to unchecked by default
    const mobileSelectAllKecamatan = document.getElementById('mobile-select-all-kecamatan');
    const mobileSelectAllKelurahan = document.getElementById('mobile-select-all-kelurahan');
    
    if (mobileSelectAllKecamatan) mobileSelectAllKecamatan.checked = false;
    if (mobileSelectAllKelurahan) mobileSelectAllKelurahan.checked = false;
    
    // Reset kecamatan mobile - set to unchecked by default
    const mobileKecamatanCheckboxes = document.querySelectorAll('.mobile-kecamatan-checkbox');
    mobileKecamatanCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset kelurahan mobile
    const mobileKelurahanCheckboxes = document.querySelectorAll('.mobile-kelurahan-checkbox');
    mobileKelurahanCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset fasilitas mobile - set to unchecked by default (changed from checked)
    const facilityFilters = ['mobile-apotek-filter', 'mobile-klinik-filter', 'mobile-rumahsakit-filter', 'mobile-puskesmas-filter'];
    facilityFilters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) filterElement.checked = false;
    });
    
    // Reset batas kota mobile
    const mobileCityBoundaryFilter = document.getElementById('mobile-city-boundary-filter');
    if (mobileCityBoundaryFilter) mobileCityBoundaryFilter.checked = false;
    
    // Reset search mobile
    const mobileSearchFaskes = document.getElementById('mobile-search-faskes');
    if (mobileSearchFaskes) mobileSearchFaskes.value = '';
    
    // Tampilkan notifikasi jika tersedia
    if (typeof window.showNotification === 'function') {
        window.showNotification('✅ Filter berhasil direset ke default', 'success');
    }
    
    console.log('Mobile filter berhasil direset');
}