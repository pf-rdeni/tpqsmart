<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratModel;
use App\Models\Administrasi\SuratKategoriModel;
use App\Models\TpqModel;
use App\Models\HelpFunctionModel;

class SuratLaporan extends BaseController
{
    protected $suratModel;
    protected $kategoriModel;
    protected $tpqModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->suratModel    = new SuratModel();
        $this->kategoriModel = new SuratKategoriModel();
        $this->tpqModel      = new TpqModel();
        $this->helpFunction  = new HelpFunctionModel();
    }

    /**
     * Halaman Rekapitulasi & Laporan Surat
     */
    public function index()
    {
        $idTpqSession = session()->get('IdTpq');
        $isAdmin      = ($idTpqSession === '0' || $idTpqSession === 0 || empty($idTpqSession));

        // Tangkap parameter filter dari GET/POST
        $jenis          = $this->request->getGet('jenis') ?: 'Semua';
        $idKategori     = $this->request->getGet('id_kategori') ?: '';
        $tanggalMulai   = $this->request->getGet('tanggal_mulai') ?: date('Y-01-01');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai') ?: date('Y-m-d');
        $status         = $this->request->getGet('status') ?: 'Semua';

        $filterTpq = $this->request->getGet('id_tpq');
        if (!$isAdmin) {
            $targetTpq = (int)$idTpqSession;
        } else {
            $targetTpq = ($filterTpq !== null && $filterTpq !== '') ? $filterTpq : 'Semua';
        }

        $filters = [
            'jenis'           => $jenis,
            'id_kategori'     => $idKategori,
            'tanggal_mulai'   => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'status'          => $status,
            'id_tpq'          => $targetTpq,
        ];

        $suratList = $this->getFilteredSurat($filters, $isAdmin, $idTpqSession);

        // Hitung ringkasan statistik dari hasil filter
        $totalSurat  = count($suratList);
        $totalMasuk  = 0;
        $totalKeluar = 0;
        foreach ($suratList as $s) {
            if ($s['JenisSurat'] === 'Masuk') {
                $totalMasuk++;
            } elseif ($s['JenisSurat'] === 'Keluar') {
                $totalKeluar++;
            }
        }

        $kategoriList = $this->kategoriModel->where('IsActive', 1)->orderBy('NamaKategori', 'ASC')->findAll();
        $listTpq      = $isAdmin ? $this->helpFunction->getDataTpq(false) : [];

        $data = [
            'page_title'   => 'Laporan & Rekapitulasi Administrasi Surat',
            'suratList'    => $suratList,
            'kategoriList' => $kategoriList,
            'listTpq'      => $listTpq,
            'filters'      => $filters,
            'isAdmin'      => $isAdmin,
            'idTpqSession' => $idTpqSession,
            'stats'        => [
                'total'  => $totalSurat,
                'masuk'  => $totalMasuk,
                'keluar' => $totalKeluar,
            ],
        ];

        return view('backend/administrasi/laporan/index', $data);
    }

    /**
     * AJAX Endpoint untuk Reload Data Filter
     */
    public function ajaxData()
    {
        $idTpqSession = session()->get('IdTpq');
        $isAdmin      = ($idTpqSession === '0' || $idTpqSession === 0 || empty($idTpqSession));

        $filters = [
            'jenis'           => $this->request->getGet('jenis') ?: 'Semua',
            'id_kategori'     => $this->request->getGet('id_kategori') ?: '',
            'tanggal_mulai'   => $this->request->getGet('tanggal_mulai') ?: date('Y-01-01'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai') ?: date('Y-m-d'),
            'status'          => $this->request->getGet('status') ?: 'Semua',
            'id_tpq'          => $this->request->getGet('id_tpq') ?: ($isAdmin ? 'Semua' : (int)$idTpqSession),
        ];

        $suratList = $this->getFilteredSurat($filters, $isAdmin, $idTpqSession);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $suratList,
            'count'   => count($suratList),
        ]);
    }

    /**
     * Ekspor Laporan ke Format Excel (.xls)
     */
    public function exportExcel()
    {
        $idTpqSession = session()->get('IdTpq');
        $isAdmin      = ($idTpqSession === '0' || $idTpqSession === 0 || empty($idTpqSession));

        $filters = [
            'jenis'           => $this->request->getVar('jenis') ?: 'Semua',
            'id_kategori'     => $this->request->getVar('id_kategori') ?: '',
            'tanggal_mulai'   => $this->request->getVar('tanggal_mulai') ?: date('Y-01-01'),
            'tanggal_selesai' => $this->request->getVar('tanggal_selesai') ?: date('Y-m-d'),
            'status'          => $this->request->getVar('status') ?: 'Semua',
            'id_tpq'          => $this->request->getVar('id_tpq') ?: ($isAdmin ? 'Semua' : (int)$idTpqSession),
        ];

        $suratList = $this->getFilteredSurat($filters, $isAdmin, $idTpqSession);

        // Ambil nama lembaga untuk header laporan
        $namaLembaga = 'FORUM KOMUNIKASI PENDIDIKAN AL-QUR\'AN (FKPQ) KABUPATEN BINTAN';
        if (!$isAdmin && !empty($idTpqSession)) {
            $tpq = $this->tpqModel->find((int)$idTpqSession);
            if ($tpq && !empty($tpq['NamaTpq'])) {
                $namaLembaga = strtoupper($tpq['NamaTpq']);
            }
        } elseif ($isAdmin && $filters['id_tpq'] !== 'Semua' && !empty($filters['id_tpq'])) {
            $tpq = $this->tpqModel->find((int)$filters['id_tpq']);
            if ($tpq && !empty($tpq['NamaTpq'])) {
                $namaLembaga = strtoupper($tpq['NamaTpq']);
            }
        }

        $filename = 'Rekap_Surat_' . preg_replace('/[^A-Za-z0-9]/', '_', $namaLembaga) . '_' . date('Ymd_His') . '.xls';

        $data = [
            'namaLembaga' => $namaLembaga,
            'filters'     => $filters,
            'suratList'   => $suratList,
            'tglCetak'    => date('d-m-Y H:i:s'),
        ];

        // Header HTTP untuk download langsung Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        echo view('backend/administrasi/laporan/excel', $data);
        exit;
    }

    /**
     * Helper Query Filter Surat
     */
    private function getFilteredSurat(array $filters, bool $isAdmin, $idTpqSession): array
    {
        $builder = $this->suratModel->db->table('tbl_surat s');
        $builder->select('s.*, k.NamaKategori, k.Kode as KodeKategori, k.Warna as WarnaKategori, t.NamaTpq');
        $builder->join('tbl_surat_kategori k', 'k.Id = s.IdKategori', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left');

        // Filter Lembaga / TPQ
        if (!$isAdmin) {
            $builder->where('s.IdTpq', (int)$idTpqSession);
        } else {
            if ($filters['id_tpq'] !== 'Semua' && $filters['id_tpq'] !== '') {
                $builder->where('s.IdTpq', (int)$filters['id_tpq']);
            }
        }

        // Filter Jenis Surat
        if (!empty($filters['jenis']) && $filters['jenis'] !== 'Semua') {
            $builder->where('s.JenisSurat', $filters['jenis']);
        }

        // Filter Kategori
        if (!empty($filters['id_kategori'])) {
            $builder->where('s.IdKategori', (int)$filters['id_kategori']);
        }

        // Filter Rentang Tanggal
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('s.Tanggal >=', $filters['tanggal_mulai']);
        }
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('s.Tanggal <=', $filters['tanggal_selesai']);
        }

        // Filter Status
        if (!empty($filters['status']) && $filters['status'] !== 'Semua') {
            $builder->where('s.Status', $filters['status']);
        }

        $builder->orderBy('s.Tanggal', 'DESC');
        $builder->orderBy('s.Id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
