<?php

namespace App\Http\Controllers;

use App\Models\Apoteks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApotekController extends Controller 
{
    public function index() 
    {
        // Ambil semua data apotek
        $apotekData = DB::table('apotek')->get();

        return view('apotek.index', compact('apotekData'));
    }

    /**
     * Menampilkan form edit untuk apotek
     */
    public function edit($id)
    {
        // Menggunakan Query Builder untuk mendapatkan data apotek berdasarkan ID
        $apotek = DB::table('apotek')->where('id_apotek', $id)->first();
        
        if (!$apotek) {
            return redirect()->route('apotek.index')
                ->with('error', 'Apotek tidak ditemukan!');
        }
        
        return view('apotek.edit', compact('apotek'));
    }

    /**
     * Mengupdate data apotek berdasarkan input form
     */
    public function update(Request $request, $id)
    {
        // Log request untuk debugging
        Log::info('Update request untuk apotek ID: ' . $id);
        Log::info('Request data: ', $request->all());

        // Validasi input
        $validatedData = $request->validate([
            'nama_apotek' => 'required|string|max:255',
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
            $updated = DB::table('apotek')
                ->where('id_apotek', $id)
                ->update([
                    'nama_apotek' => $request->nama_apotek,
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
                return redirect()->route('apotek.index')
                    ->with('success', 'Data apotek berhasil diperbarui!');
            } else {
                return redirect()->route('apotek.index')
                    ->with('warning', 'Tidak ada perubahan data yang disimpan!');
            }
                
        } catch (\Exception $e) {
            // Log error jika terjadi
            Log::error('Gagal update apotek ID ' . $id . ': ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Hapus data apotek berdasarkan ID
            $deleted = DB::table('apotek')->where('id_apotek', $id)->delete();

            if ($deleted) {
                return redirect()->route('apotek.index')
                    ->with('success', 'Data apotek berhasil dihapus!');
            } else {
                return redirect()->route('apotek.index')
                    ->with('warning', 'Data apotek tidak ditemukan atau gagal dihapus!');
            }
        } catch (\Exception $e) {
            // Log error jika terjadi
            Log::error('Gagal menghapus apotek ID ' . $id . ': ' . $e->getMessage());

            return redirect()->route('apotek.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Migrasi data dari faskes ke apotek
     */
    public function migrateFaskesToApotek()
    {
        // Kode migrasi yang sesuai dengan kebutuhan
    }
}