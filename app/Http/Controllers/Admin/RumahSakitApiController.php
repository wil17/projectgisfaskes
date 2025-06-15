<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faskes;
use Illuminate\Support\Facades\DB;

class RumahSakitApiController extends Controller
{
    /**
     * Mendapatkan daftar kelurahan berdasarkan kecamatan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getKelurahans(Request $request)
    {
        try {
            $kecamatan = $request->input('kecamatan');
            
            if (empty($kecamatan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter kecamatan diperlukan',
                    'kelurahans' => []
                ], 400);
            }
            
            // Ambil kelurahan berdasarkan kecamatan
            $kelurahans = Faskes::where('kecamatan', $kecamatan)
                ->select('kelurahan')
                ->distinct()
                ->orderBy('kelurahan')
                ->pluck('kelurahan')
                ->toArray();
            
            return response()->json([
                'success' => true,
                'message' => 'Kelurahan berhasil dimuat',
                'kelurahans' => $kelurahans
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'kelurahans' => []
            ], 500);
        }
    }
    
    /**
     * Mendapatkan koordinat tengah dari kecamatan/kelurahan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLocationCoordinates(Request $request)
    {
        try {
            $kecamatan = $request->input('kecamatan', '');
            $kelurahan = $request->input('kelurahan', '');
            
            $query = Faskes::select(
                DB::raw('AVG(latitude) as lat'),
                DB::raw('AVG(longitude) as lng')
            );
            
            if (!empty($kecamatan)) {
                $query->where('kecamatan', $kecamatan);
            }
            
            if (!empty($kelurahan)) {
                $query->where('kelurahan', $kelurahan);
            }
            
            $coordinates = $query->first();
            
            if ($coordinates && $coordinates->lat && $coordinates->lng) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koordinat berhasil ditemukan',
                    'coordinates' => [
                        'lat' => (float) $coordinates->lat,
                        'lng' => (float) $coordinates->lng
                    ]
                ]);
            } else {
                // Default koordinat jika tidak ditemukan
                return response()->json([
                    'success' => false,
                    'message' => 'Koordinat tidak ditemukan',
                    'coordinates' => [
                        'lat' => -3.3194374,
                        'lng' => 114.5900474
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'coordinates' => [
                    'lat' => -3.3194374,
                    'lng' => 114.5900474
                ]
            ], 500);
        }
    }
}