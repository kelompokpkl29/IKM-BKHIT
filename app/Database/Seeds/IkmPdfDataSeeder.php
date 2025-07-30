<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
use App\Models\IkmPdfDataModel; 

class IkmPdfDataSeeder extends Seeder
{
    public function run()
    {
        $ikmPdfDataModel = new IkmPdfDataModel();

        $data = [
            [
                'name' => 'Responden A (PDF)', 'date_recorded' => '2025-07-01', 'timestamp_recorded' => '2025-07-01 10:00:00',
                'team_name' => 'Tim PDF 1', 'total_score' => 85, 'aspect_a' => 1, 'aspect_b' => 1, 'aspect_c' => 1, 'aspect_d' => 1, 'aspect_e' => 0, 'aspect_f' => 1, 'aspect_g' => 1, 'aspect_h' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Responden B (PDF)', 'date_recorded' => '2025-07-02', 'timestamp_recorded' => '2025-07-02 11:30:00',
                'team_name' => 'Tim PDF 2', 'total_score' => 78, 'aspect_a' => 1, 'aspect_b' => 1, 'aspect_c' => 0, 'aspect_d' => 1, 'aspect_e' => 1, 'aspect_f' => 0, 'aspect_g' => 1, 'aspect_h' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Responden C (PDF)', 'date_recorded' => '2025-07-03', 'timestamp_recorded' => '2025-07-03 14:45:00',
                'team_name' => 'Tim PDF 1', 'total_score' => 92, 'aspect_a' => 1, 'aspect_b' => 1, 'aspect_c' => 1, 'aspect_d' => 1, 'aspect_e' => 1, 'aspect_f' => 1, 'aspect_g' => 1, 'aspect_h' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Responden D (PDF)', 'date_recorded' => '2025-07-04', 'timestamp_recorded' => '2025-07-04 09:00:00',
                'team_name' => 'Tim PDF 3', 'total_score' => 65, 'aspect_a' => 0, 'aspect_b' => 0, 'aspect_c' => 1, 'aspect_d' => 1, 'aspect_e' => 0, 'aspect_f' => 0, 'aspect_g' => 1, 'aspect_h' => 0,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $ikmPdfDataModel->insertBatch($data);
    }
}