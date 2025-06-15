<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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