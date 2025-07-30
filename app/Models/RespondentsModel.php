<?php

namespace App\Models;

use CodeIgniter\Model;

class RespondentsModel extends Model
{
    protected $table      = 'respondents'; // Nama tabel di database
    protected $primaryKey = 'id';    // Nama kolom primary key
    protected $allowedFields = [
        'response_session_id',
        'age_group_id',
        'gender_id',
        'education_id',
        'occupation_id',
        'service_type_id',
        'ip_address',
        'submission_timestamp',
        'respondent_name',
        'team_name'
    ];

    protected $useTimestamps = true; // Mengaktifkan otomatisasi kolom created_at dan updated_at
    protected $createdField  = 'created_at'; // Nama kolom untuk timestamp pembuatan
    protected $updatedField  = 'updated_at'; // Nama kolom untuk timestamp pembaruan
}
