@extends('layouts.admin')

@section('page-title', 'Kelola Fasilitas Puskesmas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.index') }}">Manajemen Puskesmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.edit', $puskesmas->id_puskesmas) }}">{{ $puskesmas->nama_puskesmas }}</a></li>
    <li class="breadcrumb-item active">Kelola Fasilitas</li>
@endsection

@section('content')
<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
<i class="fas fa-building me-2"></i> Kelola Fasilitas - {{ $puskesmas->nama_puskesmas }}
            </h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFasilitasModal">
                <i class="fas fa-plus-circle me-1"></i> Tambah Fasilitas
            </button>
        </div>
    </div>
    
    <div class="card-body">
        @if($fasilitas->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-building text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-muted">Belum ada data fasilitas</h5>
                <p class="text-muted">Klik tombol "Tambah Fasilitas" untuk menambahkan fasilitas baru</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Fasilitas</th>
                            <th width="45%">Deskripsi</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fasilitas as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_fasilitas }}</td>
                                <td>
                                    @if($item->deskripsi)
                                        {{ Str::limit($item->deskripsi, 100) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons d-flex">
                                        <button type="button" class="btn btn-sm btn-info me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editFasilitasModal" 
                                            data-id="{{ $item->id_fasilitas }}"
                                            data-nama="{{ $item->nama_fasilitas }}"
                                            data-deskripsi="{{ $item->deskripsi }}"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteFasilitasModal" 
                                            data-id="{{ $item->id_fasilitas }}"
                                            data-nama="{{ $item->nama_fasilitas }}"
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
            <a href="{{ route('admin.puskesmas.edit', $puskesmas->id_puskesmas) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Create Fasilitas Modal -->
<div class="modal fade" id="createFasilitasModal" tabindex="-1" aria-labelledby="createFasilitasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createFasilitasModalLabel">Tambah Fasilitas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.puskesmas.fasilitas.store', $puskesmas->id_puskesmas) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_fasilitas" class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_fasilitas" name="nama_fasilitas" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
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

<!-- Edit Fasilitas Modal -->
<div class="modal fade" id="editFasilitasModal" tabindex="-1" aria-labelledby="editFasilitasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editFasilitasModalLabel">Edit Fasilitas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFasilitasForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_fasilitas" class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_fasilitas" name="nama_fasilitas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
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

<!-- Delete Fasilitas Modal -->
<div class="modal fade" id="deleteFasilitasModal" tabindex="-1" aria-labelledby="deleteFasilitasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteFasilitasModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center">Apakah Anda yakin ingin menghapus fasilitas <strong id="delete-fasilitas-name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteFasilitasForm" action="" method="POST">
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
        $('#editFasilitasModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nama = button.data('nama');
            const deskripsi = button.data('deskripsi');
            
            const modal = $(this);
            modal.find('#edit_nama_fasilitas').val(nama);
            modal.find('#edit_deskripsi').val(deskripsi);
            
            $('#editFasilitasForm').attr('action', `{{ url('admin/puskesmas/fasilitas') }}/${id}`);
        });
        
        // Setup delete modal
        $('#deleteFasilitasModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nama = button.data('nama');
            
            $('#delete-fasilitas-name').text(nama);
            $('#deleteFasilitasForm').attr('action', `{{ url('admin/puskesmas/fasilitas') }}/${id}`);
        });
    });
</script>
@endsection   