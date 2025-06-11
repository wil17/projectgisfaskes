<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlasterPuskesmas extends Model
{
    use HasFactory;
    
    protected $table = 'klaster_puskesmas';
    protected $primaryKey = 'id_klaster';
    public $timestamps = false;
    
    protected $fillable = [
        'id_klaster',
        'id_puskesmas',
        'nama_puskesmas',
        'nama_klaster',
        'kode_klaster',
        'penanggung_jawab'
    ];
    
    /**
     * Get the puskesmas that owns the klaster.
     */
    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'id_puskesmas', 'id_puskesmas');
    }
    
    /**
     * Get the layanan for this klaster.
     */
    public function layanan()
    {
        return $this->hasMany(LayananKlaster::class, 'id_klaster', 'id_klaster');
    }
}