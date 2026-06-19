<?php

namespace App\Models\Backend\Survey;

use CodeIgniter\Model;

class SurveyQuestionModel extends Model
{
    protected $table            = 'tbl_survey_questions';
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
        'survey_id', 'section_id', 'question_text', 'question_type',
        'is_required', 'sort_order', 'description',
        'validation_rules', 'settings',
        'created_at', 'updated_at',
    ];

    /**
     * Get all questions for a survey, ordered by sort_order
     * Includes options for each question
     */
    public function getQuestionsBySurvey(int $surveyId): array
    {
        $questions = $this->where('survey_id', $surveyId)
                          ->orderBy('sort_order', 'ASC')
                          ->findAll();

        // Decode JSON fields
        foreach ($questions as &$q) {
            $q['settings']          = !empty($q['settings']) ? json_decode($q['settings'], true) : [];
            $q['validation_rules']  = !empty($q['validation_rules']) ? json_decode($q['validation_rules'], true) : [];
        }

        return $questions;
    }

    /**
     * Get questions for a specific section
     */
    public function getQuestionsBySection(?int $sectionId, int $surveyId): array
    {
        $builder = $this->where('survey_id', $surveyId)->orderBy('sort_order', 'ASC');

        if ($sectionId === null) {
            $builder->where('section_id IS NULL', null, false);
        } else {
            $builder->where('section_id', $sectionId);
        }

        return $builder->findAll();
    }

    /**
     * Reorder questions
     * @param array $orderData [ ['id' => 1, 'sort_order' => 0, 'section_id' => null], ... ]
     */
    public function reorderQuestions(array $orderData): bool
    {
        foreach ($orderData as $item) {
            $updateData = [
                'sort_order' => $item['sort_order'],
                'section_id' => $item['section_id'] ?? null,
            ];
            $this->update($item['id'], $updateData);
        }
        return true;
    }

    /**
     * Duplicate a question (and its options)
     */
    public function duplicateQuestion(int $questionId): ?int
    {
        $question = $this->find($questionId);
        if (!$question) return null;

        unset($question['id']);
        $question['sort_order'] = $this->getNextSortOrder($question['survey_id']);
        $question['created_at'] = date('Y-m-d H:i:s');
        $question['updated_at'] = date('Y-m-d H:i:s');

        $this->insert($question);
        $newId = $this->getInsertID();

        // Duplicate options
        $optionModel = new SurveyOptionModel();
        $options = $optionModel->getOptionsByQuestion($questionId);
        foreach ($options as $option) {
            unset($option['id']);
            $option['question_id'] = $newId;
            $option['created_at']  = date('Y-m-d H:i:s');
            $option['updated_at']  = date('Y-m-d H:i:s');
            $optionModel->insert($option);
        }

        return $newId;
    }

    /**
     * Get next sort_order for questions in a survey
     */
    public function getNextSortOrder(int $surveyId): int
    {
        $max = $this->selectMax('sort_order', 'max_order')
                    ->where('survey_id', $surveyId)
                    ->get()->getRow();
        return ($max && $max->max_order !== null) ? (int)$max->max_order + 1 : 0;
    }

    /**
     * Tipe pertanyaan yang memiliki opsi jawaban
     */
    public static function getTypesWithOptions(): array
    {
        return ['multiple_choice', 'checkbox', 'dropdown', 'grid_multiple', 'grid_checkbox'];
    }

    /**
     * Tipe pertanyaan yang merupakan display-only (bukan input)
     */
    public static function getDisplayOnlyTypes(): array
    {
        return ['image_display', 'video_display'];
    }

    /**
     * Tipe pertanyaan yang terhubung ke master data
     */
    public static function getMasterDataTypes(): array
    {
        return ['master_tpq', 'master_guru', 'master_santri'];
    }
}
