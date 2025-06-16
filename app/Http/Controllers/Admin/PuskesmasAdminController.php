<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faskes;
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
        $query = Faskes::where('fasilitas', 'Puskesmas')
                       ->select('id', 'nama', 'alamat', 'kepala_puskesmas', 'kecamatan', 'kelurahan');
        
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
        
        $puskesmas = $query->paginate(10)->withQueryString();
        
        // Get unique kecamatans for filter dropdown
        $kecamatans = Faskes::where('fasilitas', 'Puskesmas')
            ->select('kecamatan')
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
        
        $puskesmas = Faskes::where('fasilitas', 'Puskesmas')
            ->where(function($q) use ($query) {
                $q->where('nama', 'LIKE', "%{$query}%")
                  ->orWhere('alamat', 'LIKE', "%{$query}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$query}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$query}%");
            })
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
            // Generate a unique ID with format PKM + random string
            $id = 'PKM' . Str::random(7);
            
            // Create record in faskes table
            $puskesmas = Faskes::create([
                'id' => $id,
                'nama' => $request->input('nama_puskesmas'),
                'fasilitas' => 'Puskesmas',
                'alamat' => $request->input('alamat'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'kota' => $request->input('kota'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'jam_operasional' => $request->input('jam_operasional'),
                'kepala_puskesmas' => $request->input('kepala_puskesmas'),
            ]);
            
            // Store wilayah kerja if provided
            if ($request->has('wilayah_kerja') && is_array($request->wilayah_kerja) && count($request->wilayah_kerja) > 0) {
                foreach ($request->wilayah_kerja as $kelurahan) {
                    if (!empty($kelurahan)) {
                        // Create entry with unique id_wilayah
                        WilayahKerjaPuskesmas::create([
                            'id_wilayah' => Str::random(10),
                            'id' => $id,
                            'kelurahan' => $kelurahan
                        ]);
                    }
                }
            }
            
            // Log activity for faskes creation
            $this->logCreate($puskesmas, "Puskesmas baru '{$request->input('nama_puskesmas')}' telah ditambahkan ke sistem");
            
            DB::commit();
            
            return redirect()->route('admin.puskesmas.index')
                ->with('success', 'Puskesmas berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat menyimpan puskesmas: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified puskesmas.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $puskesmas = Faskes::where('id', $id)
                          ->where('fasilitas', 'Puskesmas')
                          ->firstOrFail();
        
        // Get wilayah kerja
        $wilayahKerjaResults = WilayahKerjaPuskesmas::where('id', $id)->get();
        
        $wilayahKerja = collect($wilayahKerjaResults)->pluck('kelurahan')->toArray();
        
        // Get klaster puskesmas with penanggung_jawab
        $klaster = KlasterPuskesmas::where('id_puskesmas', $id)
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
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
            $puskesmas = Faskes::where('id', $id)
                             ->where('fasilitas', 'Puskesmas')
                             ->firstOrFail();
            
            // Simpan nilai lama untuk logging
            $oldValues = $puskesmas->toArray();
            
            // Update tabel faskes
            $puskesmas->update([
                'nama' => $request->input('nama_puskesmas'),
                'alamat' => $request->input('alamat'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'kota' => $request->input('kota'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'jam_operasional' => $request->input('jam_operasional'),
                'kepala_puskesmas' => $request->input('kepala_puskesmas'),
            ]);
            
            // Update wilayah kerja (delete existing and create new)
            WilayahKerjaPuskesmas::where('id', $id)->delete();
            
            if ($request->has('wilayah_kerja') && is_array($request->wilayah_kerja)) {
                foreach ($request->wilayah_kerja as $kelurahan) {
                    if (!empty($kelurahan)) {
                        WilayahKerjaPuskesmas::create([
                            'id_wilayah' => Str::random(10),
                            'id' => $id,
                            'kelurahan' => $kelurahan
                        ]);
                    }
                }
            }
            
            // Log activity untuk update
            $puskesmas->refresh();
            $this->logUpdate($puskesmas, $oldValues, "Data puskesmas '{$request->input('nama_puskesmas')}' telah diperbarui");
            
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
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $puskesmas = Faskes::where('id', $id)
                              ->where('fasilitas', 'Puskesmas')
                              ->first();
            
            if (!$puskesmas) {
                throw new \Exception('Puskesmas tidak ditemukan');
            }
            
            // Delete related records
            WilayahKerjaPuskesmas::where('id', $id)->delete();
            
            // Get klaster IDs
            $klasterIds = KlasterPuskesmas::where('id_puskesmas', $id)
                         ->pluck('id_klaster')
                         ->toArray();
            
            // Delete layanan records related to klaster
            if (!empty($klasterIds)) {
                LayananKlaster::whereIn('id_klaster', $klasterIds)->delete();
            }
            
            // Delete klaster records
            KlasterPuskesmas::where('id_puskesmas', $id)->delete();
            
            // Log activity sebelum data dihapus
            $this->logDelete($puskesmas, "Puskesmas '{$puskesmas->nama}' telah dihapus dari sistem");
            
            // Hapus data faskes
            $puskesmas->delete();
            
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
        $puskesmas = Faskes::where('id', $id_puskesmas)
                          ->where('fasilitas', 'Puskesmas')
                          ->firstOrFail();
        
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
            $puskesmas = Faskes::where('id', $id_puskesmas)
                             ->where('fasilitas', 'Puskesmas')
                             ->first();
            
            if (!$puskesmas) {
                throw new \Exception('Puskesmas tidak ditemukan');
            }
            
            KlasterPuskesmas::create([
                'id_puskesmas' => $id_puskesmas,
                'nama_klaster' => $request->nama_klaster,
                'kode_klaster' => $request->kode_klaster,
                'penanggung_jawab' => $request->penanggung_jawab,
                'nama_puskesmas' => $puskesmas->nama,
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
        $puskesmas = Faskes::where('id', $klaster->id_puskesmas)
                           ->where('fasilitas', 'Puskesmas')
                           ->first();
        
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
            'nama_layanan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jadwal_layanan' => 'nullable|string|max:255',
        ]);
        
        try {
            $klaster = KlasterPuskesmas::findOrFail($id_klaster);
            
            LayananKlaster::create([
                'id_klaster' => $id_klaster,
                'id_puskesmas' => $klaster->id_puskesmas,
                'nama_layanan' => $request->nama_layanan,
                'deskripsi' => $request->deskripsi,
                'jadwal_layanan' => $request->jadwal_layanan,
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
            'nama_layanan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jadwal_layanan' => 'nullable|string|max:255',
        ]);
        
        try {
            $layanan = LayananKlaster::findOrFail($id_layanan);
            
            $layanan->update([
                'nama_layanan' => $request->nama_layanan,
                'deskripsi' => $request->deskripsi,
                'jadwal_layanan' => $request->jadwal_layanan,
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
     */
    public function exportPDF(Request $request)
    {
        try {
            $query = Faskes::where('fasilitas', 'Puskesmas');
            
            // Filter berdasarkan pencarian jika ada
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
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
            $sortField = $request->get('sort', 'nama');
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
            $firstPuskesmas = Faskes::where('fasilitas', 'Puskesmas')->first();
            $modelId = $firstPuskesmas ? $firstPuskesmas->id : 'export-pdf';
            
            // Gunakan struktur model ActivityLog yang ada
            $log = new \App\Models\ActivityLog();
            $log->user_id = auth()->id() ?? 'admin';
            $log->user_name = auth()->user()->name ?? 'Admin';
            $log->action = 'export';
            $log->model_type = 'App\Models\Faskes';
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