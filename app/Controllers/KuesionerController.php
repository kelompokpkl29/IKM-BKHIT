<?php

namespace App\Controllers;

use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\JawabanPenggunaModel;
use App\Models\RespondentsModel; // Pastikan ini diimpor

class KuesionerController extends BaseController
{
    protected $kuesionerModel;
    protected $pertanyaanModel;
    protected $opsiJawabanModel;
    protected $jawabanPenggunaModel;
    protected $respondentsModel;

    public function __construct()
    {
        $this->kuesionerModel = new KuesionerModel();
        $this->pertanyaanModel = new PertanyaanModel();
        $this->opsiJawabanModel = new OpsiJawabanModel();
        $this->jawabanPenggunaModel = new JawabanPenggunaModel();
        $this->respondentsModel = new RespondentsModel();
        helper(['form', 'url', 'session']);
    }

    public function index()
    {
        $data['kuesioner'] = $this->kuesionerModel->where('is_active', 1)->findAll();
        return view('public/kuesioner_list', $data);
    }

    public function start_survey($kuesionerId)
    {
        $kuesioner = $this->kuesionerModel->find($kuesionerId);
        if (!$kuesioner || !$kuesioner['is_active']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kuesioner tidak ditemukan atau tidak aktif.');
        }

        // Reset sesi untuk kuesioner baru
        session()->set('current_kuesioner_id', $kuesionerId);
        session()->set('current_question_index', 0); // Mulai dari index 0 (untuk pertanyaan ke-1)
        session()->set('partial_answers', []); // Reset jawaban parsial
        session()->set('response_session_id', uniqid('resp_')); // Buat ID sesi unik untuk responden ini

        // Redirect ke pertanyaan pertama
        return redirect()->to(base_url('kuesioner/question/1'));
    }

    // CARA ALTERNATIF: Metode untuk memulai kuesioner langsung dari /kuesioner/isi/{id}
    public function start_survey_direct($kuesionerId)
    {
        return $this->start_survey($kuesionerId);
    }


    public function question($questionNumber)
    {
        $kuesionerId = session()->get('current_kuesioner_id');
        if (!$kuesionerId) {
            return redirect()->to(base_url('kuesioner'))->with('error', 'Sesi kuesioner tidak ditemukan. Silakan mulai ulang.');
        }

        $kuesioner = $this->kuesionerModel->find($kuesionerId);
        // Ambil semua pertanyaan, termasuk urutan 0 untuk pendahuluan
        $allQuestions = $this->pertanyaanModel->where('kuesioner_id', $kuesionerId)->orderBy('urutan', 'ASC')->findAll();

        // Filter pertanyaan untuk tampilan (skip urutan 0)
        $displayQuestions = array_filter($allQuestions, function ($q) {
            return $q['urutan'] > 0;
        });
        $displayQuestions = array_values($displayQuestions); // Reset indeks array setelah filter

        $totalDisplayQuestions = count($displayQuestions);
        $currentQuestionIndex = $questionNumber - 1; // Konversi nomor pertanyaan ke indeks array

        // Cek apakah nomor pertanyaan valid
        if ($currentQuestionIndex < 0 || $currentQuestionIndex >= $totalDisplayQuestions) {
            if ($questionNumber > $totalDisplayQuestions) {
                // Jika nomor pertanyaan melebihi total, arahkan ke halaman submit final
                return redirect()->to(base_url('kuesioner/submit_final'));
            }
            // Jika nomor pertanyaan tidak valid (misal 0 atau negatif), arahkan ke halaman terima kasih/list kuesioner
            return redirect()->to(base_url('kuesioner/terimakasih'));
        }

        $currentQuestion = $displayQuestions[$currentQuestionIndex];
        // Ambil opsi jawaban untuk pertanyaan saat ini
        $currentQuestion['opsi'] = $this->opsiJawabanModel->where('pertanyaan_id', $currentQuestion['id'])->findAll();

        $data = [
            'kuesioner' => $kuesioner,
            'currentQuestion' => $currentQuestion,
            'questionNumber' => $questionNumber, // Nomor pertanyaan yang ditampilkan (mulai dari 1)
            'totalQuestions' => $totalDisplayQuestions,
            'partialAnswers' => session()->get('partial_answers') ?? [],
            'displayQuestions' => $allQuestions // Berikan semua pertanyaan untuk referensi urutan 0
        ];

        return view('public/kuesioner_question', $data);
    }

    public function process_answer()
    {
        $kuesionerId = session()->get('current_kuesioner_id');
        $currentQuestionNumber = $this->request->getPost('question_number');
        $questionId = $this->request->getPost('question_id');

        // Ambil semua pertanyaan (termasuk urutan 0) untuk navigasi yang tepat
        $allQuestions = $this->pertanyaanModel->where('kuesioner_id', $kuesionerId)->orderBy('urutan', 'ASC')->findAll();

        // Temukan pertanyaan saat ini dari $allQuestions berdasarkan $questionId
        $question = null;
        foreach ($allQuestions as $q) {
            if ($q['id'] == $questionId) {
                $question = $q;
                break;
            }
        }

        if (!$kuesionerId || !$question) {
            return redirect()->to(base_url('kuesioner'))->with('error', 'Sesi kuesioner tidak valid atau pertanyaan tidak ditemukan.');
        }

        // --- VALIDASI JAWABAN BERDASARKAN JENIS PERTANYAAN ---
        $rules = [];
        if ($question['jenis_jawaban'] === 'isian') {
            $rules['jawaban_isian_' . $questionId] = 'permit_empty|max_length[2000]';
        } else { // skala atau pilihan_ganda
            // Pastikan opsi yang dipilih adalah ID opsi yang valid di database
            $rules['jawaban_opsi_' . $questionId] = 'required|is_natural_no_zero|numeric|is_not_unique[opsi_jawaban.id]';
        }

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan ke form dengan input sebelumnya dan pesan error
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        // --- AKHIR VALIDASI ---

        // Simpan jawaban ke sesi sementara
        $partialAnswers = session()->get('partial_answers') ?? [];
        $jawabanData = [
            'pertanyaan_id' => $questionId,
            'jenis_jawaban' => $question['jenis_jawaban'],
            'opsi_jawaban_id' => null, // Default
            'jawaban_teks' => null,     // Default
        ];

        if ($question['jenis_jawaban'] === 'isian') {
            $jawabanData['jawaban_teks'] = $this->request->getPost('jawaban_isian_' . $questionId);
        } else {
            $jawabanData['opsi_jawaban_id'] = $this->request->getPost('jawaban_opsi_' . $questionId);
        }

        $partialAnswers[$questionId] = $jawabanData;
        session()->set('partial_answers', $partialAnswers);

        // Tentukan pertanyaan berikutnya
        $nextQuestionNumber = $currentQuestionNumber + 1;

        // Cari pertanyaan sebenarnya di $displayQuestions untuk memastikan ada pertanyaan berikutnya
        $displayQuestionsOnly = array_filter($allQuestions, function ($q) {
            return $q['urutan'] > 0;
        });
        $displayQuestionsOnly = array_values($displayQuestionsOnly);

        if ($nextQuestionNumber <= count($displayQuestionsOnly)) {
            return redirect()->to(base_url('kuesioner/question/' . $nextQuestionNumber));
        } else {
            // Semua pertanyaan sudah dijawab, arahkan ke submit final
            return redirect()->to(base_url('kuesioner/submit_final'));
        }
    }

    public function previous_question()
    {
        $kuesionerId = session()->get('current_kuesioner_id');
        $currentQuestionNumber = $this->request->getPost('question_number');

        if ($currentQuestionNumber <= 1) {
            // Jika di pertanyaan pertama, hapus sesi dan kembali ke daftar kuesioner
            session()->remove('current_kuesioner_id');
            session()->remove('current_question_index');
            session()->remove('partial_answers');
            session()->remove('response_session_id');
            return redirect()->to(base_url('kuesioner'));
        }
        $previousQuestionNumber = $currentQuestionNumber - 1;
        return redirect()->to(base_url('kuesioner/question/' . $previousQuestionNumber));
    }

    public function submit_final()
    {
        $kuesionerId = session()->get('current_kuesioner_id');
        $partialAnswers = session()->get('partial_answers');
        $responseSessionId = session()->get('response_session_id');
        $ipAddress = $this->request->getIPAddress();
        $submissionTimestamp = date('Y-m-d H:i:s');

        if (!$kuesionerId || empty($partialAnswers) || !$responseSessionId) {
            return redirect()->to(base_url('kuesioner'))->with('error', 'Sesi jawaban tidak valid. Silakan mulai ulang kuesioner.');
        }

        // Simpan data responden (demografi) terlebih dahulu
        $respondentData = [
            'response_session_id' => $responseSessionId,
            'ip_address'          => $ipAddress,
            'submission_timestamp' => $submissionTimestamp,
            'created_at'          => $submissionTimestamp,
            'updated_at'          => $submissionTimestamp,
        ];

        // Dapatkan pertanyaan demografi dari database berdasarkan urutan 1-5
        // dan cocokkan dengan jawaban yang ada di sesi
        $pertanyaanDemografi = $this->pertanyaanModel->where('kuesioner_id', $kuesionerId)
            ->whereIn('urutan', [1, 2, 3, 4, 5])
            ->findAll();
        foreach ($pertanyaanDemografi as $qDemo) {
            if (isset($partialAnswers[$qDemo['id']])) {
                // Map urutan pertanyaan ke kolom di tabel respondents
                if ($qDemo['urutan'] == 1) $respondentData['age_group_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 2) $respondentData['gender_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 3) $respondentData['education_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 4) $respondentData['occupation_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 5) $respondentData['service_type_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
            }
        }
        $this->respondentsModel->insert($respondentData);


        // Simpan jawaban granular ke tabel jawaban_pengguna
        $batchData = [];
        foreach ($partialAnswers as $jawaban) {
            // Pastikan ID pertanyaan 0 (Pendahuluan) tidak disimpan di jawaban granular
            $question_urutan = $this->pertanyaanModel->select('urutan')->find($jawaban['pertanyaan_id']);
            if ($question_urutan && $question_urutan['urutan'] === 0) {
                continue; // Skip pertanyaan pendahuluan
            }

            $batchData[] = [
                'kuesioner_id'      => $kuesionerId,
                'pertanyaan_id'     => $jawaban['pertanyaan_id'],
                'opsi_jawaban_id'   => $jawaban['opsi_jawaban_id'],
                'jawaban_teks'      => $jawaban['jawaban_teks'],
                'ip_address'        => $ipAddress,
                'response_session_id' => $responseSessionId,
                'timestamp_isi'     => $submissionTimestamp,
            ];
        }

        // PENTING: Periksa apakah $batchData tidak kosong sebelum memanggil insertBatch
        // Ini untuk menghindari error jika tidak ada jawaban yang valid untuk disimpan
        if (!empty($batchData)) {
            if ($this->jawabanPenggunaModel->insertBatch($batchData)) {
                // Bersihkan sesi setelah submit berhasil
                session()->remove('current_kuesioner_id');
                session()->remove('current_question_index');
                session()->remove('partial_answers');
                session()->remove('response_session_id');
                return redirect()->to(base_url('kuesioner/terimakasih'))->with('success', 'Terima kasih telah mengisi kuesioner!');
            } else {
                // Tangani kasus insertBatch gagal
                return redirect()->to(base_url('kuesioner/question/1'))->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
            }
        } else {
            // Jika tidak ada jawaban yang tersimpan (misal hanya pertanyaan pendahuluan yang diisi)
            // Tetap bersihkan sesi dan arahkan ke halaman terima kasih
            session()->remove('current_kuesioner_id');
            session()->remove('current_question_index');
            session()->remove('partial_answers');
            session()->remove('response_session_id');
            return redirect()->to(base_url('kuesioner/terimakasih'))->with('success', 'Kuesioner selesai. Terima kasih!');
        }
    }

    public function terimakasih()
    {
        return view('public/kuesioner_terimakasih');
    }
}
