<?php
namespace App\Controllers;
use App\Models\KuesionerModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiJawabanModel;
use App\Models\JawabanPenggunaModel;

class KuesionerController extends BaseController
{
    protected $kuesionerModel;
    protected $pertanyaanModel;
    protected $opsiJawabanModel;
    protected $jawabanPenggunaModel;

    public function __construct()
    {
        $this->kuesionerModel = new KuesionerModel();
        $this->pertanyaanModel = new PertanyaanModel();
        $this->opsiJawabanModel = new OpsiJawabanModel();
        $this->jawabanPenggunaModel = new JawabanPenggunaModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data['kuesioner'] = $this->kuesionerModel->where('is_active', 1)->findAll();
        return view('public/kuesioner_list', $data);
    }

    public function isi($id)
    {
        $data['kuesioner'] = $this->kuesionerModel->find($id);
        if (!$data['kuesioner'] || !$data['kuesioner']['is_active']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kuesioner tidak ditemukan atau tidak aktif.');
        }
        $data['pertanyaan'] = $this->pertanyaanModel->getPertanyaanWithOpsi($id);
        return view('public/kuesioner_form', $data);
    }

    public function submit()
    {
        $kuesionerId = $this->request->getPost('kuesioner_id');
        $pertanyaanIds = $this->request->getPost('pertanyaan_id');
        $ipAddress = $this->request->getIPAddress();
        $batchData = [];

        if (empty($pertanyaanIds)) {
            return redirect()->back()->with('error', 'Tidak ada pertanyaan untuk kuesioner ini.');
        }

        foreach ($pertanyaanIds as $pertanyaanId) {
            $pertanyaan = $this->pertanyaanModel->find($pertanyaanId);
            if (!$pertanyaan) continue;

            $jawabanData = [
                'kuesioner_id'  => $kuesionerId,
                'pertanyaan_id' => $pertanyaanId,
                'ip_address'    => $ipAddress,
                'timestamp_isi' => date('Y-m-d H:i:s'),
            ];

            if ($pertanyaan['jenis_jawaban'] === 'isian') {
                $jawabanData['jawaban_teks'] = $this->request->getPost('jawaban_isian_' . $pertanyaanId);
                $jawabanData['opsi_jawaban_id'] = null;
            } else { // skala atau pilihan_ganda
                $jawabanData['opsi_jawaban_id'] = $this->request->getPost('jawaban_opsi_' . $pertanyaanId);
                $jawabanData['jawaban_teks'] = null;
            }
            $batchData[] = $jawabanData;
        }

        if (!empty($batchData)) {
            if ($this->jawabanPenggunaModel->insertBatch($batchData)) {
                return redirect()->to(base_url('kuesioner/terimakasih'))->with('success', 'Terima kasih telah mengisi kuesioner!');
            } else {
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban.');
            }
        } else {
            return redirect()->back()->with('error', 'Tidak ada jawaban yang dikirimkan.');
        }
    }

    public function terimakasih()
    {
        return view('public/kuesioner_terimakasih');
    }
}