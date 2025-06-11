/**
 * SIG Fasilitas Kesehatan Banjarmasin - Dashboard JavaScript
 * Dashboard specific functions for charts and statistics
 */

// Interval untuk auto refresh data aktivitas (dalam milidetik)
const ACTIVITY_REFRESH_INTERVAL = 30000; // 30 detik
let activityRefreshTimer;

$(document).ready(function() {
    // Load main dashboard data
    loadDashboardData();
    
    // Set up refresh handlers
    $('#refreshStats').click(function() {
        refreshDashboard();
    });
    
    $('#refreshActivities').click(function() {
        refreshActivities();
        // Reset timer ketika tombol refresh diklik manual
        resetActivityRefreshTimer();
    });
    
    // Event listener for chart type change
    $('#villageChartType').change(function() {
        if (window.villageData) {
            renderVillageChart(window.villageData, $(this).val());
        }
    });
    
    // Mulai timer untuk auto refresh aktivitas
    startActivityRefreshTimer();
    
    // Hentikan timer saat pengguna tidak aktif pada tab
    $(document).on('visibilitychange', function() {
        if (document.hidden) {
            clearTimeout(activityRefreshTimer);
        } else {
            startActivityRefreshTimer();
        }
    });
});

/**
 * Memulai timer untuk auto refresh aktivitas terbaru
 */
function startActivityRefreshTimer() {
    // Clear any existing timer
    clearTimeout(activityRefreshTimer);
    
    // Set new timer
    activityRefreshTimer = setTimeout(function() {
        refreshActivities(true); // true = silent refresh (tanpa menampilkan loading spinner)
        startActivityRefreshTimer(); // restart timer setelah refresh
    }, ACTIVITY_REFRESH_INTERVAL);
}

/**
 * Reset timer untuk auto refresh aktivitas
 */
function resetActivityRefreshTimer() {
    clearTimeout(activityRefreshTimer);
    startActivityRefreshTimer();
}

/**
 * Load all dashboard data
 */
function loadDashboardData() {
    // Show loading indicators
    $('.stat-card .h3').html('<span class="placeholder col-6"></span>');
    $('#districtChart, #villageChart').html('<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    
    // Load district distribution data
    $.ajax({
        url: districtApiRoute,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            renderDistrictChart(data);
        },
        error: function(error) {
            console.error('Error fetching district data:', error);
            $('#districtChart').html('<div class="alert alert-danger">Gagal memuat data kecamatan</div>');
        }
    });
    
    // Load village distribution data
    $.ajax({
        url: villageApiRoute,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Store data globally for later reuse
            window.villageData = data;
            renderVillageChart(data, 'apotek');
        },
        error: function(error) {
            console.error('Error fetching village data:', error);
            $('#villageChart').html('<div class="alert alert-danger">Gagal memuat data kelurahan</div>');
        }
    });
    
    // Load district filter options
    loadDistrictFilter();
    
    // Load statistics
    refreshStatistics();
    
    // Load activities
    refreshActivities();
}

/**
 * Render district distribution chart
 * @param {Array} data - District data array
 */
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
    
    Highcharts.chart('districtChart', {
        chart: {
            type: 'column',
            animation: true,
            style: {
                fontFamily: 'Nunito, sans-serif'
            }
        },
        title: {
            text: null
        },
        xAxis: {
            categories: categories,
            crosshair: true,
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah Fasilitas'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y} unit</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                stacking: 'normal',
                animation: {
                    duration: 1000
                }
            }
        },
        series: [{
            name: 'Apotek',
            data: apotekData,
            color: '#f55d5d'
        }, {
            name: 'Klinik',
            data: klinikData,
            color: '#5cb7d8'
        }, {
            name: 'Puskesmas',
            data: puskesmasData,
            color: '#ffc107'
        }, {
            name: 'Rumah Sakit',
            data: rumahSakitData,
            color: '#28a745'
        }]
    });
}

/**
 * Render village distribution chart
 * @param {Object} data - Village data object
 * @param {string} type - Chart type (apotek or klinik)
 */
function renderVillageChart(data, type) {
    const chartData = [];
    
    data[type].forEach(item => {
        chartData.push({
            name: item.kelurahan,
            y: parseInt(item.total)
        });
    });
    
    // Sort chart data descending by value
    chartData.sort((a, b) => b.y - a.y);
    
    // Take top 15 for readability
    const topData = chartData.slice(0, 15);
    
    Highcharts.chart('villageChart', {
        chart: {
            type: 'bar',
            animation: true,
            style: {
                fontFamily: 'Nunito, sans-serif'
            }
        },
        title: {
            text: null
        },
        xAxis: {
            type: 'category',
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yAxis: {
            title: {
                text: 'Jumlah ' + (type === 'apotek' ? 'Apotek' : 'Klinik')
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '<b>{point.y} unit</b>'
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                animation: {
                    duration: 1000
                },
                dataLabels: {
                    enabled: true,
                    format: '{point.y}'
                }
            }
        },
        series: [{
            name: type === 'apotek' ? 'Apotek' : 'Klinik',
            data: topData,
            color: type === 'apotek' ? '#f55d5d' : '#5cb7d8'
        }]
    });
}

/**
 * Load district filter options
 */
function loadDistrictFilter() {
    $.ajax({
        url: districtsApiRoute,
        type: 'GET',
        dataType: 'json',
        success: function(districts) {
            const filterSelect = $('#districtFilter');
            
            // Add options for all districts
            districts.forEach(district => {
                filterSelect.append(`<option value="${district}">${district}</option>`);
            });
            
            // Event listener for filter change
            filterSelect.change(function() {
                const selectedDistrict = $(this).val();
                
                // If "All Districts" selected, show all data
                if(selectedDistrict === 'all') {
                    $.ajax({
                        url: districtApiRoute,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            renderDistrictChart(data);
                        }
                    });
                } else {
                    // Filter data for selected district
                    $.ajax({
                        url: districtApiRoute,
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

/**
 * Refresh dashboard data
 */
function refreshDashboard() {
    // Show loading spinner
    $('#refreshStats').html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
    
    // Reload statistics
    refreshStatistics();
    
    // Reload chart data
    $.ajax({
        url: districtApiRoute,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            renderDistrictChart(data);
            $('#refreshStats').html('<i class="fas fa-sync-alt"></i> Refresh');
        },
        error: function(error) {
            console.error('Error refreshing district data:', error);
            $('#refreshStats').html('<i class="fas fa-sync-alt"></i> Refresh');
        }
    });
    
    // Reload village data
    $.ajax({
        url: villageApiRoute,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            window.villageData = data;
            renderVillageChart(data, $('#villageChartType').val());
        }
    });
    
    // Reload activities
    refreshActivities();
    
    // Reset activity refresh timer after manual refresh
    resetActivityRefreshTimer();
}

/**
 * Refresh statistics data
 */
function refreshStatistics() {
    $.ajax({
        url: statisticsApiRoute,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Update statistics numbers with animation
            animateCount('.text-apotek', data.apotek);
            animateCount('.text-klinik', data.klinik);
            animateCount('.text-puskesmas', data.puskesmas);
            animateCount('.text-rumahsakit', data.rumahSakit);
            
            // Reset refresh button
            $('#refreshStats').html('<i class="fas fa-sync-alt"></i> Refresh');
        },
        error: function(error) {
            console.error('Error refreshing statistics:', error);
            $('#refreshStats').html('<i class="fas fa-sync-alt"></i> Refresh');
        }
    });
}

/**
 * Refresh activities list
 * @param {boolean} silent - If true, don't show loading spinner (for auto refresh)
 */
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
                                    <strong>${activity.nama}</strong>
                                    ${activity.fasilitas ? 
                                        `<span class="badge badge-light ms-1">${activity.fasilitas}</span>` : 
                                        ''
                                    }
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

/**
 * Update activity timeline with fade animation
 * @param {Array} activities - Activity data array
 */
function updateActivityTimeline(activities) {
    // Get the current activities to compare
    const currentItems = $('.activity-item').map(function() {
        return $(this).data('id') + '-' + $(this).data('updated');
    }).get();
    
    // Check if we need to update (if any new activities or activities have changed)
    const newItems = activities.map(activity => `${activity.id}-${activity.updated_at}`);
    const needsUpdate = JSON.stringify(currentItems) !== JSON.stringify(newItems);
    
    if (needsUpdate) {
        // Clear activity list with animation
        $('.activity-timeline').fadeOut(200, function() {
            $(this).empty();
            
            // Add new activities
            activities.forEach(activity => {
                $(this).append(`
                    <div class="activity-item" data-id="${activity.id}" data-updated="${activity.updated_at}">
                        <div class="activity-time">${activity.formatted_date}</div>
                        <div class="activity-content">
                            <strong>${activity.nama}</strong> (${activity.fasilitas}) telah ${activity.action}.
                        </div>
                    </div>
                `);
            });
            
            // Show with animation
            $(this).fadeIn(200);
        });
    }
}

/**
 * Animate count from current value to target value
 * @param {string} element - Target element selector
 * @param {number} newValue - Target value
 */
function animateCount(element, newValue) {
    const $element = $(element);
    const currentValue = parseInt($element.text()) || 0;
    
    // Skip animation if the value hasn't changed
    if (currentValue === newValue) return;
    
    $({ count: currentValue }).animate({ count: newValue }, {
        duration: 1000,
        easing: 'swing',
        step: function() {
            $element.text(Math.floor(this.count));
        },
        complete: function() {
            $element.text(newValue);
        }
    });
}