<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\Frontend\Infografis\InfografisLinkModel;
use App\Models\Frontend\Infografis\InfografisConfigModel;
use App\Models\Frontend\Infografis\InfografisGaleriModel;
use App\Models\Frontend\Infografis\InfografisAgendaModel;
use App\Models\HelpFunctionModel;

/**
 * TvDigital Controller (Backend)
 * Mengelola pengaturan TV Digital / Digital Signage
 * Accessible oleh Admin (FKPQ) dan Operator (TPQ)
 */
class TvDigital extends BaseController
{
    protected $linkModel;
    protected $configModel;
    protected $galeriModel;
    protected $agendaModel;
    protected $helpFunctionModel;

    public function __construct()
    {
        $this->linkModel = new InfografisLinkModel();
        $this->configModel = new InfografisConfigModel();
        $this->galeriModel = new InfografisGaleriModel();
        $this->agendaModel = new InfografisAgendaModel();
        $this->helpFunctionModel = new HelpFunctionModel();
    }

    /**
     * Get IdTpq dari session (0/null = FKPQ, lainnya = TPQ spesifik)
     */
    private function getSessionIdTpq()
    {
        return session()->get('IdTpq') ?? 0;
    }

    /**
     * Halaman utama pengaturan TV Digital
     */
    public function index()
    {
        $idTpq = $this->getSessionIdTpq();

        $links = $this->linkModel->getLinksByTpq($idTpq);

        // Ambil config untuk setiap link
        foreach ($links as &$link) {
            $link['blocks'] = $this->configModel->getAllBlocks($link['Id']);
        }

        $idTahunAjaranList = session()->get('IdTahunAjaranList') ?? [];
        if (empty($idTahunAjaranList)) {
            $idTahunAjaranList = [$this->helpFunctionModel->getTahunAjaranSaatIni()];
        }

        $data = [
            'page_title'        => 'TV Digital - Pengaturan',
            'menu_open'         => 'tv-digital',
            'menu_active'       => 'tv-digital-pengaturan',
            'links'             => $links,
            'blockLabels'       => InfografisConfigModel::BLOCK_LABELS,
            'IdTpq'             => $idTpq,
            'idTahunAjaranList' => $idTahunAjaranList,
        ];

        return view('backend/TvDigital/index', $data);
    }

    /**
     * Generate link baru (AJAX)
     */
    public function createLink()
    {
        $idTpq = $this->getSessionIdTpq();
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $namaLink = $this->request->getPost('NamaLink') ?? 'TV Digital';

        $hashKey = $this->linkModel->generateHashKey();

        $linkId = $this->linkModel->insert([
            'IdTpq'             => $idTpq ?: '0',
            'IdTahunAjaran'     => $idTahunAjaran,
            'HashKey'           => $hashKey,
            'NamaLink'          => $namaLink,
            'SlideshowInterval' => 15,
            'RefreshInterval'   => 5,
            'IsActive'          => 1,
        ]);

        // Inisialisasi default block cards
        $this->configModel->initDefaultBlocks($linkId);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Link TV Digital berhasil dibuat',
            'data'    => [
                'id'      => $linkId,
                'hashKey' => $hashKey,
                'url'     => base_url('tv/' . $hashKey),
            ],
        ]);
    }

    /**
     * Update konfigurasi link (nama, interval)
     */
    public function updateLink($id)
    {
        $idTpq = $this->getSessionIdTpq();

        // Validasi ownership
        if (!$this->linkModel->isOwnedBy($id, $idTpq)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke link ini',
            ]);
        }

        $updateData = [];

        if ($this->request->getPost('NamaLink') !== null) {
            $updateData['NamaLink'] = $this->request->getPost('NamaLink');
        }
        if ($this->request->getPost('SlideshowInterval') !== null) {
            $updateData['SlideshowInterval'] = (int)$this->request->getPost('SlideshowInterval');
        }
        if ($this->request->getPost('RefreshInterval') !== null) {
            $updateData['RefreshInterval'] = (int)$this->request->getPost('RefreshInterval');
        }
        if ($this->request->getPost('IsActive') !== null) {
            $updateData['IsActive'] = (int)$this->request->getPost('IsActive');
        }
        if ($this->request->getPost('IdTahunAjaran') !== null) {
            $updateData['IdTahunAjaran'] = $this->request->getPost('IdTahunAjaran');
        }

        if (!empty($updateData)) {
            $this->linkModel->update($id, $updateData);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Konfigurasi berhasil diperbarui',
        ]);
    }

    /**
     * Hapus link
     */
    public function deleteLink($id)
    {
        $idTpq = $this->getSessionIdTpq();

        if (!$this->linkModel->isOwnedBy($id, $idTpq)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke link ini',
            ]);
        }

        // Hapus config terkait
        $this->configModel->where('IdInfografisLink', $id)->delete();

        // Hapus link
        $this->linkModel->delete($id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Link TV Digital berhasil dihapus',
        ]);
    }

    /**
     * Simpan konfigurasi block card (AJAX)
     */
    public function saveConfig()
    {
        $idTpq = $this->getSessionIdTpq();
        $idLink = (int)$this->request->getPost('IdInfografisLink');
        $blocks = $this->request->getPost('blocks');

        if (!$this->linkModel->isOwnedBy($idLink, $idTpq)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses',
            ]);
        }

        if (!empty($blocks) && is_array($blocks)) {
            $this->configModel->saveBlockConfig($idLink, $blocks);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Konfigurasi block berhasil disimpan',
        ]);
    }

    // ========================================
    // GALERI
    // ========================================

    /**
     * Halaman kelola galeri foto
     */
    public function galeriIndex()
    {
        $idTpq = $this->getSessionIdTpq();
        $galeri = $this->galeriModel->getGaleriByTpq($idTpq);

        $data = [
            'page_title'   => 'TV Digital - Galeri Kegiatan',
            'menu_open'    => 'tv-digital',
            'menu_active'  => 'tv-digital-galeri',
            'galeri'       => $galeri,
            'IdTpq'        => $idTpq,
        ];

        return view('backend/TvDigital/galeri', $data);
    }

    /**
     * Upload foto kegiatan (AJAX)
     */
    public function uploadGaleri()
    {
        $idTpq = $this->getSessionIdTpq();

        $validationRules = [
            'foto' => [
                'rules'  => 'uploaded[foto]|max_size[foto,5120]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'uploaded'  => 'Silakan pilih file foto',
                    'max_size'  => 'Ukuran file maksimal 5MB',
                    'is_image'  => 'File harus berupa gambar',
                    'mime_in'   => 'Format yang diizinkan: JPG, PNG, WebP',
                ],
            ],
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $this->validator->getErrors()),
            ]);
        }

        $foto = $this->request->getFile('foto');

        if ($foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/galeri', $newName);

            $this->galeriModel->insert([
                'IdTpq'            => $idTpq ?: '0',
                'Judul'            => $this->request->getPost('Judul') ?? '',
                'NamaFile'         => $newName,
                'Keterangan'       => $this->request->getPost('Keterangan') ?? '',
                'TanggalKegiatan'  => $this->request->getPost('TanggalKegiatan') ?? date('Y-m-d'),
                'IsActive'         => 1,
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Foto berhasil diupload',
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal mengupload foto',
        ]);
    }

    /**
     * Update galeri
     */
    public function updateGaleri($id)
    {
        $idTpq = $this->getSessionIdTpq();
        $galeri = $this->galeriModel->find($id);

        if (!$galeri) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        $updateData = [];
        if ($this->request->getPost('Judul') !== null) {
            $updateData['Judul'] = $this->request->getPost('Judul');
        }
        if ($this->request->getPost('Keterangan') !== null) {
            $updateData['Keterangan'] = $this->request->getPost('Keterangan');
        }
        if ($this->request->getPost('TanggalKegiatan') !== null) {
            $updateData['TanggalKegiatan'] = $this->request->getPost('TanggalKegiatan');
        }
        if ($this->request->getPost('IsActive') !== null) {
            $updateData['IsActive'] = (int)$this->request->getPost('IsActive');
        }

        if (!empty($updateData)) {
            $this->galeriModel->update($id, $updateData);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil diperbarui']);
    }

    /**
     * Hapus foto galeri
     */
    public function deleteGaleri($id)
    {
        $galeri = $this->galeriModel->find($id);

        if ($galeri) {
            // Hapus file fisik
            $filePath = FCPATH . 'uploads/galeri/' . $galeri['NamaFile'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->galeriModel->delete($id);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Foto berhasil dihapus',
        ]);
    }

    // ========================================
    // AGENDA
    // ========================================

    /**
     * Halaman kelola agenda
     */
    public function agendaIndex()
    {
        $idTpq = $this->getSessionIdTpq();
        $agenda = $this->agendaModel->getAgendaByTpq($idTpq);

        $data = [
            'page_title'   => 'TV Digital - Agenda Kegiatan',
            'menu_open'    => 'tv-digital',
            'menu_active'  => 'tv-digital-agenda',
            'agenda'       => $agenda,
            'IdTpq'        => $idTpq,
        ];

        return view('backend/TvDigital/agenda', $data);
    }

    /**
     * Simpan agenda baru (AJAX)
     */
    public function saveAgenda()
    {
        $idTpq = $this->getSessionIdTpq();

        $this->agendaModel->insert([
            'IdTpq'          => $idTpq ?: '0',
            'NamaKegiatan'   => $this->request->getPost('NamaKegiatan'),
            'TanggalMulai'   => $this->request->getPost('TanggalMulai'),
            'TanggalSelesai' => $this->request->getPost('TanggalSelesai') ?: null,
            'JamMulai'       => $this->request->getPost('JamMulai') ?: null,
            'JamSelesai'     => $this->request->getPost('JamSelesai') ?: null,
            'Tempat'         => $this->request->getPost('Tempat') ?? '',
            'Keterangan'     => $this->request->getPost('Keterangan') ?? '',
            'IsActive'       => 1,
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Agenda berhasil disimpan',
        ]);
    }

    /**
     * Update agenda
     */
    public function updateAgenda($id)
    {
        $updateData = [
            'NamaKegiatan'   => $this->request->getPost('NamaKegiatan'),
            'TanggalMulai'   => $this->request->getPost('TanggalMulai'),
            'TanggalSelesai' => $this->request->getPost('TanggalSelesai') ?: null,
            'JamMulai'       => $this->request->getPost('JamMulai') ?: null,
            'JamSelesai'     => $this->request->getPost('JamSelesai') ?: null,
            'Tempat'         => $this->request->getPost('Tempat') ?? '',
            'Keterangan'     => $this->request->getPost('Keterangan') ?? '',
        ];

        if ($this->request->getPost('IsActive') !== null) {
            $updateData['IsActive'] = (int)$this->request->getPost('IsActive');
        }

        $this->agendaModel->update($id, $updateData);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Agenda berhasil diperbarui',
        ]);
    }

    /**
     * Hapus agenda
     */
    public function deleteAgenda($id)
    {
        $this->agendaModel->delete($id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Agenda berhasil dihapus',
        ]);
    }

    /**
     * Preview TV Digital (redirect ke halaman publik)
     */
    public function previewTvDigital($id)
    {
        $link = $this->linkModel->find($id);

        if (!$link) {
            return redirect()->back()->with('error', 'Link tidak ditemukan');
        }

        return redirect()->to(base_url('tv/' . $link['HashKey']));
    }
}
