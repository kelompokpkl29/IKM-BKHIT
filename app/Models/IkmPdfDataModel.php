<?php
namespace App\Models;
use CodeIgniter\Model;

class IkmPdfDataModel extends Model
{
    protected $table      = 'ikm_pdf_data'; // Nama tabel di DB sesuai migrasi
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'date_recorded', 'timestamp_recorded', 'team_name', 'total_score',
        'aspect_a', 'aspect_b', 'aspect_c', 'aspect_d', 'aspect_e', 'aspect_f', 'aspect_g', 'aspect_h'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}