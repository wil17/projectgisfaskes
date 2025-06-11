<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Fasilitas Kesehatan Kota Banjarmasin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/statistik.css') }}" rel="stylesheet">
</head>
<body>

@include('partials.header')

<!-- Kilas Data Section -->
<section class="kilas-data-section py-5">
    <div class="container">
        <h2 class="text-center mb-4">Kilas Data</h2>
        <p class="text-center text-muted mb-5">
            Kota Banjarmasin secara astronomis berada di antara 3°16'46" sampai dengan 3°22'54" Lintang Selatan dan 
            114°31'40" sampai dengan 114°39'55" Bujur Timur
        </p>
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="kilas-card text-center">
                    <div class="kilas-icon">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </div>
                    <div class="kilas-value">98,46</div>
                    <div class="kilas-label">Luas Wilayah</div>
                    <div class="kilas-unit">(km²)</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="kilas-card text-center">
                    <div class="kilas-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="kilas-value">5</div>
                    <div class="kilas-label">Kecamatan</div>
                    <div class="kilas-unit">Wilayah Administratif</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="kilas-card text-center">
                    <div class="kilas-icon">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div class="kilas-value">52</div>
                    <div class="kilas-label">Kelurahan</div>
                    <div class="kilas-unit">Wilayah Administratif</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistik Section -->
<section id="statistik" class="py-5 bg-light">
    <div class="container stats-container">
        <h2 class="section-title">Total Fasilitas Kesehatan</h2>
        
        <!-- Overall Statistics -->
        <div id="overall-statistics" class="row mb-5">
            <!-- Statistics will be loaded here -->
        </div>
        
        <!-- Dropdown Kecamatan -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4">
                    <label for="kecamatanDropdown" class="form-label">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Pilih Kecamatan untuk Detail:
                    </label>
                    <select id="kecamatanDropdown" class="form-select">
                        <option value="">-- Pilih Kecamatan --</option>
                        <!-- Opsi akan diisi secara dinamis dari JavaScript -->
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Detail Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-column me-2"></i>
                        Detail Fasilitas Kesehatan
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="detailFaskesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i>
                        Persentase Jenis Fasilitas Kesehatan
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="percentageFaskesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelurahan Statistics Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-map-marker-alt"></i> Statistik Fasilitas Kesehatan per Kelurahan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="kelurahanDropdown" class="form-label">Pilih Kecamatan:</label>
                            <select class="form-select" id="kelurahanDropdown">
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="chart-container">
                            <canvas id="kelurahanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabel Statistik Kecamatan (BARU dengan Responsif yang Ditingkatkan) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-table me-2"></i>
                        Tabel Statistik Fasilitas Kesehatan per Kecamatan
                    </div>
                    <div class="card-body">
                        <!-- Desktop/tablet view -->
                        <div class="table-responsive-container desktop-table-view">
                            <div class="scroll-indicator">
                                <i class="fas fa-arrows-left-right"></i> Geser untuk melihat seluruh tabel
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sticky-first-column">
                                    <thead>
                                        <tr>
                                            <th>Kecamatan</th>
                                            <th>Apotek</th>
                                            <th>Klinik</th>
                                            <th>Puskesmas</th>
                                            <th>Rumah Sakit</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kecamatan-totals-table">
                                        <!-- Data akan diisi dari JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Ultra-mobile view (card-based) -->
                        <div class="mobile-card-view d-none">
                            <div id="kecamatan-cards-container">
                                <!-- Cards will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom JS -->
<script src="{{ asset('js/statistik.js') }}"></script>

</body>
</html>