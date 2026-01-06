<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\Backend\Perlombaan\LombaMasterModel;
use App\Models\Backend\Perlombaan\LombaCabangModel;
use App\Models\Backend\Perlombaan\LombaKriteriaModel;
use App\Models\Backend\Perlombaan\LombaPesertaModel;
use App\Models\Backend\Perlombaan\LombaJuriModel;
use App\Models\Backend\Perlombaan\LombaNilaiModel;
use App\Models\Backend\Perlombaan\LombaRegistrasiModel;
use App\Models\Backend\Perlombaan\LombaRegistrasiAnggotaModel;
use App\Models\Backend\Perlombaan\LombaJuriKriteriaModel;
use App\Models\Backend\Perlombaan\LombaSertifikatTemplateModel;
use App\Models\Backend\Perlombaan\LombaSertifikatFieldModel;
use App\Models\SantriBaruModel;
use App\Models\HelpFunctionModel;

class Perlombaan extends BaseController
{
    protected $lombaMasterModel;
    protected $lombaCabangModel;
    protected $lombaKriteriaModel;
    protected $lombaPesertaModel;
    protected $lombaJuriModel;
    protected $lombaNilaiModel;
    protected $lombaRegistrasiModel;
    protected $lombaRegistrasiAnggotaModel;
    protected $sertifikatTemplateModel;
    protected $sertifikatFieldModel;
    protected $santriModel;
    protected $helpFunction;
    protected $db;

    public function __construct()
    {
        $this->lombaMasterModel = new LombaMasterModel();
        $this->lombaCabangModel = new LombaCabangModel();
        $this->lombaKriteriaModel = new LombaKriteriaModel();
        $this->lombaPesertaModel = new LombaPesertaModel();
        $this->lombaJuriModel = new LombaJuriModel();
        $this->lombaNilaiModel = new LombaNilaiModel();
        $this->lombaRegistrasiModel = new LombaRegistrasiModel();
        $this->lombaRegistrasiAnggotaModel = new LombaRegistrasiAnggotaModel();
        $this->sertifikatTemplateModel = new LombaSertifikatTemplateModel();
        $this->sertifikatFieldModel = new LombaSertifikatFieldModel();
        $this->santriModel = new SantriBaruModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->db = \Config\Database::connect();
    }

    // ==================== DASHBOARD & STATS ====================

    /**
     * Dashboard Perlombaan untuk Admin dan Operator
     */
    public function dashboard()
    {
        $idTpq = session()->get('IdTpq');
        $isAdmin = in_groups('Admin');
        
        // 1. Get Global Stats
        $stats = $this->lombaMasterModel->getGlobalStats($idTpq);
        
        // 2. Get Active Lomba List with Detailed Stats
        if ($isAdmin) {
            $lombaList = $this->lombaMasterModel->getLombaListDetailed(null, 'aktif');
        } else {
            $lombaList = $this->lombaMasterModel->getLombaListDetailed($idTpq, 'aktif');
        }
        
        foreach ($lombaList as &$lomba) {
            $stats_lomba = $this->lombaMasterModel->getLombaWithStats($lomba['id']);
            $lomba['total_cabang'] = $stats_lomba['total_cabang'] ?? 0;
            $lomba['total_peserta'] = $stats_lomba['total_peserta'] ?? 0;
        }

        $data = [
            'page_title' => 'Dashboard Perlombaan',
            'stats'      => $stats,
            'lomba_list' => $lombaList,
        ];

        return view('backend/Perlombaan/dashboard', $data);
    }

    /**
     * Halaman Panduan Penggunaan Perlombaan
     */
    public function panduan()
    {
        $data = [
            'page_title' => 'Panduan Penggunaan Perlombaan',
        ];

        return view('backend/Perlombaan/panduan', $data);
    }

    /**
     * Dashboard - Menampilkan daftar semua lomba yang aktif
     */
    public function index()
    {
        $idTpq = session()->get('IdTpq');
        $isAdmin = in_groups('Admin');

        if ($isAdmin) {
            $lombaList = $this->lombaMasterModel->orderBy('TanggalMulai', 'DESC')->findAll();
        } else {
            $lombaList = $this->lombaMasterModel->getLombaByTpq($idTpq);
        }

        // Tambahkan statistik untuk setiap lomba
        foreach ($lombaList as &$lomba) {
            $lomba = $this->lombaMasterModel->getLombaWithStats($lomba['id']);
        }

        $data = [
            'page_title' => 'Daftar Perlombaan',
            'lomba_list' => $lombaList,
        ];

        return view('backend/Perlombaan/index', $data);
    }

    /**
     * Menampilkan form tambah/edit lomba
     */
    public function listLomba($id = null)
    {
        $lomba = null;
        if ($id !== null) {
            $lomba = $this->lombaMasterModel->find($id);
        }

        // Get TPQ list for Admin selection
        $tpqList = [];
        if (in_groups('Admin')) {
            $tpqModel = new \App\Models\TpqModel();
            $tpqList = $tpqModel->findAll();
        }

        $data = [
            'page_title' => $lomba ? 'Edit Lomba' : 'Tambah Lomba Baru',
            'lomba' => $lomba,
            'tpq_list' => $tpqList,
        ];

        return view('backend/Perlombaan/listLomba', $data);
    }

    /**
     * Simpan lomba baru
     */
    public function storeLomba()
    {
        $rules = [
            'NamaLomba' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'NamaLomba'      => $this->request->getPost('NamaLomba'),
            'Deskripsi'      => $this->request->getPost('Deskripsi'),
            'TanggalMulai'   => $this->request->getPost('TanggalMulai'),
            'TanggalSelesai' => $this->request->getPost('TanggalSelesai'),
            'IdTpq'          => in_groups('Admin') ? ($this->request->getPost('IdTpq') ?: null) : session()->get('IdTpq'),
            'IdTahunAjaran'  => $this->helpFunction->getTahunAjaranSaatIni(),
            'Status'         => $this->request->getPost('Status') ?: 'aktif',
        ];

        if ($this->lombaMasterModel->insert($data)) {
            return redirect()->to(base_url('backend/perlombaan'))->with('success', 'Lomba berhasil ditambahkan');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan lomba');
    }

    /**
     * Update data lomba
     */
    public function updateLomba($id)
    {
        $lomba = $this->lombaMasterModel->find($id);
        if (!$lomba) {
            return redirect()->to(base_url('backend/perlombaan'))->with('error', 'Lomba tidak ditemukan');
        }

        $data = [
            'NamaLomba'      => $this->request->getPost('NamaLomba'),
            'Deskripsi'      => $this->request->getPost('Deskripsi'),
            'TanggalMulai'   => $this->request->getPost('TanggalMulai'),
            'TanggalSelesai' => $this->request->getPost('TanggalSelesai'),
            'IdTpq'          => in_groups('Admin') ? ($this->request->getPost('IdTpq') ?: null) : session()->get('IdTpq'),
            'Status'         => $this->request->getPost('Status'),
        ];

        if ($this->lombaMasterModel->update($id, $data)) {
            return redirect()->to(base_url('backend/perlombaan'))->with('success', 'Lomba berhasil diupdate');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mengupdate lomba');
    }

    /**
     * Hapus lomba
     */
    public function deleteLomba($id)
    {
        if ($this->lombaMasterModel->delete($id)) {
            return redirect()->to(base_url('backend/perlombaan'))->with('success', 'Lomba berhasil dihapus');
        }

        return redirect()->back()->with('error', 'Gagal menghapus lomba');
    }

    // ==================== METHOD CABANG ====================

    /**
     * Kelola cabang untuk lomba
     */
    public function setCabang($lombaId)
    {
        $lomba = $this->lombaMasterModel->find($lombaId);
        if (!$lomba) {
            return redirect()->to(base_url('backend/perlombaan'))->with('error', 'Lomba tidak ditemukan');
        }

        $cabangList = $this->lombaCabangModel->getCabangByLomba($lombaId);

        // Statistik sudah diambil via getCabangByLomba model logic


        $data = [
            'page_title'   => 'Setting Cabang - ' . $lomba['NamaLomba'],
            'lomba'        => $lomba,
            'cabang_list'  => $cabangList,
        ];

        return view('backend/Perlombaan/setCabang', $data);
    }

    /**
     * Simpan cabang baru (AJAX)
     */
    public function storeCabang()
    {
        $data = [
            'lomba_id'   => $this->request->getPost('lomba_id'),
            'NamaCabang' => $this->request->getPost('NamaCabang'),
            'Kategori'   => $this->request->getPost('Kategori') ?: 'Campuran',
            'Tipe'       => $this->request->getPost('Tipe') ?: 'Individu',
            'UsiaMin'    => $this->request->getPost('Batasan') === 'Kelas' ? 0 : ($this->request->getPost('UsiaMin') ?: 5),
            'UsiaMax'    => $this->request->getPost('Batasan') === 'Kelas' ? 0 : ($this->request->getPost('UsiaMax') ?: 18),
            'KelasMin'   => $this->request->getPost('Batasan') === 'Kelas' ? $this->request->getPost('KelasMin') : null,
            'KelasMax'   => $this->request->getPost('Batasan') === 'Kelas' ? $this->request->getPost('KelasMax') : null,
            'MaxPeserta' => $this->request->getPost('MaxPeserta') ?: 0,
            'MaxPerTpq'  => $this->request->getPost('MaxPerTpq') ?: 0,
        ];

        if ($this->lombaCabangModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil ditambahkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menambahkan cabang']);
    }

    /**
     * Update cabang (AJAX)
     */
    public function updateCabang($id)
    {
        $data = [
            'NamaCabang' => $this->request->getPost('NamaCabang'),
            'Kategori'   => $this->request->getPost('Kategori'),
            'Tipe'       => $this->request->getPost('Tipe'),
            'UsiaMin'    => $this->request->getPost('Batasan') === 'Kelas' ? 0 : $this->request->getPost('UsiaMin'),
            'UsiaMax'    => $this->request->getPost('Batasan') === 'Kelas' ? 0 : $this->request->getPost('UsiaMax'),
            'KelasMin'   => $this->request->getPost('Batasan') === 'Kelas' ? $this->request->getPost('KelasMin') : null,
            'KelasMax'   => $this->request->getPost('Batasan') === 'Kelas' ? $this->request->getPost('KelasMax') : null,
            'MaxPeserta' => $this->request->getPost('MaxPeserta') ?: 0,
            'MaxPerTpq'  => $this->request->getPost('MaxPerTpq') ?: 0,
            'Status'     => $this->request->getPost('Status') ?: 'aktif',
        ];

        if ($this->lombaCabangModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil diupdate']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupdate cabang']);
    }

    /**
     * Hapus cabang (AJAX)
     */
    public function deleteCabang($id)
    {
        if ($this->lombaCabangModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus cabang']);
    }

    // ==================== METHOD KRITERIA ====================

    /**
     * Kelola kriteria untuk cabang
     */
    public function setKriteria($cabangId)
    {
        $cabang = $this->lombaCabangModel->getCabangWithStats($cabangId);
        if (!$cabang) {
            return redirect()->to(base_url('backend/perlombaan'))->with('error', 'Cabang tidak ditemukan');
        }

        $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
        $kriteriaList = $this->lombaKriteriaModel->getKriteriaByCabang($cabangId);
        $totalBobot = $this->lombaKriteriaModel->getTotalBobot($cabangId);

        $data = [
            'page_title'    => 'Setting Kriteria - ' . $cabang['NamaCabang'],
            'lomba'         => $lomba,
            'cabang'        => $cabang,
            'kriteria_list' => $kriteriaList,
            'total_bobot'   => $totalBobot,
        ];

        return view('backend/Perlombaan/setKriteria', $data);
    }

    /**
     * Simpan kriteria baru (AJAX)
     */
    public function storeKriteria()
    {
        $data = [
            'cabang_id'    => $this->request->getPost('cabang_id'),
            'NamaKriteria' => $this->request->getPost('NamaKriteria'),
            'Bobot'        => $this->request->getPost('Bobot') ?: 0,
            'NilaiMin'     => $this->request->getPost('NilaiMin') ?: 0,
            'NilaiMax'     => $this->request->getPost('NilaiMax') ?: 100,
            'Urutan'       => $this->request->getPost('Urutan') ?: 0,
        ];

        if ($this->lombaKriteriaModel->insert($data)) {
            $totalBobot = $this->lombaKriteriaModel->getTotalBobot($data['cabang_id']);
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kriteria berhasil ditambahkan',
                'total_bobot' => $totalBobot
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menambahkan kriteria']);
    }

    /**
     * Update kriteria (AJAX)
     */
    public function updateKriteria($id)
    {
        $kriteria = $this->lombaKriteriaModel->find($id);
        if (!$kriteria) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kriteria tidak ditemukan']);
        }

        $data = [
            'NamaKriteria' => $this->request->getPost('NamaKriteria'),
            'Bobot'        => $this->request->getPost('Bobot'),
            'NilaiMin'     => $this->request->getPost('NilaiMin'),
            'NilaiMax'     => $this->request->getPost('NilaiMax'),
            'Urutan'       => $this->request->getPost('Urutan'),
        ];

        if ($this->lombaKriteriaModel->update($id, $data)) {
            $totalBobot = $this->lombaKriteriaModel->getTotalBobot($kriteria['cabang_id']);
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kriteria berhasil diupdate',
                'total_bobot' => $totalBobot
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupdate kriteria']);
    }

    /**
     * Hapus kriteria (AJAX)
     */
    public function deleteKriteria($id)
    {
        $kriteria = $this->lombaKriteriaModel->find($id);
        $cabangId = $kriteria['cabang_id'] ?? null;

        if ($this->lombaKriteriaModel->delete($id)) {
            $totalBobot = $cabangId ? $this->lombaKriteriaModel->getTotalBobot($cabangId) : 0;
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kriteria berhasil dihapus',
                'total_bobot' => $totalBobot
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus kriteria']);
    }

    // ==================== METHOD JURI ====================

    /**
     * Kelola juri untuk cabang
     */
    public function setJuri($cabangId = null)
    {
        $cabang = null;
        $lomba = null;
        $juriList = [];

        if ($cabangId !== null) {
            $cabang = $this->lombaCabangModel->find($cabangId);
            if ($cabang) {
                $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
                $juriList = $this->lombaJuriModel->getJuriByCabang($cabangId, null);
                
                // Add kriteria info for each juri
                $juriKriteriaModel = new LombaJuriKriteriaModel();
                $totalKriteria = $this->lombaKriteriaModel->where('cabang_id', $cabangId)->countAllResults();
                
                foreach ($juriList as &$juri) {
                    $kriteriaCount = $juriKriteriaModel->countKriteriaForJuri($juri['id']);
                    $juri['kriteria_count'] = $kriteriaCount;
                    $juri['kriteria_total'] = $totalKriteria;
                    $juri['kriteria_custom'] = $kriteriaCount > 0;
                }
            }
        }

        // Ambil semua lomba untuk dropdown (filter by TPQ for operators)
        $idTpq = session()->get('IdTpq');
        $isAdmin = in_groups('Admin');
        
        if ($isAdmin) {
            // Admin: ambil semua lomba dengan info TPQ
            $builder = $this->db->table('tbl_lomba_master l');
            $builder->select('l.*, t.NamaTpq, t.KelurahanDesa');
            $builder->join('tbl_tpq t', 't.IdTpq = l.IdTpq', 'left');
            $builder->where('l.Status', 'Aktif');
            $builder->orderBy('l.TanggalMulai', 'DESC');
            $lombaList = $builder->get()->getResultArray();
        } else {
            // Khusus setting juri, operator hanya melihat lomba milik TPQ sendiri
            $builder = $this->db->table('tbl_lomba_master l');
            $builder->select('l.*, t.NamaTpq, t.KelurahanDesa');
            $builder->join('tbl_tpq t', 't.IdTpq = l.IdTpq', 'left');
            $builder->where('l.IdTpq', $idTpq);
            $builder->orderBy('l.TanggalMulai', 'DESC');
            $lombaList = $builder->get()->getResultArray();
        }

        // Auto-select first lomba if available and no cabang selected
        if ($lomba === null && !empty($lombaList)) {
            $lomba = $lombaList[0];
        }

        $data = [
            'page_title' => 'Setting Juri Perlombaan',
            'cabang' => $cabang,
            'lomba' => $lomba,
            'lomba_list' => $lombaList,
            'juri_list' => $juriList
        ];

        return view('backend/Perlombaan/setJuri', $data);
    }

    /**
     * Ambil daftar cabang berdasarkan lomba (AJAX)
     */
    public function getCabangByLomba($lombaId)
    {
        $cabangList = $this->lombaCabangModel->getCabangByLomba($lombaId);
        return $this->response->setJSON(['success' => true, 'data' => $cabangList]);
    }

    /**
     * Simpan juri baru (AJAX) - dengan auto-create user account
     */
    public function storeJuri()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $username = $this->request->getPost('UsernameJuri');
        $namaJuri = $this->request->getPost('NamaJuri');
        $password = $this->request->getPost('PasswordJuri') ?: 'JuriLombaTpqSmart';

        // Cek apakah sudah ditugaskan
        if ($this->lombaJuriModel->isUsernameAssigned($username, $cabangId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Username sudah ditugaskan ke cabang ini']);
        }

        // Cek apakah username sudah ada di users
        $existingUser = $this->db->table('users')->where('username', $username)->get()->getRow();
        if ($existingUser) {
            return $this->response->setJSON(['success' => false, 'message' => 'Username sudah digunakan di sistem user']);
        }

        $this->db->transStart();

        try {
            // Simpan data juri
            $data = [
                'IdJuri'       => $this->lombaJuriModel->generateIdJuri(),
                'cabang_id'    => $cabangId,
                'UsernameJuri' => $username,
                'NamaJuri'     => $namaJuri,
                'Status'       => 'Aktif',
            ];

            if (!$this->lombaJuriModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data juri');
            }

            // Create user in MyAuth
            $email = $username . '@jurolomba.tpqsmart.com';
            $passwordHash = \Myth\Auth\Password::hash($password);
            
            if (!$passwordHash) {
                throw new \Exception('Gagal membuat hash password');
            }

            // Insert ke users table
            $userData = [
                'username'      => $username,
                'email'         => $email,
                'password_hash' => $passwordHash,
                'active'        => 1
            ];

            if (!$this->db->table('users')->insert($userData)) {
                throw new \Exception('Gagal menyimpan user');
            }

            $userId = $this->db->insertID();

            // Insert ke auth_groups_users table (JuriLomba group_id = 9)
            $groupData = [
                'group_id' => 9,
                'user_id'  => $userId,
            ];

            if (!$this->db->table('auth_groups_users')->insert($groupData)) {
                throw new \Exception('Gagal menambahkan user ke group JuriLomba');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Juri berhasil ditambahkan dengan akun user: ' . $username
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Generate username juri lomba (AJAX)
     * Format: Juri[NamaCabangSingkat][3DigitTPQ]_[Urutan]
     */
    public function generateUsernameJuriLomba()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $idTpq = $this->request->getPost('IdTpq') ?: '';

        $cabang = $this->lombaCabangModel->find($cabangId);
        if (!$cabang) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cabang tidak ditemukan']);
        }

        // Ambil kata pertama dari nama cabang dan singkatkan
        $namaCabang = $cabang['NamaCabang'];
        $words = explode(' ', $namaCabang);
        $cabangSingkat = ucfirst(strtolower($words[0])); // Ambil kata pertama

        // Buat prefix berdasarkan TPQ
        $tpqSuffix = '';
        if (!empty($idTpq) && $idTpq !== '0') {
            // Ambil 3 digit terakhir dari IdTpq
            $tpqSuffix = substr($idTpq, -3);
        }

        $baseUsername = 'Juri' . $cabangSingkat . $tpqSuffix;

        // Cari nomor urut terakhir
        $sequence = 1;
        $lastJuri = $this->db->table('users')
            ->like('username', $baseUsername . '_', 'after')
            ->orderBy('username', 'DESC')
            ->get()
            ->getRow();

        if ($lastJuri && preg_match('/_(\d+)$/', $lastJuri->username, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        $generatedUsername = $baseUsername . '_' . $sequence;

        return $this->response->setJSON([
            'success' => true,
            'username' => $generatedUsername
        ]);
    }

    /**
     * Update data juri (AJAX)
     */
    public function updateJuri()
    {
        $id = $this->request->getPost('id');
        $namaJuri = $this->request->getPost('NamaJuri');
        $status = $this->request->getPost('Status');
        $newPassword = $this->request->getPost('NewPassword');

        $juri = $this->lombaJuriModel->find($id);
        if (!$juri) {
            return $this->response->setJSON(['success' => false, 'message' => 'Juri tidak ditemukan']);
        }

        $this->db->transStart();

        try {
            // Update data juri
            $juriData = [
                'NamaJuri' => $namaJuri,
                'Status' => $status,
            ];

            if (!$this->lombaJuriModel->update($id, $juriData)) {
                throw new \Exception('Gagal mengupdate data juri');
            }

            // Reset password jika diisi
            if (!empty($newPassword)) {
                $user = $this->db->table('users')->where('username', $juri['UsernameJuri'])->get()->getRow();
                if ($user) {
                    $passwordHash = \Myth\Auth\Password::hash($newPassword);
                    if (!$this->db->table('users')->where('id', $user->id)->update(['password_hash' => $passwordHash])) {
                        throw new \Exception('Gagal mereset password');
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            $message = 'Data juri berhasil diupdate';
            if (!empty($newPassword)) {
                $message .= ' dan password direset';
            }

            return $this->response->setJSON(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Hapus juri (AJAX)
     */
    public function deleteJuri($id)
    {
        $juri = $this->lombaJuriModel->find($id);
        
        if (!$juri) {
            return $this->response->setJSON(['success' => false, 'message' => 'Juri tidak ditemukan']);
        }

        $this->db->transStart();

        try {
            // Hapus setting kriteria juri terlebih dahulu
            $juriKriteriaModel = new LombaJuriKriteriaModel();
            $juriKriteriaModel->clearKriteriaForJuri($id);

            // Hapus dari tbl_lomba_juri
            if (!$this->lombaJuriModel->delete($id)) {
                throw new \Exception('Gagal menghapus data juri');
            }

            // Hapus user terkait jika ada
            $user = $this->db->table('users')->where('username', $juri['UsernameJuri'])->get()->getRow();
            if ($user) {
                // Hapus dari auth_groups_users
                $this->db->table('auth_groups_users')->where('user_id', $user->id)->delete();
                // Hapus dari users
                $this->db->table('users')->where('id', $user->id)->delete();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Juri dan akun user berhasil dihapus']);

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Cek impact sebelum hapus juri (AJAX)
     */
    public function checkJuriImpact($id)
    {
        $juri = $this->lombaJuriModel->find($id);
        
        if (!$juri) {
            return $this->response->setJSON(['success' => false, 'message' => 'Juri tidak ditemukan']);
        }

        $impact = [];
        
        // Cek setting kriteria
        $juriKriteriaModel = new LombaJuriKriteriaModel();
        $kriteriaCount = $juriKriteriaModel->countKriteriaForJuri($id);
        if ($kriteriaCount > 0) {
            $impact[] = "<li><i class='fas fa-cog text-info'></i> {$kriteriaCount} setting kriteria penilaian</li>";
        }
        
        // Cek nilai yang sudah diinput
        $nilaiCount = $this->lombaNilaiModel->where('IdJuri', $juri['IdJuri'])->countAllResults();
        if ($nilaiCount > 0) {
            $impact[] = "<li><i class='fas fa-star text-warning'></i> {$nilaiCount} data nilai penilaian</li>";
        }
        
        // Cek user account
        $user = $this->db->table('users')->where('username', $juri['UsernameJuri'])->get()->getRow();
        if ($user) {
            $impact[] = "<li><i class='fas fa-user text-primary'></i> Akun user: <strong>{$juri['UsernameJuri']}</strong></li>";
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'juri' => $juri,
                'impact' => $impact,
                'has_impact' => count($impact) > 0
            ]
        ]);
    }

    /**
     * Ambil daftar TPQ untuk dropdown (AJAX)
     */
    public function getTpqList()
    {
        $tpqList = $this->helpFunction->getDataTpq();

        return $this->response->setJSON([
            'success' => true,
            'data' => $tpqList
        ]);
    }

    /**
     * Ambil daftar kelas untuk dropdown setup cabang (AJAX)
     */
    public function getKelasList()
    {
        $db = \Config\Database::connect();
        $kelas = $db->table('tbl_kelas')
            ->orderBy('NamaKelas', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $kelas]);
    }

    // ==================== METHOD PERINGKAT ====================

    /**
     * Lihat peringkat/pemenang untuk cabang
     */
    public function peringkat($cabangId = null)
    {
        $cabang = null;
        $lomba = null;
        $ranking = [];

        if ($cabangId !== null) {
            $cabang = $this->lombaCabangModel->getCabangWithStats($cabangId);
            if ($cabang) {
                $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
                $ranking = $this->lombaNilaiModel->getPeringkat($cabangId);
            }
        }

        // Ambil semua lomba untuk dropdown (filter by TPQ for operators)
        $idTpq = session()->get('IdTpq');
        $lombaList = $this->lombaMasterModel->getLombaListDetailed(in_groups('Admin') ? null : $idTpq);

        $data = [
            'page_title' => 'Peringkat Lomba',
            'lomba'      => $lomba,
            'cabang'     => $cabang,
            'cabang_id'  => $cabangId,
            'ranking'    => $ranking,
            'lomba_list' => $lombaList,
        ];

        return view('backend/Perlombaan/peringkat', $data);
    }

    /**
     * Ambil data peringkat (AJAX)
     */
    public function getPeringkatData()
    {
        $cabangId = $this->request->getPost('cabang_id');
        
        if (!$cabangId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cabang ID diperlukan']);
        }

        $ranking = $this->lombaNilaiModel->getPeringkat($cabangId);
        
        return $this->response->setJSON(['success' => true, 'data' => $ranking]);
    }

    // ==================== METHOD OPERATOR ====================

    /**
     * Pendaftaran peserta untuk cabang
     */
    public function pendaftaran($cabangId = null)
    {
        $cabang = null;
        $lomba = null;
        $pesertaList = [];

        if ($cabangId !== null) {
            $cabang = $this->lombaCabangModel->getCabangWithStats($cabangId);
            if ($cabang) {
                $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
                
                // Cek role user
                $isAdmin = in_groups('Admin');
                $idTpq = session()->get('IdTpq'); // Bisa null jika Admin
                
                if ($isAdmin) {
                    // Admin lihat semua
                    $pesertaList = $this->lombaPesertaModel->getPesertaListByCabang($cabangId);
                } else {
                    // Operator hanya lihat TPQ nya sendiri
                    // Pastikan IdTpq ada
                    if ($idTpq) {
                         $pesertaList = $this->lombaPesertaModel->getPesertaListByCabang($cabangId, null, $idTpq);
                    } else {
                        // Jika tidak ada IdTpq (misal error session), kosongkan
                        $pesertaList = [];
                    }
                }
            }
        }

        // Ambil daftar lomba untuk dropdown (filter by TPQ for operators)
        $idTpq = session()->get('IdTpq');
        $lombaList = $this->lombaMasterModel->getLombaListDetailed(in_groups('Admin') ? null : $idTpq);

        // Hitung kuota terpakai TPQ ini (jika ada MaxPerTpq)
        $quotaUsed = 0;
        if ($cabang && !empty($cabang['MaxPerTpq']) && $cabang['MaxPerTpq'] > 0 && $idTpq) {
            $tipePeserta = $cabang['Tipe'] ?? ($cabang['TipePeserta'] ?? 'Individu');
            $quotaUsed = $this->lombaPesertaModel->getQuotaUsedByTpq($cabangId, $idTpq, $tipePeserta);
        }

        $data = [
            'page_title'   => 'Pendaftaran Peserta',
            'lomba'        => $lomba,
            'cabang'       => $cabang,
            'cabang_id'    => $cabangId,
            'peserta_list' => $pesertaList,
            'lomba_list'   => $lombaList,
            'quota_used'   => $quotaUsed,
            'tpq_list'     => in_groups('Admin') ? $this->helpFunction->getDataTpq() : [],
            'is_admin'     => in_groups('Admin'),
        ];

        return view('backend/Perlombaan/pendaftaran', $data);
    }

    /**
     * Ambil daftar santri untuk pendaftaran (AJAX)
     */
    public function getSantriForRegistration()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $idTpq = session()->get('IdTpq');

        if (in_groups('Admin')) {
            $filterTpq = $this->request->getPost('filterTpq');
            if (!empty($filterTpq)) {
                $idTpq = $filterTpq;
            } else {
                return $this->response->setJSON(['success' => true, 'data' => []]);
            }
        }

        // Ambil data cabang untuk filter
        $cabang = $this->lombaCabangModel->find($cabangId);
        if (!$cabang) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cabang tidak ditemukan']);
        }

        // Ambil santri dari SantriModel
        $santriList = $this->santriModel->getListSantriBaru($idTpq);

        // Filter santri yang sudah terdaftar
        $registeredSantri = $this->lombaPesertaModel
            ->where('cabang_id', $cabangId)
            ->findColumn('IdSantri') ?? [];

        // Prepare Class Order if Class Limit is Active
        $kelasOrder = [];
        $useClassLimit = ($cabang['KelasMin'] && $cabang['KelasMin'] != 0) || ($cabang['KelasMax'] && $cabang['KelasMax'] != 0);
        
        if ($useClassLimit) {
             $db = \Config\Database::connect();
             $kelasList = $db->table('tbl_kelas')->orderBy('NamaKelas', 'ASC')->get()->getResultArray();
             foreach ($kelasList as $index => $k) {
                 $kelasOrder[$k['IdKelas']] = $index;
             }
        }

        $availableSantri = [];
        $addedIds = []; // Untuk menghilangkan duplikat
        
        foreach ($santriList as $santri) {
            $santriArray = (array) $santri;
            $idSantri = $santriArray['IdSantri'];
            
            // Skip jika sudah terdaftar atau sudah ada di list (duplikat)
            if (in_array($idSantri, $registeredSantri) || in_array($idSantri, $addedIds)) {
                continue;
            }

            // Filter berdasarkan jenis kelamin cabang
            $jenisKelamin = strtoupper($santriArray['JenisKelamin'] ?? '');
            if ($cabang['Kategori'] === 'Putra' && $jenisKelamin !== 'LAKI-LAKI') {
                continue;
            }
            if ($cabang['Kategori'] === 'Putri' && $jenisKelamin !== 'PEREMPUAN') {
                continue;
            }

            // Filter Limit: Kelas vs Usia
            if ($useClassLimit) {
                // Logic Filter Kelas
                $santriKelasId = $santriArray['IdKelas'] ?? null;
                
                // Jika santri tidak punya kelas, skip
                if (!$santriKelasId) continue;

                $santriIndex = $kelasOrder[$santriKelasId] ?? -1;
                
                // Tentukan Min Max Index
                $minKelasId = $cabang['KelasMin'];
                $maxKelasId = $cabang['KelasMax'];
                
                $minIndex = isset($kelasOrder[$minKelasId]) ? $kelasOrder[$minKelasId] : -1;
                $maxIndex = isset($kelasOrder[$maxKelasId]) ? $kelasOrder[$maxKelasId] : 99999;

                // Jika batas min diset tapi tidak ketemu di list order, skip logic min? 
                // Asumsi: jika diset harus valid. Jika tidak valid (-1), dianggap tidak lolos jika min required.
                // Tapi user mungkin set min=0 (tidak ada).
                if ($cabang['KelasMin'] && $minIndex == -1) {
                    // Config error or class deleted, fail safe
                }

                // Cek Range
                // Logic: 
                // Jika Min diset: SantriIndex harus >= MinIndex
                // Jika Max diset: SantriIndex harus <= MaxIndex
                
                if ($cabang['KelasMin'] && $santriIndex < $minIndex) continue;
                if ($cabang['KelasMax'] && $santriIndex > $maxIndex) continue;

            } else {
                // Logic Filter Usia (Default)
                $tanggalLahir = $santriArray['TanggalLahirSantri'] ?? null;
                if ($tanggalLahir && !empty($tanggalLahir)) {
                    try {
                        $birthDate = new \DateTime($tanggalLahir);
                        $now = new \DateTime();
                        $usia = $now->diff($birthDate)->y;
                        
                        // Cek range usia
                        // Validasi hanya jika UsiaMin/Max diset > 0
                        if ($cabang['UsiaMin'] > 0 && $usia < $cabang['UsiaMin']) continue;
                        if ($cabang['UsiaMax'] > 0 && $usia > $cabang['UsiaMax']) continue;

                    } catch (\Exception $e) {
                         // Ignore date error
                    }
                }
            }

            $availableSantri[] = $santriArray;
            $addedIds[] = $idSantri;
        }

        // Hitung Quota Info
        $quotaInfo = null;
        if (!empty($cabang['MaxPerTpq']) && $cabang['MaxPerTpq'] > 0 && $idTpq) {
             $tipePeserta = $cabang['Tipe'] ?? ($cabang['TipePeserta'] ?? 'Individu');
             $used = $this->lombaPesertaModel->getQuotaUsedByTpq($cabangId, $idTpq, $tipePeserta);
             $quotaInfo = [
                 'used' => $used,
                 'max' => $cabang['MaxPerTpq'],
                 'remaining' => max(0, $cabang['MaxPerTpq'] - $used)
             ];
        }

        return $this->response->setJSON([
            'success' => true, 
            'data' => $availableSantri,
            'quota_info' => $quotaInfo
        ]);
    }

    /**
     * Daftarkan peserta (AJAX)
     */
    public function registerPeserta()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $idSantri = $this->request->getPost('IdSantri');

        // Ambil data santri untuk validasi
        $santri = $this->santriModel->getDetailSantri($idSantri);
        if (!$santri) {
            return $this->response->setJSON(['success' => false, 'message' => 'Santri tidak ditemukan']);
        }

        // Validasi kelayakan
        $validation = $this->lombaCabangModel->validatePeserta($cabangId, $santri);
        if (!$validation['valid']) {
            return $this->response->setJSON(['success' => false, 'message' => $validation['message']]);
        }

        // Cek apakah sudah terdaftar
        if ($this->lombaPesertaModel->isAlreadyRegistered($idSantri, $cabangId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Santri sudah terdaftar di cabang ini']);
        }

        // Ambil info cabang
        $cabang = $this->lombaCabangModel->find($cabangId);
        
        // Tentukan ID TPQ
        if (in_groups('Admin')) {
            $idTpq = $this->request->getPost('filterTpq');
            if (empty($idTpq)) {
                $idTpq = is_object($santri) ? ($santri->IdTpq ?? null) : ($santri['IdTpq'] ?? null);
            }
        } else {
            $idTpq = session()->get('IdTpq');
            // Validasi kepemilikan
            $santriTpq = is_object($santri) ? $santri->IdTpq : $santri['IdTpq'];
            if ($santriTpq != $idTpq) {
                return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses ke santri ini']);
            }
        }

        // Validasi kuota per TPQ (jika diset)
        if (!empty($cabang['MaxPerTpq']) && $cabang['MaxPerTpq'] > 0 && $idTpq) {
            $tipePeserta = $cabang['Tipe'] ?? ($cabang['TipePeserta'] ?? 'Individu');
            $quotaUsed = $this->lombaPesertaModel->getQuotaUsedByTpq($cabangId, $idTpq, $tipePeserta);
            
            if ($quotaUsed >= $cabang['MaxPerTpq']) {
                $label = strtolower($tipePeserta) === 'kelompok' ? 'kelompok' : 'peserta';
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => "Kuota {$label} untuk TPQ Anda sudah penuh ({$cabang['MaxPerTpq']} {$label})"
                ]);
            }
        }

        // Determine if this is group registration
        $tipePeserta = $cabang['Tipe'] ?? ($cabang['TipePeserta'] ?? 'Individu');
        $isKelompok = strtolower($tipePeserta) === 'kelompok';

        // For group registration, get or create group info
        $grupUrut = null;
        $namaGrup = null;
        if ($isKelompok) {
            // Check if there's an active group session or create new one
            $grupUrut = $this->request->getPost('GrupUrut');
            if (empty($grupUrut)) {
                // New group - get next urut
                $grupUrut = $this->lombaPesertaModel->getNextGrupUrut($cabangId, $idTpq);
            }
            $namaGrup = 'Grup ' . $grupUrut;
        }

        $data = [
            'NoPeserta'          => $this->lombaPesertaModel->generateNoPeserta($cabangId),
            'lomba_id'           => $cabang['lomba_id'],
            'cabang_id'          => $cabangId,
            'IdSantri'           => $idSantri,
            'IdTpq'              => $idTpq,
            'StatusPendaftaran'  => 'valid',
            'TipePendaftaran'    => $isKelompok ? 'kelompok' : 'individu',
            'NamaGrup'           => $namaGrup,
            'GrupUrut'           => $grupUrut,
        ];

        try {
            if ($this->lombaPesertaModel->insert($data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Peserta berhasil didaftarkan', 'NoPeserta' => $data['NoPeserta']]);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mendaftarkan peserta']);
        } catch (\Exception $e) {
            // Tangkap error duplikat atau error lainnya
            $errorMessage = 'Gagal mendaftarkan peserta';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage = 'Nomor peserta sudah digunakan. Silakan coba lagi.';
            }
            return $this->response->setJSON(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * Daftarkan kelompok peserta sekaligus (AJAX)
     * Semua santri yang dipilih akan masuk ke grup yang sama
     */
    public function registerGroupPeserta()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $santriIds = $this->request->getPost('santri_ids'); // Array of santri IDs

        if (empty($santriIds) || !is_array($santriIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada santri yang dipilih']);
        }

        // Ambil info cabang
        $cabang = $this->lombaCabangModel->find($cabangId);
        if (!$cabang) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cabang tidak ditemukan']);
        }

        if (in_groups('Admin')) {
            $idTpq = $this->request->getPost('filterTpq');
            if (empty($idTpq)) {
                // Untuk Admin, ambil IdTpq dari santri pertama yang dipilih
                $firstSantri = $this->santriModel->getDetailSantri($santriIds[0]);
                $idTpq = $firstSantri['IdTpq'] ?? null;
            }
            if (!$idTpq) {
                 return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengidentifikasi TPQ Santri']);
            }
        } else {
            $idTpq = session()->get('IdTpq');
        }

        $tipePeserta = $cabang['Tipe'] ?? ($cabang['TipePeserta'] ?? 'Individu');

        // Validasi kuota per TPQ
        if (!empty($cabang['MaxPerTpq']) && $cabang['MaxPerTpq'] > 0 && $idTpq) {
            $quotaUsed = $this->lombaPesertaModel->countGroupsByTpq($cabangId, $idTpq);
            if ($quotaUsed >= $cabang['MaxPerTpq']) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => "Kuota kelompok untuk TPQ Anda sudah penuh ({$cabang['MaxPerTpq']} kelompok)"
                ]);
            }
        }

        // Generate grup baru
        $grupUrut = $this->lombaPesertaModel->getNextGrupUrut($cabangId, $idTpq);
        $namaGrup = 'Grup ' . $grupUrut;

        $successCount = 0;
        $failedSantri = [];

        foreach ($santriIds as $idSantri) {
            // Ambil data santri untuk validasi
            $santri = $this->santriModel->getDetailSantri($idSantri);
            if (!$santri) {
                $failedSantri[] = $idSantri;
                continue;
            }

            // Validasi kelayakan
            $validation = $this->lombaCabangModel->validatePeserta($cabangId, $santri);
            if (!$validation['valid']) {
                $failedSantri[] = $idSantri;
                continue;
            }

            // Cek apakah sudah terdaftar
            if ($this->lombaPesertaModel->isAlreadyRegistered($idSantri, $cabangId)) {
                $failedSantri[] = $idSantri;
                continue;
            }

            $data = [
                'NoPeserta'          => $this->lombaPesertaModel->generateNoPeserta($cabangId),
                'lomba_id'           => $cabang['lomba_id'],
                'cabang_id'          => $cabangId,
                'IdSantri'           => $idSantri,
                'IdTpq'              => $idTpq,
                'StatusPendaftaran'  => 'valid',
                'TipePendaftaran'    => 'kelompok',
                'NamaGrup'           => $namaGrup,
                'GrupUrut'           => $grupUrut,
            ];

            try {
                if ($this->lombaPesertaModel->insert($data)) {
                    $successCount++;
                } else {
                    $failedSantri[] = $idSantri;
                }
            } catch (\Exception $e) {
                $failedSantri[] = $idSantri;
            }
        }

        if ($successCount > 0) {
            $message = "Berhasil mendaftarkan {$successCount} anggota ke {$namaGrup}";
            if (count($failedSantri) > 0) {
                $message .= ", " . count($failedSantri) . " gagal";
            }
            return $this->response->setJSON(['success' => true, 'message' => $message, 'NamaGrup' => $namaGrup]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mendaftarkan kelompok']);
    }

    /**
     * Batalkan pendaftaran peserta (AJAX)
     */
    public function cancelPeserta($id)
    {
        if ($this->lombaPesertaModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Pendaftaran berhasil dibatalkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal membatalkan pendaftaran']);
    }

    /**
     * Batalkan pendaftaran seluruh grup (AJAX)
     */
    public function cancelGroup()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $idTpq = $this->request->getPost('id_tpq');
        $grupUrut = $this->request->getPost('grup_urut');

        if (empty($cabangId) || empty($grupUrut)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data grup tidak lengkap']);
        }

        // Delete all participants in the group
        $builder = $this->lombaPesertaModel->where('cabang_id', $cabangId)
                                            ->where('GrupUrut', $grupUrut);
        
        if (!empty($idTpq)) {
            $builder->where('IdTpq', $idTpq);
        }

        $deleted = $builder->delete();

        if ($deleted) {
            return $this->response->setJSON(['success' => true, 'message' => 'Seluruh anggota grup berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus grup']);
    }

    /**
     * Update/ganti peserta dengan santri lain (AJAX)
     */
    public function updatePeserta()
    {
        $id = $this->request->getPost('id');
        $newSantriId = $this->request->getPost('new_santri_id');

        if (empty($id) || empty($newSantriId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        // Get existing participant record
        $peserta = $this->lombaPesertaModel->find($id);
        if (!$peserta) {
            return $this->response->setJSON(['success' => false, 'message' => 'Peserta tidak ditemukan']);
        }

        // Check if new santri is already registered in this branch
        if ($this->lombaPesertaModel->isAlreadyRegistered($newSantriId, $peserta['cabang_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Santri pengganti sudah terdaftar di cabang ini']);
        }

        // Validate new santri eligibility
        $newSantri = $this->santriModel->getDetailSantri($newSantriId);
        if (!$newSantri) {
            return $this->response->setJSON(['success' => false, 'message' => 'Santri pengganti tidak ditemukan']);
        }

        $validation = $this->lombaCabangModel->validatePeserta($peserta['cabang_id'], $newSantri);
        if (!$validation['valid']) {
            return $this->response->setJSON(['success' => false, 'message' => $validation['message']]);
        }

        // Update the participant record with new santri
        try {
            $this->lombaPesertaModel->update($id, [
                'IdSantri' => $newSantriId
            ]);
            return $this->response->setJSON(['success' => true, 'message' => 'Peserta berhasil diganti']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengganti peserta']);
        }
    }

    /**
     * Lihat hasil penilaian
     */
    public function viewHasil($cabangId = null)
    {
        $cabang = null;
        $lomba = null;
        $hasilList = [];

        if ($cabangId !== null) {
            $cabang = $this->lombaCabangModel->find($cabangId);
            if ($cabang) {
                $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
                $hasilList = $this->lombaNilaiModel->getPeringkat($cabangId);
            }
        }

        // Ambil daftar lomba untuk dropdown (filter by TPQ for operators)
        $idTpq = session()->get('IdTpq');
        if (in_groups('Admin')) {
            // Admin: semua lomba (TPQ + Umum)
            $lombaList = $this->lombaMasterModel->getLombaListDetailed();
        } else {
            // Operator: HANYA lomba milik TPQ sendiri (tanpa lomba umum)
            $lombaList = $this->lombaMasterModel->getLombaListDetailed($idTpq, 'aktif', false);
        }

        $data = [
            'page_title' => 'Hasil Penilaian',
            'lomba'      => $lomba,
            'cabang'     => $cabang,
            'cabang_id'  => $cabangId,
            'hasil_list' => $hasilList,
            'lomba_list' => $lombaList,
        ];

        return view('backend/Perlombaan/viewHasil', $data);
    }


    // ==================== METHOD INPUT NILAI JURI ====================

    /**
     * Dashboard untuk Juri
     */
    public function dashboardLombaJuri()
    {
        $usernameJuri = user()->username;
        $juriData = $this->lombaJuriModel->getJuriByUsername($usernameJuri);

        if (!$juriData) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak terdaftar sebagai juri lomba');
        }

        // Ambil statistik
        // Ambil statistik
        $cabangId = $juriData['cabang_id'];
        $tipe = $juriData['Tipe'] ?? 'Individu';
        
        if (strcasecmp($tipe, 'Kelompok') === 0) {
            $totalPeserta = $this->lombaPesertaModel
                ->where('cabang_id', $cabangId)
                ->where('StatusPendaftaran', 'valid')
                ->groupBy(['IdTpq', 'GrupUrut'])
                ->countAllResults();
        } else {
            $totalPeserta = $this->lombaPesertaModel
                ->where('cabang_id', $cabangId)
                ->where('StatusPendaftaran', 'valid')
                ->countAllResults();
        }

        $pesertaSudahDinilai = count($this->lombaNilaiModel->getRegistrasiScoredByJuri($juriData['IdJuri'], $cabangId));

        $data = [
            'page_title'              => 'Dashboard Juri Lomba',
            'juri_data'               => $juriData,
            'total_peserta'           => $totalPeserta,
            'peserta_sudah_dinilai'   => $pesertaSudahDinilai,
            'peserta_belum_dinilai'   => $totalPeserta - $pesertaSudahDinilai,
        ];

        return view('backend/Perlombaan/dashboardLombaJuri', $data);
    }

    /**
     * Input nilai juri - form 2 langkah
     */
    public function inputNilaiJuri()
    {
        $usernameJuri = user()->username;
        $juriData = $this->lombaJuriModel->getJuriByUsername($usernameJuri);

        if (!$juriData) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak terdaftar sebagai juri lomba');
        }

        // Ambil registrasi yang baru dinilai
        $recentScored = $this->lombaNilaiModel->getRegistrasiScoredByJuri($juriData['IdJuri'], $juriData['cabang_id']);

        $data = [
            'page_title'     => 'Input Nilai Lomba',
            'juri_data'      => $juriData,
            'recent_scored'  => array_slice($recentScored, 0, 5),
        ];

        return view('backend/Perlombaan/inputNilaiJuri', $data);
    }

    /**
     * Cek peserta untuk input nilai (AJAX) - Langkah 1
     * Sekarang mengambil dari tabel registrasi, bukan peserta
     */
    public function cekPeserta()
    {
        $noPeserta = $this->request->getPost('noPeserta');
        $idJuri = $this->request->getPost('IdJuri');
        $cabangIdJuri = $this->request->getPost('cabang_id'); // Cabang ID dari juri yang login

        // Ambil info cabang juri untuk pesan error yang lebih detail
        $cabangJuri = $this->lombaCabangModel->getCabangWithLomba($cabangIdJuri);
        $infoCabang = $cabangJuri 
            ? "Lomba: {$cabangJuri['NamaLomba']}, Cabang: {$cabangJuri['NamaCabang']}, Tipe: " . ($cabangJuri['Tipe'] ?? 'Individu')
            : "Cabang ID: {$cabangIdJuri}";

        // Cari di tabel registrasi (yang sudah diundi)
        $registrasi = $this->lombaRegistrasiModel->getByNoPesertaWithSantri($noPeserta, $cabangIdJuri);

        if (!$registrasi) {
            $errorHtml = '<div class="text-left">' .
                '<p><strong>Peserta dengan nomor "' . esc($noPeserta) . '" tidak ditemukan.</strong></p>' .
                '<hr>' .
                '<p class="mb-1"><strong>Detail Juri:</strong></p>' .
                '<ul class="pl-3 mb-2">' .
                    '<li>ID Juri: ' . esc($idJuri) . '</li>' .
                    '<li>' . esc($infoCabang) . '</li>' .
                '</ul>' .
                '<p class="mb-1"><strong>Pastikan:</strong></p>' .
                '<ol class="pl-3 mb-0">' .
                    '<li>Peserta sudah diundi untuk cabang ini</li>' .
                    '<li>Nomor peserta diinput dengan benar</li>' .
                    '<li>Peserta terdaftar di cabang yang sama</li>' .
                '</ol>' .
            '</div>';
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorHtml,
                'html' => true
            ]);
        }

        // Cek apakah sudah dinilai (menggunakan registrasi_id)
        $alreadyScored = $this->lombaNilaiModel->checkRegistrasiAlreadyScored(
            $registrasi['id'], 
            $idJuri, 
            $registrasi['cabang_id']
        );

        // Ambil juri untuk mendapatkan primary key ID
        $juri = $this->lombaJuriModel->where('IdJuri', $idJuri)->first();
        $juriPrimaryId = $juri ? $juri['id'] : null;

        // Ambil kriteria untuk cabang ini (difilter berdasarkan setting juri jika ada)
        if ($juriPrimaryId) {
            $kriteria = $this->lombaKriteriaModel->getKriteriaForJuri($juriPrimaryId, $registrasi['cabang_id']);
        } else {
            // Fallback ke semua kriteria jika juri tidak ditemukan
            $kriteria = $this->lombaKriteriaModel->getKriteriaByCabang($registrasi['cabang_id']);
        }

        // Ambil nilai yang sudah ada jika ada
        $existingNilai = [];
        if ($alreadyScored) {
            $nilaiData = $this->lombaNilaiModel
                ->where('registrasi_id', $registrasi['id'])
                ->where('IdJuri', $idJuri)
                ->findAll();
            foreach ($nilaiData as $n) {
                $existingNilai[$n['kriteria_id']] = $n;
            }
        }

        return $this->response->setJSON([
            'success'        => true,
            'data'           => [
                'registrasi'     => $registrasi,
                'kriteria'       => $kriteria,
                'already_scored' => $alreadyScored,
                'existing_nilai' => $existingNilai,
            ]
        ]);
    }

    /**
     * Simpan nilai juri (AJAX) - Langkah 2
     * Sekarang menggunakan registrasi_id
     */
    public function simpanNilai()
    {
        $registrasiId = $this->request->getPost('registrasi_id');
        $cabangId = $this->request->getPost('cabang_id');
        $idJuri = $this->request->getPost('IdJuri');
        $nilaiData = $this->request->getPost('nilai');
        $isEdit = $this->request->getPost('isEdit') === 'true';

        if (empty($registrasiId) || empty($cabangId) || empty($idJuri) || empty($nilaiData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap. registrasi_id: ' . ($registrasiId ?? 'null') . 
                             ', cabang_id: ' . ($cabangId ?? 'null') . 
                             ', IdJuri: ' . ($idJuri ?? 'null') .
                             ', nilai: ' . (is_array($nilaiData) ? count($nilaiData) : 'null')
            ]);
        }

        $this->db->transStart();

        try {
            foreach ($nilaiData as $kriteriaId => $nilai) {
                $existingNilai = $this->lombaNilaiModel
                    ->where('registrasi_id', $registrasiId)
                    ->where('kriteria_id', $kriteriaId)
                    ->where('IdJuri', $idJuri)
                    ->first();

                if ($existingNilai) {
                    // Update nilai yang sudah ada
                    $this->lombaNilaiModel->update($existingNilai['id'], [
                        'Nilai' => (float) $nilai
                    ]);
                } else {
                    // Simpan nilai baru
                    $insertResult = $this->lombaNilaiModel->insert([
                        'registrasi_id' => $registrasiId,
                        'cabang_id'     => $cabangId,
                        'kriteria_id'   => $kriteriaId,
                        'IdJuri'        => $idJuri,
                        'Nilai'         => (float) $nilai,
                    ]);
                    
                    if (!$insertResult) {
                        throw new \Exception('Gagal insert nilai: ' . json_encode($this->lombaNilaiModel->errors()));
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaksi database gagal'
                ]);
            }

            // Ambil data registrasi untuk update riwayat
            $registrasi = $this->lombaRegistrasiModel->getByNoPesertaWithSantri($registrasiId, $cabangId);
            if (!$registrasi) {
                $registrasi = $this->lombaRegistrasiModel->find($registrasiId);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $isEdit ? 'Nilai berhasil diupdate' : 'Nilai berhasil disimpan',
                'last_scored' => [
                    'NoPeserta' => $registrasi['NoPeserta'] ?? '',
                    'TipePeserta' => $registrasi['TipePeserta'] ?? 'Individu',
                    'NamaKelompok' => $registrasi['NamaKelompok'] ?? null,
                    'NamaSantri' => $registrasi['NamaSantri'] ?? 'Peserta',
                    'waktu' => date('d/m H:i')
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * View nilai yang sudah diinput juri
     */
    public function dataNilaiJuri()
    {
        $usernameJuri = user()->username;
        $juriData = $this->lombaJuriModel->getJuriByUsername($usernameJuri);

        if (!$juriData) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak terdaftar sebagai juri lomba');
        }

        // Ambil daftar kriteria untuk juri ini (filtered by juri settings if any)
        $kriteriaList = $this->lombaKriteriaModel->getKriteriaForJuri($juriData['id'], $juriData['cabang_id']);

        // Ambil nilai yang sudah diinput
        $nilaiList = $this->lombaNilaiModel->getNilaiByJuri($juriData['IdJuri'], $juriData['cabang_id']);

        // Kelompokkan nilai per registrasi
        $registrasiScored = [];
        foreach ($nilaiList as $n) {
            $registrasiId = $n['registrasi_id'];
            if (!isset($registrasiScored[$registrasiId])) {
                // Tentukan nama tampilan berdasarkan tipe
                // Tentukan nama tampilan berdasarkan data dari model
                $namaTampil = $n['NamaSantriReal'] ?? ('Peserta #' . $n['NoPeserta']);
                
                $registrasiScored[$registrasiId] = [
                    'registrasi_id' => $registrasiId,
                    'NoPeserta' => $n['NoPeserta'],
                    'NamaSantri' => $namaTampil,
                    'TipePeserta' => $n['TipePeserta'],
                    'updated_at' => $n['updated_at'],
                    'nilai' => []
                ];
            }
            $registrasiScored[$registrasiId]['nilai'][$n['kriteria_id']] = (float) $n['Nilai'];
            // Update waktu terbaru
            if ($n['updated_at'] > $registrasiScored[$registrasiId]['updated_at']) {
                $registrasiScored[$registrasiId]['updated_at'] = $n['updated_at'];
            }
        }

        $data = [
            'page_title'     => 'Data Nilai Juri',
            'juri_data'      => $juriData,
            'kriteria_list'  => $kriteriaList,
            'peserta_scored' => array_values($registrasiScored),
        ];

        return view('backend/Perlombaan/dataNilaiJuri', $data);
    }

    // ==================== METHOD PENGUNDIAN ====================

    /**
     * Halaman pengundian nomor peserta
     */
    public function pengundian($cabangId = null)
    {
        // Pengecekan Akses: Operator hanya boleh akses lomba miliknya
        $isOperatorOnly = in_groups('Operator') && !in_groups('Admin');
        $myIdTpq = session()->get('IdTpq');

        if (!$cabangId) {
            // Jika tidak ada cabang, tampilkan pilihan cabang
            
            if ($isOperatorOnly) {
                // Operator: Hanya cabang dari lomba milik TPQ senidri
                $cabangList = $this->lombaCabangModel
                    ->select('tbl_lomba_cabang.*, tbl_lomba_master.NamaLomba, 
                        k_min.NamaKelas as NamaKelasMin, k_max.NamaKelas as NamaKelasMax,
                        (CASE 
                            WHEN tbl_lomba_cabang.Tipe = "Kelompok" OR tbl_lomba_cabang.TipePeserta = "Kelompok" THEN 
                                (SELECT COUNT(DISTINCT CONCAT(p.IdTpq, "-", p.GrupUrut)) FROM tbl_lomba_peserta p WHERE p.cabang_id = tbl_lomba_cabang.id AND p.StatusPendaftaran = "valid")
                            ELSE 
                                (SELECT COUNT(*) FROM tbl_lomba_peserta p WHERE p.cabang_id = tbl_lomba_cabang.id AND p.StatusPendaftaran = "valid")
                        END) as total_peserta,
                        (SELECT COUNT(*) FROM tbl_lomba_registrasi r WHERE r.cabang_id = tbl_lomba_cabang.id) as total_teregistrasi')
                    ->join('tbl_lomba_master', 'tbl_lomba_master.id = tbl_lomba_cabang.lomba_id')
                    ->join('tbl_kelas k_min', 'k_min.IdKelas = tbl_lomba_cabang.KelasMin', 'left')
                    ->join('tbl_kelas k_max', 'k_max.IdKelas = tbl_lomba_cabang.KelasMax', 'left')
                    ->where('tbl_lomba_master.IdTpq', $myIdTpq)
                    ->orderBy('tbl_lomba_master.TanggalMulai', 'DESC')
                    ->findAll();
            } else {
                // Admin: Semua cabang
                $cabangList = $this->lombaCabangModel->getAllCabangWithLomba();
            }
            
            $data = [
                'page_title'   => 'Pengundian Nomor Peserta',
                'cabang_list'  => $cabangList,
                'cabang'       => null,
            ];
            
            return view('backend/Perlombaan/pengundian', $data);
        }

        $cabang = $this->lombaCabangModel->getCabangWithLomba($cabangId);
        
        if (!$cabang) {
            return redirect()->to(base_url('backend/perlombaan/pengundian'))
                           ->with('error', 'Cabang tidak ditemukan');
        }

        // Access Check for Operator
        if ($isOperatorOnly && $cabang['IdTpq'] != $myIdTpq) {
            return redirect()->to(base_url('backend/perlombaan/pengundian'))
                           ->with('error', 'Anda tidak memiliki akses ke lomba ini');
        }

        // Ambil calon peserta yang valid dan belum teregistrasi
        $calonPeserta = $this->getCalonBelumRegistrasi($cabangId, $cabang['Tipe'] ?? 'Individu');

        // Ambil peserta yang sudah teregistrasi
        $pesertaTeregistrasi = $this->lombaRegistrasiModel->getRegistrasiWithAnggota($cabangId);

        // Hitung statistik
        $totalCalon = count($calonPeserta);
        $totalTeregistrasi = count($pesertaTeregistrasi);

        $data = [
            'page_title'            => 'Pengundian: ' . $cabang['NamaCabang'],
            'cabang'                => $cabang,
            'calon_peserta'         => $calonPeserta,
            'peserta_teregistrasi'  => $pesertaTeregistrasi,
            'total_calon'           => $totalCalon,
            'total_teregistrasi'    => $totalTeregistrasi,
        ];

        return view('backend/Perlombaan/pengundian', $data);
    }

    /**
     * Ambil calon peserta yang valid tapi belum teregistrasi
     */
    private function getCalonBelumRegistrasi($cabangId, $tipePeserta = 'Individu')
    {
        // Ambil peserta_id yang sudah teregistrasi
        $registeredIds = $this->db->table('tbl_lomba_registrasi_anggota ra')
            ->select('ra.peserta_id')
            ->join('tbl_lomba_registrasi r', 'r.id = ra.registrasi_id')
            ->where('r.cabang_id', $cabangId)
            ->get()
            ->getResultArray();
        
        $excludeIds = array_column($registeredIds, 'peserta_id');

        $builder = $this->db->table('tbl_lomba_peserta p');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left');
        $builder->where('p.cabang_id', $cabangId);
        $builder->where('p.StatusPendaftaran', 'valid');
        
        // Exclude yang sudah teregistrasi
        if (!empty($excludeIds)) {
            $builder->whereNotIn('p.id', $excludeIds);
        }
        
        if (strcasecmp($tipePeserta, 'Kelompok') === 0) {
            // Group by TPQ and GrupUrut for Kelompok
            $builder->select('MIN(p.id) as id, p.IdTpq, p.GrupUrut, p.NamaGrup, p.cabang_id, count(p.id) as jumlah_anggota, t.NamaTpq');
            $builder->select("GROUP_CONCAT(DISTINCT s.NamaSantri SEPARATOR ', ') as NamaSantri");
            $builder->groupBy('p.IdTpq');
            $builder->groupBy('p.GrupUrut');
            $builder->groupBy('p.NamaGrup');
            $builder->orderBy('t.NamaTpq', 'ASC');
        } else {
            // Individual
            $builder->select('p.id, p.IdSantri, p.IdTpq, p.cabang_id, p.StatusPendaftaran, p.created_at, s.NamaSantri, s.JenisKelamin, t.NamaTpq');
            $builder->groupBy('p.id');
            $builder->orderBy('p.created_at', 'ASC');
        }
        
        return $builder->get()->getResultArray();
    }

    /**
     * Proses pengundian bulk (AJAX)
     */
    public function prosesUndian()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $pesertaIds = $this->request->getPost('peserta_ids'); // Array of peserta_id

        if (empty($cabangId) || empty($pesertaIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        $cabang = $this->lombaCabangModel->find($cabangId);
        if (!$cabang) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang tidak ditemukan'
            ]);
        }

        // Cek tipe peserta dari cabang
        $tipePeserta = $cabang['Tipe'] ?? 'Individu';

        try {
            if (strcasecmp($tipePeserta, 'Kelompok') === 0) {
                // Untuk kelompok, pesertaIds adalah array of arrays (per kelompok)
                $kelompokList = $this->request->getPost('kelompok_list');
                
                if (empty($kelompokList)) {
                    // Jika tidak ada kelompok_list, group berdasarkan grup_pendaftaran
                    $kelompokList = $this->groupByGrupPendaftaran($pesertaIds, $cabangId);
                }
                
                if (empty($kelompokList)) {
                     return $this->response->setJSON([
                        'success' => false, 
                        'message' => 'Gagal mengelompokkan peserta. Pastikan data kelompok valid. (Empty Grouping)'
                     ]);
                }
                
                $result = $this->lombaRegistrasiModel->bulkRegistrasiKelompok($cabangId, $kelompokList);
            } else {
                // Untuk individu
                $result = $this->lombaRegistrasiModel->bulkRegistrasiIndividu($cabangId, $pesertaIds);
            }
    
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Exception Undian: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'System Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Group peserta berdasarkan TPQ (untuk TipePeserta = Kelompok)
     * Mengubah daftar ID individu menjadi daftar Kelompok per TPQ
     */
    private function groupByGrupPendaftaran($pesertaIds, $cabangId)
    {
        if (empty($pesertaIds)) return [];

        // 1. Ambil IdTpq dari peserta yang dipilih (ini adalah representative IDs)
        $representatives = $this->lombaPesertaModel
            ->whereIn('id', $pesertaIds)
            ->findAll();
            
        $targetKeys = [];
        $tpqIds = [];
        foreach ($representatives as $r) {
             $targetKeys[] = $r['IdTpq'] . '_' . ($r['GrupUrut'] ?? '1');
             $tpqIds[] = $r['IdTpq'];
        }
        $tpqIds = array_unique($tpqIds);
        
        if (empty($tpqIds)) return [];

        // 2. Ambil ID yang sudah teregistrasi untuk di-exclude
        $registeredIds = $this->db->table('tbl_lomba_registrasi_anggota ra')
            ->select('ra.peserta_id')
            ->join('tbl_lomba_registrasi r', 'r.id = ra.registrasi_id')
            ->where('r.cabang_id', $cabangId)
            ->get()
            ->getResultArray();
        $excludeIds = array_column($registeredIds, 'peserta_id');

        // 3. Ambil SEMUA peserta valid dari TPQ-TPQ tersebut
        $builder = $this->db->table('tbl_lomba_peserta p')
            ->select('p.*, t.NamaTpq')
            ->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left')
            ->whereIn('p.IdTpq', $tpqIds)
            ->where('p.cabang_id', $cabangId)
            ->where('p.StatusPendaftaran', 'valid');
            
        if (!empty($excludeIds)) {
            $builder->whereNotIn('p.id', $excludeIds);
        }
        
        $allPeserta = $builder->get()->getResultArray();
        
        // 4. Group by IdTpq
        $groups = [];
        foreach ($allPeserta as $p) {
            $grupUrut = $p['GrupUrut'] ?? '1';
            $key = $p['IdTpq'] . '_' . $grupUrut;
            
            if (!in_array($key, $targetKeys)) continue;

            
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'peserta_ids' => [],
                    'nama_kelompok' => 'Tim ' . ($p['NamaTpq'] ?? 'Unknown') . ' (Grup ' . $grupUrut . ')',
                    'IdTpq' => $p['IdTpq']
                ];
            }
            $groups[$key]['peserta_ids'][] = $p['id'];
        }
        
        return array_values($groups);
    }

    /**
     * Lihat hasil registrasi per cabang
     */
    public function hasilRegistrasi($cabangId)
    {
        $cabang = $this->lombaCabangModel->getCabangWithLomba($cabangId);
        
        if (!$cabang) {
            return redirect()->to(base_url('backend/perlombaan'))
                           ->with('error', 'Cabang tidak ditemukan');
        }

        $registrasiList = $this->lombaRegistrasiModel->getRegistrasiWithAnggota($cabangId);

        $data = [
            'page_title'       => 'Hasil Registrasi: ' . $cabang['NamaCabang'],
            'cabang'           => $cabang,
            'registrasi_list'  => $registrasiList,
        ];

        return view('backend/Perlombaan/hasilRegistrasi', $data);
    }

    // ==================== JURI KRITERIA MANAGEMENT ====================

    /**
     * Ambil kriteria cabang dengan status assigned untuk juri (AJAX)
     */
    public function getJuriKriteria($juriId)
    {
        $juri = $this->lombaJuriModel->find($juriId);
        
        if (!$juri) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Juri tidak ditemukan'
            ]);
        }

        // Ambil semua kriteria cabang
        $allKriteria = $this->lombaKriteriaModel->getKriteriaByCabang($juri['cabang_id']);
        
        // Ambil kriteria yang ditetapkan untuk juri ini
        $juriKriteriaModel = new LombaJuriKriteriaModel();
        $assignedIds = $juriKriteriaModel->getKriteriaIdsByJuri($juriId);
        $hasCustomSetting = count($assignedIds) > 0;
        
        // Ambil kriteria yang digunakan oleh juri LAIN di cabang yang sama
        $otherJuris = $this->lombaJuriModel->where('cabang_id', $juri['cabang_id'])
                                           ->where('id !=', $juriId)
                                           ->findAll();
        $usedByOthers = [];
        $usedByOthersDetails = [];
        
        foreach ($otherJuris as $otherJuri) {
            $otherKriteriaIds = $juriKriteriaModel->getKriteriaIdsByJuri($otherJuri['id']);
            foreach ($otherKriteriaIds as $kId) {
                if (!in_array($kId, $usedByOthers)) {
                    $usedByOthers[] = $kId;
                }
                $usedByOthersDetails[$kId] = $otherJuri['NamaJuri'] ?: $otherJuri['UsernameJuri'];
            }
        }
        
        // Mark kriteria yang assigned dan yang digunakan juri lain
        foreach ($allKriteria as &$k) {
            $k['assigned'] = in_array($k['id'], $assignedIds);
            $k['used_by_others'] = in_array($k['id'], $usedByOthers);
            $k['used_by'] = $usedByOthersDetails[$k['id']] ?? null;
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'kriteria' => $allKriteria,
                'has_custom_setting' => $hasCustomSetting,
                'assigned_count' => count($assignedIds),
                'total_count' => count($allKriteria),
                'juri' => $juri
            ]
        ]);
    }

    /**
     * Simpan setting kriteria untuk juri (AJAX)
     */
    public function saveJuriKriteria()
    {
        $juriId = $this->request->getPost('juri_id');
        $kriteriaIds = $this->request->getPost('kriteria_ids'); // Array or empty
        $useDefault = $this->request->getPost('use_default') === '1';
        
        if (empty($juriId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Juri tidak valid'
            ]);
        }

        $juri = $this->lombaJuriModel->find($juriId);
        if (!$juri) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Juri tidak ditemukan'
            ]);
        }

        $juriKriteriaModel = new LombaJuriKriteriaModel();
        
        if ($useDefault) {
            // Reset ke default (hapus semua setting)
            $juriKriteriaModel->clearKriteriaForJuri($juriId);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Juri akan menilai semua kriteria (default)'
            ]);
        }
        
        // Validasi minimal 1 kriteria
        if (empty($kriteriaIds) || !is_array($kriteriaIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal 1 kriteria atau gunakan default (semua kriteria)'
            ]);
        }
        
        // Simpan kriteria
        $result = $juriKriteriaModel->setKriteriaForJuri($juriId, $kriteriaIds);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Setting kriteria juri berhasil disimpan (' . count($kriteriaIds) . ' kriteria)'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan setting kriteria'
        ]);
    }

    /**
     * Monitor nilai dari semua juri (Admin/Operator)
     * Display Mode:
     * 1 = Single juri, all kriteria
     * 2 = Multiple juris, same kriteria (show rata-rata)
     * 3 = Multiple juris, different kriteria per juri
     */
    public function monitorNilai($cabangId = null)
    {
        $cabang = null;
        $lomba = null;
        $nilaiData = [];
        $kriteriaList = [];
        $juriList = [];
        $displayMode = 1;
        $kriteriaByJuri = []; // For mode 3

        if ($cabangId !== null) {
            $cabang = $this->lombaCabangModel->find($cabangId);
            if ($cabang) {
                $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
                $kriteriaList = $this->lombaKriteriaModel->getKriteriaByCabang($cabangId);
                $juriList = $this->lombaJuriModel->getJuriByCabang($cabangId, null);
                
                // Determine display mode using centralized logic
                $displayMode = $this->lombaCabangModel->getDisplayMode($cabangId);
                
                $juriCount = count($juriList);

                // Re-populate kriteria mapping for Mode 3 headers
                $juriKriteriaModel = new LombaJuriKriteriaModel();
                foreach ($juriList as $juri) {
                    $customIds = $juriKriteriaModel->getKriteriaIdsForJuri($juri['id']);
                    if (!empty($customIds)) {
                        $kriteriaByJuri[$juri['IdJuri']] = $customIds;
                    } else {
                        $kriteriaByJuri[$juri['IdJuri']] = array_column($kriteriaList, 'id');
                    }
                }
                
                // Ambil semua registrasi dengan nilai
                $registrasiModel = new LombaRegistrasiModel();
                $registrasiList = $registrasiModel->getRegistrasiListByCabang($cabangId);
                
                foreach ($registrasiList as $reg) {
                    // Ambil nilai dari semua juri untuk registrasi ini
                    $nilaiRegistrasi = $this->lombaNilaiModel->getNilaiByRegistrasi($reg['id']);
                    
                    if (empty($nilaiRegistrasi)) continue;
                    
                    // Group nilai by juri
                    $nilaiByJuri = [];
                    foreach ($nilaiRegistrasi as $n) {
                        $idJuri = $n['IdJuri'];
                        if (!isset($nilaiByJuri[$idJuri])) {
                            $nilaiByJuri[$idJuri] = [
                                'IdJuri' => $idJuri,
                                'NamaJuri' => $n['UsernameJuri'] ?? $n['NamaJuri'] ?? $idJuri,
                                'nilai' => [],
                                'updated_at' => $n['updated_at']
                            ];
                        }
                        $nilaiByJuri[$idJuri]['nilai'][$n['kriteria_id']] = (float) $n['Nilai'];
                    }
                    
                    // Get unified summary for this registration (The "Source of Truth")
                    // expectedJuriCount: for Mode 2 it's total juris, for others it's 1 (Mode 1 = 1, Mode 3 = 1 because split)
                    $expected = ($displayMode == 2) ? $juriCount : 1;
                    $summary = $this->lombaNilaiModel->getSkorTerpusat($reg['id'], $cabangId, $expected);
                    
                    if ($displayMode == 1 || $displayMode == 2) {
                        // Mode 1 & 2: Each juri as separate row
                        foreach ($nilaiByJuri as $jn) {
                            // Calculate juri-specific total for display in juri row
                            $juriTotalWeighted = 0;
                            foreach ($kriteriaList as $k) {
                                $nilai = isset($jn['nilai'][$k['id']]) ? $jn['nilai'][$k['id']] : 0;
                                $juriTotalWeighted += $nilai * ($k['Bobot'] / 100);
                            }
                            
                            $nilaiData[] = [
                                'registrasi_id' => $reg['id'],
                                'NoPeserta' => $reg['NoPeserta'],
                                'NamaSantri' => $reg['NamaSantri'],
                                'NamaTpq' => $reg['NamaTpq'] ?? '-',
                                'IdJuri' => $jn['IdJuri'],
                                'NamaJuri' => $jn['NamaJuri'],
                                'nilai' => $jn['nilai'],
                                'total_raw' => array_sum($jn['nilai']),
                                'total' => $juriTotalWeighted, // This is specific to THIS juri
                                'updated_at' => $jn['updated_at']
                            ];
                        }
                    } else {
                        // Mode 3: Combine all juri values in one row (Using Combined Summary)
                        $combinedNilai = [];
                        $latestUpdate = null;
                        
                        foreach ($nilaiByJuri as $idJuri => $jn) {
                            foreach ($jn['nilai'] as $kriteriaId => $nilai) {
                                $combinedNilai[$kriteriaId] = [
                                    'nilai' => $nilai,
                                    'IdJuri' => $idJuri,
                                    'NamaJuri' => $jn['NamaJuri']
                                ];
                            }
                            if ($latestUpdate === null || $jn['updated_at'] > $latestUpdate) {
                                $latestUpdate = $jn['updated_at'];
                            }
                        }
                        
                        $nilaiData[] = [
                            'registrasi_id' => $reg['id'],
                            'NoPeserta' => $reg['NoPeserta'],
                            'NamaSantri' => $reg['NamaSantri'],
                            'NamaTpq' => $reg['NamaTpq'] ?? '-',
                            'nilai' => $combinedNilai,
                            'total_raw' => $summary['total_nilai'],
                            'total' => $summary['nilai_akhir'], // Source of Truth
                            'status_label' => $summary['status_label'],
                            'updated_at' => $latestUpdate
                        ];
                    }
                }
                
                // For mode 2: Add consolidated average from Source of Truth
                if ($displayMode == 2 && !empty($nilaiData)) {
                    $grouped = [];
                    foreach ($nilaiData as $row) {
                        $regId = $row['registrasi_id'];
                        if (!isset($grouped[$regId])) {
                            $expected = ($displayMode == 2) ? $juriCount : 1;
                            $grouped[$regId] = [
                                'rows' => [],
                                'summary' => $this->lombaNilaiModel->getSkorTerpusat($regId, $cabangId, $expected)
                            ];
                        }
                        $grouped[$regId]['rows'][] = $row;
                    }
                    
                    $newNilaiData = [];
                    foreach ($grouped as $regId => $group) {
                        $juriCount = count($group['rows']);
                        $summary = $group['summary'];
                        
                        foreach ($group['rows'] as $i => $row) {
                            $row['rata_rata'] = ($i == 0) ? $summary['nilai_akhir'] : null;
                            $row['total_nilai_rata'] = ($i == 0) ? $summary['total_nilai'] : null;
                            $row['status_label'] = ($i == 0) ? $summary['status_label'] : null;
                            $row['rowspan'] = ($i == 0) ? $juriCount : 0;
                            $newNilaiData[] = $row;
                        }
                    }
                    $nilaiData = $newNilaiData;
                }
            }
        }

        // Ambil semua lomba untuk dropdown
        $idTpq = session()->get('IdTpq');
        $lombaList = $this->lombaMasterModel->getLombaListDetailed(in_groups('Admin') ? null : $idTpq);

        // Auto-select first lomba if available
        if ($lomba === null && !empty($lombaList)) {
            $lomba = $lombaList[0];
        }

        $data = [
            'page_title' => 'Monitor Nilai Juri',
            'cabang' => $cabang,
            'lomba' => $lomba,
            'lomba_list' => $lombaList,
            'kriteria_list' => $kriteriaList,
            'juri_list' => $juriList,
            'nilai_data' => $nilaiData,
            'display_mode' => $displayMode,
            'kriteria_by_juri' => $kriteriaByJuri
        ];

        return view('backend/Perlombaan/monitorNilai', $data);
    }

    // ==================== CERTIFICATE TEMPLATE METHODS ====================

    /**
     * Template Sertifikat Management
     */
    public function templateSertifikat($cabangId = null)
    {
        $cabang = null;
        $lomba = null;
        $template = null;

        if ($cabangId !== null) {
            $cabang = $this->lombaCabangModel->find($cabangId);
            if ($cabang) {
                $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
                $template = $this->sertifikatTemplateModel->getTemplateByCabang($cabangId);
            }
        }

        // Get lomba list for dropdown
        $idTpq = session()->get('IdTpq');
        if (in_groups('Admin')) {
            $lombaList = $this->lombaMasterModel->getLombaListDetailed();
        } else {
            $lombaList = $this->lombaMasterModel->getLombaListDetailed($idTpq, 'aktif', false);
        }

        $data = [
            'page_title' => 'Template Sertifikat',
            'lomba' => $lomba,
            'cabang' => $cabang,
            'cabang_id' => $cabangId,
            'template' => $template,
            'lomba_list' => $lombaList,
        ];

        return view('backend/Perlombaan/templateSertifikat', $data);
    }

    /**
     * Upload template sertifikat (AJAX)
     */
    public function uploadTemplate()
    {
        $cabangId = $this->request->getPost('cabang_id');
        $namaTemplate = $this->request->getPost('nama_template');
        $orientation = $this->request->getPost('orientation') ?? 'landscape';

        if (!$cabangId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cabang harus dipilih']);
        }

        $file = $this->request->getFile('template_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File template harus diupload']);
        }

        // Validate file type
        if (!in_array($file->getExtension(), ['jpg', 'jpeg', 'png'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'File harus berformat JPG atau PNG']);
        }

        // Create upload directory if not exists
        $uploadPath = FCPATH . 'uploads/sertifikat/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $newName = 'template_' . $cabangId . '_' . time() . '.' . $file->getExtension();
        
        try {
            // Move file
            $file->move($uploadPath, $newName);

            // Get image dimensions
            $imagePath = $uploadPath . $newName;
            list($width, $height) = getimagesize($imagePath);

            // Delete old template if exists
            $oldTemplate = $this->sertifikatTemplateModel->getTemplateByCabang($cabangId);
            if ($oldTemplate) {
                $this->sertifikatTemplateModel->deleteTemplate($oldTemplate['id']);
            }

            // Save to database
            $data = [
                'cabang_id' => $cabangId,
                'NamaTemplate' => $namaTemplate ?: 'Template Sertifikat',
                'FileTemplate' => 'sertifikat/' . $newName,
                'Width' => $width,
                'Height' => $height,
                'Orientation' => $orientation,
                'Status' => 'aktif',
            ];

            if ($this->sertifikatTemplateModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Template berhasil diupload',
                    'template_id' => $this->sertifikatTemplateModel->getInsertID()
                ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan template']);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete template (AJAX)
     */
    public function deleteTemplate($id)
    {
        if ($this->sertifikatTemplateModel->deleteTemplate($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Template berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus template']);
    }

    /**
     * Configure fields for template
     */
    public function configureFields($templateId)
    {
        $template = $this->sertifikatTemplateModel->getTemplateWithCabang($templateId);
        if (!$template) {
            return redirect()->to(base_url('backend/perlombaan/template-sertifikat'))
                           ->with('error', 'Template tidak ditemukan');
        }

        $fields = $this->sertifikatFieldModel->getFieldsByTemplate($templateId);
        $availableFields = $this->sertifikatFieldModel->getAvailableFields();

        $data = [
            'page_title' => 'Konfigurasi Field Sertifikat',
            'template' => $template,
            'fields' => $fields,
            'available_fields' => $availableFields,
        ];

        return view('backend/Perlombaan/configureFields', $data);
    }

    /**
     * Save field configuration (AJAX)
     */
    public function saveFieldConfig()
    {
        $templateId = $this->request->getPost('template_id');
        $fieldsData = $this->request->getPost('fields'); // Array of field configurations

        if (!$templateId || empty($fieldsData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $this->db->transStart();

        try {
            // Delete existing fields
            $this->sertifikatFieldModel->deleteByTemplate($templateId);

            // Insert new fields
            $insertData = [];
            foreach ($fieldsData as $field) {
                $insertData[] = [
                    'template_id' => $templateId,
                    'FieldName' => $field['name'],
                    'FieldLabel' => $field['label'],
                    'PosX' => (int) $field['x'],
                    'PosY' => (int) $field['y'],
                    'FontFamily' => $field['font_family'] ?? 'Arial',
                    'FontSize' => (int) ($field['font_size'] ?? 16),
                    'FontStyle' => $field['font_style'] ?? 'B',
                    'TextAlign' => $field['text_align'] ?? 'C',
                    'TextColor' => $field['text_color'] ?? '#000000',
                    'MaxWidth' => (int) ($field['max_width'] ?? 0),
                ];
            }

            if (!empty($insertData)) {
                if ($this->sertifikatFieldModel->insertBatch($insertData) === false) {
                    $errors = $this->sertifikatFieldModel->errors();
                    throw new \Exception(implode(', ', $errors));
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan konfigurasi']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Konfigurasi field berhasil disimpan']);

        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get available fields (AJAX)
     */
    public function getAvailableFields()
    {
        $fields = $this->sertifikatFieldModel->getAvailableFields();
        return $this->response->setJSON(['success' => true, 'data' => $fields]);
    }

    /**
     * Delete field (AJAX)
     */
    public function deleteField($id)
    {
        if ($this->sertifikatFieldModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Field berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus field']);
    }

    /**
     * Download sertifikat untuk peserta (from viewHasil)
     */
    public function downloadSertifikat($hasilId)
    {
        // Get hasil data
        $hasil = $this->lombaNilaiModel->getPeringkatById($hasilId);
        if (!$hasil) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Get template and fields
        $template = $this->sertifikatTemplateModel->getTemplateByCabang($hasil['cabang_id']);
        if (!$template) {
            return redirect()->back()->with('error', 'Template sertifikat belum dikonfigurasi');
        }

        $fields = $this->sertifikatFieldModel->getFieldsByTemplate($template['id']);
        if (empty($fields)) {
            return redirect()->back()->with('error', 'Field sertifikat belum dikonfigurasi');
        }

        // Prepare data for certificate
        $certificateData = $this->prepareCertificateData($hasil);

        // Generate PDF
        helper('CertificateGenerator');
        $generator = new \App\Helpers\CertificateGenerator($template, $fields);
        $generator->setData($certificateData)->generate();

        // Download
        $filename = 'Sertifikat_' . $hasil['NoPeserta'] . '_' . date('Ymd') . '.pdf';
        $generator->stream($filename, ['Attachment' => true]);
    }

    /**
     * Preview sertifikat dengan dummy data
     */
    public function previewCertificate($templateId)
    {
        log_message('info', "Preview Certificate requested for Template ID: {$templateId}");
        // Get template
        $template = $this->sertifikatTemplateModel->getTemplateWithCabang($templateId);
        if (!$template) {
            return "Template tidak ditemukan";
        }

        // Get fields
        $fields = $this->sertifikatFieldModel->getFieldsByTemplate($templateId);
        if (empty($fields)) {
            return "Field belum dikonfigurasi. Silakan simpan konfigurasi terlebih dahulu.";
        }

        // Prepare dummy data
        $dummyData = [
            'nama_santri' => 'Abdullah Azzam',
            'no_peserta' => 'A001',
            'nama_lomba' => $template['NamaLomba'] ?? 'FASI XI Tingkat Kota',
            'nama_cabang' => $template['NamaCabang'] ?? 'Tartil Al-Quran',
            'kategori' => 'TKA Putra',
            'peringkat' => 'Juara 1',
            'peringkat_text' => 'Satu',
            'nama_tpq' => 'TPA Al-Hidayah',
            'tanggal_lomba' => date('d F Y'),
            'tempat_lomba' => 'Aula Masjid Agung',
            'nilai_akhir' => '98.50'
        ];

        // Generate PDF
        helper('CertificateGenerator');
        try {
            $generator = new \App\Helpers\CertificateGenerator($template, $fields);
            $generator->setData($dummyData)->generate();
            
            // Stream inline (open in browser)
            $generator->stream('Preview_Sertifikat.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Prepare certificate data from hasil
     */
    protected function prepareCertificateData($hasil)
    {
        // Get additional data
        $cabang = $this->lombaCabangModel->find($hasil['cabang_id']);
        $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);

        // Prepare peringkat text
        $peringkatText = \App\Helpers\CertificateGenerator::peringkatToText($hasil['Peringkat']);

        return [
            'nama_santri' => $hasil['NamaSantri'] ?? '',
            'nama_lomba' => $lomba['NamaLomba'] ?? '',
            'nama_cabang' => $cabang['NamaCabang'] ?? '',
            'kategori' => $cabang['Kategori'] ?? '',
            'peringkat' => 'Juara ' . $hasil['Peringkat'],
            'peringkat_text' => $peringkatText,
            'nama_tpq' => $hasil['NamaTpq'] ?? '',
            'tanggal_lomba' => date('d F Y', strtotime($lomba['TanggalMulai'] ?? 'now')),
            'tempat_lomba' => 'Aula MDA', // TODO: Add to lomba master
            'nilai_akhir' => number_format($hasil['NilaiAkhir'], 2),
            'no_peserta' => $hasil['NoPeserta'] ?? '',
        ];
    }

    /**
     * Batch download sertifikat (AJAX)
     */
    public function batchDownloadSertifikat()
    {
        $cabangId = $this->request->getPost('cabang_id');
        log_message('info', "Batch Download Certificate requested for Cabang ID: {$cabangId}");
        
        if (!$cabangId) {
            return redirect()->back()->with('error', 'Cabang belum dipilih');
        }

        // Get template and fields
        $template = $this->sertifikatTemplateModel->getTemplateByCabang($cabangId);
        if (!$template) {
            return redirect()->back()->with('error', 'Template sertifikat belum diupload untuk cabang ini');
        }

        $fields = $this->sertifikatFieldModel->getFieldsByTemplate($template['id']);
        if (empty($fields)) {
            return redirect()->back()->with('error', 'Konfigurasi field sertifikat belum diatur');
        }

        // Get Participants (All Ranked)
        // Using getPeringkat to get consistent data
        $participants = $this->lombaNilaiModel->getPeringkat($cabangId); 
        
        if (empty($participants)) {
            return redirect()->back()->with('error', 'Belum ada data peserta yang dinilai');
        }
        
        // Prepare ZIP filename
        // Prepare ZIP filename
        $cabang = $this->lombaCabangModel->find($cabangId);
        $lomba = $this->lombaMasterModel->find($cabang['lomba_id']);
        
        $cleanLomba = preg_replace('/[^a-zA-Z0-9]/', '_', $lomba['NamaLomba']);
        $cleanCabang = preg_replace('/[^a-zA-Z0-9]/', '_', $cabang['NamaCabang']);
        $timestamp = date('YmdHis');
        
        $zipFilename = "Sertifikat_{$cleanLomba}_{$cleanCabang}_{$timestamp}.zip";
        $zipPath = WRITEPATH . 'uploads/' . $zipFilename;
        
        // Create uploads directory if not exists
        if (!is_dir(WRITEPATH . 'uploads/')) {
            mkdir(WRITEPATH . 'uploads/', 0777, true);
        }
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
             return redirect()->back()->with('error', 'Gagal membuat file ZIP di server');
        }
        
        // Load Generator helper class
        helper('CertificateGenerator');
        
        // Generate PDFs and add to ZIP
        $count = 0;
        foreach ($participants as $p) {
             try {
                $certData = $this->prepareCertificateData($p);
                
                $generator = new \App\Helpers\CertificateGenerator($template, $fields);
                $pdfContent = $generator->setData($certData)->generate()->output(null, 'S');
                
                // Filename inside ZIP: Rank_Nama.pdf
                $cleanNama = preg_replace('/[^a-zA-Z0-9\s]/', '', $p['NamaSantri']);
                $cleanNama = str_replace(' ', '_', trim($cleanNama));
                $filename = sprintf("Juara_%s_%s.pdf", $p['Peringkat'], $cleanNama);
                
                $zip->addFromString($filename, $pdfContent);
                $count++;
             } catch (\Exception $e) {
                 log_message('error', 'Certificate Batch Error for ID ' . $p['id'] . ': ' . $e->getMessage());
             }
        }
        
        $zip->close();
        
        if ($count === 0) {
            return redirect()->back()->with('error', 'Gagal membuat file sertifikat (0 file generated)');
        }
        
        // Force Download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
        header('Content-Length: ' . filesize($zipPath));
        readfile($zipPath);
        
        // Cleanup
        unlink($zipPath);
        exit;
    }
}
