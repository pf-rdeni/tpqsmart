<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratModel;
use App\Models\Administrasi\SuratTemplateModel;
use App\Models\Administrasi\SuratKategoriModel;
use App\Models\Administrasi\SuratNomorModel;
use App\Models\Administrasi\SuratPenerimaModel;
use App\Models\Administrasi\SuratTujuanMasterModel;
use App\Models\Administrasi\SuratKopCustomModel;
use App\Models\GuruModel;
use App\Models\TpqModel;
use App\Models\HelpFunctionModel;
use App\Models\QrCodeModel;
use Mpdf\Mpdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use ZipArchive;

class SuratKeluar extends BaseController
{
    protected $suratModel;
    protected $templateModel;
    protected $kategoriModel;
    protected $nomorModel;
    protected $penerimaModel;
    protected $tujuanMasterModel;
    protected $kopCustomModel;
    protected $guruModel;
    protected $tpqModel;
    protected $helpFunction;
    protected $qrCodeModel;

    public function __construct()
    {
        $this->suratModel        = new SuratModel();
        $this->templateModel     = new SuratTemplateModel();
        $this->kategoriModel     = new SuratKategoriModel();
        $this->nomorModel        = new SuratNomorModel();
        $this->penerimaModel     = new SuratPenerimaModel();
        $this->tujuanMasterModel = new SuratTujuanMasterModel();
        $this->kopCustomModel    = new SuratKopCustomModel();
        $this->guruModel         = new GuruModel();
        $this->tpqModel          = new TpqModel();
        $this->helpFunction      = new HelpFunctionModel();
        $this->qrCodeModel       = new QrCodeModel();
        helper(['kop_surat']);
    }

    /**
     * Arsip Surat Keluar (Index)
     */
    public function index()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $kategoriId = $this->request->getGet('kategori');
        $status     = $this->request->getGet('status');

        $builder = $this->suratModel->select('tbl_surat.*, k.NamaKategori, k.Kode as KodeKategori, k.Warna as WarnaKategori, t.NamaTpq,
                                            COUNT(p.Id) as TotalPenerima')
                                    ->join('tbl_surat_kategori k', 'k.Id = tbl_surat.IdKategori', 'left')
                                    ->join('tbl_tpq t', 't.IdTpq = tbl_surat.IdTpq', 'left')
                                    ->join('tbl_surat_penerima p', 'p.IdSurat = tbl_surat.Id', 'left')
                                    ->where('tbl_surat.JenisSurat', 'Keluar');

        if (!$isAdmin && !empty($idTpq)) {
            $builder->where('tbl_surat.IdTpq', $idTpq);
        }

        if (!empty($kategoriId)) {
            $builder->where('tbl_surat.IdKategori', $kategoriId);
        }

        if (!empty($status)) {
            $builder->where('tbl_surat.Status', $status);
        }

        $suratList = $builder->groupBy('tbl_surat.Id')
                             ->orderBy('tbl_surat.Id', 'DESC')
                             ->findAll();

        $kategoriList = $this->kategoriModel->getByJenis('Keluar');

        $data = [
            'page_title'   => 'Arsip Surat Keluar',
            'suratList'    => $suratList,
            'kategoriList' => $kategoriList,
            'idTpq'        => $idTpq,
            'isAdmin'      => $isAdmin,
        ];

        return view('backend/administrasi/surat_keluar/index', $data);
    }

    /**
     * Wizard Pembuatan Surat Baru (8 Langkah)
     */
    public function buat()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $templates = $this->templateModel->getActiveByTpq($isAdmin ? null : $idTpq);
        $kategori  = $this->kategoriModel->getByJenis('Keluar');
        $kopCustom = $this->kopCustomModel->where('IsActive', 1)->findAll();

        // Data Guru untuk pilihan Tanda Tangan & Penerima
        $guruList = $this->getGuruListForSurat($idTpq, $isAdmin);

        // Data Kelas untuk filter santri
        $db = \Config\Database::connect();
        $kelasList = $db->table('tbl_kelas')->orderBy('NamaKelas', 'ASC')->get()->getResultArray();

        // Tujuan Master
        $tujuanMaster = $this->tujuanMasterModel->getByTpq($isAdmin ? null : $idTpq);

        $listTpq = $this->helpFunction->getDataTpq(false);

        $data = [
            'page_title'   => 'Buat Surat Keluar Baru',
            'surat'        => null,
            'penerima'     => [],
            'isEdit'       => false,
            'isDuplicate'  => false,
            'templates'    => $templates,
            'kategori'     => $kategori,
            'kopCustom'    => $kopCustom,
            'guruList'     => $guruList,
            'kelasList'    => $kelasList,
            'tujuanMaster' => $tujuanMaster,
            'listTpq'      => $listTpq,
            'idTpq'        => $idTpq,
            'isAdmin'      => $isAdmin,
        ];

        return view('backend/administrasi/surat_keluar/wizard', $data);
    }

    /**
     * Edit Surat Keluar (Fase 4)
     */
    public function edit($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('backend/administrasi/surat-keluar'))->with('error', 'Surat tidak ditemukan');
        }

        if (!$isAdmin && $surat['IdTpq'] != $idTpq) {
            return redirect()->to(base_url('backend/administrasi/surat-keluar'))->with('error', 'Akses ditolak');
        }

        $penerima     = $this->penerimaModel->getBySurat($id);
        $templates    = $this->templateModel->getActiveByTpq($isAdmin ? null : $idTpq);
        $kategori     = $this->kategoriModel->getByJenis('Keluar');
        $kopCustom    = $this->kopCustomModel->where('IsActive', 1)->findAll();

        $guruList = $this->getGuruListForSurat($idTpq, $isAdmin, $surat);

        $db = \Config\Database::connect();
        $kelasList    = $db->table('tbl_kelas')->orderBy('NamaKelas', 'ASC')->get()->getResultArray();
        $tujuanMaster = $this->tujuanMasterModel->getByTpq($isAdmin ? null : $idTpq);
        $listTpq      = $this->helpFunction->getDataTpq(false);

        $templateSurat = !empty($surat['IdTemplate']) ? $this->templateModel->find($surat['IdTemplate']) : null;

        $data = [
            'page_title'    => 'Edit Surat Keluar',
            'surat'         => $surat,
            'templateSurat' => $templateSurat,
            'penerima'      => $penerima,
            'isEdit'        => true,
            'isDuplicate'   => false,
            'templates'     => $templates,
            'kategori'      => $kategori,
            'kopCustom'     => $kopCustom,
            'guruList'      => $guruList,
            'kelasList'     => $kelasList,
            'tujuanMaster'  => $tujuanMaster,
            'listTpq'       => $listTpq,
            'idTpq'         => $idTpq,
            'isAdmin'       => $isAdmin,
        ];

        return view('backend/administrasi/surat_keluar/wizard', $data);
    }

    /**
     * Duplikat Surat Keluar (Fase 4)
     */
    public function duplikat($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('backend/administrasi/surat-keluar'))->with('error', 'Surat sumber tidak ditemukan');
        }

        if (!$isAdmin && $surat['IdTpq'] != $idTpq) {
            return redirect()->to(base_url('backend/administrasi/surat-keluar'))->with('error', 'Akses ditolak');
        }

        $penerima = $this->penerimaModel->getBySurat($id);

        // Reset atribut khusus untuk duplikasi
        $surat['Id']         = null;
        $surat['Tanggal']    = date('Y-m-d');
        $surat['TanggalTtd'] = date('Y-m-d');
        $surat['NomorSurat'] = ''; // Di-generate baru

        $templates    = $this->templateModel->getActiveByTpq($isAdmin ? null : $idTpq);
        $kategori     = $this->kategoriModel->getByJenis('Keluar');
        $kopCustom    = $this->kopCustomModel->where('IsActive', 1)->findAll();

        $guruList = $this->getGuruListForSurat($idTpq, $isAdmin, $surat);

        $db = \Config\Database::connect();
        $kelasList    = $db->table('tbl_kelas')->orderBy('NamaKelas', 'ASC')->get()->getResultArray();
        $tujuanMaster = $this->tujuanMasterModel->getByTpq($isAdmin ? null : $idTpq);
        $listTpq      = $this->helpFunction->getDataTpq(false);

        $templateSurat = !empty($surat['IdTemplate']) ? $this->templateModel->find($surat['IdTemplate']) : null;

        $data = [
            'page_title'    => 'Duplikasi Surat Keluar',
            'surat'         => $surat,
            'templateSurat' => $templateSurat,
            'penerima'      => $penerima,
            'isEdit'        => false,
            'isDuplicate'   => true,
            'templates'     => $templates,
            'kategori'      => $kategori,
            'kopCustom'     => $kopCustom,
            'guruList'      => $guruList,
            'kelasList'     => $kelasList,
            'tujuanMaster'  => $tujuanMaster,
            'listTpq'       => $listTpq,
            'idTpq'         => $idTpq,
            'isAdmin'       => $isAdmin,
        ];

        return view('backend/administrasi/surat_keluar/wizard', $data);
    }

    /**
     * AJAX: Ambil detail template
     */
    public function ajaxGetTemplate($id = null)
    {
        $template = $this->templateModel->find($id);
        if (!$template) {
            return $this->response->setJSON(['success' => false, 'message' => 'Template tidak ditemukan']);
        }

        // Ambil info kategori
        $kategori = $this->kategoriModel->find($template['IdKategori']);
        $template['KodeKategori'] = $kategori['Kode'] ?? 'SURAT';

        return $this->response->setJSON(['success' => true, 'template' => $template]);
    }

    /**
     * AJAX: Ambil daftar penerima dinamis (Guru / Santri / Tujuan Master)
     */
    public function ajaxGetRecipients()
    {
        $type    = $this->request->getGet('type');
        $idTpq   = session()->get('IdTpq');
        $idKelas = $this->request->getGet('id_kelas');
        $db      = \Config\Database::connect();

        $results = [];

        if ($type === 'guru') {
            $builder = $db->table('tbl_guru')
                          ->select('IdGuru as id, Nama as nama, TempatTugas as info, NoHp as kontak')
                          ->where('Status', 1);
            if (!empty($idTpq)) {
                $builder->where('IdTpq', $idTpq);
            }
            $raw = $builder->orderBy('Nama', 'ASC')->get()->getResultArray();
            foreach ($raw as $r) {
                $results[] = [
                    'id'     => $r['id'],
                    'nama'   => formatNamaGelar($r['nama']),
                    'info'   => $r['info'],
                    'kontak' => $r['kontak']
                ];
            }
        } 
        elseif ($type === 'wali_santri' || $type === 'santri') {
            $builder = $db->table('tbl_santri_baru s')
                          ->select('s.IdSantri as id, s.NamaSantri, s.NamaAyah, s.NamaIbu, s.NamaWali, s.NoHpSantri, s.NoHpAyah, COALESCE(k_ks.NamaKelas, k.NamaKelas) as NamaKelas')
                          ->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left')
                          ->join('tbl_kelas_santri ks', 'ks.IdSantri = s.IdSantri AND ks.Status = 1', 'left')
                          ->join('tbl_kelas k_ks', 'k_ks.IdKelas = ks.IdKelas', 'left')
                          ->where('s.Active', 1);

            if (!empty($idTpq)) {
                $builder->where('s.IdTpq', $idTpq);
            }
            if (!empty($idKelas)) {
                $builder->groupStart()
                        ->where('s.IdKelas', $idKelas)
                        ->orWhere('ks.IdKelas', $idKelas)
                        ->groupEnd();
            }

            $raw = $builder->groupBy('s.IdSantri')->orderBy('s.NamaSantri', 'ASC')->get()->getResultArray();

            foreach ($raw as $r) {
                $waliName = !empty($r['NamaAyah']) ? $r['NamaAyah'] : (!empty($r['NamaIbu']) ? $r['NamaIbu'] : (!empty($r['NamaWali']) ? $r['NamaWali'] : 'Orang Tua / Wali'));
                $kontak   = !empty($r['NoHpAyah']) ? $r['NoHpAyah'] : $r['NoHpSantri'];
                $waliFormatted   = formatNamaGelar($waliName);
                $santriFormatted = formatNamaGelar($r['NamaSantri']);

                if ($type === 'wali_santri') {
                    $results[] = [
                        'id'     => $r['id'],
                        'nama'   => $waliFormatted . ' (Wali ' . $santriFormatted . ')',
                        'info'   => 'Kelas: ' . ($r['NamaKelas'] ?: '-'),
                        'kontak' => $kontak,
                        'santri' => $santriFormatted,
                        'wali'   => $waliFormatted,
                        'kelas'  => $r['NamaKelas']
                    ];
                } else {
                    $results[] = [
                        'id'     => $r['id'],
                        'nama'   => $santriFormatted,
                        'info'   => 'Kelas: ' . ($r['NamaKelas'] ?: '-'),
                        'kontak' => $kontak,
                        'santri' => $santriFormatted,
                        'wali'   => $waliFormatted,
                        'kelas'  => $r['NamaKelas']
                    ];
                }
            }
        }
        elseif ($type === 'tujuan_master' || $type === 'master') {
            $kategoriTujuan = $this->request->getGet('kategori_tujuan');
            $builder = $db->table('tbl_surat_tujuan_master')
                          ->select('Id as id, NamaTujuan as nama, Jabatan, Instansi, Alamat, Kota, NoHp as kontak, KategoriTujuan')
                          ->where('IsActive', 1);

            if (!empty($idTpq)) {
                $builder->groupStart()
                        ->where('IdTpq', $idTpq)
                        ->orWhere('IdTpq IS NULL', null, false)
                        ->orWhere('IdTpq', 0)
                        ->groupEnd();
            }

            if (!empty($kategoriTujuan)) {
                $builder->where('KategoriTujuan', $kategoriTujuan);
            }
            $raw = $builder->orderBy('NamaTujuan', 'ASC')->get()->getResultArray();
            foreach ($raw as $r) {
                $info = trim(($r['Jabatan'] ? $r['Jabatan'] . ' - ' : '') . ($r['Instansi'] ?: ''));
                $results[] = [
                    'id'       => $r['id'],
                    'nama'     => formatNamaGelar($r['nama']),
                    'info'     => $info ?: ($r['Kota'] ?: '-'),
                    'jabatan'  => $r['Jabatan'] ?? '',
                    'instansi' => $r['Instansi'] ?? '',
                    'alamat'   => $r['Alamat'] ?? '',
                    'kota'     => $r['Kota'] ?? '',
                    'kontak'   => $r['kontak'] ?? '',
                    'kategori' => $r['KategoriTujuan'] ?? ''
                ];
            }
        }

        return $this->response->setJSON(['success' => true, 'data' => $results]);
    }

    /**
     * AJAX: Hitung nomor surat otomatis berikutnya
     */
    public function ajaxGenerateNomor()
    {
        $idTpq      = session()->get('IdTpq');
        $isAdmin    = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));
        $postIdTpq  = $this->request->getPost('id_tpq');
        $targetTpq  = ($postIdTpq !== null && $postIdTpq !== '') ? (int)$postIdTpq : ($isAdmin ? 0 : (int)$idTpq);

        $idKategori = (int)$this->request->getPost('id_kategori');
        $format     = $this->request->getPost('format') ?: '[NO:3]/[KODE-TPQ]/[KODE-KAT]/[BLN-ROMAWI]/[TAHUN]';
        $tahun      = (int)date('Y');
        $bulan      = (int)date('n');

        $kategori = $this->kategoriModel->find($idKategori);
        $kodeKat  = $kategori['Kode'] ?? 'SURAT';

        if ($targetTpq === 0) {
            $kodeTpq = 'FKPQ';
        } else {
            $tpqData  = $this->tpqModel->find($targetTpq);
            $kodeTpq  = !empty($tpqData['NamaTpq']) ? preg_replace('/[^A-Za-z0-9]/', '', substr($tpqData['NamaTpq'], 0, 10)) : 'TPQ';
        }

        // Hitung counter saat ini + 1 (simulasi, belum commit)
        $row = $this->nomorModel->where('IdTpq', $targetTpq)
                                ->where('IdKategori', $idKategori)
                                ->where('Tahun', $tahun)
                                ->where('Bulan', $bulan)
                                ->first();

        $urutan = $row ? ((int)$row['UrutanTerakhir'] + 1) : 1;

        $nomorSurat = $this->formatStringNomor($format, $urutan, $kodeTpq, $kodeKat, $bulan, $tahun);

        return $this->response->setJSON([
            'success'    => true,
            'nomorSurat' => $nomorSurat,
            'urutan'     => $urutan
        ]);
    }

    /**
     * AJAX: Preview HTML Surat (Step 7)
     */
    public function ajaxPreviewHtml()
    {
        $postData = $this->request->getPost();
        $rendered = $this->prepareSuratViewData($postData);

        return $this->response->setJSON([
            'success' => true,
            'html'    => view('backend/administrasi/surat_keluar/pdf/surat_template_pdf', $rendered)
        ]);
    }

    /**
     * Simpan Surat (Draft atau Final)
     */
    public function simpan()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $rules = [
            'IdTemplate' => 'required|numeric',
            'Hal'        => 'required|min_length[3]',
            'NomorSurat' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $postTpq    = $this->request->getPost('IdTpq');
        $targetTpq  = $isAdmin ? (($postTpq !== null && $postTpq !== '') ? (int)$postTpq : 0) : (int)$idTpq;
        $idTemplate = (int)$this->request->getPost('IdTemplate');
        $template   = $this->templateModel->find($idTemplate);
        $idKategori = (int)($template['IdKategori'] ?? $this->request->getPost('IdKategori') ?? 1);
        $status     = $this->request->getPost('Status') ?: 'Final'; // Langsung Final sesuai instruksi

        $tahun  = (int)date('Y');
        $bulan  = (int)date('n');
        $urutan = (int)$this->request->getPost('UrutanNomor') ?: 1;

        // Ambil data penerima list jika ada
        $penerimaList = $this->request->getPost('PenerimaList');
        if (!empty($penerimaList) && is_string($penerimaList)) {
            $penerimaList = json_decode($penerimaList, true);
        }

        $tujuanNama    = formatNamaGelar($this->request->getPost('TujuanNama'));
        $tujuanJabatan = $this->request->getPost('TujuanJabatan');
        if (empty($tujuanNama) && !empty($penerimaList) && is_array($penerimaList) && !empty($penerimaList[0]['nama'])) {
            $tujuanNama    = formatNamaGelar($penerimaList[0]['nama']);
            $tujuanJabatan = $penerimaList[0]['jabatan'] ?? '';
        }

        $suratData = [
            'IdTpq'            => $targetTpq,
            'IdTemplate'       => $idTemplate,
            'IdKategori'       => $idKategori,
            'JenisSurat'       => 'Keluar',
            'NomorSurat'       => $this->request->getPost('NomorSurat'),
            'UrutanNomor'      => $urutan,
            'Tahun'            => $tahun,
            'Bulan'            => $bulan,
            'Tanggal'          => $this->request->getPost('Tanggal') ?: date('Y-m-d'),
            'Hal'              => $this->request->getPost('Hal'),
            'Lampiran'         => $this->request->getPost('Lampiran'),
            'IdTujuanMaster'   => $this->request->getPost('IdTujuanMaster') ?: null,
            'TujuanNama'       => $tujuanNama,
            'TujuanJabatan'    => $tujuanJabatan,
            'TujuanInstansi'   => $this->request->getPost('TujuanInstansi'),
            'TujuanAlamat'     => $this->request->getPost('TujuanAlamat'),
            'TujuanKota'       => $this->request->getPost('TujuanKota') ?: 'Tempat',
            'SalamPembuka'     => $this->request->getPost('SalamPembuka'),
            'MukadimahIsi'     => $this->request->getPost('MukadimahIsi'),
            'IsiSurat'         => $this->request->getPost('IsiSurat'),
            'Penutup'          => $this->request->getPost('Penutup'),
            'SalamPenutup'     => $this->request->getPost('SalamPenutup'),
            'TempatTtd'              => $this->request->getPost('TempatTtd') ?: 'Bintan',
            'TanggalTtd'             => $this->request->getPost('TanggalTtd') ?: date('Y-m-d'),
            'IdPenandaTangan1'       => $this->request->getPost('IdPenandaTangan1') ?: null,
            'Jabatan1'               => $this->request->getPost('Jabatan1'),
            'ModeTtd1'               => $this->request->getPost('ModeTtd1') ?: 'Manual',
            'IdPenandaTangan2'       => $this->request->getPost('IdPenandaTangan2') ?: null,
            'Jabatan2'               => $this->request->getPost('Jabatan2'),
            'ModeTtd2'               => $this->request->getPost('ModeTtd2') ?: 'Manual',
            'PenandaTanganConfig'    => $this->request->getPost('PenandaTanganConfig') ?: null,
            'Status'           => $status,
            'Catatan'          => $this->request->getPost('Catatan'),
        ];

        $idSuratEdit = (int)$this->request->getPost('IdSurat');

        if (!empty($idSuratEdit)) {
            $existing = $this->suratModel->find($idSuratEdit);
            if (!$existing) {
                return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak ditemukan']);
            }
            if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
            }

            $suratData['UpdatedBy'] = function_exists('user_id') ? user_id() : null;
            $this->suratModel->update($idSuratEdit, $suratData);
            $idSurat = $idSuratEdit;

            // Hapus penerima lama lalu re-insert
            $this->penerimaModel->where('IdSurat', $idSurat)->delete();
        } else {
            // Jika surat baru dan status Final, update counter di tbl_surat_nomor
            if ($status === 'Final') {
                $this->nomorModel->getNextNomor($targetTpq, $idKategori, $tahun, $bulan);
            }

            $suratData['CreatedBy'] = function_exists('user_id') ? user_id() : null;
            $idSurat = $this->suratModel->insert($suratData);

            if (!$idSurat) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan surat']);
            }
        }

        // Simpan Penerima (Penerima Tunggal atau Penerima Massal)
        if (!empty($penerimaList) && is_array($penerimaList)) {
            foreach ($penerimaList as $p) {
                $this->penerimaModel->insert([
                    'IdSurat'         => $idSurat,
                    'TipePenerima'    => $p['tipe'] ?? 'Manual',
                    'IdReferensi'     => $p['id_ref'] ?? null,
                    'NamaPenerima'    => formatNamaGelar($p['nama'] ?? ''),
                    'JabatanPenerima' => $p['jabatan'] ?? '',
                    'StatusKirim'     => 'Belum',
                ]);
            }
        } else {
            // Jika penerima tunggal dari input text
            if (!empty($suratData['TujuanNama'])) {
                $this->penerimaModel->insert([
                    'IdSurat'         => $idSurat,
                    'TipePenerima'    => 'Manual',
                    'NamaPenerima'    => formatNamaGelar($suratData['TujuanNama']),
                    'JabatanPenerima' => $suratData['TujuanJabatan'],
                    'StatusKirim'     => 'Belum',
                ]);
            }
        }

        // ====================================================================
        // Fase 3 & 6: Proses PenandaTanganConfig, Token QR 10 Karakter & Auto-Save
        // ====================================================================
        $tokenModel = new \App\Models\Administrasi\SuratSignerTokenModel();
        $rawConfig  = $this->request->getPost('PenandaTanganConfig');

        if (!empty($rawConfig)) {
            $config = is_string($rawConfig) ? json_decode($rawConfig, true) : $rawConfig;
            if (is_array($config) && !empty($config['signers'])) {
                if ($status === 'Final') {
                    // Revoke token lama jika ada pembaruan dokumen (surat diedit ulang)
                    $tokenModel->revokeBySurat($idSurat, 'Pembaruan Dokumen Final');

                    foreach ($config['signers'] as $idx => &$signer) {
                        // Auto-save pejabat baru ke master tujuan jika dipilih
                        if (($signer['sumber'] ?? '') === 'manual' && !empty($signer['save_master']) && !empty($signer['nama'])) {
                            $this->tujuanMasterModel->insert([
                                'IdTpq'          => $targetTpq,
                                'KategoriTujuan' => 'Pejabat Pemerintah',
                                'NamaTujuan'     => function_exists('formatNamaGelar') ? formatNamaGelar($signer['nama']) : $signer['nama'],
                                'Jabatan'        => $signer['jabatan'] ?? '',
                                'Instansi'       => $signer['instansi'] ?? '',
                                'Nip'            => $signer['nip'] ?? '',
                                'IsActive'       => 1,
                            ]);
                        }

                        // Generate token 10 karakter jika mode QR
                        if (($signer['mode_ttd'] ?? 'Manual') === 'QR') {
                            $token10 = $tokenModel->generateUniqueToken();
                            $signer['token_10'] = $token10;

                            $tokenModel->insert([
                                'IdSurat'       => $idSurat,
                                'SignerIndex'   => $idx + 1,
                                'Token'         => $token10,
                                'Nama'          => function_exists('formatNamaGelar') ? formatNamaGelar($signer['nama'] ?? '') : ($signer['nama'] ?? ''),
                                'Jabatan'       => $signer['jabatan'] ?? '',
                                'Instansi'      => $signer['instansi'] ?? '',
                                'IsRevoked'     => 0,
                                'CreatedAt'     => date('Y-m-d H:i:s'),
                            ]);
                        } else {
                            $signer['token_10'] = null;
                        }
                    }
                    unset($signer);

                    // Update kembali config yang sudah terisi token_10 ke tbl_surat
                    $this->suratModel->update($idSurat, [
                        'PenandaTanganConfig' => json_encode($config)
                    ]);
                } elseif ($status === 'Draft' || $status === 'Dibatalkan') {
                    // Jika Draft atau Dibatalkan, revoke semua token
                    $tokenModel->revokeBySurat($idSurat, 'Status surat diubah menjadi ' . $status);
                }
            }
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => !empty($idSuratEdit) ? 'Perubahan surat berhasil disimpan' : 'Surat berhasil difinalisasi',
            'idSurat'   => $idSurat,
            'printUrl'  => base_url('backend/administrasi/surat-keluar/cetak/' . $idSurat),
            'zipUrl'    => base_url('backend/administrasi/surat-keluar/cetak-zip/' . $idSurat),
        ]);
    }

    /**
     * Cetak PDF on-demand (Streaming ke browser — tidak disimpan di server)
     * Mode: 'gabungan' (semua penerima dalam 1 file) atau per penerima
     */
    public function cetakPdf($idSurat = null)
    {
        $surat = $this->suratModel->find($idSurat);
        if (!$surat) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat tidak ditemukan');
        }

        $idTpqSession = session()->get('IdTpq');
        $isAdmin      = ($idTpqSession === '0' || $idTpqSession === 0 || empty($idTpqSession));
        if (!$isAdmin && $surat['IdTpq'] != $idTpqSession) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses ditolak: Dokumen bukan milik lembaga Anda');
        }

        $template = $this->templateModel->find($surat['IdTemplate']);
        $penerima = $this->penerimaModel->getBySurat($idSurat);

        if (empty($penerima)) {
            $penerima = [[
                'NamaPenerima'    => $surat['TujuanNama'],
                'JabatanPenerima' => $surat['TujuanJabatan']
            ]];
        }

        // Inisialisasi mPDF
        $ukuranKertas = !empty($template['UkuranKertas']) ? $template['UkuranKertas'] : 'A4';
        $mpdfFormat   = $this->resolveMpdfPaperFormat($ukuranKertas);
        $dompdfFormat = $this->resolveDompdfPaperFormat($ukuranKertas);
        $orientasi    = !empty($template['Orientasi']) ? ($template['Orientasi'] === 'Landscape' ? 'L' : 'P') : 'P';
        $marginTop    = !empty($template['MarginAtas']) ? (float)$template['MarginAtas'] : 20;
        $marginBottom = !empty($template['MarginBawah']) ? (float)$template['MarginBawah'] : 20;
        $marginLeft   = !empty($template['MarginKiri']) ? (float)$template['MarginKiri'] : 25;
        $marginRight  = !empty($template['MarginKanan']) ? (float)$template['MarginKanan'] : 15;

        try {
            $mpdf = new Mpdf([
                'format'        => $mpdfFormat,
                'orientation'   => $orientasi,
                'margin_top'    => $marginTop,
                'margin_bottom' => $marginBottom,
                'margin_left'   => $marginLeft,
                'margin_right'  => $marginRight,
                'tempDir'       => sys_get_temp_dir(),
            ]);

            $total = count($penerima);
            foreach ($penerima as $idx => $p) {
                if ($idx > 0) {
                    $mpdf->AddPage();
                }

                $viewData = $this->prepareSuratViewData($surat, $p, $template);
                $html = view('backend/administrasi/surat_keluar/pdf/surat_template_pdf', $viewData);
                $mpdf->WriteHTML($html);
            }

            $cleanNomor   = preg_replace('/[^A-Za-z0-9\-]/', '-', $surat['NomorSurat'] ?: 'SURAT');
            $penerimaName = !empty($penerima[0]['NamaPenerima']) ? preg_replace('/[^A-Za-z0-9]/', '', $penerima[0]['NamaPenerima']) : 'DOKUMEN';
            $filename     = date('Y-m-d') . '_' . $cleanNomor . '_' . ($total > 1 ? 'GABUNGAN' : $penerimaName) . '.pdf';

            // Stream langsung ke browser (inline display)
            return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                        ->setBody($mpdf->Output($filename, 'S'));

        } catch (\Exception $e) {
            // Fallback ke Dompdf jika ada kendala di mPDF
            log_message('error', 'mPDF error: ' . $e->getMessage() . ', trying Dompdf fallback');

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            $viewData = $this->prepareSuratViewData($surat, $penerima[0] ?? null, $template);
            $html = view('backend/administrasi/surat_keluar/pdf/surat_template_pdf', $viewData);
            $dompdf->loadHtml($html);
            $dompdf->setPaper($dompdfFormat, $orientasi === 'L' ? 'landscape' : 'portrait');
            $dompdf->render();

            $cleanNomor = preg_replace('/[^A-Za-z0-9\-]/', '-', $surat['NomorSurat'] ?: 'SURAT');
            $filename   = date('Y-m-d') . '_' . $cleanNomor . '.pdf';

            return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                        ->setBody($dompdf->output());
        }
    }

    /**
     * Cetak PDF Individual dikemas dalam 1 file ZIP (Streaming on-demand)
     */
    public function cetakZip($idSurat = null)
    {
        $surat = $this->suratModel->find($idSurat);
        if (!$surat) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat tidak ditemukan');
        }

        $idTpqSession = session()->get('IdTpq');
        $isAdmin      = ($idTpqSession === '0' || $idTpqSession === 0 || empty($idTpqSession));
        if (!$isAdmin && $surat['IdTpq'] != $idTpqSession) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses ditolak: Dokumen bukan milik lembaga Anda');
        }

        $template = $this->templateModel->find($surat['IdTemplate']);
        $penerima = $this->penerimaModel->getBySurat($idSurat);

        if (empty($penerima)) {
            $penerima = [[
                'NamaPenerima'    => $surat['TujuanNama'],
                'JabatanPenerima' => $surat['TujuanJabatan']
            ]];
        }

        $ukuranKertas = !empty($template['UkuranKertas']) ? $template['UkuranKertas'] : 'A4';
        $mpdfFormat   = $this->resolveMpdfPaperFormat($ukuranKertas);
        $orientasi    = !empty($template['Orientasi']) ? ($template['Orientasi'] === 'Landscape' ? 'L' : 'P') : 'P';
        $marginTop    = !empty($template['MarginAtas']) ? (float)$template['MarginAtas'] : 20;
        $marginBottom = !empty($template['MarginBawah']) ? (float)$template['MarginBawah'] : 20;
        $marginLeft   = !empty($template['MarginKiri']) ? (float)$template['MarginKiri'] : 25;
        $marginRight  = !empty($template['MarginKanan']) ? (float)$template['MarginKanan'] : 15;

        $zipFile = tempnam(sys_get_temp_dir(), 'zip_surat_');
        $zip     = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Gagal membuat file ZIP');
        }

        $cleanNomor = preg_replace('/[^A-Za-z0-9\-]/', '-', $surat['NomorSurat'] ?: 'SURAT');

        foreach ($penerima as $p) {
            $mpdf = new Mpdf([
                'format'        => $mpdfFormat,
                'orientation'   => $orientasi,
                'margin_top'    => $marginTop,
                'margin_bottom' => $marginBottom,
                'margin_left'   => $marginLeft,
                'margin_right'  => $marginRight,
                'tempDir'       => sys_get_temp_dir(),
            ]);

            $viewData = $this->prepareSuratViewData($surat, $p, $template);
            $html = view('backend/administrasi/surat_keluar/pdf/surat_template_pdf', $viewData);
            $mpdf->WriteHTML($html);
            $pdfContent = $mpdf->Output('', 'S');

            $cleanPenerima = preg_replace('/[^A-Za-z0-9]/', '_', $p['NamaPenerima'] ?: 'Penerima');
            $pdfName = date('Y-m-d') . '_' . $cleanNomor . '_' . $cleanPenerima . '.pdf';

            $zip->addFromString($pdfName, $pdfContent);
        }

        $zip->close();

        $zipDownloadName = date('Y-m-d') . '_' . $cleanNomor . '_INDIVIDUAL.zip';
        $zipContent = file_get_contents($zipFile);
        unlink($zipFile); // Hapus file zip sementara di server!

        return $this->response
                    ->setHeader('Content-Type', 'application/zip')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $zipDownloadName . '"')
                    ->setBody($zipContent);
    }

    /**
     * Hapus Draft Surat Keluar
     */
    public function delete($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak ditemukan']);
        }

        if (!$isAdmin && $surat['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        // Hapus penerima & revoke token tanda tangan
        $this->penerimaModel->where('IdSurat', $id)->delete();
        $tokenModel = new \App\Models\Administrasi\SuratSignerTokenModel();
        $tokenModel->revokeBySurat($id, 'Surat telah dihapus dari sistem');
        $this->suratModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Surat berhasil dihapus']);
    }

    // ----------------------------------------------------------------
    // Private Helpers
    // ----------------------------------------------------------------

    private function formatStringNomor($format, $urutan, $kodeTpq, $kodeKat, $bulan, $tahun)
    {
        $romawiMap = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ];
        $bulanRomawi = $romawiMap[$bulan] ?? 'I';

        $output = $format;
        if (preg_match('/\[NO:(\d+)\]/', $output, $matches)) {
            $padLen = (int)$matches[1];
            $output = str_replace($matches[0], str_pad($urutan, $padLen, '0', STR_PAD_LEFT), $output);
        } else {
            $output = str_replace('[NO]', str_pad($urutan, 3, '0', STR_PAD_LEFT), $output);
        }

        $output = str_replace('[KODE-TPQ]', $kodeTpq, $output);
        $output = str_replace('[KODE-KAT]', $kodeKat, $output);
        $output = str_replace('[BLN-ROMAWI]', $bulanRomawi, $output);
        $output = str_replace('[BLN]', str_pad($bulan, 2, '0', STR_PAD_LEFT), $output);
        $output = str_replace('[TAHUN]', $tahun, $output);
        $output = str_replace('[TAHUN-PENDEK]', substr($tahun, -2), $output);

        return $output;
    }

    private function prepareSuratViewData($surat, $penerima = null, $template = null)
    {
        if (empty($template) && !empty($surat['IdTemplate'])) {
            $template = $this->templateModel->find($surat['IdTemplate']);
        }
        $template = $template ?? [];

        // Timpa konfigurasi layout jika dispesifikasikan dalam input surat (misal saat live preview wizard)
        if (!empty($surat['UkuranKertas'])) {
            $template['UkuranKertas'] = $surat['UkuranKertas'];
        }
        if (!empty($surat['Orientasi'])) {
            $template['Orientasi'] = $surat['Orientasi'];
        }
        if (!empty($surat['FontFamily'])) {
            $template['FontFamily'] = $surat['FontFamily'];
        }
        if (!empty($surat['FontSize'])) {
            $template['FontSize'] = $surat['FontSize'];
        }
        if (isset($surat['MarginAtas']) && $surat['MarginAtas'] !== '') {
            $template['MarginAtas'] = $surat['MarginAtas'];
        }
        if (isset($surat['MarginBawah']) && $surat['MarginBawah'] !== '') {
            $template['MarginBawah'] = $surat['MarginBawah'];
        }
        if (isset($surat['MarginKiri']) && $surat['MarginKiri'] !== '') {
            $template['MarginKiri'] = $surat['MarginKiri'];
        }
        if (isset($surat['MarginKanan']) && $surat['MarginKanan'] !== '') {
            $template['MarginKanan'] = $surat['MarginKanan'];
        }
        if (!empty($surat['SumberKop'])) {
            $template['SumberKop'] = $surat['SumberKop'];
        }
        if (isset($surat['IdKopCustom'])) {
            $template['IdKopCustom'] = !empty($surat['IdKopCustom']) ? (int)$surat['IdKopCustom'] : null;
        }

        $idTpq = isset($surat['IdTpq']) ? (int)$surat['IdTpq'] : 0;
        $tpq   = ($idTpq > 0) ? $this->tpqModel->find($idTpq) : null;

        // 1. Resolve Kop menggunakan Helper Unified
        $sumberKop   = $template['SumberKop'] ?? 'TPQ';
        $idKopCustom = !empty($template['IdKopCustom']) ? (int)$template['IdKopCustom'] : null;
        $kopData     = getKopSuratData($sumberKop, $idTpq ?: null, $idKopCustom);
        $kopImageSrc = $kopData['imageSrc'];
        $kopText     = $kopData['text'];

        // 2. Resolve Tanda Tangan Dinamis & Multi-Signer QR Code
        $ttd1            = null;
        $ttd2            = null;
        $ttdConfig       = null;
        $renderedSigners = [];
        $rawConfig       = $surat['PenandaTanganConfig'] ?? null;

        if (!empty($rawConfig)) {
            $decodedConfig = is_string($rawConfig) ? json_decode($rawConfig, true) : $rawConfig;
            if (is_array($decodedConfig) && !empty($decodedConfig['signers'])) {
                $ttdConfig = $decodedConfig;
                foreach ($decodedConfig['signers'] as $idx => $s) {
                    $namaSigner = $s['nama'] ?? '';
                    if (empty($namaSigner)) {
                        if (($s['sumber'] ?? '') === 'guru' && !empty($s['ref_id'])) {
                            $g = $this->guruModel->find($s['ref_id']);
                            if ($g) $namaSigner = $g['Nama'];
                        } elseif (($s['sumber'] ?? '') === 'master_tujuan' && !empty($s['ref_id'])) {
                            $m = $this->tujuanMasterModel->find($s['ref_id']);
                            if ($m) $namaSigner = $m['NamaTujuan'];
                        } elseif (($s['sumber'] ?? '') === 'fkpq') {
                            $db = \Config\Database::connect();
                            $fkpq = $db->table('tbl_fkpq')->get()->getRowArray();
                            if (!empty($fkpq['KetuaFkpq'])) $namaSigner = $fkpq['KetuaFkpq'];
                        }
                    }
                    if (empty($namaSigner)) {
                        $namaSigner = '____________________';
                    } else {
                        $namaSigner = function_exists('formatNamaGelar') ? formatNamaGelar($namaSigner) : $namaSigner;
                    }

                    $qrSrc         = null;
                    $validationUrl = null;
                    if (($s['mode_ttd'] ?? 'Manual') === 'QR') {
                        // URL QR Code verifikasi bersih 10 karakter
                        if (!empty($s['token_10'])) {
                            $cleanUrl = base_url('signature/verify/' . $s['token_10']);
                        } elseif (!empty($surat['Id'])) {
                            // Cek apakah sudah ada token terdaftar di tbl_surat_signer_tokens
                            $tokenModel = new \App\Models\Administrasi\SuratSignerTokenModel();
                            $existingToken = $tokenModel->where('IdSurat', $surat['Id'])
                                                        ->where('SignerIndex', $idx + 1)
                                                        ->where('IsRevoked', 0)
                                                        ->first();
                            if ($existingToken) {
                                $cleanUrl = base_url('signature/verify/' . $existingToken['Token']);
                            } else {
                                $cleanUrl = base_url('signature/validateSurat/' . $surat['Id'] . '?signer=' . ($idx + 1));
                            }
                        } else {
                            $cleanUrl = base_url('signature/verify/preview?s=' . ($idx + 1));
                        }

                        // Render gambar QR matriks murni dari cleanUrl (agar saat discan kamera HP URL-nya 100% bersih tanpa ?v=1.0)
                        try {
                            $options = new \chillerlan\QRCode\QROptions([
                                'outputType'   => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                                'eccLevel'     => \chillerlan\QRCode\Common\EccLevel::M,
                                'imageBase64'  => true,
                                'addQuietzone' => true,
                                'quietzoneSize'=> 1,
                            ]);
                            $qrcode = new \chillerlan\QRCode\QRCode($options);
                            $qrSrc  = $qrcode->render($cleanUrl);
                        } catch (\Exception $e) {
                            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
                            $qrSrc = null;
                        }

                        // Normalisasi URL tautan mPDF (hanya jika host tanpa titik seperti localhost, sertakan query bertitik agar mPDF menjadikannya /URI external link bukan internal bookmark /Dest)
                        $validationUrl = $cleanUrl;
                        if (!empty($validationUrl) && strpos($validationUrl, '.') === false) {
                            $validationUrl .= (strpos($validationUrl, '?') !== false ? '&' : '?') . 'v=1.0';
                        }
                    }

                    $renderedSigners[] = [
                        'signer_id'        => $s['signer_id'] ?? ('signer_' . ($idx + 1)),
                        'posisi'           => $s['posisi'] ?? 'kanan',
                        'sumber'           => $s['sumber'] ?? 'guru',
                        'nama'             => $namaSigner,
                        'jabatan'          => $s['jabatan'] ?? '',
                        'instansi'         => $s['instansi'] ?? '',
                        'nip'              => $s['nip'] ?? null,
                        'show_nip'         => !empty($s['show_nip']),
                        'show_instansi'    => !empty($s['show_instansi']),
                        'mode'             => $s['mode_ttd'] ?? 'Manual',
                        'token_10'         => $s['token_10'] ?? ($existingToken['Token'] ?? null),
                        'validation_url'   => $validationUrl,
                        'qrSrc'            => $qrSrc,
                        'label_mengetahui' => $s['label_mengetahui'] ?? ($decodedConfig['label_mengetahui'] ?? 'Mengetahui,'),
                    ];
                }

                // Backward compatibility mapping for ttd1 & ttd2
                $kananSigner = null;
                $kiriSigner  = null;
                foreach ($renderedSigners as $rs) {
                    if ($rs['posisi'] === 'kanan' && !$kananSigner) $kananSigner = $rs;
                    if ($rs['posisi'] === 'kiri'  && !$kiriSigner)  $kiriSigner  = $rs;
                }
                $ttd1 = $kananSigner ?: ($renderedSigners[0] ?? null);
                $ttd2 = $kiriSigner  ?: ($renderedSigners[1] ?? null);
            }
        }

        // Fallback jika belum ada konfigurasi dinamis (surat lama)
        if (empty($renderedSigners) && (!isset($template['ShowMengetahui']) || !empty($template['ShowMengetahui']) || !empty($surat['IdPenandaTangan1']) || !empty($surat['Jabatan1']))) {
            $nama1  = '____________________';
            $nip1   = null;
            $qrSrc1 = null;

            if (!empty($surat['IdPenandaTangan1'])) {
                $guru1 = $this->guruModel->find($surat['IdPenandaTangan1']);
                if ($guru1) {
                    $nama1 = $guru1['Nama'];
                    $nip1  = $guru1['IdGuru'] ?? null;
                }
            } elseif ($sumberKop === 'FKPQ') {
                $db = \Config\Database::connect();
                $fkpq = $db->table('tbl_fkpq')->get()->getRowArray();
                if (!empty($fkpq['KetuaFkpq'])) {
                    $nama1 = $fkpq['KetuaFkpq'];
                }
            } elseif (!empty($tpq['KepalaSekolah'])) {
                $nama1 = $tpq['KepalaSekolah'];
            }

            // Generate QR Code Digital Verifikasi Resmi jika Mode QR
            $validationUrl1 = null;
            if (($surat['ModeTtd1'] ?? 'Manual') === 'QR') {
                $validationUrl1 = !empty($surat['Id']) 
                    ? base_url('signature/validateSurat/' . $surat['Id']) 
                    : base_url('signature/validateSurat?preview=1&no=' . urlencode($surat['NomorSurat'] ?? ''));

                if (!empty($validationUrl1) && strpos($validationUrl1, '.') === false) {
                    $validationUrl1 .= (strpos($validationUrl1, '?') !== false ? '&' : '?') . 'v=1.0';
                }

                try {
                    $options = new \chillerlan\QRCode\QROptions([
                        'outputType'   => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                        'eccLevel'     => \chillerlan\QRCode\Common\EccLevel::M,
                        'imageBase64'  => true,
                        'addQuietzone' => true,
                        'quietzoneSize'=> 1,
                    ]);
                    $qrcode = new \chillerlan\QRCode\QRCode($options);
                    $qrSrc1 = $qrcode->render($validationUrl1);
                } catch (\Exception $e) {
                    log_message('error', 'QR Code generation failed (TTD1): ' . $e->getMessage());
                    $qrSrc1 = null;
                }
            }

            $ttd1 = [
                'nama'           => $nama1,
                'jabatan'        => $surat['Jabatan1'] ?? ($sumberKop === 'FKPQ' ? 'Ketua FKPQ' : 'Kepala TPQ'),
                'qrSrc'          => $qrSrc1,
                'validation_url' => $validationUrl1,
                'nip'            => $nip1,
                'mode'           => $surat['ModeTtd1'] ?? 'Manual'
            ];

            if (!empty($surat['IdPenandaTangan2']) || !empty($surat['Jabatan2'])) {
                $nama2          = '____________________';
                $nip2           = null;
                $qrSrc2         = null;
                $validationUrl2 = null;

                if (!empty($surat['IdPenandaTangan2'])) {
                    $guru2 = $this->guruModel->find($surat['IdPenandaTangan2']);
                    if ($guru2) {
                        $nama2 = $guru2['Nama'];
                        $nip2  = $guru2['IdGuru'] ?? null;
                    }
                }

                if (($surat['ModeTtd2'] ?? 'Manual') === 'QR') {
                    $validationUrl2 = !empty($surat['Id']) 
                        ? base_url('signature/validateSurat/' . $surat['Id']) 
                        : base_url('signature/validateSurat?preview=1&no=' . urlencode($surat['NomorSurat'] ?? ''));

                    if (!empty($validationUrl2) && strpos($validationUrl2, '.') === false) {
                        $validationUrl2 .= (strpos($validationUrl2, '?') !== false ? '&' : '?') . 'v=1.0';
                    }

                    try {
                        $options = new \chillerlan\QRCode\QROptions([
                            'outputType'   => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                            'eccLevel'     => \chillerlan\QRCode\Common\EccLevel::M,
                            'imageBase64'  => true,
                            'addQuietzone' => true,
                            'quietzoneSize'=> 1,
                        ]);
                        $qrcode = new \chillerlan\QRCode\QRCode($options);
                        $qrSrc2 = $qrcode->render($validationUrl2);
                    } catch (\Exception $e) {
                        log_message('error', 'QR Code generation failed (TTD2): ' . $e->getMessage());
                        $qrSrc2 = null;
                    }
                }

                $ttd2 = [
                    'nama'           => $nama2,
                    'jabatan'        => $surat['Jabatan2'] ?? 'Sekretaris',
                    'qrSrc'          => $qrSrc2,
                    'validation_url' => $validationUrl2,
                    'nip'            => $nip2,
                    'mode'           => $surat['ModeTtd2'] ?? 'Manual'
                ];
            }
        }

        // 3. Substitusi Variabel Isi Surat
        $isiSurat = $surat['IsiSurat'] ?? '';
        $namaTujuanFinal = $penerima['NamaPenerima'] ?? $surat['TujuanNama'] ?? '-';
        $jabatanFinal    = $penerima['JabatanPenerima'] ?? $surat['TujuanJabatan'] ?? '';

        $placeholders = [
            '{{NOMOR_SURAT}}'   => $surat['NomorSurat'] ?? '-',
            '{{TANGGAL_SURAT}}' => !empty($surat['Tanggal']) ? formatTanggalIndonesia($surat['Tanggal']) : formatTanggalIndonesia(date('Y-m-d')),
            '{{HAL}}'           => $surat['Hal'] ?? '-',
            '{{LAMPIRAN}}'      => $surat['Lampiran'] ?? '-',
            '{{NAMA_TUJUAN}}'   => $namaTujuanFinal,
            '{{JABATAN_TUJUAN}}'=> $jabatanFinal,
            '{{NAMA_TPQ}}'      => $tpq['NamaTpq'] ?? 'TPQ',
            '{{ALAMAT_TPQ}}'    => $tpq['Alamat'] ?? '',
            '{{NAMA_GURU}}'     => $namaTujuanFinal,
            '{{NAMA_SANTRI}}'   => $penerima['santri'] ?? $namaTujuanFinal,
            '{{NAMA_WALI}}'     => $penerima['wali'] ?? $namaTujuanFinal,
            '{{NAMA_KELAS}}'    => $penerima['kelas'] ?? '',
        ];

        $renderedIsiSurat = str_replace(array_keys($placeholders), array_values($placeholders), $isiSurat);

        return [
            'surat'            => $surat,
            'penerima'         => $penerima,
            'template'         => $template,
            'kopImageSrc'      => $kopImageSrc,
            'kopText'          => $kopText,
            'ttd1'             => $ttd1,
            'ttd2'             => $ttd2,
            'ttdConfig'        => $ttdConfig,
            'renderedSigners'  => $renderedSigners,
            'renderedIsiSurat' => $renderedIsiSurat,
        ];
    }

    /**
     * Ambil daftar guru penanda tangan dengan memastikan guru tersimpan selalu ada di list
     */
    private function getGuruListForSurat($idTpq, $isAdmin, $surat = null): array
    {
        $builder = $this->guruModel->where('Status', 1);

        if ($isAdmin || empty($idTpq)) {
            $guruList = $builder->orderBy('Nama', 'ASC')->findAll();
        } else {
            $guruList = $builder->where('IdTpq', $idTpq)->orderBy('Nama', 'ASC')->findAll();
        }

        if (!empty($surat['IdPenandaTangan1'])) {
            $found = false;
            foreach ($guruList as $g) {
                if ((string)$g['IdGuru'] === (string)$surat['IdPenandaTangan1']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $g1 = $this->guruModel->find($surat['IdPenandaTangan1']);
                if ($g1) {
                    array_unshift($guruList, $g1);
                }
            }
        }

        if (!empty($surat['IdPenandaTangan2'])) {
            $found = false;
            foreach ($guruList as $g) {
                if ((string)$g['IdGuru'] === (string)$surat['IdPenandaTangan2']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $g2 = $this->guruModel->find($surat['IdPenandaTangan2']);
                if ($g2) {
                    $guruList[] = $g2;
                }
            }
        }

        return $guruList;
    }

    /**
     * Resolve format kertas untuk mPDF (mPDF memerlukan array [w, h] dalam mm untuk F4 / Folio)
     */
    private function resolveMpdfPaperFormat(string $ukuran): string|array
    {
        $ukuranUpper = strtoupper(trim($ukuran));
        if (in_array($ukuranUpper, ['F4', 'FOLIO'])) {
            return [215, 330];
        }
        return $ukuran;
    }

    /**
     * Resolve format kertas untuk Dompdf (Dompdf memerlukan bounding box [0, 0, w, h] dalam pt untuk F4)
     */
    private function resolveDompdfPaperFormat(string $ukuran): string|array
    {
        $ukuranUpper = strtoupper(trim($ukuran));
        if (in_array($ukuranUpper, ['F4', 'FOLIO'])) {
            return [0, 0, 609.45, 935.43];
        }
        return $ukuran;
    }
}
