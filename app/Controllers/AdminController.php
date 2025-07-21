<?php
namespace App\Controllers;
use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\UserModel;
use App\Models\JawabanPenggunaModel;

require_once APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php';


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
        
        $data['totalResponden'] = $this->jawabanPenggunaModel
                                     ->select('COUNT(DISTINCT ip_address) as total_ips')
                                     ->get()
                                     ->getRow('total_ips');

        // Perhitungan IKM Rata-rata (menggunakan nilai dari opsi jawaban)
        // Rumus IKM sederhana: (Total Nilai Jawaban Opsi / Total Jawaban Opsi)
        $result = $this->jawabanPenggunaModel
                       ->select('SUM(opsi_jawaban.nilai) as total_nilai, COUNT(jawaban_pengguna.id) as total_jawaban')
                       ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                       ->where('opsi_jawaban.nilai IS NOT NULL') // Hanya jawaban yang memiliki nilai (skala)
                       ->first();

        $totalNilai = $result['total_nilai'] ?? 0;
        $totalJawabanValid = $result['total_jawaban'] ?? 0;
        
        $ikm = 0;
        if ($totalJawabanValid > 0) {
            // Asumsi skala 1-5, dan nilai maksimal per pertanyaan adalah 5
            // Rumus IKM: (Jumlah nilai jawaban x 1) / (jumlah responden x jumlah pertanyaan) x 100
            // Karena kita menghitung rata-rata per jawaban, ini bisa disederhanakan
            // Jika skala 1-5, IKM rata-rata adalah nilai rata-rata dari semua jawaban skala.
            $ikm = ($totalNilai / $totalJawabanValid); // Nilai rata-rata per item
            // Untuk mengubahnya menjadi skala 0-100%, jika IKM Anda menggunakan skala 1-5:
            // (Nilai Rata-rata / Nilai Skala Tertinggi) * 100
            // $ikm = ($ikm / 5) * 100;
        }

        $data['ikmAverage'] = round($ikm, 2); // Nilai IKM Rata-rata (misal: 3.92)


        $data['recentKuesioner'] = $this->kuesionerModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        return view('admin/dashboard', $data);
    }

    public function kuesioner() {
        $data['kuesioner'] = $this->kuesionerModel->findAll();
        return view('admin/kuesioner/index', $data);
    }
    public function createKuesioner() { return view('admin/kuesioner/create'); }
    public function storeKuesioner() {
        // Validasi dasar tetap diaktifkan untuk CRUD
        $rules = [
            'nama_kuesioner' => 'required|min_length[3]|max_length[255]',
            'deskripsi'      => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

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
        $rules = [
            'nama_kuesioner' => 'required|min_length[3]|max_length[255]',
            'deskripsi'      => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

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
        $rules = [
            'kuesioner_id'    => 'required|is_natural_no_zero',
            'teks_pertanyaan' => 'required|min_length[5]',
            'jenis_jawaban'   => 'required|in_list[skala,pilihan_ganda,isian]',
            'urutan'          => 'required|is_natural_no_zero',
        ];
        $jenisJawaban = $this->request->getPost('jenis_jawaban');
        if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
            $rules['opsi_teks'] = 'required|array';
            $rules['opsi_teks.*'] = 'required|min_length[1]|max_length[255]';
            if ($jenisJawaban === 'skala') {
                $rules['opsi_nilai'] = 'required|array';
                $rules['opsi_nilai.*'] = 'required|is_natural|less_than_equal_to[5]';
            }
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $pertanyaanData = [
            'kuesioner_id'    => $kuesionerId,
            'teks_pertanyaan' => $this->request->getPost('teks_pertanyaan'),
            'jenis_jawaban'   => $this->request->getPost('jenis_jawaban'),
            'urutan'          => $this->request->getPost('urutan'),
        ];
        
        $pertanyaanId = $this->pertanyaanModel->insert($pertanyaanData); 

        if ($pertanyaanId) {
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

        $rules = [
            'teks_pertanyaan' => 'required|min_length[5]',
            'jenis_jawaban'   => 'required|in_list[skala,pilihan_ganda,isian]',
            'urutan'          => 'required|is_natural_no_zero',
        ];
        $jenisJawaban = $this->request->getPost('jenis_jawaban');
        if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
            $rules['opsi_teks'] = 'required|array';
            $rules['opsi_teks.*'] = 'required|min_length[1]|max_length[255]';
            if ($jenisJawaban === 'skala') {
                $rules['opsi_nilai'] = 'required|array';
                $rules['opsi_nilai.*'] = 'required|is_natural|less_than_equal_to[5]';
            }
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $pertanyaanData = [
            'teks_pertanyaan' => $this->request->getPost('teks_pertanyaan'),
            'jenis_jawaban'   => $this->request->getPost('jenis_jawaban'),
            'urutan'          => $this->request->getPost('urutan'),
        ];
        if ($this->pertanyaanModel->update($id, $pertanyaanData)) {
            // Hapus semua opsi lama dan masukkan yang baru (sederhana untuk contoh ini)
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

        $rules = [
            'username' => 'required|min_length[3]|max_length[255]',
            'email'    => 'required|valid_email|max_length[255]',
        ];
        // Validasi unik untuk username dan email jika diubah
        if ($user && $this->request->getPost('username') !== $user['username']) { // Hanya cek unik jika username berubah
            $rules['username'] .= '|is_unique[users.username,id,' . $userId . ']';
        }
        if ($user && $this->request->getPost('email') !== $user['email']) { // Hanya cek unik jika email berubah
            $rules['email'] .= '|is_unique[users.email,id,' . $userId . ']';
        }

        // Jika ada input password lama/baru, tambahkan aturan validasi password
        if ($this->request->getPost('old_password') || $this->request->getPost('new_password')) {
            $rules['old_password'] = [
                'rules' => 'required|check_old_password', 
                'errors' => ['required' => 'Password lama wajib diisi untuk mengubah password.']
            ];
            $rules['new_password'] = 'required|min_length[6]|matches[confirm_new_password]';
            $rules['confirm_new_password'] = 'required';
        }

        if (!$this->validate($rules, [ 
            'old_password' => [
                'check_old_password' => 'Password lama salah.'
            ]
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

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

    // Aturan validasi kustom check_old_password (perlu di-enable di app/Config/Validation.php)
    public function check_old_password(string $inputPassword, string $field, array $data, ?string &$error = null): bool
    {
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

        // Contoh perhitungan ringkasan (ini bisa disempurnakan dengan filter tanggal/kuesioner)
        $totalRespondenHasil = $this->jawabanPenggunaModel->select('COUNT(DISTINCT ip_address) as total_ips')->get()->getRow('total_ips');
        $data['totalRespondenHasil'] = $totalRespondenHasil;

        $avgIkmResult = $this->jawabanPenggunaModel
                             ->select('AVG(opsi_jawaban.nilai) as average_score')
                             ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                             ->where('opsi_jawaban.nilai IS NOT NULL')
                             ->first();
        $data['ikmRataRataHasil'] = round($avgIkmResult['average_score'] ?? 0, 2);

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

        // Detail hasil per pertanyaan (contoh, untuk kuesioner pertama yang aktif)
        $activeKuesioner = $this->kuesionerModel->where('is_active', 1)->first();
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
                    // Rata-rata nilai untuk pertanyaan skala
                    if ($pertanyaan['jenis_jawaban'] === 'skala') {
                        $avgNilai = $this->jawabanPenggunaModel
                                         ->select('AVG(opsi_jawaban.nilai) as avg_score')
                                         ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                                         ->where('jawaban_pengguna.pertanyaan_id', $pertanyaan['id'])
                                         ->where('opsi_jawaban.nilai IS NOT NULL')
                                         ->first();
                        $detail['statistik']['rata_rata_nilai'] = round($avgNilai['avg_score'] ?? 0, 2);
                    }
                } else { // Isian
                    $saran = $this->jawabanPenggunaModel->where('pertanyaan_id', $pertanyaan['id'])->where('jawaban_teks IS NOT NULL')->findAll();
                    $detail['saran'] = array_map(function($j){ return ['teks' => $j['jawaban_teks'], 'timestamp' => $j['timestamp_isi']]; }, $saran);
                }
                $data['detailHasilPertanyaan'][] = $detail;
            }
        }

        return view('admin/hasil/index', $data);
    }

    // --- Export PDF ---
    public function exportPdfHasil() {
        // Ambil data yang akan diekspor (sama seperti di hasil() method, bisa difilter)
        $kuesionerList = $this->kuesionerModel->findAll();
        
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
        
        // Detail hasil per pertanyaan
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

        // Buat instance TCPDF
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Atur informasi dokumen
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Admin IKM');
        $pdf->SetTitle('Laporan Hasil IKM');
        $pdf->SetSubject('Laporan Indeks Kepuasan Masyarakat');
        $pdf->SetKeywords('IKM, Laporan, Kepuasan Masyarakat');

        // Hapus header/footer default
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margin
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetFont('helvetica', '', 10);

        // Tambah halaman baru
        $pdf->AddPage();

        // Konten HTML untuk PDF
        $html = '
        <h1 style="text-align: center; color: #007bff;">Laporan Hasil Indeks Kepuasan Masyarakat</h1>
        <h3 style="text-align: center;">Per ' . date('d-m-Y H:i:s') . '</h3>
        <br>
        <table border="0" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%;">
                    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                        <tr><td style="background-color: #f0f8ff;"><strong>Ringkasan Umum:</strong></td></tr>
                        <tr><td>Total Responden: <strong>' . esc($totalRespondenHasil) . '</strong></td></tr>
                        <tr><td>Nilai IKM Rata-rata: <strong>' . number_format(esc($ikmRataRataHasil), 2) . ' / 5.0</strong></td></tr>
                        <tr><td>Persentase Puas: <strong>' . esc($persentasePuasHasil) . '%</strong></td></tr>
                    </table>
                </td>
            </tr>
        </table>
        <br><br>
        ';
        
        $html .= '<h3 style="color: #007bff;">Detail Hasil Per Pertanyaan (Kuesioner Aktif):</h3>';
        if (empty($detailHasilPertanyaan)) {
            $html .= '<p>Tidak ada data pertanyaan untuk kuesioner aktif.</p>';
        } else {
            foreach ($detailHasilPertanyaan as $detail) {
                $html .= '<p style="font-weight: bold; margin-bottom: 5px;">' . esc($detail['teks_pertanyaan']) . '</p>';
                if ($detail['jenis_jawaban'] !== 'isian') {
                    $html .= '<ul style="list-style-type: none; padding: 0;">';
                    foreach ($detail['statistik'] as $stat) {
                        if (isset($stat['opsi_teks'])) { // Cek jika ini bukan rata-rata nilai
                            $html .= '<li>' . esc($stat['opsi_teks']) . ': ' . esc($stat['count']) . ' (' . esc($stat['percentage']) . '%)</li>';
                        }
                    }
                    if (isset($detail['statistik']['rata_rata_nilai'])) {
                        $html .= '<li style="font-weight: bold;">Rata-rata Nilai: ' . number_format(esc($detail['statistik']['rata_rata_nilai']), 2) . '</li>';
                    }
                    $html .= '</ul>';
                } else {
                    if (empty($detail['saran'])) {
                        $html .= '<p style="font-style: italic; color: #666;">Belum ada saran untuk pertanyaan ini.</p>';
                    } else {
                        $html .= '<ul style="list-style-type: square; padding-left: 20px;">';
                        foreach ($detail['saran'] as $saran) {
                            $html .= '<li>' . esc($saran['teks']) . ' <span style="font-size: 8pt; color: #888;">(' . esc($saran['timestamp']) . ')</span></li>';
                        }
                        $html .= '</ul>';
                    }
                }
                $html .= '<br>'; // Jarak antar pertanyaan
            }
        }
        
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $fileName = 'laporan_ikm_' . date('Ymd_His') . '.pdf';
        $pdf->Output($fileName, 'D'); // 'D' untuk download
    }
}