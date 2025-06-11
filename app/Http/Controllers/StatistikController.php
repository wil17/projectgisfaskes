<?php

namespace App\Http\Controllers;

use App\Models\Faskes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        return view('statistik'); // Pastikan tampilan Statistik.blade.php ada
    }
    
    // Metode untuk statistik faskes per kecamatan
    public function getFaskesStatistics()
    {
        // Ambil semua fasilitas kesehatan dikelompokkan berdasarkan kecamatan dan jenis fasilitas
        $faskesData = Faskes::select('kecamatan', 'fasilitas', DB::raw('count(*) as total'))
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->groupBy('kecamatan', 'fasilitas')
            ->get();
        
        // Organisasi data untuk konsumsi frontend
        $statistics = [];
        
        foreach ($faskesData as $faskes) {
            // Normalisasi nama kecamatan dan fasilitas
            $normalizedKecamatan = $this->normalizeString($faskes->kecamatan);
            $normalizedFasilitas = $this->normalizeString($faskes->fasilitas);
            
            // Inisialisasi kecamatan jika belum ada
            if (!isset($statistics[$normalizedKecamatan])) {
                $statistics[$normalizedKecamatan] = [
                    "Apotek" => 0,
                    "Klinik" => 0,
                    "Puskesmas" => 0,
                    "Rumah Sakit" => 0
                ];
            }
            
            // Tambahkan jumlah berdasarkan jenis fasilitas
            $statistics[$normalizedKecamatan][$normalizedFasilitas] = $faskes->total;
        }
        
        return response()->json($statistics);
    }
    
    // Metode untuk statistik apotek, klinik, dan rumah sakit per kelurahan
public function getKelurahanStatistics(Request $request)
{
    $kecamatan = $request->query('kecamatan');
    
    if (!$kecamatan) {
        return response()->json(['error' => 'Kecamatan parameter is required'], 400);
    }
    
    // Ambil data apotek, klinik, puskesmas, dan rumah sakit berdasarkan kecamatan dan kelurahan
    $kelurahanData = Faskes::select('kelurahan', 'fasilitas', DB::raw('count(*) as total'))
        ->where('kecamatan', $kecamatan)
        ->whereNotNull('kelurahan')
        ->where('kelurahan', '!=', '')
        ->whereIn('fasilitas', ['Apotek', 'Klinik', 'Puskesmas', 'Rumah Sakit'])  // Added Puskesmas
        ->groupBy('kelurahan', 'fasilitas')
        ->get();
    
    // Organisasi data untuk konsumsi frontend
    $statistics = [];
    
    foreach ($kelurahanData as $data) {
        $normalizedKelurahan = $this->normalizeString($data->kelurahan);
        $normalizedFasilitas = $this->normalizeString($data->fasilitas);
        
        // Inisialisasi kelurahan jika belum ada
        if (!isset($statistics[$normalizedKelurahan])) {
            $statistics[$normalizedKelurahan] = [
                "Apotek" => 0,
                "Klinik" => 0,
                "Puskesmas" => 0,  // Added Puskesmas
                "Rumah Sakit" => 0
            ];
        }
        
        // Tambahkan jumlah berdasarkan jenis fasilitas
        $statistics[$normalizedKelurahan][$normalizedFasilitas] = $data->total;
    }
    
    return response()->json($statistics);
}
    
    // Metode untuk mendapatkan statistik total per kecamatan
    public function getKecamatanTotals()
    {
        // Ambil total per jenis fasilitas untuk tiap kecamatan
        $kecamatanData = Faskes::select('kecamatan', 'fasilitas', DB::raw('count(*) as total'))
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->groupBy('kecamatan', 'fasilitas')
            ->get();
        
        // Organisasi data dalam format tabel
        $totals = [];
        
        foreach ($kecamatanData as $data) {
            $normalizedKecamatan = $this->normalizeString($data->kecamatan);
            $normalizedFasilitas = $this->normalizeString($data->fasilitas);
            
            // Inisialisasi kecamatan jika belum ada
            if (!isset($totals[$normalizedKecamatan])) {
                $totals[$normalizedKecamatan] = [
                    "Kecamatan" => $normalizedKecamatan,
                    "Apotek" => 0,
                    "Klinik" => 0,
                    "Puskesmas" => 0,
                    "Rumah Sakit" => 0
                ];
            }
            
            // Tambahkan jumlah berdasarkan jenis fasilitas
            $totals[$normalizedKecamatan][$normalizedFasilitas] = $data->total;
        }
        
        // Konversi ke array biasa untuk output JSON
        $result = array_values($totals);
        
        return response()->json($result);
    }
    
    // Fungsi helper untuk normalisasi string
    private function normalizeString($str)
    {
        return ucwords(strtolower($str));
    }
}