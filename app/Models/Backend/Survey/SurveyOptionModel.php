<?php

namespace App\Models\Backend\Survey;

use CodeIgniter\Model;

class SurveyOptionModel extends Model
{
    protected $table            = 'tbl_survey_options';
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
        'question_id', 'option_text', 'option_value', 'sort_order', 'image_url',
        'created_at', 'updated_at',
    ];

    /**
     * Get all options for a question, ordered by sort_order
     */
    public function getOptionsByQuestion(int $questionId): array
    {
        return $this->where('question_id', $questionId)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Get options grouped by question_id for multiple questions at once
     */
    public function getOptionsForQuestions(array $questionIds): array
    {
        if (empty($questionIds)) return [];

        $rows = $this->whereIn('question_id', $questionIds)
                     ->orderBy('sort_order', 'ASC')
                     ->findAll();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['question_id']][] = $row;
        }
        return $grouped;
    }

    /**
     * Batch save options for a question (delete old, insert new)
     * @param array $options [ ['option_text' => 'A', 'option_value' => null], ... ]
     */
    public function batchSaveOptions(int $questionId, array $options): bool
    {
        // Delete existing options for this question
        $this->where('question_id', $questionId)->delete();

        if (empty($options)) return true;

        $insertData = [];
        foreach ($options as $index => $option) {
            if (empty(trim($option['option_text'] ?? ''))) continue;
            $insertData[] = [
                'question_id'  => $questionId,
                'option_text'  => trim($option['option_text']),
                'option_value' => $option['option_value'] ?? null,
                'sort_order'   => $index,
                'image_url'    => $option['image_url'] ?? null,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
        }

        if (empty($insertData)) return true;

        return $this->insertBatch($insertData) !== false;
    }

    /**
     * Delete all options for a question
     */
    public function deleteByQuestion(int $questionId): bool
    {
        return $this->where('question_id', $questionId)->delete();
    }
}
