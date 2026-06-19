<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\Backend\Survey\SurveyModel;
use App\Models\Backend\Survey\SurveySectionModel;
use App\Models\Backend\Survey\SurveyQuestionModel;
use App\Models\Backend\Survey\SurveyOptionModel;
use App\Models\Backend\Survey\SurveyResponseModel;
use App\Models\Backend\Survey\SurveyTargetModel;
use App\Models\HelpFunctionModel;

class SurveyPublic extends BaseController
{
    protected SurveyModel         $surveyModel;
    protected SurveyResponseModel $responseModel;
    protected SurveyQuestionModel $questionModel;
    protected SurveyOptionModel   $optionModel;
    protected SurveySectionModel  $sectionModel;
    protected SurveyTargetModel   $targetModel;
    protected                     $db;

    public function __construct()
    {
        $this->db            = \Config\Database::connect();
        $this->surveyModel   = new SurveyModel();
        $this->responseModel = new SurveyResponseModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->optionModel   = new SurveyOptionModel();
        $this->sectionModel  = new SurveySectionModel();
        $this->targetModel   = new SurveyTargetModel();
    }

    /**
     * Tampilkan form survey publik
     */
    public function index(string $surveyKey)
    {
        $survey = $this->surveyModel->getSurveyByKey($surveyKey);

        // Survey tidak ditemukan
        if (!$survey) {
            return view('frontend/survey/closed', [
                'page_title' => 'Survey Tidak Ditemukan',
                'reason'     => 'not_found',
                'message'    => 'Link survey tidak valid atau tidak ditemukan.',
            ]);
        }

        // Cek status aktif berdasarkan tanggal
        $statusCheck = $this->surveyModel->checkDateBasedStatus($survey);

        if ($statusCheck !== 'active') {
            $messages = [
                'draft'       => 'Survey ini belum dipublikasikan.',
                'inactive'    => 'Survey ini sudah ditutup.',
                'not_started' => 'Survey ini belum dimulai. Silakan coba lagi nanti.',
                'expired'     => 'Batas waktu pengisian survey sudah berakhir.',
            ];
            return view('frontend/survey/closed', [
                'page_title' => 'Survey Tidak Tersedia',
                'reason'     => $statusCheck,
                'message'    => $messages[$statusCheck] ?? 'Survey tidak tersedia.',
                'survey'     => $survey,
            ]);
        }

        // Cek quota
        $currentCount = $this->responseModel->countResponsesBySurvey($survey['id']);
        if ($this->surveyModel->isQuotaFull($survey['id'], $currentCount)) {
            return view('frontend/survey/closed', [
                'page_title' => 'Survey Penuh',
                'reason'     => 'quota_full',
                'message'    => 'Batas maksimal responden sudah tercapai.',
                'survey'     => $survey,
            ]);
        }

        // Cek duplikasi responden (via session/cookie identifier)
        if ($survey['limit_one_response']) {
            $identifier = $this->getRespondentIdentifier($survey['id']);
            if ($identifier && $this->responseModel->checkDuplicateResponse($survey['id'], $identifier)) {
                return view('frontend/survey/closed', [
                    'page_title' => 'Sudah Mengisi',
                    'reason'     => 'already_submitted',
                    'message'    => 'Anda sudah pernah mengisi survey ini.',
                    'survey'     => $survey,
                    'result_url' => $survey['public_result_enabled'] ? base_url("survey/{$survey['survey_key']}/hasil") : null,
                ]);
            }
        }

        // Ambil data form
        $sections  = $this->sectionModel->getSectionsBySurvey($survey['id']);
        $questions = $this->questionModel->getQuestionsBySurvey($survey['id']);

        if ($survey['shuffle_questions']) {
            shuffle($questions);
        }

        $questionIds = array_column($questions, 'id');
        $optionsMap  = $this->optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }

        $data = [
            'page_title' => $survey['title'],
            'survey'     => $survey,
            'sections'   => $sections,
            'questions'  => $questions,
        ];

        return view('frontend/survey/form', $data);
    }

    /**
     * Submit jawaban survey
     */
    public function submit()
    {
        $surveyKey = $this->request->getPost('survey_key');
        $survey    = $this->surveyModel->getSurveyByKey($surveyKey);

        if (!$survey) {
            return $this->response->setJSON(['success' => false, 'message' => 'Survey tidak ditemukan.']);
        }

        // Validasi status
        $statusCheck = $this->surveyModel->checkDateBasedStatus($survey);
        if ($statusCheck !== 'active') {
            return $this->response->setJSON(['success' => false, 'message' => 'Survey sudah tidak aktif.']);
        }

        // Cek quota
        $currentCount = $this->responseModel->countResponsesBySurvey($survey['id']);
        if ($this->surveyModel->isQuotaFull($survey['id'], $currentCount)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Batas responden sudah tercapai.']);
        }

        // Cek duplikasi via identifier (browser)
        if ($survey['limit_one_response']) {
            $identifier = $this->getRespondentIdentifier($survey['id']);
            if ($identifier && $this->responseModel->checkDuplicateResponse($survey['id'], $identifier)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Anda sudah pernah mengisi survey ini.']);
            }
        }

        // Cek duplikasi via respondent_ref_id (master data)
        $refId = $this->request->getPost('respondent_ref_id');
        if ($survey['limit_one_response'] && !empty($refId) && $survey['target_type'] !== 'public') {
            if ($this->responseModel->checkDuplicateByRefId($survey['id'], $refId)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Identitas Anda terdeteksi sudah pernah mengirim respon untuk survey ini.']);
            }
        }

        // Validasi email/phone unik
        $email = trim($this->request->getPost('respondent_email') ?? '');
        $phone = trim($this->request->getPost('respondent_phone') ?? '');

        if ($survey['unique_field_type'] === 'email' && !empty($email)) {
            if ($this->responseModel->checkDuplicateByEmail($survey['id'], $email)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email ini sudah pernah digunakan untuk mengisi survey.',
                ]);
            }
        }

        if ($survey['unique_field_type'] === 'phone' && !empty($phone)) {
            if ($this->responseModel->checkDuplicateByPhone($survey['id'], $phone)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nomor HP ini sudah pernah digunakan untuk mengisi survey.',
                ]);
            }
        }

        // Validasi field wajib
        if ($survey['unique_field_required']) {
            if ($survey['unique_field_type'] === 'email' && empty($email)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Email wajib diisi.']);
            }
            if ($survey['unique_field_type'] === 'phone' && empty($phone)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nomor HP wajib diisi.']);
            }
        }

        // Proses jawaban
        $post    = $this->request->getPost();
        $answers = [];
        $questions = $this->questionModel->getQuestionsBySurvey($survey['id']);

        // Pre-populate raw answers first for active sections resolution
        foreach ($questions as $q) {
            $key   = 'q_' . $q['id'];
            $value = $post[$key] ?? null;
            if ($value !== null && $value !== '' && $value !== []) {
                $answers[$key] = $value;
            }
        }

        // Get active sections
        $activeSections = $this->request->getPost('active_sections');
        if ($activeSections === null) {
            $activeSections = $this->responseModel->getActiveSections($survey['id'], $questions, $answers);
        } else {
            if (!is_array($activeSections)) {
                $activeSections = [$activeSections];
            }
        }

        foreach ($questions as $q) {
            $qSectionId = $q['section_id'];

            // Jika pertanyaan berada di dalam suatu bagian, namun bagian tersebut tidak aktif/dikunjungi, abaikan pertanyaan ini
            if ($qSectionId !== null && !in_array($qSectionId, $activeSections)) {
                $key = 'q_' . $q['id'];
                unset($answers[$key]);
                continue;
            }

            $key   = 'q_' . $q['id'];
            $value = $answers[$key] ?? null;

            // Validasi pertanyaan wajib
            if ($q['is_required'] && ($value === null || $value === '' || $value === [])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pertanyaan "' . strip_tags($q['question_text']) . '" wajib diisi.',
                    'field'   => $key,
                ]);
            }

            // Validasi custom validation_rules jika ada untuk jawaban singkat
            if ($q['question_type'] === 'text_short' && !empty($q['validation_rules']) && $value !== null && $value !== '') {
                $rules = $q['validation_rules'];
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
                    if (!is_numeric($value)) {
                        $isValid = false;
                        $errorMsg = 'Jawaban harus berupa angka.';
                    } else {
                        $num = (float)$value;
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
                        }
                    }
                } elseif ($ruleType === 'text') {
                    switch ($condition) {
                        case 'contains':
                            $isValid = (strpos($value, $val1) !== false);
                            break;
                        case 'not_contains':
                            $isValid = (strpos($value, $val1) === false);
                            break;
                        case 'email':
                            $isValid = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                            if (!$isValid && empty($rules['error_message'])) {
                                $errorMsg = 'Masukkan alamat email yang valid.';
                            }
                            break;
                        case 'url':
                            $isValid = filter_var($value, FILTER_VALIDATE_URL) !== false;
                            if (!$isValid && empty($rules['error_message'])) {
                                $errorMsg = 'Masukkan URL yang valid.';
                            }
                            break;
                    }
                } elseif ($ruleType === 'length') {
                    $len = mb_strlen($value);
                    switch ($condition) {
                        case 'min_char':
                            $isValid = ($len >= (int)$val1);
                            break;
                        case 'max_char':
                            $isValid = ($len <= (int)$val1);
                            break;
                    }
                }

                if (!$isValid) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $errorMsg,
                        'field'   => $key,
                    ]);
                }
            }
        }

        // Simpan response
        $identifier = $this->generateRespondentIdentifier($survey['id']);

        $responseData = [
            'survey_id'              => $survey['id'],
            'respondent_identifier'  => $identifier,
            'respondent_name'        => $post['respondent_name'] ?? null,
            'respondent_email'       => !empty($email) ? $email : null,
            'respondent_phone'       => !empty($phone) ? $phone : null,
            'respondent_type'        => $this->determineRespondentType($survey['target_type']),
            'respondent_ref_id'      => $post['respondent_ref_id'] ?? null,
            'respondent_tpq_id'      => $post['respondent_tpq_id'] ?? null,
            'answers'                => json_encode($answers, JSON_UNESCAPED_UNICODE),
            'ip_address'             => $this->request->getIPAddress(),
            'user_agent'             => substr($this->request->getUserAgent()->getAgentString(), 0, 500),
            'submitted_at'           => date('Y-m-d H:i:s'),
        ];

        $responseId = $this->responseModel->insert($responseData);

        if (!$responseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan jawaban.']);
        }

        return $this->response->setJSON([
            'success'      => true,
            'message'      => 'Terima kasih! Jawaban Anda berhasil disimpan.',
            'redirect'     => base_url("survey/{$survey['survey_key']}/terima-kasih"),
        ]);
    }

    /**
     * Halaman terima kasih
     */
    public function thankYou(string $surveyKey)
    {
        $survey = $this->surveyModel->getSurveyByKey($surveyKey);
        if (!$survey) {
            return redirect()->to('/');
        }

        $data = [
            'page_title' => 'Terima Kasih!',
            'survey'     => $survey,
            'result_url' => $survey['public_result_enabled'] ? base_url("survey/{$surveyKey}/hasil") : null,
        ];

        return view('frontend/survey/thank_you', $data);
    }

    /**
     * Halaman hasil publik
     */
    public function results(string $surveyKey)
    {
        $survey = $this->surveyModel->getSurveyByKey($surveyKey);

        if (!$survey || !$survey['public_result_enabled']) {
            return view('frontend/survey/closed', [
                'page_title' => 'Hasil Tidak Tersedia',
                'reason'     => 'result_disabled',
                'message'    => 'Halaman hasil survey tidak tersedia atau belum diaktifkan.',
            ]);
        }

        $questions = $this->questionModel->getQuestionsBySurvey($survey['id']);
        $questionIds = array_column($questions, 'id');
        $optionsMap  = $this->optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }

        $totalResponse = $this->responseModel->countResponsesBySurvey($survey['id']);
        $summary       = ($survey['public_result_mode'] === 'detail')
                         ? $this->responseModel->getResponseSummary($survey['id'])
                         : [];

        // Status pengisian (siapa sudah/belum)
        $fillingStatus = [];
        $targetType    = $survey['target_type'];
        if (in_array($targetType, ['guru', 'santri', 'tpq'])) {
            $targetList  = $this->targetModel->getTargetListWithStatus($survey['id'], $targetType);
            $fillingStatus = $this->responseModel->getFillingStatus($survey['id'], $targetList);
        }

        // Daftar TPQ untuk filter
        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };
        $tpqsRaw = $this->db->table('tbl_tpq')->select('IdTpq, NamaTpq, KelurahanDesa')->orderBy('NamaTpq')->get()->getResultArray();
        $namesCount = [];
        foreach ($tpqsRaw as $t) {
            $name = trim($t['NamaTpq']);
            $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
        }
        $tpqs = array_map(function($t) use ($namesCount, $titleCase) {
            $name = $titleCase($t['NamaTpq']);
            if (($namesCount[trim($t['NamaTpq'])] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                $name = $name . ' - ' . $titleCase($t['KelurahanDesa']);
            }
            return [
                'IdTpq'   => $t['IdTpq'],
                'NamaTpq' => $name,
            ];
        }, $tpqsRaw);

        $data = [
            'page_title'     => 'Hasil Survey — ' . $survey['title'],
            'survey'         => $survey,
            'questions'      => $questions,
            'total_response' => $totalResponse,
            'summary'        => $summary,
            'filling_status' => $fillingStatus,
            'target_type'    => $targetType,
            'tpqs'           => $tpqs,
        ];

        return view('frontend/survey/results', $data);
    }

    /**
     * API: Master data untuk cascading dropdown di form publik
     */
    public function getMasterData()
    {
        $type      = $this->request->getPost('type');
        $tpqId     = $this->request->getPost('tpq_id');
        $surveyKey = $this->request->getPost('survey_key');

        $helpModel = new HelpFunctionModel();

        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };

        // Get list of ref_ids that have already responded to this survey
        $filledRefIds = [];
        if ($surveyKey) {
            $survey = $this->surveyModel->getSurveyByKey($surveyKey);
            if ($survey) {
                $responses = $this->responseModel->select('respondent_ref_id')
                                                 ->where('survey_id', $survey['id'])
                                                 ->where('respondent_ref_id IS NOT NULL')
                                                 ->findAll();
                $filledRefIds = array_column($responses, 'respondent_ref_id');
            }
        }

        if ($type === 'tpq') {
            $tpqsRaw = $helpModel->getDataTpq();
            $namesCount = [];
            foreach ($tpqsRaw as $t) {
                $name = trim($t['NamaTpq']);
                $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
            }
            $data = array_map(function($t) use ($namesCount, $titleCase, $filledRefIds) {
                $name = $titleCase($t['NamaTpq']);
                if (($namesCount[trim($t['NamaTpq'])] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                    $name = $name . ' - ' . $titleCase($t['KelurahanDesa']);
                }
                $isFilled = in_array($t['IdTpq'], $filledRefIds);
                return [
                    'id'       => $t['IdTpq'],
                    'name'     => $name . ($isFilled ? ' (Sudah Mengisi ✓)' : ''),
                    'disabled' => $isFilled,
                ];
            }, $tpqsRaw);
        } elseif ($type === 'guru' && $tpqId) {
            $gurus = $helpModel->getDataGuru(false, true, $tpqId);
            $data  = array_map(function($g) use ($titleCase, $filledRefIds) {
                $isFilled = in_array($g['IdGuru'], $filledRefIds);
                return [
                    'id'       => $g['IdGuru'],
                    'name'     => $titleCase($g['Nama']) . ($isFilled ? ' (Sudah Mengisi ✓)' : ''),
                    'disabled' => $isFilled,
                ];
            }, $gurus);
        } elseif ($type === 'santri' && $tpqId) {
            $santris = $helpModel->getDataSantriStatus(1, (int)$tpqId);
            $data    = array_map(function($s) use ($titleCase, $filledRefIds) {
                $isFilled = in_array($s['IdSantri'], $filledRefIds);
                return [
                    'id'       => $s['IdSantri'],
                    'name'     => $titleCase($s['NamaSantri']) . ($isFilled ? ' (Sudah Mengisi ✓)' : ''),
                    'kelas'    => $s['NamaKelas'] ?? '',
                    'disabled' => $isFilled,
                ];
            }, $santris);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter tidak valid.']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * API: Upload file dari responden
     */
    public function uploadFile()
    {
        $surveyKey = $this->request->getPost('survey_key');
        $survey    = $this->surveyModel->getSurveyByKey($surveyKey);
        if (!$survey) {
            return $this->response->setJSON(['success' => false, 'message' => 'Survey tidak valid.']);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid.']);
        }

        // Dapatkan aturan upload dari settings pertanyaan
        $questionId = $this->request->getPost('question_id');
        $question   = $this->questionModel->find($questionId);
        $settings   = $question ? json_decode($question['settings'] ?? '{}', true) : [];

        $allowedTypes = $settings['allowed_types'] ?? 'jpg,jpeg,png,pdf,doc,docx';
        $maxSizeMb    = (int)($settings['max_size_mb'] ?? 5);

        $allowedList = array_map('trim', explode(',', $allowedTypes));
        $ext = strtolower($file->getExtension());

        if (!in_array($ext, $allowedList)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Tipe file tidak diizinkan. Tipe yang diperbolehkan: {$allowedTypes}",
            ]);
        }

        if ($file->getSizeByUnit('mb') > $maxSizeMb) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Ukuran file maksimal {$maxSizeMb}MB.",
            ]);
        }

        $newName  = $file->getRandomName();
        $savePath = FCPATH . "uploads/survey/files/{$survey['survey_key']}/";
        if (!is_dir($savePath)) mkdir($savePath, 0755, true);

        $file->move($savePath, $newName);

        return $this->response->setJSON([
            'success'   => true,
            'file_name' => $newName,
            'file_path' => "uploads/survey/files/{$survey['survey_key']}/{$newName}",
        ]);
    }

    /**
     * API: Cek duplikasi email/phone sebelum submit
     */
    public function checkDuplicate()
    {
        $surveyKey = $this->request->getPost('survey_key');
        $survey    = $this->surveyModel->getSurveyByKey($surveyKey);

        if (!$survey) {
            return $this->response->setJSON(['success' => false, 'is_duplicate' => false]);
        }

        // Cek duplikasi berdasarkan ref_id (dari master data dropdown) jika dikirimkan
        $refId = trim($this->request->getPost('ref_id') ?? '');
        if (!empty($refId) && $survey['limit_one_response']) {
            $isDuplicate = $this->responseModel->checkDuplicateByRefId($survey['id'], $refId);
            return $this->response->setJSON([
                'success'      => true,
                'is_duplicate' => $isDuplicate,
                'message'      => $isDuplicate ? 'Identitas ini sudah pernah mengisi survey ini.' : '',
            ]);
        }

        $type  = $survey['unique_field_type'];
        $value = trim($this->request->getPost('value') ?? '');

        if (empty($value) || $type === 'none') {
            return $this->response->setJSON(['success' => true, 'is_duplicate' => false]);
        }

        $isDuplicate = false;
        if ($type === 'email') {
            $isDuplicate = $this->responseModel->checkDuplicateByEmail($survey['id'], $value);
        } elseif ($type === 'phone') {
            $isDuplicate = $this->responseModel->checkDuplicateByPhone($survey['id'], $value);
        }

        return $this->response->setJSON([
            'success'      => true,
            'is_duplicate' => $isDuplicate,
            'message'      => $isDuplicate ? 'Sudah pernah mengisi survey ini.' : '',
        ]);
    }

    /**
     * API: Dapatkan data status pengisian untuk halaman hasil publik
     */
    public function getPublicFillingStatusData(string $surveyKey)
    {
        $survey = $this->surveyModel->getSurveyByKey($surveyKey);

        if (!$survey || !$survey['public_result_enabled']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Halaman hasil survey tidak tersedia atau belum diaktifkan.',
            ]);
        }

        $targetType = $this->request->getGet('target_type') ?? $survey['target_type'];
        $tpqFilter  = $this->request->getGet('tpq_id');

        $targetList = $this->targetModel->getTargetListWithStatus($survey['id'], $targetType);

        // Filter per TPQ jika ada
        if ($tpqFilter) {
            $targetList = array_filter($targetList, fn($t) => $t['tpq_id'] == $tpqFilter);
            $targetList = array_values($targetList);
        }

        $status = $this->responseModel->getFillingStatus($survey['id'], $targetList);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $status,
        ]);
    }

    // =============================================================
    // Helper Private Methods
    // =============================================================

    /**
     * Generate identifier unik untuk responden (disimpan di cookie)
     */
    private function generateRespondentIdentifier(int $surveyId): string
    {
        $cookieKey  = 'survey_resp_' . $surveyId;
        $identifier = bin2hex(random_bytes(16));

        // Simpan ke cookie 30 hari
        $this->response->setCookie($cookieKey, $identifier, 30 * 24 * 3600);

        return $identifier;
    }

    /**
     * Dapatkan identifier yang sudah ada dari cookie
     */
    private function getRespondentIdentifier(int $surveyId): ?string
    {
        $cookieKey = 'survey_resp_' . $surveyId;
        return $this->request->getCookie($cookieKey);
    }

    /**
     * Tentukan tipe responden berdasarkan target_type survey
     */
    private function determineRespondentType(string $targetType): string
    {
        return match($targetType) {
            'guru'   => 'guru',
            'santri' => 'santri',
            'tpq'    => 'tpq',
            default  => 'public',
        };
    }
}
