<?php

namespace App\Models;

use CodeIgniter\Model;

class PertanyaanModel extends Model
{
    protected $table      = 'pertanyaan'; // Nama tabel di database
    protected $primaryKey = 'id';    // Nama kolom primary key
    protected $allowedFields = ['kuesioner_id', 'teks_pertanyaan', 'jenis_jawaban', 'urutan']; // Kolom yang diizinkan untuk diisi/diupdate

    protected $useTimestamps = true; // Mengaktifkan otomatisasi kolom created_at dan updated_at
    protected $createdField  = 'created_at'; // Nama kolom untuk timestamp pembuatan
    protected $updatedField  = 'updated_at'; // Nama kolom untuk timestamp pembaruan

    // Metode kustom untuk mendapatkan pertanyaan beserta opsi jawabannya
    public function getPertanyaanWithOpsi($kuesionerId)
    {
        $pertanyaan = $this->where('kuesioner_id', $kuesionerId)
            ->orderBy('urutan', 'ASC')
            ->findAll();
        $opsiJawabanModel = new OpsiJawabanModel(); // Inisialisasi model OpsiJawaban
        foreach ($pertanyaan as &$p) {
            if ($p['jenis_jawaban'] !== 'isian') {
                $p['opsi'] = $opsiJawabanModel->where('pertanyaan_id', $p['id'])->findAll();
            } else {
                $p['opsi'] = []; // Jika isian, tidak ada opsi
            }
        }
        return $pertanyaan;
    }
}
