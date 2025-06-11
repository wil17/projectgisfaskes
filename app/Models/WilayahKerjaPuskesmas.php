<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahKerjaPuskesmas extends Model
{
    use HasFactory;
    
    protected $table = 'wilayah_kerja_puskesmas';
    protected $primaryKey = 'id_wilayah';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'id_wilayah',
        'id_puskesmas',
        'kelurahan'
    ];
    
    /**
     * Get the puskesmas that owns the wilayah kerja.
     */
    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'id_puskesmas', 'id_puskesmas');
    }
}