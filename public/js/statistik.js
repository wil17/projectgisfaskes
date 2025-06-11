document.addEventListener('DOMContentLoaded', function() {
    // Instance chart
    let detailFaskesChart;
    let percentageFaskesChart;
    let kelurahanChart;

    // Inisialisasi charts dan peta dengan status loading
    initializeCharts();

    // Load data dan update charts
    loadFaskesData();
    loadKelurahanData();
    loadKecamatanTotals();

    // Event listener untuk dropdown kecamatan
    document.getElementById('kecamatanDropdown').addEventListener('change', function() {
        updateDetailCharts();
    });

    // Event listener untuk dropdown kelurahan
    document.getElementById('kelurahanDropdown').addEventListener('change', function() {
        updateKelurahanChart();
    });

    // Check responsive view
    function checkMobileView() {
        const desktopView = document.querySelector('.desktop-table-view');
        const mobileView = document.querySelector('.mobile-card-view');
        
        if (desktopView && mobileView) {
            if (window.innerWidth <= 400) {
                desktopView.classList.add('d-none');
                mobileView.classList.remove('d-none');
            } else {
                desktopView.classList.remove('d-none');
                mobileView.classList.add('d-none');
            }
        }
    }
    
    // Check on load and resize
    window.addEventListener('resize', checkMobileView);
    checkMobileView();

    function initializeCharts() {
        const detailCtx = document.getElementById('detailFaskesChart').getContext('2d');
        detailFaskesChart = new Chart(detailCtx, {
            type: 'bar',
            data: {
                labels: ['Detail Fasilitas'],
                datasets: [
                    {
                        label: 'Apotek',
                        data: [0],
                        backgroundColor: '#2196F3'
                    },
                    {
                        label: 'Klinik',
                        data: [0],
                        backgroundColor: '#4CAF50'
                    },
                    {
                        label: 'Puskesmas',
                        data: [0],
                        backgroundColor: '#FF9800'
                    },
                    {
                        label: 'Rumah Sakit',
                        data: [0],
                        backgroundColor: '#F44336'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Detail Fasilitas Kesehatan'
                    }
                }
            }
        });

        // Percentage Faskes Chart - Doughnut Chart
        const percentageCtx = document.getElementById('percentageFaskesChart').getContext('2d');
        percentageFaskesChart = new Chart(percentageCtx, {
            type: 'doughnut',
            data: {
                labels: ['Apotek', 'Klinik', 'Puskesmas', 'Rumah Sakit'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#2196F3',
                        '#4CAF50',
                        '#FF9800',
                        '#F44336'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Persentase Jenis Fasilitas Kesehatan'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Kelurahan Chart - Bar Chart
        const kelurahanCtx = document.getElementById('kelurahanChart').getContext('2d');
        kelurahanChart = new Chart(kelurahanCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Apotek',
                        data: [],
                        backgroundColor: '#2196F3'
                    },
                    {
                        label: 'Klinik',
                        data: [],
                        backgroundColor: '#4CAF50'
                    },
                    {
                        label: 'Puskesmas',
                        data: [],
                        backgroundColor: '#FF9800'  // Yellow color for Puskesmas
                    },
                    {
                        label: 'Rumah Sakit',
                        data: [],
                        backgroundColor: '#F44336'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Fasilitas Kesehatan per Kelurahan'
                    }
                }
            }
        });
    }

    function loadFaskesData() {
        fetch('/api/faskes-statistics')
            .then(response => response.json())
            .then(data => {
                console.log('Data yang diterima:', data);
                processStatisticsData(data);
            })
            .catch(error => {
                console.error('Error loading faskes data:', error);
                showErrorMessage('Gagal memuat data statistik fasilitas kesehatan.');
            });
    }

    function loadKelurahanData() {
        // Isi dropdown kelurahan dengan opsi kecamatan
        fetch('/api/faskes-statistics')
            .then(response => response.json())
            .then(data => {
                const kelurahanDropdown = document.getElementById('kelurahanDropdown');
                kelurahanDropdown.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                
                Object.keys(data).forEach(kecamatan => {
                    const option = document.createElement('option');
                    option.value = kecamatan;
                    option.textContent = kecamatan;
                    kelurahanDropdown.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading kelurahan data:', error);
            });
    }
    
    function loadKecamatanTotals() {
        fetch('/api/kecamatan-totals')
            .then(response => response.json())
            .then(data => {
                populateKecamatanTable(data);
            })
            .catch(error => {
                console.error('Error loading kecamatan totals:', error);
                showErrorMessage('Gagal memuat data tabel statistik kecamatan.');
            });
    }
    
    function populateKecamatanTable(data) {
        // Populate regular table view
        const tableBody = document.getElementById('kecamatan-totals-table');
        tableBody.innerHTML = '';
        
        // Card container for mobile view
        const cardsContainer = document.getElementById('kecamatan-cards-container');
        if (cardsContainer) {
            cardsContainer.innerHTML = '';
        }
        
        // Footer untuk total keseluruhan
        let grandTotalApotek = 0;
        let grandTotalKlinik = 0;
        let grandTotalPuskesmas = 0;
        let grandTotalRumahSakit = 0;
        
        // Tambahkan baris untuk setiap kecamatan
        data.forEach(kecamatan => {
            const apotek = parseInt(kecamatan.Apotek) || 0;
            const klinik = parseInt(kecamatan.Klinik) || 0;
            const puskesmas = parseInt(kecamatan.Puskesmas) || 0;
            const rumahSakit = parseInt(kecamatan["Rumah Sakit"]) || 0;
            const total = apotek + klinik + puskesmas + rumahSakit;
            
            // Update grand totals
            grandTotalApotek += apotek;
            grandTotalKlinik += klinik;
            grandTotalPuskesmas += puskesmas;
            grandTotalRumahSakit += rumahSakit;
            
            // Create table row
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${kecamatan.Kecamatan}</td>
                <td>${apotek}</td>
                <td>${klinik}</td>
                <td>${puskesmas}</td>
                <td>${rumahSakit}</td>
                <td><strong>${total}</strong></td>
            `;
            tableBody.appendChild(row);
            
            // Create card for mobile view
            if (cardsContainer) {
                const card = document.createElement('div');
                card.className = 'facility-card';
                card.innerHTML = `
                    <div class="facility-card-header">${kecamatan.Kecamatan}</div>
                    <div class="facility-card-body">
                        <div class="facility-stat">
                            <div class="facility-label">Apotek</div>
                            <div class="facility-value">${apotek}</div>
                        </div>
                        <div class="facility-stat">
                            <div class="facility-label">Klinik</div>
                            <div class="facility-value">${klinik}</div>
                        </div>
                        <div class="facility-stat">
                            <div class="facility-label">Puskesmas</div>
                            <div class="facility-value">${puskesmas}</div>
                        </div>
                        <div class="facility-stat">
                            <div class="facility-label">Rumah Sakit</div>
                            <div class="facility-value">${rumahSakit}</div>
                        </div>
                        <div class="facility-stat">
                            <div class="facility-label">Total</div>
                            <div class="facility-value"><strong>${total}</strong></div>
                        </div>
                    </div>
                `;
                cardsContainer.appendChild(card);
            }
        });
        
        // Tambahkan baris total keseluruhan
        const grandTotal = grandTotalApotek + grandTotalKlinik + grandTotalPuskesmas + grandTotalRumahSakit;
        const totalRow = document.createElement('tr');
        totalRow.className = 'table-dark';
        totalRow.innerHTML = `
            <td><strong>TOTAL</strong></td>
            <td><strong>${grandTotalApotek}</strong></td>
            <td><strong>${grandTotalKlinik}</strong></td>
            <td><strong>${grandTotalPuskesmas}</strong></td>
            <td><strong>${grandTotalRumahSakit}</strong></td>
            <td><strong>${grandTotal}</strong></td>
        `;
        tableBody.appendChild(totalRow);
        
        // Add total card for mobile view
        if (cardsContainer) {
            const totalCard = document.createElement('div');
            totalCard.className = 'facility-card bg-dark text-white';
            totalCard.innerHTML = `
                <div class="facility-card-header">TOTAL</div>
                <div class="facility-card-body">
                    <div class="facility-stat">
                        <div class="facility-label">Apotek</div>
                        <div class="facility-value">${grandTotalApotek}</div>
                    </div>
                    <div class="facility-stat">
                        <div class="facility-label">Klinik</div>
                        <div class="facility-value">${grandTotalKlinik}</div>
                    </div>
                    <div class="facility-stat">
                        <div class="facility-label">Puskesmas</div>
                        <div class="facility-value">${grandTotalPuskesmas}</div>
                    </div>
                    <div class="facility-stat">
                        <div class="facility-label">Rumah Sakit</div>
                        <div class="facility-value">${grandTotalRumahSakit}</div>
                    </div>
                    <div class="facility-stat">
                        <div class="facility-label">Total</div>
                        <div class="facility-value"><strong>${grandTotal}</strong></div>
                    </div>
                </div>
            `;
            cardsContainer.appendChild(totalCard);
        }
    }

    function processStatisticsData(data) {
        // Isi dropdown kecamatan
        populateKecamatanDropdown(Object.keys(data));

        // Update detail charts dengan kecamatan pertama
        updateDetailCharts();
        
        // Hitung statistik keseluruhan
        calculateOverallStatistics(data);
    }

    function populateKecamatanDropdown(kecamatanNames) {
        const dropdown = document.getElementById('kecamatanDropdown');
        dropdown.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        
        kecamatanNames.forEach(kecamatan => {
            const option = document.createElement('option');
            option.value = kecamatan;
            option.textContent = kecamatan;
            dropdown.appendChild(option);
        });
    }

    function updateDetailCharts() {
        const selectedKecamatan = document.getElementById('kecamatanDropdown').value;
        
        if (!selectedKecamatan) {
            // Jika tidak ada kecamatan yang dipilih, reset charts
            detailFaskesChart.data.datasets[0].data = [0];
            detailFaskesChart.data.datasets[1].data = [0];
            detailFaskesChart.data.datasets[2].data = [0];
            detailFaskesChart.data.datasets[3].data = [0];
            detailFaskesChart.update();
            
            percentageFaskesChart.data.datasets[0].data = [0, 0, 0, 0];
            percentageFaskesChart.update();
            
            return;
        }

        // Ambil data kembali untuk kecamatan yang dipilih
        fetch('/api/faskes-statistics')
            .then(response => response.json())
            .then(data => {
                const kecamatanData = data[selectedKecamatan];
                
                if (kecamatanData) {
                    // Konversi nilai ke integer
                    const apotekCount = parseInt(kecamatanData.Apotek) || 0;
                    const klinikCount = parseInt(kecamatanData.Klinik) || 0;
                    const puskesmasCount = parseInt(kecamatanData.Puskesmas) || 0;
                    const rumahSakitCount = parseInt(kecamatanData['Rumah Sakit']) || 0;
                    
                    // Update detail chart
                    detailFaskesChart.data.datasets[0].data = [apotekCount];
                    detailFaskesChart.data.datasets[1].data = [klinikCount];
                    detailFaskesChart.data.datasets[2].data = [puskesmasCount];
                    detailFaskesChart.data.datasets[3].data = [rumahSakitCount];
                    detailFaskesChart.options.plugins.title.text = `Detail Fasilitas Kesehatan - ${selectedKecamatan}`;
                    detailFaskesChart.update();
                    
                    // Update percentage chart
                    percentageFaskesChart.data.datasets[0].data = [
                        apotekCount,
                        klinikCount,
                        puskesmasCount,
                        rumahSakitCount
                    ];
                    percentageFaskesChart.options.plugins.title.text = `Persentase Fasilitas Kesehatan - ${selectedKecamatan}`;
                    percentageFaskesChart.update();
                }
            })
            .catch(error => {
                console.error('Error updating detail charts:', error);
            });
    }

    function showErrorMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-warning text-center mt-3';
        alertDiv.innerHTML = `<strong>Perhatian!</strong> ${message}`;
        
        const statsContainer = document.getElementById('statistik');
        statsContainer.insertBefore(alertDiv, statsContainer.firstChild);
        
        // Hapus alert setelah 5 detik
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }

    function calculateOverallStatistics(data) {
        let totalApotek = 0;
        let totalKlinik = 0;
        let totalPuskesmas = 0;
        let totalRumahSakit = 0;

        Object.values(data).forEach(kecamatanData => {
            // Konversi string ke number dengan parseInt
            totalApotek += parseInt(kecamatanData.Apotek) || 0;
            totalKlinik += parseInt(kecamatanData.Klinik) || 0;
            totalPuskesmas += parseInt(kecamatanData.Puskesmas) || 0;
            totalRumahSakit += parseInt(kecamatanData['Rumah Sakit']) || 0;
        });

        // Tampilkan statistik keseluruhan
        const overallStats = document.getElementById('overall-statistics');
        const grandTotal = totalApotek + totalKlinik + totalPuskesmas + totalRumahSakit;
        
        overallStats.innerHTML = `
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-icon apotek">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="stat-value">${totalApotek}</div>
                    <div class="stat-label">Total Apotek</div>
                    <div class="stat-percentage">${grandTotal > 0 ? ((totalApotek / grandTotal) * 100).toFixed(1) : 0}%</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-icon klinik">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="stat-value">${totalKlinik}</div>
                    <div class="stat-label">Total Klinik</div>
                    <div class="stat-percentage">${grandTotal > 0 ? ((totalKlinik / grandTotal) * 100).toFixed(1) : 0}%</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-icon puskesmas">
                        <i class="fas fa-first-aid"></i>
                    </div>
                    <div class="stat-value">${totalPuskesmas}</div>
                    <div class="stat-label">Total Puskesmas</div>
                    <div class="stat-percentage">${grandTotal > 0 ? ((totalPuskesmas / grandTotal) * 100).toFixed(1) : 0}%</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-icon rumah-sakit">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div class="stat-value">${totalRumahSakit}</div>
                    <div class="stat-label">Total Rumah Sakit</div>
                    <div class="stat-percentage">${grandTotal > 0 ? ((totalRumahSakit / grandTotal) * 100).toFixed(1) : 0}%</div>
                </div>
            </div>
        `;

        console.log('Statistik Keseluruhan:', {
            'Total Apotek': totalApotek,
            'Total Klinik': totalKlinik,
            'Total Puskesmas': totalPuskesmas,
            'Total Rumah Sakit': totalRumahSakit
        });
    }

    function updateKelurahanChart() {
        const selectedKecamatan = document.getElementById('kelurahanDropdown').value;
        
        if (!selectedKecamatan) {
            kelurahanChart.data.labels = [];
            kelurahanChart.data.datasets[0].data = [];
            kelurahanChart.data.datasets[1].data = [];
            kelurahanChart.data.datasets[2].data = [];
            kelurahanChart.data.datasets[3].data = [];
            kelurahanChart.options.plugins.title.text = 'Fasilitas Kesehatan per Kelurahan';
            kelurahanChart.update();
            return;
        }
        
        // Fetch data kelurahan untuk kecamatan yang dipilih
        fetch(`/api/kelurahan-statistics?kecamatan=${encodeURIComponent(selectedKecamatan)}`)
            .then(response => response.json())
            .then(data => {
                const kelurahanNames = Object.keys(data);
                // Konversi nilai ke integer untuk setiap tipe fasilitas
                const apotekData = kelurahanNames.map(kelurahan => parseInt(data[kelurahan].Apotek) || 0);
                const klinikData = kelurahanNames.map(kelurahan => parseInt(data[kelurahan].Klinik) || 0);
                const puskesmasData = kelurahanNames.map(kelurahan => parseInt(data[kelurahan].Puskesmas) || 0);
                const rumahSakitData = kelurahanNames.map(kelurahan => parseInt(data[kelurahan]["Rumah Sakit"]) || 0);
                
                kelurahanChart.data.labels = kelurahanNames;
                kelurahanChart.data.datasets[0].data = apotekData;
                kelurahanChart.data.datasets[1].data = klinikData;
                kelurahanChart.data.datasets[2].data = puskesmasData;
                kelurahanChart.data.datasets[3].data = rumahSakitData;
                kelurahanChart.options.plugins.title.text = `Fasilitas Kesehatan per Kelurahan - ${selectedKecamatan}`;
                kelurahanChart.update();
            })
            .catch(error => {
                console.error('Error updating kelurahan chart:', error);
                showErrorMessage('Gagal memuat data kelurahan.');
            });
    }
});