<?php

namespace App\Controllers\Backend\Survey;

use App\Controllers\BaseController;
use App\Models\Backend\Survey\SurveyModel;
use App\Models\Backend\Survey\SurveySectionModel;
use App\Models\Backend\Survey\SurveyQuestionModel;
use App\Models\Backend\Survey\SurveyOptionModel;
use App\Models\Backend\Survey\SurveyResponseModel;
use App\Models\Backend\Survey\SurveyTargetModel;

class Survey extends BaseController
{
    protected SurveyModel         $surveyModel;
    protected SurveyResponseModel $responseModel;
    protected SurveyTargetModel   $targetModel;

    public function __construct()
    {
        $this->surveyModel   = new SurveyModel();
        $this->responseModel = new SurveyResponseModel();
        $this->targetModel   = new SurveyTargetModel();
    }

    /**
     * Cek apakah user adalah Admin atau Operator
     */
    private function isAdmin(): bool
    {
        return in_groups('Admin');
    }

    /**
     * Dapatkan IdTpq untuk Operator, null untuk Admin
     */
    private function getOperatorTpqId(): ?string
    {
        if ($this->isAdmin()) return null;
        return session()->get('IdTpq');
    }

    /**
     * Dashboard — daftar semua survey
     */
    public function index()
    {
        $tpqId   = $this->getOperatorTpqId();
        $surveys = $this->surveyModel->getSurveyWithStats($tpqId);

        $data = [
            'page_title'     => 'Manajemen Survey',
            'surveys'        => $surveys,
            'total_survey'   => count($surveys),
            'total_active'   => count(array_filter($surveys, fn($s) => $s['status'] === 'active')),
            'total_response' => array_sum(array_column($surveys, 'response_count')),
            'is_admin'       => $this->isAdmin(),
        ];

        return view('backend/survey/index', $data);
    }

    /**
     * Form create survey baru
     */
    public function create()
    {
        $data = [
            'page_title' => 'Buat Survey Baru',
        ];
        return view('backend/survey/create', $data);
    }

    /**
     * Simpan survey baru
     */
    public function store()
    {
        $title = $this->request->getPost('title');
        if (empty($title)) {
            return redirect()->back()->withInput()->with('error', 'Judul survey wajib diisi.');
        }

        $tpqId = $this->getOperatorTpqId();

        $surveyId = $this->surveyModel->insert([
            'title'              => $title,
            'description'        => $this->request->getPost('description'),
            'survey_key'         => $this->surveyModel->generateSurveyKey(),
            'status'             => 'draft',
            'target_type'        => $this->request->getPost('target_type') ?? 'public',
            'theme_color'        => $this->request->getPost('theme_color') ?? '#4285F4',
            'created_by'         => user_id(),
            'created_by_tpq_id'  => $tpqId,
        ]);

        if (!$surveyId) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat survey.');
        }

        return redirect()->to("backend/survey/edit/{$surveyId}")->with('message', 'Survey berhasil dibuat! Sekarang tambahkan pertanyaan.');
    }

    /**
     * Halaman form builder
     */
    public function edit(int $id)
    {
        $survey = $this->getSurveyOrFail($id);

        $data = [
            'page_title' => 'Form Builder — ' . $survey['title'],
            'survey'     => $survey,
            'survey_id'  => $id,
        ];

        return view('backend/survey/builder', $data);
    }

    /**
     * Update metadata survey
     */
    public function update(int $id)
    {
        $this->getSurveyOrFail($id);

        $this->surveyModel->update($id, [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'theme_color' => $this->request->getPost('theme_color'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Survey diperbarui.']);
    }

    /**
     * Hapus survey
     */
    public function delete(int $id)
    {
        $this->getSurveyOrFail($id);

        // Hapus semua data terkait
        $sectionModel  = new SurveySectionModel();
        $questionModel = new SurveyQuestionModel();
        $optionModel   = new SurveyOptionModel();

        // Hapus options -> questions -> sections -> targets -> responses -> survey
        $questions = $questionModel->where('survey_id', $id)->findAll();
        foreach ($questions as $q) {
            $optionModel->deleteByQuestion($q['id']);
        }
        $questionModel->where('survey_id', $id)->delete();
        $sectionModel->where('survey_id', $id)->delete();
        $this->targetModel->where('survey_id', $id)->delete();
        $this->responseModel->where('survey_id', $id)->delete();
        $this->surveyModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Survey berhasil dihapus beserta seluruh data terkait.']);
        }

        return redirect()->to('backend/survey')->with('message', 'Survey berhasil dihapus.');
    }

    /**
     * Duplikasi survey beserta seluruh strukturnya
     */
    public function duplicate(int $id)
    {
        $survey = $this->getSurveyOrFail($id);

        $sectionModel  = new SurveySectionModel();
        $questionModel = new SurveyQuestionModel();
        $optionModel   = new SurveyOptionModel();

        // Buat survey baru
        unset($survey['id']);
        $survey['title']      = $survey['title'] . ' (Salinan)';
        $survey['survey_key'] = $this->surveyModel->generateSurveyKey();
        $survey['status']     = 'draft';
        $survey['created_by'] = user_id();
        $survey['created_at'] = date('Y-m-d H:i:s');
        $survey['updated_at'] = date('Y-m-d H:i:s');

        $newSurveyId = $this->surveyModel->insert($survey);

        // Duplikasi sections
        $sections    = $sectionModel->getSectionsBySurvey($id);
        $sectionMap  = [];
        foreach ($sections as $section) {
            $oldSectionId = $section['id'];
            unset($section['id']);
            $section['survey_id']  = $newSurveyId;
            $section['created_at'] = date('Y-m-d H:i:s');
            $section['updated_at'] = date('Y-m-d H:i:s');
            $newSectionId = $sectionModel->insert($section);
            $sectionMap[$oldSectionId] = $newSectionId;
        }

        // Duplikasi questions & options
        $questions = $questionModel->where('survey_id', $id)->orderBy('sort_order')->findAll();
        foreach ($questions as $question) {
            $oldQuestionId = $question['id'];
            unset($question['id']);
            $question['survey_id']  = $newSurveyId;
            $question['section_id'] = $question['section_id'] ? ($sectionMap[$question['section_id']] ?? null) : null;
            $question['created_at'] = date('Y-m-d H:i:s');
            $question['updated_at'] = date('Y-m-d H:i:s');
            $newQuestionId = $questionModel->insert($question);

            // Duplikasi options
            $options = $optionModel->getOptionsByQuestion($oldQuestionId);
            foreach ($options as $option) {
                unset($option['id']);
                $option['question_id'] = $newQuestionId;
                $option['created_at']  = date('Y-m-d H:i:s');
                $option['updated_at']  = date('Y-m-d H:i:s');
                $optionModel->insert($option);
            }
        }

        return redirect()->to("backend/survey/edit/{$newSurveyId}")->with('message', 'Survey berhasil diduplikasi.');
    }

    /**
     * Toggle status active/inactive
     */
    public function toggleStatus()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID tidak valid.']);
        }

        $this->getSurveyOrFail((int)$id);
        $this->surveyModel->toggleStatus((int)$id);
        $survey = $this->surveyModel->find($id);

        return $this->response->setJSON([
            'success' => true,
            'status'  => $survey['status'],
            'message' => 'Status survey diperbarui.',
        ]);
    }

    /**
     * Preview form survey
     */
    public function preview(int $id)
    {
        $survey        = $this->getSurveyOrFail($id);
        $sectionModel  = new SurveySectionModel();
        $questionModel = new SurveyQuestionModel();
        $optionModel   = new SurveyOptionModel();

        $sections  = $sectionModel->getSectionsBySurvey($id);
        $questions = $questionModel->getQuestionsBySurvey($id);
        $questionIds = array_column($questions, 'id');
        $optionsMap  = $optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }

        $data = [
            'page_title' => 'Preview — ' . $survey['title'],
            'survey'     => $survey,
            'sections'   => $sections,
            'questions'  => $questions,
            'is_preview' => true,
        ];

        return view('backend/survey/preview', $data);
    }

    /**
     * Halaman pengaturan survey
     */
    public function settings(int $id)
    {
        $survey  = $this->getSurveyOrFail($id);
        $targets = $this->targetModel->getTargetsBySurvey($id);

        $baseUrl   = base_url("survey/{$survey['survey_key']}");
        $resultUrl = base_url("survey/{$survey['survey_key']}/hasil");

        $data = [
            'page_title' => 'Pengaturan Survey — ' . $survey['title'],
            'survey'     => $survey,
            'targets'    => $targets,
            'public_url' => $baseUrl,
            'result_url' => $resultUrl,
            'response_count' => $this->responseModel->countResponsesBySurvey($id),
        ];

        return view('backend/survey/settings', $data);
    }

    /**
     * Simpan pengaturan survey
     */
    public function saveSettings(int $id)
    {
        $this->getSurveyOrFail($id);

        $post = $this->request->getPost();

        $updateData = [
            'title'                => $post['title'] ?? null,
            'description'          => $post['description'] ?? null,
            'status'               => $post['status'] ?? 'draft',
            'target_type'          => $post['target_type'] ?? 'public',
            'allow_anonymous'      => isset($post['allow_anonymous']) ? 1 : 0,
            'allow_edit_response'  => isset($post['allow_edit_response']) ? 1 : 0,
            'limit_one_response'   => isset($post['limit_one_response']) ? 1 : 0,
            'unique_field_type'    => $post['unique_field_type'] ?? 'none',
            'unique_field_required'=> isset($post['unique_field_required']) ? 1 : 0,
            'show_progress_bar'    => isset($post['show_progress_bar']) ? 1 : 0,
            'shuffle_questions'    => isset($post['shuffle_questions']) ? 1 : 0,
            'confirmation_message' => $post['confirmation_message'] ?? null,
            'theme_color'          => $post['theme_color'] ?? '#4285F4',
            'start_date'           => !empty($post['start_date']) ? $post['start_date'] : null,
            'end_date'             => !empty($post['end_date']) ? $post['end_date'] : null,
            'max_responses'        => !empty($post['max_responses']) ? (int)$post['max_responses'] : null,
            'public_result_enabled'=> isset($post['public_result_enabled']) ? 1 : 0,
            'public_result_mode'   => $post['public_result_mode'] ?? 'summary',
        ];

        // Hapus null values untuk title/description agar tidak overwrite
        $updateData = array_filter($updateData, fn($v) => $v !== null);

        $this->surveyModel->update($id, $updateData);

        // Simpan targets
        if (!empty($post['targets'])) {
            $targets = [];
            foreach ($post['targets'] as $targetType => $refIds) {
                if (is_array($refIds)) {
                    foreach ($refIds as $refId) {
                        $targets[] = [
                            'target_type'   => $targetType,
                            'target_ref_id' => $refId ?: null,
                        ];
                    }
                } else {
                    $targets[] = [
                        'target_type'   => $targetType,
                        'target_ref_id' => $refIds ?: null,
                    ];
                }
            }
            $this->targetModel->setTargets($id, $targets);
        }

        return redirect()->to("backend/survey/settings/{$id}")->with('message', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Regenerate survey key
     */
    public function regenerateKey(int $id)
    {
        $this->getSurveyOrFail($id);
        $newKey = $this->surveyModel->generateSurveyKey();
        $this->surveyModel->update($id, ['survey_key' => $newKey]);

        return $this->response->setJSON([
            'success'    => true,
            'survey_key' => $newKey,
            'public_url' => base_url("survey/{$newKey}"),
            'result_url' => base_url("survey/{$newKey}/hasil"),
        ]);
    }

    /**
     * Helper — get survey and check access, or throw 404
     */
    private function getSurveyOrFail(int $id): array
    {
        $survey = $this->surveyModel->find($id);
        if (!$survey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Survey tidak ditemukan.');
        }

        // Operator hanya bisa akses survey milik TPQ-nya
        if (!$this->isAdmin()) {
            $tpqId = $this->getOperatorTpqId();
            if ($survey['created_by_tpq_id'] !== $tpqId) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses ditolak.');
            }
        }

        return $survey;
    }
}
