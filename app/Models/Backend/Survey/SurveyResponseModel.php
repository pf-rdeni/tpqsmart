<?php

namespace App\Models\Backend\Survey;

use CodeIgniter\Model;

class SurveyResponseModel extends Model
{
    protected $table            = 'tbl_survey_responses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $useTimestamps    = false; // Manual timestamp (submitted_at, updated_at)

    protected $allowedFields = [
        'survey_id', 'respondent_identifier', 'respondent_name',
        'respondent_email', 'respondent_phone',
        'respondent_type', 'respondent_ref_id', 'respondent_tpq_id',
        'answers', 'ip_address', 'user_agent',
        'submitted_at', 'updated_at',
    ];

    /**
     * Get all responses for a survey
     */
    public function getResponsesBySurvey(int $surveyId, array $filters = []): array
    {
        $builder = $this->where('survey_id', $surveyId)->orderBy('submitted_at', 'DESC');

        if (!empty($filters['tpq_id'])) {
            $builder->where('respondent_tpq_id', $filters['tpq_id']);
        }
        if (!empty($filters['respondent_type'])) {
            $builder->where('respondent_type', $filters['respondent_type']);
        }
        if (!empty($filters['start_date'])) {
            $builder->where('submitted_at >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $builder->where('submitted_at <=', $filters['end_date'] . ' 23:59:59');
        }

        $responses = $builder->findAll();

        // Decode JSON answers
        foreach ($responses as &$r) {
            $r['answers'] = !empty($r['answers']) ? json_decode($r['answers'], true) : [];
        }

        return $responses;
    }

    /**
     * Count total responses for a survey
     */
    public function countResponsesBySurvey(int $surveyId): int
    {
        return $this->where('survey_id', $surveyId)->countAllResults();
    }

    /**
     * Check if respondent already submitted (by identifier/cookie)
     */
    public function checkDuplicateResponse(int $surveyId, string $identifier): bool
    {
        return $this->where('survey_id', $surveyId)
                    ->where('respondent_identifier', $identifier)
                    ->countAllResults() > 0;
    }

    /**
     * Check duplicate by email
     */
    public function checkDuplicateByEmail(int $surveyId, string $email): bool
    {
        return $this->where('survey_id', $surveyId)
                    ->where('respondent_email', $email)
                    ->countAllResults() > 0;
    }

    /**
     * Check duplicate by phone
     */
    public function checkDuplicateByPhone(int $surveyId, string $phone): bool
    {
        return $this->where('survey_id', $surveyId)
                    ->where('respondent_phone', $phone)
                    ->countAllResults() > 0;
    }

    /**
     * Check duplicate by respondent ref id (for guru/santri/tpq targets)
     */
    public function checkDuplicateByRefId(int $surveyId, string $refId): bool
    {
        return $this->where('survey_id', $surveyId)
                    ->where('respondent_ref_id', $refId)
                    ->countAllResults() > 0;
    }

    /**
     * Get responses data for export (flat format)
     */
    public function getResponsesForExport(int $surveyId): array
    {
        $responses = $this->where('survey_id', $surveyId)
                          ->orderBy('submitted_at', 'ASC')
                          ->findAll();

        foreach ($responses as &$r) {
            $r['answers'] = !empty($r['answers']) ? json_decode($r['answers'], true) : [];
        }

        return $responses;
    }

    /**
     * Get response count per day for chart
     */
    public function getResponseCountPerDay(int $surveyId, int $days = 30): array
    {
        return $this->db->table('tbl_survey_responses')
            ->select("DATE(submitted_at) as date, COUNT(*) as count")
            ->where('survey_id', $surveyId)
            ->where('submitted_at >=', date('Y-m-d', strtotime("-{$days} days")))
            ->groupBy('DATE(submitted_at)')
            ->orderBy('date', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Get summary stats for a survey's answers (per question)
     * Returns aggregated data for chart visualization
     */
    public function getResponseSummary(int $surveyId): array
    {
        $questionModel = new \App\Models\Backend\Survey\SurveyQuestionModel();
        $questions = $questionModel->getQuestionsBySurvey($surveyId);
        $questionIds = array_column($questions, 'id');

        $optionModel = new \App\Models\Backend\Survey\SurveyOptionModel();
        $optionsMap = $optionModel->getOptionsForQuestions($questionIds);

        $responses = $this->where('survey_id', $surveyId)->findAll();

        $summary = [];
        
        // 1. Pre-initialize structure by question ID & type
        foreach ($questions as $q) {
            $qId = (int)$q['id'];
            $type = $q['question_type'];

            if (in_array($type, ['image_display', 'video_display'])) {
                continue;
            }

            if (in_array($type, ['text_short', 'text_paragraph'])) {
                $summary[$qId] = [
                    'answers' => [],
                    'labels' => [],
                    'counts' => [],
                    'option_index_map' => [],
                    'is_numeric_chart' => (isset($q['validation_rules']['rule_type']) && $q['validation_rules']['rule_type'] === 'number')
                ];
            } elseif (in_array($type, ['multiple_choice', 'checkbox', 'dropdown'])) {
                $qOptions = $optionsMap[$qId] ?? [];
                $labels = [];
                $counts = [];
                $optionIndexMap = [];
                foreach ($qOptions as $idx => $opt) {
                    $labels[] = $opt['option_text'];
                    $counts[] = 0;
                    $optionIndexMap[$opt['option_text']] = $idx;
                }
                $summary[$qId] = [
                    'labels' => $labels,
                    'counts' => $counts,
                    'option_index_map' => $optionIndexMap,
                ];
            } elseif (in_array($type, ['linear_scale', 'rating'])) {
                $settings = $q['settings'] ?? [];
                if ($type === 'linear_scale') {
                    $min = isset($settings['min']) ? (int)$settings['min'] : 1;
                    $max = isset($settings['max']) ? (int)$settings['max'] : 5;
                } else {
                    $min = 1;
                    $max = isset($settings['max_stars']) ? (int)$settings['max_stars'] : 5;
                }
                $labels = [];
                $counts = [];
                $optionIndexMap = [];
                $idx = 0;
                for ($i = $min; $i <= $max; $i++) {
                    $labels[] = (string)$i;
                    $counts[] = 0;
                    $optionIndexMap[(string)$i] = $idx;
                    $idx++;
                }
                $summary[$qId] = [
                    'labels' => $labels,
                    'counts' => $counts,
                    'option_index_map' => $optionIndexMap,
                ];
            } elseif (in_array($type, ['grid_multiple', 'grid_checkbox'])) {
                $settings = $q['settings'] ?? [];
                $rows = $settings['rows'] ?? [];
                $cols = $settings['columns'] ?? [];
                
                $rowStats = [];
                foreach ($rows as $row) {
                    $rowStats[$row] = [];
                    foreach ($cols as $col) {
                        $rowStats[$row][$col] = 0;
                    }
                }
                
                $summary[$qId] = [
                    'rows' => $rowStats,
                    'columns' => $cols,
                ];
            } elseif ($type === 'file_upload') {
                $summary[$qId] = [
                    'files' => []
                ];
            } elseif (in_array($type, ['master_tpq', 'master_guru', 'master_santri'])) {
                $summary[$qId] = [
                    'counts' => []
                ];
            }
        }

        // 2. Fetch and resolve master data references (TPQ, Guru, Santri)
        $tpqIds = [];
        $guruIds = [];
        $santriIds = [];

        foreach ($responses as $response) {
            $answers = !empty($response['answers']) ? json_decode($response['answers'], true) : [];
            foreach ($questions as $q) {
                $qId = (int)$q['id'];
                $type = $q['question_type'];
                if (!in_array($type, ['master_tpq', 'master_guru', 'master_santri'])) {
                    continue;
                }
                $key = 'q_' . $qId;
                $answer = $answers[$key] ?? null;
                if ($answer === null || $answer === '' || $answer === []) {
                    continue;
                }
                if ($type === 'master_tpq') {
                    $tpqIds[] = $answer;
                } elseif ($type === 'master_guru') {
                    $guruIds[] = $answer;
                } elseif ($type === 'master_santri') {
                    $santriIds[] = $answer;
                }
            }
        }

        $tpqNames = [];
        if (!empty($tpqIds)) {
            $tpqIds = array_unique($tpqIds);
            $tpqs = $this->db->table('tbl_tpq')
                             ->select('IdTpq, NamaTpq, KelurahanDesa')
                             ->whereIn('IdTpq', $tpqIds)
                             ->get()->getResultArray();
            $namesCount = [];
            foreach ($tpqs as $t) {
                $name = trim($t['NamaTpq']);
                $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
            }
            $titleCase = function($str) {
                return ucwords(strtolower(trim($str)));
            };
            foreach ($tpqs as $t) {
                $name = $titleCase($t['NamaTpq']);
                if (($namesCount[trim($t['NamaTpq'])] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                    $name = $name . ' - ' . $titleCase($t['KelurahanDesa']);
                }
                $tpqNames[$t['IdTpq']] = $name;
            }
        }

        $guruNames = [];
        if (!empty($guruIds)) {
            $guruIds = array_unique($guruIds);
            $gurus = $this->db->table('tbl_guru')
                             ->select('IdGuru, Nama')
                             ->whereIn('IdGuru', $guruIds)
                             ->get()->getResultArray();
            $titleCase = function($str) {
                return ucwords(strtolower(trim($str)));
            };
            foreach ($gurus as $g) {
                $guruNames[$g['IdGuru']] = $titleCase($g['Nama']);
            }
        }

        $santriNames = [];
        if (!empty($santriIds)) {
            $santriIds = array_unique($santriIds);
            $santris = $this->db->table('tbl_santri_baru')
                               ->select('IdSantri, NamaSantri')
                               ->whereIn('IdSantri', $santriIds)
                               ->get()->getResultArray();
            $titleCase = function($str) {
                return ucwords(strtolower(trim($str)));
            };
            foreach ($santris as $s) {
                $santriNames[$s['IdSantri']] = $titleCase($s['NamaSantri']);
            }
        }

        // 3. Process responses to compile stats
        foreach ($responses as $response) {
            $answers = !empty($response['answers']) ? json_decode($response['answers'], true) : [];
            foreach ($questions as $q) {
                $qId = (int)$q['id'];
                $type = $q['question_type'];

                if (!isset($summary[$qId])) {
                    continue;
                }

                $key = 'q_' . $qId;
                $answer = $answers[$key] ?? null;
                if ($answer === null || $answer === '' || $answer === []) {
                    continue;
                }

                if (in_array($type, ['text_short', 'text_paragraph'])) {
                    $ansStr = (string)$answer;
                    $summary[$qId]['answers'][] = $ansStr;
                    if (isset($summary[$qId]['option_index_map'][$ansStr])) {
                        $idx = $summary[$qId]['option_index_map'][$ansStr];
                        $summary[$qId]['counts'][$idx]++;
                    } else {
                        $summary[$qId]['labels'][] = $ansStr;
                        $summary[$qId]['counts'][] = 1;
                        $newIdx = count($summary[$qId]['labels']) - 1;
                        $summary[$qId]['option_index_map'][$ansStr] = $newIdx;
                    }
                } elseif (in_array($type, ['multiple_choice', 'dropdown'])) {
                    $ansStr = (string)$answer;
                    if (isset($summary[$qId]['option_index_map'][$ansStr])) {
                        $idx = $summary[$qId]['option_index_map'][$ansStr];
                        $summary[$qId]['counts'][$idx]++;
                    } else {
                        // Dynamically append new label if not pre-defined
                        $summary[$qId]['labels'][] = $ansStr;
                        $summary[$qId]['counts'][] = 1;
                        $newIdx = count($summary[$qId]['labels']) - 1;
                        $summary[$qId]['option_index_map'][$ansStr] = $newIdx;
                    }
                } elseif ($type === 'checkbox') {
                    $vals = is_array($answer) ? $answer : [$answer];
                    foreach ($vals as $val) {
                        $ansStr = (string)$val;
                        if ($ansStr === '') continue;
                        if (isset($summary[$qId]['option_index_map'][$ansStr])) {
                            $idx = $summary[$qId]['option_index_map'][$ansStr];
                            $summary[$qId]['counts'][$idx]++;
                        } else {
                            $summary[$qId]['labels'][] = $ansStr;
                            $summary[$qId]['counts'][] = 1;
                            $newIdx = count($summary[$qId]['labels']) - 1;
                            $summary[$qId]['option_index_map'][$ansStr] = $newIdx;
                        }
                    }
                } elseif (in_array($type, ['linear_scale', 'rating'])) {
                    $ansStr = (string)$answer;
                    if (isset($summary[$qId]['option_index_map'][$ansStr])) {
                        $idx = $summary[$qId]['option_index_map'][$ansStr];
                        $summary[$qId]['counts'][$idx]++;
                    } else {
                        $summary[$qId]['labels'][] = $ansStr;
                        $summary[$qId]['counts'][] = 1;
                        $newIdx = count($summary[$qId]['labels']) - 1;
                        $summary[$qId]['option_index_map'][$ansStr] = $newIdx;
                    }
                } elseif (in_array($type, ['grid_multiple', 'grid_checkbox'])) {
                    $gridData = $answer;
                    if (is_string($gridData) && strpos($gridData, '{') === 0) {
                        $gridData = json_decode($gridData, true);
                    }
                    if (is_array($gridData) && isset($gridData['rows'])) {
                        foreach ($gridData['rows'] as $row => $cols) {
                            if (is_array($cols)) {
                                foreach ($cols as $col) {
                                    $colStr = (string)$col;
                                    if (isset($summary[$qId]['rows'][$row][$colStr])) {
                                        $summary[$qId]['rows'][$row][$colStr]++;
                                    }
                                }
                            } else {
                                $colStr = (string)$cols;
                                if (isset($summary[$qId]['rows'][$row][$colStr])) {
                                    $summary[$qId]['rows'][$row][$colStr]++;
                                }
                            }
                        }
                    }
                } elseif ($type === 'file_upload') {
                    $fileData = $answer;
                    if (is_string($fileData) && strpos($fileData, '{') === 0) {
                        $fileData = json_decode($fileData, true);
                    }
                    if (is_array($fileData) && isset($fileData['file_name']) && isset($fileData['file_path'])) {
                        $summary[$qId]['files'][] = [
                            'file_name' => $fileData['file_name'],
                            'file_path' => $fileData['file_path']
                        ];
                    }
                } elseif (in_array($type, ['master_tpq', 'master_guru', 'master_santri'])) {
                    $ansStr = (string)$answer;
                    $resolvedName = '';
                    if ($type === 'master_tpq' && isset($tpqNames[$ansStr])) {
                        $resolvedName = $tpqNames[$ansStr];
                    } elseif ($type === 'master_guru' && isset($guruNames[$ansStr])) {
                        $resolvedName = $guruNames[$ansStr];
                    } elseif ($type === 'master_santri' && isset($santriNames[$ansStr])) {
                        $resolvedName = $santriNames[$ansStr];
                    }
                    
                    if ($resolvedName !== '') {
                        $summary[$qId]['counts'][$resolvedName] = ($summary[$qId]['counts'][$resolvedName] ?? 0) + 1;
                    } else {
                        $summary[$qId]['counts'][$ansStr] = ($summary[$qId]['counts'][$ansStr] ?? 0) + 1;
                    }
                }
            }
        }

        // Clean up helper index mapping before returning
        foreach ($summary as $qId => &$qSum) {
            if (isset($qSum['is_numeric_chart']) && $qSum['is_numeric_chart'] && !empty($qSum['labels'])) {
                $labels = $qSum['labels'];
                $counts = $qSum['counts'];
                $numericLabels = array_map('floatval', $labels);
                array_multisort($numericLabels, SORT_ASC, SORT_NUMERIC, $labels, $counts);
                $qSum['labels'] = $labels;
                $qSum['counts'] = $counts;
            }
            if (isset($qSum['option_index_map'])) {
                unset($qSum['option_index_map']);
            }
        }
        unset($qSum);

        return $summary;
    }

    /**
     * Resolves active section IDs by traversing the branching logic based on the answers array.
     */
    public function getActiveSections(int $surveyId, array $questions, array $answers): array
    {
        $sectionModel = new \App\Models\Backend\Survey\SurveySectionModel();
        $sections = $sectionModel->getSectionsBySurvey($surveyId);
        if (empty($sections)) {
            return [];
        }

        // Sort sections by sort_order
        usort($sections, function($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });

        $questionsBySection = [];
        foreach ($questions as $q) {
            if ($q['section_id'] !== null) {
                $questionsBySection[$q['section_id']][] = $q;
            }
        }

        $questionIds = array_column($questions, 'id');
        $optionModel = new \App\Models\Backend\Survey\SurveyOptionModel();
        $optionsMap = $optionModel->getOptionsForQuestions($questionIds);

        $activeSections = [];
        $currentSection = $sections[0];
        $activeSections[] = $currentSection['id'];

        while ($currentSection !== null) {
            $currentSectionId = $currentSection['id'];
            $nextDestination = null;

            // Check choice questions in the current section for branching
            $secQuestions = $questionsBySection[$currentSectionId] ?? [];
            foreach ($secQuestions as $q) {
                if (!in_array($q['question_type'], ['multiple_choice', 'dropdown'])) {
                    continue;
                }

                $key = 'q_' . $q['id'];
                $ansVal = $answers[$key] ?? null;
                if ($ansVal === null || $ansVal === '') {
                    continue;
                }

                // Find matching option
                $qOptions = $optionsMap[$q['id']] ?? [];
                foreach ($qOptions as $opt) {
                    if ($opt['option_text'] === $ansVal) {
                        if (!empty($opt['option_value'])) {
                            $nextDestination = $opt['option_value'];
                        }
                        break;
                    }
                }
                if ($nextDestination) {
                    break;
                }
            }

            if ($nextDestination === 'submit') {
                break;
            } elseif ($nextDestination) {
                // Find section by ID
                $foundSection = null;
                foreach ($sections as $sec) {
                    if ((string)$sec['id'] === (string)$nextDestination) {
                        $foundSection = $sec;
                        break;
                    }
                }
                if ($foundSection) {
                    // Check to avoid infinite loop
                    if (in_array($foundSection['id'], $activeSections)) {
                        break;
                    }
                    $activeSections[] = $foundSection['id'];
                    $currentSection = $foundSection;
                } else {
                    // Fallback to next section in order
                    $currentSection = $this->getNextSection($sections, $currentSection['id']);
                    if ($currentSection) {
                        $activeSections[] = $currentSection['id'];
                    }
                }
            } else {
                // No branching, go to next section in order
                $currentSection = $this->getNextSection($sections, $currentSection['id']);
                if ($currentSection) {
                    $activeSections[] = $currentSection['id'];
                }
            }
        }

        return $activeSections;
    }

    private function getNextSection(array $sections, int $currentSectionId): ?array
    {
        $found = false;
        foreach ($sections as $sec) {
            if ($found) {
                return $sec;
            }
            if ($sec['id'] === $currentSectionId) {
                $found = true;
            }
        }
        return null;
    }

    /**
     * Get filling status — who has filled and who hasn't (per target list)
     * @param array $targetList  [ ['ref_id' => 'G001', 'name' => 'Ahmad', 'tpq_id' => '01', 'type' => 'guru'], ... ]
     */
    public function getFillingStatus(int $surveyId, array $targetList): array
    {
        // Get all ref_ids that have submitted
        $submitted = $this->db->table('tbl_survey_responses')
            ->select('respondent_ref_id, respondent_name, respondent_tpq_id, submitted_at')
            ->where('survey_id', $surveyId)
            ->where('respondent_ref_id IS NOT NULL')
            ->get()->getResultArray();

        $submittedMap = [];
        foreach ($submitted as $s) {
            $submittedMap[$s['respondent_ref_id']] = $s;
        }

        $filled   = [];
        $unfilled = [];

        foreach ($targetList as $target) {
            $refId = $target['ref_id'];
            if (isset($submittedMap[$refId])) {
                $filled[] = array_merge($target, [
                    'submitted_at' => $submittedMap[$refId]['submitted_at'],
                ]);
            } else {
                $unfilled[] = $target;
            }
        }

        return [
            'filled'   => $filled,
            'unfilled' => $unfilled,
            'total'    => count($targetList),
            'filled_count'   => count($filled),
            'unfilled_count' => count($unfilled),
            'percentage' => count($targetList) > 0 ? round((count($filled) / count($targetList)) * 100, 1) : 0,
        ];
    }
}
