<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apotek;
use App\Models\Kliniks;
use App\Models\RumahSakit;
use App\Models\Faskes;
use App\Models\Puskesmas;
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
        // Ambil data apotek
        $query = Apotek::query();
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_apotek', 'like', '%' . $search . '%')
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
        $sortField = $request->get('sort', 'nama_apotek');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $apotek = $query->paginate(10)->withQueryString();

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = DB::table('apotek')
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
        $apotek = Apotek::findOrFail($id);
        return view('data_layanan.detail_apotek', compact('apotek'));
    }

    /**
     * Menampilkan data klinik dengan filter dan pagination
     */
    public function klinik(Request $request)
    {
        // Ambil data klinik
        $query = Kliniks::query();
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_klinik', 'like', '%' . $search . '%')
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
        $sortField = $request->get('sort', 'nama_klinik');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $klinik = $query->paginate(10)->withQueryString();

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = DB::table('klinik')
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
        $klinik = Kliniks::findOrFail($id);
        return view('data_layanan.detail_klinik', compact('klinik'));
    }

    /**
     * Menampilkan data rumah sakit dengan filter dan pagination
     * Menggunakan Model Eloquent untuk mendapatkan accessor poliklinik_dokter_array
     */
    public function rumahSakit(Request $request)
    {
        // Gunakan model RumahSakit daripada query builder
        $query = RumahSakit::select('id_rs', 'nama_rs', 'alamat', 'poliklinik_dokter', 
                                     'kota', 'kecamatan', 'kelurahan', 'longitude', 'latitude')
                           ->distinct('id_rs');
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_rs', 'like', '%' . $search . '%')
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
        $sortField = $request->get('sort', 'nama_rs');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $rumahSakit = $query->paginate(5)->withQueryString();  // Mengurangi jumlah item per halaman menjadi 5

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = DB::table('rumahsakit')
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
        // Gunakan model RumahSakit daripada query builder
        $rumahSakit = RumahSakit::where('id_rs', $id)->first();
                        
        if (!$rumahSakit) {
            abort(404, 'Rumah Sakit tidak ditemukan');
        }
        
        return view('data_layanan.detail_rumahsakit', compact('rumahSakit'));
    }

    /**
     * Menampilkan data puskesmas dengan filter dan pagination
     */
    public function puskesmas(Request $request)
    {
        // Ambil data puskesmas
        $query = Puskesmas::query();
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_puskesmas', 'like', '%' . $search . '%')
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
        $sortField = $request->get('sort', 'nama_puskesmas');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $puskesmas = $query->paginate(10)->withQueryString();

        // Ambil daftar kecamatan untuk filter
        $kecamatanList = DB::table('puskesmas')
                        ->select('kecamatan')
                        ->distinct()
                        ->orderBy('kecamatan')
                        ->pluck('kecamatan');

        return view('data_layanan.puskesmas', compact('puskesmas', 'kecamatanList'));
    }

    /**
     * Menampilkan detail puskesmas
     */
    public function detailPuskesmas($id)
    {
        // Ambil data puskesmas
        $puskesmas = Puskesmas::where('id_puskesmas', $id)->first();
        
        if (!$puskesmas) {
            abort(404, 'Puskesmas tidak ditemukan');
        }
        
        // Ambil data klaster puskesmas
        $klaster = KlasterPuskesmas::where('id_puskesmas', $id)->get();
        
        // Ambil data wilayah kerja puskesmas
        $wilayahKerja = WilayahKerjaPuskesmas::where('id_puskesmas', $id)->get();
        
        // Ambil data layanan untuk setiap klaster
        $klasterIds = $klaster->pluck('id_klaster')->toArray();
        $layanan = LayananKlaster::whereIn('id_klaster', $klasterIds)->get();
        
        // Kelompokkan layanan berdasarkan id_klaster
        $layananPerKlaster = $layanan->groupBy('id_klaster');
        
        return view('data_layanan.detail_puskesmas', compact('puskesmas', 'klaster', 'wilayahKerja', 'layananPerKlaster'));
    }
}