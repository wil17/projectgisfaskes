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
        'id_klaster',
        'id_puskesmas',
        'nama_layanan',
        'deskripsi_layanan',
        'jumlah_petugas'
    ];
    
    /**
     * Get the klaster that owns the layanan.
     */
    public function klaster()
    {
        return $this->belongsTo(KlasterPuskesmas::class, 'id_klaster', 'id_klaster');
    }
    
    /**
     * Get the puskesmas associated with the layanan.
     */
    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'id_puskesmas', 'id_puskesmas');
    }
}