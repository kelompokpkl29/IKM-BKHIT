<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
class KuesionerSeeder extends Seeder
{
    public function run()
    {
        $kuesionerModel = new KuesionerModel();
        $pertanyaanModel = new PertanyaanModel();
        $opsiJawabanModel = new OpsiJawabanModel();

        // Kuesioner 1: Pelayanan Administrasi
        $kuesionerId1 = $kuesionerModel->insert([
            'nama_kuesioner' => 'Kuesioner Pelayanan Administrasi',
            'deskripsi'      => 'Survei kepuasan terhadap proses dan kecepatan pelayanan administrasi.',
            'is_active'      => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($kuesionerId1) {
            // Pertanyaan 1.1: Kemudahan Akses
            $pertanyaanId1_1 = $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerId1,
                'teks_pertanyaan' => 'Bagaimana tingkat kemudahan dalam mengakses layanan kami?',
                'jenis_jawaban'   => 'skala',
                'urutan'          => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            if ($pertanyaanId1_1) {
                $opsiJawabanModel->insertBatch([
                    ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Sangat Sulit', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Sulit', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Cukup Mudah', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Mudah', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Sangat Mudah', 'nilai' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ]);
            }

            // Pertanyaan 1.2: Kecepatan Respon Petugas
            $pertanyaanId1_2 = $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerId1,
                'teks_pertanyaan' => 'Bagaimana kecepatan respon petugas dalam melayani kebutuhan Anda?',
                'jenis_jawaban'   => 'skala',
                'urutan'          => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            if ($pertanyaanId1_2) {
                $opsiJawabanModel->insertBatch([
                    ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Sangat Lambat', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Lambat', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Cukup Cepat', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Cepat', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Sangat Cepat', 'nilai' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ]);
            }

            // Pertanyaan 1.3: Saran/Masukan
            $pertanyaanId1_3 = $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerId1,
                'teks_pertanyaan' => 'Sebutkan saran atau masukan Anda untuk peningkatan layanan kami:',
                'jenis_jawaban'   => 'isian',
                'urutan'          => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Kuesioner 2: Fasilitas Umum (Tidak Aktif)
        $kuesionerModel->insert([
            'nama_kuesioner' => 'Kuesioner Fasilitas Umum',
            'deskripsi'      => 'Survei tingkat kepuasan terhadap ketersediaan dan kebersihan fasilitas umum.',
            'is_active'      => false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}