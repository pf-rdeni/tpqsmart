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
    public function getDataSantriStatus($Active = 0, $IdTpq = 0)
    {
        $builder = $this->db->table('tbl_santri_baru');

        // Join dengan tbl_kelas dan gunakan alias NamaKelas
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas AS NamaKelas, tbl_tpq.NamaTpq AS NamaTpq, tbl_tpq.KelurahanDesa AS NamaKelDesa');
        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas');
        $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        //Active Active=1, NonActive=2, Active = 0 Baru
        if ($Active != 1 && $Active != 2) {
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

    public function getDataGuru($id = false, $status = true, $IdTpq = null)
    {
        $namaTable = "tbl_guru";
        $builder = $this->db->table($namaTable)->where('Status', $status);
    
        if ($id) {
            $builder->where('IdTpq', $id);
        }

        if ($IdTpq !== null) {
            $builder->where('IdTpq', $IdTpq);
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

    public function getDataGuruKelas($IdGuru = null, $IdTpq = null)
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
        } else if ($IdTpq !== null) {
            $builder->where('gk.IdTpq', $IdTpq);
            return $builder->get()->getResultObject();
        } else {
            return $builder->get()->getResultObject();
        }


    }

    public function getKelasMateriPelajaran($kelas = null, $IdTpq = null, $Semester = null)
    {
        $builder = $this->db->table('tbl_kelas_materi_pelajaran kmp');
        $builder->select('kmp.IdKelas, kmp.IdMateri, kmp.SemesterGanjil, kmp.SemesterGenap, kmp.IdTpq, mp.NamaMateri, mp.Kategori');
        $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = kmp.IdMateri');
        
        if ($kelas !== null) {
            if (is_array($kelas)) {
                $builder->whereIn('kmp.IdKelas', $kelas);
            } else {
                $builder->where('kmp.IdKelas', $kelas);
            }
        }
        if ($IdTpq !== null) {
            $builder->where('kmp.IdTpq', $IdTpq);
        }
        if ($Semester !== null) {
            $builder->where('kmp.SemesterGanjil', $Semester);
        }

        return $builder->get()->getResultObject();
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

    //get data auth_groups
    public function getDataAuthGoups($id = false)
    {
        $namaTable = "auth_groups";
        $builder = $this->db->table($namaTable);

        if ($id) {
            $builder->where('id', $id);
        }
        return $builder->get()->getResultArray();
    }

    //get user by username
    public function getUserByUsername($username)
    {
        $builder = $this->db->table('users')
        ->where('username', $username);
        return $builder->get()->getRowArray();
    }

    //get user available by nik guru atau username 
    public function getGuruByIdNik($idNik)
    {
        $builder = $this->db->table('users')
        ->where('nik', $idNik);
        return $builder->get()->getRowArray();
    }

    //get nama guru by IdNik
    public function getNamaGuruByIdNik($idNik)
    {
        $builder = $this->db->table('tbl_guru')
        ->select('Nama')
        ->where('IdGuru', $idNik);
        return $builder->get()->getRowArray();
    }
    //===================================================================
    // Set Related
    // Set Related to Insert tabel auth_groups_users
    public function insertAuthGroupsUsers($data)
    {
        $builder = $this->db->table('auth_groups_users');
        return $builder->insert($data);
    }
    //===================================================================
    // Delete Related
    // Delete Related to Delete tabel auth_groups_users
    public function deleteAuthGroupsUsers($id)
    {
        $builder = $this->db->table('auth_groups_users');
        return $builder->delete(['user_id' => $id]);
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
        // jika array ambil index 0
        if (is_array($TahunAjaran)) {
            $TahunAjaran = $TahunAjaran[0];
        }
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

    //get data total santri
    public function getTotalSantri($IdTpq, $IdKelas = null, $IdGuru = null)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('COUNT(DISTINCT IdSantri) as total');
        $builder->where('IdTpq', $IdTpq);

        if ($IdKelas) {
            if (is_array($IdKelas)) {
                $builder->whereIn('IdKelas', $IdKelas);
            } else {
                $builder->where('IdKelas', $IdKelas);
            }
        }
        if ($IdGuru) {
            $builder->where('IdGuru', $IdGuru);
        }

        return $builder->get()->getRow()->total;
    }
    // get data total guru
    public function getTotalGuru($IdTpq)
    {
        $builder = $this->db->table('tbl_guru');
        $builder->select('COUNT(DISTINCT IdGuru) as total');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('Status', 1);

        return $builder->get()->getRow()->total;
    }

    // get data total kelas
    public function getTotalKelas($IdTpq, $IdTahunAjaran)
    {
        $builder = $this->db->table('tbl_kelas_santri');
        $builder->select('COUNT(DISTINCT IdKelas) as total');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('IdTahunAjaran', $IdTahunAjaran);

        return $builder->get()->getRow()->total;
    }

    // get data santri baru active = 0
    public function getTotalSantriBaru($IdTpq, $IdKelas = null, $Active = 0)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('COUNT(DISTINCT IdSantri) as total');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('Active', $Active);
        

        if ($IdKelas) {
            $builder->where('IdKelas', $IdKelas);
        }

        return $builder->get()->getRow()->total;
    }

    private function buildNilaiQuery($IdTpq, $IdTahunAjaran, $IdKelas, $Semester)
    {
        $builder = $this->db->table('tbl_nilai');
        $builder->where('IdTpq', $IdTpq);

        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('IdTahunAjaran', $IdTahunAjaran);
        }

        if ($IdKelas != 0) {
            if (is_array($IdKelas)) {
                $builder->whereIn('IdKelas', $IdKelas);
            } else {
                $builder->where('IdKelas', $IdKelas);
            }
        }

        $builder->where('Semester', $Semester);
        return $builder;
    }

    public function getStatusInputNilai($IdTpq, $IdTahunAjaran, $IdKelas = null, $Semester = null)
    {
        // Query untuk total
        $countTotal = $this->buildNilaiQuery($IdTpq, $IdTahunAjaran, $IdKelas, $Semester)->countAllResults();

        // Query untuk nilai sudah diinput
        $countSudah = $this->buildNilaiQuery($IdTpq, $IdTahunAjaran, $IdKelas, $Semester)
            ->where('Nilai !=', 0)
            ->countAllResults();

        // Query untuk nilai belum diinput
        $countBelum = $this->buildNilaiQuery($IdTpq, $IdTahunAjaran, $IdKelas, $Semester)
            ->where('Nilai', 0)
            ->countAllResults();

        // buat persentasi yang sudah dan belum jika total > 0
        if ($countTotal == 0) {
            $persentasiSudah = 0;
            $persentasiBelum = 0;
        } else {
            $persentasiSudah = round(($countSudah / $countTotal) * 100, 2);
            $persentasiBelum = round(($countBelum / $countTotal) * 100, 2);
        }

        return (object)[
            'countTotal' => $countTotal,
            'countSudah' => $countSudah,
            'countBelum' => $countBelum,
            'persentasiSudah' => $persentasiSudah,
            'persentasiBelum' => $persentasiBelum,
        ];
    }

    // get total wali kelas dari tbl_guru_kelas
    public function getTotalWaliKelas($IdTpq, $IdTahunAjaran)
    {
        $builder = $this->db->table('tbl_guru_kelas');
        $builder->select('COUNT(DISTINCT IdGuru) as total');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('IdTahunAjaran', $IdTahunAjaran);
        $builder->where('IdJabatan', 3); // Wali Kelas

        return $builder->get()->getRow()->total;
    }

    // get nama kelas dari IdKelas return hanya nama kelas
    public function getNamaKelas($IdKelas)
    {
        return $this->db->table('tbl_kelas')->where('IdKelas', $IdKelas)->get()->getRowArray()['NamaKelas'];
    }
}

