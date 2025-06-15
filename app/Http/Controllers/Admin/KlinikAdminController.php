<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faskes;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KlinikAdminController extends Controller
{
    use LogsActivity;
    
    /**
     * Display a listing of the kliniks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Faskes::where('fasilitas', 'Klinik')
                      ->select('id', 'nama', 'skala_usaha', 'alamat', 'kecamatan', 'kelurahan');
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$search}%");
            });
        }
        
        // Handle filter by kecamatan
        if ($request->has('kecamatan') && !empty($request->kecamatan)) {
            $query->where('kecamatan', $request->kecamatan);
        }
        
        $kliniks = $query->paginate(10)->withQueryString();
        
        // Get unique kecamatans for filter dropdown
        $kecamatans = Faskes::where('fasilitas', 'Klinik')
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
        
        return view('admin.klinik.index', compact('kliniks', 'kecamatans'));
    }

    /**
     * Search kliniks via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $kliniks = Faskes::where('fasilitas', 'Klinik')
            ->where(function($q) use ($query) {
                $q->where('nama', 'LIKE', "%{$query}%")
                  ->orWhere('alamat', 'LIKE', "%{$query}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$query}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();
        
        return response()->json($kliniks);
    }

    /**
     * Show the form for creating a new klinik.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.klinik.create', compact('kecamatans', 'kelurahans'));
    }

    /**
     * Store a newly created klinik in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_klinik' => 'required|string|max:255',
            'skala_usaha' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'tgl_berdiri' => 'required|string|max:255',
            'tenaga_kerja' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Generate a unique ID
            $id = 'K' . Str::random(9);
            
            // Create record in faskes table
            $klinik = Faskes::create([
                'id' => $id,
                'nama' => $request->input('nama_klinik'),
                'fasilitas' => 'Klinik',
                'alamat' => $request->input('alamat'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'kota' => $request->input('kota', 'Banjarmasin'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'tgl_berdiri' => $request->input('tgl_berdiri'),
                'skala_usaha' => $request->input('skala_usaha'),
                'tenaga_kerja' => $request->input('tenaga_kerja'),
                'jenis_faskes' => 'klinik',
            ]);
            
            // Log activity for klinik creation
            $this->logCreate($klinik, "Klinik baru '{$request->input('nama_klinik')}' telah ditambahkan ke sistem");
            
            DB::commit();
            
            return redirect()->route('admin.klinik.index')
                ->with('success', 'Klinik berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified klinik.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $klinik = Faskes::where('id', $id)
                       ->where('fasilitas', 'Klinik')
                       ->firstOrFail();
        
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.klinik.edit', compact('klinik', 'kecamatans', 'kelurahans'));
    }

    /**
     * Update the specified klinik in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_klinik' => 'required|string|max:255',
            'skala_usaha' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'tgl_berdiri' => 'required|string|max:255',
            'tenaga_kerja' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $klinik = Faskes::where('id', $id)
                           ->where('fasilitas', 'Klinik')
                           ->firstOrFail();
            
            // Simpan nilai lama untuk logging
            $oldKlinikValues = $klinik->toArray();
            
            // Update tabel faskes
            $klinik->update([
                'nama' => $request->input('nama_klinik'),
                'alamat' => $request->input('alamat'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'kota' => $request->input('kota', 'Banjarmasin'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'tgl_berdiri' => $request->input('tgl_berdiri'),
                'skala_usaha' => $request->input('skala_usaha'),
                'tenaga_kerja' => $request->input('tenaga_kerja'),
            ]);
            
            // Refresh the model to get updated values
            $klinik->refresh();
            $this->logUpdate($klinik, $oldKlinikValues, "Data klinik '{$request->input('nama_klinik')}' telah diperbarui");
            
            DB::commit();
            
            return redirect()->route('admin.klinik.index')
                ->with('success', 'Klinik berhasil diperbarui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified klinik from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $klinik = Faskes::where('id', $id)
                           ->where('fasilitas', 'Klinik')
                           ->firstOrFail();
            
            // Log activity sebelum data dihapus
            $this->logDelete($klinik, "Klinik '{$klinik->nama}' telah dihapus dari sistem");
            
            // Hapus data faskes
            $klinik->delete();
            
            DB::commit();
            
            return redirect()->route('admin.klinik.index')
                ->with('success', 'Klinik berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export data klinik ke PDF
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportPDF(Request $request)
    {
        try {
            $query = Faskes::where('fasilitas', 'Klinik');
            
            // Filter berdasarkan pencarian jika ada
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('alamat', 'LIKE', "%{$search}%")
                      ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                      ->orWhere('kelurahan', 'LIKE', "%{$search}%")
                      ->orWhere('skala_usaha', 'LIKE', "%{$search}%");
                });
            }

            // Filter berdasarkan kecamatan jika ada
            if ($request->has('kecamatan') && !empty($request->kecamatan)) {
                $query->where('kecamatan', $request->kecamatan);
            }

            // Sorting
            $sortField = $request->get('sort', 'nama');
            $sortDirection = $request->get('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            $kliniksList = $query->get();
            
            // Menentukan nama file berdasarkan filter
            $fileName = 'klinik';
            if ($request->kecamatan) {
                $fileName .= '-' . str_replace(' ', '-', strtolower($request->kecamatan));
            }
            $fileName .= '-' . date('Y-m-d') . '.pdf';

            // Data untuk PDF
            $data = [
                'kliniksList' => $kliniksList,
                'kecamatan' => $request->kecamatan ?: 'Semua Kecamatan',
                'search' => $request->search ?: '',
                'total' => $kliniksList->count(),
                'generated_at' => \Carbon\Carbon::now()->format('d/m/Y H:i:s'),
                'generated_by' => auth()->user()->name ?? 'Admin'
            ];

            $pdf = $this->createPdf('admin.exports.klinik-pdf', $data);
            
            // Catat aktivitas ekspor
            $this->logExportActivity("Export PDF data Klinik untuk " . ($request->kecamatan ?: 'Semua Kecamatan'));
            
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
                return $pdf::loadView($view, $data)->setPaper('A4', 'landscape');
            }
        } catch (\Exception $e) {
            // Lanjut ke pendekatan berikutnya
        }

        // Pendekatan 2: App Container
        try {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView($view, $data);
            $pdf->setPaper('A4', 'landscape');
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
                $dompdf->setPaper('A4', 'landscape');
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
            $pdf->setPaper('A4', 'landscape');
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
            // Dapatkan ID klinik pertama sebagai model_id (atau gunakan string jika tidak ada)
            $firstKlinik = Faskes::where('fasilitas', 'Klinik')->first();
            $modelId = $firstKlinik ? $firstKlinik->id : 'export-pdf';
            
            // Gunakan struktur model ActivityLog yang ada
            $log = new \App\Models\ActivityLog();
            $log->user_id = auth()->id() ?? 'admin';
            $log->user_name = auth()->user()->name ?? 'Admin';
            $log->action = 'export';
            $log->model_type = 'App\Models\Faskes';
            $log->model_id = $modelId; // Gunakan ID yang valid
            $log->model_name = 'Ekspor PDF Klinik';
            $log->facility_type = 'Klinik';
            $log->description = $description;
            $log->save();
        } catch (\Exception $e) {
            // Jika logging gagal, hanya catat ke log aplikasi tanpa mengganggu proses ekspor
            \Illuminate\Support\Facades\Log::error('Gagal mencatat aktivitas ekspor: ' . $e->getMessage());
        }
    }
}