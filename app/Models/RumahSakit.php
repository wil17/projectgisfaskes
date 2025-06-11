<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumahSakit extends Model
{
    protected $table = 'rumahsakit';
    protected $primaryKey = 'id_rs';
    public $incrementing = false; // Karena menggunakan string sebagai primary key
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_rs', 'id', 'nama_rs', 'alamat', 'poliklinik_dokter',
        'kota', 'kecamatan', 'kelurahan', 'longitude', 'latitude'
    ];
    
    public $timestamps = false;

    /**
     * Parse poliklinik_dokter menjadi array yang terstruktur
     */
    public function getPoliklinikDokterArrayAttribute()
    {
        if (empty($this->poliklinik_dokter)) {
            return [];
        }
        
        $poliDokterMap = [];
        
        // Split string berdasarkan pola "Poli X: Dr. A, Dr. B; Poli Y: Dr. C"
        $poliSections = preg_split('/;\s*/', $this->poliklinik_dokter);
        
        foreach ($poliSections as $section) {
            // Skip if section is empty
            if (empty(trim($section))) {
                continue;
            }
            
            // Split each section into polyclinic name and doctors list
            if (preg_match('/^(.*?):\s*(.*)$/', $section, $matches)) {
                $poliName = trim($matches[1]);
                $dokterList = trim($matches[2]);
                
                // Skip if poliName is empty
                if (empty($poliName)) {
                    continue;
                }
                
                // Split the doctors string into an array
                $dokters = [];
                if (!empty($dokterList)) {
                    $dokters = array_map('trim', explode(',', $dokterList));
                }
                
                $poliDokterMap[$poliName] = $dokters;
            } else {
                // If the section doesn't match the pattern, assume it's just a polyclinic name
                $poliDokterMap[trim($section)] = [];
            }
        }
        
        return $poliDokterMap;
    }
    
    /**
     * Update atau insert data rumah sakit
     */
    public function updateOrInsertRumahSakit($data)
    {
        // Jika data dengan id_rs sudah ada, lakukan update, jika tidak lakukan insert
        $existingData = $this->where('id_rs', $data['id_rs'])->first();
        
        if ($existingData) {
            // Update data yang sudah ada
            $existingData->update($data);
        } else {
            // Insert data baru
            $this->insert($data);
        }
    }
    
    /**
     * Get the faskes record associated with the rumah sakit.
     */
    public function faskes()
    {
        return $this->belongsTo(Faskes::class, 'id', 'id');
    }
}