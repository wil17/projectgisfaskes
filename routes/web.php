<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\KlinikController;
use App\Http\Controllers\ApotekController;
use App\Http\Controllers\DataLayananController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaskesController;
use App\Http\Controllers\Admin\ApotekAdminController;
use App\Http\Controllers\Admin\KlinikAdminController;
use App\Http\Controllers\Admin\RumahSakitAdminController;
use App\Http\Controllers\Admin\PuskesmasAdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\StatistikController;

// Routes untuk Landing Page
Route::get('/', [MapController::class, 'index'])->name('map');
Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');

// API Routes untuk statistik
Route::get('/api/faskes-statistics', [StatistikController::class, 'getFaskesStatistics'])->name('api.faskes.statistics');
Route::get('/api/kelurahan-statistics', [StatistikController::class, 'getKelurahanStatistics'])->name('api.kelurahan.statistics');
Route::get('/api/kecamatan-totals', [StatistikController::class, 'getKecamatanTotals'])->name('api.kecamatan.totals');
// Route untuk halaman map
Route::get('/api/faskes', [MapController::class, 'getFaskes'])->name('api.faskes');
Route::get('/api/faskes/nearby', [MapController::class, 'getNearbyFaskes'])->name('api.faskes.nearby');
Route::get('/api/stats', [MapController::class, 'getStats'])->name('api.stats');

// Data Layanan Routes - Tanpa rute export PDF
Route::get('/data-layanan', [DataLayananController::class, 'index'])->name('data.layanan');

// Apotek Routes
Route::get('/data-layanan/apotek', [DataLayananController::class, 'apotek'])->name('data.layanan.apotek');
Route::get('/data-layanan/apotek/{id}', [DataLayananController::class, 'detailApotek'])->name('data.layanan.apotek.detail');

// Klinik Routes
Route::get('/data-layanan/klinik', [DataLayananController::class, 'klinik'])->name('data.layanan.klinik');
Route::get('/data-layanan/klinik/{id}', [DataLayananController::class, 'detailKlinik'])->name('data.layanan.klinik.detail');

// Rumah Sakit Routes
Route::get('/data-layanan/rumahsakit', [DataLayananController::class, 'rumahSakit'])->name('data.layanan.rumahsakit');
Route::get('/data-layanan/rumahsakit/{id}', [DataLayananController::class, 'detailRumahSakit'])->name('data.layanan.rumahsakit.detail');

// Puskesmas Routes
Route::get('/data-layanan/puskesmas', [DataLayananController::class, 'puskesmas'])->name('data.layanan.puskesmas');
Route::get('/data-layanan/puskesmas/{id}', [DataLayananController::class, 'detailPuskesmas'])->name('data.layanan.puskesmas.detail');

// Admin Authentication Routes (Public - No Middleware)
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Routes (Protected with Middleware)
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // API untuk data statistik 
    Route::get('/api/statistics', [DashboardController::class, 'getStatistics'])->name('admin.api.statistics');
    Route::get('/api/district-distribution', [DashboardController::class, 'getDistrictDistribution'])->name('admin.api.district');
    Route::get('/api/village-distribution', [DashboardController::class, 'getVillageDistribution'])->name('admin.api.village');
    Route::get('/api/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('admin.api.activities');
    Route::get('/api/districts', [DashboardController::class, 'getDistricts'])->name('admin.api.districts');
   
    // API routes untuk dashboard
    Route::get('/admin/api/statistics', [DashboardController::class, 'apiStatistics'])->name('admin.api.statistics');
    Route::get('/admin/api/activities', [DashboardController::class, 'apiActivities'])->name('admin.api.activities');
    
    // Apotek Admin Management Routes
    Route::get('/apotek', [ApotekAdminController::class, 'index'])->name('admin.apotek.index');
    Route::get('/apotek/create', [ApotekAdminController::class, 'create'])->name('admin.apotek.create');
    Route::post('/apotek/store', [ApotekAdminController::class, 'store'])->name('admin.apotek.store');
    Route::get('/apotek/edit/{id}', [ApotekAdminController::class, 'edit'])->name('admin.apotek.edit');
    Route::put('/apotek/update/{id}', [ApotekAdminController::class, 'update'])->name('admin.apotek.update');
    Route::delete('/apotek/destroy/{id}', [ApotekAdminController::class, 'destroy'])->name('admin.apotek.destroy');
    Route::get('/apotek/search', [ApotekAdminController::class, 'search'])->name('admin.apotek.search');
    Route::get('/apotek/export/pdf', [ApotekAdminController::class, 'exportPDF'])->name('admin.apotek.export.pdf');
    Route::get('/get-kelurahans', [App\Http\Controllers\Admin\ApotekApiController::class, 'getKelurahans'])->name('admin.get-kelurahans');
Route::get('/get-location-coordinates', [App\Http\Controllers\Admin\ApotekApiController::class, 'getLocationCoordinates'])->name('admin.get-location-coordinates');

    // Klinik Admin Management Routes
    Route::get('/klinik', [KlinikAdminController::class, 'index'])->name('admin.klinik.index');
    Route::get('/klinik/create', [KlinikAdminController::class, 'create'])->name('admin.klinik.create');
    Route::post('/klinik/store', [KlinikAdminController::class, 'store'])->name('admin.klinik.store');
    Route::get('/klinik/edit/{id}', [KlinikAdminController::class, 'edit'])->name('admin.klinik.edit');
    Route::put('/klinik/update/{id}', [KlinikAdminController::class, 'update'])->name('admin.klinik.update');
    Route::delete('/klinik/destroy/{id}', [KlinikAdminController::class, 'destroy'])->name('admin.klinik.destroy');
    Route::get('/klinik/search', [KlinikAdminController::class, 'search'])->name('admin.klinik.search');
    Route::get('/klinik/export/pdf', [KlinikAdminController::class, 'exportPDF'])->name('admin.klinik.export.pdf');
    
    // Rumah Sakit Admin Management Routes
    Route::get('/rumahsakit', [RumahSakitAdminController::class, 'index'])->name('admin.rumahsakit.index');
    Route::get('/rumahsakit/create', [RumahSakitAdminController::class, 'create'])->name('admin.rumahsakit.create');
    Route::post('/rumahsakit/store', [RumahSakitAdminController::class, 'store'])->name('admin.rumahsakit.store');
    Route::get('/rumahsakit/edit/{id_rs}', [RumahSakitAdminController::class, 'edit'])->name('admin.rumahsakit.edit');
    Route::put('/rumahsakit/update/{id_rs}', [RumahSakitAdminController::class, 'update'])->name('admin.rumahsakit.update');
    Route::delete('/rumahsakit/destroy/{id_rs}', [RumahSakitAdminController::class, 'destroy'])->name('admin.rumahsakit.destroy');
    Route::get('/rumahsakit/search', [RumahSakitAdminController::class, 'search'])->name('admin.rumahsakit.search');
    Route::get('/rumahsakit/export/pdf', [RumahSakitAdminController::class, 'exportPDF'])->name('admin.rumahsakit.export.pdf');

    // Puskesmas Admin Management Routes
    Route::get('/puskesmas', [PuskesmasAdminController::class, 'index'])->name('admin.puskesmas.index');
    Route::get('/puskesmas/create', [PuskesmasAdminController::class, 'create'])->name('admin.puskesmas.create');
    Route::post('/puskesmas/store', [PuskesmasAdminController::class, 'store'])->name('admin.puskesmas.store');
    Route::get('/puskesmas/edit/{id_puskesmas}', [PuskesmasAdminController::class, 'edit'])->name('admin.puskesmas.edit');
    Route::put('/puskesmas/update/{id_puskesmas}', [PuskesmasAdminController::class, 'update'])->name('admin.puskesmas.update');
    Route::delete('/puskesmas/destroy/{id_puskesmas}', [PuskesmasAdminController::class, 'destroy'])->name('admin.puskesmas.destroy');
    Route::get('/puskesmas/search', [PuskesmasAdminController::class, 'search'])->name('admin.puskesmas.search');
    Route::get('/puskesmas/export/pdf', [PuskesmasAdminController::class, 'exportPDF'])->name('admin.puskesmas.export.pdf');

    // Klaster routes
    Route::get('/puskesmas/{id_puskesmas}/klaster', [PuskesmasAdminController::class, 'klasterIndex'])->name('admin.puskesmas.klaster.index');
    Route::post('/puskesmas/{id_puskesmas}/klaster/store', [PuskesmasAdminController::class, 'klasterStore'])->name('admin.puskesmas.klaster.store');
    Route::put('/puskesmas/klaster/{id_klaster}', [PuskesmasAdminController::class, 'klasterUpdate'])->name('admin.puskesmas.klaster.update');
    Route::delete('/puskesmas/klaster/{id_klaster}', [PuskesmasAdminController::class, 'klasterDestroy'])->name('admin.puskesmas.klaster.destroy');
    
    // Layanan routes
    Route::get('/puskesmas/klaster/{id_klaster}/layanan', [PuskesmasAdminController::class, 'layananIndex'])->name('admin.puskesmas.layanan.index');
    Route::post('/puskesmas/klaster/{id_klaster}/layanan/store', [PuskesmasAdminController::class, 'layananStore'])->name('admin.puskesmas.layanan.store');
    Route::put('/puskesmas/layanan/{id_layanan}', [PuskesmasAdminController::class, 'layananUpdate'])->name('admin.puskesmas.layanan.update');
    Route::delete('/puskesmas/layanan/{id_layanan}', [PuskesmasAdminController::class, 'layananDestroy'])->name('admin.puskesmas.layanan.destroy');
});