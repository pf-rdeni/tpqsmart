<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\ToolsModel;
use App\Models\HelpFunctionModel;

class Tools extends BaseController
{
    protected $toolsModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->toolsModel = new ToolsModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function index()
    {
        // Ambil IdTpq dari session
        $idTpq = session()->get('IdTpq');

        // Get configuration data based on IdTpq
        // If IdTpq exists and not 0, show default template + IdTpq data
        // If IdTpq = 0 or null (admin), show all
        $tools = $this->toolsModel->getByTpq($idTpq);

        // Get list TPQ untuk dropdown
        $listTpq = $this->helpFunction->getDataTpq(false); // false = ambil semua TPQ

        $data = [
            'page_title' => 'Tools Setting',
            'tools' => $tools,
            'idTpq' => $idTpq,
            'listTpq' => $listTpq
        ];

        return view('backend/tools/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Tools Setting'
        ];

        return view('backend/tools/create', $data);
    }

    public function store()
    {
        $rules = [
            'IdTpq' => 'required|numeric',
            'SettingKey' => 'required|min_length[3]',
            'SettingValue' => 'required',
            'SettingType' => 'required',
            'Description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->toolsModel->insert([
            'IdTpq' => $this->request->getPost('IdTpq'),
            'SettingKey' => $this->request->getPost('SettingKey'),
            'SettingValue' => $this->request->getPost('SettingValue'),
            'SettingType' => $this->request->getPost('SettingType'),
            'Description' => $this->request->getPost('Description')
        ]);

        return redirect()->to('/backend/tools')->with('message', 'Tools Setting berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'page_title' => 'Edit Tools Setting',
            'tool' => $this->toolsModel->find($id)
        ];

        return view('backend/tools/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'IdTpq' => 'required|numeric',
            'SettingKey' => 'required|min_length[3]',
            'SettingValue' => 'required',
            'SettingType' => 'required',
            'Description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->toolsModel->update($id, [
            'IdTpq' => $this->request->getPost('IdTpq'),
            'SettingKey' => $this->request->getPost('SettingKey'),
            'SettingValue' => $this->request->getPost('SettingValue'),
            'SettingType' => $this->request->getPost('SettingType'),
            'Description' => $this->request->getPost('Description')
        ]);

        return redirect()->to('/backend/tools')->with('message', 'Tools Setting berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->toolsModel->delete($id);
        return redirect()->to('/backend/tools')->with('message', 'Tools Setting berhasil dihapus');
    }

    /**
     * Save tools setting (AJAX)
     */
    public function saveTools()
    {
        try {
            $rules = [
                'IdTpq' => 'required',
                'SettingKey' => 'required|min_length[3]',
                'SettingValue' => 'required',
                'SettingType' => 'required|in_list[number,text,boolean,json]',
                'Description' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if combination IdTpq + SettingKey already exists
            $existing = $this->toolsModel
                ->where('IdTpq', $this->request->getPost('IdTpq'))
                ->where('SettingKey', $this->request->getPost('SettingKey'))
                ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Konfigurasi dengan IdTpq dan SettingKey tersebut sudah ada',
                    'duplicate' => true
                ]);
            }

            $data = [
                'IdTpq' => $this->request->getPost('IdTpq'),
                'SettingKey' => $this->request->getPost('SettingKey'),
                'SettingValue' => $this->request->getPost('SettingValue'),
                'SettingType' => $this->request->getPost('SettingType'),
                'Description' => $this->request->getPost('Description') ?? ''
            ];

            if ($this->toolsModel->save($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data tools setting berhasil disimpan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data',
                    'errors' => $this->toolsModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in saveTools: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update tools setting (AJAX)
     */
    public function updateTools($id)
    {
        try {
            // Check if record exists
            $existing = $this->toolsModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tools setting tidak ditemukan'
                ]);
            }

            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));

            $rules = [
                'SettingKey' => $isAdmin ? 'required|min_length[3]' : 'required',
                'SettingValue' => 'required',
                'SettingType' => 'required|in_list[number,text,boolean,json]',
                'Description' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $newSettingKey = $this->request->getPost('SettingKey');
            $settingValue = $this->request->getPost('SettingValue');
            $settingType = $this->request->getPost('SettingType');
            $description = $this->request->getPost('Description') ?? '';

            if (!$isAdmin && $newSettingKey !== $existing['SettingKey']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah Setting Key'
                ]);
            }

            if ($isAdmin && $newSettingKey !== $existing['SettingKey']) {
                $duplicate = $this->toolsModel
                    ->where('IdTpq', $existing['IdTpq'])
                    ->where('SettingKey', $newSettingKey)
                    ->where('id !=', $id)
                    ->first();

                if ($duplicate) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Setting Key tersebut sudah digunakan untuk ID TPQ yang sama'
                    ]);
                }
            }

            if (!$isAdmin && $settingType !== $existing['SettingType']) {
                $settingType = $existing['SettingType'];
            }

            $data = [
                'SettingValue' => $settingValue,
                'SettingType' => $settingType,
                'Description' => $description
            ];

            if ($isAdmin) {
                $data['SettingKey'] = $newSettingKey;
            }

            if ($this->toolsModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data tools setting berhasil diupdate'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->toolsModel->errors()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateTools: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Duplicate tools setting (AJAX)
     */
    public function duplicateTools()
    {
        try {
            $sourceId = $this->request->getPost('source_id');
            $targetIdTpq = $this->request->getPost('IdTpq');
            $requestedSettingKey = $this->request->getPost('SettingKey');
            $settingValue = $this->request->getPost('SettingValue');
            $description = $this->request->getPost('Description') ?? '';

            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));

            // Validate input
            if (empty($sourceId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Source ID tidak boleh kosong'
                ]);
            }

            if (empty($targetIdTpq)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID TPQ tujuan harus diisi'
                ]);
            }

            // Prevent duplicating to 'default'
            if ($targetIdTpq === 'default') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat menduplikasi ke "default". Gunakan ID TPQ lain atau "0" untuk admin.'
                ]);
            }

            // Get source configuration
            $source = $this->toolsModel->find($sourceId);
            if (!$source) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tools setting sumber tidak ditemukan'
                ]);
            }

            // Verify source is from 'default'
            if ($source['IdTpq'] !== 'default') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya konfigurasi dengan IdTpq = "default" yang dapat diduplikasi'
                ]);
            }

            if (!$isAdmin && $requestedSettingKey !== $source['SettingKey']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah Setting Key'
                ]);
            }

            $requestedSettingKey = $requestedSettingKey ?: $source['SettingKey'];

            if ($isAdmin) {
                $requestedSettingKey = trim($requestedSettingKey);
                if ($requestedSettingKey === '') {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Setting Key tidak boleh kosong'
                    ]);
                }
            } else {
                $requestedSettingKey = $source['SettingKey'];
            }

            // Check if configuration already exists for target IdTpq + SettingKey
            $existing = $this->toolsModel
                ->where('IdTpq', $targetIdTpq)
                ->where('SettingKey', $requestedSettingKey)
                ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Konfigurasi dengan IdTpq "' . $targetIdTpq . '" dan SettingKey "' . $requestedSettingKey . '" sudah ada. Silakan edit konfigurasi yang sudah ada.',
                    'duplicate' => true,
                    'existing_id' => $existing['id']
                ]);
            }

            // Create new configuration
            $data = [
                'IdTpq' => $targetIdTpq,
                'SettingKey' => $requestedSettingKey,
                'SettingValue' => !empty($settingValue) ? $settingValue : $source['SettingValue'],
                'SettingType' => $source['SettingType'],
                'Description' => !empty($description) ? $description : $source['Description']
            ];

            if ($this->toolsModel->save($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Konfigurasi berhasil diduplikasi ke IdTpq "' . $targetIdTpq . '"'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menduplikasi data',
                    'errors' => $this->toolsModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in duplicateTools: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete tools setting (AJAX)
     */
    public function deleteTools($id)
    {
        try {
            // Check if record exists
            $existing = $this->toolsModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tools setting tidak ditemukan'
                ]);
            }

            if ($this->toolsModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data tools setting berhasil dihapus'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteTools: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
