<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\JawabanPenggunaModel;
use App\Models\RespondentsModel;

class KuesionerSeeder extends Seeder
{
    public function run()
    {
        $kuesionerModel = new KuesionerModel();
        $pertanyaanModel = new PertanyaanModel();
        $opsiJawabanModel = new OpsiJawabanModel();
        $jawabanPenggunaModel = new JawabanPenggunaModel();
        $respondentsModel = new RespondentsModel();

        // Pastikan database bersih dari data kuesioner lama sebelum seeding
        $jawabanPenggunaModel->emptyTable();
        $opsiJawabanModel->emptyTable();
        $pertanyaanModel->emptyTable();
        $kuesionerModel->emptyTable();
        $respondentsModel->emptyTable();

        // --- Kuesioner Utama: Indeks Kepuasan Masyarakat ---
        $kuesionerIdIKM = $kuesionerModel->insert([
            'nama_kuesioner' => 'Indeks Kepuasan Masyarakat',
            'deskripsi'      => 'Aplikasi survei IKM untuk umpan balik respons kepuasan masyarakat terhadap layanan yang diberikan oleh Balai Karantina Pertanian Kelas II Palangkaraya.',
            'is_active'      => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($kuesionerIdIKM) {
            // --- Bagian Pendahuluan ---
            $pertanyaanModel->insert([
                'kuesioner_id'    => $kuesionerIdIKM,
                'teks_pertanyaan' => 'Pendahuluan: Assalamu alaikum Warahmatullahi Wabarakatuh (Pembukaan)',
                'jenis_jawaban'   => 'isian',
                'urutan' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // --- Pertanyaan 1-14 dari Screenshot (sesuai urutan gambar) ---

            // Q1: Umur? (dari 2.jpg)
            $q1_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Umur?', 'jenis_jawaban' => 'pilihan_ganda', 'urutan' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q1_id, 'opsi_teks' => '21-30 tahun', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q1_id, 'opsi_teks' => '31-40 tahun', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q1_id, 'opsi_teks' => '41-50 tahun', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q1_id, 'opsi_teks' => '51-60 tahun', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q2: Jenis Kelamin? (dari 2.jpg)
            $q2_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Jenis Kelamin?', 'jenis_jawaban' => 'pilihan_ganda', 'urutan' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q2_id, 'opsi_teks' => 'Laki-laki', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q2_id, 'opsi_teks' => 'Perempuan', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q3: Pendidikan Terakhir? (dari 3.jpg)
            $q3_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Pendidikan Terakhir?', 'jenis_jawaban' => 'pilihan_ganda', 'urutan' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q3_id, 'opsi_teks' => 'SD-SLTA', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q3_id, 'opsi_teks' => 'D1-S1', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q3_id, 'opsi_teks' => 'S2-S3', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q4: Pekerjaan Utama? (dari 3.jpg)
            $q4_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Pekerjaan Utama?', 'jenis_jawaban' => 'pilihan_ganda', 'urutan' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q4_id, 'opsi_teks' => 'Wirausaha', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q4_id, 'opsi_teks' => 'ASN/PNS/TNI/POLRI', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q4_id, 'opsi_teks' => 'Karyawan Swasta', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q4_id, 'opsi_teks' => 'Pelajar/Mahasiswa', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q5: Jenis Layanan Yang Diterima? (dari 4.jpg)
            $q5_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Jenis Layanan Yang Diterima?', 'jenis_jawaban' => 'pilihan_ganda', 'urutan' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q5_id, 'opsi_teks' => 'Ekspor', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q5_id, 'opsi_teks' => 'Impor', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q5_id, 'opsi_teks' => 'Antar Area', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q5_id, 'opsi_teks' => 'Administrasi', 'nilai' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q6: Bagaimana pendapat Anda tentang kesesuaian persyaratan? (dari 5.jpg)
            $q6_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang kesesuaian persyaratan yang harus dipenuhi dengan jenis pelayanan yang Anda dapatkan?', 'jenis_jawaban' => 'skala', 'urutan' => 6, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q6_id, 'opsi_teks' => 'Tidak Sesuai', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q6_id, 'opsi_teks' => 'Cukup Sesuai', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q6_id, 'opsi_teks' => 'Sesuai', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q6_id, 'opsi_teks' => 'Sangat Sesuai', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q7: Bagaimana prosedur yang anda lalui? (dari 5.jpg)
            $q7_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana prosedur yang anda lalui untuk mendapatkan pelayanan?', 'jenis_jawaban' => 'skala', 'urutan' => 7, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q7_id, 'opsi_teks' => 'Sangat Sulit', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q7_id, 'opsi_teks' => 'Sulit', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q7_id, 'opsi_teks' => 'Cukup Mudah', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q7_id, 'opsi_teks' => 'Mudah', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q7_id, 'opsi_teks' => 'Sangat Mudah', 'nilai' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q8: Bagaimana pendapat Anda tentang kemampuan waktu? (dari 6.jpg)
            $q8_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang kemampuan waktu dalam pemberian pelayanan?', 'jenis_jawaban' => 'skala', 'urutan' => 8, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q8_id, 'opsi_teks' => 'Sangat Kurang', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q8_id, 'opsi_teks' => 'Cukup', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q8_id, 'opsi_teks' => 'Baik', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q8_id, 'opsi_teks' => 'Baik Sekali', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q9: Bagaimana pendapat Anda tentang kesesuaian biaya? (dari 6.jpg)
            $q9_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang kesesuaian biaya yang dibayarkan untuk jenis pelayanan yang anda terima?', 'jenis_jawaban' => 'skala', 'urutan' => 9, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q9_id, 'opsi_teks' => 'Tidak Wajar', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q9_id, 'opsi_teks' => 'Wajar', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q9_id, 'opsi_teks' => 'Sesuai Aturan', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q9_id, 'opsi_teks' => 'Sangat Sesuai Aturan', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q10: Bagaimana pendapat Anda tentang kesesuaian pelayanan dengan standar? (dari 7.jpg)
            $q10_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang kesesuaian pelayanan yang diberikan dengan standar pelayanan dengan hasil atau produk yang Anda terima?', 'jenis_jawaban' => 'skala', 'urutan' => 10, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q10_id, 'opsi_teks' => 'Sangat Kurang', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q10_id, 'opsi_teks' => 'Cukup', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q10_id, 'opsi_teks' => 'Baik', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q10_id, 'opsi_teks' => 'Baik Sekali', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q11: Bagaimana pendapat Anda tentang kemampuan/kompetensi petugas? (dari 7.jpg)
            $q11_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang kemampuan/kompetensi petugas dalam memberikan pelayanan?', 'jenis_jawaban' => 'skala', 'urutan' => 11, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q11_id, 'opsi_teks' => 'Tidak Kompeten', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q11_id, 'opsi_teks' => 'Cukup Kompeten', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q11_id, 'opsi_teks' => 'Kompeten', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q11_id, 'opsi_teks' => 'Sangat Kompeten', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q12: Bagaimana pendapat Anda tentang kesopanan dan keramahan petugas? (dari 8.jpg)
            $q12_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang kesopanan dan keramahan petugas pada saat memberikan pelayanan kepada Saudara?', 'jenis_jawaban' => 'skala', 'urutan' => 12, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q12_id, 'opsi_teks' => 'Tidak Sopan', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q12_id, 'opsi_teks' => 'Cukup', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q12_id, 'opsi_teks' => 'Sopan', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q12_id, 'opsi_teks' => 'Sangat Sopan', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q13: Bagaimana pendapat Anda tentang penanganan pengaduan? (dari 8.jpg)
            $q13_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang penanganan pengaduan pengguna layanan?', 'jenis_jawaban' => 'skala', 'urutan' => 13, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q13_id, 'opsi_teks' => 'Sangat Buruk', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q13_id, 'opsi_teks' => 'Cukup', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q13_id, 'opsi_teks' => 'Baik', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q13_id, 'opsi_teks' => 'Sangat Baik', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Q14: Bagaimana pendapat Anda tentang lokasi, sarana dan prasarana? (dari 9.jpg)
            $q14_id = $pertanyaanModel->insert(['kuesioner_id' => $kuesionerIdIKM, 'teks_pertanyaan' => 'Bagaimana pendapat Anda tentang lokasi, sarana dan prasarana serta pelayanan?', 'jenis_jawaban' => 'skala', 'urutan' => 14, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $opsiJawabanModel->insertBatch([
                ['pertanyaan_id' => $q14_id, 'opsi_teks' => 'Sangat Kurang', 'nilai' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q14_id, 'opsi_teks' => 'Cukup', 'nilai' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q14_id, 'opsi_teks' => 'Baik', 'nilai' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['pertanyaan_id' => $q14_id, 'opsi_teks' => 'Baik Sekali', 'nilai' => 4, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);

            // Generate Dummy Responses untuk mengisi tabel jawaban_pengguna dan respondents
            $this->generateDummyResponses($kuesionerIdIKM, $pertanyaanModel, $opsiJawabanModel, $jawabanPenggunaModel, $respondentsModel);
        }
    }

    // Fungsi helper untuk generate dummy responses
    private function generateDummyResponses($kuesionerId, $pertanyaanModel, $opsiJawabanModel, $jawabanPenggunaModel, $respondentsModel)
    {
        $allQuestions = $pertanyaanModel->where('kuesioner_id', $kuesionerId)->orderBy('urutan', 'ASC')->findAll();

        // Ambil ID opsi untuk pertanyaan-pertanyaan yang butuh skor dan demografi
        $opsiMap = []; // [pertanyaan_id => [nilai/teks => opsi_id]]
        $qDemografiIds = []; // Q1-Q5 (Urutan pertanyaan demografi)
        foreach ($allQuestions as $q) {
            $opsi = $opsiJawabanModel->where('pertanyaan_id', $q['id'])->findAll();
            foreach ($opsi as $o) {
                if ($q['jenis_jawaban'] === 'skala' && $o['nilai'] !== null) {
                    $opsiMap[$q['id']]['skala'][$o['nilai']] = $o['id'];
                } else { // Pilihan ganda
                    $opsiMap[$q['id']]['pilihan_ganda'][] = $o['id'];
                }
            }
            if ($q['urutan'] >= 1 && $q['urutan'] <= 5) {
                $qDemografiIds[$q['urutan']] = $q['id'];
            }
        }

        $ips = ['192.168.1.100', '192.168.1.101', '192.168.1.102', '192.168.1.103', '192.168.1.104'];
        $responseTimes = ['-5 days', '-4 days', '-3 days', '-2 days', '-1 day', 'now'];
        $respondentNames = ['Responden A', 'Responden B', 'Responden C', 'Responden D', 'Responden E'];
        $teamNames = ['Tim A', 'Tim B', 'Tim C'];

        // Generate 5 dummy respondents
        foreach ($ips as $index => $ip) {
            $responseSessionId = uniqid('resp_'); // Unique session ID for multi-page
            $timestamp = date('Y-m-d H:i:s', strtotime($responseTimes[$index % count($responseTimes)]));
            $batchAnswers = [];

            // --- Simpan data responden ke tabel 'respondents' ---
            $respondentData = [
                'response_session_id' => $responseSessionId,
                'ip_address'          => $ip,
                'submission_timestamp' => $timestamp,
                'created_at'          => $timestamp,
                'updated_at'          => $timestamp,
                // Ambil ID opsi demografi dari map
                'age_group_id'        => $opsiMap[$qDemografiIds[1]]['pilihan_ganda'][mt_rand(0, count($opsiMap[$qDemografiIds[1]]['pilihan_ganda']) - 1)] ?? null,
                'gender_id'           => $opsiMap[$qDemografiIds[2]]['pilihan_ganda'][mt_rand(0, count($opsiMap[$qDemografiIds[2]]['pilihan_ganda']) - 1)] ?? null,
                'education_id'        => $opsiMap[$qDemografiIds[3]]['pilihan_ganda'][mt_rand(0, count($opsiMap[$qDemografiIds[3]]['pilihan_ganda']) - 1)] ?? null,
                'occupation_id'       => $opsiMap[$qDemografiIds[4]]['pilihan_ganda'][mt_rand(0, count($opsiMap[$qDemografiIds[4]]['pilihan_ganda']) - 1)] ?? null,
                'service_type_id'     => $opsiMap[$qDemografiIds[5]]['pilihan_ganda'][mt_rand(0, count($opsiMap[$qDemografiIds[5]]['pilihan_ganda']) - 1)] ?? null,
                'respondent_name'     => $respondentNames[$index % count($respondentNames)],
                'team_name'           => $teamNames[$index % count($teamNames)],
            ];
            $respondentsModel->insert($respondentData);

            // --- Simpan Jawaban Kuesioner ke tabel 'jawaban_pengguna' ---
            foreach ($allQuestions as $q) {
                if ($q['urutan'] === 0) continue; // Skip welcome message

                $jawaban = [
                    'kuesioner_id'      => $kuesionerId,
                    'pertanyaan_id'     => $q['id'],
                    'ip_address'        => $ip,
                    'response_session_id' => $responseSessionId,
                    'timestamp_isi'     => $timestamp,
                ];

                if ($q['jenis_jawaban'] === 'skala') {
                    $randomScore = mt_rand(1, 4); // Skala 1-4 (sesuai opsi, Q6, Q8, Q9, Q10, Q11, Q12, Q13, Q14)
                    $jawaban['opsi_jawaban_id'] = $opsiMap[$q['id']]['skala'][$randomScore] ?? null;
                    $jawaban['jawaban_teks'] = null;
                } elseif ($q['jenis_jawaban'] === 'pilihan_ganda') {
                    $opsiIds = $opsiMap[$q['id']]['pilihan_ganda'];
                    $randomOpsiId = $opsiIds[array_rand($opsiIds)];
                    $jawaban['opsi_jawaban_id'] = $randomOpsiId;
                    $jawaban['jawaban_teks'] = null;
                } elseif ($q['jenis_jawaban'] === 'isian') {
                    $jawaban['opsi_jawaban_id'] = null;
                    $jawaban['jawaban_teks'] = "Saran dari " . $ip . " untuk pertanyaan " . $q['urutan'] . ".";
                }
                $batchAnswers[] = $jawaban;
            }
            if (!empty($batchAnswers)) {
                $jawabanPenggunaModel->insertBatch($batchAnswers);
            }
        }
    }
}
