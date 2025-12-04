<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\RaporGroupKategoriModel;
use App\Models\HelpFunctionModel;
use App\Models\MateriPelajaranModel;
use App\Models\ToolsModel;

class RaporGroupKategori extends BaseController
{
    protected $raporGroupKategoriModel;
    protected $helpFunction;
    protected $materiPelajaranModel;
    protected $toolsModel;

    public function __construct()
    {
        $this->raporGroupKategoriModel = new RaporGroupKategoriModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->materiPelajaranModel = new MateriPelajaranModel();
        $this->toolsModel = new ToolsModel();
    }

    public function index()
    {
        // Ambil IdTpq dari session
        $idTpq = session()->get('IdTpq');

        // Get configuration data based on IdTpq
        $configs = $this->raporGroupKategoriModel->getByTpq($idTpq);

        // Get list TPQ untuk dropdown
        $listTpq = $this->helpFunction->getDataTpq(false); // false = ambil semua TPQ

        // Ambil daftar kategori yang ada dari materi pelajaran
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_materi_pelajaran');
        $builder->select('Kategori');
        $builder->distinct();
        $builder->where('Kategori IS NOT NULL');
        $builder->where('Kategori !=', '');
        $builder->orderBy('Kategori', 'ASC');
        $kategoriList = $builder->get()->getResultArray();
        $kategoriOptions = array_column($kategoriList, 'Kategori');

        // Ambil setting GroupKategoriNilai dan GroupKategoriNilaiKelas
        $groupKategoriNilaiEnabled = $this->toolsModel->getSettingAsBool($idTpq, 'GroupKategoriNilai', false);
        $groupKategoriNilaiKelas = $this->toolsModel->getSettingAsString($idTpq, 'GroupKategoriNilaiKelas', '');

        // Cek apakah setting sudah ada di database untuk TPQ ini (bukan dari default)
        $settingGroupKategoriNilai = $this->toolsModel
            ->where('IdTpq', $idTpq)
            ->where('SettingKey', 'GroupKategoriNilai')
            ->first();
        
        $settingGroupKategoriNilaiKelas = $this->toolsModel
            ->where('IdTpq', $idTpq)
            ->where('SettingKey', 'GroupKategoriNilaiKelas')
            ->first();

        // Cek apakah setting sudah ada (untuk enable/disable form)
        $hasSetting = !empty($settingGroupKategoriNilai) || !empty($settingGroupKategoriNilaiKelas);

        // Ambil nama TPQ untuk ID TPQ saat ini
        $namaTpq = '';
        if (!empty($idTpq) && $idTpq !== '0' && $idTpq !== 0) {
            $tpqData = $this->helpFunction->getNamaTpqById($idTpq);
            $namaTpq = $tpqData['NamaTpq'] ?? $idTpq;
        } else {
            $namaTpq = 'Admin';
        }

        // Buat mapping IdTpq ke NamaTpq untuk semua configs
        $tpqMapping = [];
        if (!empty($listTpq)) {
            foreach ($listTpq as $tpq) {
                $tpqMapping[$tpq['IdTpq']] = $tpq['NamaTpq'] ?? $tpq['IdTpq'];
            }
        }
        // Tambahkan default dan 0 ke mapping
        $tpqMapping['default'] = 'Default (Template)';
        $tpqMapping['0'] = 'Admin';

        $data = [
            'page_title' => 'Pengaturan Group Kategori Rapor',
            'configs' => $configs,
            'idTpq' => $idTpq,
            'namaTpq' => $namaTpq,
            'listTpq' => $listTpq,
            'tpqMapping' => $tpqMapping,
            'kategoriOptions' => $kategoriOptions,
            'groupKategoriNilaiEnabled' => $groupKategoriNilaiEnabled,
            'groupKategoriNilaiKelas' => $groupKategoriNilaiKelas,
            'settingGroupKategoriNilai' => $settingGroupKategoriNilai,
            'settingGroupKategoriNilaiKelas' => $settingGroupKategoriNilaiKelas,
            'hasSetting' => $hasSetting
        ];

        return view('backend/rapor/groupKategori/index', $data);
    }

    /**
     * Save group kategori (AJAX)
     */
    public function save()
    {
        try {
            $rules = [
                'IdTpq' => 'required',
                'KategoriAsal' => 'required|max_length[255]',
                'NamaMateriBaru' => 'required|max_length[255]',
                'Status' => 'required|in_list[Aktif,Tidak Aktif]',
                'Urutan' => 'permit_empty|integer'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Validasi permission: Operator hanya bisa menambah untuk TPQ mereka sendiri
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));
            $isOperator = in_groups('Operator');
            $requestedIdTpq = $this->request->getPost('IdTpq');

            if ($isOperator && !$isAdmin) {
                // Operator hanya bisa menambah untuk TPQ mereka sendiri, tidak bisa untuk 'default' atau TPQ lain
                if ($requestedIdTpq !== $sessionIdTpq || $requestedIdTpq === 'default' || $requestedIdTpq === '0') {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda hanya dapat menambah konfigurasi untuk TPQ Anda sendiri'
                    ]);
                }
            }

            // Check if combination IdTpq + KategoriAsal already exists
            $existing = $this->raporGroupKategoriModel
                ->where('IdTpq', $this->request->getPost('IdTpq'))
                ->where('KategoriAsal', $this->request->getPost('KategoriAsal'))
                ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Konfigurasi dengan IdTpq dan KategoriAsal tersebut sudah ada',
                    'duplicate' => true
                ]);
            }

            $data = [
                'IdTpq' => $this->request->getPost('IdTpq'),
                'KategoriAsal' => $this->request->getPost('KategoriAsal'),
                'NamaMateriBaru' => $this->request->getPost('NamaMateriBaru'),
                'Status' => $this->request->getPost('Status'),
                'Urutan' => (int)($this->request->getPost('Urutan') ?? 0)
            ];

            // Log data untuk debugging
            log_message('debug', 'RaporGroupKategori save - Data: ' . json_encode($data));

            // Gunakan insert() untuk data baru
            $insertId = $this->raporGroupKategoriModel->insert($data);
            
            if ($insertId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data group kategori berhasil disimpan',
                    'id' => $insertId
                ]);
            } else {
                $errors = $this->raporGroupKategoriModel->errors();
                log_message('error', 'RaporGroupKategori save - Errors: ' . json_encode($errors));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data',
                    'errors' => $errors
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in save RaporGroupKategori: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update group kategori (AJAX)
     */
    public function update($id)
    {
        try {
            // Check if record exists
            $existing = $this->raporGroupKategoriModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data group kategori tidak ditemukan'
                ]);
            }

            // Validasi permission: Operator hanya bisa update untuk TPQ mereka sendiri
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));
            $isOperator = in_groups('Operator');

            if ($isOperator && !$isAdmin) {
                // Operator hanya bisa update untuk TPQ mereka sendiri, tidak bisa untuk 'default'
                if ($existing['IdTpq'] !== $sessionIdTpq || $existing['IdTpq'] === 'default') {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda hanya dapat mengubah konfigurasi untuk TPQ Anda sendiri'
                    ]);
                }
            }

            $rules = [
                'KategoriAsal' => 'required|max_length[255]',
                'NamaMateriBaru' => 'required|max_length[255]',
                'Status' => 'required|in_list[Aktif,Tidak Aktif]',
                'Urutan' => 'permit_empty|integer'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if combination IdTpq + KategoriAsal already exists (excluding current record)
            $newKategoriAsal = $this->request->getPost('KategoriAsal');
            $duplicate = $this->raporGroupKategoriModel
                ->where('IdTpq', $existing['IdTpq'])
                ->where('KategoriAsal', $newKategoriAsal)
                ->where('id !=', $id)
                ->first();

            if ($duplicate) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Konfigurasi dengan IdTpq dan KategoriAsal tersebut sudah ada'
                ]);
            }

            $data = [
                'KategoriAsal' => $newKategoriAsal,
                'NamaMateriBaru' => $this->request->getPost('NamaMateriBaru'),
                'Status' => $this->request->getPost('Status'),
                'Urutan' => $this->request->getPost('Urutan') ?? 0
            ];

            if ($this->raporGroupKategoriModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data group kategori berhasil diupdate'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->raporGroupKategoriModel->errors()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in update RaporGroupKategori: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete group kategori (AJAX)
     */
    public function delete($id)
    {
        try {
            // Check if record exists
            $existing = $this->raporGroupKategoriModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data group kategori tidak ditemukan'
                ]);
            }

            // Validasi permission: Operator hanya bisa delete untuk TPQ mereka sendiri
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));
            $isOperator = in_groups('Operator');

            if ($isOperator && !$isAdmin) {
                // Operator hanya bisa delete untuk TPQ mereka sendiri, tidak bisa untuk 'default'
                if ($existing['IdTpq'] !== $sessionIdTpq || $existing['IdTpq'] === 'default') {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda hanya dapat menghapus konfigurasi untuk TPQ Anda sendiri'
                    ]);
                }
            }

            if ($this->raporGroupKategoriModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data group kategori berhasil dihapus'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in delete RaporGroupKategori: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save atau update setting GroupKategoriNilai dan GroupKategoriNilaiKelas (AJAX)
     */
    public function saveSetting()
    {
        try {
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));
            $isOperator = in_groups('Operator');

            // Validasi permission
            if (!$isAdmin && !$isOperator) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah setting ini'
                ]);
            }

            $rules = [
                'GroupKategoriNilai' => 'required|in_list[true,false,1,0]',
                'GroupKategoriNilaiKelas' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $idTpq = $this->request->getPost('IdTpq') ?? $sessionIdTpq;
            
            // Validasi: Operator hanya bisa save untuk TPQ mereka sendiri
            if ($isOperator && !$isAdmin) {
                if ($idTpq !== $sessionIdTpq) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda hanya dapat mengubah setting untuk TPQ Anda sendiri'
                    ]);
                }
            }

            $groupKategoriNilai = $this->request->getPost('GroupKategoriNilai');
            $groupKategoriNilaiKelas = $this->request->getPost('GroupKategoriNilaiKelas') ?? '';

            // Convert boolean value
            $groupKategoriNilaiValue = in_array(strtolower($groupKategoriNilai), ['true', '1', 'yes', 'on']) ? 'true' : 'false';

            // Update atau insert GroupKategoriNilai
            $existingGroupKategoriNilai = $this->toolsModel
                ->where('IdTpq', $idTpq)
                ->where('SettingKey', 'GroupKategoriNilai')
                ->first();

            if ($existingGroupKategoriNilai) {
                $this->toolsModel->update($existingGroupKategoriNilai['id'], [
                    'SettingValue' => $groupKategoriNilaiValue
                ]);
            } else {
                $this->toolsModel->insert([
                    'IdTpq' => $idTpq,
                    'SettingKey' => 'GroupKategoriNilai',
                    'SettingValue' => $groupKategoriNilaiValue,
                    'SettingType' => 'boolean',
                    'Description' => 'Aktifkan pengelompokan kategori nilai di rapor'
                ]);
            }

            // Update atau insert GroupKategoriNilaiKelas
            $existingGroupKategoriNilaiKelas = $this->toolsModel
                ->where('IdTpq', $idTpq)
                ->where('SettingKey', 'GroupKategoriNilaiKelas')
                ->first();

            if ($existingGroupKategoriNilaiKelas) {
                $this->toolsModel->update($existingGroupKategoriNilaiKelas['id'], [
                    'SettingValue' => $groupKategoriNilaiKelas
                ]);
            } else {
                $this->toolsModel->insert([
                    'IdTpq' => $idTpq,
                    'SettingKey' => 'GroupKategoriNilaiKelas',
                    'SettingValue' => $groupKategoriNilaiKelas,
                    'SettingType' => 'text',
                    'Description' => 'Daftar kelas yang menggunakan pengelompokan kategori (kosongkan untuk semua kelas, format: TPQ1, TPQ2, MDA1)'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Setting berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in saveSetting RaporGroupKategori: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}

