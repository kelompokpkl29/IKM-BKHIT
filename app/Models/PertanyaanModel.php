<?php

namespace App\Models;

use CodeIgniter\Model;

class PertanyaanModel extends Model
{
    protected $table      = 'pertanyaan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kuesioner_id', 'teks_pertanyaan', 'jenis_jawaban', 'urutan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Mendapatkan pertanyaan beserta opsi jawabannya
    public function getPertanyaanWithOpsi($kuesionerId)
    {
        $pertanyaan = $this->where('kuesioner_id', $kuesionerId)
            ->orderBy('urutan', 'ASC')
            ->findAll();
        $opsiJawabanModel = new OpsiJawabanModel();
        foreach ($pertanyaan as &$p) {
            if ($p['jenis_jawaban'] !== 'isian') {
                $p['opsi'] = $opsiJawabanModel->where('pertanyaan_id', $p['id'])->findAll();
            } else {
                $p['opsi'] = [];
            }
        }
        return $pertanyaan;
    }
}
