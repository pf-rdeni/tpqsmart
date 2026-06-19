<?php

namespace App\Models\Backend\Survey;

use CodeIgniter\Model;

class SurveySectionModel extends Model
{
    protected $table            = 'tbl_survey_sections';
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
        'survey_id', 'title', 'description', 'sort_order',
        'created_at', 'updated_at',
    ];

    /**
     * Get all sections by survey ID, ordered by sort_order
     */
    public function getSectionsBySurvey(int $surveyId): array
    {
        return $this->where('survey_id', $surveyId)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Reorder sections based on new order array
     * @param array $orderData [ ['id' => 1, 'sort_order' => 0], ... ]
     */
    public function reorderSections(int $surveyId, array $orderData): bool
    {
        foreach ($orderData as $item) {
            $this->where('id', $item['id'])
                 ->where('survey_id', $surveyId)
                 ->set('sort_order', $item['sort_order'])
                 ->update();
        }
        return true;
    }

    /**
     * Get next sort_order for a survey
     */
    public function getNextSortOrder(int $surveyId): int
    {
        $max = $this->selectMax('sort_order', 'max_order')
                    ->where('survey_id', $surveyId)
                    ->get()->getRow();
        return ($max && $max->max_order !== null) ? (int)$max->max_order + 1 : 0;
    }
}
