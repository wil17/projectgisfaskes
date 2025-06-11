<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kliniks extends Model
{
   protected $table = 'klinik';
    protected $primaryKey = 'id_klinik';
    public $incrementing = false; // Karena menggunakan string sebagai primary key
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_klinik', 'id', 'nama_klinik', 'skala_usaha', 'alamat', 'kota', 'kecamatan',
        'kelurahan', 'longitude', 'latitude', 'tgl_berdiri', 'tenaga_kerja'
    ];
    
    public $timestamps = false;

    /**
     * Update atau insert data klinik
     */
    public function updateOrInsertKlinik($data)
    {
        // Jika data dengan id_klinik sudah ada, lakukan update, jika tidak lakukan insert
        $existingData = $this->where('id_klinik', $data['id_klinik'])->first();
        
        if ($existingData) {
            // Update data yang sudah ada
            $existingData->update($data);
        } else {
            // Insert data baru
            $this->insert($data);
        }
    }
    
    /**
     * Get the faskes record associated with the klinik.
     */
    public function faskes()
    {
        return $this->belongsTo(Faskes::class, 'id', 'id');
    }
}