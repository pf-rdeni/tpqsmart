<?php

namespace App\Models;
use CodeIgniter\Model;

class HelpFunctionModel extends Model
{
    /**
     * Model untuk tabel kelas
     */
    protected $kelasModel;
    /**
     * Model untuk tabel nilai
     */
    protected $nilaiModel;
    /**
     * Model untuk tabel santri baru
     */
    protected $santriBaruModel;

    /**
     * Konstruktor, inisialisasi model-model terkait
     */
    public function __construct()
    {
        parent::__construct();
        $this->kelasModel = new \App\Models\KelasModel();
        $this->nilaiModel = new \App\Models\NilaiModel();
        $this->santriBaruModel = new \App\Models\SantriBaruModel();
    }
    //=================================================================
    // Select Related to Read tabel
    /**
     * Mengambil data santri berdasarkan status aktif dan IdTpq
     * @param int $Active Status aktif santri (0: baru, 1: aktif, 2: nonaktif)
     * @param int $IdTpq ID TPQ
     * @return array
     */
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


    /**
     * Mengambil data TPQ, bisa berdasarkan ID tertentu
     * @param mixed $id ID TPQ (optional)
     * @return array
     */
    public function getDataTpq($id = false)
    {
        $namaTable = "tbl_tpq";
        $builder = $this->db->table($namaTable);

        if ($id) {
            $builder->where('IdTpq', $id);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil data guru berdasarkan ID, status, dan IdTpq
     * @param mixed $id ID Guru (optional)
     * @param bool $status Status guru (default: true)
     * @param mixed $IdTpq ID TPQ (optional)
     * @return array
     */
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

    /**
     * Mengambil data seluruh kelas
     * @return array
     */
    public function getDataKelas()
    {
        return $this->db->table('tbl_kelas')
            ->select('IdKelas, NamaKelas')
            ->get()->getResultArray();
    }

    /**
     * Mengambil data jabatan, bisa berdasarkan ID tertentu
     * @param mixed $id ID Jabatan (optional)
     * @return array
     */
    public function getDataJabatan($id = false)
    {
        $namaTable = "tbl_jabatan";
        $builder = $this->db->table($namaTable);
    
        if ($id) {
            $builder->where('IdJabatan', $id);
        }
        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil data guru kelas berdasarkan filter yang diberikan
     * @param mixed $IdGuru
     * @param mixed $IdTpq
     * @param mixed $IdKelas
     * @param mixed $IdTahunAjaran
     * @param mixed $IdJabatan
     * @return object|array
     */
    public function getDataGuruKelas($IdGuru = null, $IdTpq = null, $IdKelas = null, $IdTahunAjaran = null, $IdJabatan = null)
    {
        $builder = $this->db->table('tbl_guru_kelas gk')
            ->select('j.IdJabatan, j.NamaJabatan, gk.IdTahunAjaran, gk.Id, gk.IdGuru, gk.IdTpq, gk.IdKelas, g.Nama, t.NamaTpq, k.NamaKelas,g.Status')
                            ->join('tbl_guru g', 'g.IdGuru = gk.IdGuru')
                            ->join('tbl_tpq t', 't.IdTpq = gk.IdTpq')
                            ->join('tbl_kelas k', 'k.IdKelas = gk.IdKelas')
                            ->join('tbl_jabatan j', 'j.IdJabatan = gk.IdJabatan');

        // Filter berdasarkan parameter yang diberikan
        if ($IdGuru !== null) {
            $builder->where('gk.IdGuru', $IdGuru);
        }
        if ($IdTpq !== null) {
            $builder->where('gk.IdTpq', $IdTpq);
        }
        if ($IdKelas !== null) {
            if (is_array($IdKelas)) {
                $builder->whereIn('gk.IdKelas', $IdKelas);
            } else {
                $builder->where('gk.IdKelas', $IdKelas);
            }
        }
        if ($IdTahunAjaran !== null) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('gk.IdTahunAjaran', $IdTahunAjaran);
            } else {
                $builder->where('gk.IdTahunAjaran', $IdTahunAjaran);
            }
        }
        if ($IdJabatan !== null) {
            $builder->where('gk.IdJabatan', $IdJabatan);
        }

        // Jika hanya mencari satu data spesifik (IdGuru dan IdTpq), kembalikan satu baris
        if ($IdGuru !== null && $IdTpq !== null && $IdKelas === null && $IdTahunAjaran === null && $IdJabatan === null) {
            return $builder->get()->getResultObject();
        }

        // Jika tidak, kembalikan semua hasil
        return $builder->get()->getResultObject();
    }

    /**
     * Mengambil data materi pelajaran yang terkait dengan kelas tertentu
     * @param mixed $kelas
     * @param mixed $IdTpq
     * @param mixed $Semester
     * @return object|array
     */
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

    /**
     * Mengambil data materi pelajaran, bisa berdasarkan ID tertentu
     * @param mixed $IdMateri
     * @return array
     */
    public function getDataMateriPelajaran($IdMateri = null)
    {
        $builder = $this->db->table('tbl_materi_pelajaran');

        if ($IdMateri !== null) {
            $builder->where('IdMateri', $IdMateri);
        }

        return $builder->get()->getResultArray();
    }

    //get IdTpq dari IdGuru
    /**
     * Mengambil IdTpq berdasarkan IdGuru
     * @param mixed $IdGuru
     * @return array|null
     */
    public function getIdTpq($IdGuru)
    {
        $builder = $this->db->table('tbl_guru')
        ->select('IdTpq')
        ->where('IdGuru', $IdGuru);
        return $builder->get()->getRowArray();
    }

    //get data auth_groups
    /**
     * Mengambil data auth_groups, bisa berdasarkan ID tertentu
     * @param mixed $id
     * @return array
     */
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
    /**
     * Mengambil data user berdasarkan username
     * @param string $username
     * @return array|null
     */
    public function getUserByUsername($username)
    {
        $builder = $this->db->table('users')
        ->where('username', $username);
        return $builder->get()->getRowArray();
    }

    //get user available by nik guru atau username 
    /**
     * Mengambil data guru berdasarkan NIK
     * @param string $idNik
     * @return array|null
     */
    public function getGuruByIdNik($idNik)
    {
        $builder = $this->db->table('users')
        ->where('nik', $idNik);
        return $builder->get()->getRowArray();
    }

    //get nama guru by IdNik
    /**
     * Mengambil nama guru berdasarkan IdGuru
     * @param string $idNik
     * @return array|null
     */
    public function getNamaGuruByIdNik($idNik)
    {
        $builder = $this->db->table('tbl_guru')
        ->select('Nama')
        ->where('IdGuru', $idNik);
        return $builder->get()->getRowArray();
    }

    // get nama wali kelas dari inputan IdKelas check di tbl_guru_kelas fiter IdTpq IdTahunAjaran
    /**
     * Mengambil nama wali kelas berdasarkan IdKelas, IdTpq, dan IdTahunAjaran
     * @param mixed $IdKelas
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @return object|null
     */
    public function getWaliKelasByIdKelas($IdKelas, $IdTpq, $IdTahunAjaran)
    {
        $builder = $this->db->table('tbl_guru_kelas');
        $builder->select('g.Nama');
        $builder->join('tbl_guru g', 'g.IdGuru = tbl_guru_kelas.IdGuru');
        $builder->where('tbl_guru_kelas.IdKelas', $IdKelas);
        $builder->where('tbl_guru_kelas.IdTpq', $IdTpq);
        $builder->where('tbl_guru_kelas.IdTahunAjaran', $IdTahunAjaran);

        // where IdJabatan Wali Kelas
        $builder->where('tbl_guru_kelas.IdJabatan', 3); // Wali Kelas
        // Ambil hanya satu baris
        $builder->limit(1);
        // Mengembalikan hasil sebagai object
        return $builder->get()->getRowObject();
    }
    //===================================================================
    // Set Related
    // Set Related to Insert tabel auth_groups_users
    /**
     * Menyisipkan data ke tabel auth_groups_users
     * @param array $data
     * @return bool
     */
    public function insertAuthGroupsUsers($data)
    {
        $builder = $this->db->table('auth_groups_users');
        return $builder->insert($data);
    }
    //===================================================================
    // Delete Related
    // Delete Related to Delete tabel auth_groups_users
    /**
     * Menghapus data dari tabel auth_groups_users berdasarkan user_id
     * @param mixed $id
     * @return bool
     */
    public function deleteAuthGroupsUsers($id)
    {
        $builder = $this->db->table('auth_groups_users');
        return $builder->delete(['user_id' => $id]);
    }
    //===================================================================

    /**
     * Mendapatkan kelas berikutnya berdasarkan ID kelas saat ini
     * @param string $idKelas
     * @return string
     */
    public function getNextKelas($idKelas)
    {
        $classMapping = [
            1 => 2,   // TKQ -> TKQA
            2 => 3,   // TKQA -> TKQB
            3 => 4,   // TKQB -> TPQ1/SD1
            4 => 5,   // TPQ1/SD1 -> TPQ2/SD2
            5 => 6,   // TPQ2/SD2 -> TPQ3/SD3
            6 => 7,   // TPQ3/SD3 -> TPQ4/SD4
            7 => 8,   // TPQ4/SD4 -> TPQ5/SD5
            8 => 9,   // TPQ5/SD5 -> TPQ6/SD6
            9 => 10,  // TPQ6/SD6 -> ALUMNI
            10 => 10, // ALUMNI tetap ALUMNI
        ];

        return $classMapping[$idKelas] ?? 10;
    }


    /**
     * Mendapatkan tahun ajaran berikutnya dari tahun ajaran saat ini
     * @param string $currentTahunAjaran
     * @return string
     */
    public function getTahuanAjaranBerikutnya($currentTahunAjaran)
    {
        $startYear = (int) substr($currentTahunAjaran, 0, 4);
        $endYear = (int) substr($currentTahunAjaran, 4);

        $nextStartYear = $startYear + 1;
        $nextEndYear = $endYear + 1;

        return $nextStartYear . $nextEndYear;
    }

    /**
     * Mengonversi tahun ajaran ke format StartYear/EndYear
     * @param mixed $TahunAjaran
     * @return string
     */
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

    /**
     * Mendapatkan tahun ajaran saat ini berdasarkan bulan berjalan
     * @return string
     */
    public function getTahunAjaranSaatIni()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        // Get the current year and determine the academic year
        return ($currentMonth >= 7) ? $currentYear . ($currentYear + 1) : ($currentYear - 1) . $currentYear;
    }

    /**
     * Mendapatkan tahun ajaran sebelumnya
     * @return string
     */
    public function getTahunAjaranSebelumnya()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        // Get the current year and determine the academic year
        return ($currentMonth >= 7) ? ($currentYear - 1) . $currentYear : $currentYear . ($currentYear + 1);
    }

    // convert data for nominal e.g Rp. 100.000 to 100000
    /**
     * Mengonversi nominal rupiah (Rp. 100.000) menjadi angka (100000)
     * @param string $nominal
     * @return string
     */
    public function convertToNumber($nominal)
    {
        return preg_replace('/\D/', '', $nominal);
    }

    // convert data for nominal e.g 100000 to Rp. 100.000
    /**
     * Mengonversi angka menjadi format rupiah (Rp. 100.000)
     * @param int $nominal
     * @return string
     */
    public function convertToRupiah($nominal)
    {
        return 'Rp. ' . number_format($nominal, 0, ',', '.');
    }

    // conver month number to month name indonesia
    /**
     * Mengonversi nomor bulan ke nama bulan dalam bahasa Indonesia
     * @param int $number
     * @return string
     */
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

    /**
     * Mengambil nama TPQ berdasarkan IdTpq
     * @param mixed $IdTpq
     * @return array|null
     */
    public function getNamaTpqById($IdTpq)
    {
        return $this->db->table('tbl_tpq')->where('IdTpq', $IdTpq)->get()->getRowArray();
    }

    //get data total santri
    /**
     * Mengambil total santri berdasarkan IdTpq, IdKelas, dan IdGuru
     * @param mixed $IdTpq
     * @param mixed $IdKelas
     * @param mixed $IdGuru
     * @return int
     */
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
    /**
     * Mengambil total guru aktif berdasarkan IdTpq
     * @param mixed $IdTpq
     * @return int
     */
    public function getTotalGuru($IdTpq)
    {
        $builder = $this->db->table('tbl_guru');
        $builder->select('COUNT(DISTINCT IdGuru) as total');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('Status', 1);

        return $builder->get()->getRow()->total;
    }

    // get data total kelas
    /**
     * Mengambil total kelas berdasarkan IdTpq dan IdTahunAjaran
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @return int
     */
    public function getTotalKelas($IdTpq, $IdTahunAjaran)
    {
        $builder = $this->db->table('tbl_kelas_santri');
        $builder->select('COUNT(DISTINCT IdKelas) as total');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('IdTahunAjaran', $IdTahunAjaran);

        return $builder->get()->getRow()->total;
    }

    // get data santri baru active = 0
    /**
     * Mengambil total santri baru berdasarkan IdTpq, IdKelas, dan status aktif
     * @param mixed $IdTpq
     * @param mixed $IdKelas
     * @param int $Active
     * @return int
     */
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

    /**
     * Membuat query builder untuk nilai berdasarkan filter
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @param mixed $IdKelas
     * @param mixed $Semester
     * @return \CodeIgniter\Database\BaseBuilder
     */
    private function buildNilaiQuery($IdTpq, $IdTahunAjaran, $IdKelas, $Semester)
    {
        $builder = $this->db->table('tbl_nilai');
        $builder->select('tbl_nilai.*');
        $builder->join('tbl_santri_baru', 'tbl_santri_baru.IdSantri = tbl_nilai.IdSantri');
        if (!empty($IdTpq)) {
            $builder->where('tbl_nilai.IdTpq', $IdTpq);
        }
        $builder->where('tbl_santri_baru.Active', 1);


        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('tbl_nilai.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('tbl_nilai.IdTahunAjaran', $IdTahunAjaran);
        }

        if ($IdKelas != null) {
            if (is_array($IdKelas)) {
                $builder->whereIn('tbl_nilai.IdKelas', $IdKelas);
            } else {
                $builder->where('tbl_nilai.IdKelas', $IdKelas);
            }
        }

        $builder->where('tbl_nilai.Semester', $Semester);
        return $builder;
    }

    /**
     * Mengambil status input nilai (total, sudah, belum, persentase) berdasarkan filter
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @param mixed $IdKelas
     * @param mixed $Semester
     * @return object
     */
    public function getStatusInputNilai($IdTpq, $IdTahunAjaran, $IdKelas = null, $Semester = null)
    {
        $builder = $this->buildNilaiQuery($IdTpq, $IdTahunAjaran, $IdKelas, $Semester);

        // Menggunakan satu query dengan CASE WHEN untuk menghitung total, sudah, dan belum
        $result = $builder->select('
            COUNT(*) as countTotal,
            SUM(CASE WHEN Nilai != 0 THEN 1 ELSE 0 END) as countSudah,
            SUM(CASE WHEN Nilai = 0 THEN 1 ELSE 0 END) as countBelum
        ')->get()->getRow();

        // buat persentasi yang sudah dan belum jika total > 0
        if ($result->countTotal == 0) {
            $persentasiSudah = 0;
            $persentasiBelum = 0;
        } else {
            $persentasiSudah = round(($result->countSudah / $result->countTotal) * 100, 2);
            $persentasiBelum = round(($result->countBelum / $result->countTotal) * 100, 2);
        }

        return (object)[
            'countTotal' => $result->countTotal,
            'countSudah' => $result->countSudah,
            'countBelum' => $result->countBelum,
            'persentasiSudah' => $persentasiSudah,
            'persentasiBelum' => $persentasiBelum,
        ];
    }

    // get total wali kelas dari tbl_guru_kelas
    /**
     * Mengambil total wali kelas berdasarkan IdTpq dan IdTahunAjaran
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @return int
     */
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
    /**
     * Mengambil nama kelas berdasarkan IdKelas
     * @param mixed $IdKelas
     * @return string
     */
    public function getNamaKelas($IdKelas)
    {
        return $this->db->table('tbl_kelas')->where('IdKelas', $IdKelas)->get()->getRowArray()['NamaKelas'];
    }

    // get list kelas grouped kelas filter IdTpq, IdTahunAjaran dari tbl_kelas_santri
    /**
     * Mengambil daftar kelas berdasarkan IdTpq, IdTahunAjaran, dan IdKelas
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @param mixed $IdKelas
     * @return object|array
     */
    public function getListKelas($IdTpq, $IdTahunAjaran, $IdKelas = null)
    {
        $builder = $this->db->table('tbl_kelas_santri');
        $builder->select('tbl_kelas_santri.IdKelas, NamaKelas');
        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_kelas_santri.IdKelas');
        if (!empty($IdTpq)) {
            $builder->where('tbl_kelas_santri.IdTpq', $IdTpq);
        }
        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('tbl_kelas_santri.IdTahunAjaran', $IdTahunAjaran);
            } else {
                $builder->where('tbl_kelas_santri.IdTahunAjaran', $IdTahunAjaran);
            }
        }

        // Jika IdKelas tidak null, filter berdasarkan IdKelas
        if ($IdKelas !== null) {
            if (is_array($IdKelas)) {
                $builder->whereIn('tbl_kelas_santri.IdKelas', $IdKelas);
            } else {
                $builder->where('tbl_kelas_santri.IdKelas', $IdKelas);
            }
        }
        $builder->groupBy('tbl_kelas_santri.IdKelas, NamaKelas');
        $builder->orderBy('NamaKelas', 'ASC');

        return $builder->get()->getResultObject();
    }

    // Get value setting input nilai min dan max dari tbl_tools
    /**
     * Mengambil nilai setting limit input nilai dari tabel tools
     * @param mixed $IdTpq
     * @param string $SettingKey
     * @return int|null
     */
    public function getSettingLimitInputNilai($IdTpq = null, $SettingKey)
    {
        $builder = $this->db->table('tbl_tools');
        $builder->select('SettingValue');
        if (!empty($IdTpq)) {
            $builder->where('IdTpq', $IdTpq);
        }
        $builder->where('SettingKey', $SettingKey);

        $result = $builder->get()->getRowArray();

        return $result ? (int)$result['SettingValue'] : null;
    }

    // Get nilai alfabetic settings based on keys
    /**
     * Mengambil setting nilai alfabetik berdasarkan IdTpq
     * @param mixed $IdTpq
     * @return object|null
     */
    public function getNilaiAlphabetSettings($IdTpq)
    {
        // jika IdTpq tidak ada, kembalikan null
        if (empty($IdTpq)) {
            return null;
        }
        // Check the value of Nilai_Alphabet setting
        $nilaiAlfabeticSetting = $this->db->table('tbl_tools')
            ->select('SettingValue')
            ->where('IdTpq', $IdTpq)
            ->where('SettingKey', 'Nilai_Alphabet')
            ->get()
            ->getRowArray();

        // If Nilai_Alfabetic setting is 1, retrieve other settings
        if ($nilaiAlfabeticSetting && $nilaiAlfabeticSetting['SettingValue'] == '1') {
            $settings = $this->db->table('tbl_tools')
                ->select('SettingKey, SettingValue')
                ->where('IdTpq', $IdTpq)
                ->whereIn('SettingKey', ['Nilai_Alphabet', 'Nilai_Alphabet_Persamaan', 'Nilai_Alphabet_Kelas'])
                ->get()
                ->getResultArray();

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting['SettingKey']] = $setting['SettingValue'];
            }

            return (object)$result;
        } else {
            // Return empty array or null if condition not met
            return null;
        }
    }

    // Get nilai setting angka arabic 
    /**
     * Mengambil setting nilai angka arabic berdasarkan IdTpq
     * @param mixed $IdTpq
     * @return object|null
     */
    public function getNilaiArabicSettings($IdTpq)
    {
        // jika IdTpq tidak ada, kembalikan null
        if (empty($IdTpq)) {
            return null;
        }
        // Check the value of Nilai_Arabic setting
        $nilaiArabicSetting = $this->db->table('tbl_tools')
            ->select('SettingValue')
            ->where('IdTpq', $IdTpq)
            ->where('SettingKey', 'Nilai_Angka_Arabic')
            ->get()
            ->getRowArray();

        // If Nilai_Arabic setting is 1, retrieve other settings
        if ($nilaiArabicSetting && $nilaiArabicSetting['SettingValue'] == '1') {
            $settings = $this->db->table('tbl_tools')
                ->select('SettingKey, SettingValue')
                ->where('IdTpq', $IdTpq)
                ->whereIn('SettingKey', ['Nilai_Angka_Arabic'])
                ->get()
                ->getResultArray();

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting['SettingKey']] = $setting['SettingValue'];
            }

            return (object)$result;
        } else {
            // Return empty array or null if condition not met
            return null;
        }
    }

    // Get nama Materi pelajaran berdasarkan IdKelas, IdTpq, Semester, TahunAjaran
    /**
     * Mengambil nama materi pelajaran berdasarkan IdKelas, IdTpq, Semester
     * @param mixed $IdTpq
     * @param mixed $IdKelas
     * @param mixed $Semester
     * @return object|array
     */
    public function getMateriPelajaranByKelas($IdTpq, $IdKelas = null, $Semester)
    {
        $builder = $this->db->table('tbl_kelas_materi_pelajaran');
        $builder->select('IdKelas, tbl_kelas_materi_pelajaran.IdMateri, NamaMateri, Kategori, UrutanMateri');
        $builder->join('tbl_materi_pelajaran', 'tbl_materi_pelajaran.IdMateri = tbl_kelas_materi_pelajaran.IdMateri');
        // Mengahandle jika IdKelas adalah null
        if ($IdKelas) {
            //Mengahandle jkik IdKelas adalah array
            if (is_array($IdKelas)) {
                $builder->whereIn('IdKelas', $IdKelas);
            } else {
                $builder->where('IdKelas', $IdKelas);
            }
        }
        $builder->where('tbl_kelas_materi_pelajaran.IdTpq', $IdTpq);
        // Jika Semester Ganjil
        if ($Semester == 'Ganjil') {
            $builder->where('SemesterGanjil', 1);
        } elseif ($Semester == 'Genap') {
            // Jika Semester Genap
            $builder->where('SemesterGenap', 1);
        } else {
            // Jika Semester tidak ditentukan, ambil semua
            $builder->where('SemesterGanjil', 1)
                ->orWhere('SemesterGenap', 1);
        }
        // order by IdKelas, Ketegori, NamaMateri
        $builder->orderBy('IdKelas, UrutanMateri, Kategori');
        // Mengembalikan hasil sebagai objek
        return $builder->get()->getResultObject();
    }

    /**
     * ====================================================================================
     * Start Fungsi untuk menyimpan data santri dan materi pelajaran ke dalam tabel nilai
     * ====================================================================================
     */
    /**
     * Menyimpan data santri dan materi pelajaran ke tabel nilai
     * @param int $StatusSantri
     * @param array $santriList
     * @return void
     */
    public function saveDataSantriDanMateriDiTabelNilai($StatusSantri, $santriList)
    {
        // 1. Tentukan status dan tahun ajaran
        $tahunAjaran = $this->getTahunAjaran($StatusSantri);
        $isSantriBaru = ($StatusSantri == 0);

        // 2. Proses setiap santri
        foreach ($santriList as $santri) {
            $this->prosesSantri($santri, $isSantriBaru, $tahunAjaran);
        }
    }

    /**
     * Mendapatkan tahun ajaran berdasarkan status santri
     * @param int $StatusSantri
     * @return string
     */
    private function getTahunAjaran($StatusSantri)
    {
        if ($StatusSantri == 0) {
            return $this->getTahunAjaranSaatIni(); // Tahun ajaran saat ini untuk santri baru
        }
        return $this->getTahuanAjaranBerikutnya(0); // Tahun ajaran berikutnya untuk naik kelas
    }

    /**
     * Memproses data satu santri (baru/naik kelas)
     * @param array $santri
     * @param bool $isSantriBaru
     * @param string $tahunAjaran
     * @return void
     */
    private function prosesSantri($santri, $isSantriBaru, $tahunAjaran)
    {
        // 1. Ambil data dasar santri
        $dataSantri = $this->getDataSantri($santri);

        // 2. Proses berdasarkan status
        if ($isSantriBaru) {
            $this->prosesSantriBaru($dataSantri);
        } else {
            $this->prosesNaikKelas($dataSantri, $tahunAjaran);
        }

        // 3. Proses materi dan nilai
        $this->prosesMateriDanNilai($dataSantri, $tahunAjaran);
    }

    /**
     * Mengambil data dasar santri
     * @param array $santri
     * @return array
     */
    private function getDataSantri($santri)
    {
        return [
            'IdSantri' => $santri['IdSantri'],
            'IdTpq' => $santri['IdTpq'],
            'IdKelas' => $santri['IdKelas'],
            'IdTahunAjaran' => $santri['IdTahunAjaran']
        ];
    }

    /**
     * Memproses data santri baru (insert kelas_santri dan update status aktif)
     * @param array $dataSantri
     * @return void
     */
    private function prosesSantriBaru($dataSantri)
    {
        // 1. Simpan di tabel kelas_santri
        $this->kelasModel->insert($dataSantri);

        // 2. Update status aktif santri
        $this->santriBaruModel->updateActiveSantri($dataSantri['IdSantri']);
    }

    /**
     * Memproses kenaikan kelas santri
     * @param array $dataSantri
     * @param string $tahunAjaran
     * @return void
     */
    private function prosesNaikKelas($dataSantri, $tahunAjaran)
    {
        // 1. Dapatkan kelas baru
        $kelasBaru = $this->getNextKelas($dataSantri['IdKelas']);

        // 2. Insert data kelas baru
        $this->kelasModel->insert([
            'IdKelas' => $kelasBaru,
            'IdTpq' => $dataSantri['IdTpq'],
            'IdSantri' => $dataSantri['IdSantri'],
            'IdTahunAjaran' => $tahunAjaran
        ]);

        // 3. Update status kelas lama
        $this->kelasModel->update($dataSantri['Id'], ['Status' => 0]);
    }

    /**
     * Memproses materi dan nilai untuk santri
     * @param array $dataSantri
     * @param string $tahunAjaran
     * @return void
     */
    private function prosesMateriDanNilai($dataSantri, $tahunAjaran)
    {
        // 1. Ambil materi pelajaran
        $listMateri = $this->getKelasMateriPelajaran(
            $dataSantri['IdKelas'],
            $dataSantri['IdTpq']
        );

        // 2. Proses setiap materi
        foreach ($listMateri as $materi) {
            $this->simpanNilaiMateri($materi, $dataSantri, $tahunAjaran);
        }
    }

    /**
     * Menyimpan nilai untuk satu materi
     * @param object $materi
     * @param array $dataSantri
     * @param string $tahunAjaran
     * @return void
     */
    private function simpanNilaiMateri($materi, $dataSantri, $tahunAjaran)
    {
        // Proses semester ganjil
        if ($materi->SemesterGanjil == 1) {
            $this->insertNilai($materi, $dataSantri, $tahunAjaran, "Ganjil");
        }

        // Proses semester genap
        if ($materi->SemesterGenap == 1) {
            $this->insertNilai($materi, $dataSantri, $tahunAjaran, "Genap");
        }
    }

    /**
     * Insert nilai ke database
     * @param object $materi
     * @param array $dataSantri
     * @param string $tahunAjaran
     * @param string $semester
     * @return void
     */
    private function insertNilai($materi, $dataSantri, $tahunAjaran, $semester)
    {
        $data = [
            'IdTpq' => $dataSantri['IdTpq'],
            'IdSantri' => $dataSantri['IdSantri'],
            'IdKelas' => $materi->IdKelas,
            'IdMateri' => $materi->IdMateri,
            'IdTahunAjaran' => $tahunAjaran,
            'Semester' => $semester
        ];

        $this->nilaiModel->insertNilai($data);
    }
    //================================================================================
    // End funsi untuk menyimpan data santri dan materi pelajaran ke dalam tabel nilai
    //================================================================================

    // Membuat fungsi untuk mengupdate jika ada perubahan pada materi pelajaran untuk di update di tabel nilai
    /**
     * Mengupdate materi pelajaran pada tabel nilai jika ada perubahan
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @return bool|int
     */
    public function updateMateriPelajaranPadaTabelNilai($IdTpq, $IdTahunAjaran)
    {
        // Step1: Ambil Data Santri Actif di tbl_santri_baru filter by IdTpq
        $santriList = $this->santriBaruModel->where(['IdTpq' => $IdTpq, 'Active' => 1])->findAll();

        // Siapkan array untuk batch insert
        $batchData = [];

        foreach ($santriList as $santri) {
            // Step2: Ambil Data Kelas Materi Pelajaran
            $kelasMateriList = $this->getKelasMateriPelajaran(IdTpq: $IdTpq, kelas: $santri['IdKelas'], Semester: null);

            // Kumpulkan semua IdMateri untuk query
            $materiIds = array_column($kelasMateriList, 'IdMateri');

            // Step3: Ambil semua nilai yang sudah ada dalam satu query
            $existingNilai = $this->nilaiModel->where([
                'IdTpq' => $IdTpq,
                'IdSantri' => $santri['IdSantri'],
                'IdTahunAjaran' => $IdTahunAjaran
            ])
                ->whereIn('IdMateri', $materiIds)
                ->findAll();

            // Buat array untuk pengecekan cepat
            $existingNilaiMap = [];
            foreach ($existingNilai as $nilai) {
                $key = $nilai['IdMateri'] . '_' . $nilai['Semester'];
                $existingNilaiMap[$key] = true;
            }

            // Step4: Proses setiap materi
            foreach ($kelasMateriList as $materi) {
                // Cek semester ganjil
                if ($materi->SemesterGanjil == 1) {
                    $key = $materi->IdMateri . '_Ganjil';
                    if (!isset($existingNilaiMap[$key])) {
                        $batchData[] = [
                            'IdTpq' => $IdTpq,
                            'IdSantri' => $santri['IdSantri'],
                            'IdKelas' => $materi->IdKelas,
                            'IdMateri' => $materi->IdMateri,
                            'IdTahunAjaran' => $IdTahunAjaran,
                            'Semester' => 'Ganjil'
                        ];
                    }
                }

                // Cek semester genap
                if ($materi->SemesterGenap == 1) {
                    $key = $materi->IdMateri . '_Genap';
                    if (!isset($existingNilaiMap[$key])) {
                        $batchData[] = [
                            'IdTpq' => $IdTpq,
                            'IdSantri' => $santri['IdSantri'],
                            'IdKelas' => $materi->IdKelas,
                            'IdMateri' => $materi->IdMateri,
                            'IdTahunAjaran' => $IdTahunAjaran,
                            'Semester' => 'Genap'
                        ];
                    }
                }
            }
        }

        // Step5: Lakukan batch insert jika ada data
        if (!empty($batchData)) {
            return $this->nilaiModel->insertBatch($batchData);
        } else {
            return true; // Tidak ada data yang perlu diupdate
        }
    }

    // Fungsi untuk check apakah ada materi pelajaran yang sudah tidak ada di daftar materi pelajaran tetapi ada di tabel nilai
    /**
     * Mengecek materi pelajaran yang sudah tidak ada di daftar materi pelajaran namun masih ada di tabel nilai
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @return array
     */
    public function getMateriPelajaranYangSudahTidakAda($IdTpq, $IdTahunAjaran)
    {
        // Step1: Ambil Data Santri Actif di tbl_santri_baru filter by IdTpq
        $santriList = $this->santriBaruModel->where(['IdTpq' => $IdTpq, 'Active' => 1])->findAll();

        // Step2: Ambil semua nilai yang ada di tabel nilai untuk TPQ dan tahun ajaran tertentu
        $existingNilai = $this->nilaiModel->where([
            'IdTpq' => $IdTpq,
            'IdTahunAjaran' => $IdTahunAjaran
        ])->findAll();

        // Step3: Buat array untuk menyimpan kombinasi IdMateri, IdKelas, dan Semester yang valid
        $validMateriKelasSemester = [];

        // Step4: Proses setiap santri untuk mendapatkan materi yang valid
        foreach ($santriList as $santri) {
            // Ambil Data Kelas Materi Pelajaran untuk santri ini
            $kelasMateriList = $this->getKelasMateriPelajaran(
                IdTpq: $IdTpq,
                kelas: $santri['IdKelas'],
                Semester: null
            );

            // Tambahkan kombinasi IdMateri, IdKelas, dan Semester ke array valid
            foreach ($kelasMateriList as $materi) {
                // Untuk semester ganjil
                if ($materi->SemesterGanjil == 1) {
                    $key = $materi->IdMateri . '_' . $materi->IdKelas . '_Ganjil';
                    $validMateriKelasSemester[$key] = [
                        'IdMateri' => $materi->IdMateri,
                        'IdKelas' => $materi->IdKelas,
                        'NamaMateri' => $materi->NamaMateri,
                        'Kategori' => $materi->Kategori,
                        'Semester' => 'Ganjil'
                    ];
                }
                // Untuk semester genap
                if ($materi->SemesterGenap == 1) {
                    $key = $materi->IdMateri . '_' . $materi->IdKelas . '_Genap';
                    $validMateriKelasSemester[$key] = [
                        'IdMateri' => $materi->IdMateri,
                        'IdKelas' => $materi->IdKelas,
                        'NamaMateri' => $materi->NamaMateri,
                        'Kategori' => $materi->Kategori,
                        'Semester' => 'Genap'
                    ];
                }
            }
        }

        // Step5: Buat array untuk menyimpan data yang perlu dihapus
        $materiToDelete = [];

        // Step6: Cek setiap nilai yang ada
        foreach ($existingNilai as $nilai) {
            // Buat key untuk pengecekan
            $key = $nilai['IdMateri'] . '_' . $nilai['IdKelas'] . '_' . $nilai['Semester'];

            // Jika kombinasi IdMateri, IdKelas, dan Semester tidak ada di daftar valid
            if (!isset($validMateriKelasSemester[$key])) {
                // Ambil informasi materi untuk ditampilkan
                $materiInfo = $this->db->table('tbl_materi_pelajaran')
                    ->where('IdMateri', $nilai['IdMateri'])
                    ->get()
                    ->getRowArray();

                // Ambil informasi kelas
                $kelasInfo = $this->db->table('tbl_kelas')
                    ->where('IdKelas', $nilai['IdKelas'])
                    ->get()
                    ->getRowArray();

                $materiToDelete[] = [
                    'Id' => $nilai['Id'],
                    'IdMateri' => $nilai['IdMateri'],
                    'IdKelas' => $nilai['IdKelas'],
                    'NamaMateri' => $materiInfo ? $materiInfo['NamaMateri'] : 'Materi tidak ditemukan',
                    'NamaKelas' => $kelasInfo ? $kelasInfo['NamaKelas'] : 'Kelas tidak ditemukan',
                    'Kategori' => $materiInfo ? $materiInfo['Kategori'] : 'Kategori tidak ditemukan',
                    'Semester' => $nilai['Semester'],
                    'IdSantri' => $nilai['IdSantri']
                ];
            }
        }

        return $materiToDelete;
    }

    /**
     * Mendapatkan materi baru yang perlu ditambahkan ke tabel nilai
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @return array
     */
    public function getMateriBaruUntukDitambahkan($IdTpq, $IdTahunAjaran)
    {
        // Step1: Ambil Data Santri Actif di tbl_santri_baru filter by IdTpq
        $santriList = $this->santriBaruModel->where(['IdTpq' => $IdTpq, 'Active' => 1])->findAll();

        // Step2: Ambil semua nilai yang sudah ada dalam satu query
        $existingNilai = $this->nilaiModel->where([
            'IdTpq' => $IdTpq,
            'IdTahunAjaran' => $IdTahunAjaran
        ])->findAll();

        // Step3: Buat array untuk pengecekan cepat
        $existingNilaiMap = [];
        foreach ($existingNilai as $nilai) {
            $key = $nilai['IdMateri'] . '_' . $nilai['IdKelas'] . '_' . $nilai['Semester'];
            $existingNilaiMap[$key] = true;
        }

        // Step4: Siapkan array untuk materi baru
        $materiBaru = [];

        // Step5: Proses setiap santri
        foreach ($santriList as $santri) {
            // Ambil Data Kelas Materi Pelajaran
            $kelasMateriList = $this->getKelasMateriPelajaran(
                IdTpq: $IdTpq,
                kelas: $santri['IdKelas'],
                Semester: null
            );

            // Proses setiap materi
            foreach ($kelasMateriList as $materi) {
                // Cek semester ganjil
                if ($materi->SemesterGanjil == 1) {
                    $key = $materi->IdMateri . '_' . $materi->IdKelas . '_Ganjil';
                    if (!isset($existingNilaiMap[$key])) {
                        $materiBaru[] = [
                            'IdKelas' => $materi->IdKelas,
                            'NamaKelas' => $this->getNamaKelas($materi->IdKelas),
                            'IdMateri' => $materi->IdMateri,
                            'NamaMateri' => $materi->NamaMateri,
                            'Kategori' => $materi->Kategori,
                            'Semester' => 'Ganjil',
                            'IdSantri' => $santri['IdSantri']
                        ];
                    }
                }

                // Cek semester genap
                if ($materi->SemesterGenap == 1) {
                    $key = $materi->IdMateri . '_' . $materi->IdKelas . '_Genap';
                    if (!isset($existingNilaiMap[$key])) {
                        $materiBaru[] = [
                            'IdKelas' => $materi->IdKelas,
                            'NamaKelas' => $this->getNamaKelas($materi->IdKelas),
                            'IdMateri' => $materi->IdMateri,
                            'NamaMateri' => $materi->NamaMateri,
                            'Kategori' => $materi->Kategori,
                            'Semester' => 'Genap',
                            'IdSantri' => $santri['IdSantri']
                        ];
                    }
                }
            }
        }

        return $materiBaru;
    }

    /**
     * Mengambil status input nilai secara bulk per kelas
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @param mixed $IdKelas
     * @param mixed $Semester
     * @return array
     */
    public function getStatusInputNilaiBulk($IdTpq, $IdTahunAjaran, $IdKelas, $Semester)
    {
        $builder = $this->buildNilaiQuery($IdTpq, $IdTahunAjaran, $IdKelas, $Semester);

        // Menggunakan GROUP BY untuk mendapatkan status per kelas
        $result = $builder->select('
            tbl_nilai.IdKelas,
            COUNT(*) as countTotal,
            SUM(CASE WHEN tbl_nilai.Nilai != 0 THEN 1 ELSE 0 END) as countSudah,
            SUM(CASE WHEN tbl_nilai.Nilai = 0 THEN 1 ELSE 0 END) as countBelum
        ')
            ->groupBy('tbl_nilai.IdKelas')
            ->get()
            ->getResultArray();

        // Format hasil
        $formattedResult = [];
        foreach ($result as $row) {
            $persentasiSudah = $row['countTotal'] > 0 ?
                round(($row['countSudah'] / $row['countTotal']) * 100, 1) : 0;
            $persentasiBelum = $row['countTotal'] > 0 ?
                round(($row['countBelum'] / $row['countTotal']) * 100, 1) : 0;

            $formattedResult[$row['IdKelas']] = (object)[
                'countTotal' => $row['countTotal'],
                'countSudah' => $row['countSudah'],
                'countBelum' => $row['countBelum'],
                'persentasiSudah' => $persentasiSudah,
                'persentasiBelum' => $persentasiBelum,
            ];
        }

        return $formattedResult;
    }

    /**
     * Mengambil nama kelas secara bulk berdasarkan array ID kelas
     * @param array $kelasIds
     * @return array
     */
    public function getNamaKelasBulk($kelasIds)
    {
        $builder = $this->db->table('tbl_kelas')
            ->select('IdKelas, NamaKelas');

        if ($kelasIds != null) {
            $builder->whereIn('IdKelas', $kelasIds);
        }

        $result = $builder->get()->getResultArray();

        $formattedResult = [];
        foreach ($result as $row) {
            $formattedResult[$row['IdKelas']] = $row['NamaKelas'];
        }

        return $formattedResult;
    }

    /**
     * Mengambil jumlah santri per kelas berdasarkan IdTpq, IdTahunAjaran, dan array kelas
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @param mixed $kelasIds
     * @return array
     */
    public function getJumlahSantriPerKelas($IdTpq, $kelasIds)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('IdKelas, COUNT(DISTINCT IdSantri) as jumlah_santri');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('Active', 1);
        if ($kelasIds != null) {
            if (is_array($kelasIds)) {
                $builder->whereIn('IdKelas', $kelasIds);
            } else {
                $builder->where('IdKelas', $kelasIds);
            }
            $builder->groupBy('IdKelas');
        }
        $result = $builder->get()->getResultArray();

        // Format hasil ke array asosiatif
        $formattedResult = [];
        foreach ($result as $row) {
            $formattedResult[$row['IdKelas']] = $row['jumlah_santri'];
        }

        return $formattedResult;
    }
}

