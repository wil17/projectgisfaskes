<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Faskes;
use App\Models\ActivityLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin
     */
    public function index()
    {
        // Dapatkan statistik untuk ditampilkan di dashboard
        $statistics = $this->getStatistics();
        
        // Dapatkan data aktivitas terbaru
        $recentActivities = $this->getRecentActivities();
        
        return view('admin.dashboard', compact('statistics', 'recentActivities'));
    }
    
    /**
     * Mendapatkan statistik dasar faskes
     */
    public function getStatistics()
    {
        $apotek = DB::table('faskes')->where('fasilitas', 'Apotek')->count();
        $klinik = DB::table('faskes')->where('fasilitas', 'Klinik')->count();
        $puskesmas = DB::table('faskes')->where('fasilitas', 'Puskesmas')->count();
        $rumahSakit = DB::table('faskes')->where('fasilitas', 'Rumah Sakit')->count();
        
        return [
            'apotek' => $apotek,
            'klinik' => $klinik,
            'puskesmas' => $puskesmas,
            'rumahSakit' => $rumahSakit,
            'total' => $apotek + $klinik + $puskesmas + $rumahSakit
        ];
    }
    
    /**
     * Mendapatkan aktivitas terbaru dari log aktivitas
     */
    public function getRecentActivities()
    {
        $activities = ActivityLog::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return (object) [
                    'id' => $activity->id,
                    'nama' => $activity->model_name,
                    'fasilitas' => $activity->facility_type,
                    'action' => $activity->action_name,
                    'formatted_date' => $activity->created_at->diffForHumans(),
                    'description' => $activity->formatted_description,
                    'icon' => $activity->action_icon,
                    'color' => $activity->action_color,
                    'details' => $this->getActivityDetails($activity)
                ];
            });
        
        return $activities;
    }
    
    /**
     * Get detailed information about the activity
     */
    private function getActivityDetails($activity)
    {
        $details = [];
        
        if ($activity->action === 'update' && $activity->new_values) {
            foreach ($activity->new_values as $field => $change) {
                // Skip ID fields
                if (in_array($field, ['id', 'id_apotek', 'id_klinik', 'created_at', 'updated_at'])) {
                    continue;
                }
                
                if (is_array($change) && isset($change['old'], $change['new'])) {
                    $fieldName = $this->getFieldDisplayName($field);
                    $details[] = "{$fieldName}: {$change['old']} → {$change['new']}";
                }
            }
        }
        
        return $details;
    }
    
    /**
     * Get user-friendly field names
     */
    private function getFieldDisplayName($field)
    {
        $fieldNames = [
            'nama' => 'Nama',
            'nama_apotek' => 'Nama Apotek',
            'nama_klinik' => 'Nama Klinik',
            'alamat' => 'Alamat',
            'kecamatan' => 'Kecamatan',
            'kelurahan' => 'Kelurahan',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'jam_buka' => 'Jam Buka',
            'jam_tutup' => 'Jam Tutup',
            'no_telepon' => 'No. Telepon',
            'skala_usaha' => 'Skala Usaha',
            'kota' => 'Kota',
            'tgl_berdiri' => 'Tanggal Berdiri',
            'tenaga_kerja' => 'Tenaga Kerja'
        ];
        
        return $fieldNames[$field] ?? ucfirst($field);
    }

    public function getDistricts()
    {
        $districts = DB::table('faskes')
            ->select('kecamatan')
            ->whereNotNull('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan')
            ->toArray();
            
        return response()->json($districts);
    }

    public function getDistrictDistribution()
    {
        $districts = DB::table('faskes')
            ->select('kecamatan', 
                    DB::raw('SUM(CASE WHEN fasilitas = "Apotek" THEN 1 ELSE 0 END) as apotek_count'),
                    DB::raw('SUM(CASE WHEN fasilitas = "Klinik" THEN 1 ELSE 0 END) as klinik_count'),
                    DB::raw('SUM(CASE WHEN fasilitas = "Puskesmas" THEN 1 ELSE 0 END) as puskesmas_count'),
                    DB::raw('SUM(CASE WHEN fasilitas = "Rumah Sakit" THEN 1 ELSE 0 END) as rumahsakit_count'))
            ->whereNotNull('kecamatan')
            ->groupBy('kecamatan')
            ->get();
            
        return response()->json($districts);
    }

    /**
 * Mendapatkan distribusi faskes per kelurahan berdasarkan kecamatan
 */
public function getVillageDistribution(Request $request)
{
    // Ambil parameter kecamatan dari request
    $kecamatan = $request->input('kecamatan', null);
    
    // Query dasar
    $query = DB::table('faskes')
        ->select('kelurahan', 'fasilitas', DB::raw('COUNT(*) as total'))
        ->whereNotNull('kelurahan')
        ->whereIn('fasilitas', ['Apotek', 'Klinik', 'Puskesmas', 'Rumah Sakit']);
    
    // Filter berdasarkan kecamatan jika ada
    if ($kecamatan && $kecamatan !== 'all') {
        $query->where('kecamatan', $kecamatan);
    }
    
    // Grup berdasarkan kelurahan dan fasilitas
    $data = $query->groupBy('kelurahan', 'fasilitas')->get();
    
    // Format data untuk respons
    $result = [
        'Apotek' => [],
        'Klinik' => [],
        'Puskesmas' => [],
        'Rumah Sakit' => []
    ];
    
    foreach ($data as $item) {
        $result[$item->fasilitas][] = [
            'kelurahan' => $item->kelurahan,
            'total' => $item->total
        ];
    }
    
    return response()->json($result);
}
    
    /**
     * API endpoint untuk mendapatkan statistik (untuk AJAX refresh)
     */
    public function apiStatistics()
    {
        return response()->json($this->getStatistics());
    }
    
    /**
     * API endpoint untuk mendapatkan aktivitas terbaru (untuk AJAX refresh)
     */
    public function apiActivities()
    {
        return response()->json($this->getRecentActivities());
    }
}