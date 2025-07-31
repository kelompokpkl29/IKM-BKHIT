<?php

namespace App\Controllers;

use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\UserModel;
use App\Models\JawabanPenggunaModel;
use App\Models\RespondentsModel;
use App\Models\IkmPdfDataModel;

class AdminController extends BaseController
{
    protected $kuesionerModel;
    protected $pertanyaanModel;
    protected $opsiJawabanModel;
    protected $userModel;
    protected $jawabanPenggunaModel;
    protected $respondentsModel;
    protected $ikmPdfDataModel;

    public function __construct()
    {
        $this->kuesionerModel = new KuesionerModel();
        $this->pertanyaanModel = new PertanyaanModel();
        $this->opsiJawabanModel = new OpsiJawabanModel();
        $this->userModel = new UserModel();
        $this->jawabanPenggunaModel = new JawabanPenggunaModel();
        $this->respondentsModel = new RespondentsModel();
        $this->ikmPdfDataModel = new IkmPdfDataModel();
        helper(['form', 'url', 'session']);
    }

    public function dashboard()
    {
        $data['totalKuesioner'] = $this->kuesionerModel->countAllResults();
        $data['activeKuesioner'] = $this->kuesionerModel->where('is_active', 1)->countAllResults();

        // Mengambil total responden dari tabel respondents
        $data['totalResponden'] = $this->respondentsModel->countAllResults();

        // Perhitungan IKM Rata-rata dari jawaban granular (hanya jenis 'skala' dan nilai tidak NULL)
        $totalJawabanSkalaDiberikan = $this->jawabanPenggunaModel
            ->join('pertanyaan', 'pertanyaan.id = jawaban_pengguna.pertanyaan_id')
            ->where('pertanyaan.jenis_jawaban', 'skala')
            ->where('jawaban_pengguna.opsi_jawaban_id IS NOT NULL') // Hanya hitung jawaban yang benar-benar ada opsi
            ->countAllResults();

        $totalNilaiJawabanSkala = $this->jawabanPenggunaModel
            ->select('SUM(opsi_jawaban.nilai) as sum_nilai')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
            ->join('pertanyaan', 'pertanyaan.id = jawaban_pengguna.pertanyaan_id')
            ->where('pertanyaan.jenis_jawaban', 'skala')
            ->where('opsi_jawaban.nilai IS NOT NULL')
            ->get()
            ->getRow('sum_nilai');

        $ikmRataRataHasil = 0;
        if ($totalJawabanSkalaDiberikan > 0) {
            $ikmRataRataHasil = ($totalNilaiJawabanSkala / $totalJawabanSkalaDiberikan);
        }
        $data['ikmAverage'] = round($ikmRataRataHasil, 2);
        if ($data['ikmAverage'] > 5.0) { // Asumsi skala maksimum 5
            $data['ikmAverage'] = 5.0;
        }

        // Perhitungan persentase puas (contoh: nilai 3 atau 4 dianggap puas dari skala 1-4/5)
        $totalPuas = $this->jawabanPenggunaModel
            ->select('COUNT(jawaban_pengguna.id) as count_puas')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
            ->where('opsi_jawaban.nilai >=', 3) // Misal, nilai 3 atau lebih dianggap puas
            ->countAllResults();
        $totalSemuaJawabanSkala = $this->jawabanPenggunaModel
            ->select('COUNT(jawaban_pengguna.id) as count_all_skala')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
            ->countAllResults();
        $data['persentasePuasHasil'] = ($totalSemuaJawabanSkala > 0) ? round(($totalPuas / $totalSemuaJawabanSkala) * 100, 2) : 0;

        $data['recentKuesioner'] = $this->kuesionerModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        return view('admin/dashboard', $data);
    }

    public function kuesioner()
    {
        $data['kuesioner'] = $this->kuesionerModel->findAll();
        return view('admin/kuesioner/index', $data);
    }
    public function createKuesioner()
    {
        return view('admin/kuesioner/create');
    }
    public function storeKuesioner()
    {
        // --- VALIDASI SEDERHANA ---
        $rules = [
            'nama_kuesioner' => 'required|min_length[3]|max_length[255]',
            'deskripsi'      => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        // --- AKHIR VALIDASI ---

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
    public function editKuesioner($id)
    {
        $data['kuesioner'] = $this->kuesionerModel->find($id);
        if (empty($data['kuesioner'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kuesioner tidak ditemukan.');
        }
        return view('admin/kuesioner/edit', $data);
    }
    public function updateKuesioner($id)
    {
        // --- VALIDASI SEDERHANA ---
        $rules = [
            'nama_kuesioner' => 'required|min_length[3]|max_length[255]',
            'deskripsi'      => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        // --- AKHIR VALIDASI ---

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
    public function deleteKuesioner($id)
    {
        if ($this->kuesionerModel->delete($id)) {
            return redirect()->to(base_url('admin/kuesioner'))->with('success', 'Kuesioner berhasil dihapus! (Termasuk pertanyaan & jawaban terkait).');
        } else {
            return redirect()->to(base_url('admin/kuesioner'))->with('error', 'Gagal menghapus kuesioner.');
        }
    }

    public function pertanyaan($kuesionerId)
    {
        $data['kuesioner'] = $this->kuesionerModel->find($kuesionerId);
        if (empty($data['kuesioner'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kuesioner tidak ditemukan.');
        }
        $data['pertanyaan'] = $this->pertanyaanModel->getPertanyaanWithOpsi($kuesionerId);
        return view('admin/pertanyaan/index', $data);
    }
    public function createPertanyaan($kuesionerId)
    {
        $data['kuesioner'] = $this->kuesionerModel->find($kuesionerId);
        if (empty($data['kuesioner'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kuesioner tidak ditemukan.');
        }
        return view('admin/pertanyaan/create', $data);
    }
    public function storePertanyaan()
    {
        // Ambil kuesioner_id dari POST request
        $kuesionerId = $this->request->getPost('kuesioner_id');

        // --- VALIDASI UNTUK PERTANYAAN ---
        $rules = [
            'kuesioner_id'    => 'required|is_natural_no_zero',
            'teks_pertanyaan' => 'required|min_length[5]',
            'jenis_jawaban'   => 'required|in_list[skala,pilihan_ganda,isian]',
            'urutan'          => 'required|is_natural_no_zero',
        ];
        $jenisJawaban = $this->request->getPost('jenis_jawaban');
        if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
            $rules['opsi_teks'] = 'required|array'; // Pastikan aturan 'array' dikenali
            $rules['opsi_teks.*'] = 'required|min_length[1]|max_length[255]';
            if ($jenisJawaban === 'skala') {
                $rules['opsi_nilai'] = 'required|array'; // Pastikan aturan 'array' dikenali
                $rules['opsi_nilai.*'] = 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]';
            }
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        // --- AKHIR VALIDASI ---

        $pertanyaanData = [
            'kuesioner_id'    => $kuesionerId,
            'teks_pertanyaan' => $this->request->getPost('teks_pertanyaan'),
            'jenis_jawaban'   => $this->request->getPost('jenis_jawaban'),
            'urutan'          => $this->request->getPost('urutan'),
        ];

        // Simpan pertanyaan baru
        $pertanyaanId = $this->pertanyaanModel->insert($pertanyaanData);

        if ($pertanyaanId) {
            // Jika jenis jawaban adalah skala atau pilihan ganda, simpan opsi jawaban
            if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
                $opsiTeks = $this->request->getPost('opsi_teks');
                $opsiNilai = $this->request->getPost('opsi_nilai'); // Ini akan ada jika jenisnya 'skala'
                $batchOpsi = [];

                if (!empty($opsiTeks)) {
                    foreach ($opsiTeks as $key => $teks) {
                        if (!empty(trim($teks))) { // Pastikan teks opsi tidak kosong setelah di-trim
                            $batchOpsi[] = [
                                'pertanyaan_id' => $pertanyaanId,
                                'opsi_teks'     => trim($teks),
                                // Pastikan nilai hanya ditambahkan jika jenisnya 'skala' dan ada nilainya
                                'nilai'         => ($jenisJawaban === 'skala' && isset($opsiNilai[$key])) ? (int)$opsiNilai[$key] : null,
                            ];
                        }
                    }
                    if (!empty($batchOpsi)) {
                        $this->opsiJawabanModel->insertBatch($batchOpsi);
                    }
                }
            }
            return redirect()->to(base_url('admin/pertanyaan/' . $kuesionerId))->with('success', 'Pertanyaan berhasil ditambahkan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pertanyaan.');
        }
    }
    public function editPertanyaan($id)
    {
        $data['pertanyaan'] = $this->pertanyaanModel->find($id);
        if (empty($data['pertanyaan'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pertanyaan tidak ditemukan.');
        }
        $data['kuesioner'] = $this->kuesionerModel->find($data['pertanyaan']['kuesioner_id']);
        $data['opsiJawaban'] = $this->opsiJawabanModel->where('pertanyaan_id', $id)->findAll();
        return view('admin/pertanyaan/edit', $data);
    }
    public function updatePertanyaan($id)
    {
        $pertanyaan = $this->pertanyaanModel->find($id);
        if (empty($pertanyaan)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pertanyaan tidak ditemukan.');
        }

        // --- VALIDASI UNTUK UPDATE PERTANYAAN ---
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
                $rules['opsi_nilai.*'] = 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]';
            }
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        // --- AKHIR VALIDASI ---

        $pertanyaanData = [
            'teks_pertanyaan' => $this->request->getPost('teks_pertanyaan'),
            'jenis_jawaban'   => $this->request->getPost('jenis_jawaban'),
            'urutan'          => $this->request->getPost('urutan'),
        ];
        if ($this->pertanyaanModel->update($id, $pertanyaanData)) {
            // Hapus opsi lama terlebih dahulu
            $this->opsiJawabanModel->where('pertanyaan_id', $id)->delete();

            // Kemudian sisipkan opsi baru jika jenisnya skala atau pilihan ganda
            if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
                $opsiTeks = $this->request->getPost('opsi_teks');
                $opsiNilai = $this->request->getPost('opsi_nilai');
                $batchOpsi = [];
                if (!empty($opsiTeks)) {
                    foreach ($opsiTeks as $key => $teks) {
                        if (!empty(trim($teks))) {
                            $batchOpsi[] = [
                                'pertanyaan_id' => $id,
                                'opsi_teks'     => trim($teks),
                                'nilai'         => ($jenisJawaban === 'skala' && isset($opsiNilai[$key])) ? (int)$opsiNilai[$key] : null,
                            ];
                        }
                    }
                    if (!empty($batchOpsi)) {
                        $this->opsiJawabanModel->insertBatch($batchOpsi);
                    }
                }
            }
            return redirect()->to(base_url('admin/pertanyaan/' . $pertanyaan['kuesioner_id']))->with('success', 'Pertanyaan berhasil diperbarui!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pertanyaan.');
        }
    }

    public function deletePertanyaan($id)
    {
        $pertanyaan = $this->pertanyaanModel->find($id);
        if (empty($pertanyaan)) {
            return redirect()->back()->with('error', 'Pertanyaan tidak ditemukan.');
        }
        if ($this->pertanyaanModel->delete($id)) {
            return redirect()->to(base_url('admin/pertanyaan/' . $pertanyaan['kuesioner_id']))->with('success', 'Pertanyaan berhasil dihapus! (Termasuk opsi & jawaban terkait).');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus pertanyaan.');
        }
    }

    // --- Admin Profile Management ---
    public function profile()
    {
        $data['user'] = $this->userModel->find(session()->get('user_id'));
        if (empty($data['user'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan.');
        }
        return view('admin/profile/index', $data);
    }
    public function updateProfile()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        // --- VALIDASI UNTUK UPDATE PROFIL ---
        $rules = [
            'username' => 'required|min_length[3]|max_length[255]',
            'email'    => 'required|valid_email|max_length[255]',
        ];
        // Validasi unik untuk username dan email jika diubah
        if ($user && $this->request->getPost('username') !== $user['username']) {
            $rules['username'] .= '|is_unique[users.username,id,' . $userId . ']';
        }
        if ($user && $this->request->getPost('email') !== $user['email']) {
            $rules['email'] .= '|is_unique[users.email,id,' . $userId . ']';
        }

        // Jika ada input password lama/baru, tambahkan aturan validasi password
        if ($this->request->getPost('old_password') || $this->request->getPost('new_password')) {
            $rules['old_password'] = 'required|check_old_password[old_password]'; // Menggunakan aturan kustom
            $rules['new_password'] = 'required|min_length[6]|matches[confirm_new_password]';
            $rules['confirm_new_password'] = 'required';
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        // --- AKHIR VALIDASI ---

        $dataToUpdate = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ];
        if ($this->request->getPost('new_password')) {
            $dataToUpdate['password'] = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        }
        if ($this->userModel->update($userId, $dataToUpdate)) {
            // Perbarui sesi dengan data terbaru jika username/email berubah
            session()->set([
                'username' => $dataToUpdate['username'],
                'email'    => $dataToUpdate['email']
            ]);
            return redirect()->to(base_url('admin/profile'))->with('success', 'Profil berhasil diperbarui!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil.');
        }
    }


    // --- Hasil IKM ---
    public function hasil()
    {
        $kuesionerList = $this->kuesionerModel->findAll();

        $data['kuesionerList'] = $kuesionerList; // Untuk dropdown filter

        // Mengambil total responden dari tabel respondents
        $data['totalRespondenHasil'] = $this->respondentsModel->countAllResults();

        // Perhitungan IKM Rata-rata dari jawaban granular
        // Mengambil semua jawaban dengan jenis 'skala' yang memiliki nilai
        $totalJawabanSkalaDiberikan = $this->jawabanPenggunaModel
            ->join('pertanyaan', 'pertanyaan.id = jawaban_pengguna.pertanyaan_id')
            ->where('pertanyaan.jenis_jawaban', 'skala')
            ->where('jawaban_pengguna.opsi_jawaban_id IS NOT NULL') // Hanya hitung jawaban yang benar-benar ada opsi
            ->countAllResults();

        // Menjumlahkan semua nilai dari jawaban 'skala'
        $totalNilaiJawabanSkala = $this->jawabanPenggunaModel
            ->select('SUM(opsi_jawaban.nilai) as sum_nilai')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
            ->join('pertanyaan', 'pertanyaan.id = jawaban_pengguna.pertanyaan_id')
            ->where('pertanyaan.jenis_jawaban', 'skala')
            ->where('opsi_jawaban.nilai IS NOT NULL')
            ->get()
            ->getRow('sum_nilai');

        $ikmRataRataHasil = 0;
        if ($totalJawabanSkalaDiberikan > 0) {
            $ikmRataRataHasil = ($totalNilaiJawabanSkala / $totalJawabanSkalaDiberikan);
        }
        $data['ikmAverage'] = round($ikmRataRataHasil, 2);
        if ($data['ikmAverage'] > 5.0) { // Asumsi skala maksimum 5
            $data['ikmAverage'] = 5.0;
        }

        // Perhitungan persentase puas (contoh: nilai 3 atau 4 dianggap puas dari skala 1-4/5)
        $totalPuas = $this->jawabanPenggunaModel
            ->select('COUNT(jawaban_pengguna.id) as count_puas')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
            ->where('opsi_jawaban.nilai >=', 3) // Misal, nilai 3 atau lebih dianggap puas
            ->countAllResults();
        $totalSemuaJawabanSkala = $this->jawabanPenggunaModel
            ->select('COUNT(jawaban_pengguna.id) as count_all_skala')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
            ->countAllResults();
        $data['persentasePuasHasil'] = ($totalSemuaJawabanSkala > 0) ? round(($totalPuas / $totalSemuaJawabanSkala) * 100, 2) : 0;

        // Detail hasil per pertanyaan
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
                            'percentage' => $percentage,
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
                    $detail['saran'] = array_map(function ($j) {
                        return ['teks' => $j['jawaban_teks'], 'timestamp' => $j['timestamp_isi']];
                    }, $saran);
                }
                $data['detailHasilPertanyaan'][] = $detail;
            }
        }

        // Data IKM PDF (jika ada)
        // Pastikan model IkmPdfDataModel sudah dibuat dan berfungsi
        $ikmPdfDataRaw = $this->ikmPdfDataModel->findAll();
        $data['ikmPdfData'] = $ikmPdfDataRaw; // Masukkan data IkmPdfDataModel ke view

        return view('admin/hasil/index', $data);
    }

    public function exportCsvHasil()
    {
        // Header untuk download CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=hasil_ikm_' . date('Y-m-d') . '.csv');

        // Buka output stream
        $output = fopen('php://output', 'w');

        // Tulis BOM untuk UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        // Header CSV
        fputcsv($output, [
            'Statistik',
            'Nilai'
        ]);

        // Hitung data statistik
        $totalRespondenHasil = $this->respondentsModel->countAllResults();

        $totalSkalaPertanyaan = $this->pertanyaanModel->where('jenis_jawaban', 'skala')->countAllResults();
        $totalJawabanSkalaDiberikan = $this->jawabanPenggunaModel
            ->join('pertanyaan', 'pertanyaan.id = jawaban_pengguna.pertanyaan_id')
            ->where('pertanyaan.jenis_jawaban', 'skala')
            ->countAllResults();

        $totalNilaiJawabanSkala = $this->jawabanPenggunaModel
            ->select('SUM(opsi_jawaban.nilai) as sum_nilai')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
            ->join('pertanyaan', 'pertanyaan.id = jawaban_pengguna.pertanyaan_id')
            ->where('pertanyaan.jenis_jawaban', 'skala')
            ->where('opsi_jawaban.nilai IS NOT NULL')
            ->get()
            ->getRow('sum_nilai');

        $ikmRataRataHasil = 0;
        if ($totalJawabanSkalaDiberikan > 0) {
            $ikmRataRataHasil = ($totalNilaiJawabanSkala / $totalJawabanSkalaDiberikan);
        }
        $ikmRataRataHasil = round($ikmRataRataHasil, 2);

        $totalPuas = $this->jawabanPenggunaModel
            ->select('COUNT(jawaban_pengguna.id) as count_puas')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
            ->where('opsi_jawaban.nilai >=', 3) // Misal, nilai 3 atau lebih dianggap puas
            ->countAllResults();
        $totalSemuaJawabanSkala = $this->jawabanPenggunaModel
            ->select('COUNT(jawaban_pengguna.id) as count_all_skala')
            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id')
            ->countAllResults(); // Menghitung total semua jawaban yang ada opsi nilai
        $persentasePuasHasil = ($totalSemuaJawabanSkala > 0) ? round(($totalPuas / $totalSemuaJawabanSkala) * 100, 2) : 0;

        // Tulis data statistik ke CSV
        fputcsv($output, ['Total Responden', $totalRespondenHasil]);
        fputcsv($output, ['Total Pertanyaan Skala', $totalSkalaPertanyaan]);
        fputcsv($output, ['Total Jawaban Skala', $totalJawabanSkalaDiberikan]);
        fputcsv($output, ['IKM Rata-rata', $ikmRataRataHasil]);
        fputcsv($output, ['Persentase Puas (%)', $persentasePuasHasil]);

        // Tambah baris kosong
        fputcsv($output, []);

        // Header detail pertanyaan
        fputcsv($output, ['Detail Per Pertanyaan', '']);
        fputcsv($output, ['Pertanyaan', 'Jenis', 'Opsi/Statistik', 'Jumlah', 'Persentase']);

        // Detail hasil per pertanyaan
        $activeKuesioner = $this->kuesionerModel->where('is_active', 1)->first();
        if ($activeKuesioner) {
            $pertanyaanList = $this->pertanyaanModel->getPertanyaanWithOpsi($activeKuesioner['id']);
            foreach ($pertanyaanList as $pertanyaan) {
                if ($pertanyaan['jenis_jawaban'] !== 'isian') {
                    $totalJawabanPertanyaan = $this->jawabanPenggunaModel->where('pertanyaan_id', $pertanyaan['id'])->countAllResults();
                    foreach ($pertanyaan['opsi'] as $opsi) {
                        $count = $this->jawabanPenggunaModel->where('opsi_jawaban_id', $opsi['id'])->countAllResults();
                        $percentage = ($totalJawabanPertanyaan > 0) ? round(($count / $totalJawabanPertanyaan) * 100, 2) : 0;
                        fputcsv($output, [
                            $pertanyaan['teks_pertanyaan'],
                            $pertanyaan['jenis_jawaban'],
                            $opsi['opsi_teks'],
                            $count,
                            $percentage . '%'
                        ]);
                    }
                    if ($pertanyaan['jenis_jawaban'] === 'skala') {
                        $avgNilai = $this->jawabanPenggunaModel
                            ->select('AVG(opsi_jawaban.nilai) as avg_score')
                            ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                            ->where('jawaban_pengguna.pertanyaan_id', $pertanyaan['id'])
                            ->where('opsi_jawaban.nilai IS NOT NULL')
                            ->first();
                        fputcsv($output, [
                            '',
                            '',
                            'Rata-rata Nilai',
                            round($avgNilai['avg_score'] ?? 0, 2),
                            ''
                        ]);
                    }
                } else {
                    // Untuk pertanyaan isian (saran)
                    $saran = $this->jawabanPenggunaModel
                        ->where('pertanyaan_id', $pertanyaan['id'])
                        ->where('jawaban_teks IS NOT NULL')
                        ->findAll();

                    fputcsv($output, [
                        $pertanyaan['teks_pertanyaan'],
                        $pertanyaan['jenis_jawaban'],
                        'Total Saran',
                        count($saran),
                        ''
                    ]);

                    // Tambahkan beberapa saran sebagai contoh (maksimal 10)
                    $maxSaran = min(10, count($saran));
                    for ($i = 0; $i < $maxSaran; $i++) {
                        fputcsv($output, [
                            '',
                            '',
                            'Saran ' . ($i + 1),
                            $saran[$i]['jawaban_teks'],
                            $saran[$i]['timestamp_isi']
                        ]);
                    }
                }

                // Baris kosong setelah setiap pertanyaan
                fputcsv($output, []);
            }
        }

        // Tutup output stream
        fclose($output);
        exit;
    }
}
