<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faskes;
use App\Models\Apoteks;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApotekAdminController extends Controller
{
    use LogsActivity;
    
    /**
     * Display a listing of the apoteks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Apoteks::select('id_apotek', 'nama_apotek', 'skala_usaha', 'alamat', 'kecamatan', 'kelurahan');
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_apotek', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$search}%");
            });
        }
        
        // Handle filter by kecamatan
        if ($request->has('kecamatan') && !empty($request->kecamatan)) {
            $query->where('kecamatan', $request->kecamatan);
        }
        
        $apoteks = $query->paginate(10)->withQueryString();
        
        // Get unique kecamatans for filter dropdown
        $kecamatans = Apoteks::select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
        
        return view('admin.apotek.index', compact('apoteks', 'kecamatans'));
    }

    /**
     * Search apoteks via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $apoteks = Apoteks::where('nama_apotek', 'LIKE', "%{$query}%")
            ->orWhere('alamat', 'LIKE', "%{$query}%")
            ->orWhere('kecamatan', 'LIKE', "%{$query}%")
            ->orWhere('kelurahan', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        return response()->json($apoteks);
    }

    /**
     * Show the form for creating a new apotek.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.apotek.create', compact('kecamatans', 'kelurahans'));
    }

    /**
     * Store a newly created apotek in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_apotek' => 'required|string|max:255',
            'skala_usaha' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'tgl_berdiri' => 'required|string|max:255',
            'tenaga_kerja' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Generate a unique ID for both tables
            $id = 'A' . Str::random(9);
            
            // Create record in faskes table
            $faskes = Faskes::create([
                'id' => $id,
                'nama' => $request->input('nama_apotek'),
                'fasilitas' => 'Apotek',
                'alamat' => $request->input('alamat'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);
            
            // Create record in apoteks table
            $apotek = Apoteks::create([
                'id_apotek' => $id,
                'id' => $id, // Foreign key to faskes table
                'nama_apotek' => $request->input('nama_apotek'),
                'skala_usaha' => $request->input('skala_usaha'),
                'alamat' => $request->input('alamat'),
                'kota' => $request->input('kota'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'tgl_berdiri' => $request->input('tgl_berdiri'),
                'tenaga_kerja' => $request->input('tenaga_kerja'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
            ]);
            
            // Log activity for faskes creation
            $this->logCreate($faskes, "Apotek baru '{$request->input('nama_apotek')}' telah ditambahkan ke sistem");
            
            DB::commit();
            
            return redirect()->route('admin.apotek.index')
                ->with('success', 'Apotek berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified apotek.
     *
     * @param  string  $id_apotek
     * @return \Illuminate\Http\Response
     */
    public function edit($id_apotek)
    {
        $apotek = Apoteks::where('id_apotek', $id_apotek)->firstOrFail();
        
        // Get related faskes data
        $faskes = Faskes::where('id', $apotek->id)->first();
        
        // If faskes exists, add latitude and longitude to apotek
        if ($faskes) {
            $apotek->latitude = $faskes->latitude;
            $apotek->longitude = $faskes->longitude;
        }
        
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.apotek.edit', compact('apotek', 'kecamatans', 'kelurahans'));
    }

    /**
     * Update the specified apotek in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id_apotek
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_apotek)
    {
        $request->validate([
            'nama_apotek' => 'required|string|max:255',
            'skala_usaha' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'tgl_berdiri' => 'required|string|max:255',
            'tenaga_kerja' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $apotek = Apoteks::where('id_apotek', $id_apotek)->firstOrFail();
            $faskes = Faskes::where('id', $apotek->id)->first();
            
            // Simpan nilai lama untuk logging
            $oldFaskesValues = $faskes ? $faskes->toArray() : [];
            $oldApotekValues = $apotek->toArray();
            
            // Update tabel faskes
            if ($faskes) {
                $faskes->update([
                    'nama' => $request->input('nama_apotek'),
                    'alamat' => $request->input('alamat'),
                    'kecamatan' => $request->input('kecamatan'),
                    'kelurahan' => $request->input('kelurahan'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ]);
            }
            
            // Update tabel apotek
            $apotek->update([
                'nama_apotek' => $request->input('nama_apotek'),
                'skala_usaha' => $request->input('skala_usaha'),
                'alamat' => $request->input('alamat'),
                'kota' => $request->input('kota'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'tgl_berdiri' => $request->input('tgl_berdiri'),
                'tenaga_kerja' => $request->input('tenaga_kerja'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
            ]);
            
            // Log activity untuk update - gabungkan nilai lama dari kedua tabel
            $allOldValues = array_merge($oldFaskesValues, $oldApotekValues);
            
            // Refresh the model to get updated values
            $faskes->refresh();
            $this->logUpdate($faskes, $allOldValues, "Data apotek '{$request->input('nama_apotek')}' telah diperbarui");
            
            DB::commit();
            
            return redirect()->route('admin.apotek.index')
                ->with('success', 'Apotek berhasil diperbarui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
 * Remove the specified apotek from storage.
 *
 * @param  string  $id
 * @return \Illuminate\Http\Response
 */
public function destroy($id)
{
    DB::beginTransaction();
    try {
        $apotek = Apoteks::where('id_apotek', $id)->first();
        
        if (!$apotek) {
            throw new \Exception('Apotek tidak ditemukan');
        }
        
        $faskes = Faskes::where('id', $apotek->id)->first();
        
        // Log activity sebelum data dihapus
        if ($faskes) {
            $this->logDelete($faskes, "Apotek '{$apotek->nama_apotek}' telah dihapus dari sistem");
        }
        
        // Hapus data
        if ($apotek) {
            $apotek->delete();
        }
        
        // Hapus data faskes jika tidak ada relasi lain yang menggunakan
        if ($faskes) {
            $faskes->delete();
        }
        
        DB::commit();
        
        return redirect()->route('admin.apotek.index')
            ->with('success', 'Apotek berhasil dihapus!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
/**
 * Export data apotek ke PDF
 * 
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function exportPDF(Request $request)
{
    try {
        $query = Apoteks::query();
        
        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_apotek', 'LIKE', "%{$search}%")
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
        $sortField = $request->get('sort', 'nama_apotek');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $apoteksList = $query->get();
        
        // Menentukan nama file berdasarkan filter
        $fileName = 'apotek';
        if ($request->kecamatan) {
            $fileName .= '-' . str_replace(' ', '-', strtolower($request->kecamatan));
        }
        $fileName .= '-' . date('Y-m-d') . '.pdf';

        // Data untuk PDF
        $data = [
            'apoteksList' => $apoteksList,
            'kecamatan' => $request->kecamatan ?: 'Semua Kecamatan',
            'search' => $request->search ?: '',
            'total' => $apoteksList->count(),
            'generated_at' => \Carbon\Carbon::now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()->name ?? 'Admin'
        ];

        $pdf = $this->createPdf('admin.exports.apotek-pdf', $data);
        
        // Catat aktivitas ekspor
        $this->logExportActivity("Export PDF data Apotek untuk " . ($request->kecamatan ?: 'Semua Kecamatan'));
        
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
        // Dapatkan ID apotek pertama sebagai model_id (atau gunakan string jika tidak ada)
        $firstApotek = Apoteks::first();
        $modelId = $firstApotek ? $firstApotek->id_apotek : 'export-pdf';
        
        // Gunakan struktur model ActivityLog yang ada
        $log = new \App\Models\ActivityLog();
        $log->user_id = auth()->id() ?? 'admin';
        $log->user_name = auth()->user()->name ?? 'Admin';
        $log->action = 'export';
        $log->model_type = 'App\Models\Apoteks';
        $log->model_id = $modelId; // Gunakan ID yang valid
        $log->model_name = 'Ekspor PDF Apotek';
        $log->facility_type = 'Apotek';
        $log->description = $description;
        $log->save();
    } catch (\Exception $e) {
        // Jika logging gagal, hanya catat ke log aplikasi tanpa mengganggu proses ekspor
        \Illuminate\Support\Facades\Log::error('Gagal mencatat aktivitas ekspor: ' . $e->getMessage());
    }
}
}