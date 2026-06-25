<?php

namespace App\Controllers\Backend\Survey;

use App\Controllers\BaseController;
use App\Models\Backend\Survey\SurveyModel;
use App\Models\Backend\Survey\SurveySectionModel;
use App\Models\Backend\Survey\SurveyQuestionModel;
use App\Models\Backend\Survey\SurveyOptionModel;
use App\Models\HelpFunctionModel;

class SurveyBuilder extends BaseController
{
    protected SurveyModel         $surveyModel;
    protected SurveySectionModel  $sectionModel;
    protected SurveyQuestionModel $questionModel;
    protected SurveyOptionModel   $optionModel;

    public function __construct()
    {
        $this->surveyModel   = new SurveyModel();
        $this->sectionModel  = new SurveySectionModel();
        $this->questionModel = new SurveyQuestionModel();
        $this->optionModel   = new SurveyOptionModel();
    }

    // =============================================================
    // Form Builder Data API
    // =============================================================

    /**
     * GET: Ambil seluruh data form (sections + questions + options)
     */
    public function getFormData(int $surveyId)
    {
        $survey    = $this->surveyModel->find($surveyId);
        if (!$survey) {
            return $this->response->setJSON(['success' => false, 'message' => 'Survey tidak ditemukan.']);
        }

        $sections  = $this->sectionModel->getSectionsBySurvey($surveyId);
        $questions = $this->questionModel->getQuestionsBySurvey($surveyId);
        $questionIds = array_column($questions, 'id');
        $optionsMap  = $this->optionModel->getOptionsForQuestions($questionIds);

        foreach ($questions as &$q) {
            $q['options'] = $optionsMap[$q['id']] ?? [];
        }

        return $this->response->setJSON([
            'success'   => true,
            'survey'    => $survey,
            'sections'  => $sections,
            'questions' => $questions,
        ]);
    }

    // =============================================================
    // Section CRUD
    // =============================================================

    public function saveSection()
    {
        $post      = $this->request->getJSON(true);
        $surveyId  = (int)($post['survey_id'] ?? 0);
        $sectionId = (int)($post['id'] ?? 0);

        if (!$surveyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Survey ID wajib ada.']);
        }

        $data = [
            'survey_id'   => $surveyId,
            'title'       => $post['title'] ?? 'Bagian Baru',
            'description' => $post['description'] ?? null,
        ];

        if (isset($post['sort_order'])) {
            $data['sort_order'] = (int)$post['sort_order'];
        }

        if ($sectionId) {
            $this->sectionModel->update($sectionId, $data);
            $id = $sectionId;
        } else {
            if (!isset($data['sort_order'])) {
                $data['sort_order'] = $this->sectionModel->getNextSortOrder($surveyId);
            }
            $id = $this->sectionModel->insert($data);
        }

        return $this->response->setJSON([
            'success'    => true,
            'id'         => $id,
            'message'    => 'Bagian berhasil disimpan.',
            'section'    => $this->sectionModel->find($id),
        ]);
    }

    public function deleteSection(int $id)
    {
        // Pindahkan questions di section ini ke tanpa section
        $this->questionModel->where('section_id', $id)->set('section_id', null)->update();
        $this->sectionModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Bagian dihapus.']);
    }

    // =============================================================
    // Question CRUD
    // =============================================================

    public function saveQuestion()
    {
        $post       = $this->request->getJSON(true);
        $surveyId   = (int)($post['survey_id'] ?? 0);
        $questionId = (int)($post['id'] ?? 0);

        if (!$surveyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Survey ID wajib ada.']);
        }

        $settings         = $post['settings'] ?? [];
        $validationRules  = $post['validation_rules'] ?? [];

        $data = [
            'survey_id'        => $surveyId,
            'section_id'       => !empty($post['section_id']) ? (int)$post['section_id'] : null,
            'question_text'    => $post['question_text'] ?? 'Pertanyaan Baru',
            'question_type'    => $post['question_type'] ?? 'text_short',
            'is_required'      => !empty($post['is_required']) ? 1 : 0,
            'description'      => $post['description'] ?? null,
            'settings'         => !empty($settings) ? json_encode($settings) : null,
            'validation_rules' => !empty($validationRules) ? json_encode($validationRules) : null,
        ];

        if (isset($post['sort_order'])) {
            $data['sort_order'] = (int)$post['sort_order'];
        }

        if ($questionId) {
            $this->questionModel->update($questionId, $data);
            $id = $questionId;
        } else {
            if (!isset($data['sort_order'])) {
                $data['sort_order'] = $this->questionModel->getNextSortOrder($surveyId);
            }
            $id = $this->questionModel->insert($data);
        }

        // Save options jika ada
        if (isset($post['options'])) {
            $this->optionModel->batchSaveOptions($id, $post['options']);
        }

        $question          = $this->questionModel->find($id);
        $question['options'] = $this->optionModel->getOptionsByQuestion($id);
        $question['settings'] = !empty($question['settings']) ? json_decode($question['settings'], true) : [];

        return $this->response->setJSON([
            'success'  => true,
            'id'       => $id,
            'message'  => 'Pertanyaan berhasil disimpan.',
            'question' => $question,
        ]);
    }

    public function deleteQuestion(int $id)
    {
        $this->optionModel->deleteByQuestion($id);
        $this->questionModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Pertanyaan dihapus.']);
    }

    public function duplicateQuestion(int $id)
    {
        $newId = $this->questionModel->duplicateQuestion($id);

        if (!$newId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menduplikasi pertanyaan.']);
        }

        $question           = $this->questionModel->find($newId);
        $question['options'] = $this->optionModel->getOptionsByQuestion($newId);
        $question['settings'] = !empty($question['settings']) ? json_decode($question['settings'], true) : [];

        return $this->response->setJSON([
            'success'  => true,
            'id'       => $newId,
            'message'  => 'Pertanyaan diduplikasi.',
            'question' => $question,
        ]);
    }

    // =============================================================
    // Reorder
    // =============================================================

    /**
     * Reorder sections dan/atau questions via drag-and-drop
     * Body JSON: { "type": "questions", "items": [{"id": 1, "sort_order": 0, "section_id": null}, ...] }
     */
    public function reorderItems()
    {
        $post = $this->request->getJSON(true);
        $type = $post['type'] ?? '';

        if ($type === 'sections') {
            $this->sectionModel->reorderSections($post['survey_id'], $post['items']);
        } elseif ($type === 'questions') {
            $this->questionModel->reorderQuestions($post['items']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Urutan disimpan.']);
    }

    // =============================================================
    // Options Save (Batch)
    // =============================================================

    public function saveOptions()
    {
        $post       = $this->request->getJSON(true);
        $questionId = (int)($post['question_id'] ?? 0);
        $options    = $post['options'] ?? [];

        if (!$questionId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Question ID wajib ada.']);
        }

        $this->optionModel->batchSaveOptions($questionId, $options);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Opsi berhasil disimpan.',
            'options' => $this->optionModel->getOptionsByQuestion($questionId),
        ]);
    }

    // =============================================================
    // Image Upload
    // =============================================================

    public function uploadImage()
    {
        $file = $this->request->getFile('image');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid.']);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya file gambar yang diizinkan.']);
        }

        if ($file->getSizeByUnit('mb') > 5) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 5MB.']);
        }

        $newName  = $file->getRandomName();
        $savePath = FCPATH . 'uploads/survey/images/';
        if (!is_dir($savePath)) mkdir($savePath, 0755, true);

        $file->move($savePath, $newName);

        return $this->response->setJSON([
            'success'   => true,
            'file_name' => $newName,
            'url'       => base_url("uploads/survey/images/{$newName}"),
        ]);
    }

    // =============================================================
    // Master Data API (untuk cascading dropdown di form builder)
    // =============================================================

    public function getMasterTpq()
    {
        $helpModel = new HelpFunctionModel();
        $tpqsRaw = $helpModel->getDataTpq();

        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };

        $namesCount = [];
        foreach ($tpqsRaw as $t) {
            $name = trim($t['NamaTpq']);
            $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
        }

        $list = array_map(function($t) use ($namesCount, $titleCase) {
            $name = $titleCase($t['NamaTpq']);
            if (($namesCount[trim($t['NamaTpq'])] ?? 0) > 1 && !empty($t['KelurahanDesa'])) {
                $name = $name . ' - ' . $titleCase($t['KelurahanDesa']);
            }
            return [
                'id'   => $t['IdTpq'],
                'name' => $name,
            ];
        }, $tpqsRaw);

        return $this->response->setJSON(['success' => true, 'data' => $list]);
    }

    public function getMasterGuru(string $tpqId = '0')
    {
        $helpModel = new HelpFunctionModel();
        $gurus = $helpModel->getDataGuru(false, true, $tpqId === '0' ? null : $tpqId);

        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };

        $list = array_map(fn($g) => [
            'id'     => $g['IdGuru'],
            'name'   => $titleCase($g['Nama']),
            'tpq_id' => $g['IdTpq'],
        ], $gurus);

        return $this->response->setJSON(['success' => true, 'data' => $list]);
    }

    public function getMasterSantri(string $tpqId = '0')
    {
        $helpModel = new HelpFunctionModel();
        $IdTahunAjaran = session()->get('IdTahunAjaran') ?? $helpModel->getTahunAjaranSaatIni();
        $santris = $helpModel->getDataSantriStatus(1, $tpqId === '0' ? 0 : (int)$tpqId, 0, $IdTahunAjaran);

        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };

        $list = array_map(fn($s) => [
            'id'     => $s['IdSantri'],
            'name'   => $titleCase($s['NamaSantri']),
            'tpq_id' => $s['IdTpq'],
            'kelas'  => $s['NamaKelas'] ?? '',
        ], $santris);

        return $this->response->setJSON(['success' => true, 'data' => $list]);
    }
}
