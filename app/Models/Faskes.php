<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faskes extends Model
{
    use HasFactory;
    
    protected $table = 'faskes';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'nama',
        'fasilitas',
        'alamat',
        'kota',
        'kecamatan',
        'kelurahan',
        'latitude',
        'longitude',
        'jenis_faskes',
        'jam_operasional',
        'tgl_berdiri',
        'skala_usaha',
        'tenaga_kerja',
        'kepala_puskesmas',
        'poliklinik_dokter',
        'created_at',
        'updated_at'
    ];
    
    /**
     * Get the wilayah kerja for the puskesmas.
     */
    public function wilayahKerja()
    {
        return $this->hasMany(WilayahKerjaPuskesmas::class, 'id', 'id');
    }
    
    /**
     * Get the klaster for the puskesmas.
     */
    public function klaster()
    {
        return $this->hasMany(KlasterPuskesmas::class, 'id_puskesmas', 'id');
    }
    
    /**
     * Scope a query to only include apoteks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApoteks($query)
    {
        return $query->where('fasilitas', 'Apotek');
    }
    
    /**
     * Scope a query to only include kliniks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKliniks($query)
    {
        return $query->where('fasilitas', 'Klinik');
    }
    
    /**
     * Scope a query to only include rumahsakits.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRumahsakits($query)
    {
        return $query->where('fasilitas', 'Rumah Sakit');
    }
    
    /**
     * Scope a query to only include puskesmas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePuskesmas($query)
    {
        return $query->where('fasilitas', 'Puskesmas');
    }
    
    /**
     * Get the nama_apotek attribute.
     *
     * @return string
     */
    public function getNamaApotekAttribute()
    {
        return $this->nama;
    }
    
    /**
     * Set the nama_apotek attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setNamaApotekAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }
    
    /**
     * Get the id_apotek attribute.
     *
     * @return string
     */
    public function getIdApotekAttribute()
    {
        return $this->id;
    }
    
    /**
     * Get the nama_klinik attribute.
     *
     * @return string
     */
    public function getNamaKlinikAttribute()
    {
        return $this->nama;
    }
    
    /**
     * Get the id_klinik attribute.
     *
     * @return string
     */
    public function getIdKlinikAttribute()
    {
        return $this->id;
    }
    
    /**
     * Get the nama_rs attribute.
     *
     * @return string
     */
    public function getNamaRsAttribute()
    {
        return $this->nama;
    }
    
    /**
     * Get the id_rs attribute.
     *
     * @return string
     */
    public function getIdRsAttribute()
    {
        return $this->id;
    }
    
    /**
     * Get the nama_puskesmas attribute.
     *
     * @return string
     */
    public function getNamaPuskesmasAttribute()
    {
        return $this->nama;
    }
    
    /**
     * Get the id_puskesmas attribute.
     *
     * @return string
     */
    public function getIdPuskesmasAttribute()
    {
        return $this->id;
    }
    
    /**
     * Get the poliklinik_dokter_array attribute.
     * Converts the poliklinik_dokter text field to an array
     *
     * @return array
     */
    public function getPoliklinikDokterArrayAttribute()
    {
        if (empty($this->poliklinik_dokter)) {
            return [];
        }
        
        // Initialize result array
        $result = [];
        
        // Split by semicolons to get each poliklinik with its doctors
        $sections = preg_split('/;\s*/', $this->poliklinik_dokter, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($sections as $section) {
            // Split each section by colon to separate poliklinik name and doctors
            $parts = explode(':', $section, 2);
            
            if (count($parts) == 2) {
                $poliklinik = trim($parts[0]);
                $dokters = array_map('trim', explode(',', $parts[1]));
                
                // Add to result array
                $result[$poliklinik] = $dokters;
            }
        }
        
        return $result;
    }
}