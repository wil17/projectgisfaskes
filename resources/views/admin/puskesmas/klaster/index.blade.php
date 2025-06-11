@extends('layouts.admin')

@section('page-title', 'Kelola Klaster Puskesmas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.index') }}">Manajemen Puskesmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.puskesmas.edit', $puskesmas->id_puskesmas) }}">{{ $puskesmas->nama_puskesmas }}</a></li>
    <li class="breadcrumb-item active">Kelola Klaster</li>
@endsection

@section('content')
<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-layer-group me-2"></i> Kelola Klaster {{ $puskesmas->nama_puskesmas }}
            </h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKlasterModal">
                <i class="fas fa-plus-circle me-1"></i> Tambah Klaster
            </button>
        </div>
    </div>
    
    <div class="card-body">
        @if($klaster->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-layer-group text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-muted">Belum ada data klaster</h5>
                <p class="text-muted">Klik tombol "Tambah Klaster" untuk menambahkan klaster baru</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="5%">Kode</th>
                            <th width="20%">Nama Klaster</th>
                            <th width="25%">Penanggung Jawab</th>
                            <th width="10%">Jumlah Petugas</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($klaster as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode_klaster }}</td>
                                <td>{{ $item->nama_klaster }}</td>
                                <td>{{ $item->penanggung_jawab }}</td>
                                <td class="text-center">{{ $item->jumlah_petugas }}</td>
                                <td>
                                    <div class="action-buttons d-flex">
                                        <a href="{{ route('admin.puskesmas.layanan.index', $item->id_klaster) }}" class="btn btn-sm btn-primary me-1" title="Kelola Layanan">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editKlasterModal" 
                                            data-id="{{ $item->id_klaster }}"
                                            data-nama="{{ $item->nama_klaster }}"
                                            data-kode="{{ $item->kode_klaster }}"
                                            data-penanggung="{{ $item->penanggung_jawab }}"
                                            data-jumlah-petugas="{{ $item->jumlah_petugas }}"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteKlasterModal" 
                                            data-id="{{ $item->id_klaster }}"
                                            data-nama="{{ $item->nama_klaster }}"
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

<!-- Create Klaster Modal -->
<div class="modal fade" id="createKlasterModal" tabindex="-1" aria-labelledby="createKlasterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createKlasterModalLabel">Tambah Klaster Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.puskesmas.klaster.store', $puskesmas->id_puskesmas) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kode_klaster" class="form-label">Kode Klaster <span class="text-danger">*</span></label>
                            <select class="form-select" id="kode_klaster" name="kode_klaster" required>
                                <option value="">-- Pilih Kode Klaster --</option>
                                <option value="1">1 - Manajemen</option>
                                <option value="2">2 - Ibu dan Anak</option>
                                <option value="3">3 - Usia Produktif dan Lanjut Usia</option>
                                <option value="4">4 - Penanggulangan Penyakit Menular</option>
                                <option value="5">5 - Pelayanan Kesehatan Lintas Klaster</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_klaster" class="form-label">Nama Klaster <span class="text-danger">*</span></label>
                            <select class="form-select" id="nama_klaster" name="nama_klaster" required>
                                <option value="">-- Pilih Nama Klaster --</option>
                                <option value="Manajemen">Manajemen</option>
                                <option value="Ibu dan Anak">Ibu dan Anak</option>
                                <option value="Usia Dewasa dan Lansia">Usia Dewasa dan Lansia</option>
                                <option value="Penanggulangan Penyakit Menular">Penanggulangan Penyakit Menular</option>
                                <option value="Lintas Klaster">Lintas Klaster</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="penanggung_jawab" class="form-label">Penanggung Jawab <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab" required>
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

<!-- Edit Klaster Modal -->
<div class="modal fade" id="editKlasterModal" tabindex="-1" aria-labelledby="editKlasterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editKlasterModalLabel">Edit Klaster</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editKlasterForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_kode_klaster" class="form-label">Kode Klaster <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_kode_klaster" name="kode_klaster" required>
                                <option value="">-- Pilih Kode Klaster --</option>
                                <option value="1">1 - Manajemen</option>
                                <option value="2">2 - Ibu dan Anak</option>
                                <option value="3">3 - Usia Produktif dan Lanjut Usia</option>
                                <option value="4">4 - Penanggulangan Penyakit Menular</option>
                                <option value="5">5 - Pelayanan Kesehatan Lintas Klaster</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nama_klaster" class="form-label">Nama Klaster <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_nama_klaster" name="nama_klaster" required>
                                <option value="">-- Pilih Nama Klaster --</option>
                                <option value="Manajemen">Manajemen</option>
                                <option value="Ibu dan Anak">Ibu dan Anak</option>
                                <option value="Usia Dewasa dan Lansia">Usia Dewasa dan Lansia</option>
                                <option value="Penanggulangan Penyakit Menular">Penanggulangan Penyakit Menular</option>
                                <option value="Lintas Klaster">Lintas Klaster</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_penanggung_jawab" class="form-label">Penanggung Jawab <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_penanggung_jawab" name="penanggung_jawab" required>
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

<!-- Delete Klaster Modal -->
<div class="modal fade" id="deleteKlasterModal" tabindex="-1" aria-labelledby="deleteKlasterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteKlasterModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center">Apakah Anda yakin ingin menghapus klaster <strong id="delete-klaster-name"></strong>?</p>
                <p class="text-center text-danger">Tindakan ini akan menghapus semua layanan yang terkait dengan klaster ini.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteKlasterForm" action="" method="POST">
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
        // Sync kode_klaster and nama_klaster on create form
        $('#kode_klaster').on('change', function() {
            const kodeKlaster = $(this).val();
            if (kodeKlaster) {
                $('#nama_klaster').val(getKlasterNameFromCode(kodeKlaster));
            } else {
                $('#nama_klaster').val('');
            }
        });
        
        $('#nama_klaster').on('change', function() {
            const namaKlaster = $(this).val();
            if (namaKlaster) {
                $('#kode_klaster').val(getKlasterCodeFromName(namaKlaster));
            } else {
                $('#kode_klaster').val('');
            }
        });
        
        // Sync kode_klaster and nama_klaster on edit form
        $('#edit_kode_klaster').on('change', function() {
            const kodeKlaster = $(this).val();
            if (kodeKlaster) {
                $('#edit_nama_klaster').val(getKlasterNameFromCode(kodeKlaster));
            } else {
                $('#edit_nama_klaster').val('');
            }
        });
        
        $('#edit_nama_klaster').on('change', function() {
            const namaKlaster = $(this).val();
            if (namaKlaster) {
                $('#edit_kode_klaster').val(getKlasterCodeFromName(namaKlaster));
            } else {
                $('#edit_kode_klaster').val('');
            }
        });
        
        // Setup edit modal
        $('#editKlasterModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nama = button.data('nama');
            const kode = button.data('kode');
            const penanggung = button.data('penanggung');
            const jumlahPetugas = button.data('jumlah-petugas');
            
            const modal = $(this);
            modal.find('#edit_nama_klaster').val(nama);
            modal.find('#edit_kode_klaster').val(kode);
            modal.find('#edit_penanggung_jawab').val(penanggung);
            modal.find('#edit_jumlah_petugas').val(jumlahPetugas);
            
            $('#editKlasterForm').attr('action', `{{ url('admin/puskesmas/klaster') }}/${id}`);
        });
        
        // Setup delete modal
        $('#deleteKlasterModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const nama = button.data('nama');
            
            $('#delete-klaster-name').text(nama);
            $('#deleteKlasterForm').attr('action', `{{ url('admin/puskesmas/klaster') }}/${id}`);
        });
        
        function getKlasterNameFromCode(code) {
            const klasterMap = {
                '1': 'Manajemen',
                '2': 'Ibu dan Anak',
                '3': 'Usia Dewasa dan Lansia',
                '4': 'Penanggulangan Penyakit Menular',
                '5': 'Lintas Klaster'
            };
            return klasterMap[code] || '';
        }
        
        function getKlasterCodeFromName(name) {
            const klasterMap = {
                'Manajemen': '1',
                'Ibu dan Anak': '2',
                'Usia Dewasa dan Lansia': '3',
                'Penanggulangan Penyakit Menular': '4',
                'Lintas Klaster': '5'
            };
            return klasterMap[name] || '';
        }
    });
</script>
@endsection