@extends('layouts.admin')

@section('page-title', 'Manajemen Puskesmas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.index') }}">Manajemen Faskes</a></li>
    <li class="breadcrumb-item active">Puskesmas</li>
@endsection

@section('content')
<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-clinic-medical me-2"></i> Daftar Puskesmas
            </h5>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-danger dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li><h6 class="dropdown-header">Export Data Puskesmas</h6></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.puskesmas.export.pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}">
                                <i class="fas fa-globe me-2"></i> Semua Data
                            </a>
                        </li>
                        @if(count($kecamatans) > 0)
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Per Kecamatan</h6></li>
                            @foreach($kecamatans as $kecamatan)
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.puskesmas.export.pdf', array_merge(request()->all(), ['kecamatan' => $kecamatan])) }}">
                                        <i class="fas fa-map-marker-alt me-2"></i> {{ $kecamatan }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <a href="{{ route('admin.puskesmas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Puskesmas
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search and filter bar - Simplified -->
        <form method="GET" action="{{ route('admin.puskesmas.index') }}" id="filterForm">
            <div class="row g-3 mb-4">
                <div class="col-md-8 col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Cari puskesmas...">
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
                        <a href="{{ route('admin.puskesmas.index') }}" class="btn btn-outline-secondary ms-1">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
        
        @if(request('search') || request('kecamatan'))
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i> 
                Menampilkan hasil pencarian {{ $puskesmas->total() }} puskesmas
                @if(request('search'))
                    untuk "<strong>{{ request('search') }}</strong>"
                @endif
                @if(request('kecamatan'))
                    di kecamatan <strong>{{ request('kecamatan') }}</strong>
                @endif
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover facility-table align-middle" id="puskesmasTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama Puskesmas</th>
                        <th width="25%">Alamat</th>
                        <th width="15%">Kepala Puskesmas</th>
                        <th width="15%">Kecamatan</th>
                        <th width="15%">Kelurahan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($puskesmas as $index => $pkm)
                        <tr>
                            <td>{{ ($puskesmas->currentPage() - 1) * $puskesmas->perPage() + $index + 1 }}</td>
                            <td>
                                <span class="fw-medium">{{ $pkm->nama }}</span>
                            </td>
                            <td>{{ $pkm->alamat }}</td>
                            <td>{{ $pkm->kepala_puskesmas }}</td>
                            <td>{{ $pkm->kecamatan }}</td>
                            <td>{{ $pkm->kelurahan }}</td>
                            <td>
                                <div class="action-buttons d-flex">
                                    <a href="{{ route('admin.puskesmas.edit', $pkm->id) }}" class="btn btn-sm btn-info me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal" 
                                        data-id="{{ $pkm->id }}"
                                        data-name="{{ $pkm->nama }}"
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
                                        <p class="mb-0 text-muted">Tidak ada puskesmas yang sesuai dengan pencarian</p>
                                    @else
                                        <p class="mb-0 text-muted">Tidak ada data puskesmas</p>
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
            {{ $puskesmas->links() }}
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
                <p class="text-center">Apakah Anda yakin ingin menghapus puskesmas <strong id="delete-puskesmas-name"></strong>?</p>
                <p class="text-center text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus data dari sistem.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="POST" data-base-url="{{ url('admin/puskesmas/destroy') }}">
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
            
            $('#delete-puskesmas-name').text(name);
            $('#deleteForm').attr('action', `{{ url('admin/puskesmas/destroy') }}/${id}`);});
        
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
        if ($('#puskesmasTable').width() > $('.card-body').width()) {
            $('#puskesmasTable').addClass('table-responsive');
        }
    });
</script>
@endsection