<?php
namespace App\Controllers;
use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\UserModel;
use App\Models\JawabanPenggunaModel;

class AdminController extends BaseController
{
    protected $kuesionerModel;
    protected $pertanyaanModel;
    protected $opsiJawabanModel;
    protected $userModel;
    protected $jawabanPenggunaModel;

    public function __construct()
    {
        $this->kuesionerModel = new KuesionerModel();
        $this->pertanyaanModel = new PertanyaanModel();
        $this->opsiJawabanModel = new OpsiJawabanModel();
        $this->userModel = new UserModel();
        $this->jawabanPenggunaModel = new JawabanPenggunaModel();
        helper(['form', 'url', 'session']);
    }

    public function dashboard() {
        $data['totalKuesioner'] = $this->kuesionerModel->countAllResults();
        $data['activeKuesioner'] = $this->kuesionerModel->where('is_active', 1)->countAllResults();
        
        // Menghitung total responden unik berdasarkan IP address
        $data['totalResponden'] = $this->jawabanPenggunaModel
                                     ->select('COUNT(DISTINCT ip_address) as total_ips')
                                     ->get()
                                     ->getRow('total_ips');

        // Perhitungan IKM Rata-rata
        // Rumus IKM sederhana: (Total Nilai Jawaban Opsi / Total Jawaban Opsi)
        $avgScoreResult = $this->jawabanPenggunaModel
                                 ->select('AVG(opsi_jawaban.nilai) as average_score')
                                 ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                                 ->where('opsi_jawaban.nilai IS NOT NULL') // Hanya jawaban yang memiliki nilai (skala)
                                 ->first();

        $data['ikmAverage'] = $avgScoreResult['average_score'] ? round($avgScoreResult['average_score'], 2) : 0;
        if ($data['ikmAverage'] > 5.0) $data['ikmAverage'] = 5.0; // Batasi maksimal 5

        $data['recentKuesioner'] = $this->kuesionerModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        return view('admin/dashboard', $data);
    }

    // --- Kuesioner Management ---
    public function kuesioner() {
        $data['kuesioner'] = $this->kuesionerModel->findAll();
        return view('admin/kuesioner/index', $data);
    }
    public function createKuesioner() { return view('admin/kuesioner/create'); }
    public function storeKuesioner() {
        // --- VALIDASI DIHILANGKAN ---
        // $rules = [...]; if (!$this->validate($rules)) { ... }
        
        $data = [
            'nama_kuesioner' => $this->request->getPost('nama_kuesioner'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ];
        if ($this->kuesionerModel->save($data)) {
            return redirect()->to(base_url('admin/kuesioner'))->with('success', 'Kuesioner berhasil ditambahkan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kuesioner.');
        }
    }
    public function editKuesioner($id) {
        $data['kuesioner'] = $this->kuesionerModel->find($id);
        if (empty($data['kuesioner'])) { throw new \CodeIgniter\Exceptions\PageNotFoundException('Kuesioner tidak ditemukan.'); }
        return view('admin/kuesioner/edit', $data);
    }
    public function updateKuesioner($id) {
        // --- VALIDASI DIHILANGKAN ---
        // $rules = [...]; if (!$this->validate($rules)) { ... }

        $data = [
            'nama_kuesioner' => $this->request->getPost('nama_kuesioner'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ];
        if ($this->kuesionerModel->update($id, $data)) {
            return redirect()->to(base_url('admin/kuesioner'))->with('success', 'Kuesioner berhasil diperbarui!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kuesioner.');
        }
    }
    public function deleteKuesioner($id) {
        if ($this->kuesionerModel->delete($id)) {
            return redirect()->to(base_url('admin/kuesioner'))->with('success', 'Kuesioner berhasil dihapus! (Termasuk pertanyaan & jawaban terkait).');
        } else {
            return redirect()->to(base_url('admin/kuesioner'))->with('error', 'Gagal menghapus kuesioner.');
        }
    }

    // --- Pertanyaan Management ---
    public function pertanyaan($kuesionerId) {
        $data['kuesioner'] = $this->kuesionerModel->find($kuesionerId);
        if (empty($data['kuesioner'])) { throw new \CodeIgniter\Exceptions\PageNotFoundException('Kuesioner tidak ditemukan.'); }
        $data['pertanyaan'] = $this->pertanyaanModel->getPertanyaanWithOpsi($kuesionerId);
        return view('admin/pertanyaan/index', $data);
    }
    public function createPertanyaan($kuesionerId) {
        $data['kuesioner'] = $this->kuesionerModel->find($kuesionerId);
        if (empty($data['kuesioner'])) { throw new \CodeIgniter\Exceptions\PageNotFoundException('Kuesioner tidak ditemukan.'); }
        return view('admin/pertanyaan/create', $data);
    }
    public function storePertanyaan() {
        $kuesionerId = $this->request->getPost('kuesioner_id');
        // --- VALIDASI DIHILANGKAN ---
        /*
        $rules = [ ... ]; if (!$this->validate($rules)) { ... }
        */

        $pertanyaanData = [
            'kuesioner_id'    => $kuesionerId,
            'teks_pertanyaan' => $this->request->getPost('teks_pertanyaan'),
            'jenis_jawaban'   => $this->request->getPost('jenis_jawaban'),
            'urutan'          => $this->request->getPost('urutan'),
        ];
        
        $pertanyaanId = $this->pertanyaanModel->insert($pertanyaanData); 

        if ($pertanyaanId) { // Ini akan dieksekusi hanya jika insert berhasil
            $jenisJawaban = $this->request->getPost('jenis_jawaban'); // Pastikan didefinisikan dari POST
            if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
                $opsiTeks = $this->request->getPost('opsi_teks');
                $opsiNilai = $this->request->getPost('opsi_nilai');
                $batchOpsi = [];
                if (!empty($opsiTeks)) {
                    foreach ($opsiTeks as $key => $teks) {
                        if (!empty($teks)) {
                            $batchOpsi[] = [
                                'pertanyaan_id' => $pertanyaanId, 
                                'opsi_teks'     => trim($teks),
                                'nilai'         => ($jenisJawaban === 'skala' && isset($opsiNilai[$key])) ? (int)$opsiNilai[$key] : null,
                            ];
                        }
                    }
                    if (!empty($batchOpsi)) { $this->opsiJawabanModel->insertBatch($batchOpsi); }
                }
            }
            return redirect()->to(base_url('admin/pertanyaan/' . $kuesionerId))->with('success', 'Pertanyaan berhasil ditambahkan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pertanyaan.');
        }
    }
    public function editPertanyaan($id) {
        $data['pertanyaan'] = $this->pertanyaanModel->find($id);
        if (empty($data['pertanyaan'])) { throw new \CodeIgniter\Exceptions\PageNotFoundException('Pertanyaan tidak ditemukan.'); }
        $data['kuesioner'] = $this->kuesionerModel->find($data['pertanyaan']['kuesioner_id']);
        $data['opsiJawaban'] = $this->opsiJawabanModel->where('pertanyaan_id', $id)->findAll();
        return view('admin/pertanyaan/edit', $data);
    }
    public function updatePertanyaan($id) {
        $pertanyaan = $this->pertanyaanModel->find($id);
        if (empty($pertanyaan)) { throw new \CodeIgniter\Exceptions\PageNotFoundException('Pertanyaan tidak ditemukan.'); }

        // --- VALIDASI DIHILANGKAN ---
        
        $pertanyaanData = [
            'teks_pertanyaan' => $this->request->getPost('teks_pertanyaan'),
            'jenis_jawaban'   => $this->request->getPost('jenis_jawaban'),
            'urutan'          => $this->request->getPost('urutan'),
        ];
        if ($this->pertanyaanModel->update($id, $pertanyaanData)) {
            $jenisJawaban = $this->request->getPost('jenis_jawaban'); // Pastikan didefinisikan di sini
            $this->opsiJawabanModel->where('pertanyaan_id', $id)->delete();

            if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
                $opsiTeks = $this->request->getPost('opsi_teks');
                $opsiNilai = $this->request->getPost('opsi_nilai');
                $batchOpsi = [];
                if (!empty($opsiTeks)) {
                    foreach ($opsiTeks as $key => $teks) {
                        if (!empty($teks)) {
                            $batchOpsi[] = [
                                'pertanyaan_id' => $id,
                                'opsi_teks'     => trim($teks),
                                'nilai'         => ($jenisJawaban === 'skala' && isset($opsiNilai[$key])) ? (int)$opsiNilai[$key] : null,
                            ];
                        }
                    }
                    if (!empty($batchOpsi)) { $this->opsiJawabanModel->insertBatch($batchOpsi); }
                }
            }
            return redirect()->to(base_url('admin/pertanyaan/' . $pertanyaan['kuesioner_id']))->with('success', 'Pertanyaan berhasil diperbarui!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pertanyaan.');
        }
    }
    public function deletePertanyaan($id) {
        $pertanyaan = $this->pertanyaanModel->find($id);
        if (empty($pertanyaan)) { return redirect()->back()->with('error', 'Pertanyaan tidak ditemukan.'); }
        if ($this->pertanyaanModel->delete($id)) {
            return redirect()->to(base_url('admin/pertanyaan/' . $pertanyaan['kuesioner_id']))->with('success', 'Pertanyaan berhasil dihapus! (Termasuk opsi & jawaban terkait).');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus pertanyaan.');
        }
    }

    // --- Admin Profile Management ---
    public function profile() {
        $data['user'] = $this->userModel->find(session()->get('user_id'));
        if (empty($data['user'])) { throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan.'); }
        return view('admin/profile/index', $data);
    }
    public function updateProfile() {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        // --- VALIDASI DIHILANGKAN ---
        $dataToUpdate = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ];
        if ($this->request->getPost('new_password')) {
            $dataToUpdate['password'] = $this->request->getPost('new_password'); // Akan di-hash oleh model's beforeUpdate
        }
        if ($this->userModel->update($userId, $dataToUpdate)) {
            session()->set([
                'username' => $dataToUpdate['username'],
                'email'    => $dataToUpdate['email']
            ]);
            return redirect()->to(base_url('admin/profile'))->with('success', 'Profil berhasil diperbarui!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil.');
        }
    }

    // Aturan validasi kustom check_old_password (tetap ada karena model memanggilnya, namun tidak terpicu jika validasi dinonaktifkan di atas)
    public function check_old_password(string $inputPassword, string $field, array $data, ?string &$error = null): bool
    {
        // Ini hanya akan terpicu jika ada aturan validasi yang memanggilnya,
        // yang saat ini dinonaktifkan di updateProfile.
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        if (!$user) {
            $error = 'Pengguna tidak ditemukan.';
            return false;
        }
        if (!password_verify($inputPassword, $user['password'])) {
            $error = 'Password lama salah.';
            return false;
        }
        return true;
    }


    // --- Hasil IKM ---
    public function hasil() {
        // Logika untuk mengambil data hasil IKM dari database
        // Data ini akan digunakan untuk perhitungan dan tampilan grafik
        $kuesionerList = $this->kuesionerModel->findAll();
        
        $data['kuesionerList'] = $kuesionerList; // Untuk dropdown filter

        $totalRespondenHasil = $this->jawabanPenggunaModel->select('COUNT(DISTINCT ip_address) as total_ips')->get()->getRow('total_ips');
        $data['totalRespondenHasil'] = $totalRespondenHasil;

        $avgIkmResult = $this->jawabanPenggunaModel
                             ->select('AVG(opsi_jawaban.nilai) as average_score')
                             ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                             ->where('opsi_jawaban.nilai IS NOT NULL')
                             ->first();
        $ikmRataRataHasil = round($avgIkmResult['average_score'] ?? 0, 2);
        $data['ikmRataRataHasil'] = $ikmRataRataHasil;

        // Perhitungan persentase puas (contoh: nilai 4 atau 5 dianggap puas dari skala 1-5)
        $totalPuas = $this->jawabanPenggunaModel
                          ->select('COUNT(jawaban_pengguna.id) as count_puas')
                          ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
                          ->where('opsi_jawaban.nilai >=', 4) // Asumsi nilai 4 atau 5 = puas
                          ->countAllResults();
        $totalSemuaJawabanSkala = $this->jawabanPenggunaModel
                                     ->select('COUNT(jawaban_pengguna.id) as count_all_skala')
                                     ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id') 
                                     ->countAllResults();
        
        $data['persentasePuasHasil'] = ($totalSemuaJawabanSkala > 0) ? round(($totalPuas / $totalSemuaJawabanSkala) * 100, 2) : 0;

        // Detail hasil per pertanyaan (untuk kuesioner pertama yang aktif, atau yang dipilih)
        $activeKuesioner = $this->kuesionerModel->where('is_active', 1)->first(); // Ambil kuesioner aktif pertama
        $data['detailHasilPertanyaan'] = [];
        if ($activeKuesioner) {
            $pertanyaanList = $this->pertanyaanModel->getPertanyaanWithOpsi($activeKuesioner['id']);
            foreach ($pertanyaanList as $pertanyaan) {
                $detail = [
                    'teks_pertanyaan' => $pertanyaan['teks_pertanyaan'],
                    'jenis_jawaban'   => $pertanyaan['jenis_jawaban'],
                    'statistik'       => [],
                    'saran'           => []
                ];

                if ($pertanyaan['jenis_jawaban'] !== 'isian') {
                    $totalJawabanPertanyaan = $this->jawabanPenggunaModel->where('pertanyaan_id', $pertanyaan['id'])->countAllResults();
                    foreach ($pertanyaan['opsi'] as $opsi) {
                        $count = $this->jawabanPenggunaModel->where('opsi_jawaban_id', $opsi['id'])->countAllResults();
                        $percentage = ($totalJawabanPertanyaan > 0) ? round(($count / $totalJawabanPertanyaan) * 100, 2) : 0;
                        $detail['statistik'][] = [
                            'opsi_teks' => $opsi['opsi_teks'],
                            'count'     => $count,
                            'percentage'=> $percentage
                        ];
                    }
                    if ($pertanyaan['jenis_jawaban'] === 'skala') {
                        $avgNilai = $this->jawabanPenggunaModel
                                         ->select('AVG(opsi_jawaban.nilai) as avg_score')
                                         ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                                         ->where('jawaban_pengguna.pertanyaan_id', $pertanyaan['id'])
                                         ->where('opsi_jawaban.nilai IS NOT NULL')
                                         ->first();
                        $detail['statistik']['rata_rata_nilai'] = round($avgNilai['avg_score'] ?? 0, 2);
                    }
                } else {
                    $saran = $this->jawabanPenggunaModel->where('pertanyaan_id', $pertanyaan['id'])->where('jawaban_teks IS NOT NULL')->findAll();
                    $detail['saran'] = array_map(function($j){ return ['teks' => $j['jawaban_teks'], 'timestamp' => $j['timestamp_isi']]; }, $saran);
                }
                $data['detailHasilPertanyaan'][] = $detail;
            }
        }

        return view('admin/hasil/index', $data);
    }

    // --- Export CSV ---
    public function exportCsvHasil() {
        // Logika pengambilan data serupa dengan method hasil()
        $totalRespondenHasil = $this->jawabanPenggunaModel->select('COUNT(DISTINCT ip_address) as total_ips')->get()->getRow('total_ips');
        $avgIkmResult = $this->jawabanPenggunaModel
                             ->select('AVG(opsi_jawaban.nilai) as average_score')
                             ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                             ->where('opsi_jawaban.nilai IS NOT NULL')
                             ->first();
        $ikmRataRataHasil = round($avgIkmResult['average_score'] ?? 0, 2);
        
        $totalPuas = $this->jawabanPenggunaModel
                          ->select('COUNT(jawaban_pengguna.id) as count_puas')
                          ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
                          ->where('opsi_jawaban.nilai >=', 4)
                          ->countAllResults();
        $totalSemuaJawabanSkala = $this->jawabanPenggunaModel
                                     ->select('COUNT(jawaban_pengguna.id) as count_all_skala')
                                     ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id') 
                                     ->countAllResults();
        $persentasePuasHasil = ($totalSemuaJawabanSkala > 0) ? round(($totalPuas / $totalSemuaJawabanSkala) * 100, 2) : 0;
        
        $detailHasilPertanyaan = [];
        $activeKuesioner = $this->kuesionerModel->where('is_active', 1)->first();
        if ($activeKuesioner) {
            $pertanyaanList = $this->pertanyaanModel->getPertanyaanWithOpsi($activeKuesioner['id']);
            foreach ($pertanyaanList as $pertanyaan) {
                $detail = [
                    'teks_pertanyaan' => $pertanyaan['teks_pertanyaan'],
                    'jenis_jawaban'   => $pertanyaan['jenis_jawaban'],
                    'statistik'       => [],
                    'saran'           => []
                ];

                if ($pertanyaan['jenis_jawaban'] !== 'isian') {
                    $totalJawabanPertanyaan = $this->jawabanPenggunaModel->where('pertanyaan_id', $pertanyaan['id'])->countAllResults();
                    foreach ($pertanyaan['opsi'] as $opsi) {
                        $count = $this->jawabanPenggunaModel->where('opsi_jawaban_id', $opsi['id'])->countAllResults();
                        $percentage = ($totalJawabanPertanyaan > 0) ? round(($count / $totalJawabanPertanyaan) * 100, 2) : 0;
                        $detail['statistik'][] = [
                            'opsi_teks' => $opsi['opsi_teks'],
                            'count'     => $count,
                            'percentage'=> $percentage
                        ];
                    }
                    if ($pertanyaan['jenis_jawaban'] === 'skala') {
                        $avgNilai = $this->jawabanPenggunaModel
                                         ->select('AVG(opsi_jawaban.nilai) as avg_score')
                                         ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                                         ->where('jawaban_pengguna.pertanyaan_id', $pertanyaan['id'])
                                         ->where('opsi_jawaban.nilai IS NOT NULL')
                                         ->first();
                        $detail['statistik']['rata_rata_nilai'] = round($avgNilai['avg_score'] ?? 0, 2);
                    }
                } else {
                    $saran = $this->jawabanPenggunaModel->where('pertanyaan_id', $pertanyaan['id'])->where('jawaban_teks IS NOT NULL')->findAll();
                    $detail['saran'] = array_map(function($j){ return ['teks' => $j['jawaban_teks'], 'timestamp' => $j['timestamp_isi']]; }, $saran);
                }
                $detailHasilPertanyaan[] = $detail;
            }
        }

        // --- Persiapan Data CSV ---
        $fileName = 'laporan_ikm_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Header CSV
        fputcsv($output, ['Laporan Indeks Kepuasan Masyarakat']);
        fputcsv($output, ['Tanggal Laporan: ' . date('d-m-Y H:i:s')]);
        fputcsv($output, []); // Baris kosong

        fputcsv($output, ['RINGKASAN UMUM']);
        fputcsv($output, ['Total Responden', 'Nilai IKM Rata-rata (/5.0)', 'Persentase Puas (%)']);
        fputcsv($output, [esc($totalRespondenHasil), number_format(esc($ikmRataRataHasil), 2), esc($persentasePuasHasil)]);
        fputcsv($output, []);

        fputcsv($output, ['DETAIL HASIL PER PERTANYAAN']);
        foreach ($detailHasilPertanyaan as $detail) {
            fputcsv($output, ['Pertanyaan:', esc($detail['teks_pertanyaan'])]);
            if ($detail['jenis_jawaban'] !== 'isian') {
                foreach ($detail['statistik'] as $stat) {
                    if (isset($stat['opsi_teks'])) {
                        fputcsv($output, [esc($stat['opsi_teks']), esc($stat['count']), esc($stat['percentage']) . '%']);
                    }
                }
                if (isset($detail['statistik']['rata_rata_nilai'])) {
                    fputcsv($output, ['Rata-rata Nilai', number_format(esc($detail['statistik']['rata_rata_nilai']), 2)]);
                }
            } else {
                fputcsv($output, ['Saran/Masukan:']);
                if (empty($detail['saran'])) {
                    fputcsv($output, ['- Tidak ada saran -']);
                } else {
                    foreach ($detail['saran'] as $saran) {
                        fputcsv($output, ['"', esc($saran['teks']), '"', esc($saran['timestamp'])]);
                    }
                }
            }
            fputcsv($output, []); // Baris kosong antar pertanyaan
        }

        fclose($output);
        exit(); 
    }
}