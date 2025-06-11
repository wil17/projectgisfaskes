<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class RumahSakitImportController extends Controller
{
    /**
     * Tampilkan form untuk upload file Excel
     */
    public function showImportForm()
    {
        return view('admin.rumahsakit.import');
    }

    /**
     * Proses impor data dari file Excel
     */
    public function processImport(Request $request)
    {
        // Validasi request
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ], [
            'excel_file.required' => 'File Excel diperlukan',
            'excel_file.file' => 'Upload harus berupa file',
            'excel_file.mimes' => 'Format file harus xlsx, xls, atau csv'
        ]);

        // Mendapatkan file Excel yang diupload
        $file = $request->file('excel_file');
        
        try {
            // Load file Excel menggunakan PhpSpreadsheet
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Periksa apakah file memiliki data
            if (count($rows) <= 1) {
                return back()->with('error', 'File Excel tidak berisi data yang cukup');
            }
            
            // Ambil header (baris pertama)
            $header = array_shift($rows);
            
            // Cari indeks kolom yang diperlukan
            $columnIndexes = $this->findColumnIndexes($header);
            
            if (!$columnIndexes) {
                return back()->with('error', 'Format file Excel tidak sesuai. Pastikan file memiliki kolom: nama_rs, poliklinik, nama_dokter, kota, kelurahan');
            }
            
            // Mulai transaksi DB
            DB::beginTransaction();
            
            $rowsUpdated = 0;
            $errorRows = [];
            
            // Loop melalui setiap baris data
            foreach ($rows as $index => $row) {
                // Skip baris kosong
                if (empty($row[$columnIndexes['nama_rs']])) {
                    continue;
                }
                
                $nama_rs = $row[$columnIndexes['nama_rs']];
                $poliklinik = $row[$columnIndexes['poliklinik']] ?? null;
                $nama_dokter = $row[$columnIndexes['nama_dokter']] ?? null;
                $kota = $row[$columnIndexes['kota']] ?? null;
                $kelurahan = $row[$columnIndexes['kelurahan']] ?? null;
                
                try {
                    // Update data rumah sakit berdasarkan nama_rs
                    $updated = DB::table('rumahsakit')
                        ->where('nama_rs', $nama_rs)
                        ->update([
                            'poliklinik' => $poliklinik,
                            'nama_dokter' => $nama_dokter,
                            'kota' => $kota,
                            'kelurahan' => $kelurahan,
                            'updated_at' => Carbon::now(),
                        ]);
                    
                    if ($updated) {
                        $rowsUpdated++;
                    } else {
                        // Rumah sakit dengan nama tersebut tidak ditemukan
                        $errorRows[] = [
                            'row' => $index + 2, // +2 karena indeks array dimulai dari 0 dan sudah menghapus header
                            'nama_rs' => $nama_rs,
                            'error' => 'Rumah sakit tidak ditemukan'
                        ];
                    }
                } catch (\Exception $e) {
                    // Gagal memperbarui baris tersebut
                    $errorRows[] = [
                        'row' => $index + 2,
                        'nama_rs' => $nama_rs,
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            // Commit transaksi jika semua berjalan baik
            DB::commit();
            
            // Tampilkan hasil impor
            return back()->with([
                'success' => "Berhasil memperbarui data $rowsUpdated rumah sakit",
                'errors' => $errorRows
            ]);
            
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
    
    /**
     * Find column indexes from header row
     * 
     * @param array $header
     * @return array|false
     */
    private function findColumnIndexes(array $header)
    {
        $columnIndexes = [];
        $requiredColumns = ['nama_rs', 'poliklinik', 'nama_dokter', 'kota', 'kelurahan'];
        
        foreach ($header as $index => $columnName) {
            $columnName = strtolower(trim($columnName));
            if (in_array($columnName, $requiredColumns)) {
                $columnIndexes[$columnName] = $index;
            }
        }
        
        // Pastikan semua kolom yang diperlukan ditemukan
        foreach ($requiredColumns as $column) {
            if (!isset($columnIndexes[$column])) {
                return false;
            }
        }
        
        return $columnIndexes;
    }
}