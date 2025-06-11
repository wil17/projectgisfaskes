<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('faskes', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('fasilitas');
            $table->string('alamat');
            $table->string('kecamatan');
            $table->double('latitude', 15, 10);  // Menggunakan DOUBLE untuk longitude
            $table->double('longitude', 15, 10);   // Menggunakan DOUBLE untuk latitude
            $table->timestamps();
        });
    }
    
    
    public function down()
    {
        Schema::dropIfExists('faskes');
    }
    
};
