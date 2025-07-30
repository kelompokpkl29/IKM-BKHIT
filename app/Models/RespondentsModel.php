<?php
namespace App\Models;
use CodeIgniter\Model;

class RespondentsModel extends Model
{
    protected $table      = 'respondents';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'response_session_id', 'age_group_id', 'gender_id', 'education_id', 
        'occupation_id', 'service_type_id', 'ip_address', 'submission_timestamp',
        'respondent_name', 'team_name' // Tambahan kolom nama dan tim untuk data PDF
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}