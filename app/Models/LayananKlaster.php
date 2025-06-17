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
     */
    public function scopeKlasterOnly($query)
    {
        return $query->whereNull('nama_layanan');
    }
    
    /**
     * Scope a query untuk mendapatkan hanya layanan.
     */
    public function scopeLayananOnly($query)
    {
        return $query->whereNotNull('nama_layanan');
    }
}