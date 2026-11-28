<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;
use App\Models\Backend\Penghargaan\PenghargaanTemplateFieldModel;
use App\Models\Backend\Penghargaan\PenghargaanTemplateModel;

/**
 * Controller Kelola Template Desain Sertifikat (Background + Field Overlay).
 */
class PenghargaanTemplate extends BaseController
{
    protected $templateModel;
    protected $fieldModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->templateModel = new PenghargaanTemplateModel();
        $this->fieldModel    = new PenghargaanTemplateFieldModel();
        $this->jenisModel    = new PenghargaanJenisModel();
    }

    private function isAdminUser(): bool
    {
        $idTpq = (string) (session()->get('IdTpq') ?? '0');
        return (function_exists('in_groups') && in_groups('Admin')) || $idTpq === '0' || $idTpq === '';
    }

    private function canEditTemplate(array $tpl, string $idTpq, bool $isAdmin): bool
    {
        $tplTpq = (string) ($tpl['IdTpq'] ?? '');
        if ($isAdmin) {
            return $tplTpq === '' || $tplTpq === '0';
        }
        return $tplTpq !== '' && $tplTpq !== '0' && $tplTpq === $idTpq;
    }

    /**
     * Daftar seluruh template (Admin: template umum IdTpq=0; Operator: milik sendiri + Admin AllowDuplicate=1).
     */
    public function index()
    {
        $this->ensureStarterTemplates();

        $idTpq     = (string) (session()->get('IdTpq') ?? '0');
        $isAdmin   = $this->isAdminUser();
        $templates = $this->templateModel->getForManagement($idTpq, $isAdmin);
        $listJenis = $this->jenisModel->getAvailableByTpq($idTpq, true);

        $jenisMap = [];
        foreach ($listJenis as $j) {
            $jenisMap[(string) $j['Id']] = '[' . $j['Kode'] . '] ' . $j['NamaPenghargaan'];
        }

        // Segregasi: pisahkan template referensi (Admin/Starter untuk Operator, atau Starter untuk Admin)
        // dengan template yang dikelola sendiri (milik TPQ atau custom Admin)
        $referenceTemplates = [];
        $grouped            = [
            'umum' => [
                'title' => 'Semua Jenis Penghargaan (Template Umum / Fallback)',
                'items' => [],
            ],
        ];

        foreach ($templates as $tpl) {
            $tplTpq     = (string) ($tpl['IdTpq'] ?? '');
            $isStarter  = ($tplTpq === '');
            $isAdminTpl = ($tplTpq === '' || $tplTpq === '0');

            // Untuk Operator: semua template Admin masuk ke bagian Referensi (atas, compact).
            // Untuk Admin: starter bawaan (IdTpq IS NULL) bisa tetap dikelola, atau pisahkan jika mau;
            // namun agar Admin juga bisa memilih starter, Admin hanya pisahkan jika ada di tab Referensi atau tetap di grouped.
            if (!$isAdmin && $isAdminTpl) {
                $referenceTemplates[] = $tpl;
                continue;
            }
            if ($isAdmin && $isStarter) {
                $referenceTemplates[] = $tpl;
                continue;
            }

            $jid = $tpl['IdJenisPenghargaan'] ?? null;
            if (empty($jid)) {
                $grouped['umum']['items'][] = $tpl;
            } else {
                $key = 'jenis_' . $jid;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'title' => 'Khusus: ' . ($jenisMap[(string) $jid] ?? 'Jenis #' . $jid),
                        'items' => [],
                    ];
                }
                $grouped[$key]['items'][] = $tpl;
            }
        }

        return view('backend/penghargaan/template/index', [
            'page_title'         => 'Template Desain Sertifikat',
            'referenceTemplates' => $referenceTemplates,
            'groupedTemplates'   => $grouped,
            'listJenis'          => $listJenis,
            'idTpq'              => $idTpq,
            'isAdmin'            => $isAdmin,
        ]);
    }

    /**
     * Simpan template baru beserta field overlay default.
     */
    public function store()
    {
        $idTpq        = (string) (session()->get('IdTpq') ?? '0');
        $isAdmin      = $this->isAdminUser();
        $namaTemplate = trim((string) $this->request->getPost('NamaTemplate'));
        $idJenis      = $this->request->getPost('IdJenisPenghargaan') ?: null;
        $ukuran       = $this->request->getPost('UkuranKertas') ?: 'A4';
        $orientasi    = $this->request->getPost('Orientasi') ?: 'landscape';

        if ($namaTemplate === '') {
            return redirect()->back()->with('error', 'Nama template wajib diisi.');
        }

        $bgPath    = 'uploads/penghargaan/templates/starter_formal.svg';
        $uploadDir = FCPATH . 'uploads/penghargaan/templates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $file = $this->request->getFile('BackgroundFile');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp'], true)) {
                $newName = 'bg_' . ($isAdmin ? '0' : $idTpq) . '_' . time() . '.' . $ext;
                $file->move($uploadDir, $newName);
                $bgPath = 'uploads/penghargaan/templates/' . $newName;
            }
        }

        $idTemplate = $this->templateModel->insert([
            'IdTpq'              => $isAdmin ? '0' : $idTpq,
            'IdJenisPenghargaan' => $idJenis ? (int) $idJenis : null,
            'NamaTemplate'       => $namaTemplate,
            'BackgroundFile'     => $bgPath,
            'UkuranKertas'       => $ukuran,
            'Orientasi'          => $orientasi,
            'IsDefault'          => 0,
            'AllowDuplicate'     => $isAdmin ? ($this->request->getPost('AllowDuplicate') ? 1 : 0) : 0,
            'Status'             => 'draft',
            'CreatedBy'          => function_exists('user_id') ? user_id() : null,
        ], true);

        $this->seedDefaultFields((int) $idTemplate);

        return redirect()->to(base_url('backend/penghargaan/template/editor/' . $idTemplate))
            ->with('success', 'Template berhasil dibuat. Silakan atur posisi field di bawah ini.');
    }

    /**
     * Halaman Visual Editor untuk menyusun posisi dan gaya field overlay.
     */
    public function editor(int $id)
    {
        $template = $this->templateModel->find($id);
        $idTpq    = (string) (session()->get('IdTpq') ?? '0');
        $isAdmin  = $this->isAdminUser();

        if (!$template || !$this->canEditTemplate($template, $idTpq, $isAdmin)) {
            return redirect()->to(base_url('backend/penghargaan/template'))
                ->with('error', 'Akses ditolak: Anda hanya dapat mengedit template milik Anda sendiri.');
        }

        $fields = $this->fieldModel->getByTemplate($id, false);
        if (empty($fields)) {
            $this->seedDefaultFields($id);
            $fields = $this->fieldModel->getByTemplate($id, false);
        }

        $listJenis = $this->jenisModel->getAvailableByTpq($idTpq, true);

        return view('backend/penghargaan/template/editor', [
            'page_title' => 'Editor Template: ' . $template['NamaTemplate'],
            'template'   => $template,
            'fields'     => $fields,
            'listJenis'  => $listJenis,
            'isAdmin'    => $isAdmin,
        ]);
    }

    /**
     * Simpan perubahan properti template, background baru (jika ada), dan koordinat field.
     */
    public function saveFields(int $id)
    {
        $template = $this->templateModel->find($id);
        $idTpq    = (string) (session()->get('IdTpq') ?? '0');
        $isAdmin  = $this->isAdminUser();

        if (!$template || !$this->canEditTemplate($template, $idTpq, $isAdmin)) {
            return redirect()->to(base_url('backend/penghargaan/template'))
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin mengubah template ini.');
        }

        $updateTemplate = [
            'NamaTemplate'       => trim((string) $this->request->getPost('NamaTemplate')) ?: $template['NamaTemplate'],
            'IdJenisPenghargaan' => $this->request->getPost('IdJenisPenghargaan') ? (int) $this->request->getPost('IdJenisPenghargaan') : null,
            'UkuranKertas'       => $this->request->getPost('UkuranKertas') ?: $template['UkuranKertas'],
            'Orientasi'          => $this->request->getPost('Orientasi') ?: $template['Orientasi'],
            'Status'             => $this->request->getPost('Status') ?: $template['Status'],
        ];
        if ($isAdmin) {
            $updateTemplate['AllowDuplicate'] = $this->request->getPost('AllowDuplicate') ? 1 : 0;
        }

        $file = $this->request->getFile('BackgroundFile');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp'], true)) {
                $uploadDir = FCPATH . 'uploads/penghargaan/templates/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newName = 'bg_' . ($template['IdTpq'] ?? '0') . '_' . time() . '.' . $ext;
                $file->move($uploadDir, $newName);
                $updateTemplate['BackgroundFile'] = 'uploads/penghargaan/templates/' . $newName;
            }
        }

        $this->templateModel->update($id, $updateTemplate);

        $fieldsPost = $this->request->getPost('fields');
        if (is_array($fieldsPost)) {
            foreach ($fieldsPost as $fieldId => $fData) {
                $this->fieldModel->update((int) $fieldId, [
                    'IsAktif'    => !empty($fData['IsAktif']) ? 1 : 0,
                    'PosisiX'    => (int) ($fData['PosisiX'] ?? 0),
                    'PosisiY'    => (int) ($fData['PosisiY'] ?? 0),
                    'FontSize'   => max(8, (int) ($fData['FontSize'] ?? 14)),
                    'FontFamily' => (string) ($fData['FontFamily'] ?? 'Arial'),
                    'FontBold'   => !empty($fData['FontBold']) ? 1 : 0,
                    'FontColor'  => (string) ($fData['FontColor'] ?? '#000000'),
                    'TextAlign'  => (string) ($fData['TextAlign'] ?? 'center'),
                    'Lebar'      => max(40, (int) ($fData['Lebar'] ?? 300)),
                    'Tinggi'     => max(20, (int) ($fData['Tinggi'] ?? 40)),
                ]);
            }
        }

        return redirect()->to(base_url('backend/penghargaan/template/editor/' . $id))
            ->with('success', 'Desain dan posisi field template berhasil disimpan.');
    }

    /**
     * Preview template dengan data contoh santri.
     */
    public function preview(int $id)
    {
        $template = $this->templateModel->find($id);
        if (!$template) {
            return redirect()->to(base_url('backend/penghargaan/template'))
                ->with('error', 'Template tidak ditemukan.');
        }

        $fields = $this->fieldModel->getByTemplate($id, true);

        return view('backend/penghargaan/template/preview', [
            'page_title' => 'Preview Template: ' . $template['NamaTemplate'],
            'template'   => $template,
            'fields'     => $fields,
        ]);
    }

    /**
     * Duplikasi template (misal dari starter sistem ke template milik lembaga).
     */
    public function duplicate(int $id)
    {
        $src     = $this->templateModel->find($id);
        $idTpq   = (string) (session()->get('IdTpq') ?? '0');
        $isAdmin = $this->isAdminUser();

        if (!$src) {
            return redirect()->back()->with('error', 'Template sumber tidak ditemukan.');
        }

        $srcIsAdmin = empty($src['IdTpq']) || (string) $src['IdTpq'] === '0';
        if (!$isAdmin) {
            $allowedAdmin = $srcIsAdmin && ($src['Status'] ?? '') === 'aktif' && !empty($src['AllowDuplicate']);
            $allowedOwn   = (string) ($src['IdTpq'] ?? '') === $idTpq;
            if (!$allowedAdmin && !$allowedOwn) {
                return redirect()->back()->with('error', 'Template ini tidak diizinkan untuk diduplikasi.');
            }
        }

        $newId = $this->templateModel->insert([
            'IdTpq'              => $isAdmin ? '0' : $idTpq,
            'IdJenisPenghargaan' => $src['IdJenisPenghargaan'],
            'NamaTemplate'       => $src['NamaTemplate'] . ' (Salinan)',
            'BackgroundFile'     => $src['BackgroundFile'],
            'UkuranKertas'       => $src['UkuranKertas'],
            'Orientasi'          => $src['Orientasi'],
            'IsDefault'          => 0,
            'AllowDuplicate'     => $isAdmin ? 1 : 0,
            'Status'             => 'draft',
            'CreatedBy'          => function_exists('user_id') ? user_id() : null,
        ], true);

        $srcFields = $this->fieldModel->getByTemplate($id, false);
        foreach ($srcFields as $f) {
            unset($f['Id'], $f['CreatedAt'], $f['UpdatedAt']);
            $f['IdTemplate'] = $newId;
            $this->fieldModel->insert($f);
        }

        return redirect()->to(base_url('backend/penghargaan/template/editor/' . $newId))
            ->with('success', 'Template berhasil diduplikasi. Silakan sesuaikan desainnya.');
    }

    /**
     * Tandai 1 template sebagai DIPILIH (eksklusif per grup Semua Jenis / Jenis tertentu).
     */
    public function select(int $id)
    {
        $idTpq   = (string) (session()->get('IdTpq') ?? '0');
        $isAdmin = $this->isAdminUser();
        $target  = $this->templateModel->find($id);

        if (!$target || !$this->canEditTemplate($target, $idTpq, $isAdmin)) {
            $msg = 'Silakan duplikat template Admin terlebih dahulu sebelum menjadikannya pilihan TPQ Anda.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $ok = $this->templateModel->setSelectedTemplate($id, $idTpq, $isAdmin);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $ok,
                'message' => $ok
                    ? 'Template berhasil dipilih dan diaktifkan untuk kelompoknya.'
                    : 'Template tidak ditemukan.',
            ]);
        }

        if (!$ok) {
            return redirect()->back()->with('error', 'Template tidak ditemukan.');
        }

        return redirect()->to(base_url('backend/penghargaan/template'))
            ->with('success', 'Template berhasil dipilih dan diaktifkan untuk kelompoknya.');
    }

    /**
     * Hapus template milik sendiri.
     */
    public function delete(int $id)
    {
        $row     = $this->templateModel->find($id);
        $idTpq   = (string) (session()->get('IdTpq') ?? '0');
        $isAdmin = $this->isAdminUser();

        if (!$row || !$this->canEditTemplate($row, $idTpq, $isAdmin)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin menghapus template ini.');
        }

        $this->fieldModel->where('IdTemplate', $id)->delete();
        $this->templateModel->delete($id);

        return redirect()->to(base_url('backend/penghargaan/template'))
            ->with('success', 'Template berhasil dihapus.');
    }

    /**
     * Pastikan 3 template starter (Formal, Modern, Islami) tersedia di database.
     */
    private function ensureStarterTemplates(): void
    {
        $existingCount = $this->templateModel->where('IdTpq IS NULL')->countAllResults();
        if ($existingCount > 0) {
            return;
        }

        $starters = [
            [
                'NamaTemplate'   => 'Starter Formal (Emas & Navy)',
                'BackgroundFile' => 'uploads/penghargaan/templates/starter_formal.svg',
                'IsDefault'      => 1,
            ],
            [
                'NamaTemplate'   => 'Starter Modern (Teal Minimalis)',
                'BackgroundFile' => 'uploads/penghargaan/templates/starter_modern.svg',
                'IsDefault'      => 0,
            ],
            [
                'NamaTemplate'   => 'Starter Islami (Hijau & Emas)',
                'BackgroundFile' => 'uploads/penghargaan/templates/starter_islami.svg',
                'IsDefault'      => 0,
            ],
        ];

        foreach ($starters as $st) {
            $id = $this->templateModel->insert([
                'IdTpq'              => null,
                'IdJenisPenghargaan' => null,
                'NamaTemplate'       => $st['NamaTemplate'],
                'BackgroundFile'     => $st['BackgroundFile'],
                'UkuranKertas'       => 'A4',
                'Orientasi'          => 'landscape',
                'IsDefault'          => $st['IsDefault'],
                'Status'             => 'aktif',
            ], true);

            $this->seedDefaultFields((int) $id);
        }
    }

    /**
     * Seed koordinat field default untuk template landscape 1123x794 px.
     */
    private function seedDefaultFields(int $idTemplate): void
    {
        $defaults = [
            ['LOGO_LEMBAGA',      1, 521,  65,  14, 'Arial', 0, '#000000', 'center', 80,  80],
            ['NAMA_LEMBAGA',      1, 211, 155,  18, 'Georgia', 1, '#1e3a5f', 'center', 700, 35],
            ['JUDUL_SERTIFIKAT',  1, 211, 200,  34, 'Georgia', 1, '#b8860b', 'center', 700, 50],
            ['NOMOR_SERTIFIKAT',  1, 311, 255,  13, 'Arial', 0, '#555555', 'center', 500, 28],
            ['NAMA_SANTRI',       1, 161, 325,  32, 'Georgia', 1, '#111827', 'center', 800, 52],
            ['KELAS',             1, 311, 382,  16, 'Arial', 0, '#374151', 'center', 500, 30],
            ['JENIS_PENGHARGAAN', 1, 211, 425,  22, 'Georgia', 1, '#0f766e', 'center', 700, 38],
            ['PERINGKAT',         1, 361, 468,  16, 'Arial', 1, '#b45309', 'center', 400, 30],
            ['PERIODE',           1, 311, 502,  14, 'Arial', 0, '#4b5563', 'center', 500, 28],
            ['NILAI_RATA_RATA',   0, 311, 535,  13, 'Arial', 0, '#4b5563', 'center', 240, 25],
            ['PERSEN_KEHADIRAN',  0, 571, 535,  13, 'Arial', 0, '#4b5563', 'center', 240, 25],
            ['TANGGAL_CETAK',     1, 760, 560,  13, 'Arial', 0, '#374151', 'center', 260, 26],
            ['TTD_1_JABATAN',     1, 110, 590,  13, 'Arial', 0, '#111827', 'center', 240, 26],
            ['TTD_1_QR',          1, 185, 620,  12, 'Arial', 0, '#111827', 'center', 90,  90],
            ['TTD_1_NAMA',        1, 110, 712,  13, 'Arial', 1, '#111827', 'center', 240, 26],
            ['TTD_2_JABATAN',     1, 770, 590,  13, 'Arial', 0, '#111827', 'center', 240, 26],
            ['TTD_2_QR',          1, 845, 620,  12, 'Arial', 0, '#111827', 'center', 90,  90],
            ['TTD_2_NAMA',        1, 770, 712,  13, 'Arial', 1, '#111827', 'center', 240, 26],
            ['TTD_3_JABATAN',     0, 440, 590,  13, 'Arial', 0, '#111827', 'center', 240, 26],
            ['TTD_3_QR',          0, 515, 620,  12, 'Arial', 0, '#111827', 'center', 90,  90],
            ['TTD_3_NAMA',        0, 440, 712,  13, 'Arial', 1, '#111827', 'center', 240, 26],
        ];

        foreach ($defaults as $d) {
            $this->fieldModel->insert([
                'IdTemplate' => $idTemplate,
                'NamaField'  => $d[0],
                'IsAktif'    => $d[1],
                'PosisiX'    => $d[2],
                'PosisiY'    => $d[3],
                'FontSize'   => $d[4],
                'FontFamily' => $d[5],
                'FontBold'   => $d[6],
                'FontColor'  => $d[7],
                'TextAlign'  => $d[8],
                'Lebar'      => $d[9],
                'Tinggi'     => $d[10],
            ]);
        }
    }
}
