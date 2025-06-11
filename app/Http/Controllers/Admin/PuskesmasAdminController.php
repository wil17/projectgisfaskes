<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faskes;
use App\Models\Puskesmas;
use App\Models\KlasterPuskesmas;
use App\Models\LayananKlaster;
use App\Models\WilayahKerjaPuskesmas;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PuskesmasAdminController extends Controller
{
    use LogsActivity;
    
    /**
     * Display a listing of the puskesmas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Puskesmas::select('id_puskesmas', 'nama_puskesmas', 'alamat', 'kepala_puskesmas', 'kecamatan', 'kelurahan');
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_puskesmas', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$search}%");
            });
        }
        
        // Handle filter by kecamatan
        if ($request->has('kecamatan') && !empty($request->kecamatan)) {
            $query->where('kecamatan', $request->kecamatan);
        }
        
        $puskesmas = $query->paginate(10)->withQueryString();
        
        // Get unique kecamatans for filter dropdown
        $kecamatans = Puskesmas::select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
        
        return view('admin.puskesmas.index', compact('puskesmas', 'kecamatans'));
    }

    /**
     * Search puskesmas via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $puskesmas = Puskesmas::where('nama_puskesmas', 'LIKE', "%{$query}%")
            ->orWhere('alamat', 'LIKE', "%{$query}%")
            ->orWhere('kecamatan', 'LIKE', "%{$query}%")
            ->orWhere('kelurahan', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        return response()->json($puskesmas);
    }

    /**
     * Show the form for creating a new puskesmas.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.puskesmas.create', compact('kecamatans', 'kelurahans'));
    }

    /**
     * Store a newly created puskesmas in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_puskesmas' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kepala_puskesmas' => 'required|string|max:255',
            'jam_operasional' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'wilayah_kerja' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Generate a unique ID for both tables
            $id = 'PKM' . Str::random(8);
            
            // Create record in faskes table
            $faskes = Faskes::create([
                'id' => $id,
                'nama' => $request->input('nama_puskesmas'),
                'fasilitas' => 'Puskesmas',
                'alamat' => $request->input('alamat'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);
            
            // Create record in puskesmas table
            $puskesmas = Puskesmas::create([
                'id_puskesmas' => $id,
                'id' => $id, // Foreign key to faskes table
                'nama_puskesmas' => $request->input('nama_puskesmas'),
                'alamat' => $request->input('alamat'),
                'kepala_puskesmas' => $request->input('kepala_puskesmas'),
                'jam_operasional' => $request->input('jam_operasional'),
                'kota' => $request->input('kota'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
            ]);
            
            // Store wilayah kerja if provided
            if ($request->has('wilayah_kerja') && is_array($request->wilayah_kerja)) {
                foreach ($request->wilayah_kerja as $kelurahan) {
                    WilayahKerjaPuskesmas::create([
                        'id_puskesmas' => $id,
                        'kelurahan' => $kelurahan
                    ]);
                }
            }
            
            // Log activity for faskes creation
            $this->logCreate($faskes, "Puskesmas baru '{$request->input('nama_puskesmas')}' telah ditambahkan ke sistem");
            
            DB::commit();
            
            return redirect()->route('admin.puskesmas.index')
                ->with('success', 'Puskesmas berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified puskesmas.
     *
     * @param  string  $id_puskesmas
     * @return \Illuminate\Http\Response
     */
    public function edit($id_puskesmas)
    {
        $puskesmas = Puskesmas::where('id_puskesmas', $id_puskesmas)->firstOrFail();
        
        // Get related faskes data
        $faskes = Faskes::where('id', $puskesmas->id)->first();
        
        // If faskes exists, add latitude and longitude to puskesmas
        if ($faskes) {
            $puskesmas->latitude = $faskes->latitude;
            $puskesmas->longitude = $faskes->longitude;
        }
        
        // Get wilayah kerja
        $wilayahKerja = WilayahKerjaPuskesmas::where('id_puskesmas', $id_puskesmas)
                        ->pluck('kelurahan')
                        ->toArray();
        
        // Get klaster puskesmas with penanggung_jawab
        $klaster = KlasterPuskesmas::where('id_puskesmas', $id_puskesmas)
                    ->orderBy('kode_klaster')
                    ->get();
        
        $kecamatans = Faskes::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $kelurahans = Faskes::select('kelurahan')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        
        return view('admin.puskesmas.edit', compact(
            'puskesmas', 
            'wilayahKerja', 
            'klaster', 
            'kecamatans', 
            'kelurahans'
        ));
    }

    /**
     * Update the specified puskesmas in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id_puskesmas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_puskesmas)
    {
        $request->validate([
            'nama_puskesmas' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kepala_puskesmas' => 'required|string|max:255',
            'jam_operasional' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'wilayah_kerja' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $puskesmas = Puskesmas::where('id_puskesmas', $id_puskesmas)->firstOrFail();
            $faskes = Faskes::where('id', $puskesmas->id)->first();
            
            // Simpan nilai lama untuk logging
            $oldFaskesValues = $faskes ? $faskes->toArray() : [];
            $oldPuskesmasValues = $puskesmas->toArray();
            
            // Update tabel faskes
            if ($faskes) {
                $faskes->update([
                    'nama' => $request->input('nama_puskesmas'),
                    'alamat' => $request->input('alamat'),
                    'kecamatan' => $request->input('kecamatan'),
                    'kelurahan' => $request->input('kelurahan'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ]);
            }
            
            // Update tabel puskesmas
            $puskesmas->update([
                'nama_puskesmas' => $request->input('nama_puskesmas'),
                'alamat' => $request->input('alamat'),
                'kepala_puskesmas' => $request->input('kepala_puskesmas'),
                'jam_operasional' => $request->input('jam_operasional'),
                'kota' => $request->input('kota'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
            ]);
            
            // Update wilayah kerja (delete existing and create new)
            WilayahKerjaPuskesmas::where('id_puskesmas', $id_puskesmas)->delete();
            
            if ($request->has('wilayah_kerja') && is_array($request->wilayah_kerja)) {
                foreach ($request->wilayah_kerja as $kelurahan) {
                    WilayahKerjaPuskesmas::create([
                        'id_puskesmas' => $id_puskesmas,
                        'kelurahan' => $kelurahan
                    ]);
                }
            }
            
            // Log activity untuk update - gabungkan nilai lama dari kedua tabel
            $allOldValues = array_merge($oldFaskesValues, $oldPuskesmasValues);
            
            // Refresh the model to get updated values
            $faskes->refresh();
            $this->logUpdate($faskes, $allOldValues, "Data puskesmas '{$request->input('nama_puskesmas')}' telah diperbarui");
            
            DB::commit();
            
            return redirect()->route('admin.puskesmas.index')
                ->with('success', 'Puskesmas berhasil diperbarui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified puskesmas from storage.
     *
     * @param  string  $id_puskesmas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_puskesmas)
    {
        DB::beginTransaction();
        try {
            $puskesmas = Puskesmas::where('id_puskesmas', $id_puskesmas)->first();
            
            if (!$puskesmas) {
                throw new \Exception('Puskesmas tidak ditemukan');
            }
            
            $faskes = Faskes::where('id', $puskesmas->id)->first();
            
            // Delete related records
            WilayahKerjaPuskesmas::where('id_puskesmas', $id_puskesmas)->delete();
            
            // Get klaster IDs
            $klasterIds = KlasterPuskesmas::where('id_puskesmas', $id_puskesmas)
                         ->pluck('id_klaster')
                         ->toArray();
            
            // Delete layanan records related to klaster
            if (!empty($klasterIds)) {
                LayananKlaster::whereIn('id_klaster', $klasterIds)->delete();
            }
            
            // Delete klaster records
            KlasterPuskesmas::where('id_puskesmas', $id_puskesmas)->delete();
            
            // Log activity sebelum data dihapus
            if ($faskes) {
                $this->logDelete($faskes, "Puskesmas '{$puskesmas->nama_puskesmas}' telah dihapus dari sistem");
            }
            
            // Hapus data
            if ($puskesmas) {
                $puskesmas->delete();
            }
            
            // Hapus data faskes jika tidak ada relasi lain yang menggunakan
            if ($faskes) {
                $faskes->delete();
            }
            
            DB::commit();
            
            return redirect()->route('admin.puskesmas.index')
                ->with('success', 'Puskesmas berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Display klaster for specific puskesmas
     */
    public function klasterIndex($id_puskesmas)
    {
        $puskesmas = Puskesmas::where('id_puskesmas', $id_puskesmas)->firstOrFail();
        
        $klaster = KlasterPuskesmas::where('id_puskesmas', $id_puskesmas)
                    ->orderBy('kode_klaster')
                    ->get();
        
        return view('admin.puskesmas.klaster.index', compact('puskesmas', 'klaster'));
    }

    /**
     * Store a new klaster
     */
    public function klasterStore(Request $request, $id_puskesmas)
    {
        $request->validate([
            'nama_klaster' => 'required|string|max:100',
            'kode_klaster' => 'required|integer|between:1,5',
            'penanggung_jawab' => 'required|string|max:255',
        ]);
        
        try {
            $puskesmas = Puskesmas::where('id_puskesmas', $id_puskesmas)->first();
            
            if (!$puskesmas) {
                throw new \Exception('Puskesmas tidak ditemukan');
            }
            
            KlasterPuskesmas::create([
                'id_puskesmas' => $id_puskesmas,
                'nama_klaster' => $request->nama_klaster,
                'kode_klaster' => $request->kode_klaster,
                'penanggung_jawab' => $request->penanggung_jawab,
                'nama_puskesmas' => $puskesmas->nama_puskesmas,
            ]);
            
            return redirect()->route('admin.puskesmas.klaster.index', $id_puskesmas)
                ->with('success', 'Klaster berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified klaster
     */
    public function klasterUpdate(Request $request, $id_klaster)
    {
        $request->validate([
            'nama_klaster' => 'required|string|max:100',
            'kode_klaster' => 'required|integer|between:1,5',
            'penanggung_jawab' => 'required|string|max:255',
        ]);
        
        try {
            $klaster = KlasterPuskesmas::findOrFail($id_klaster);
            
            $klaster->update([
                'nama_klaster' => $request->nama_klaster,
                'kode_klaster' => $request->kode_klaster,
                'penanggung_jawab' => $request->penanggung_jawab,
            ]);
            
            return redirect()->route('admin.puskesmas.klaster.index', $klaster->id_puskesmas)
                ->with('success', 'Klaster berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified klaster
     */
    public function klasterDestroy($id_klaster)
    {
        try {
            $klaster = KlasterPuskesmas::findOrFail($id_klaster);
            $id_puskesmas = $klaster->id_puskesmas;
            
            // Delete related layanan
            LayananKlaster::where('id_klaster', $id_klaster)->delete();
            
            // Delete klaster
            $klaster->delete();
            
            return redirect()->route('admin.puskesmas.klaster.index', $id_puskesmas)
                ->with('success', 'Klaster berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display layanan for specific klaster
     */
    public function layananIndex($id_klaster)
    {
        $klaster = KlasterPuskesmas::findOrFail($id_klaster);
        $puskesmas = Puskesmas::where('id_puskesmas', $klaster->id_puskesmas)->first();
        
        $layanan = LayananKlaster::where('id_klaster', $id_klaster)
                    ->where('id_puskesmas', $klaster->id_puskesmas)
                    ->get();
        
        return view('admin.puskesmas.layanan.index', compact('klaster', 'puskesmas', 'layanan'));
    }

    /**
     * Store a new layanan
     */
    public function layananStore(Request $request, $id_klaster)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi_layanan' => 'required|string',
            'jumlah_petugas' => 'required|integer|min:1',
        ]);
        
        try {
            $klaster = KlasterPuskesmas::findOrFail($id_klaster);
            
            LayananKlaster::create([
                'id_klaster' => $id_klaster,
                'id_puskesmas' => $klaster->id_puskesmas,
                'nama_layanan' => $request->nama_layanan,
                'deskripsi_layanan' => $request->deskripsi_layanan,
                'jumlah_petugas' => $request->jumlah_petugas,
            ]);
            
            return redirect()->route('admin.puskesmas.layanan.index', $id_klaster)
                ->with('success', 'Layanan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified layanan
     */
    public function layananUpdate(Request $request, $id_layanan)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi_layanan' => 'required|string',
            'jumlah_petugas' => 'required|integer|min:1',
        ]);
        
        try {
            $layanan = LayananKlaster::findOrFail($id_layanan);
            
            $layanan->update([
                'nama_layanan' => $request->nama_layanan,
                'deskripsi_layanan' => $request->deskripsi_layanan,
                'jumlah_petugas' => $request->jumlah_petugas,
            ]);
            
            return redirect()->route('admin.puskesmas.layanan.index', $layanan->id_klaster)
                ->with('success', 'Layanan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified layanan
     */
    public function layananDestroy($id_layanan)
    {
        try {
            $layanan = LayananKlaster::findOrFail($id_layanan);
            $id_klaster = $layanan->id_klaster;
            
            $layanan->delete();
            
            return redirect()->route('admin.puskesmas.layanan.index', $id_klaster)
                ->with('success', 'Layanan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
 * Export data puskesmas ke PDF
 * 
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function exportPDF(Request $request)
{
    try {
        $query = Puskesmas::query();
        
        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_puskesmas', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('kepala_puskesmas', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan kecamatan jika ada
        if ($request->has('kecamatan') && !empty($request->kecamatan)) {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Sorting
        $sortField = $request->get('sort', 'nama_puskesmas');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $puskesmasList = $query->get();
        
        // Menentukan nama file berdasarkan filter
        $fileName = 'puskesmas';
        if ($request->kecamatan) {
            $fileName .= '-' . str_replace(' ', '-', strtolower($request->kecamatan));
        }
        $fileName .= '-' . date('Y-m-d') . '.pdf';

        // Data untuk PDF
        $data = [
            'puskesmasList' => $puskesmasList,
            'kecamatan' => $request->kecamatan ?: 'Semua Kecamatan',
            'search' => $request->search ?: '',
            'total' => $puskesmasList->count(),
            'generated_at' => \Carbon\Carbon::now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()->name ?? 'Admin'
        ];

        $pdf = $this->createPdf('admin.exports.puskesmas-pdf', $data);
        
        // Catat aktivitas ekspor
        $this->logExportActivity("Export PDF data Puskesmas untuk " . ($request->kecamatan ?: 'Semua Kecamatan'));
        
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
        // Dapatkan ID puskesmas pertama sebagai model_id (atau gunakan string jika tidak ada)
        $firstPuskesmas = Puskesmas::first();
        $modelId = $firstPuskesmas ? $firstPuskesmas->id_puskesmas : 'export-pdf';
        
        // Gunakan struktur model ActivityLog yang ada
        $log = new \App\Models\ActivityLog();
        $log->user_id = auth()->id() ?? 'admin';
        $log->user_name = auth()->user()->name ?? 'Admin';
        $log->action = 'export';
        $log->model_type = 'App\Models\Puskesmas';
        $log->model_id = $modelId; // Gunakan ID yang valid
        $log->model_name = 'Ekspor PDF Puskesmas';
        $log->facility_type = 'Puskesmas';
        $log->description = $description;
        $log->save();
    } catch (\Exception $e) {
        // Jika logging gagal, hanya catat ke log aplikasi tanpa mengganggu proses ekspor
        \Illuminate\Support\Facades\Log::error('Gagal mencatat aktivitas ekspor: ' . $e->getMessage());
    }
}
}