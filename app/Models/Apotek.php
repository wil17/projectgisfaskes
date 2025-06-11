<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apotek extends Model
{
   protected $table = 'apotek';
    protected $primaryKey = 'id_apotek';
    public $incrementing = false; // Karena menggunakan string sebagai primary key
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_apotek', 'id', 'nama_apotek', 'skala_usaha', 'alamat', 'kota', 'kecamatan',
        'kelurahan', 'longitude', 'latitude', 'tgl_berdiri', 'tenaga_kerja'
    ];
    
    public $timestamps = false;

    /**
     * Update atau insert data apotek
     */
    public function updateOrInsertApotek($data)
    {
        // Jika data dengan id_apotek sudah ada, lakukan update, jika tidak lakukan insert
        $existingData = $this->where('id_apotek', $data['id_apotek'])->first();
        
        if ($existingData) {
            // Update data yang sudah ada
            $existingData->update($data);
        } else {
            // Insert data baru
            $this->insert($data);
        }
    }
    
    /**
     * Get the faskes record associated with the apotek.
     */
    public function faskes()
    {
        return $this->belongsTo(Faskes::class, 'id', 'id');
    }
}