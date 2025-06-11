<?php

namespace App\Http\Controllers;

use App\Models\Faskes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Debug: Cek jumlah data
        $totalFaskes = Faskes::count();
        \Log::info("Total Faskes: " . $totalFaskes);

        // Ambil statistik fasilitas kesehatan per kecamatan
        $faskesStatistics = Faskes::select('kecamatan', 'fasilitas', DB::raw('count(*) as count'))
            ->groupBy('kecamatan', 'fasilitas')
            ->get()
            ->groupBy('kecamatan')
            ->map(function ($kecamatanGroup) {
                // Inisialisasi default dengan 0 untuk semua jenis fasilitas
                $stats = [
                    'Apotek' => 0,
                    'Klinik' => 0,
                    'Puskesmas' => 0,
                    'Rumah Sakit' => 0
                ];

                // Update dengan data aktual
                foreach ($kecamatanGroup as $item) {
                    $stats[$item->fasilitas] = $item->count;
                }

                return $stats;
            });

        // Debug: Cek statistik yang akan dikirim
        \Log::info("Faskes Statistics:", $faskesStatistics->toArray());

        return view('landingpage', [
            'faskesStatistics' => $faskesStatistics
        ]);
    }
}