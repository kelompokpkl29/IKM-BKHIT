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
        
        $data['totalResponden'] = $this->jawabanPenggunaModel
                                     ->select('COUNT(DISTINCT ip_address) as total_ips')
                                     ->get()
                                     ->getRow('total_ips');

        $avgScoreResult = $this->jawabanPenggunaModel
                                 ->select('AVG(opsi_jawaban.nilai) as average_score')
                                 ->join('opsi_jawaban', 'opsi_jawaban.id = jawaban_pengguna.opsi_jawaban_id', 'left')
                                 ->where('opsi_jawaban.nilai IS NOT NULL')
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
        // Validasi dasar (bisa diperluas jika diperlukan)
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
        // Validasi dasar
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
        // Validasi dasar
        $rules = [
            'kuesioner_id'    => 'required|is_natural_no_zero',
            'teks_pertanyaan' => 'required|min_length[5]',
            'jenis_jawaban'   => 'required|in_list[skala,pilihan_ganda,isian]',
            'urutan'          => 'required|is_natural_no_zero',
        ];
        // Aturan validasi tambahan untuk opsi jawaban jika jenisnya skala atau pilihan ganda
        $jenisJawaban = $this->request->getPost('jenis_jawaban');
        if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
            $rules['opsi_teks'] = 'required|array'; // Pastikan array opsi teks tidak kosong
            $rules['opsi_teks.*'] = 'required|min_length[1]|max_length[255]'; // Setiap opsi teks wajib diisi
            if ($jenisJawaban === 'skala') {
                $rules['opsi_nilai'] = 'required|array';
                $rules['opsi_nilai.*'] = 'required|is_natural|less_than_equal_to[5]'; // Contoh: nilai 1-5
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

        if ($pertanyaanId) { // Check if insert was successful and ID obtained
            if ($jenisJawaban === 'skala' || $jenisJawaban === 'pilihan_ganda') {
                $opsiTeks = $this->request->getPost('opsi_teks');
                $opsiNilai = $this->request->getPost('opsi_nilai');
                $batchOpsi = [];
                if (!empty($opsiTeks)) {
                    foreach ($opsiTeks as $key => $teks) {
                        if (!empty($teks)) {
                            // $pertanyaanId pasti terdefinisi di sini jika insert di atas sukses
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

        // Validasi dasar
        $rules = [
            'teks_pertanyaan' => 'required|min_length[5]',
            'jenis_jawaban'   => 'required|in_list[skala,pilihan_ganda,isian]',
            'urutan'          => 'required|is_natural_no_zero',
        ];
        // Aturan validasi tambahan untuk opsi jawaban jika jenisnya skala atau pilihan ganda
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
                                'pertanyaan_id' => $id, // $id dari pertanyaan yang diupdate
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
        $dataToUpdate = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ];
        // Validasi dasar (nama pengguna, email)
        $rules = [
            'username' => 'required|min_length[3]|max_length[255]',
            'email'    => 'required|valid_email|max_length[255]',
        ];
        // Validasi unik untuk username dan email jika diubah
        $user = $this->userModel->find($userId); // Dapatkan data user saat ini untuk cek unik
        if ($dataToUpdate['username'] !== $user['username']) {
            $rules['username'] .= '|is_unique[users.username,id,' . $userId . ']';
        }
        if ($dataToUpdate['email'] !== $user['email']) {
            $rules['email'] .= '|is_unique[users.email,id,' . $userId . ']';
        }

        // Jika ada input password lama/baru, tambahkan aturan validasi password
        if ($this->request->getPost('old_password') || $this->request->getPost('new_password')) {
            $rules['old_password'] = [
                'rules' => 'required|check_old_password', // Aturan kustom untuk cek password lama
                'errors' => ['required' => 'Password lama wajib diisi untuk mengubah password.']
            ];
            $rules['new_password'] = 'required|min_length[6]|matches[confirm_new_password]';
            $rules['confirm_new_password'] = 'required';
        }

        if (!$this->validate($rules, [ // Jalankan validasi
            'old_password' => [
                'check_old_password' => 'Password lama salah.'
            ]
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        if ($this->request->getPost('new_password')) {
            $dataToUpdate['password'] = $this->request->getPost('new_password'); // Akan di-hash oleh model's beforeUpdate
        }

        if ($this->userModel->update($userId, $dataToUpdate)) {
            // Perbarui sesi dengan data baru setelah perubahan profil
            session()->set([
                'username' => $dataToUpdate['username'],
                'email'    => $dataToUpdate['email']
            ]);
            return redirect()->to(base_url('admin/profile'))->with('success', 'Profil berhasil diperbarui!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil.');
        }
    }

    // Aturan validasi kustom untuk memeriksa password lama.
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


    // --- Hasil IKM (Halaman Placeholder) ---
    public function hasil() {
        $data['kuesionerList'] = $this->kuesionerModel->findAll();
        return view('admin/hasil/index', $data);
    }
}