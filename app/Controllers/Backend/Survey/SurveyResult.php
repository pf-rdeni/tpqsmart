<?php

namespace App\Controllers\Backend\Survey;

use App\Controllers\BaseController;
use App\Models\Backend\Survey\SurveyModel;
use App\Models\Backend\Survey\SurveySectionModel;
use App\Models\Backend\Survey\SurveyQuestionModel;
use App\Models\Backend\Survey\SurveyOptionModel;
use App\Models\Backend\Survey\SurveyResponseModel;
use App\Models\Backend\Survey\SurveyTargetModel;

class SurveyResult extends BaseController
{
    protected SurveyModel         $surveyModel;
    protected SurveyResponseModel $responseModel;
    protected SurveyTargetModel   $targetModel;
    protected SurveyQuestionModel $questionModel;
    protected SurveyOptionModel   $optionModel;
    protected                     $db;

    public function __construct()
    {
        $this->db            = \Config\Database::connect();
        $this->surveyModel   = new SurveyModel();
        $this->responseModel = new SurveyResponseModel();
        $this->targetModel   = new SurveyTargetModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->optionModel   = new SurveyOptionModel();
    }

    /**
     * Halaman utama hasil survey
     */
    public function index(int $surveyId)
    {
        $survey = $this->getSurveyOrFail($surveyId);
        $totalResponse = $this->responseModel->countResponsesBySurvey($surveyId);

        $data = [
            'page_title'     => 'Hasil Survey — ' . $survey['title'],
            'survey'         => $survey,
            'total_response' => $totalResponse,
        ];

        return view('backend/survey/results/index', $data);
    }

    /**
     * Summary — chart & statistik
     */
    public function summary(int $surveyId)
    {
        $survey    = $this->getSurveyOrFail($surveyId);
        $questions = $this->questionModel->getQuestionsBySurvey($surveyId);
        $questionIds = array_column($questions, 'id');
        $optionsMap  = $this->optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }
        unset($q);

        $summary       = $this->responseModel->getResponseSummary($surveyId);
        $totalResponse = $this->responseModel->countResponsesBySurvey($surveyId);
        $dailyData     = $this->responseModel->getResponseCountPerDay($surveyId);

        $data = [
            'page_title'     => 'Summary — ' . $survey['title'],
            'survey'         => $survey,
            'questions'      => $questions,
            'summary'        => $summary,
            'total_response' => $totalResponse,
            'daily_data'     => $dailyData,
        ];

        return view('backend/survey/results/summary', $data);
    }

    /**
     * List semua responses
     */
    public function responses(int $surveyId)
    {
        $survey    = $this->getSurveyOrFail($surveyId);
        $filters   = $this->request->getGet();
        $responses = $this->responseModel->getResponsesBySurvey($surveyId, $filters);
        $questions = $this->questionModel->getQuestionsBySurvey($surveyId);

        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };

        // Ambil daftar TPQ untuk filter
        $tpqsRaw = $this->db->table('tbl_tpq')->select('IdTpq, NamaTpq, KelurahanDesa')->orderBy('NamaTpq')->get()->getResultArray();
        $namesCount = [];
        foreach ($tpqsRaw as $t) {
            $name = trim($t['NamaTpq']);
            $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
        }

        $tpqMap = [];
        $tpqs = array_map(function($t) use ($namesCount, $titleCase, &$tpqMap) {
            $name = $titleCase($t['NamaTpq']);
            if (($namesCount[trim($t['NamaTpq'])] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                $name = $name . ' - ' . $titleCase($t['KelurahanDesa']);
            }
            $tpqMap[$t['IdTpq']] = $name;
            return [
                'IdTpq'   => $t['IdTpq'],
                'NamaTpq' => $name,
            ];
        }, $tpqsRaw);

        // Map respondent_tpq_id to NamaTpq for table responses list
        foreach ($responses as &$resp) {
            $tpqId = $resp['respondent_tpq_id'];
            $resp['NamaTpq'] = isset($tpqMap[$tpqId]) ? $tpqMap[$tpqId] : null;
        }

        $data = [
            'page_title' => 'Daftar Responses — ' . $survey['title'],
            'survey'     => $survey,
            'responses'  => $responses,
            'questions'  => $questions,
            'tpqs'       => $tpqs,
            'filters'    => $filters,
        ];

        return view('backend/survey/results/responses', $data);
    }

    /**
     * Detail satu response individu
     */
    public function viewResponse(int $responseId)
    {
        $response = $this->responseModel->find($responseId);
        if (!$response) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $survey    = $this->getSurveyOrFail($response['survey_id']);
        $questions = $this->questionModel->getQuestionsBySurvey($response['survey_id']);
        $questionIds = array_column($questions, 'id');
        $optionsMap  = $this->optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }
        unset($q);

        $answers = !empty($response['answers']) ? json_decode($response['answers'], true) : [];

        // Ambil Nama TPQ jika ada
        $response['NamaTpq'] = null;
        if (!empty($response['respondent_tpq_id'])) {
            $tpq = $this->db->table('tbl_tpq')
                ->select('NamaTpq, KelurahanDesa')
                ->where('IdTpq', $response['respondent_tpq_id'])
                ->get()
                ->getRowArray();
            if ($tpq) {
                $titleCase = function($str) {
                    return ucwords(strtolower(trim($str)));
                };
                $name = trim($tpq['NamaTpq']);
                $count = $this->db->table('tbl_tpq')->where('NamaTpq', $name)->countAllResults();
                $formattedName = $titleCase($tpq['NamaTpq']);
                if ($count > 1 && !empty($tpq['KelurahanDesa'])) {
                    $formattedName .= ' - ' . $titleCase($tpq['KelurahanDesa']);
                }
                $response['NamaTpq'] = $formattedName;
            }
        }

        $data = [
            'page_title' => 'Detail Response — ' . ($response['respondent_name'] ?? 'Anonim'),
            'survey'     => $survey,
            'response'   => $response,
            'questions'  => $questions,
            'answers'    => $answers,
        ];

        return view('backend/survey/results/view_response', $data);
    }

    /**
     * Tampilkan form edit response
     */
    public function editResponse(int $responseId)
    {
        $response = $this->responseModel->find($responseId);
        if (!$response) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $survey    = $this->getSurveyOrFail($response['survey_id']);
        $questions = $this->questionModel->getQuestionsBySurvey($response['survey_id']);
        $questionIds = array_column($questions, 'id');
        $optionsMap  = $this->optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }
        unset($q);

        $answers = !empty($response['answers']) ? json_decode($response['answers'], true) : [];

        // Fetch master lists for dynamic selects if any question needs them
        $tpqs = [];
        $gurus = [];
        $santris = [];
        $hasTpq = false;
        $hasGuru = false;
        $hasSantri = false;

        foreach ($questions as $q) {
            if ($q['question_type'] === 'master_tpq') $hasTpq = true;
            if ($q['question_type'] === 'master_guru') $hasGuru = true;
            if ($q['question_type'] === 'master_santri') $hasSantri = true;
        }

        $helpModel = new \App\Models\HelpFunctionModel();
        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };

        // Always load TPQ list because respondent_tpq_id is in metadata
        $tpqsRaw = $helpModel->getDataTpq();
        $namesCount = [];
        foreach ($tpqsRaw as $t) {
            $name = trim($t['NamaTpq']);
            $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
        }
        foreach ($tpqsRaw as $t) {
            $name = $titleCase($t['NamaTpq']);
            if (($namesCount[trim($t['NamaTpq'])] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                $name = $name . ' - ' . $titleCase($t['KelurahanDesa']);
            }
            $tpqs[] = [
                'id' => $t['IdTpq'],
                'name' => $name
            ];
        }

        if ($hasGuru) {
            $gurusRaw = $helpModel->getDataGuru();
            foreach ($gurusRaw as $g) {
                $gurus[] = [
                    'id' => $g['IdGuru'],
                    'name' => $titleCase($g['Nama'])
                ];
            }
        }

        if ($hasSantri) {
            $santrisRaw = $helpModel->getDataSantriStatus(1);
            foreach ($santrisRaw as $s) {
                $santris[] = [
                    'id' => $s['IdSantri'],
                    'name' => $titleCase($s['NamaSantri'])
                ];
            }
        }

        $data = [
            'survey'     => $survey,
            'response'   => $response,
            'questions'  => $questions,
            'answers'    => $answers,
            'tpqs'       => $tpqs,
            'gurus'      => $gurus,
            'santris'    => $santris,
        ];

        return view('backend/survey/results/edit_response', $data);
    }

    /**
     * Simpan update response
     */
    public function updateResponse(int $responseId)
    {
        $response = $this->responseModel->find($responseId);
        if (!$response) {
            return $this->response->setJSON(['success' => false, 'message' => 'Response tidak ditemukan.']);
        }

        $post = $this->request->getPost();
        
        // Metadata fields
        $respondentName  = $post['respondent_name'] ?? null;
        $respondentEmail = $post['respondent_email'] ?? null;
        $respondentPhone = $post['respondent_phone'] ?? null;
        $respondentTpqId = $post['respondent_tpq_id'] ?? null;

        // Process answers
        $rawAnswers = $post['answers'] ?? [];
        $questions = $this->questionModel->getQuestionsBySurvey($response['survey_id']);
        $answers = [];

        // Pre-populate raw answers first
        foreach ($questions as $q) {
            if (in_array($q['question_type'], ['image_display', 'video_display'])) continue;
            $key = 'q_' . $q['id'];
            $val = $rawAnswers[$key] ?? null;

            if ($q['question_type'] === 'file_upload' && empty($val)) {
                $oldAnswers = !empty($response['answers']) ? json_decode($response['answers'], true) : [];
                $val = $oldAnswers[$key] ?? null;
            }

            if ($val !== null && $val !== '' && $val !== []) {
                $answers[$key] = $val;
            }
        }

        // Get active sections dynamically (since admin edit form doesn't send active_sections)
        $activeSections = $this->responseModel->getActiveSections($response['survey_id'], $questions, $answers);

        foreach ($questions as $q) {
            if (in_array($q['question_type'], ['image_display', 'video_display'])) continue;

            $qSectionId = $q['section_id'];
            // Jika pertanyaan berada di dalam suatu bagian, namun bagian tersebut tidak aktif/dikunjungi, abaikan
            if ($qSectionId !== null && !in_array($qSectionId, $activeSections)) {
                $key = 'q_' . $q['id'];
                unset($answers[$key]);
                continue;
            }

            $key = 'q_' . $q['id'];
            $val = $answers[$key] ?? null;

            // Validasi pertanyaan wajib
            if ($q['is_required'] && ($val === null || $val === '' || $val === [])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pertanyaan "' . strip_tags($q['question_text']) . '" wajib diisi.',
                ]);
            }

            // Validasi custom validation_rules jika ada untuk jawaban singkat
            if ($q['question_type'] === 'text_short' && !empty($q['validation_rules']) && $val !== null && $val !== '') {
                $rules = is_string($q['validation_rules']) ? json_decode($q['validation_rules'], true) : $q['validation_rules'];
                $ruleType = $rules['rule_type'] ?? '';
                $condition = $rules['condition'] ?? '';
                $val1 = $rules['value'] ?? '';
                $val2 = $rules['value_2'] ?? '';
                $errorMsg = $rules['error_message'] ?? '';
                if (empty($errorMsg)) {
                    $errorMsg = 'Jawaban tidak valid.';
                }

                $isValid = true;

                if ($ruleType === 'number') {
                    if (!is_numeric($val)) {
                        $isValid = false;
                        $errorMsg = 'Jawaban harus berupa angka.';
                    } else {
                        $num = (float)$val;
                        switch ($condition) {
                            case 'between':
                                $isValid = ($num >= (float)$val1 && $num <= (float)$val2);
                                break;
                            case 'not_between':
                                $isValid = ($num < (float)$val1 || $num > (float)$val2);
                                break;
                            case 'greater_than':
                                $isValid = ($num > (float)$val1);
                                break;
                            case 'greater_than_or_equal':
                                $isValid = ($num >= (float)$val1);
                                break;
                            case 'less_than':
                                $isValid = ($num < (float)$val1);
                                break;
                            case 'less_than_or_equal':
                                $isValid = ($num <= (float)$val1);
                                break;
                            case 'equal':
                                $isValid = ($num == (float)$val1);
                                break;
                            case 'not_equal':
                                $isValid = ($num != (float)$val1);
                                break;
                            case 'is_integer':
                                $isValid = (filter_var($val, FILTER_VALIDATE_INT) !== false);
                                break;
                            case 'is_number':
                                $isValid = true;
                                break;
                        }
                    }
                } elseif ($ruleType === 'text') {
                    switch ($condition) {
                        case 'contains':
                            $isValid = (strpos($val, $val1) !== false);
                            break;
                        case 'not_contains':
                            $isValid = (strpos($val, $val1) === false);
                            break;
                        case 'email':
                            $isValid = (filter_var($val, FILTER_VALIDATE_EMAIL) !== false);
                            break;
                        case 'url':
                            $isValid = (filter_var($val, FILTER_VALIDATE_URL) !== false);
                            break;
                    }
                } elseif ($ruleType === 'length') {
                    $len = mb_strlen($val);
                    switch ($condition) {
                        case 'min_length':
                            $isValid = ($len >= (int)$val1);
                            break;
                        case 'max_length':
                            $isValid = ($len <= (int)$val1);
                            break;
                    }
                }

                if (!$isValid) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Pertanyaan "' . strip_tags($q['question_text']) . '": ' . $errorMsg,
                    ]);
                }
            }
        }

        $updateData = [
            'respondent_name'  => $respondentName,
            'respondent_email' => !empty($respondentEmail) ? $respondentEmail : null,
            'respondent_phone' => !empty($respondentPhone) ? $respondentPhone : null,
            'respondent_tpq_id' => !empty($respondentTpqId) ? $respondentTpqId : null,
            'answers'          => json_encode($answers, JSON_UNESCAPED_UNICODE),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->responseModel->update($responseId, $updateData);

        return $this->response->setJSON(['success' => true, 'message' => 'Tanggapan responden berhasil diperbarui.']);
    }

    /**
     * Hapus satu response
     */
    public function deleteResponse(int $responseId)
    {
        $response = $this->responseModel->find($responseId);
        if (!$response) {
            return $this->response->setJSON(['success' => false, 'message' => 'Response tidak ditemukan.']);
        }

        $this->responseModel->delete($responseId);

        return $this->response->setJSON(['success' => true, 'message' => 'Response berhasil dihapus.']);
    }

    /**
     * Reset semua response untuk survey tertentu
     */
    public function resetResponses(int $surveyId)
    {
        $survey = $this->getSurveyOrFail($surveyId);
        
        // Hapus semua response data
        $this->responseModel->where('survey_id', $surveyId)->delete();
        
        return $this->response->setJSON(['success' => true, 'message' => 'Seluruh respon survey berhasil di-reset.']);
    }

    /**
     * Export ke Excel (menggunakan library native PHP, atau dengan PhpSpreadsheet via Composer jika tersedia)
     * Fallback: CSV export
     */
    public function exportExcel(int $surveyId)
    {
        $survey    = $this->getSurveyOrFail($surveyId);
        $questions = $this->questionModel->getQuestionsBySurvey($surveyId);
        $responses = $this->responseModel->getResponsesForExport($surveyId);

        // Ambil daftar TPQ untuk lookup nama
        $tpqsRaw = $this->db->table('tbl_tpq')->select('IdTpq, NamaTpq, KelurahanDesa')->orderBy('NamaTpq')->get()->getResultArray();
        $namesCount = [];
        foreach ($tpqsRaw as $t) {
            $name = trim($t['NamaTpq']);
            $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
        }
        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };
        $tpqMap = [];
        foreach ($tpqsRaw as $t) {
            $name = $titleCase($t['NamaTpq']);
            if (($namesCount[trim($t['NamaTpq'])] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                $name = $name . ' - ' . $titleCase($t['KelurahanDesa']);
            }
            $tpqMap[$t['IdTpq']] = $name;
        }

        // Buat CSV
        $filename = 'survey_' . $survey['survey_key'] . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8

        // Header row
        $headers = ['No', 'Waktu Submit', 'Nama Responden', 'TPQ', 'Email', 'No HP'];
        foreach ($questions as $q) {
            if (!in_array($q['question_type'], ['image_display', 'video_display'])) {
                $headers[] = trim(html_entity_decode(strip_tags($q['question_text']), ENT_QUOTES, 'UTF-8'));
            }
        }
        fputcsv($output, $headers);

        // Data rows
        foreach ($responses as $index => $response) {
            $answers = $response['answers'];
            $tpqName = isset($tpqMap[$response['respondent_tpq_id']]) ? $tpqMap[$response['respondent_tpq_id']] : 'Lembaga Lain / Publik';
            $row = [
                $index + 1,
                $response['submitted_at'],
                $response['respondent_name'] ?? '-',
                $tpqName,
                $response['respondent_email'] ?? '-',
                $response['respondent_phone'] ?? '-',
            ];

            foreach ($questions as $q) {
                if (in_array($q['question_type'], ['image_display', 'video_display'])) continue;

                $key = 'q_' . $q['id'];
                $answer = $answers[$key] ?? '';

                if (is_array($answer)) {
                    $answer = implode(', ', $answer);
                } elseif (is_object($answer)) {
                    $answer = json_encode($answer, JSON_UNESCAPED_UNICODE);
                }

                $row[] = $answer;
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export summary ke PDF (print view)
     */
    public function exportPdf(int $surveyId)
    {
        $survey    = $this->getSurveyOrFail($surveyId);
        $questions = $this->questionModel->getQuestionsBySurvey($surveyId);
        $questionIds = array_column($questions, 'id');
        $optionsMap  = $this->optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }
        unset($q);

        $summary       = $this->responseModel->getResponseSummary($surveyId);
        $totalResponse = $this->responseModel->countResponsesBySurvey($surveyId);

        $data = [
            'page_title'     => 'Export PDF — ' . $survey['title'],
            'survey'         => $survey,
            'questions'      => $questions,
            'summary'        => $summary,
            'total_response' => $totalResponse,
            'generated_at'   => date('d/m/Y H:i'),
        ];

        return view('backend/survey/results/export_pdf', $data);
    }

    /**
     * API: Data chart per pertanyaan
     */
    public function getChartData(int $surveyId)
    {
        $this->getSurveyOrFail($surveyId);

        $summary   = $this->responseModel->getResponseSummary($surveyId);
        $questions = $this->questionModel->getQuestionsBySurvey($surveyId);
        $totalResponse = $this->responseModel->countResponsesBySurvey($surveyId);
        $dailyData     = $this->responseModel->getResponseCountPerDay($surveyId);

        return $this->response->setJSON([
            'success'        => true,
            'summary'        => $summary,
            'questions'      => $questions,
            'total_response' => $totalResponse,
            'daily_data'     => $dailyData,
        ]);
    }

    // =============================================================
    // Status Pengisian (Filling Status)
    // =============================================================

    /**
     * Halaman status pengisian — siapa sudah & belum mengisi
     */
    public function fillingStatus(int $surveyId)
    {
        $survey     = $this->getSurveyOrFail($surveyId);
        $targetType = $survey['target_type'];

        // Hanya relevan untuk target guru/santri/tpq
        $targetList = [];
        $status     = [];

        if (in_array($targetType, ['guru', 'santri', 'tpq'])) {
            $targetList = $this->targetModel->getTargetListWithStatus($surveyId, $targetType);
            $status     = $this->responseModel->getFillingStatus($surveyId, $targetList);
        }

        // Ambil list TPQ untuk filter
        $tpqsRaw = $this->db->table('tbl_tpq')->select('IdTpq, NamaTpq, KelurahanDesa')->orderBy('NamaTpq')->get()->getResultArray();
        $namesCount = [];
        foreach ($tpqsRaw as $t) {
            $name = trim($t['NamaTpq']);
            $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
        }
        $tpqs = array_map(function($t) use ($namesCount) {
            $name = trim($t['NamaTpq']);
            if (($namesCount[$name] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                $name = $name . ' - ' . trim($t['KelurahanDesa']);
            }
            return [
                'IdTpq'   => $t['IdTpq'],
                'NamaTpq' => $name,
            ];
        }, $tpqsRaw);

        $data = [
            'page_title'  => 'Status Pengisian — ' . $survey['title'],
            'survey'      => $survey,
            'target_type' => $targetType,
            'status'      => $status,
            'tpqs'        => $tpqs,
        ];

        return view('backend/survey/results/filling_status', $data);
    }

    /**
     * API: Get filling status data (AJAX untuk filter/refresh)
     */
    public function getFillingStatusData(int $surveyId)
    {
        $survey     = $this->getSurveyOrFail($surveyId);
        $targetType = $this->request->getGet('target_type') ?? $survey['target_type'];
        $tpqFilter  = $this->request->getGet('tpq_id');

        $targetList = $this->targetModel->getTargetListWithStatus($surveyId, $targetType);

        // Filter per TPQ jika ada
        if ($tpqFilter) {
            $targetList = array_filter($targetList, fn($t) => $t['tpq_id'] == $tpqFilter);
            $targetList = array_values($targetList);
        }

        $status = $this->responseModel->getFillingStatus($surveyId, $targetList);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $status,
        ]);
    }

    /**
     * Halaman setting tampilan hasil publik
     */
    public function publicResultSettings(int $surveyId)
    {
        $survey = $this->getSurveyOrFail($surveyId);

        $data = [
            'page_title' => 'Setting Hasil Publik — ' . $survey['title'],
            'survey'     => $survey,
            'result_url' => base_url("survey/{$survey['survey_key']}/hasil"),
        ];

        return view('backend/survey/results/public_settings', $data);
    }

    /**
     * Simpan setting tampilan hasil publik
     */
    public function savePublicResultSettings(int $surveyId)
    {
        $this->getSurveyOrFail($surveyId);
        $post = $this->request->getPost();

        $surveyModel = new SurveyModel();
        $surveyModel->update($surveyId, [
            'public_result_enabled' => isset($post['public_result_enabled']) ? 1 : 0,
            'public_result_mode'    => $post['public_result_mode'] ?? 'summary',
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Pengaturan hasil publik disimpan.']);
    }

    /**
     * Helper — get survey or throw 404
     */
    private function getSurveyOrFail(int $id): array
    {
        $surveyModel = new SurveyModel();
        $survey = $surveyModel->find($id);
        if (!$survey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Survey tidak ditemukan.');
        }

        if (!in_groups('Admin')) {
            $tpqId = session()->get('IdTpq');
            if ($survey['created_by_tpq_id'] !== $tpqId) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses ditolak.');
            }
        }

        return $survey;
    }
}
