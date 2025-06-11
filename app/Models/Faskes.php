<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faskes extends Model
{
    use HasFactory;
    
    protected $table = 'faskes';
    
    protected $fillable = [
        'id',
        'nama',
        'fasilitas',
        'alamat',
        'kecamatan',
        'kelurahan',
        'latitude',
        'longitude'
    ];
    
    /**
     * Get the apotek record associated with this faskes.
     */
    public function apotek()
    {
        return $this->hasOne(Apoteks::class, 'id', 'id');
    }
    
    /**
     * Get the klinik record associated with this faskes.
     */
    public function klinik()
    {
        return $this->hasOne(Kliniks::class, 'id', 'id');
    }

    public function rumahsakit()
    {
        return $this->hasOne(RumahSakit::class, 'id', 'id');
    }
}