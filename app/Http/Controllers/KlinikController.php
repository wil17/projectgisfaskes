<?php

namespace App\Http\Controllers;

use App\Models\Kliniks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KlinikController extends Controller 
{
    public function index() 
    {
        $klinikData = DB::table('klinik')->get();

        return view('klinik.index', compact('klinikData'));
    }
    

    /**
     * Menampilkan form edit untuk klinik
     */
    public function edit($id)
    {
        // Menggunakan Query Builder untuk mendapatkan data klinik berdasarkan ID
        $klinik = DB::table('klinik')->where('id_klinik', $id)->first();
        
        if (!$klinik) {
            return redirect()->route('klinik.index')
                ->with('error', 'Klinik tidak ditemukan!');
        }
        
        return view('klinik.edit', compact('klinik'));
    }

    /**
     * Mengupdate data klinik berdasarkan input form
     */
    public function update(Request $request, $id)
    {
        // Log request untuk debugging
        Log::info('Update request untuk klinik ID: ' . $id);
        Log::info('Request data: ', $request->all());

        // Validasi input
        $validatedData = $request->validate([
            'nama_klinik' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'skala_usaha' => 'nullable|string|max:255',
            'tgl_berdiri' => 'nullable|string|max:255', // Ubah validasi menjadi string
            'tenaga_kerja' => 'nullable|integer',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        try {
            // Update data menggunakan Query Builder
            $updated = DB::table('klinik')
                ->where('id_klinik', $id)
                ->update([
                    'nama_klinik' => $request->nama_klinik,
                    'skala_usaha' => $request->skala_usaha,
                    'alamat' => $request->alamat,
                    'kota' => $request->kota,
                    'kecamatan' => $request->kecamatan,
                    'kelurahan' => $request->kelurahan,
                    'tgl_berdiri' => $request->tgl_berdiri, // Sudah berupa string, tidak perlu konversi
                    'tenaga_kerja' => $request->tenaga_kerja,
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                ]);

            // Log hasil update
            Log::info('Update result: ' . ($updated ? 'Berhasil' : 'Gagal'));

            if ($updated) {
                return redirect()->route('klinik.index')
                    ->with('success', 'Data klinik berhasil diperbarui!');
            } else {
                return redirect()->route('klinik.index')
                    ->with('warning', 'Tidak ada perubahan data yang disimpan!');
            }
                
        } catch (\Exception $e) {
            // Log error jika terjadi
            Log::error('Gagal update klinik ID ' . $id . ': ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
{
    try {
        // Hapus data klinik berdasarkan ID
        $deleted = DB::table('klinik')->where('id_klinik', $id)->delete();

        if ($deleted) {
            return redirect()->route('klinik.index')
                ->with('success', 'Data klinik berhasil dihapus!');
        } else {
            return redirect()->route('klinik.index')
                ->with('warning', 'Data klinik tidak ditemukan atau gagal dihapus!');
        }
    } catch (\Exception $e) {
        // Log error jika terjadi
        Log::error('Gagal menghapus klinik ID ' . $id . ': ' . $e->getMessage());

        return redirect()->route('klinik.index')
            ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
    }
}


    /**
     * Migrasi data dari faskes ke klinik
     */
    public function migrateFaskesToKlinik()
    {
        // Kode migrasi yang sudah ada sebelumnya
    }
}