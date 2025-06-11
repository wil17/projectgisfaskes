<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable(); // ID admin yang melakukan aksi
            $table->string('user_name')->nullable(); // Nama admin
            $table->string('action'); // create, update, delete
            $table->string('model_type'); // App\Models\Faskes, App\Models\Apotek, etc
            $table->string('model_id'); // ID dari data yang diubah
            $table->string('model_name')->nullable(); // Nama faskes/apotek/klinik
            $table->string('facility_type')->nullable(); // Apotek, Klinik, Puskesmas, Rumah Sakit
            $table->json('old_values')->nullable(); // Data lama (untuk update/delete)
            $table->json('new_values')->nullable(); // Data baru (untuk create/update)
            $table->text('description')->nullable(); // Deskripsi singkat aktivitas
            $table->timestamps();
            
            // Indexes
            $table->index(['created_at']);
            $table->index(['action']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};