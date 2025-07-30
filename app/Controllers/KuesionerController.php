<?php

namespace App\Controllers;

use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\JawabanPenggunaModel;
use App\Models\RespondentsModel;

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

        session()->set('current_kuesioner_id', $kuesionerId);
        session()->set('current_question_index', 0);
        session()->set('partial_answers', []);
        session()->set('response_session_id', uniqid('resp_'));

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
        $allQuestions = $this->pertanyaanModel->where('kuesioner_id', $kuesionerId)->orderBy('urutan', 'ASC')->findAll();

        $displayQuestions = array_filter($allQuestions, function ($q) {
            return $q['urutan'] > 0;
        });
        $displayQuestions = array_values($displayQuestions);

        $totalDisplayQuestions = count($displayQuestions);
        $currentQuestionIndex = $questionNumber - 1;

        if ($currentQuestionIndex < 0 || $currentQuestionIndex >= $totalDisplayQuestions) {
            if ($questionNumber > $totalDisplayQuestions) {
                return redirect()->to(base_url('kuesioner/submit_final'));
            }
            return redirect()->to(base_url('kuesioner/terimakasih'));
        }

        $currentQuestion = $displayQuestions[$currentQuestionIndex];
        $currentQuestion['opsi'] = $this->opsiJawabanModel->where('pertanyaan_id', $currentQuestion['id'])->findAll();

        $data = [
            'kuesioner' => $kuesioner,
            'currentQuestion' => $currentQuestion,
            'questionNumber' => $questionNumber,
            'totalQuestions' => $totalDisplayQuestions,
            'partialAnswers' => session()->get('partial_answers') ?? [],
            'displayQuestions' => $displayQuestions
        ];

        return view('public/kuesioner_question', $data);
    }

    public function process_answer()
    {
        $kuesionerId = session()->get('current_kuesioner_id');
        $currentQuestionNumber = $this->request->getPost('question_number');
        $questionId = $this->request->getPost('question_id');
        $allQuestions = $this->pertanyaanModel->where('kuesioner_id', $kuesionerId)->orderBy('urutan', 'ASC')->findAll();

        $displayQuestions = array_filter($allQuestions, function ($q) {
            return $q['urutan'] > 0;
        });
        $displayQuestions = array_values($displayQuestions);
        $currentQuestionIndex = $currentQuestionNumber - 1;

        if (!$kuesionerId || $currentQuestionIndex < 0 || $currentQuestionIndex >= count($displayQuestions)) {
            return redirect()->to(base_url('kuesioner'))->with('error', 'Sesi kuesioner tidak valid atau pertanyaan tidak ditemukan.');
        }

        $question = $displayQuestions[$currentQuestionIndex];

        // --- VALIDASI SEDERHANA ---
        $rules = [];
        if ($question['jenis_jawaban'] === 'isian') {
            $rules['jawaban_isian_' . $questionId] = 'permit_empty|max_length[2000]';
        } else { // skala atau pilihan_ganda
            $rules['jawaban_opsi_' . $questionId] = 'required|is_natural_no_zero';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        // --- AKHIR VALIDASI ---

        // Simpan jawaban ke sesi sementara
        $partialAnswers = session()->get('partial_answers') ?? [];
        $jawabanData = [
            'pertanyaan_id' => $questionId,
            'jenis_jawaban' => $question['jenis_jawaban'],
            'opsi_jawaban_id' => ($question['jenis_jawaban'] !== 'isian') ? $this->request->getPost('jawaban_opsi_' . $questionId) : null,
            'jawaban_teks' => ($question['jenis_jawaban'] === 'isian') ? $this->request->getPost('jawaban_isian_' . $questionId) : null,
        ];
        $partialAnswers[$questionId] = $jawabanData;
        session()->set('partial_answers', $partialAnswers);

        $nextQuestionNumber = $currentQuestionNumber + 1;
        return redirect()->to(base_url('kuesioner/question/' . $nextQuestionNumber));
    }

    public function previous_question()
    {
        $currentQuestionNumber = $this->request->getPost('question_number');
        if ($currentQuestionNumber <= 1) {
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

        $respondentData = [
            'response_session_id' => $responseSessionId,
            'ip_address'          => $ipAddress,
            'submission_timestamp' => $submissionTimestamp,
            'created_at'          => $submissionTimestamp,
            'updated_at'          => $submissionTimestamp,
        ];
        $pertanyaanDemografi = $this->pertanyaanModel->where('kuesioner_id', $kuesionerId)
            ->whereIn('urutan', [1, 2, 3, 4, 5])
            ->findAll();
        foreach ($pertanyaanDemografi as $qDemo) {
            if (isset($partialAnswers[$qDemo['id']])) {
                if ($qDemo['urutan'] == 1) $respondentData['age_group_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 2) $respondentData['gender_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 3) $respondentData['education_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 4) $respondentData['occupation_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
                if ($qDemo['urutan'] == 5) $respondentData['service_type_id'] = $partialAnswers[$qDemo['id']]['opsi_jawaban_id'];
            }
        }
        $this->respondentsModel->insert($respondentData);


        $batchData = [];
        foreach ($partialAnswers as $jawaban) {
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

        if (!empty($batchData)) {
            if ($this->jawabanPenggunaModel->insertBatch($batchData)) {
                session()->remove('current_kuesioner_id');
                session()->remove('current_question_index');
                session()->remove('partial_answers');
                session()->remove('response_session_id');
                return redirect()->to(base_url('kuesioner/terimakasih'))->with('success', 'Terima kasih telah mengisi kuesioner!');
            } else {
                return redirect()->to(base_url('kuesioner/question/1'))->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
            }
        } else {
            return redirect()->to(base_url('kuesioner/question/1'))->with('error', 'Tidak ada jawaban yang dikirimkan.');
        }
    }

    public function terimakasih()
    {
        return view('public/kuesioner_terimakasih');
    }
}
