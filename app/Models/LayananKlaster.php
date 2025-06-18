<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananKlaster extends Model
{
    use HasFactory;
    
    protected $table = 'layanan_klaster';
    protected $primaryKey = 'id_layanan';
    public $timestamps = false;
    
    protected $fillable = [
        'id_layanan',
        'id',
        'id_klaster',
        'nama_layanan',
        'deskripsi_layanan',
        'jumlah_petugas',
        'nama_puskesmas',
        'nama_klaster',
        'kode_klaster',
        'penanggung_jawab'
    ];
    
    /**
     * Get the puskesmas associated with the layanan.
     */
    public function puskesmas()
    {
        return $this->belongsTo(Faskes::class, 'id', 'id');
    }
    
    /**
     * Scope a query untuk mendapatkan hanya klaster.
     * Klaster record bisa diidentifikasi dari nama_layanan NULL
     * ATAU jika id_klaster = id_layanan (untuk data lama)
     */
    public function scopeKlasterOnly($query)
    {
        return $query->where(function($q) {
            $q->whereNull('nama_layanan')
              ->orWhereRaw('id_klaster = id_layanan');
        });
    }
    
    /**
     * Scope a query untuk mendapatkan hanya layanan.
     * Layanan record bisa diidentifikasi dari nama_layanan NOT NULL
     * DAN id_klaster != id_layanan (untuk memastikan bukan klaster lama)
     */
    public function scopeLayananOnly($query)
    {
        return $query->whereNotNull('nama_layanan')
                    ->whereRaw('id_klaster != id_layanan OR id_layanan IS NULL');
    }
    
    /**
     * Accessor untuk nama layanan pada record klaster lama
     */
    public function getNamaKlasterDisplayAttribute()
    {
        // Jika ini record klaster lama (id_klaster = id_layanan)
        if ($this->id_klaster == $this->id_layanan && $this->nama_layanan) {
            return $this->nama_layanan;
        }
        
        return $this->nama_klaster;
    }
    
    /**
     * Accessor untuk mendapatkan deskripsi layanan yang benar
     */
    public function getDeskripsiLayananDisplayAttribute()
    {
        return $this->deskripsi_layanan ?? 'Tidak ada deskripsi';
    }
}