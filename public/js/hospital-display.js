// Auto-submit when kecamatan selection changes
document.getElementById('kecamatanSelect').addEventListener('change', function() {
    document.getElementById('kecamatanForm').submit();
});

// Modal detail functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get all detail buttons
    const detailButtons = document.querySelectorAll('.view-detail');
    let map = null;
    let marker = null;
    
    // Function to format poliklinik list
    function formatPoliklinik(poliklinikString) {
        if (!poliklinikString || poliklinikString === 'null') {
            return '<p class="text-center text-muted my-3">Tidak ada data poliklinik</p>';
        }
        
        const polikliniks = poliklinikString.split(',').map(item => item.trim()).filter(item => item);
        if (polikliniks.length === 0) {
            return '<p class="text-center text-muted my-3">Tidak ada data poliklinik</p>';
        }
        
        // Group polikliniks by category where possible
        const categories = {
            'Bedah': [],
            'Penyakit Dalam': [],
            'Anak': [],
            'Kandungan': [],
            'Saraf': [],
            'Kulit': [],
            'Mata': [],
            'THT': [],
            'Gigi': [],
            'Jantung': [],
            'Ortopedi': [],
            'Lainnya': []
        };
        
        polikliniks.forEach(poli => {
            let categorized = false;
            for (const category in categories) {
                if (poli.toLowerCase().includes(category.toLowerCase())) {
                    categories[category].push(poli);
                    categorized = true;
                    break;
                }
            }
            
            if (!categorized) {
                categories['Lainnya'].push(poli);
            }
        });
        
        // Build HTML
        let html = '<ul class="list-group list-group-flush">';
        
        // Display categories that have polikliniks
        for (const category in categories) {
            if (categories[category].length > 0) {
                // Add category header if it's not 'Lainnya' or if 'Lainnya' has items
                if (category !== 'Lainnya' || (category === 'Lainnya' && categories[category].length > 0)) {
                    html += `<li class="list-group-item bg-light fw-bold">${category}</li>`;
                }
                
                // Add polikliniks in this category
                categories[category].forEach(poli => {
                    html += `
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-circle-check text-success me-2"></i>
                            <span>${poli}</span>
                        </li>
                    `;
                });
            }
        }
        
        html += '</ul>';
        return html;
    }
    
    // Function to format doctor list
    function formatDokter(dokterString) {
        if (!dokterString || dokterString === 'null') {
            return '<p class="text-center text-muted my-3">Tidak ada data dokter</p>';
        }
        
        const dokters = dokterString.split(',').map(item => item.trim()).filter(item => item);
        if (dokters.length === 0) {
            return '<p class="text-center text-muted my-3">Tidak ada data dokter</p>';
        }
        
        // Group doctors by specialty where possible
        const specialties = {
            'Umum': [],
            'Bedah': [],
            'Penyakit Dalam': [],
            'Anak': [],
            'Kandungan': [],
            'THT': [],
            'Mata': [],
            'Gigi': [],
            'Kulit': [],
            'Lainnya': []
        };
        
        // Extract specialty from doctor name (if format includes specialty info)
        dokters.forEach(dokter => {
            let matched = false;
            const drInfo = dokter.split('.').join(' ');
            
            for (const specialty in specialties) {
                if (drInfo.toLowerCase().includes(specialty.toLowerCase()) || 
                    drInfo.toLowerCase().includes('sp.' + specialty.charAt(0).toLowerCase())) {
                    specialties[specialty].push(dokter);
                    matched = true;
                    break;
                }
            }
            
            if (!matched) {
                specialties['Lainnya'].push(dokter);
            }
        });
        
        // Build HTML
        let html = '<div class="accordion" id="doctorAccordion">';
        let accordionIndex = 0;
        
        for (const specialty in specialties) {
            if (specialties[specialty].length > 0) {
                accordionIndex++;
                const accordionId = 'collapse-' + specialty.toLowerCase().replace(/\s+/g, '-');
                
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-${accordionId}">
                            <button class="accordion-button ${accordionIndex > 1 ? 'collapsed' : ''}" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#${accordionId}" 
                                    aria-expanded="${accordionIndex === 1 ? 'true' : 'false'}" 
                                    aria-controls="${accordionId}">
                                ${specialty} (${specialties[specialty].length})
                            </button>
                        </h2>
                        <div id="${accordionId}" class="accordion-collapse collapse ${accordionIndex === 1 ? 'show' : ''}" 
                             aria-labelledby="heading-${accordionId}" data-bs-parent="#doctorAccordion">
                            <div class="accordion-body p-0">
                                <ul class="list-group list-group-flush">
                `;
                
                specialties[specialty].forEach(dokter => {
                    html += `
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-user-md text-primary me-2"></i>
                            <span>${dokter}</span>
                        </li>
                    `;
                });
                
                html += `
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
        
        html += '</div>';
        
        // If no doctors were categorized, just show a simple list
        if (accordionIndex === 0) {
            html = '<ul class="list-group list-group-flush">';
            dokters.forEach(dokter => {
                html += `
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-user-md text-primary me-2"></i>
                        <span>${dokter}</span>
                    </li>
                `;
            });
            html += '</ul>';
        }
        
        return html;
    }
    
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get data from button attributes
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const alamat = this.getAttribute('data-alamat');
            const poliklinik = this.getAttribute('data-poliklinik');
            const dokter = this.getAttribute('data-dokter');
            const kota = this.getAttribute('data-kota');
            const kecamatan = this.getAttribute('data-kecamatan');
            const kelurahan = this.getAttribute('data-kelurahan');
            const longitude = this.getAttribute('data-long');
            const latitude = this.getAttribute('data-lat');
            
            // Set data to modal elements
            document.getElementById('modal-nama').textContent = nama;
            document.getElementById('modal-alamat').textContent = alamat || 'Tidak ada data';
            document.getElementById('modal-kota').textContent = kota || 'Tidak ada data';
            document.getElementById('modal-kecamatan').textContent = kecamatan || 'Tidak ada data';
            document.getElementById('modal-kelurahan').textContent = kelurahan || 'Tidak ada data';
            document.getElementById('modal-koordinat').textContent = 
                (longitude && latitude && longitude !== 'null' && latitude !== 'null') ? 
                `${latitude}, ${longitude}` : 'Tidak ada data';
            
            // Format poliklinik data with the improved function
            document.getElementById('modal-poliklinik-list').innerHTML = formatPoliklinik(poliklinik);
            
            // Format dokter data with the improved function
            document.getElementById('modal-dokter-list').innerHTML = formatDokter(dokter);
            
            // Set link to map
            document.getElementById('lihatPeta').href = `{{ route('map.index') }}?focus=rumahsakit&id=${id}`;
            
            // Initialize modal map
            const hasCoordinates = longitude && latitude && longitude !== 'null' && latitude !== 'null';
            
            if (hasCoordinates) {
                // Initialize map after modal is shown
                const detailModal = document.getElementById('detailModal');
                detailModal.addEventListener('shown.bs.modal', function() {
                    if (map) {
                        map.remove();
                    }
                    
                    map = L.map('modalMap').setView([latitude, longitude], 15);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    
                    marker = L.marker([latitude, longitude]).addTo(map)
                        .bindPopup(`<b>${nama}</b><br>${alamat}`).openPopup();
                    
                    // Fix map display issue by invalidating size
                    setTimeout(function() {
                        map.invalidateSize();
                    }, 100);
                });
            } else {
                // If no coordinates, hide the map container
                document.getElementById('modalMap').style.display = 'none';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        });
    });
    
    // Fix map display issue when modal reopened
    document.getElementById('detailModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('modalMap').style.display = 'block';
    });
    
    // Add animation for cards
    const hospitalCards = document.querySelectorAll('.hospital-card');
    if (hospitalCards.length > 0) {
        hospitalCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index); // Stagger the animations
        });
    }
});