<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puskesmas extends Model
{
    use HasFactory;
    
    protected $table = 'puskesmas';
    protected $primaryKey = 'id_puskesmas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'id_puskesmas',
        'id',
        'nama_puskesmas',
        'alamat',
        'jam_operasional',
        'kepala_puskesmas',
        'kota',
        'kecamatan',
        'kelurahan',
        'longitude',
        'latitude'
    ];
    
    /**
     * Get the klaster for this puskesmas.
     */
    public function klaster()
    {
        return $this->hasMany(KlasterPuskesmas::class, 'id_puskesmas', 'id_puskesmas');
    }
    
    /**
     * Get the wilayah kerja for this puskesmas.
     */
    public function wilayahKerja()
    {
        return $this->hasMany(WilayahKerjaPuskesmas::class, 'id_puskesmas', 'id_puskesmas');
    }
    
    /**
     * Get the faskes record associated with this puskesmas.
     */
    public function faskes()
    {
        return $this->belongsTo(Faskes::class, 'id', 'id');
    }
}