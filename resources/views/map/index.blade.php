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
    <link rel="stylesheet" href="{{ asset('css/mapstyle.css') }}">
    <style>
        /* Style tambahan untuk memastikan menu toggle di sebelah kiri */
        @media (max-width: 991.98px) {
            .navbar-toggler {
                order: 1;
            }
            .navbar-brand {
                order: 2;
                margin-right: 0;
                margin-left: 0.5rem;
            }
        }
    </style>
</head>
<body>
    @include('partials.header')

    <div class="container mt-4 mb-4">
        <h1 class="page-title">Peta Fasilitas Kesehatan Kota Banjarmasin</h1>

        <div class="d-md-none mb-3">
            <button id="mobile-filter-toggle" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-filter me-1"></i> Filter Peta
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-3 filter-sidebar d-none d-md-block">
                <div class="filter-container">
                    <ul class="nav nav-tabs nav-fill mb-2" id="filterTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="kecamatan-tab" data-bs-toggle="tab" data-bs-target="#kecamatan-panel" type="button" role="tab" aria-selected="true"><i class="fas fa-map-marker-alt"></i> Kecamatan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kelurahan-tab" data-bs-toggle="tab" data-bs-target="#kelurahan-panel" type="button" role="tab" aria-selected="false"><i class="fas fa-map-pin"></i> Kelurahan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fasilitas-tab" data-bs-toggle="tab" data-bs-target="#fasilitas-panel" type="button" role="tab" aria-selected="false"><i class="fas fa-filter"></i> Fasilitas</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="filterTabContent">
                        <div class="tab-pane fade show active" id="kecamatan-panel" role="tabpanel">
                            <div class="filter-section compact-filter">
                                <div class="select-all-option mb-2">
                                    <input type="checkbox" id="select-all-kecamatan">
                                    <label for="select-all-kecamatan">Semua Kecamatan</label>
                                </div>
                                <div class="checkbox-container scrollable-container">
                                    @foreach($kecamatan as $k)
                                        <div class="filter-checkbox">
                                            <input type="checkbox" id="kecamatan-{{ str_replace(' ', '-', $k->kecamatan) }}" class="kecamatan-checkbox" value="{{ $k->kecamatan }}">
                                            <label for="kecamatan-{{ str_replace(' ', '-', $k->kecamatan) }}">{{ $k->kecamatan }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="kelurahan-panel" role="tabpanel">
                            <div class="filter-section compact-filter">
                                <div class="select-all-option mb-2">
                                    <input type="checkbox" id="select-all-kelurahan">
                                    <label for="select-all-kelurahan">Semua Kelurahan</label>
                                </div>
                                <div class="scrollable-container" id="kelurahan-checkbox-container">
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="fasilitas-panel" role="tabpanel">
                            <div class="filter-section compact-filter">
                                <div class="facility-filter">
                                    <div class="facility-checkbox">
                                        <input type="checkbox" id="apotek-filter" checked>
                                        <label for="apotek-filter">Apotek</label>
                                    </div>
                                    <div class="facility-checkbox">
                                        <input type="checkbox" id="klinik-filter" checked>
                                        <label for="klinik-filter">Klinik</label>
                                    </div>
                                    <div class="facility-checkbox">
                                        <input type="checkbox" id="rumahsakit-filter" checked>
                                        <label for="rumahsakit-filter">Rumah Sakit</label>
                                    </div>
                                    <div class="facility-checkbox">
                                        <input type="checkbox" id="puskesmas-filter" checked>
                                        <label for="puskesmas-filter">Puskesmas</label>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="facility-checkbox">
                                        <input type="checkbox" id="city-boundary-filter">
                                        <label for="city-boundary-filter">Batas Kota Banjarmasin</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="search-box mt-3">
                        <input type="text" id="search-faskes" class="form-control" placeholder="Cari fasilitas kesehatan...">
                    </div>
                    
                    <button id="reset-filter" class="btn btn-secondary btn-icon w-100 mt-3">
                        <i class="fas fa-sync-alt"></i> Reset Filter
                    </button>
                </div>
            </div>

            <div class="col-md-9 col-12">
                <div id="map">
                    <div id="notification-area"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mobile-filter-modal" tabindex="-1" aria-labelledby="mobileFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mobileFilterModalLabel">Filter Peta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="mobileFilterAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#mobileKecamatanCollapse" aria-expanded="true">
                                    <i class="fas fa-map-marker-alt me-2"></i> Filter Kecamatan
                                </button>
                            </h2>
                            <div id="mobileKecamatanCollapse" class="accordion-collapse collapse show" data-bs-parent="#mobileFilterAccordion">
                                <div class="accordion-body p-2">
                                    <div class="select-all-option mb-2">
                                        <input type="checkbox" id="mobile-select-all-kecamatan">
                                        <label for="mobile-select-all-kecamatan">Semua Kecamatan</label>
                                    </div>
                                    <div class="checkbox-container scrollable-mobile-container">
                                        @foreach($kecamatan as $k)
                                            <div class="filter-checkbox">
                                                <input type="checkbox" id="mobile-kecamatan-{{ str_replace(' ', '-', $k->kecamatan) }}" class="mobile-kecamatan-checkbox" value="{{ $k->kecamatan }}">
                                                <label for="mobile-kecamatan-{{ str_replace(' ', '-', $k->kecamatan) }}">{{ $k->kecamatan }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mobileKelurahanCollapse" aria-expanded="false">
                                    <i class="fas fa-map-pin me-2"></i> Filter Kelurahan
                                </button>
                            </h2>
                            <div id="mobileKelurahanCollapse" class="accordion-collapse collapse" data-bs-parent="#mobileFilterAccordion">
                                <div class="accordion-body p-2">
                                    <div class="select-all-option mb-2">
                                        <input type="checkbox" id="mobile-select-all-kelurahan">
                                        <label for="mobile-select-all-kelurahan">Semua Kelurahan</label>
                                    </div>
                                    <div class="mobile-kelurahan-info alert alert-info py-2 px-3 mb-2" style="font-size: 0.8rem;">
                                        <i class="fas fa-info-circle me-1"></i> Pilih kecamatan terlebih dahulu untuk melihat kelurahan
                                    </div>
                                    <div class="scrollable-mobile-container" id="mobile-kelurahan-checkbox-container">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mobileFasilitasCollapse" aria-expanded="false">
                                    <i class="fas fa-hospital me-2"></i> Filter Fasilitas
                                </button>
                            </h2>
                            <div id="mobileFasilitasCollapse" class="accordion-collapse collapse" data-bs-parent="#mobileFilterAccordion">
                                <div class="accordion-body p-2">
                                    <div class="facility-filter-mobile">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="facility-checkbox">
                                                    <input type="checkbox" id="mobile-apotek-filter" checked>
                                                    <label for="mobile-apotek-filter">
                                                        <i class="fas fa-pills text-primary me-1"></i> Apotek
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="facility-checkbox">
                                                    <input type="checkbox" id="mobile-klinik-filter" checked>
                                                    <label for="mobile-klinik-filter">
                                                        <i class="fas fa-stethoscope text-success me-1"></i> Klinik
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="facility-checkbox">
                                                    <input type="checkbox" id="mobile-rumahsakit-filter" checked>
                                                    <label for="mobile-rumahsakit-filter">
                                                        <i class="fas fa-hospital text-danger me-1"></i> Rumah Sakit
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="facility-checkbox">
                                                    <input type="checkbox" id="mobile-puskesmas-filter" checked>
                                                    <label for="mobile-puskesmas-filter">
                                                        <i class="fas fa-first-aid text-warning me-1"></i> Puskesmas
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-5 border-top pt-2">
                                            <div class="facility-checkbox">
                                                <input type="checkbox" id="mobile-city-boundary-filter">
                                                <label for="mobile-city-boundary-filter">
                                                    <i class="fas fa-border-all text-info me-1"></i> Batas Kota Banjarmasin
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="search-box mt-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="mobile-search-faskes" class="form-control" placeholder="Cari fasilitas kesehatan...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="mobile-reset-filter" class="btn btn-outline-secondary btn-icon">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-check me-1"></i> Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <!-- JQuery-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.min.js"></script>
    <script src="{{ asset('js/maps.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
           
            function resizeMap() {
                if (window.mapInstance) {
                    window.mapInstance.invalidateSize();
                }
            }

            const filterTabs = document.querySelectorAll('#filterTabs .nav-link');
            filterTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function() {
                    resizeMap();
                });
            });

            const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabEls.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', resizeMap);
            });
        });
    </script>

    <script>
        setTimeout(function() {
            const script = document.createElement('script');
            script.src = "{{ asset('js/map.js') }}";
            document.body.appendChild(script);
        }, 500);
    </script>

    <!-- Leaflet plugins -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-minimap/3.6.0/Control.MiniMap.min.js"></script>
</body>
</html>