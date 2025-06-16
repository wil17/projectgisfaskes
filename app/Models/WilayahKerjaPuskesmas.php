<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'kelurahan',
        'id'
    ];
    
    /**
     * Get the puskesmas that owns the wilayah kerja.
     */
    public function puskesmas()
    {
        return $this->belongsTo(Faskes::class, 'id', 'id');
    }
    
    /**
     * Get wilayah kerja by puskesmas ID
     */
    public static function getWilayahKerjaByPuskesmasId($id)
    {
        return DB::table('wilayah_kerja_puskesmas')
                 ->where('id', $id)
                 ->select('kelurahan')
                 ->get();
    }
    
    /**
     * Delete wilayah kerja by puskesmas ID
     */
    public static function deleteByPuskesmasId($id)
    {
        return DB::table('wilayah_kerja_puskesmas')
                 ->where('id', $id)
                 ->delete();
    }
    
    /**
     * Add new wilayah kerja
     */
    public static function addWilayahKerja($id, $kelurahan)
    {
        return DB::table('wilayah_kerja_puskesmas')->insert([
            'id_wilayah' => \Illuminate\Support\Str::random(10), // Generate unique ID for id_wilayah
            'id' => $id,
            'kelurahan' => $kelurahan
        ]);
    }
}