<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faskes;
use App\Models\KlasterPuskesmas;
use App\Models\LayananKlaster;
use App\Models\WilayahKerjaPuskesmas;
use Illuminate\Support\Facades\DB;

class DataLayananController extends Controller
{
    /**
     * Menampilkan halaman utama data layanan
     */
    public function index()
    {
        return view('data_layanan.index');
    }

    /**
     * Menampilkan data apotek dengan filter dan pagination
     */
    public function apotek(Request $request)
    {
        // Ambil data apotek dari tabel faskes
        $query = Faskes::where('fasilitas', 'Apotek');
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $search . '%')
                  ->orWhere('kelurahan', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kecamatan
        if ($request->has('kecamatan') && $request->kecamatan != '') {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Sorting
        $sortField = $request->get('sort', 'nama');
        if ($sortField == 'nama_apotek') {
            $sortField = 'nama'; // Konversi field nama dari frontend ke backend
        }
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $apotek = $query->paginate(10)->withQueryString();

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = Faskes::where('fasilitas', 'Apotek')
                        ->select('kecamatan')
                        ->distinct()
                        ->orderBy('kecamatan')
                        ->pluck('kecamatan');

        return view('data_layanan.apotek', compact('apotek', 'kecamatanList'));
    }

    /**
     * Menampilkan detail apotek
     */
    public function detailApotek($id)
    {
        $apotek = Faskes::where('id', $id)
                       ->where('fasilitas', 'Apotek')
                       ->firstOrFail();
        return view('data_layanan.detail_apotek', compact('apotek'));
    }

    /**
     * Menampilkan data klinik dengan filter dan pagination
     */
    public function klinik(Request $request)
    {
        // Ambil data klinik dari tabel faskes
        $query = Faskes::where('fasilitas', 'Klinik');
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $search . '%')
                  ->orWhere('kelurahan', 'like', '%' . $search . '%')
                  ->orWhere('skala_usaha', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kecamatan
        if ($request->has('kecamatan') && $request->kecamatan != '') {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Sorting
        $sortField = $request->get('sort', 'nama');
        if ($sortField == 'nama_klinik') {
            $sortField = 'nama'; // Konversi field nama dari frontend ke backend
        }
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $klinik = $query->paginate(10)->withQueryString();

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = Faskes::where('fasilitas', 'Klinik')
                        ->select('kecamatan')
                        ->distinct()
                        ->orderBy('kecamatan')
                        ->pluck('kecamatan');

        return view('data_layanan.klinik', compact('klinik', 'kecamatanList'));
    }

    /**
     * Menampilkan detail klinik
     */
    public function detailKlinik($id)
    {
        $klinik = Faskes::where('id', $id)
                       ->where('fasilitas', 'Klinik')
                       ->firstOrFail();
        return view('data_layanan.detail_klinik', compact('klinik'));
    }

    /**
     * Menampilkan data rumah sakit dengan filter dan pagination
     * Menggunakan Model Faskes dengan scope rumahsakits
     */
    public function rumahSakit(Request $request)
    {
        // Gunakan model Faskes dengan scope rumahsakits
        $query = Faskes::rumahsakits();
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%')
                  ->orWhere('poliklinik_dokter', 'like', '%' . $search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $search . '%')
                  ->orWhere('kelurahan', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kecamatan
        if ($request->has('kecamatan') && $request->kecamatan != '') {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Sorting
        $sortField = $request->get('sort', 'nama');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $rumahSakit = $query->paginate(5)->withQueryString();  // Mengurangi jumlah item per halaman menjadi 5

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = Faskes::rumahsakits()
                        ->select('kecamatan')
                        ->distinct()
                        ->orderBy('kecamatan')
                        ->pluck('kecamatan');

        return view('data_layanan.rumahsakit', compact('rumahSakit', 'kecamatanList'));
    }

    /**
     * Menampilkan detail rumah sakit
     */
    public function detailRumahSakit($id)
    {
        // Gunakan model Faskes
        $rumahSakit = Faskes::where('id', $id)
                    ->where('fasilitas', 'Rumah Sakit')
                    ->firstOrFail();
                        
        return view('data_layanan.detail_rumahsakit', compact('rumahSakit'));
    }

    /**
     * Menampilkan data puskesmas dengan filter dan pagination
     * PERUBAHAN: Menggunakan tabel faskes dengan fasilitas 'Puskesmas'
     */
    public function puskesmas(Request $request)
    {
        // Ambil data puskesmas dari tabel faskes
        $query = Faskes::where('fasilitas', 'Puskesmas');
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $search . '%')
                  ->orWhere('kelurahan', 'like', '%' . $search . '%')
                  ->orWhere('kepala_puskesmas', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kecamatan
        if ($request->has('kecamatan') && $request->kecamatan != '') {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Sorting
        $sortField = $request->get('sort', 'nama');
        if ($sortField == 'nama_puskesmas') {
            $sortField = 'nama'; // Konversi field nama dari frontend ke backend
        }
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $puskesmas = $query->paginate(10)->withQueryString();

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = Faskes::where('fasilitas', 'Puskesmas')
                        ->select('kecamatan')
                        ->distinct()
                        ->orderBy('kecamatan')
                        ->pluck('kecamatan');

        return view('data_layanan.puskesmas', compact('puskesmas', 'kecamatanList'));
    }

    public function detailPuskesmas($id)
    {
        // Ambil data puskesmas dari tabel faskes
        $puskesmas = Faskes::where('id', $id)
                       ->where('fasilitas', 'Puskesmas')
                       ->firstOrFail();
        
        // Ambil data wilayah kerja puskesmas
        $wilayahKerja = WilayahKerjaPuskesmas::where('id', $id)->get();
        
        // Identifikasi semua record klaster (yang memiliki nama_layanan NULL)
        $klaster = DB::table('layanan_klaster')
                  ->where('id', $id)
                  ->whereNull('nama_layanan')
                  ->orderBy('kode_klaster')
                  ->get();
                  
        // Jika tidak ada klaster dengan nama_layanan NULL, coba identifikasi klaster dengan pendekatan lain
        if ($klaster->isEmpty()) {
            // Ambil data klaster berdasarkan id_klaster yang unik
            $distinctKlasterIds = DB::table('layanan_klaster')
                                 ->where('id', $id)
                                 ->select('id_klaster')
                                 ->distinct()
                                 ->pluck('id_klaster');
            
            // Cari klaster dengan layanan untuk setiap id_klaster unik
            $klasterData = [];
            
            foreach ($distinctKlasterIds as $klasterId) {
                // Cari data yang bisa dijadikan klaster (prioritas: record dengan nama_layanan NULL)
                $klasterRecord = DB::table('layanan_klaster')
                                ->where('id', $id)
                                ->where('id_klaster', $klasterId)
                                ->whereNull('nama_layanan')
                                ->first();
                
                // Jika tidak ada record dengan nama_layanan NULL, ambil record pertama dari id_klaster tersebut
                if (!$klasterRecord) {
                    $klasterRecord = DB::table('layanan_klaster')
                                   ->where('id', $id)
                                   ->where('id_klaster', $klasterId)
                                   ->first();
                                   
                    if ($klasterRecord) {
                        // Buat "representasi klaster" dari record layanan
                        $klasterRecord->is_from_layanan = true;
                    }
                }
                
                if ($klasterRecord) {
                    $klasterData[] = $klasterRecord;
                }
            }
            
            $klaster = collect($klasterData);
        }
        
        // Persiapkan array untuk menyimpan layanan per klaster
        $layananPerKlaster = [];
        
        // Ambil data layanan untuk setiap klaster
        foreach ($klaster as $k) {
            $layanan = DB::table('layanan_klaster')
                      ->where('id', $id)
                      ->where('id_klaster', $k->id_klaster)
                      ->whereNotNull('nama_layanan')
                      ->get();
                      
            if ($layanan->isNotEmpty()) {
                $layananPerKlaster[$k->id_klaster] = $layanan;
            }
        }
        
        return view('data_layanan.detail_puskesmas', compact('puskesmas', 'klaster', 'wilayahKerja', 'layananPerKlaster'));
    }
}