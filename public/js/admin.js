$(document).ready(function() {
    // Add fade-in animation to cards
    $('.card').addClass('fade-in');
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Sidebar toggle for mobile - Fixed implementation
    $('#sidebarToggle').on('click', function(e) {
        e.preventDefault();
        $('.sidebar').toggleClass('show-mobile-sidebar');
        $('body').toggleClass('sidebar-open');
    });
    
    // Close sidebar when clicking outside (for mobile)
    $(document).on('click', function(e) {
        if (window.innerWidth < 768) {
            if (!$(e.target).closest('.sidebar').length && 
                !$(e.target).closest('#sidebarToggle').length && 
                $('.sidebar').hasClass('show-mobile-sidebar')) {
                $('.sidebar').removeClass('show-mobile-sidebar');
                $('body').removeClass('sidebar-open');
            }
        }
    });
    
    // Reset sidebar state on window resize
    $(window).on('resize', function() {
        if (window.innerWidth >= 768) {
            $('.sidebar').removeClass('show-mobile-sidebar');
            $('body').removeClass('sidebar-open');
        }
    });
    
    // Search feature functionality for global search (if exists)
    if (typeof searchRoute !== 'undefined') {
        $('#searchButton').click(function() {
            handleGlobalSearch();
        });
        
        $('#closeSearchResults').click(function() {
            $('#searchResults').addClass('d-none');
        });
        
        // Enter key for search
        $('#searchFaskes').keypress(function(e) {
            if (e.which === 13) {
                handleGlobalSearch();
            }
        });
    }
    
    // Notification dropdown
    $('.notification-toggle').click(function() {
        $('#notificationDropdown').toggleClass('show');
    });
    
    $(document).click(function(e) {
        if (!$(e.target).closest('.notification-area').length) {
            $('#notificationDropdown').removeClass('show');
        }
    });
    
    // Table responsiveness enhancements
    enhanceTableResponsiveness();
    
    // Kecamatan filter auto-submit
    $('select[name="kecamatan"]').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Enter key for search inputs
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });
    
    // Setup delete modal
    setupDeleteModal();
    
    // Add horizontal scroll hint for tables on mobile
    addTableScrollHint();
});

/**
 * Handle global search functionality
 */
function handleGlobalSearch() {
    const query = $('#searchFaskes').val();
    if (query.length < 3) {
        showAlert('warning', 'Masukkan minimal 3 karakter untuk pencarian');
        return;
    }
    
    // Show loading indicator
    $('#searchButton').html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: searchRoute,
        type: 'GET',
        data: { query: query },
        dataType: 'json',
        success: function(data) {
            // Reset button
            $('#searchButton').html('<i class="fas fa-search"></i> Cari');
            
            // Show results
            $('#searchResults').removeClass('d-none');
            $('#resultsList').empty();
            
            if (data.length === 0) {
                $('#resultsList').html('<p class="text-center">Tidak ada hasil yang ditemukan</p>');
                return;
            }
            
            let html = '<div class="list-group">';
            data.forEach(item => {
                html += `
                    <a href="${adminUrl}/faskes/edit/${item.id}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${item.nama}</h6>
                            <small class="text-muted">${item.fasilitas}</small>
                        </div>
                        <p class="mb-1">${item.alamat}</p>
                        <small>${item.kecamatan}</small>
                    </a>
                `;
            });
            html += '</div>';
            
            $('#resultsList').html(html);
        },
        error: function(error) {
            $('#searchButton').html('<i class="fas fa-search"></i> Cari');
            console.error('Error searching faskes:', error);
            $('#resultsList').html('<p class="text-center text-danger">Terjadi kesalahan saat melakukan pencarian</p>');
        }
    });
}

/**
 * Show alert message
 * @param {string} type - Alert type (success, info, warning, danger)
 * @param {string} message - Alert message
 */
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insert alert before main content
    $('.container-fluid').first().prepend(alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}

/**
 * Enhance table responsiveness
 */
function enhanceTableResponsiveness() {
    // Make sure tables are properly wrapped in responsive containers
    $('.table:not(.dataTable)').each(function() {
        if (!$(this).parent().hasClass('table-responsive')) {
            $(this).wrap('<div class="table-responsive"></div>');
        }
    });
    
    // For mobile, add data attributes to help identify columns
    if (window.innerWidth < 768) {
        $('.facility-table').each(function() {
            const headers = [];
            
            // Get all headers
            $(this).find('thead th').each(function(index) {
                headers[index] = $(this).text().trim();
            });
            
            // Add data-label attribute to each cell
            $(this).find('tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    $(this).attr('data-label', headers[index]);
                });
            });
        });
    }
}

/**
 * Setup delete confirmation modal
 */
function setupDeleteModal() {
    $('.modal[id*="delete"]').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const name = button.data('name');
        const type = button.data('type') || '';
        
        // Set the name in the confirmation text
        $('[id*="delete-"][id$="-name"]', this).text(name);
        
        // Update form action URL
        const form = $('form[id*="delete"]', this);
        const baseUrl = form.data('base-url') || form.attr('action').split('/').slice(0, -1).join('/');
        form.attr('action', `${baseUrl}/${id}`);
    });
}

/**
 * Add table scroll hint for mobile
 */
function addTableScrollHint() {
    if (window.innerWidth < 992) {
        $('.table-responsive').each(function() {
            const table = $(this).find('table');
            if (table.width() > $(this).width()) {
                // Only add hint if table is wider than container
                if (!$(this).find('.scroll-hint').length) {
                    $(this).prepend('<div class="scroll-hint">← Geser untuk melihat lebih banyak →</div>');
                    
                    // Auto-hide after 3 seconds
                    setTimeout(() => {
                        $(this).find('.scroll-hint').fadeOut();
                    }, 3000);
                }
            }
        });
    }
}