<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
    /**
     * Display the map with health facilities
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        $kecamatan = Cache::remember('kecamatan_list', 3600, function () {
            return DB::table('faskes')
                ->select('kecamatan')
                ->distinct()
                ->whereNotNull('kecamatan')
                ->where('kecamatan', '!=', '')
                ->orderBy('kecamatan')
                ->get();
        });
            
        $kelurahan = Cache::remember('kelurahan_list', 3600, function () {
            return DB::table('faskes')
                ->select('kelurahan', 'kecamatan')
                ->whereNotNull('kelurahan')
                ->whereNotNull('kecamatan')
                ->where('kelurahan', '!=', '')
                ->where('kecamatan', '!=', '')
                ->distinct()
                ->orderBy('kelurahan')
                ->get();
        });
            
        return view('map.index', compact('kecamatan', 'kelurahan'));
    }
    
    /**
     * API endpoint to fetch health facilities data for the map
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFaskes(Request $request)
    {
        try {
            // Log semua input request untuk debugging - FIX: always use array for second parameter
            \Log::info('=== API FASKES REQUEST ===', []);
            \Log::info('All Request Data:', $request->all());
            \Log::info('Request Query String:', [$request->getQueryString()]);
            
            // Mulai membangun query
            $query = DB::table('faskes')
                ->select('id', 'nama', 'fasilitas', 'alamat', 'kecamatan', 'kelurahan', 'longitude', 'latitude');
            
            // Filter berdasarkan kecamatan (multi)
            if ($request->has('kecamatan')) {
                $kecamatanParam = $request->input('kecamatan');
                
                // Handle array format kecamatan[]
                if (is_array($kecamatanParam) && count($kecamatanParam) > 0) {
                    // Filter out empty values
                    $kecamatanFiltered = array_filter($kecamatanParam, function($value) {
                        return !empty(trim($value));
                    });
                    
                    if (!empty($kecamatanFiltered)) {
                        $query->whereIn('kecamatan', $kecamatanFiltered);
                        \Log::info('Applied whereIn kecamatan filter:', $kecamatanFiltered);
                    }
                } elseif (!is_array($kecamatanParam) && !empty(trim($kecamatanParam))) {
                    // Handle single value
                    $query->where('kecamatan', trim($kecamatanParam));
                    \Log::info('Applied where kecamatan filter:', [$kecamatanParam]);
                }
            }
            
            // Filter berdasarkan kelurahan (multi)
            if ($request->has('kelurahan')) {
                $kelurahanParam = $request->input('kelurahan');
                
                // Handle array format kelurahan[]
                if (is_array($kelurahanParam) && count($kelurahanParam) > 0) {
                    // Filter out empty values
                    $kelurahanFiltered = array_filter($kelurahanParam, function($value) {
                        return !empty(trim($value));
                    });
                    
                    if (!empty($kelurahanFiltered)) {
                        $query->whereIn('kelurahan', $kelurahanFiltered);
                        \Log::info('Applied whereIn kelurahan filter:', $kelurahanFiltered);
                    }
                } elseif (!is_array($kelurahanParam) && !empty(trim($kelurahanParam))) {
                    // Handle single value
                    $query->where('kelurahan', trim($kelurahanParam));
                    \Log::info('Applied where kelurahan filter:', [$kelurahanParam]);
                }
            }
            
            // Filter berdasarkan jenis fasilitas
            if ($request->has('fasilitas')) {
                $fasilitasParam = $request->input('fasilitas');
                
                if (is_array($fasilitasParam) && count($fasilitasParam) > 0) {
                    // Filter out empty values
                    $fasilitasFiltered = array_filter($fasilitasParam, function($value) {
                        return !empty(trim($value));
                    });
                    
                    if (!empty($fasilitasFiltered)) {
                        $query->whereIn('fasilitas', $fasilitasFiltered);
                        \Log::info('Applied whereIn fasilitas filter:', $fasilitasFiltered);
                    }
                } elseif (!is_array($fasilitasParam) && !empty(trim($fasilitasParam))) {
                    // Handle single value
                    $query->where('fasilitas', trim($fasilitasParam));
                    \Log::info('Applied where fasilitas filter:', [$fasilitasParam]);
                }
            }
            
            // Pencarian berdasarkan nama atau alamat
            if ($request->has('search') && !empty(trim($request->input('search')))) {
                $searchTerm = '%' . trim($request->input('search')) . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nama', 'like', $searchTerm)
                      ->orWhere('alamat', 'like', $searchTerm);
                });
                \Log::info('Applied search filter:', [$searchTerm]);
            }
            
            // Pastikan hanya mendapatkan data dengan koordinat yang valid
            $query->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  ->where('latitude', '!=', '')
                  ->where('longitude', '!=', '')
                  ->where('latitude', '!=', 0)
                  ->where('longitude', '!=', 0);
            
            // Log SQL query untuk debugging
            \Log::info('Final SQL Query:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            // Execute query
            $faskes = $query->get();
            
            // Log hasil
            \Log::info('Query Results Count:', ['count' => $faskes->count()]);
            \Log::info('=== END API FASKES REQUEST ===', []);
            
            // Return response
            return response()->json($faskes);
            
        } catch (\Exception $e) {
            \Log::error('Error in getFaskes API:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Internal server error', 
                'message' => 'Terjadi kesalahan saat mengambil data fasilitas kesehatan'
            ], 500);
        }
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNearbyFaskes(Request $request)
{
    $lat = $request->input('lat');
    $lng = $request->input('lng');
    $radius = $request->input('radius', 3); // default 3km
    
    // Validasi parameter yang diperlukan
    if (!$lat || !$lng) {
        return response()->json(['error' => 'Latitude and longitude are required'], 400);
    }
    
    // Validasi rentang latitude dan longitude
    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
        return response()->json(['error' => 'Invalid latitude or longitude values'], 400);
    }
    
    try {
        \Log::info('Nearby Faskes Request:', [
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius
        ]);
        
        // Hitung fasilitas terdekat menggunakan rumus Haversine
        // Menggunakan raw SQL untuk performa lebih baik dengan perhitungan jarak
        $nearbyFaskes = DB::table('faskes')
            ->select(
                'id', 'nama', 'fasilitas', 'alamat', 'kecamatan', 
                'kelurahan', 'longitude', 'latitude'
            )
            ->selectRaw(
                "ROUND((6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(latitude)))), 2) AS distance",
                [$lat, $lng, $lat]
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance')
            ->limit(50) // Batasi 50 fasilitas terdekat untuk performa
            ->get();
        
        \Log::info('Nearby Faskes Results:', ['count' => $nearbyFaskes->count()]);
        
        return response()->json($nearbyFaskes);
        
    } catch (\Exception $e) {
        \Log::error('Error in getNearbyFaskes:', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'Internal server error'], 500);
    }
}
    
    /**
     * API endpoint to get statistics about health facilities
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        try {
            // Cache statistics for better performance
            $stats = Cache::remember('faskes_stats', 1800, function () {
                // Get counts by facility type
                $facilityStats = DB::table('faskes')
                    ->select('fasilitas', DB::raw('count(*) as total'))
                    ->whereNotNull('fasilitas')
                    ->where('fasilitas', '!=', '')
                    ->groupBy('fasilitas')
                    ->orderBy('fasilitas')
                    ->get();
                    
                // Get counts by kecamatan
                $kecamatanStats = DB::table('faskes')
                    ->select('kecamatan', DB::raw('count(*) as total'))
                    ->whereNotNull('kecamatan')
                    ->where('kecamatan', '!=', '')
                    ->groupBy('kecamatan')
                    ->orderBy('kecamatan')
                    ->get();
                
                // Get total count
                $totalFaskes = DB::table('faskes')->count();
                
                // Get facilities with coordinates count
                $faskesWithCoordinates = DB::table('faskes')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->where('latitude', '!=', '')
                    ->where('longitude', '!=', '')
                    ->where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->count();
                
                return [
                    'facilities' => $facilityStats,
                    'kecamatan' => $kecamatanStats,
                    'total_faskes' => $totalFaskes,
                    'faskes_with_coordinates' => $faskesWithCoordinates,
                    'coverage_percentage' => $totalFaskes > 0 ? round(($faskesWithCoordinates / $totalFaskes) * 100, 2) : 0
                ];
            });
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            \Log::error('Error in getStats:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Get all distinct kecamatan names
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKecamatan()
    {
        try {
            $kecamatan = Cache::remember('all_kecamatan', 3600, function () {
                return DB::table('faskes')
                    ->select('kecamatan')
                    ->distinct()
                    ->whereNotNull('kecamatan')
                    ->where('kecamatan', '!=', '')
                    ->orderBy('kecamatan')
                    ->pluck('kecamatan');
            });
            
            return response()->json($kecamatan);
            
        } catch (\Exception $e) {
            \Log::error('Error in getKecamatan:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Get kelurahan by kecamatan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKelurahanByKecamatan(Request $request)
    {
        $kecamatan = $request->input('kecamatan');
        
        if (!$kecamatan) {
            return response()->json(['error' => 'Kecamatan parameter is required'], 400);
        }
        
        try {
            $kelurahan = Cache::remember("kelurahan_{$kecamatan}", 3600, function () use ($kecamatan) {
                return DB::table('faskes')
                    ->select('kelurahan')
                    ->distinct()
                    ->where('kecamatan', $kecamatan)
                    ->whereNotNull('kelurahan')
                    ->where('kelurahan', '!=', '')
                    ->orderBy('kelurahan')
                    ->pluck('kelurahan');
            });
            
            return response()->json($kelurahan);
            
        } catch (\Exception $e) {
            \Log::error('Error in getKelurahanByKecamatan:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}