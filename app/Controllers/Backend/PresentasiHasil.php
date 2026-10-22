<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PresentasiHasilModel;
use App\Models\TpqModel;

class PresentasiHasil extends BaseController
{
    protected PresentasiHasilModel $presentasiModel;
    protected TpqModel $tpqModel;

    public function __construct()
    {
        $this->presentasiModel = new PresentasiHasilModel();
        $this->tpqModel        = new TpqModel();
    }

    // ---------------------------------------------------------------
    // Helpers: deteksi role & idTpq
    // ---------------------------------------------------------------
    private function getIdTpqUser(): ?string
    {
        $user = user();
        if (!$user) return null;

        // Cek via session (pola yang ada di Munaqosah.php)
        $session = session();
        $idTpq   = $session->get('IdTpq');

        // Admin / SuperAdmin: idTpq bisa 0 atau null (pilih sendiri)
        if (in_groups('Admin') || in_groups('SuperAdmin')) {
            return $idTpq ?: '0';
        }

        return $idTpq ?: null;
    }

    private function isAdminOrSuper(): bool
    {
        return in_groups('Admin') || in_groups('SuperAdmin');
    }

    // ---------------------------------------------------------------
    // GET /backend/munaqosah/presentasi-hasil  → Halaman konfigurasi
    // ---------------------------------------------------------------
    public function index()
    {
        if (!logged_in()) {
            return redirect()->to(base_url('login'));
        }

        // Juri tidak boleh akses
        if (in_groups('Juri')) {
            return redirect()->to(base_url('backend'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $idTpq   = $this->getIdTpqUser();
        $isAdmin = $this->isAdminOrSuper();

        // Daftar TPQ untuk admin
        $daftarTpq = $isAdmin ? $this->tpqModel->findAll() : [];

        // Konfigurasi tersimpan
        $config = $this->presentasiModel->getKonfigurasi($idTpq ?? '0');

        // Tahun ajaran tersedia
        $tahunList = [];
        if ($idTpq && $idTpq !== '0') {
            $tahunList = $this->presentasiModel->getTahunAjaranAvailable($idTpq);
        }

        $data = [
            'title'      => 'Presentasi Hasil Munaqosah',
            'config'     => $config,
            'daftarTpq'  => $daftarTpq,
            'tahunList'  => $tahunList,
            'idTpq'      => $idTpq,
            'isAdmin'    => $isAdmin,
        ];

        return view('backend/Munaqosah/presentasiHasil', $data);
    }

    // ---------------------------------------------------------------
    // POST /backend/munaqosah/presentasi-hasil/save-config → Simpan config
    // ---------------------------------------------------------------
    public function saveConfig()
    {
        if (!logged_in() || in_groups('Juri')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $idTpq = $this->request->getPost('IdTpq') ?? $this->getIdTpqUser();

        // Operator hanya bisa simpan untuk TPQ sendiri
        if (!$this->isAdminOrSuper()) {
            $idTpq = $this->getIdTpqUser();
        }

        $fields = [
            'NamaKonfigurasi', 'IdTahunAjaran', 'TypeUjian',
            'ThresholdKKM', 'ThresholdAman',
            'NamaAcara', 'SubtitleCover', 'JudulCover', 'TaglineCover',
            'FooterKiri', 'FooterKanan',
            'TemaWarna', 'WarnaAksen', 'BackgroundType', 'BackgroundValue',
            'AnimasiTransisi',
        ];

        $data = [];
        foreach ($fields as $f) {
            $val = $this->request->getPost($f);
            if ($val !== null) {
                $data[$f] = $val;
            }
        }

        // JSON fields
        $jsonFields = ['ConfigSlide2','ConfigSlide3','ConfigSlide5','ConfigSlide6',
                       'ConfigSlide7','ConfigSlide8','ConfigSlide9','ConfigSlide10',
                       'ConfigSlide11','ConfigSlide12','SlideAktif'];
        foreach ($jsonFields as $jf) {
            $val = $this->request->getPost($jf);
            if ($val !== null) {
                $data[$jf] = is_string($val) ? json_decode($val, true) : $val;
            }
        }

        try {
            $this->presentasiModel->simpanKonfigurasi($idTpq, $data);
            return $this->response->setJSON(['success' => true, 'message' => 'Konfigurasi berhasil disimpan.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    // ---------------------------------------------------------------
    // POST /backend/munaqosah/presentasi-hasil/data → AJAX data slide
    // ---------------------------------------------------------------
    public function getData()
    {
        if (!logged_in()) {
            return $this->response->setJSON(['success' => false])->setStatusCode(403);
        }

        $idTpq         = $this->request->getPost('idTpq') ?? $this->getIdTpqUser();
        $idTahunAjaran = $this->request->getPost('idTahunAjaran');
        $typeUjian     = $this->request->getPost('typeUjian') ?? 'pra-munaqosah';
        $thresholdKKM  = (float)($this->request->getPost('thresholdKKM') ?? 70);
        $thresholdAman = (float)($this->request->getPost('thresholdAman') ?? 80);
        $topN          = (int)($this->request->getPost('topN') ?? 3);
        $bottomN       = (int)($this->request->getPost('bottomN') ?? 3);
        $thresholdKlinik    = (int)($this->request->getPost('thresholdKlinik') ?? 3);
        $thresholdPenguatan = (int)($this->request->getPost('thresholdPenguatan') ?? 2);

        if (!$idTpq || !$idTahunAjaran) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter tidak lengkap.'])->setStatusCode(400);
        }

        $sebaran  = $this->presentasiModel->getSebaranNilaiPerMateri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM, $thresholdAman);
        $topMateri    = $this->presentasiModel->getTopMateri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM, $topN);
        $bottomMateri = $this->presentasiModel->getBottomMateri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM, $bottomN);
        $kelompok     = $this->presentasiModel->getKelompokSantri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM, $thresholdAman, $thresholdKlinik, $thresholdPenguatan);
        $infoTpq      = $this->presentasiModel->getInfoTpq($idTpq);
        $totalSantri  = $this->presentasiModel->getTotalSantri($idTpq, $idTahunAjaran, $typeUjian);

        return $this->response->setJSON([
            'success'      => true,
            'sebaran'      => $sebaran,
            'topMateri'    => $topMateri,
            'bottomMateri' => $bottomMateri,
            'kelompok'     => $kelompok,
            'infoTpq'      => $infoTpq,
            'totalSantri'  => $totalSantri,
        ]);
    }

    // ---------------------------------------------------------------
    // GET /backend/munaqosah/presentasi-hasil/slideshow → Fullscreen
    // ---------------------------------------------------------------
    public function slideshow()
    {
        if (!logged_in()) {
            return redirect()->to(base_url('login'));
        }
        if (in_groups('Juri')) {
            return redirect()->to(base_url('backend'));
        }

        $idTpq = $this->getIdTpqUser();
        $config = $this->presentasiModel->getKonfigurasi($idTpq ?? '0');
        $infoTpq = $this->presentasiModel->getInfoTpq($idTpq);

        $data = [
            'title'   => 'Presentasi — ' . ($config['JudulCover'] ?? 'Statistik Hasil Munaqosah'),
            'config'  => $config,
            'infoTpq' => $infoTpq,
            'idTpq'   => $idTpq,
        ];

        return view('backend/Munaqosah/presentasiSlideshow', $data);
    }

    // ---------------------------------------------------------------
    // POST /backend/munaqosah/presentasi-hasil/upload-hero → Upload gambar cover
    // ---------------------------------------------------------------
    public function uploadHero()
    {
        if (!logged_in()) {
            return $this->response->setJSON(['success' => false])->setStatusCode(403);
        }

        $file = $this->request->getFile('hero_image');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid.']);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, atau WebP.']);
        }

        $maxSize = 5 * 1024 * 1024; // 5 MB
        if ($file->getSize() > $maxSize) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 5 MB.']);
        }

        $uploadPath = FCPATH . 'uploads/presentasi/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $newName = 'hero_' . time() . '_' . uniqid() . '.' . $file->getExtension();
        $file->move($uploadPath, $newName);

        $publicPath = base_url('uploads/presentasi/' . $newName);

        return $this->response->setJSON(['success' => true, 'path' => $publicPath, 'filename' => $newName]);
    }
}
