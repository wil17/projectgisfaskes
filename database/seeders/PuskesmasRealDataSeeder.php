<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PuskesmasRealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Data Puskesmas dari file yang diupload
        $puskesmasData = [
            [
                'nama_puskesmas' => 'Puskesmas Sungai Bilu',
                'kepala_puskesmas' => 'dr. Hj. Sri Heriyani',
                'alamat' => 'Jl. Veteran Simpang SMP 7 RT 30 RW 002 No 4 Banjarmasin',
                'kecamatan' => 'Banjarmasin Selatan',
                'kelurahan' => 'Sungai Bilu',
                'latitude' => -3.3420,
                'longitude' => 114.5820,
                'email' => 'pkmsungaibilu@gmail.com',
                'klaster_data' => [
                    1 => [
                        ['penanggung_jawab' => 'Noorjamilah, A.Md', 'nama_anggota' => 'Khairini Rizky Septia, A.Md.RMIK', 'jabatan' => 'Ketatausahaan (KIR-KES)'],
                        ['penanggung_jawab' => 'Noorjamilah, A.Md', 'nama_anggota' => 'Saidah Haryatie', 'jabatan' => 'Ketatausahaan (KIR-KES)'],
                        ['penanggung_jawab' => 'Noorjamilah, A.Md', 'nama_anggota' => 'Noor Hasanah, Amd.AK', 'jabatan' => 'Manajemen Sumber Daya'],
                        ['penanggung_jawab' => 'Noorjamilah, A.Md', 'nama_anggota' => 'apt. Muhammad Iman Rizqiawan, S.Farm', 'jabatan' => 'Manajemen Sumber Daya'],
                        ['penanggung_jawab' => 'Noorjamilah, A.Md', 'nama_anggota' => 'Muhammad Rizal, A.Md.Ak', 'jabatan' => 'Manajemen Puskesmas'],
                        ['penanggung_jawab' => 'Noorjamilah, A.Md', 'nama_anggota' => 'Zumrotush Sholihah, AM.Keb', 'jabatan' => 'Manajemen Mutu & Keselamatan Pasien'],
                    ],
                    2 => [
                        ['penanggung_jawab' => 'Chairiati Yulidasari, S.ST', 'nama_anggota' => 'Vivin Maharani, A.Md.Keb', 'jabatan' => 'IBU (ANC, ibu hamil, persalinan dan nifas)'],
                        ['penanggung_jawab' => 'Chairiati Yulidasari, S.ST', 'nama_anggota' => 'Novita Apriliana, A.Md.Kep', 'jabatan' => 'ANAK (Neonatal esensial, SDIDTK, MTBS)'],
                        ['penanggung_jawab' => 'Chairiati Yulidasari, S.ST', 'nama_anggota' => 'Nur Asmi latifah, A.Md.Gz', 'jabatan' => 'Pelayanan gizi bagi ibu dan anak'],
                        ['penanggung_jawab' => 'Chairiati Yulidasari, S.ST', 'nama_anggota' => 'Rosa Sosiawati, S.Kep.Ners', 'jabatan' => 'Imunisasi'],
                        ['penanggung_jawab' => 'Chairiati Yulidasari, S.ST', 'nama_anggota' => 'Eka Maulinda, A.Md.KG', 'jabatan' => 'Kesehatan Gigi dan Mulut'],
                    ],
                    3 => [
                        ['penanggung_jawab' => 'dr. Nawis Esti Wibowo', 'nama_anggota' => 'Syahruddin', 'jabatan' => 'Skrining Penyakit Menular'],
                        ['penanggung_jawab' => 'dr. Nawis Esti Wibowo', 'nama_anggota' => 'Dwi Risnawati, AM.Kep', 'jabatan' => 'Skrining PTM'],
                        ['penanggung_jawab' => 'dr. Nawis Esti Wibowo', 'nama_anggota' => 'Berniati, S.Kep.Ners', 'jabatan' => 'Skrining Kesehatan Jiwa'],
                        ['penanggung_jawab' => 'dr. Nawis Esti Wibowo', 'nama_anggota' => 'Hairun Nisa, A.Md.Kep', 'jabatan' => 'Skrining Kebugaran'],
                        ['penanggung_jawab' => 'dr. Nawis Esti Wibowo', 'nama_anggota' => 'Nahdiati, A.Md.Gz', 'jabatan' => 'Pelayanan Gizi Bagi Usia Produktif dan Lansia'],
                    ],
                    4 => [
                        ['penanggung_jawab' => 'Berniati, S.Kep.Ners', 'nama_anggota' => 'Muhammad Ridha Ansyari, SKM', 'jabatan' => 'Surveilans, Penemuan Kasus, Penyelidikan Epidemiologi'],
                        ['penanggung_jawab' => 'Berniati, S.Kep.Ners', 'nama_anggota' => 'Rosa Sosiawati, S.Kep.Ners', 'jabatan' => 'Outbreak Respons Imunization (ORI)'],
                        ['penanggung_jawab' => 'Berniati, S.Kep.Ners', 'nama_anggota' => 'Tazkiyatul Helmiah, AMKL', 'jabatan' => 'Pelayanan Kesehatan Lingkungan'],
                    ],
                    5 => [
                        ['penanggung_jawab' => 'apt. Muhammad Iman Rizqiawan, S.Farm', 'nama_anggota' => 'Syahruddin', 'jabatan' => 'Kegawatdaruratan'],
                        ['penanggung_jawab' => 'apt. Muhammad Iman Rizqiawan, S.Farm', 'nama_anggota' => 'Dariah, Amd.Farm', 'jabatan' => 'Kefarmasian'],
                        ['penanggung_jawab' => 'apt. Muhammad Iman Rizqiawan, S.Farm', 'nama_anggota' => 'Rismarini, Amd.Farm', 'jabatan' => 'Kefarmasian'],
                        ['penanggung_jawab' => 'apt. Muhammad Iman Rizqiawan, S.Farm', 'nama_anggota' => 'Noor Hasanah, Amd.AK', 'jabatan' => 'Pemeriksaan Laboratorium'],
                    ]
                ]
            ],
            [
                'nama_puskesmas' => 'Puskesmas Teluk Tiram',
                'kepala_puskesmas' => 'dr. Hj. Mei Sari Prihatini',
                'alamat' => 'Jalan Teluk Tiram Darat Nomor 208 RT 13',
                'kecamatan' => 'Banjarmasin Utara',
                'kelurahan' => 'Teluk Tiram',
                'latitude' => -3.2920,
                'longitude' => 114.5680,
                'email' => 'pusk.teluktiram@gmail.com',
                'klaster_data' => [
                    1 => [
                        ['penanggung_jawab' => 'Ridwan Marhal, SKM', 'nama_anggota' => 'Riswandayani Savitri,S.Farm,Apt', 'jabatan' => 'Ketua Mutu'],
                        ['penanggung_jawab' => 'Ridwan Marhal, SKM', 'nama_anggota' => 'Inandi Harini,A.Md.AK', 'jabatan' => 'Sekretaris Mutu'],
                        ['penanggung_jawab' => 'Ridwan Marhal, SKM', 'nama_anggota' => 'Rita Kesumawati,A.Md.Kg', 'jabatan' => 'Pengelola keuangan'],
                        ['penanggung_jawab' => 'Ridwan Marhal, SKM', 'nama_anggota' => 'Khairuzzakirin, A.Md.Kes', 'jabatan' => 'Pengadministrasi Rekam Medis'],
                    ],
                    2 => [
                        ['penanggung_jawab' => 'Elli Novita sari, AM.Keb', 'nama_anggota' => 'Rizky Amalia, Amd.Keb', 'jabatan' => 'Bidan'],
                        ['penanggung_jawab' => 'Elli Novita sari, AM.Keb', 'nama_anggota' => 'Khairunnisa, A.Md.Keb', 'jabatan' => 'Bidan'],
                        ['penanggung_jawab' => 'Elli Novita sari, AM.Keb', 'nama_anggota' => 'Jumiati Olpah,S.Keb.bd', 'jabatan' => 'Bidan'],
                        ['penanggung_jawab' => 'Elli Novita sari, AM.Keb', 'nama_anggota' => 'Fitriani Mauliada,A.Md.Gz', 'jabatan' => 'Gizi'],
                    ],
                    3 => [
                        ['penanggung_jawab' => 'dr. Devita Wijayanti', 'nama_anggota' => 'Nova Meli,AMK', 'jabatan' => 'Perawat'],
                        ['penanggung_jawab' => 'dr. Devita Wijayanti', 'nama_anggota' => 'M. Rifqi Maulani,A.Md.Kep', 'jabatan' => 'Perawat'],
                        ['penanggung_jawab' => 'dr. Devita Wijayanti', 'nama_anggota' => 'Dwi Yunia sari,A.Md.Gizi', 'jabatan' => 'Gizi'],
                        ['penanggung_jawab' => 'dr. Devita Wijayanti', 'nama_anggota' => 'Anida Hayati,A.Md.Keb', 'jabatan' => 'Bidan'],
                    ],
                    4 => [
                        ['penanggung_jawab' => 'Ahriyana,AMKL', 'nama_anggota' => 'Khairun nisa,A.Md.Keb', 'jabatan' => 'ISPA'],
                        ['penanggung_jawab' => 'Ahriyana,AMKL', 'nama_anggota' => 'Dina Aulia, SKM', 'jabatan' => 'Surveilans dan Malaria'],
                        ['penanggung_jawab' => 'Ahriyana,AMKL', 'nama_anggota' => 'Andriani zaikun,S.Kep', 'jabatan' => 'HIV'],
                    ],
                    5 => [
                        ['penanggung_jawab' => 'apt. Riswandayani Savitri, S.Farm', 'nama_anggota' => 'dr. Nor Halimah Amini', 'jabatan' => 'Dokter Ruang Tindakan'],
                        ['penanggung_jawab' => 'apt. Riswandayani Savitri, S.Farm', 'nama_anggota' => 'drg. Apriyanti Khairina', 'jabatan' => 'Dokter Gigi'],
                        ['penanggung_jawab' => 'apt. Riswandayani Savitri, S.Farm', 'nama_anggota' => 'Inandi Harini,A.Md.AK', 'jabatan' => 'Laboratorium'],
                        ['penanggung_jawab' => 'apt. Riswandayani Savitri, S.Farm', 'nama_anggota' => 'Agustianuri,A.Md.Farm', 'jabatan' => 'Asisten Apoteker'],
                    ]
                ]
            ],
            [
                'nama_puskesmas' => 'Puskesmas Banjarmasin Indah',
                'kepala_puskesmas' => 'dr. Chusna Farida',
                'alamat' => 'Jl. Banjarmasin Indah',
                'kecamatan' => 'Banjarmasin Selatan',
                'kelurahan' => 'Pemurus Baru',
                'latitude' => -3.3450,
                'longitude' => 114.5950,
                'email' => 'pkm.banjarmasinindah@gmail.com',
                'klaster_data' => [
                    1 => [
                        ['penanggung_jawab' => 'Gusti Mulyani,Amd.Keb', 'nama_anggota' => 'Novi Rezeki Aulia', 'jabatan' => 'Manajemen Inti Puskesmas'],
                        ['penanggung_jawab' => 'Gusti Mulyani,Amd.Keb', 'nama_anggota' => 'dr.Lessyana Yulita', 'jabatan' => 'Manajemen Sumber Daya Manusia'],
                        ['penanggung_jawab' => 'Gusti Mulyani,Amd.Keb', 'nama_anggota' => 'drg.Muniroh', 'jabatan' => 'Manajemen Keuangan dan Aset'],
                        ['penanggung_jawab' => 'Gusti Mulyani,Amd.Keb', 'nama_anggota' => 'Sartika Bestarini Sari,SKM', 'jabatan' => 'Manajemen Sistem Informasi Digital'],
                    ],
                    2 => [
                        ['penanggung_jawab' => 'dr.Lessyana Yulita', 'nama_anggota' => 'Yeni Andikawati,AM Keb', 'jabatan' => 'Ibu Hamil Bersalin Nifas'],
                        ['penanggung_jawab' => 'dr.Lessyana Yulita', 'nama_anggota' => 'Stevy Kumboti,AM Keb', 'jabatan' => 'Bayi dan anak Balita'],
                        ['penanggung_jawab' => 'dr.Lessyana Yulita', 'nama_anggota' => 'Gusti Mulyani,Amd.Keb', 'jabatan' => 'Anak Pra sekolah'],
                        ['penanggung_jawab' => 'dr.Lessyana Yulita', 'nama_anggota' => 'Stevy Kumboti,AM Keb', 'jabatan' => 'Anak Usia Sekolah'],
                    ],
                    3 => [
                        ['penanggung_jawab' => 'dr.Oktavia Rahayu Ulfah', 'nama_anggota' => 'Sartika Bestarini Sari,SKM', 'jabatan' => 'Usia Dewasa'],
                        ['penanggung_jawab' => 'dr.Oktavia Rahayu Ulfah', 'nama_anggota' => 'Gusti Mulyani,Amd.Keb', 'jabatan' => 'Lansia'],
                    ],
                    4 => [
                        ['penanggung_jawab' => 'Asharul Faisal,SKM', 'nama_anggota' => 'Asharul Faisal,SKM', 'jabatan' => 'Surveilans Dan Respon Penyakit Menular'],
                        ['penanggung_jawab' => 'Asharul Faisal,SKM', 'nama_anggota' => 'Sarbini,Amd.Kes', 'jabatan' => 'Surveilans Dan Respon Kesehatan Lingkungan'],
                    ],
                    5 => [
                        ['penanggung_jawab' => 'apt.Muhammad Noraidi Nafarin,S.Farm', 'nama_anggota' => 'drg.Muniroh', 'jabatan' => 'Pelayanan Kesehatan Gigi Dan Mulut'],
                        ['penanggung_jawab' => 'apt.Muhammad Noraidi Nafarin,S.Farm', 'nama_anggota' => 'dr.Oktavia Rahayu Ulfah', 'jabatan' => 'Pelayanan Gawat Darurat'],
                        ['penanggung_jawab' => 'apt.Muhammad Noraidi Nafarin,S.Farm', 'nama_anggota' => 'apt.Muhammad Noraidi Nafarin,S.Farm', 'jabatan' => 'Pelayanan Kefarmasian'],
                        ['penanggung_jawab' => 'apt.Muhammad Noraidi Nafarin,S.Farm', 'nama_anggota' => 'Nurul Ainah,SKM', 'jabatan' => 'Pelayanan Laboratorium Kesehatan Masyarakat'],
                    ]
                ]
            ],
            [
                'nama_puskesmas' => 'Puskesmas Pekapuran Raya',
                'kepala_puskesmas' => 'Muhammad Ary Aprian Noor, S.Far., Apt, M.M',
                'alamat' => 'Jl. Pekapuran Raya',
                'kecamatan' => 'Banjarmasin Timur',
                'kelurahan' => 'Pekapuran',
                'latitude' => -3.3100,
                'longitude' => 114.6100,
                'email' => 'pkm.pekapuranraya@gmail.com',
                'klaster_data' => [
                    1 => [
                        ['penanggung_jawab' => 'Herman, AMd.Kep', 'nama_anggota' => 'Herman, A.Md.Kep.', 'jabatan' => 'Manajemen Inti Puskesmas'],
                        ['penanggung_jawab' => 'Herman, AMd.Kep', 'nama_anggota' => 'Muhammad Fikri, A.Md.Farm.', 'jabatan' => 'Manajemen Sarana, Prasarana, dan Perbekalan Kesehatan'],
                        ['penanggung_jawab' => 'Herman, AMd.Kep', 'nama_anggota' => 'drg. Endah Dwi Ariyani', 'jabatan' => 'Manajemen Mutu Pelayanan'],
                        ['penanggung_jawab' => 'Herman, AMd.Kep', 'nama_anggota' => 'Nina Rismalia, SKM.', 'jabatan' => 'Manajemen Sistem Informasi Digital'],
                    ],
                    2 => [
                        ['penanggung_jawab' => 'dr. Indah Lutfiatin', 'nama_anggota' => 'Hj.Rahmawati, A.MKeb.', 'jabatan' => 'Ibu Hamil, Bersalin, Nifas'],
                        ['penanggung_jawab' => 'dr. Indah Lutfiatin', 'nama_anggota' => 'Hj.Normaliani, A.Md.Keb.', 'jabatan' => 'Bayi, Anak Balita dan Anak Pra Sekolah'],
                        ['penanggung_jawab' => 'dr. Indah Lutfiatin', 'nama_anggota' => 'Nadia Melati, A.MKeb.', 'jabatan' => 'Anak Usia Sekolah dan Remaja'],
                    ],
                    3 => [
                        ['penanggung_jawab' => 'dr. Hj. Widi Utami, M.M.', 'nama_anggota' => 'Helmiah, AMK', 'jabatan' => 'Dewasa'],
                        ['penanggung_jawab' => 'dr. Hj. Widi Utami, M.M.', 'nama_anggota' => 'Khafizatun Nadia, A.MKeb', 'jabatan' => 'Lanjut usia (Lansia)'],
                    ],
                    4 => [
                        ['penanggung_jawab' => 'Hairawaty, AMK.', 'nama_anggota' => 'Aditya Saputra, SKM', 'jabatan' => 'Surveilans'],
                        ['penanggung_jawab' => 'Hairawaty, AMK.', 'nama_anggota' => 'Eka Rosmaya, AMd.Kes.', 'jabatan' => 'Kesehatan Lingkungan'],
                    ],
                    5 => [
                        ['penanggung_jawab' => 'drg Endah Dwi Ariyani', 'nama_anggota' => 'drg Endah Dwi Ariyani', 'jabatan' => 'Pelayanan Kesehatan Gigi dan Mulut'],
                        ['penanggung_jawab' => 'drg Endah Dwi Ariyani', 'nama_anggota' => 'Lina Puspa Sari, S.Kep, Ns.', 'jabatan' => 'Pelayanan Gawat Darurat'],
                        ['penanggung_jawab' => 'drg Endah Dwi Ariyani', 'nama_anggota' => 'Nisa Abdina, S.Farm., Apt', 'jabatan' => 'Pelayanan Kefarmasian'],
                        ['penanggung_jawab' => 'drg Endah Dwi Ariyani', 'nama_anggota' => 'Rabiah Rahmah N, A.Md.AK.', 'jabatan' => 'Pelayanan Laboratorium Kesehatan Masyarakat'],
                    ]
                ]
            ]
        ];

        // Update/Insert data puskesmas
        foreach ($puskesmasData as $data) {
            DB::beginTransaction();
            try {
                // Cari puskesmas yang sudah ada berdasarkan nama (case insensitive)
                $existingFaskes = DB::table('faskes')
                    ->where('fasilitas', 'Puskesmas')
                    ->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower(str_replace('Puskesmas ', '', $data['nama_puskesmas'])) . '%'])
                    ->first();

                $faskesId = null;

                if ($existingFaskes) {
                    // Update data faskes yang sudah ada
                    $faskesId = $existingFaskes->id;
                    
                    DB::table('faskes')
                        ->where('id', $faskesId)
                        ->update([
                            'alamat' => $data['alamat'],
                            'kecamatan' => $data['kecamatan'],
                            'kelurahan' => $data['kelurahan'],
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                            'updated_at' => Carbon::now(),
                        ]);
                        
                    echo "✓ Updated faskes: {$data['nama_puskesmas']} (ID: {$faskesId})\n";
                } else {
                    // Insert new faskes jika tidak ditemukan
                    $faskesId = DB::table('faskes')->insertGetId([
                        'fasilitas' => 'Puskesmas',
                        'nama' => $data['nama_puskesmas'], // Tambahkan nama jika kolom ada
                        'alamat' => $data['alamat'],
                        'kecamatan' => $data['kecamatan'],
                        'kelurahan' => $data['kelurahan'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    
                    echo "✓ Created new faskes: {$data['nama_puskesmas']} (ID: {$faskesId})\n";
                }

                // Cek apakah data puskesmas sudah ada
                $existingPuskesmas = DB::table('puskesmas')->where('id', $faskesId)->first();

                if ($existingPuskesmas) {
                    // Update data puskesmas (TANPA timestamps)
                    DB::table('puskesmas')
                        ->where('id', $faskesId)
                        ->update([
                            'nama_puskesmas' => $data['nama_puskesmas'],
                            'kepala_puskesmas' => $data['kepala_puskesmas'],
                        ]);
                    echo "✓ Updated puskesmas: {$data['nama_puskesmas']}\n";
                } else {
                    // Insert ke tabel puskesmas (TANPA timestamps)
                    DB::table('puskesmas')->insert([
                        'id' => $faskesId,
                        'nama_puskesmas' => $data['nama_puskesmas'],
                        'kepala_puskesmas' => $data['kepala_puskesmas'],
                    ]);
                    echo "✓ Created puskesmas: {$data['nama_puskesmas']}\n";
                }

                // Clear existing klaster data untuk update
                for ($i = 1; $i <= 5; $i++) {
                    DB::table("klaster{$i}_manajemen")
                        ->where('id_puskesmas', $faskesId)
                        ->delete();
                }

                // Insert klaster data
                $this->insertKlasterData($faskesId, $data['nama_puskesmas'], $data['klaster_data']);

                DB::commit();
                echo "✓ Completed processing: {$data['nama_puskesmas']}\n\n";
                
            } catch (\Exception $e) {
                DB::rollback();
                echo "✗ Error processing '{$data['nama_puskesmas']}': " . $e->getMessage() . "\n\n";
            }
        }
        
        echo "=== Seeder completed ===\n";
    }

    /**
     * Insert klaster data for each puskesmas
     */
    private function insertKlasterData($puskesmasId, $namaPuskesmas, $klasterData)
    {
        foreach ($klasterData as $klasterNum => $members) {
            foreach ($members as $member) {
                DB::table("klaster{$klasterNum}_manajemen")->insert([
                    'id_puskesmas' => $puskesmasId,
                    'nama_puskesmas' => $namaPuskesmas,
                    'penanggung_jawab' => $member['penanggung_jawab'],
                    'nama_anggota' => $member['nama_anggota'],
                    'jabatan' => $member['jabatan'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        echo "  ✓ Inserted klaster data for {$namaPuskesmas}\n";
    }
}