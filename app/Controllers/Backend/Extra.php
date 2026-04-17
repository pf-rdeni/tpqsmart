<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SantriBaruModel;
use App\Models\GuruModel;
use App\Models\HelpFunctionModel;
use App\Models\TpqModel;

class Extra extends BaseController
{
    protected $santriModel;
    protected $guruModel;
    protected $helpFunction;
    protected $tpqModel;

    public function __construct()
    {
        $this->santriModel   = new SantriBaruModel();
        $this->guruModel     = new GuruModel();
        $this->helpFunction  = new HelpFunctionModel();
        $this->tpqModel      = new TpqModel();
    }

    /**
     * Halaman utama Download kustomisasi data
     * GET backend/extra/showDownload
     */
    public function showDownload()
    {
        $sessionIdTpq = session()->get('IdTpq');
        $isAdmin      = in_groups('Admin');

        // Daftar field santri yang tersedia (db_field => label tampilan)
        $santriFields = [
            'IdSantri'             => 'ID Santri',
            'NamaSantri'           => 'Nama Santri',
            'NikSantri'            => 'NIK Santri',
            'NISN'                 => 'NISN',
            'JenisKelamin'         => 'Jenis Kelamin',
            'TempatLahirSantri'    => 'Tempat Lahir',
            'TanggalLahirSantri'   => 'Tanggal Lahir',
            'NamaAyah'             => 'Nama Ayah',
            'NamaIbu'              => 'Nama Ibu',
            'NamaWali'             => 'Nama Wali',
            'NoHpSantri'           => 'No HP Santri',
            'NoHpAyah'             => 'No HP Ayah',
            'NoHpIbu'              => 'No HP Ibu',
            'AlamatSantri'         => 'Alamat Santri',
            'KelurahanDesaSantri'  => 'Kelurahan/Desa',
            'KecamatanSantri'      => 'Kecamatan',
            'KabupatenKotaSantri'  => 'Kabupaten/Kota',
            'ProvinsiSantri'       => 'Provinsi',
            'AnakKe'               => 'Anak Ke',
            'JumlahSaudara'        => 'Jumlah Saudara',
            'StatusMukim'          => 'Status Mukim',
            'YangBiayaSekolah'     => 'Yang Membiayai Sekolah',
            'PendidikanAyah'       => 'Pendidikan Ayah',
            'PekerjaanUtamaAyah'   => 'Pekerjaan Ayah',
            'PendidikanIbu'        => 'Pendidikan Ibu',
            'PekerjaanUtamaIbu'    => 'Pekerjaan Ibu',
            'NamaKelas'            => 'Kelas',
            'NamaTpq'              => 'Nama TPQ',
            'Active'               => 'Status Aktif',
            'Status'               => 'Status',
        ];

        // Daftar field guru yang tersedia
        $guruFields = [
            'IdGuru'                      => 'ID Guru / NIK',
            'Nama'                        => 'Nama Guru',
            'JenisKelamin'                => 'Jenis Kelamin',
            'TempatLahir'                 => 'Tempat Lahir',
            'TanggalLahir'                => 'Tanggal Lahir',
            'TanggalMulaiTugas'           => 'Tanggal Mulai Tugas',
            'TempatTugas'                 => 'Tempat Tugas',
            'PendidikanTerakhir'          => 'Pendidikan Terakhir',
            'Alamat'                      => 'Alamat',
            'Rt'                          => 'RT',
            'Rw'                          => 'RW',
            'KelurahanDesa'               => 'Kelurahan/Desa',
            'Kecamatan'                   => 'Kecamatan',
            'Kabupaten'                   => 'Kabupaten/Kota',
            'Provinsi'                    => 'Provinsi',
            'NoHp'                        => 'No HP',
            'NoRekBpr'                    => 'No Rekening BPR',
            'NoRekRiauKepri'              => 'No Rekening BRK',
            'JenisPenerimaInsentif'       => 'Jenis Penerima Insentif',
            'Status'                      => 'Status Aktif',
            'NamaTpq'                     => 'Nama TPQ',
        ];

        // Daftar TPQ
        if ($isAdmin) {
            $listTpq = $this->helpFunction->getDataTpq(false);
        } else {
            $listTpq = $this->helpFunction->getDataTpq();
        }

        // Ambil data Kelas & Tahun Ajaran untuk filter santri
        $listKelas = $this->helpFunction->getDataKelas();
        
        $db = db_connect();
        $listTahunAjaran = [];
        try {
            $listTahunAjaran = $db->table('tbl_kelas_santri')
                                  ->select('IdTahunAjaran')
                                  ->distinct()
                                  ->orderBy('IdTahunAjaran', 'DESC')
                                  ->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Gagal ambil list tahun ajaran: ' . $e->getMessage());
        }

        $data = [
            'page_title'      => 'Download Kustomisasi Data',
            'isAdmin'         => $isAdmin,
            'sessionIdTpq'    => $sessionIdTpq,
            'santriFields'    => $santriFields,
            'guruFields'      => $guruFields,
            'listTpq'         => $listTpq,
            'listKelas'       => $listKelas,
            'listTahunAjaran' => $listTahunAjaran,
        ];

        return view('backend/extra/showDownload', $data);
    }

    /**
     * Halaman preview hasil seleksi field - DataTable + Excel export
     * POST backend/extra/previewDownload
     */
    public function previewDownload()
    {
        $sessionIdTpq = session()->get('IdTpq');
        $isAdmin      = in_groups('Admin');

        $dataType          = $this->request->getPost('data_type');   // 'santri' | 'guru'
        $filterIdTpq       = $this->request->getPost('filter_tpq');  // bisa array atau single val
        $filterTahunAjaran = $this->request->getPost('filter_tahun_ajaran');
        $filterKelas       = $this->request->getPost('filter_kelas');
        $filterStatusAktif = $this->request->getPost('filter_status_aktif');
        $formatTanggal     = $this->request->getPost('format_tanggal') ?? 'indo';
        $formatJk          = $this->request->getPost('format_jk') ?? 'full';
        $formatTeks        = $this->request->getPost('format_teks') ?? 'titlecase';
        $selectedFields    = $this->request->getPost('selected_fields'); // array [['db'=>...,'label'=>...]]
        $fieldMappings     = $this->request->getPost('field_mappings');  // JSON: [{db, label}, ...]

        // Decode field mappings dari JSON
        $mappings = [];
        if (!empty($fieldMappings)) {
            $decoded = json_decode($fieldMappings, true);
            if (is_array($decoded)) {
                $mappings = $decoded;
            }
        } elseif (!empty($selectedFields) && is_array($selectedFields)) {
            // Fallback: pakai selected_fields langsung
            foreach ($selectedFields as $f) {
                $mappings[] = $f; // tiap item adalah [{db, label}]
            }
        }

        if (empty($mappings)) {
            return redirect()->back()->with('error', 'Pilih minimal satu field untuk ditampilkan.');
        }

        // Tentukan filter TPQ
        $idTpqFilter = null;
        if ($isAdmin) {
            if (!empty($filterIdTpq)) {
                $idTpqFilter = is_array($filterIdTpq) ? $filterIdTpq : [$filterIdTpq];
            }
        } else {
            $idTpqFilter = $sessionIdTpq;
        }

        // Ambil data berdasarkan tipe
        $rawData     = [];
        $columnDefs  = [];
        $pageTitle   = '';
        $filePrefix  = '';

        if ($dataType === 'santri') {
            $pageTitle  = 'Preview Data Santri';
            $filePrefix = 'data_santri_custom';
            $rawData    = $this->getSantriData($idTpqFilter, $mappings, $filterTahunAjaran, $filterKelas, $filterStatusAktif);
            $columnDefs = $mappings;
        } elseif ($dataType === 'guru') {
            $pageTitle  = 'Preview Data Guru';
            $filePrefix = 'data_guru_custom';
            $rawData    = $this->getGuruData($idTpqFilter, $mappings);
            $columnDefs = $mappings;
        } else {
            return redirect()->back()->with('error', 'Tipe data tidak valid.');
        }

        $data = [
            'page_title'  => $pageTitle,
            'filePrefix'  => $filePrefix,
            'columnDefs'  => $columnDefs,
            'rawData'     => $rawData,
            'dataType'    => $dataType,
            'isAdmin'     => $isAdmin,
            'filterIdTpq' => $idTpqFilter,
            'formatTanggal' => $formatTanggal,
            'formatJk'      => $formatJk,
            'formatTeks'    => $formatTeks,
        ];

        return view('backend/extra/previewDownload', $data);
    }

    /**
     * Ambil data santri dengan field yang dipilih + join tbl_kelas & tbl_tpq
     */
    private function getSantriData($idTpqFilter, array $mappings, $filterTahunAjaran = null, $filterKelas = null, $filterStatusAktif = 'all'): array
    {
        $db      = db_connect();
        $builder = $db->table('tbl_santri_baru sb');

        // Bangun SELECT berdasarkan field yang dipilih
        $selects = ['sb.id'];
        $needKelas = false;
        $needTpq   = false;

        foreach ($mappings as $m) {
            $dbField = $m['db'] ?? '';
            if ($dbField === 'NamaKelas') {
                $needKelas = true;
                $selects[] = 'k.NamaKelas';
            } elseif ($dbField === 'NamaTpq') {
                $needTpq = true;
                $selects[] = 't.NamaTpq';
            } elseif (!empty($dbField)) {
                $selects[] = 'sb.' . $dbField;
            }
        }

        $builder->select(implode(', ', array_unique($selects)));

        if ($needKelas || $needTpq) {
            $builder->join('tbl_kelas k', 'k.IdKelas = sb.IdKelas', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = sb.IdTpq', 'left');
        }

        // Handle Filter Tahun Ajaran (wajib join tbl_kelas_santri jika filter dipilih)
        if (!empty($filterTahunAjaran)) {
            $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = sb.IdSantri', 'inner');
            if (is_array($filterTahunAjaran)) {
                $builder->whereIn('ks.IdTahunAjaran', $filterTahunAjaran);
            } else {
                $builder->where('ks.IdTahunAjaran', $filterTahunAjaran);
            }
            // Group by agar jika murid ada di 2 thn ajaran tdk dobel baris
            $builder->groupBy('sb.IdSantri');
        }

        // Filter Kelas
        if (!empty($filterKelas)) {
            if (is_array($filterKelas)) {
                $builder->whereIn('sb.IdKelas', $filterKelas);
            } else {
                $builder->where('sb.IdKelas', $filterKelas);
            }
        }

        // Filter Status Aktif
        if ($filterStatusAktif !== 'all') {
            $builder->where('sb.Active', $filterStatusAktif);
        }

        // Filter TPQ
        if (!empty($idTpqFilter)) {
            if (is_array($idTpqFilter)) {
                $builder->whereIn('sb.IdTpq', $idTpqFilter);
            } else {
                $builder->where('sb.IdTpq', $idTpqFilter);
            }
        }

        $builder->orderBy('sb.NamaSantri', 'ASC');

        try {
            $result = $builder->get();
            return $result ? $result->getResultArray() : [];
        } catch (\Exception $e) {
            log_message('error', 'Extra::getSantriData - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil data guru dengan field yang dipilih + join tbl_tpq
     */
    private function getGuruData($idTpqFilter, array $mappings): array
    {
        $db      = db_connect();
        $builder = $db->table('tbl_guru g');

        $selects = ['g.IdGuru'];
        $needTpq = false;

        foreach ($mappings as $m) {
            $dbField = $m['db'] ?? '';
            if ($dbField === 'NamaTpq') {
                $needTpq = true;
                $selects[] = 't.NamaTpq';
            } elseif ($dbField === 'IdGuru') {
                // already in selects
            } elseif (!empty($dbField)) {
                $selects[] = 'g.' . $dbField;
            }
        }

        $builder->select(implode(', ', array_unique($selects)));

        if ($needTpq) {
            $builder->join('tbl_tpq t', 't.IdTpq = g.IdTpq', 'left');
        }

        // Filter TPQ
        if (!empty($idTpqFilter)) {
            if (is_array($idTpqFilter)) {
                $builder->whereIn('g.IdTpq', $idTpqFilter);
            } else {
                $builder->where('g.IdTpq', $idTpqFilter);
            }
        }

        $builder->orderBy('g.Nama', 'ASC');

        try {
            $result = $builder->get();
            return $result ? $result->getResultArray() : [];
        } catch (\Exception $e) {
            log_message('error', 'Extra::getGuruData - ' . $e->getMessage());
            return [];
        }
    }
}
