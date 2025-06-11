@extends('layouts.admin')

@section('page-title', 'Manajemen Rumah Sakit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.rumahsakit.index') }}">Manajemen Faskes</a></li>
    <li class="breadcrumb-item active">Rumah Sakit</li>
@endsection

@section('content')
<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-hospital-alt me-2"></i> Daftar Rumah Sakit
            </h5>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-danger dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li><h6 class="dropdown-header">Export Data Rumah Sakit</h6></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.rumahsakit.export.pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}">
                                <i class="fas fa-globe me-2"></i> Semua Data
                            </a>
                        </li>
                        @if(count($kecamatans) > 0)
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Per Kecamatan</h6></li>
                            @foreach($kecamatans as $kecamatan)
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.rumahsakit.export.pdf', array_merge(request()->all(), ['kecamatan' => $kecamatan])) }}">
                                        <i class="fas fa-map-marker-alt me-2"></i> {{ $kecamatan }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <a href="{{ route('admin.rumahsakit.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Rumah Sakit
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search and filter bar - Simplified -->
        <form method="GET" action="{{ route('admin.rumahsakit.index') }}" id="filterForm">
            <div class="row g-3 mb-4">
                <div class="col-md-8 col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Cari rumah sakit...">
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <select name="kecamatan" class="form-select">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan }}" {{ request('kecamatan') == $kecamatan ? 'selected' : '' }}>
                                {{ $kecamatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    @if(request('search') || request('kecamatan'))
                        <a href="{{ route('admin.rumahsakit.index') }}" class="btn btn-outline-secondary ms-1">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
        
        @if(request('search') || request('kecamatan'))
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i> 
                Menampilkan hasil pencarian {{ $rumahsakits->total() }} rumah sakit
                @if(request('search'))
                    untuk "<strong>{{ request('search') }}</strong>"
                @endif
                @if(request('kecamatan'))
                    di kecamatan <strong>{{ request('kecamatan') }}</strong>
                @endif
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover facility-table align-middle" id="rumahsakitTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama Rumah Sakit</th>
                        <th width="20%">Alamat</th>
                        <th width="25%">Poliklinik & Dokter</th>
                        <th width="15%">Kecamatan</th>
                        <th width="15%">Kelurahan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rumahsakits as $index => $rumahsakit)
                        <tr>
                            <td>{{ ($rumahsakits->currentPage() - 1) * $rumahsakits->perPage() + $index + 1 }}</td>
                            <td>
                                <span class="fw-medium">{{ $rumahsakit->nama_rs }}</span>
                            </td>
                            <td>{{ $rumahsakit->alamat }}</td>
                            <td>
                                @php
                                    $poliklinikDokter = $rumahsakit->poliklinik_dokter;
                                    // If poliklinik_dokter is too long, truncate it
                                    if (strlen($poliklinikDokter) > 100) {
                                        $poliklinikDokter = substr($poliklinikDokter, 0, 100) . '...';
                                    }
                                    
                                    // Get the first few polikliniks to display
                                    $poliSections = preg_split('/;\s*/', $poliklinikDokter, -1, PREG_SPLIT_NO_EMPTY);
                                    $displayPoli = count($poliSections) > 3 
                                        ? implode('; ', array_slice($poliSections, 0, 3)) . '...' 
                                        : $poliklinikDokter;
                                @endphp
                                {{ $displayPoli }}
                            </td>
                            <td>{{ $rumahsakit->kecamatan }}</td>
                            <td>{{ $rumahsakit->kelurahan }}</td>
                            <td>
                                <div class="action-buttons d-flex">
                                    <a href="{{ route('admin.rumahsakit.edit', $rumahsakit->id_rs) }}" class="btn btn-sm btn-info me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal" 
                                        data-id="{{ $rumahsakit->id_rs }}"
                                        data-name="{{ $rumahsakit->nama_rs }}"
                                        title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-folder-open text-muted mb-2" style="font-size: 2.5rem;"></i>
                                    @if(request('search') || request('kecamatan'))
                                        <p class="mb-0 text-muted">Tidak ada rumah sakit yang sesuai dengan pencarian</p>
                                    @else
                                        <p class="mb-0 text-muted">Tidak ada data rumah sakit</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $rumahsakits->links() }}
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center">Apakah Anda yakin ingin menghapus rumah sakit <strong id="delete-rumahsakit-name"></strong>?</p>
                <p class="text-center text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus data dari sistem.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="POST" data-base-url="{{ url('admin/rumahsakit/destroy') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Setup delete modal
        $('#deleteModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            
            $('#delete-rumahsakit-name').text(name);
            $('#deleteForm').attr('action', `{{ url('admin/rumahsakit/destroy') }}/${id}`);
        });
        
        // Auto submit form when filter changes
        $('select[name="kecamatan"]').on('change', function() {
            $('#filterForm').submit();
        });
        
        // Submit form on Enter key press
        $('input[name="search"]').on('keypress', function(e) {
            if (e.which === 13) {
                $('#filterForm').submit();
            }
        });
        
        // Make sure tables are responsive
        if ($('#rumahsakitTable').width() > $('.card-body').width()) {
            $('#rumahsakitTable').addClass('table-responsive');
        }
    });
</script>
@endsection