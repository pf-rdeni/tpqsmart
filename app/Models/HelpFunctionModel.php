<?php

namespace App\Models;
use CodeIgniter\Model;

class HelpFunctionModel extends Model
{
    protected $santriModel;

    public function __construct()
    {
        parent::__construct();
    }
    //=================================================================
    // Select Related to Read tabel
    public function getDataSantriStatus($Status = 0, $IdTpq = 0)
    {
        $builder = $this->db->table('tbl_santri_baru');

        // Join dengan tbl_kelas dan gunakan alias NamaKelas
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas AS NamaKelas, tbl_tpq.NamaTpq AS NamaTpq, tbl_tpq.KelurahanDesa AS NamaKelDesa');
        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas');
        $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        //if status bukan Aktif=1 dan Nonaktif=2 maka status = 0 Baru
        if ($Status != 1 && $Status != 2) {
            $builder->where('tbl_santri_baru.Active', 0);
        }
        if ($IdTpq != 0) {
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
        }
        return $builder->get()->getResultArray();
    }


    public function getDataTpq($id = false)
    {
        $namaTable = "tbl_tpq";
        $builder = $this->db->table($namaTable);

        if ($id) {
            $builder->where('IdTpq', $id);
        }

        return $builder->get()->getResultArray();
    }

    public function getDataGuru($id = false, $status = true)
    {
        $namaTable = "tbl_guru";
        $builder = $this->db->table($namaTable)->where('Status', $status);
    
        if ($id) {
            $builder->where('IdTpq', $id);
        }
        return $builder->get()->getResultArray();
    }

    public function getDataKelas()
    {
        return $this->db->table('tbl_kelas')
            ->select('IdKelas, NamaKelas')
            ->get()->getResultArray();
    }

     public function getDataJabatan($id = false)
    {
        $namaTable = "tbl_jabatan";
        $builder = $this->db->table($namaTable);
    
        if ($id) {
            $builder->where('IdJabatan', $id);
        }
        return $builder->get()->getResultArray();
    }

    public function getDataGuruKelas($IdGuru = null)
    {
        $builder = $this->db->table('tbl_guru_kelas gk')
                            ->select('j.IdJabatan, j.NamaJabatan, gk.IdTahunAjaran, gk.Id, gk.IdGuru, gk.IdTpq, gk.IdKelas, g.Nama, t.NamaTpq, k.NamaKelas')
                            ->join('tbl_guru g', 'g.IdGuru = gk.IdGuru')
                            ->join('tbl_tpq t', 't.IdTpq = gk.IdTpq')
                            ->join('tbl_kelas k', 'k.IdKelas = gk.IdKelas')
                            ->join('tbl_jabatan j', 'j.IdJabatan = gk.IdJabatan');

        // Tambahkan kondisi jika IdGuru diberikan
        if ($IdGuru !== null) {
            $builder->where('gk.IdGuru', $IdGuru);
            return $builder->get()->getResultObject();
        }
        else{

            return $builder->get()->getResultArray();
        }

    }

    public function getKelasMateriPelajaran($kelas = null, $IdTpq = null)
    {
        $builder = $this->db->table('tbl_kelas_materi_pelajaran');

        $builder->select('IdKelas, IdMateri, SemesterGanjil, SemesterGenap, IdTpq');
        
        if ($kelas !== null) {
            $builder->where('IdKelas', $kelas);
        }
        if ($IdTpq !== null) {
            $builder->where('IdTpq', $IdTpq);
        }

        return $builder->get()->getResultArray();
    }

    public function getDataMateriPelajaran($IdMateri = null)
    {
        $builder = $this->db->table('tbl_materi_pelajaran');

        if ($IdMateri !== null) {
            $builder->where('IdMateri', $IdMateri);
        }

        return $builder->get()->getResultArray();
    }

    //get IdTpq dari IdGuru
    public function getIdTpq($IdGuru)
    {
        $builder = $this->db->table('tbl_guru')
        ->select('IdTpq')
        ->where('IdGuru', $IdGuru);
        return $builder->get()->getRowArray();
    }

    //===================================================================

    public function getNextKelas($idKelas)
    {
        $classMapping = [
            'TK1' => 'TKA',
            'TK2' => 'SD1',
            'TK' => 'SD1',
            'SD1' => 'SD2',
            'SD2' => 'SD3',
            'SD3' => 'SD4',
            'SD4' => 'SD5',
            'SD5' => 'SD6',
            'SD6' => 'SMP1',
            'SMP1' => 'SMP2',
            'SMP2' => 'SMP3',
            'SMP3' => 'Alumni'
        ];

        return $classMapping[$idKelas] ?? 'Alumni';
    }


    public function getTahuanAjaranBerikutnya($currentTahunAjaran)
    {
        $startYear = (int) substr($currentTahunAjaran, 0, 4);
        $endYear = (int) substr($currentTahunAjaran, 4);

        $nextStartYear = $startYear + 1;
        $nextEndYear = $endYear + 1;

        return $nextStartYear . $nextEndYear;
    }

    public function convertTahunAjaran($TahunAjaran)
    {
        $startYear = (int) substr($TahunAjaran, 0, 4);
        $endYear = (int) substr($TahunAjaran, 4);

        $StartYear = $startYear;
        $EndYear = $endYear;

        return $StartYear .'/'. $EndYear;
    }

    public function getTahunAjaranSaatIni()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        // Get the current year and determine the academic year
        return ($currentMonth >= 7) ? $currentYear . ($currentYear + 1) : ($currentYear - 1) . $currentYear;
    }

    public function getTahunAjaranSebelumnya()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        // Get the current year and determine the academic year
        return ($currentMonth >= 7) ? ($currentYear - 1) . $currentYear : $currentYear . ($currentYear + 1);
    }

    // convert data for nominal e.g Rp. 100.000 to 100000
    public function convertToNumber($nominal)
    {
        return preg_replace('/\D/', '', $nominal);
    }

    // convert data for nominal e.g 100000 to Rp. 100.000
    public function convertToRupiah($nominal)
    {
        return 'Rp. ' . number_format($nominal, 0, ',', '.');
    }

    // conver month number to month name indonesia
    public function numberToMonth($number)
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $months[$number];
    }

    public function getNamaTpqById($IdTpq)
    {
        return $this->db->table('tbl_tpq')->where('IdTpq', $IdTpq)->get()->getRowArray();
    }
}

