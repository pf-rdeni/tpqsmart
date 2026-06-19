<?php

namespace App\Models\Backend\Survey;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table            = 'tbl_survey';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'title', 'description', 'survey_key', 'status', 'target_type',
        'allow_anonymous', 'allow_edit_response', 'limit_one_response',
        'unique_field_type', 'unique_field_required',
        'show_progress_bar', 'shuffle_questions',
        'confirmation_message', 'header_image', 'theme_color',
        'start_date', 'end_date', 'max_responses',
        'public_result_enabled', 'public_result_mode',
        'created_by', 'created_by_tpq_id',
        'created_at', 'updated_at',
    ];

    /**
     * Generate unique survey key (slug-like random string)
     */
    public function generateSurveyKey(): string
    {
        do {
            $key = bin2hex(random_bytes(8)); // 16 chars hex
        } while ($this->where('survey_key', $key)->countAllResults() > 0);

        return $key;
    }

    /**
     * Get survey by key (untuk halaman publik)
     */
    public function getSurveyByKey(string $key): ?array
    {
        return $this->where('survey_key', $key)->first();
    }

    /**
     * Get all active surveys
     */
    public function getActiveSurveys(): array
    {
        return $this->where('status', 'active')->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get surveys filtered by TPQ (untuk Operator)
     */
    public function getSurveysByTpq(string $tpqId): array
    {
        return $this->where('created_by_tpq_id', $tpqId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get survey list with response count stats
     */
    public function getSurveyWithStats(?string $tpqId = null): array
    {
        $builder = $this->db->table('tbl_survey s')
            ->select('s.*, COUNT(DISTINCT r.id) as response_count')
            ->join('tbl_survey_responses r', 'r.survey_id = s.id', 'left')
            ->groupBy('s.id')
            ->orderBy('s.created_at', 'DESC');

        if ($tpqId !== null) {
            $builder->where('s.created_by_tpq_id', $tpqId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Toggle status (active <-> inactive)
     */
    public function toggleStatus(int $id): bool
    {
        $survey = $this->find($id);
        if (!$survey) return false;

        $newStatus = $survey['status'] === 'active' ? 'inactive' : 'active';
        return $this->update($id, ['status' => $newStatus]);
    }

    /**
     * Cek apakah survey masih aktif berdasarkan periode tanggal
     */
    public function checkDateBasedStatus(array $survey): string
    {
        if ($survey['status'] !== 'active') {
            return $survey['status'];
        }

        $now = date('Y-m-d H:i:s');

        if (!empty($survey['start_date']) && $now < $survey['start_date']) {
            return 'not_started'; // Belum dimulai
        }

        if (!empty($survey['end_date']) && $now > $survey['end_date']) {
            return 'expired'; // Sudah kadaluarsa
        }

        return 'active';
    }

    /**
     * Check apakah quota respons sudah penuh
     */
    public function isQuotaFull(int $surveyId, int $currentCount): bool
    {
        $survey = $this->find($surveyId);
        if (!$survey || empty($survey['max_responses'])) {
            return false;
        }
        return $currentCount >= $survey['max_responses'];
    }
}
