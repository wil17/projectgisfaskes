<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faskes;
use App\Models\RumahSakit;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RumahSakitAdminController extends Controller
{
    use LogsActivity;
    
    /**
     * Display a listing of the rumah sakit.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = RumahSakit::select('id_rs', 'nama_rs', 'alamat', 'poliklinik_dokter', 'kecamatan', 'kelurahan');
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_rs', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('poliklinik_dokter', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$search}%");
            });
        }
        
        // Handle filter by kecamatan
        if ($request->has('kecamatan') && !empty($request->kecamatan)) {
            $query->where('kecamatan', $request->kecamatan);
        }
        
        $rumahsakits = $query->paginate(10)->withQueryString();
        
        // Get unique kecamatans for filter dropdown
        $kecamatans = RumahSakit::select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
        
        return view('admin.rumahsakit.index', compact('rumahsakits', 'kecamatans'));
    }

    /**
     * Search rumah sakit via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $rumahsakits = RumahSakit::where('nama_rs', 'LIKE', "%{$query}%")
            ->orWhere('alamat', 'LIKE', "%{$query}%")
            ->orWhere('poliklinik_dokter', 'LIKE', "%{$query}%")
            ->orWhere('kecamatan', 'LIKE', "%{$query}%")
            ->orWhere('kelurahan', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        return response()->json($rumahsakits);
    }

    /**
     * Show the form for creating a new rumah sakit.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.rumahsakit.create', compact('kecamatans', 'kelurahans'));
    }

    /**
     * Store a newly created rumah sakit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_rs' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'poliklinik_dokter' => 'required|string',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Generate a unique ID for both tables
            $id = 'RS' . Str::random(8);
            
            // Create record in faskes table
            $faskes = Faskes::create([
                'id' => $id,
                'nama' => $request->input('nama_rs'),
                'fasilitas' => 'Rumah Sakit',
                'alamat' => $request->input('alamat'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);
            
            // Create record in rumahsakit table
            $rumahsakit = RumahSakit::create([
                'id_rs' => $id,
                'id' => $id, // Foreign key to faskes table
                'nama_rs' => $request->input('nama_rs'),
                'alamat' => $request->input('alamat'),
                'poliklinik_dokter' => $request->input('poliklinik_dokter'),
                'kota' => $request->input('kota'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
            ]);
            
            // Log activity for faskes creation
            $this->logCreate($faskes, "Rumah Sakit baru '{$request->input('nama_rs')}' telah ditambahkan ke sistem");
            
            DB::commit();
            
            return redirect()->route('admin.rumahsakit.index')
                ->with('success', 'Rumah Sakit berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified rumah sakit.
     *
     * @param  string  $id_rs
     * @return \Illuminate\Http\Response
     */
    public function edit($id_rs)
    {
        $rumahsakit = RumahSakit::where('id_rs', $id_rs)->firstOrFail();
        
        // Get related faskes data
        $faskes = Faskes::where('id', $rumahsakit->id)->first();
        
        // If faskes exists, add latitude and longitude to rumah sakit
        if ($faskes) {
            $rumahsakit->latitude = $faskes->latitude;
            $rumahsakit->longitude = $faskes->longitude;
        }
        
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.rumahsakit.edit', compact('rumahsakit', 'kecamatans', 'kelurahans'));
    }

    /**
     * Update the specified rumah sakit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id_rs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_rs)
    {
        $request->validate([
            'nama_rs' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'poliklinik_dokter' => 'required|string',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $rumahsakit = RumahSakit::where('id_rs', $id_rs)->firstOrFail();
            $faskes = Faskes::where('id', $rumahsakit->id)->first();
            
            // Simpan nilai lama untuk logging
            $oldFaskesValues = $faskes ? $faskes->toArray() : [];
            $oldRumahSakitValues = $rumahsakit->toArray();
            
            // Update tabel faskes
            if ($faskes) {
                $faskes->update([
                    'nama' => $request->input('nama_rs'),
                    'alamat' => $request->input('alamat'),
                    'kecamatan' => $request->input('kecamatan'),
                    'kelurahan' => $request->input('kelurahan'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ]);
            }
            
            // Update tabel rumahsakit
            $rumahsakit->update([
                'nama_rs' => $request->input('nama_rs'),
                'alamat' => $request->input('alamat'),
                'poliklinik_dokter' => $request->input('poliklinik_dokter'),
                'kota' => $request->input('kota'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
            ]);
            
            // Log activity untuk update - gabungkan nilai lama dari kedua tabel
            $allOldValues = array_merge($oldFaskesValues, $oldRumahSakitValues);
            
            // Refresh the model to get updated values
            $faskes->refresh();
            $this->logUpdate($faskes, $allOldValues, "Data rumah sakit '{$request->input('nama_rs')}' telah diperbarui");
            
            DB::commit();
            
            return redirect()->route('admin.rumahsakit.index')
                ->with('success', 'Rumah Sakit berhasil diperbarui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified rumah sakit from storage.
     *
     * @param  string  $id_rs
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_rs)
    {
        DB::beginTransaction();
        try {
            $rumahsakit = RumahSakit::where('id_rs', $id_rs)->first();
            
            if (!$rumahsakit) {
                throw new \Exception('Rumah Sakit tidak ditemukan');
            }
            
            $faskes = Faskes::where('id', $rumahsakit->id)->first();
            
            // Log activity sebelum data dihapus
            if ($faskes) {
                $this->logDelete($faskes, "Rumah Sakit '{$rumahsakit->nama_rs}' telah dihapus dari sistem");
            }
            
            // Hapus data
            if ($rumahsakit) {
                $rumahsakit->delete();
            }
            
            // Hapus data faskes jika tidak ada relasi lain yang menggunakan
            if ($faskes) {
                $faskes->delete();
            }
            
            DB::commit();
            
            return redirect()->route('admin.rumahsakit.index')
                ->with('success', 'Rumah Sakit berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export data rumah sakit ke PDF
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportPDF(Request $request)
    {
        try {
            $query = RumahSakit::query();
            
            // Filter berdasarkan pencarian jika ada
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_rs', 'LIKE', "%{$search}%")
                      ->orWhere('alamat', 'LIKE', "%{$search}%")
                      ->orWhere('poliklinik_dokter', 'LIKE', "%{$search}%")
                      ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                      ->orWhere('kelurahan', 'LIKE', "%{$search}%");
                });
            }

            // Filter berdasarkan kecamatan jika ada
            if ($request->has('kecamatan') && !empty($request->kecamatan)) {
                $query->where('kecamatan', $request->kecamatan);
            }

            // Sorting
            $sortField = $request->get('sort', 'nama_rs');
            $sortDirection = $request->get('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            $rumahsakits = $query->get();
            
            // Menentukan nama file berdasarkan filter
            $fileName = 'rumahsakit';
            if ($request->kecamatan) {
                $fileName .= '-' . str_replace(' ', '-', strtolower($request->kecamatan));
            }
            $fileName .= '-' . date('Y-m-d') . '.pdf';

            // Data untuk PDF
            $data = [
                'rumahsakits' => $rumahsakits,
                'kecamatan' => $request->kecamatan ?: 'Semua Kecamatan',
                'search' => $request->search ?: '',
                'total' => $rumahsakits->count(),
                'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
                'generated_by' => auth()->user()->name ?? 'Admin'
            ];

            $pdf = $this->createPdf('admin.exports.rumahsakit-pdf', $data);
            
            // Catat aktivitas ekspor
            $this->logExportActivity("Export PDF data Rumah Sakit untuk " . ($request->kecamatan ?: 'Semua Kecamatan'));
            
            if ($pdf instanceof \Illuminate\Http\Response) {
                return $pdf; // Untuk pendekatan Dompdf langsung
            }
            
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengexport PDF: ' . $e->getMessage());
        }
    }

    /**
     * Helper method untuk create PDF
     */
    private function createPdf($view, $data)
    {
        // Coba beberapa pendekatan
        
        // Pendekatan 1: Laravel DomPDF Facade
        try {
            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::class;
                return $pdf::loadView($view, $data)->setPaper('A4', 'portrait');
            }
        } catch (\Exception $e) {
            // Lanjut ke pendekatan berikutnya
        }

        // Pendekatan 2: App Container
        try {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView($view, $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf;
        } catch (\Exception $e) {
            // Lanjut ke pendekatan berikutnya
        }

        // Pendekatan 3: Direct Dompdf (jika tersedia)
        try {
            if (class_exists('Dompdf\Dompdf')) {
                $dompdf = new \Dompdf\Dompdf();
                $html = view($view, $data)->render();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                return response()->streamDownload(function() use ($dompdf) {
                    echo $dompdf->output();
                }, 'export.pdf', [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        } catch (\Exception $e) {
            // Lanjut ke pendekatan berikutnya
        }

        // Pendekatan 4: Menggunakan resolve
        try {
            $pdf = resolve('dompdf.wrapper');
            $pdf->loadView($view, $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf;
        } catch (\Exception $e) {
            throw new \Exception('Tidak dapat membuat PDF. Pastikan package dompdf sudah terinstall dengan benar.');
        }
    }

    /**
     * Mencatat aktivitas ekspor secara manual
     */
    private function logExportActivity($description = '')
    {
        try {
            // Dapatkan ID rumah sakit pertama sebagai model_id (atau gunakan string jika tidak ada)
            $firstRumahSakit = RumahSakit::first();
            $modelId = $firstRumahSakit ? $firstRumahSakit->id_rs : 'export-pdf';
            
            // Gunakan struktur model ActivityLog yang ada
            $log = new \App\Models\ActivityLog();
            $log->user_id = auth()->id() ?? 'admin';
            $log->user_name = auth()->user()->name ?? 'Admin';
            $log->action = 'export';
            $log->model_type = 'App\Models\RumahSakit';
            $log->model_id = $modelId; // Gunakan ID yang valid
            $log->model_name = 'Ekspor PDF Rumah Sakit';
            $log->facility_type = 'Rumah Sakit';
            $log->description = $description;
            $log->save();
        } catch (\Exception $e) {
            // Jika logging gagal, hanya catat ke log aplikasi tanpa mengganggu proses ekspor
            \Illuminate\Support\Facades\Log::error('Gagal mencatat aktivitas ekspor: ' . $e->getMessage());
        }
    }
}