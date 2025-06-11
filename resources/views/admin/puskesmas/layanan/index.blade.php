@extends('layouts.admin')

@section('page-title', 'Kelola Layanan Klaster')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.index') }}">Manajemen Puskesmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.edit', $puskesmas->id_puskesmas) }}">{{ $puskesmas->nama_puskesmas }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.klaster.index', $puskesmas->id_puskesmas) }}">Kelola Klaster</a></li>
    <li class="breadcrumb-item active">Layanan Klaster</li>
@endsection

@section('content')
<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-list-alt me-2"></i> Kelola Layanan - {{ $klaster->nama_klaster }}
            </h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLayananModal">
                <i class="fas fa-plus-circle me-1"></i> Tambah Layanan
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x"></i>
                </div>
                <div>
                    <h5 class="alert-heading">Informasi Klaster</h5>
                    <p class="mb-0">Penanggung Jawab: <strong>{{ $klaster->penanggung_jawab }}</strong></p>
                    <p class="mb-0">Kode Klaster: <strong>{{ $klaster->kode_klaster }}</strong></p>
                    <p class="mb-0">Total Layanan: <strong>{{ $layanan->count() }}</strong></p>
                    <p class="mb-0">Total Petugas: <strong>{{ $klaster->total_petugas }}</strong></p>
                </div>
            </div>
        </div>
        
        @if($layanan->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-list-alt text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-muted">Belum ada data layanan</h5>
                <p class="text-muted">Klik tombol "Tambah Layanan" untuk menambahkan layanan baru</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Layanan</th>
                            <th width="45%">Deskripsi Layanan</th>
                            <th width="10%">Jumlah Petugas</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($layanan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_layanan }}</td>
                                <td>{{ Str::limit($item->deskripsi_layanan, 100) }}</td>
                                <td class="text-center">{{ $item->jumlah_petugas }}</td>
                                <td>
                                    <div class="action-buttons d-flex">
                                        <button type="button" class="btn btn-sm btn-info me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editLayananModal" 
                                            data-id="{{ $item->id_layanan }}"
                                            data-nama="{{ $item->nama_layanan }}"
                                            data-deskripsi="{{ $item->deskripsi_layanan }}"
                                            data-jumlah="{{ $item->jumlah_petugas }}"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteLayananModal" 
                                            data-id="{{ $item->id_layanan }}"
                                            data-nama="{{ $item->nama_layanan }}"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('admin.puskesmas.klaster.index', $puskesmas->id_puskesmas) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Klaster
            </a>
        </div>
    </div>
</div>

<!-- Create Layanan Modal -->
<div class="modal fade" id="createLayananModal" tabindex="-1" aria-labelledby="createLayananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createLayananModalLabel">Tambah Layanan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.puskesmas.layanan.store', $klaster->id_klaster) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_layanan" class="form-label">Nama Layanan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_layanan" name="nama_layanan" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi_layanan" class="form-label">Deskripsi Layanan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deskripsi_layanan" name="deskripsi_layanan" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah_petugas" class="form-label">Jumlah Petugas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah_petugas" name="jumlah_petugas" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Layanan Modal -->
<div class="modal fade" id="editLayananModal" tabindex="-1" aria-labelledby="editLayananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editLayananModalLabel">Edit Layanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLayananForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_layanan" class="form-label">Nama Layanan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_layanan" name="nama_layanan" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_deskripsi_layanan" class="form-label">Deskripsi Layanan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_deskripsi_layanan" name="deskripsi_layanan" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_jumlah_petugas" class="form-label">Jumlah Petugas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_jumlah_petugas" name="jumlah_petugas" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Layanan Modal -->
<div class="modal fade" id="deleteLayananModal" tabindex="-1" aria-labelledby="deleteLayananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteLayananModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center">Apakah Anda yakin ingin menghapus layanan <strong id="delete-layanan-name"></strong>?</p>
                <p class="text-center text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteLayananForm" action="" method="POST">
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
        // Setup edit modal
        $('#editLayananModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nama = button.data('nama');
            const deskripsi = button.data('deskripsi');
            const jumlah = button.data('jumlah');
            
            const modal = $(this);
            modal.find('#edit_nama_layanan').val(nama);
            modal.find('#edit_deskripsi_layanan').val(deskripsi);
            modal.find('#edit_jumlah_petugas').val(jumlah);
            
            $('#editLayananForm').attr('action', `{{ url('admin/puskesmas/layanan') }}/${id}`);
        });
        
        // Setup delete modal
        $('#deleteLayananModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nama = button.data('nama');
            
            $('#delete-layanan-name').text(nama);
            $('#deleteLayananForm').attr('action', `{{ url('admin/puskesmas/layanan') }}/${id}`);
        });
    });
</script>
@endsection