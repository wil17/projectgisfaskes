@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistik Ringkas -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-chart-pie me-1"></i> Statistik Ringkas Fasilitas Kesehatan
                    </div>
                    <div>
                        <button id="refreshStats" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Apotek -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card border-apotek bg-apotek-light mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small mb-1">Total Apotek</div>
                                        <div class="h3 mb-0 text-apotek">{{ $statistics['apotek'] }}</div>
                                    </div>
                                    <div>
                                        <i class="fas fa-pills text-apotek"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Klinik -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card border-klinik bg-klinik-light mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small mb-1">Total Klinik</div>
                                        <div class="h3 mb-0 text-klinik">{{ $statistics['klinik'] }}</div>
                                    </div>
                                    <div>
                                        <i class="fas fa-stethoscope text-klinik"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Puskesmas -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card border-puskesmas bg-puskesmas-light mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small mb-1">Total Puskesmas</div>
                                        <div class="h3 mb-0 text-puskesmas">{{ $statistics['puskesmas'] }}</div>
                                    </div>
                                    <div>
                                        <i class="fas fa-clinic-medical text-puskesmas"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rumah Sakit -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card border-rumahsakit bg-rumahsakit-light mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small mb-1">Total Rumah Sakit</div>
                                        <div class="h3 mb-0 text-rumahsakit">{{ $statistics['rumahSakit'] }}</div>
                                    </div>
                                    <div>
                                        <i class="fas fa-hospital-alt text-rumahsakit"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik Distribusi per Kecamatan -->
    <div class="col-xl-8 col-lg-7">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-chart-bar me-1"></i> Distribusi Faskes per Kecamatan
                    </div>
                    <div>
                        <select id="districtFilter" class="form-select form-select-sm">
                            <option value="all">Semua Kecamatan</option>
                            <!-- Opsi kecamatan akan ditambahkan oleh JavaScript -->
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="districtChart" style="height: 400px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Aktivitas Terbaru -->
    <div class="col-xl-4 col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-history me-1"></i> Aktivitas Terbaru
                    </div>
                    <div>
                        <button id="refreshActivities" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="activity-timeline-modern" id="activityTimeline">
                    @foreach ($recentActivities as $activity)
                        <div class="activity-item-modern {{ $activity->color }}">
                            <div class="activity-icon">
                                <i class="{{ $activity->icon }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    <div class="activity-action">{{ $activity->action }}</div>
                                    <div class="activity-time">{{ $activity->formatted_date }}</div>
                                </div>
                                <div class="activity-description">
                                    <strong>{{ $activity->fasilitas }} {{ $activity->nama }}</strong>
                                </div>
                                @if(!empty($activity->details))
                                    <div class="activity-details mt-2">
                                        <small class="text-muted">
                                            @foreach($activity->details as $detail)
                                                <div class="mb-1">
                                                    <i class="fas fa-arrow-right text-info me-1"></i>
                                                    {{ $detail }}
                                                </div>
                                            @endforeach
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik Distribusi per Kelurahan -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-chart-area me-1"></i> Distribusi Faskes Kelurahan
                    </div>
                    <div class="d-flex">
                        <select id="districtFilterVillage" class="form-select form-select-sm me-2">
                            <option value="all">Semua Kecamatan</option>
                            <!-- Opsi kecamatan akan ditambahkan oleh JavaScript -->
                        </select>
                        <select id="villageChartType" class="form-select form-select-sm">
                            <option value="Apotek">Apotek</option>
                            <option value="Klinik">Klinik</option>
                            <option value="Puskesmas">Puskesmas</option>
                            <option value="Rumah Sakit">Rumah Sakit</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="villageChart" style="height: 400px;"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let districtChart = null;
    let villageChart = null;
    
    // Definisi warna untuk faskes - sesuai dengan halaman user
    const faskesColors = {
        'Apotek': {
            backgroundColor: 'rgba(76, 148, 255, 0.8)',
            borderColor: '#4c94ff'
        },
        'Klinik': {
            backgroundColor: 'rgba(69, 196, 93, 0.8)',
            borderColor: '#45c45d'
        },
        'Puskesmas': {
            backgroundColor: 'rgba(255, 167, 38, 0.8)',
            borderColor: '#ffa726'
        },
        'Rumah Sakit': {
            backgroundColor: 'rgba(239, 83, 80, 0.8)',
            borderColor: '#ef5350'
        }
    };
    
    $(document).ready(function() {
        // Inisialisasi Chart.js dengan default settings
        Chart.defaults.font.family = "'Nunito', sans-serif";
        Chart.defaults.color = '#858796';
        
        // Grafik Distribusi per Kecamatan
        $.ajax({
            url: '{{ route("admin.api.district") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                renderDistrictChart(data);
            },
            error: function(error) {
                console.error('Error fetching district data:', error);
            }
        });
        
        // Load district filter
        loadDistrictFilter();
        
        // Set up refresh handlers
        $('#refreshStats').click(function() {
            refreshDashboard();
        });
        
        $('#refreshActivities').click(function() {
            refreshActivities();
        });
        
        // Load Kecamatan Filter untuk Village Chart
        $.ajax({
            url: '{{ route("admin.api.districts") }}',
            type: 'GET',
            dataType: 'json',
            success: function(districts) {
                const filterSelect = $('#districtFilterVillage');
                districts.forEach(district => {
                    filterSelect.append(`<option value="${district}">${district}</option>`);
                });
                
                // Initial load of village chart
                loadVillageChartData('all');
                
                // Event listener untuk perubahan filter kecamatan
                filterSelect.change(function() {
                    const selectedDistrict = $(this).val();
                    loadVillageChartData(selectedDistrict);
                });
                
                // Event listener untuk perubahan tipe faskes
                $('#villageChartType').change(function() {
                    renderVillageChart(window.villageData, $(this).val());
                });
            }
        });
    });
    
    function loadVillageChartData(kecamatan) {
        $.ajax({
            url: '{{ route("admin.api.village") }}',
            type: 'GET',
            data: { kecamatan: kecamatan },
            dataType: 'json',
            success: function(data) {
                window.villageData = data;
                renderVillageChart(data, $('#villageChartType').val());
            },
            error: function(error) {
                console.error('Error fetching village data:', error);
            }
        });
    }
    
    function renderDistrictChart(data) {
        const categories = [];
        const apotekData = [];
        const klinikData = [];
        const puskesmasData = [];
        const rumahSakitData = [];
        
        data.forEach(item => {
            categories.push(item.kecamatan);
            apotekData.push(parseInt(item.apotek_count));
            klinikData.push(parseInt(item.klinik_count));
            puskesmasData.push(parseInt(item.puskesmas_count));
            rumahSakitData.push(parseInt(item.rumahsakit_count));
        });
        
        const ctx = document.getElementById('districtChart').getContext('2d');
        
        // Destroy existing chart if exists
        if (districtChart) {
            districtChart.destroy();
        }
        
        districtChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Apotek',
                    data: apotekData,
                    backgroundColor: faskesColors['Apotek'].backgroundColor,
                    borderColor: faskesColors['Apotek'].borderColor,
                    borderWidth: 1
                }, {
                    label: 'Klinik',
                    data: klinikData,
                    backgroundColor: faskesColors['Klinik'].backgroundColor,
                    borderColor: faskesColors['Klinik'].borderColor,
                    borderWidth: 1
                }, {
                    label: 'Puskesmas',
                    data: puskesmasData,
                    backgroundColor: faskesColors['Puskesmas'].backgroundColor,
                    borderColor: faskesColors['Puskesmas'].borderColor,
                    borderWidth: 1
                }, {
                    label: 'Rumah Sakit',
                    data: rumahSakitData,
                    backgroundColor: faskesColors['Rumah Sakit'].backgroundColor,
                    borderColor: faskesColors['Rumah Sakit'].borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Jumlah Fasilitas'
                        }
                    },
                    x: {
                        stacked: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' unit';
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
    
    function renderVillageChart(data, type) {
        const chartData = [];
        const labels = [];
        const values = [];
        
        if (data && data[type]) {
            data[type].forEach(item => {
                chartData.push({
                    label: item.kelurahan,
                    value: parseInt(item.total)
                });
            });
            
            // Sort chart data descending by value
            chartData.sort((a, b) => b.value - a.value);
            
            // Take top 15 for readability
            const topData = chartData.slice(0, 15);
            
            topData.forEach(item => {
                labels.push(item.label);
                values.push(item.value);
            });
        }
        
        const ctx = document.getElementById('villageChart').getContext('2d');
        
        // Destroy existing chart if exists
        if (villageChart) {
            villageChart.destroy();
        }
        
        villageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: type,
                    data: values,
                    backgroundColor: faskesColors[type].backgroundColor,
                    borderColor: faskesColors[type].borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah ' + type
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Kelurahan'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.x + ' unit';
                            }
                        }
                    }
                }
            }
        });
    }
    
    function loadDistrictFilter() {
        $.ajax({
            url: '{{ route("admin.api.districts") }}',
            type: 'GET',
            dataType: 'json',
            success: function(districts) {
                const filterSelect = $('#districtFilter');
                // Tambahkan opsi untuk semua kecamatan
                districts.forEach(district => {
                    filterSelect.append(`<option value="${district}">${district}</option>`);
                });
                
                // Event listener untuk perubahan filter
                filterSelect.change(function() {
                    const selectedDistrict = $(this).val();
                    
                    // Jika "Semua Kecamatan" dipilih, tampilkan semua data
                    if(selectedDistrict === 'all') {
                        $.ajax({
                            url: '{{ route("admin.api.district") }}',
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                renderDistrictChart(data);
                            }
                        });
                    } else {
                        // Filter data untuk kecamatan yang dipilih
                        $.ajax({
                            url: '{{ route("admin.api.district") }}',
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                const filteredData = data.filter(item => item.kecamatan === selectedDistrict);
                                renderDistrictChart(filteredData);
                            }
                        });
                    }
                });
            },
            error: function(error) {
                console.error('Error loading districts:', error);
            }
        });
    }
    
    function refreshDashboard() {
        // Tampilkan loading spinner
        $('#refreshStats').html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
        
        // Reload statistik
        $.ajax({
            url: '{{ route("admin.api.statistics") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Update angka statistik
                $('.text-apotek').text(data.apotek);
                $('.text-klinik').text(data.klinik);
                $('.text-puskesmas').text(data.puskesmas);
                $('.text-rumahsakit').text(data.rumahSakit);
                
                // Reset tombol refresh
                $('#refreshStats').html('<i class="fas fa-sync-alt"></i> Refresh');
            },
            error: function(error) {
                console.error('Error refreshing statistics:', error);
                $('#refreshStats').html('<i class="fas fa-sync-alt"></i> Refresh');
            }
        });
        
        // Reload chart data
        $.ajax({
            url: '{{ route("admin.api.district") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                renderDistrictChart(data);
            }
        });
        
        // Reload aktivitas terbaru
        refreshActivities();
    }
    
    function refreshActivities() {
        $('#refreshActivities').html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("admin.api.activities") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Kosongkan daftar aktivitas
                $('#activityTimeline').empty();
                
                // Tambahkan aktivitas baru
                data.forEach(activity => {
                    let detailsHtml = '';
                    if (activity.details && activity.details.length > 0) {
                        detailsHtml = `
                            <div class="activity-details mt-2">
                                <small class="text-muted">
                                    ${activity.details.map(detail => 
                                        `<div class="mb-1">
                                            <i class="fas fa-arrow-right text-info me-1"></i>
                                            ${detail}
                                        </div>`
                                    ).join('')}
                                </small>
                            </div>
                        `;
                    }
                    
                    $('#activityTimeline').append(`
                        <div class="activity-item-modern ${activity.color}">
                            <div class="activity-icon">
                                <i class="${activity.icon}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    <div class="activity-action">${activity.action}</div>
                                    <div class="activity-time">${activity.formatted_date}</div>
                                </div>
                                <div class="activity-description">
                                    <strong>${activity.fasilitas} ${activity.nama}</strong>
                                </div>
                                ${detailsHtml}
                            </div>
                        </div>
                    `);
                });
                
                $('#refreshActivities').html('<i class="fas fa-sync-alt"></i>');
            },
            error: function(error) {
                console.error('Error refreshing activities:', error);
                $('#refreshActivities').html('<i class="fas fa-sync-alt"></i>');
            }
        });
    }
</script>
@endsection