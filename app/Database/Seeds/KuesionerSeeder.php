<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\JawabanPenggunaModel; // Import JawabanPenggunaModel

class KuesionerSeeder extends Seeder
{
    public function run()
    {
        $kuesionerModel = new KuesionerModel();
        $pertanyaanModel = new PertanyaanModel();
        $opsiJawabanModel = new OpsiJawabanModel();
        $jawabanPenggunaModel = new JawabanPenggunaModel(); // Inisialisasi model jawaban

        // Kuesioner 1: Pelayanan Administrasi
        $kuesionerId1 = $kuesionerModel->insert([
            'nama_kuesioner' => 'Kuesioner Pelayanan Administrasi',
            'deskripsi'      => 'Survei kepuasan terhadap proses dan kecepatan pelayanan administrasi.',
            'is_active'      => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($kuesionerId1) {
            // Pertanyaan 1.1: Kemudahan Akses (Skala)
            $pertanyaanId1_1 = $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerId1,
                'teks_pertanyaan' => 'Bagaimana tingkat kemudahan dalam mengakses layanan kami?',
                'jenis_jawaban'   => 'skala',
                'urutan'          => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $opsi1_1 = [];
            if ($pertanyaanId1_1) {
                $opsi1_1[] = ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Sangat Sulit', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_1[] = ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Sulit', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_1[] = ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Cukup Mudah', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_1[] = ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Mudah', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_1[] = ['pertanyaan_id' => $pertanyaanId1_1, 'opsi_teks' => 'Sangat Mudah', 'nilai' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsiJawabanModel->insertBatch($opsi1_1);
            }

            // Pertanyaan 1.2: Kecepatan Respon Petugas (Skala)
            $pertanyaanId1_2 = $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerId1,
                'teks_pertanyaan' => 'Bagaimana kecepatan respon petugas dalam melayani kebutuhan Anda?',
                'jenis_jawaban'   => 'skala',
                'urutan'          => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $opsi1_2 = [];
            if ($pertanyaanId1_2) {
                $opsi1_2[] = ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Sangat Lambat', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_2[] = ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Lambat', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_2[] = ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Cukup Cepat', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_2[] = ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Cepat', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi1_2[] = ['pertanyaan_id' => $pertanyaanId1_2, 'opsi_teks' => 'Sangat Cepat', 'nilai' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsiJawabanModel->insertBatch($opsi1_2);
            }

            // Pertanyaan 1.3: Saran/Masukan (Isian)
            $pertanyaanId1_3 = $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerId1,
                'teks_pertanyaan' => 'Sebutkan saran atau masukan Anda untuk peningkatan layanan kami:',
                'jenis_jawaban'   => 'isian',
                'urutan'          => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // --- Dummy Jawaban Pengguna untuk Kuesioner 1 ---
            // Ambil ID opsi yang baru dibuat
            $opsi_q1_1_sangat_mudah_id = $opsiJawabanModel->where(['pertanyaan_id' => $pertanyaanId1_1, 'nilai' => 5])->first()['id'];
            $opsi_q1_1_mudah_id = $opsiJawabanModel->where(['pertanyaan_id' => $pertanyaanId1_1, 'nilai' => 4])->first()['id'];
            $opsi_q1_2_sangat_cepat_id = $opsiJawabanModel->where(['pertanyaan_id' => $pertanyaanId1_2, 'nilai' => 5])->first()['id'];
            $opsi_q1_2_cepat_id = $opsiJawabanModel->where(['pertanyaan_id' => $pertanyaanId1_2, 'nilai' => 4])->first()['id'];

            $dummyJawaban = [];
            // Responden 1
            $dummyJawaban[] = [
                'kuesioner_id' => $kuesionerId1, 'pertanyaan_id' => $pertanyaanId1_1, 'opsi_jawaban_id' => $opsi_q1_1_sangat_mudah_id, 'jawaban_teks' => null, 'ip_address' => '192.168.1.1', 'timestamp_isi' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ];
            $dummyJawaban[] = [
                'kuesioner_id' => $kuesionerId1, 'pertanyaan_id' => $pertanyaanId1_2, 'opsi_jawaban_id' => $opsi_q1_2_sangat_cepat_id, 'jawaban_teks' => null, 'ip_address' => '192.168.1.1', 'timestamp_isi' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ];
            $dummyJawaban[] = [
                'kuesioner_id' => $kuesionerId1, 'pertanyaan_id' => $pertanyaanId1_3, 'opsi_jawaban_id' => null, 'jawaban_teks' => 'Pelayanan sangat memuaskan, terus tingkatkan!', 'ip_address' => '192.168.1.1', 'timestamp_isi' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ];
            // Responden 2
            $dummyJawaban[] = [
                'kuesioner_id' => $kuesionerId1, 'pertanyaan_id' => $pertanyaanId1_1, 'opsi_jawaban_id' => $opsi_q1_1_mudah_id, 'jawaban_teks' => null, 'ip_address' => '192.168.1.2', 'timestamp_isi' => date('Y-m-d H:i:s', strtotime('-2 day'))
            ];
            $dummyJawaban[] = [
                'kuesioner_id' => $kuesionerId1, 'pertanyaan_id' => $pertanyaanId1_2, 'opsi_jawaban_id' => $opsi_q1_2_cepat_id, 'jawaban_teks' => null, 'ip_address' => '192.168.1.2', 'timestamp_isi' => date('Y-m-d H:i:s', strtotime('-2 day'))
            ];
            // Responden 3 (hanya beberapa jawaban)
            $dummyJawaban[] = [
                'kuesioner_id' => $kuesionerId1, 'pertanyaan_id' => $pertanyaanId1_1, 'opsi_jawaban_id' => $opsi_q1_1_sangat_mudah_id, 'jawaban_teks' => null, 'ip_address' => '192.168.1.3', 'timestamp_isi' => date('Y-m-d H:i:s', strtotime('-3 day'))
            ];

            $jawabanPenggunaModel->insertBatch($dummyJawaban);
        }

        // Kuesioner 2: Fasilitas Umum (Tidak Aktif)
        $kuesionerModel->insert([
            'nama_kuesioner' => 'Kuesioner Fasilitas Umum',
            'deskripsi'      => 'Survei tingkat kepuasan terhadap ketersediaan dan kebersihan fasilitas umum.',
            'is_active'      => false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Kuesioner 3: Kualitas Informasi (Aktif)
        $kuesionerId3 = $kuesionerModel->insert([
            'nama_kuesioner' => 'Kuesioner Kualitas Informasi',
            'deskripsi'      => 'Survei kepuasan terhadap kejelasan dan akurasi informasi yang diberikan.',
            'is_active'      => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if ($kuesionerId3) {
            $pertanyaanId3_1 = $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerId3,
                'teks_pertanyaan' => 'Seberapa jelas informasi yang kami berikan?',
                'jenis_jawaban'   => 'skala',
                'urutan'          => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $opsi3_1 = [];
            if ($pertanyaanId3_1) {
                $opsi3_1[] = ['pertanyaan_id' => $pertanyaanId3_1, 'opsi_teks' => 'Sangat Buruk', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi3_1[] = ['pertanyaan_id' => $pertanyaanId3_1, 'opsi_teks' => 'Buruk', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi3_1[] = ['pertanyaan_id' => $pertanyaanId3_1, 'opsi_teks' => 'Cukup Baik', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi3_1[] = ['pertanyaan_id' => $pertanyaanId3_1, 'opsi_teks' => 'Baik', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsi3_1[] = ['pertanyaan_id' => $pertanyaanId3_1, 'opsi_teks' => 'Sangat Baik', 'nilai' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                $opsiJawabanModel->insertBatch($opsi3_1);
            }
        }
    }
}