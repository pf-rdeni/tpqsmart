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
    public function getDataSantriStatus($Active = 0, $IdTpq = 0, $IdKelas = 0)
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

        // Filter TPQ - hanya jika IdTpq tidak 0 (bukan "semua")
        if ($IdTpq != 0) {
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
        }

        // Filter Kelas - hanya jika IdKelas tidak 0 (bukan "semua")
        if ($IdKelas != 0) {
            $builder->where('tbl_santri_baru.IdKelas', $IdKelas);
        }
        $builder->orderBy('tbl_santri_baru.IdTpq', 'ASC');
        $builder->orderBy('tbl_santri_baru.IdKelas', 'ASC');
        $builder->orderBy('tbl_santri_baru.NamaSantri', 'ASC');
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

        $builder->orderBy('NamaTpq', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil total jumlah TPQ
     * @return int
     */
    public function getTotalTpq()
    {
        $builder = $this->db->table('tbl_tpq');
        $builder->select('COUNT(DISTINCT IdTpq) as total');

        $result = $builder->get()->getRow();
        return $result ? (int)$result->total : 0;
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
     * Mengambil data Kepala TPQ dari struktur lembaga
     * @param mixed $IdGuru
     * @param mixed $IdTpq
     * @param mixed $IdJabatan
     * @return object|array
     */
    public function getDataKepalaTpqStrukturLembaga($IdGuru = null, $IdTpq = null, $IdJabatan = null)
    {
        $builder = $this->db->table('tbl_struktur_lembaga sl')
            ->select('j.IdJabatan, j.NamaJabatan, NULL as IdTahunAjaran, sl.Id, sl.IdGuru, sl.IdTpq, NULL as IdKelas, g.Nama, t.NamaTpq, NULL as NamaKelas, g.Status')
            ->join('tbl_guru g', 'g.IdGuru = sl.IdGuru')
            ->join('tbl_tpq t', 't.IdTpq = sl.IdTpq')
            ->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan')
            ->where('j.NamaJabatan', 'Kepala TPQ');

        // Filter berdasarkan parameter yang diberikan
        if ($IdGuru !== null) {
            $builder->where('sl.IdGuru', $IdGuru);
        }
        if ($IdTpq !== null) {
            $builder->where('sl.IdTpq', $IdTpq);
        }
        if ($IdJabatan !== null) {
            $builder->where('sl.IdJabatan', $IdJabatan);
        }

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

    /**
     * Mengambil data santri berdasarkan NIK
     * @param string $idNik
     * @return array|null
     */
    public function getSantriByIdNik($idNik)
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

    /**
     * Mengambil data guru (Nama, JenisKelamin) berdasarkan IdGuru
     * @param string $IdGuru
     * @return array|null
     */
    public function getGuruById($IdGuru)
    {
        return $this->db->table('tbl_guru')
            ->select('Nama, JenisKelamin')
            ->where('IdGuru', $IdGuru)
            ->get()
            ->getRowArray();
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
    // get guru pendamping dari IdKelas, IdTpq, IdTahunAjaran
    /**
     * Mengambil guru pendamping berdasarkan IdKelas, IdTpq, dan IdTahunAjaran
     * @param mixed $IdKelas
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @return object|null
     */
    public function getGuruPendampingByIdKelas($IdKelas, $IdTpq, $IdTahunAjaran)
    {
        $builder = $this->db->table('tbl_guru_kelas');
        $builder->select('g.Nama');
        $builder->join('tbl_guru g', 'g.IdGuru = tbl_guru_kelas.IdGuru');
        $builder->where('tbl_guru_kelas.IdKelas', $IdKelas);
        $builder->where('tbl_guru_kelas.IdTpq', $IdTpq);
        $builder->where('tbl_guru_kelas.IdTahunAjaran', $IdTahunAjaran);
        $builder->where('tbl_guru_kelas.IdJabatan', 4); // Guru Pendamping
        // $builder->limit(1);
        return $builder->get()->getResultObject();
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
     * Mendapatkan tahun ajaran sebelumnya dari tahun ajaran tertentu
     * @param string $currentTahunAjaran Tahun ajaran saat ini (format: 20252026)
     * @return string Tahun ajaran sebelumnya (format: 20242025)
     */
    public function getTahunAjaranSebelumnyaDari($currentTahunAjaran)
    {
        $tahunAjaranStr = (string)$currentTahunAjaran;
        
        // Normalisasi: hapus karakter non-numeric
        $normalized = preg_replace('/\D/', '', $tahunAjaranStr);
        
        if (strlen($normalized) == 8) {
            $startYear = (int) substr($normalized, 0, 4);
            $endYear = (int) substr($normalized, 4, 4);

            $previousStartYear = $startYear - 1;
            $previousEndYear = $endYear - 1;

            return str_pad($previousStartYear, 4, '0', STR_PAD_LEFT) . str_pad($previousEndYear, 4, '0', STR_PAD_LEFT);
        } else {
            // Fallback: gunakan fungsi getTahunAjaranSebelumnya() tanpa parameter
            return $this->getTahunAjaranSebelumnya();
        }
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
     * Normalisasi tahun ajaran dengan menghapus karakter non-numeric
     * Mengkonversi format "2025/2026" menjadi "20252026"
     * @param mixed $tahunAjaran Tahun ajaran yang akan dinormalisasi
     * @return string Tahun ajaran dalam format 8 digit (contoh: 20252026)
     * @throws \Exception Jika format tahun ajaran tidak valid
     */
    public function normalizeTahunAjaran($tahunAjaran)
    {
        // Jika array, ambil index 0
        if (is_array($tahunAjaran)) {
            $tahunAjaran = $tahunAjaran[0];
        }
        
        // Hapus semua karakter non-numeric (termasuk garis miring, spasi, dll)
        $normalized = preg_replace('/\D/', '', (string)$tahunAjaran);
        
        // Validasi: harus 8 digit
        if (strlen($normalized) !== 8) {
            throw new \Exception("Format tahun ajaran tidak valid. Harus 8 digit (contoh: 20252026). Nilai yang diterima: " . $tahunAjaran);
        }
        
        return $normalized;
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
     * Mengambil data TPQ lengkap berdasarkan IdTpq termasuk KopLembaga dan LogoLembaga
     * @param mixed $IdTpq
     * @return array|null
     */
    public function getNamaTpqById($IdTpq)
    {
        return $this->db->table('tbl_tpq')
            ->select('IdTpq, NamaTpq, Alamat, TahunBerdiri, TempatBelajar, KepalaSekolah, NoHp, LogoLembaga, KopLembaga')
            ->where('IdTpq', $IdTpq)
            ->get()
            ->getRowArray();
    }

    /**
     * Mengambil detail santri dari tbl_kelas_santri yang di-join ke tbl_santri_baru
     * Filter berdasarkan IdSantri, IdTahunAjaran, IdTpq
     * @param mixed $IdSantri
     * @param mixed $IdTahunAjaran
     * @param mixed $IdTpq
     * @return array|null
     */
    public function getDetailSantriByKelasSantri($IdSantri, $IdTahunAjaran, $IdTpq)
    {
        $builder = $this->db->table('tbl_kelas_santri ks');
        $builder->select('s.*, ks.IdKelas, k.NamaKelas');
        $builder->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri');
        $builder->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas');

        $builder->where('ks.IdSantri', $IdSantri);
        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('ks.IdTahunAjaran', $IdTahunAjaran);
            } else {
                $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
            }
        }
        if (!empty($IdTpq)) {
            if (is_array($IdTpq)) {
                $builder->whereIn('ks.IdTpq', $IdTpq);
            } else {
                $builder->where('ks.IdTpq', $IdTpq);
            }
        }

        // Jika ada banyak baris (multi tahun), ambil yang terbaru
        $builder->orderBy('ks.IdTahunAjaran', 'DESC');
        $builder->limit(1);

        return $builder->get()->getRowArray();
    }

    //get data total santri
    /**
     * Mengambil total santri berdasarkan IdTpq, IdKelas, dan IdGuru
     * @param mixed $IdTpq
     * @param mixed $IdKelas
     * @param mixed $IdGuru
     * @return int
     */
    public function getTotalSantri($IdTpq, $IdKelas = null, $IdGuru = null, $Active = 1)
    {
        // Handle IdTpq untuk admin (IdTpq=0, null, atau empty berarti semua TPQ)
        $idTpqForQuery = (empty($IdTpq) || $IdTpq == '0' || $IdTpq == 0) ? 0 : $IdTpq;

        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('COUNT(DISTINCT IdSantri) as total');

        // Filter berdasarkan IdTpq jika tidak 0
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }

        if ($Active == 1) {
            $builder->where('Active', $Active);
        }

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

        $result = $builder->get()->getRow();
        return $result ? (int)$result->total : 0;
    }
    // get data total guru
    /**
     * Mengambil total guru aktif berdasarkan IdTpq
     * @param mixed $IdTpq ID TPQ (0, null, atau empty untuk semua TPQ)
     * @param int $Status Status guru (default: 1 = aktif)
     * @return int
     */
    public function getTotalGuru($IdTpq, $Status = 1)
    {
        // Handle IdTpq untuk admin (IdTpq=0, null, atau empty berarti semua TPQ)
        $idTpqForQuery = (empty($IdTpq) || $IdTpq == '0' || $IdTpq == 0) ? 0 : $IdTpq;

        $builder = $this->db->table('tbl_guru');
        $builder->select('COUNT(DISTINCT IdGuru) as total');

        // Filter berdasarkan IdTpq jika tidak 0
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }

        if ($Status == 1) {
            $builder->where('Status', $Status);
        }

        $result = $builder->get()->getRow();
        return $result ? (int)$result->total : 0;
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
        // Handle IdTpq untuk admin (IdTpq=0, null, atau empty berarti semua TPQ)
        $idTpqForQuery = (empty($IdTpq) || $IdTpq == '0' || $IdTpq == 0) ? 0 : $IdTpq;

        $builder = $this->db->table('tbl_kelas_santri');
        $builder->select('COUNT(DISTINCT IdKelas) as total');

        // Filter berdasarkan IdTpq jika tidak 0
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }

        $builder->where('IdTahunAjaran', $IdTahunAjaran);

        $result = $builder->get()->getRow();
        return $result ? (int)$result->total : 0;
    }

    // get data santri baru active = 0
    /**
     * Mengambil total santri baru berdasarkan IdTpq, IdKelas, dan status aktif
     * @param mixed $IdTpq ID TPQ (0, null, atau empty untuk semua TPQ)
     * @param mixed $IdKelas
     * @param int $Active
     * @return int
     */
    public function getTotalSantriBaru($IdTpq, $IdKelas = null, $Active = 0)
    {
        // Handle IdTpq untuk admin (IdTpq=0, null, atau empty berarti semua TPQ)
        $idTpqForQuery = (empty($IdTpq) || $IdTpq == '0' || $IdTpq == 0) ? 0 : $IdTpq;

        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('COUNT(DISTINCT IdSantri) as total');

        // Filter berdasarkan IdTpq jika tidak 0
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }

        $builder->where('Active', $Active);

        if ($IdKelas) {
            if (is_array($IdKelas)) {
                $builder->whereIn('IdKelas', $IdKelas);
            } else {
                $builder->where('IdKelas', $IdKelas);
            }
        }

        $result = $builder->get()->getRow();
        return $result ? (int)$result->total : 0;
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

        // Pastikan hanya menghitung santri yang Active = 1 (konsisten dengan getStatusInputNilaiBulk)
        $builder->where('tbl_santri_baru.Active', 1);

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
     * @param mixed $IdTpq ID TPQ (0, null, atau empty untuk semua TPQ)
     * @param mixed $IdTahunAjaran
     * @return int
     */
    public function getTotalWaliKelas($IdTpq, $IdTahunAjaran)
    {
        // Handle IdTpq untuk admin (IdTpq=0, null, atau empty berarti semua TPQ)
        $idTpqForQuery = (empty($IdTpq) || $IdTpq == '0' || $IdTpq == 0) ? 0 : $IdTpq;

        $builder = $this->db->table('tbl_guru_kelas');
        $builder->select('COUNT(DISTINCT IdGuru) as total');

        // Filter berdasarkan IdTpq jika tidak 0
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }

        $builder->where('IdTahunAjaran', $IdTahunAjaran);
        $builder->where('IdJabatan', 3); // Wali Kelas

        $result = $builder->get()->getRow();
        return $result ? (int)$result->total : 0;
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

    /**
     * Mengambil daftar kelas berdasarkan ID guru atau admin
     * 
     * Fungsi ini digunakan untuk mendapatkan list kelas dengan tiga mode:
     * 1. Mode Guru: Jika IdGuru diberikan dan bukan Kepala Sekolah, akan mengambil kelas yang diajar oleh guru tersebut
     * 2. Mode Kepala Sekolah: Jika IdGuru adalah Kepala Sekolah, akan mengambil semua kelas (akses penuh)
     * 3. Mode Admin: Jika IdGuru tidak diberikan, akan mengambil semua kelas (akses admin)
     * 
     * @param mixed $IdTpq ID TPQ untuk filter
     * @param mixed $IdTahunAjaran ID Tahun Ajaran (bisa array atau single value)
     * @param mixed $IdKelas ID Kelas untuk filter (bisa array atau single value, optional)
     * @param mixed $IdGuru ID Guru untuk filter (optional - jika tidak ada maka mode admin)
     * @return object|array Daftar kelas yang memenuhi kriteria
     */
    public function getListKelas($IdTpq, $IdTahunAjaran, $IdKelas = null, $IdGuru = null, $isOperator = false)
    {
        // Tentukan mode query berdasarkan IdGuru dan status jabatan
        $isKepalaSekolah = $this->isKepalaSekolah($IdGuru, $IdTpq);

        // Operator diperlakukan seperti Kepala Sekolah (bisa melihat semua kelas)
        if ($isOperator) {
            $isKepalaSekolah = true;
        }

        $isGuruMode = !empty($IdGuru) && !$isKepalaSekolah;

        // Buat query builder sesuai mode (Guru, Kepala Sekolah, atau Admin)
        $builder = $this->createKelasQueryBuilder($isGuruMode, $IdGuru, $isKepalaSekolah);

        // Terapkan filter-filter yang diberikan
        $this->applyKelasFilters($builder, $IdTpq, $IdTahunAjaran, $IdKelas, $isGuruMode, $isKepalaSekolah);

        // Set grouping dan ordering
        $this->setKelasGroupingAndOrdering($builder, $isGuruMode, $isKepalaSekolah);

        return $builder->get()->getResultObject();
    }

    /**
     * Mengecek apakah guru adalah Kepala Sekolah
     * 
     * @param mixed $IdGuru ID Guru
     * @param mixed $IdTpq ID TPQ
     * @return bool True jika guru adalah Kepala Sekolah
     */
    private function isKepalaSekolah($IdGuru, $IdTpq)
    {
        if (empty($IdGuru) || empty($IdTpq)) {
            return false;
        }

        $jabatanData = $this->getStrukturLembagaJabatan($IdGuru, $IdTpq);

        if (empty($jabatanData)) {
            return false;
        }

        // Cek apakah ada jabatan "Kepala Sekolah" dalam data jabatan
        foreach ($jabatanData as $jabatan) {
            if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                return true;
            }
        }

        return false;
    }

    /**
     * Membuat query builder untuk mengambil data kelas
     * 
     * @param bool $isGuruMode True jika menggunakan mode guru, false jika mode admin
     * @param mixed $IdGuru ID Guru (hanya digunakan jika isGuruMode = true)
     * @param bool $isKepalaSekolah True jika guru adalah Kepala Sekolah
     * @return \CodeIgniter\Database\BaseBuilder
     */
    private function createKelasQueryBuilder($isGuruMode, $IdGuru, $isKepalaSekolah = false)
    {
        if ($isGuruMode) {
            // Mode Guru: Ambil kelas yang diajar oleh guru tertentu
            $builder = $this->db->table('tbl_guru_kelas gk');
            $builder->select('gk.IdKelas, k.NamaKelas, gk.IdTahunAjaran');
            $builder->join('tbl_kelas k', 'k.IdKelas = gk.IdKelas');
            $builder->where('gk.IdGuru', $IdGuru);
        } else {
            // Mode Admin atau Kepala Sekolah: Ambil semua kelas dari data santri
            $builder = $this->db->table('tbl_kelas_santri');
            $builder->select('tbl_kelas_santri.IdKelas, NamaKelas, IdTahunAjaran');
            $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_kelas_santri.IdKelas');
        }

        return $builder;
    }

    /**
     * Menerapkan filter-filter pada query builder
     * 
     * @param \CodeIgniter\Database\BaseBuilder $builder Query builder
     * @param mixed $IdTpq ID TPQ
     * @param mixed $IdTahunAjaran ID Tahun Ajaran
     * @param mixed $IdKelas ID Kelas
     * @param bool $isGuruMode Mode query (guru atau admin)
     * @param bool $isKepalaSekolah True jika guru adalah Kepala Sekolah
     */
    private function applyKelasFilters($builder, $IdTpq, $IdTahunAjaran, $IdKelas, $isGuruMode, $isKepalaSekolah = false)
    {
        // Tentukan prefix tabel berdasarkan mode
        $tablePrefix = $isGuruMode ? 'gk' : 'tbl_kelas_santri';

        // Filter berdasarkan TPQ
        if (!empty($IdTpq)) {
            $builder->where($tablePrefix . '.IdTpq', $IdTpq);
        }

        // Filter berdasarkan Tahun Ajaran
        if (!empty($IdTahunAjaran)) {
            $this->applyArrayOrSingleFilter($builder, $tablePrefix . '.IdTahunAjaran', $IdTahunAjaran);
        }

        // Filter berdasarkan Kelas
        if ($IdKelas !== null) {
            $this->applyArrayOrSingleFilter($builder, $tablePrefix . '.IdKelas', $IdKelas);
        }
    }

    /**
     * Menerapkan filter yang bisa berupa array atau single value
     * 
     * @param \CodeIgniter\Database\BaseBuilder $builder Query builder
     * @param string $column Nama kolom
     * @param mixed $value Value yang bisa berupa array atau single value
     */
    private function applyArrayOrSingleFilter($builder, $column, $value)
    {
        if (is_array($value)) {
            $builder->whereIn($column, $value);
        } else {
            $builder->where($column, $value);
        }
    }

    /**
     * Mengatur grouping dan ordering untuk query
     * 
     * @param \CodeIgniter\Database\BaseBuilder $builder Query builder
     * @param bool $isGuruMode Mode query (guru atau admin)
     * @param bool $isKepalaSekolah True jika guru adalah Kepala Sekolah
     */
    private function setKelasGroupingAndOrdering($builder, $isGuruMode, $isKepalaSekolah = false)
    {
        if ($isGuruMode) {
            $builder->groupBy('gk.IdKelas, k.NamaKelas, gk.IdTahunAjaran');
        } else {
            $builder->groupBy('tbl_kelas_santri.IdKelas, NamaKelas, IdTahunAjaran');
        }

        $builder->orderBy('NamaKelas', 'ASC');
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
        log_message('info', "getNilaiAlphabetSettings START - IdTpq: {$IdTpq}");

        // jika IdTpq tidak ada, kembalikan null
        if (empty($IdTpq)) {
            log_message('info', "getNilaiAlphabetSettings: IdTpq is empty, returning null");
            return null;
        }

        // Check the value of Nilai_Alphabet setting dengan type conversion
        log_message('info', "getNilaiAlphabetSettings Query 1: Check Nilai_Alphabet setting START");
        $builder1 = $this->db->table('tbl_tools');
        $builder1->select('SettingValue, SettingType');
        $builder1->where('IdTpq', $IdTpq);
        $builder1->where('SettingKey', 'Nilai_Alphabet');

        $sql1 = $builder1->getCompiledSelect(false);
        log_message('info', "getNilaiAlphabetSettings Query 1 SQL: {$sql1}");

        $queryStartTime1 = microtime(true);
        $nilaiAlfabeticSetting = $builder1->get()->getRowArray();
        $queryEndTime1 = microtime(true);
        $queryExecutionTime1 = ($queryEndTime1 - $queryStartTime1) * 1000; // Convert to milliseconds
        log_message('info', "getNilaiAlphabetSettings Query 1: Check Nilai_Alphabet setting END - Execution Time: {$queryExecutionTime1}ms");
        log_message('info', "getNilaiAlphabetSettings Query 1 Result: " . ($nilaiAlfabeticSetting ? 'Found' : 'Not Found'));

        // If Nilai_Alfabetic setting is true (boolean), retrieve other settings
        if ($nilaiAlfabeticSetting) {
            $isEnabled = $this->convertSettingValue($nilaiAlfabeticSetting['SettingValue'], $nilaiAlfabeticSetting['SettingType']);
            log_message('info', "getNilaiAlphabetSettings: isEnabled = " . ($isEnabled ? 'true' : 'false'));

            if ($isEnabled) {
                log_message('info', "getNilaiAlphabetSettings Query 2: Get detail settings START");
                $builder2 = $this->db->table('tbl_tools');
                $builder2->select('SettingKey, SettingValue, SettingType');
                $builder2->where('IdTpq', $IdTpq);
                $builder2->whereIn('SettingKey', ['Nilai_Alphabet', 'Nilai_Alphabet_Persamaan', 'Nilai_Alphabet_Kelas']);

                $sql2 = $builder2->getCompiledSelect(false);
                log_message('info', "getNilaiAlphabetSettings Query 2 SQL: {$sql2}");

                $queryStartTime2 = microtime(true);
                $settings = $builder2->get()->getResultArray();
                $queryEndTime2 = microtime(true);
                $queryExecutionTime2 = ($queryEndTime2 - $queryStartTime2) * 1000; // Convert to milliseconds
                log_message('info', "getNilaiAlphabetSettings Query 2: Get detail settings END - Execution Time: {$queryExecutionTime2}ms");
                log_message('info', "getNilaiAlphabetSettings Query 2 Result Count: " . count($settings));

                $result = [];
                foreach ($settings as $setting) {
                    // Convert value berdasarkan type
                    $result[$setting['SettingKey']] = $this->convertSettingValue($setting['SettingValue'], $setting['SettingType']);
                }

                log_message('info', "getNilaiAlphabetSettings END - Returning object with " . count($result) . " settings");
                return (object)$result;
            }
        }

        // Return null if condition not met
        log_message('info', "getNilaiAlphabetSettings END - Returning null");
        return null;
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
     * OPTIMIZED: Menyimpan data santri dan materi di tabel nilai dengan bulk operations
     * Mengurangi N+1 query problem dengan menggunakan bulk operations
     * 
     * @param int $StatusSantri Status santri (0: baru, 1: aktif, 2: nonaktif)
     * @param array $santriList List santri yang akan diproses
     * @return array Result dengan success count dan error count
     */
    public function saveDataSantriDanMateriDiTabelNilaiOptimized($StatusSantri, $santriList)
    {
        if (empty($santriList)) {
            return ['success' => 0, 'errors' => 0, 'message' => 'No santri to process'];
        }

        // Start database transaction for consistency
        $this->db->transStart();

        try {
            // 1. Tentukan status dan tahun ajaran
            $tahunAjaran = $this->getTahunAjaran($StatusSantri);
            $isSantriBaru = ($StatusSantri == 0);

            $result = [
                'success' => 0,
                'errors' => 0,
                'processed_santri' => [],
                'failed_santri' => []
            ];

            // 2. Bulk process santri baru (if needed)
            if ($isSantriBaru) {
                $this->prosesSantriBaruBulk($santriList);
            } else {
                $this->prosesNaikKelasBulk($santriList, $tahunAjaran);
            }

            // 3. Bulk process materi dan nilai
            $this->prosesMateriDanNilaiBulk($santriList, $tahunAjaran);

            // 4. Mark all as successful
            $result['success'] = count($santriList);
            $result['processed_santri'] = array_column($santriList, 'IdSantri');

            $this->db->transComplete();

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Log error
            log_message('error', 'Error in saveDataSantriDanMateriDiTabelNilaiOptimized: ' . $e->getMessage());

            return [
                'success' => 0,
                'errors' => count($santriList),
                'message' => 'Transaction failed: ' . $e->getMessage(),
                'failed_santri' => array_column($santriList, 'IdSantri')
            ];
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
     * OPTIMIZED: Bulk process santri baru
     * @param array $santriList
     * @return void
     */
    private function prosesSantriBaruBulk($santriList)
    {
        if (empty($santriList)) {
            return;
        }

        // Prepare bulk data for kelas_santri table
        $kelasSantriData = [];
        $santriIds = [];

        foreach ($santriList as $santri) {
            $kelasSantriData[] = [
                'IdKelas' => $santri['IdKelas'],
                'IdTpq' => $santri['IdTpq'],
                'IdSantri' => $santri['IdSantri'],
                'IdTahunAjaran' => $santri['IdTahunAjaran']
            ];
            $santriIds[] = $santri['IdSantri'];
        }

        // Bulk insert kelas_santri
        if (!empty($kelasSantriData)) {
            $this->db->table('tbl_kelas_santri')->insertBatch($kelasSantriData);
        }

        // Bulk update status aktif santri
        if (!empty($santriIds)) {
            $this->db->table('tbl_santri_baru')
                ->whereIn('IdSantri', $santriIds)
                ->update(['Active' => 1]);
        }
    }

    /**
     * OPTIMIZED: Bulk process naik kelas
     * @param array $santriList
     * @param string $tahunAjaran
     * @return void
     */
    private function prosesNaikKelasBulk($santriList, $tahunAjaran)
    {
        if (empty($santriList)) {
            return;
        }

        $kelasSantriData = [];
        $updateIds = [];

        foreach ($santriList as $santri) {
            // Get next kelas
            $kelasBaru = $this->getNextKelas($santri['IdKelas']);

            $kelasSantriData[] = [
                'IdKelas' => $kelasBaru,
                'IdTpq' => $santri['IdTpq'],
                'IdSantri' => $santri['IdSantri'],
                'IdTahunAjaran' => $tahunAjaran
            ];

            $updateIds[] = $santri['Id'];
        }

        // Bulk insert new kelas_santri records
        if (!empty($kelasSantriData)) {
            $this->db->table('tbl_kelas_santri')->insertBatch($kelasSantriData);
        }

        // Bulk update old records status
        if (!empty($updateIds)) {
            $this->db->table('tbl_kelas_santri')
                ->whereIn('Id', $updateIds)
                ->update(['Status' => 0]);
        }
    }

    /**
     * OPTIMIZED: Bulk process materi dan nilai
     * 
     * PENTING untuk proses pindah TPQ:
     * - Menggunakan IdTpq dari $santriList (yang sudah sesuai dengan IdTpq di tbl_santri_baru)
     * - Generate nilai dengan IdTpq baru untuk konsistensi query
     * - Data lama dengan IdTpq lama tetap ada (tidak dihapus)
     * 
     * @param array $santriList Array dengan struktur:
     *   [
     *     'IdSantri' => ...,
     *     'IdKelas' => ...,
     *     'IdTpq' => ..., // IdTpq baru (untuk santri pindah TPQ)
     *     'IdTahunAjaran' => ...
     *   ]
     * @param string $tahunAjaran
     * @return void
     */
    private function prosesMateriDanNilaiBulk($santriList, $tahunAjaran)
    {
        if (empty($santriList)) {
            return;
        }

        // Get unique TPQ and Kelas combinations
        $tpqKelasMap = [];
        foreach ($santriList as $santri) {
            $key = $santri['IdTpq'] . '_' . $santri['IdKelas'];
            if (!isset($tpqKelasMap[$key])) {
                $tpqKelasMap[$key] = [
                    'IdTpq' => $santri['IdTpq'], // IdTpq baru untuk santri pindah TPQ
                    'IdKelas' => $santri['IdKelas']
                ];
            }
        }

        // Get all materi for all unique TPQ-Kelas combinations
        // Menggunakan IdTpq baru untuk mengambil materi dari TPQ baru
        $allMateri = [];
        foreach ($tpqKelasMap as $tpqKelas) {
            $materi = $this->getKelasMateriPelajaran(
                $tpqKelas['IdKelas'],
                $tpqKelas['IdTpq'] // IdTpq baru
            );
            $allMateri = array_merge($allMateri, $materi);
        }

        // Prepare bulk data for nilai table
        // Semua nilai yang di-generate menggunakan IdTpq baru
        $nilaiData = [];
        foreach ($santriList as $santri) {
            foreach ($allMateri as $materi) {
                // Only process materi for this santri's kelas
                if ($materi->IdKelas == $santri['IdKelas']) {
                    // Process semester ganjil
                    if ($materi->SemesterGanjil == 1) {
                        $nilaiData[] = [
                            'IdTpq' => $santri['IdTpq'], // IdTpq baru
                            'IdSantri' => $santri['IdSantri'],
                            'IdKelas' => $materi->IdKelas,
                            'IdMateri' => $materi->IdMateri,
                            'IdTahunAjaran' => $tahunAjaran,
                            'Semester' => 'Ganjil',
                            'Nilai' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }

                    // Process semester genap
                    if ($materi->SemesterGenap == 1) {
                        $nilaiData[] = [
                            'IdTpq' => $santri['IdTpq'], // IdTpq baru
                            'IdSantri' => $santri['IdSantri'],
                            'IdKelas' => $materi->IdKelas,
                            'IdMateri' => $materi->IdMateri,
                            'IdTahunAjaran' => $tahunAjaran,
                            'Semester' => 'Genap',
                            'Nilai' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }

        // Bulk insert nilai data dengan IdTpq baru
        if (!empty($nilaiData)) {
            $this->db->table('tbl_nilai')->insertBatch($nilaiData);
        }
    }

    /**
     * Generate nilai untuk santri yang sudah ada di kelas_santri
     * Hanya generate nilai tanpa insert ke kelas_santri
     * 
     * @param array $santriList List santri yang akan diproses
     * @param string $tahunAjaran Tahun ajaran
     * @return array Result dengan success count dan error count
     */
    public function generateNilaiUntukSantri($santriList, $tahunAjaran)
    {
        if (empty($santriList)) {
            return ['success' => 0, 'errors' => 0, 'message' => 'No santri to process'];
        }

        // Start database transaction for consistency
        $this->db->transStart();

        try {
            $result = [
                'success' => 0,
                'errors' => 0,
                'processed_santri' => [],
                'failed_santri' => []
            ];

            // Bulk process materi dan nilai saja (tanpa insert ke kelas_santri)
            $this->prosesMateriDanNilaiBulk($santriList, $tahunAjaran);

            // Mark all as successful
            $result['success'] = count($santriList);
            $result['processed_santri'] = array_column($santriList, 'IdSantri');

            $this->db->transComplete();

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Log error
            log_message('error', 'Error in generateNilaiUntukSantri: ' . $e->getMessage());

            return [
                'success' => 0,
                'errors' => count($santriList),
                'message' => 'Transaction failed: ' . $e->getMessage(),
                'failed_santri' => array_column($santriList, 'IdSantri')
            ];
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
        $santriListFromSantriBaru = $this->santriBaruModel->where(['IdTpq' => $IdTpq, 'Active' => 1])->findAll();

        // Step1b: Ambil Data Santri dari tbl_kelas_santri untuk tahun ajaran tertentu
        $builder = $this->db->table('tbl_kelas_santri ks');
        $builder->select('ks.IdSantri, ks.IdKelas, s.Active');
        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri', 'inner');
        $builder->where('ks.IdTpq', $IdTpq);
        $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        $builder->where('ks.Status', 1); // Hanya status aktif
        $builder->where('s.Active', 1); // Hanya santri aktif
        $santriListFromKelasSantri = $builder->get()->getResultArray();

        // Step1c: Gabungkan kedua sumber data dan hapus duplikasi
        $santriMap = [];

        // Tambahkan dari tbl_santri_baru
        foreach ($santriListFromSantriBaru as $santri) {
            $key = $santri['IdSantri'];
            if (!isset($santriMap[$key])) {
                $santriMap[$key] = [
                    'IdSantri' => $santri['IdSantri'],
                    'IdKelas' => $santri['IdKelas'] // IdKelas dari tbl_santri_baru
                ];
            }
        }

        // Tambahkan/update dari tbl_kelas_santri (prioritas lebih tinggi karena lebih spesifik per tahun ajaran)
        foreach ($santriListFromKelasSantri as $santri) {
            $key = $santri['IdSantri'];
            $santriMap[$key] = [
                'IdSantri' => $santri['IdSantri'],
                'IdKelas' => $santri['IdKelas'] // IdKelas dari tbl_kelas_santri (lebih akurat untuk tahun ajaran)
            ];
        }

        $santriList = array_values($santriMap);

        // Step2: Ambil semua nilai yang sudah ada dalam satu query
        $existingNilai = $this->nilaiModel->where([
            'IdTpq' => $IdTpq,
            'IdTahunAjaran' => $IdTahunAjaran
        ])->findAll();

        // Step3: Buat array untuk pengecekan cepat (per santri, per materi, per semester)
        $existingNilaiMap = [];
        foreach ($existingNilai as $nilai) {
            // Key: IdSantri_IdMateri_IdKelas_Semester untuk pengecekan per santri
            $key = $nilai['IdSantri'] . '_' . $nilai['IdMateri'] . '_' . $nilai['IdKelas'] . '_' . $nilai['Semester'];
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
                    // Key untuk pengecekan: IdSantri_IdMateri_IdKelas_Semester
                    $key = $santri['IdSantri'] . '_' . $materi->IdMateri . '_' . $materi->IdKelas . '_Ganjil';
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
                    // Key untuk pengecekan: IdSantri_IdMateri_IdKelas_Semester
                    $key = $santri['IdSantri'] . '_' . $materi->IdMateri . '_' . $materi->IdKelas . '_Genap';
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

        // Pastikan hanya menghitung santri yang Active = 1
        $builder->where('tbl_santri_baru.Active', 1);

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
            if (is_array($kelasIds)) {
                $builder->whereIn('IdKelas', $kelasIds);
            } else {
                $builder->where('IdKelas', $kelasIds);
            }
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

    /**
     * Mengambil IdKelas dari tbl_kelas_santri berdasarkan IdTpq, IdTahunAjaran, dan Semester
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @param mixed $Semester
     * @return array
     */
    public function getIdKelasByTahunAjaranDanSemester($IdTpq, $IdTahunAjaran, $Semester, $IdSantri = null)
    {
        $builder = $this->db->table('tbl_kelas_santri ks');
        $builder->select('ks.IdKelas, k.NamaKelas');
        $builder->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas');
        $builder->where('ks.IdTpq', $IdTpq);
        $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        if ($IdSantri != null) {
            $builder->where('ks.IdSantri', $IdSantri);
        }

        // Filter berdasarkan semester - cek apakah ada nilai untuk semester tersebut
        $builder->join('tbl_nilai n', 'n.IdSantri = ks.IdSantri AND n.IdKelas = ks.IdKelas AND n.Semester = "' . $Semester . '"', 'inner');

        $builder->groupBy('ks.IdKelas, k.NamaKelas');
        $builder->orderBy('k.NamaKelas', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get jabatan by ID
     */
    public function getJabatanById($IdJabatan)
    {
        $builder = $this->db->table('tbl_jabatan');
        $builder->select('*');
        $builder->where('IdJabatan', $IdJabatan);

        return $builder->get()->getRowArray();
    }

    /**
     * Check guru kelas permission
     */
    public function checkGuruKelasPermission($IdTpq, $IdGuru, $IdKelas, $IdTahunAjaran)
    {
        // Cek apakah guru adalah Kepala TPQ dari struktur lembaga
        $kepalaTpqData = $this->getStrukturLembagaJabatan($IdGuru, $IdTpq);
        $isKepalaTpq = false;

        foreach ($kepalaTpqData as $jabatan) {
            if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                $isKepalaTpq = true;
                break;
            }
        }

        // Jika adalah Kepala TPQ, kembalikan data jabatan tanpa kelas
        if ($isKepalaTpq) {
            foreach ($kepalaTpqData as $jabatan) {
                if ($jabatan['NamaJabatan'] === 'Kepala TPQ') {
                    return [
                        'IdGuru' => $IdGuru,
                        'IdTpq' => $IdTpq,
                        'IdKelas' => null, // Kepala TPQ tidak memiliki kelas
                        'IdTahunAjaran' => $IdTahunAjaran,
                        'IdJabatan' => $jabatan['IdJabatan'],
                        'NamaJabatan' => $jabatan['NamaJabatan'],
                        'TanggalStart' => $jabatan['TanggalStart'],
                        'TanggalAkhir' => $jabatan['TanggalAkhir']
                    ];
                }
            }
        }

        // Jika bukan Kepala TPQ, cek dari guru kelas seperti biasa
        $builder = $this->db->table('tbl_guru_kelas gk');
        $builder->select('gk.*, j.NamaJabatan');
        $builder->join('tbl_jabatan j', 'j.IdJabatan = gk.IdJabatan');
        $builder->where('gk.IdTpq', $IdTpq);
        $builder->where('gk.IdGuru', $IdGuru);
        $builder->where('gk.IdKelas', $IdKelas);
        $builder->where('gk.IdTahunAjaran', $IdTahunAjaran);
        return $builder->get()->getRowArray();
    }

    /**
     * Get all guru kelas permissions for multiple classes
     */
    public function getGuruKelasPermissions($IdTpq, $IdGuru, $IdKelas, $IdTahunAjaran)
    {
        // Cek apakah guru adalah Kepala TPQ dari struktur lembaga
        $kepalaTpqData = $this->getStrukturLembagaJabatan($IdGuru, $IdTpq);
        $isKepalaTpq = false;

        foreach ($kepalaTpqData as $jabatan) {
            if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                $isKepalaTpq = true;
                break;
            }
        }

        // Jika adalah Kepala TPQ, kembalikan data jabatan tanpa kelas
        if ($isKepalaTpq) {
            $result = [];
            foreach ($kepalaTpqData as $jabatan) {
                if ($jabatan['NamaJabatan'] === 'Kepala TPQ') {
                    $result[] = [
                        'IdGuru' => $IdGuru,
                        'IdTpq' => $IdTpq,
                        'IdKelas' => null, // Kepala TPQ tidak memiliki kelas
                        'IdTahunAjaran' => $IdTahunAjaran,
                        'IdJabatan' => $jabatan['IdJabatan'],
                        'NamaJabatan' => $jabatan['NamaJabatan'],
                        'NamaKelas' => null, // Kepala TPQ tidak memiliki kelas
                        'TanggalStart' => $jabatan['TanggalStart'],
                        'TanggalAkhir' => $jabatan['TanggalAkhir']
                    ];
                }
            }
            return $result;
        }

        // Jika bukan Kepala TPQ, ambil data dari guru kelas seperti biasa
        $builder = $this->db->table('tbl_guru_kelas gk');
        $builder->select('gk.*, j.NamaJabatan, k.NamaKelas');
        $builder->join('tbl_jabatan j', 'j.IdJabatan = gk.IdJabatan');
        $builder->join('tbl_kelas k', 'k.IdKelas = gk.IdKelas');
        $builder->where('gk.IdTpq', $IdTpq);
        $builder->where('gk.IdGuru', $IdGuru);
        $builder->where('gk.IdTahunAjaran', $IdTahunAjaran);

        if (is_array($IdKelas)) {
            $builder->whereIn('gk.IdKelas', $IdKelas);
        } else {
            $builder->where('gk.IdKelas', $IdKelas);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get list IdTahunAjaran from tbl_guru_kelas filter IdTpq grouped IdTahunAjaran
     */
    public function getListIdTahunAjaranFromGuruKelas($IdTpq = null)
    {
        $builder = $this->db->table('tbl_guru_kelas gk');
        $builder->select('gk.IdTahunAjaran');
        if ($IdTpq != null) {
            if (is_array($IdTpq)) {
                $builder->whereIn('gk.IdTpq', $IdTpq);
            } else {
                $builder->where('gk.IdTpq', $IdTpq);
            }
        }
        $builder->groupBy('gk.IdTahunAjaran');
        $result = $builder->get()->getResultArray();

        // Extract only the IdTahunAjaran values into a simple array
        return array_column($result, 'IdTahunAjaran');
    }

    /**
     * Get all settings for a TPQ in one optimized query with caching
     * This replaces multiple getSettingLimitInputNilai, getNilaiAlphabetSettings, and getNilaiArabicSettings calls
     * Handles different data types based on SettingType column
     * 
     * @param string $IdTpq TPQ ID
     * @return array Array containing all settings with proper data types
     */
    public function getAllTpqSettings($IdTpq)
    {
        // Check cache first
        $cacheKey = 'tpq_settings_' . $IdTpq;
        $cachedSettings = cache($cacheKey);

        if ($cachedSettings !== null) {
            return $cachedSettings;
        }

        $settings = [];

        // Use single query with UNION to get both TPQ and default settings including SettingType
        $query = "
            SELECT SettingKey, SettingValue, SettingType, IdTpq 
            FROM tbl_tools 
            WHERE IdTpq IN (?, 'default') 
            AND SettingKey IN ('Min', 'Max', 'Nilai_Alphabet', 'Nilai_Angka_Arabic')
            ORDER BY IdTpq = ? DESC, IdTpq
        ";

        $result = $this->db->query($query, [$IdTpq, $IdTpq])->getResultArray();

        // Process results - prioritize TPQ settings over defaults
        foreach ($result as $setting) {
            if (!isset($settings[$setting['SettingKey']])) {
                // Convert SettingValue based on SettingType
                $settings[$setting['SettingKey']] = $this->convertSettingValue(
                    $setting['SettingValue'],
                    $setting['SettingType']
                );
            }
        }

        // Set final defaults if still missing with proper data types
        $settings['Min'] = $settings['Min'] ?? 0; // Always integer
        $settings['Max'] = $settings['Max'] ?? 100; // Always integer
        $settings['Nilai_Alphabet'] = $settings['Nilai_Alphabet'] ?? false; // Always boolean
        $settings['Nilai_Angka_Arabic'] = $settings['Nilai_Angka_Arabic'] ?? false; // Always boolean

        // Cache for 1 hour (3600 seconds)
        cache()->save($cacheKey, $settings, 3600);

        return $settings;
    }

    /**
     * Convert SettingValue based on SettingType
     * 
     * @param string $value Raw setting value from database
     * @param string $type Setting type (text, number, boolean, json)
     * @return mixed Converted value with proper data type
     */
    private function convertSettingValue($value, $type)
    {
        switch (strtolower($type)) {
            case 'number':
                // Convert to integer or float
                return is_numeric($value) ? (int)$value : 0;

            case 'boolean':
                // Convert to boolean
                if (is_bool($value)) {
                    return $value;
                }

                $lowerValue = strtolower(trim($value));
                return in_array($lowerValue, ['true', '1', 'yes', 'on', 'enabled', 'active']);

            case 'json':
                // Decode JSON string
                $decoded = json_decode($value, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;

            case 'text':
            default:
                // Return as string
                return (string)$value;
        }
    }

    /**
     * Clear TPQ settings cache
     * Call this method when settings are updated
     * 
     * @param string $IdTpq TPQ ID
     */
    public function clearTpqSettingsCache($IdTpq = null)
    {
        if ($IdTpq) {
            // Clear specific TPQ cache
            cache()->delete('tpq_settings_' . $IdTpq);
        } else {
            // Clear all TPQ settings cache
            $cache = \Config\Services::cache();
            $cacheInfo = $cache->getCacheInfo();

            if (isset($cacheInfo['tpq_settings_'])) {
                foreach ($cacheInfo['tpq_settings_'] as $key => $value) {
                    if (strpos($key, 'tpq_settings_') === 0) {
                        cache()->delete($key);
                    }
                }
            }
        }
    }

    /**
     * Get comprehensive guru session data in optimized queries
     * This replaces multiple separate queries in setGuruSessionData
     * 
     * @param string $idGuru Guru ID
     * @return array Comprehensive guru data including settings
     */
    public function getGuruSessionDataOptimized($idGuru)
    {
        // Start database transaction for consistency
        $this->db->transStart();

        try {
            // Get guru kelas data with all related information in one query
            $guruKelasData = $this->getDataGuruKelas(IdGuru: $idGuru);

            $result = [
                'guruKelasData' => $guruKelasData,
                'settings' => null,
                'kelasOnLatestTa' => null,
                'idTpqFromGuru' => null,
                'tahunAjaranFromGuruKelas' => null
            ];

            if (!empty($guruKelasData)) {
                // Extract TPQ ID from first record
                $idTpq = $guruKelasData[0]->IdTpq ?? null;

                if ($idTpq) {
                    // Get all settings in one optimized query
                    $result['settings'] = $this->getAllTpqSettings($idTpq);

                    // Get unique tahun ajaran list
                    $idTahunAjaranList = array_unique(array_column($guruKelasData, 'IdTahunAjaran'));
                    $idTahunAjaranList = array_values($idTahunAjaranList);

                    if (!empty($idTahunAjaranList)) {
                        // Get kelas on latest tahun ajaran
                        $latestTahunAjaran = $idTahunAjaranList[count($idTahunAjaranList) - 1];
                        $result['kelasOnLatestTa'] = $this->getListKelas(
                            IdTpq: $idTpq,
                            IdTahunAjaran: $latestTahunAjaran,
                            IdGuru: $idGuru
                        );
                    }
                }
            } else {
                // If no guru kelas data, get TPQ from guru table
                $idTpqData = $this->getIdTpq($idGuru);
                if ($idTpqData && isset($idTpqData['IdTpq'])) {
                    $result['idTpqFromGuru'] = $idTpqData['IdTpq'];

                    // Get settings for this TPQ
                    $result['settings'] = $this->getAllTpqSettings($idTpqData['IdTpq']);

                    // Get tahun ajaran from guru kelas
                    $result['tahunAjaranFromGuruKelas'] = $this->getListIdTahunAjaranFromGuruKelas($idTpqData['IdTpq']);
                }
            }

            $this->db->transComplete();

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    // Get Nama Jabatan dan Jabatan by IdGuru dan IdTpq pada tbl_struktur_lembaga
    public function getStrukturLembagaJabatan($IdGuru, $IdTpq)
    {
        $builder = $this->db->table('tbl_struktur_lembaga sl');
        $builder->select('sl.*, j.NamaJabatan');
        $builder->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan');
        $builder->where('sl.IdGuru', $IdGuru);
        $builder->where('sl.IdTpq', $IdTpq);
        return $builder->get()->getResultArray();
    }

    // Get list IdKelas dari tbl_kelas_santri filter IdTpq, IdTahunAjaran, IdKelas
    public function getListIdKelasFromKelasSantri($IdTpq, $IdTahunAjaran)
    {
        $builder = $this->db->table('tbl_kelas_santri');
        $builder->select('IdKelas');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('IdTahunAjaran', $IdTahunAjaran);
        $builder->groupBy('IdKelas');
        return array_column($builder->get()->getResultArray(), 'IdKelas');
    }

    /**
     * Mengecek status MDA dan mapping kelas untuk menentukan apakah kelas santri sesuai dengan mapping MDA
     * 
     * @param string $idTpq ID TPQ
     * @param string $namaKelasSantri Nama kelas santri (contoh: "TPQ3/SD3" atau "TPQ3")
     * @return array Array dengan keys:
     *   - 'hasMda': bool - Apakah memiliki lembaga MDA
     *   - 'useMdaData': bool - Apakah kelas sesuai dengan mapping MDA
     *   - 'mappedMdaKelas': string|null - Kelas MDA yang sesuai dari mapping (contoh: "MDA1")
     *   - 'kelasMapping': array - Array mapping kelas (key: TPQ kelas, value: MDA kelas)
     */
    public function checkMdaKelasMapping($idTpq, $namaKelasSantri)
    {
        $toolsModel = new \App\Models\ToolsModel();

        // Handle admin dengan IdTpq=0, gunakan 'default' sebagai gantinya
        $idTpqForQuery = (empty($idTpq) || $idTpq == '0' || $idTpq == 0) ? 'default' : $idTpq;

        // Check apakah memiliki lembaga MDA
        $hasMda = $toolsModel->getSettingAsBool($idTpqForQuery, 'MDA_S1_ApakahMemilikiLembagaMDATA', false);
        $useMdaData = false;
        $mappedMdaKelas = null;
        $kelasMapping = [];

        if ($hasMda) {
            // Ambil mapping persamaan kelas MDA
            $persamaanKelas = $toolsModel->getSettingAsString($idTpqForQuery, 'MDA_S1_PersamaanKelasMDA', '');

            // Parse mapping: TPQ3=MDA1, TPQ4=MDA2, TPQ5=MDA3, TPQ6=MDA4
            $kelasMapping = $this->parseMdaKelasMapping($persamaanKelas);

            // Check apakah nama kelas santri sesuai dengan mapping MDA
            $matchResult = $this->matchKelasWithMdaMapping($namaKelasSantri, $kelasMapping);
            $useMdaData = $matchResult['useMdaData'];
            $mappedMdaKelas = $matchResult['mappedMdaKelas'];
        }

        return [
            'hasMda' => $hasMda,
            'useMdaData' => $useMdaData,
            'mappedMdaKelas' => $mappedMdaKelas,
            'kelasMapping' => $kelasMapping
        ];
    }

    /**
     * Parse string mapping kelas MDA menjadi array
     * Format input: "TPQ3=MDA1, TPQ4=MDA2, TPQ5=MDA3, TPQ6=MDA4"
     * 
     * @param string $persamaanKelas String mapping kelas MDA (contoh: "TPQ3=MDA1,TPQ4=MDA2")
     * @return array Array mapping kelas (key: TPQ kelas, value: MDA kelas)
     */
    public function parseMdaKelasMapping($persamaanKelas)
    {
        $kelasMapping = [];

        if (!empty($persamaanKelas)) {
            $pairs = explode(',', $persamaanKelas);
            foreach ($pairs as $pair) {
                $pair = trim($pair);
                if (strpos($pair, '=') !== false) {
                    list($tpqKelas, $mdaKelas) = explode('=', $pair, 2);
                    $kelasMapping[trim($tpqKelas)] = trim($mdaKelas);
                }
            }
        }

        return $kelasMapping;
    }

    /**
     * Mencocokkan nama kelas santri dengan mapping MDA
     * Mapping format: TPQ3=MDA1, TPQ4=MDA2, TPQ5=MDA3, TPQ6=MDA4
     * Format nama kelas: "TPQ3/SD3" atau "TPQ3" atau "Kelas TPQ3", dll
     * Gunakan search/in (contains) untuk matching, bukan exact match
     * 
     * @param string $namaKelasSantri Nama kelas santri (contoh: "TPQ3/SD3" atau "TPQ3")
     * @param array $kelasMapping Array mapping kelas (key: TPQ kelas, value: MDA kelas)
     * @return array Array dengan keys:
     *   - 'useMdaData': bool - Apakah kelas sesuai dengan mapping MDA
     *   - 'mappedMdaKelas': string|null - Kelas MDA yang sesuai dari mapping (contoh: "MDA1")
     */
    public function matchKelasWithMdaMapping($namaKelasSantri, $kelasMapping)
    {
        $useMdaData = false;
        $mappedMdaKelas = null;

        // Check apakah nama kelas santri sesuai dengan mapping MDA
        foreach ($kelasMapping as $tpqKelas => $mdaKelas) {
            // Normalisasi nama kelas untuk perbandingan
            $namaKelasNormalized = strtoupper(trim($namaKelasSantri));
            $tpqKelasNormalized = strtoupper(trim($tpqKelas));

            // Gunakan search/in (contains) untuk matching
            // Cek apakah nama kelas santri mengandung key mapping
            // Contoh: "TPQ3/SD3" mengandung "TPQ3" -> match
            $isMatch = false;

            // 1. Cek contains match (search in) - utama
            if (stripos($namaKelasNormalized, $tpqKelasNormalized) !== false) {
                $isMatch = true;
            }
            // 2. Cek juga pattern TPQ + angka jika mapping mengandung "TPQ"
            // Misal: mapping "TPQ3" bisa match dengan "TPQ3/SD3" atau "TPQ 3"
            elseif (strpos($tpqKelasNormalized, 'TPQ') !== false) {
                $tpqKelasWithoutPrefix = str_replace('TPQ', '', $tpqKelasNormalized);
                // Cek apakah ada pattern "TPQ" diikuti angka dari mapping
                // Contoh: "TPQ3" -> cek apakah ada "TPQ" + "3" di "TPQ3/SD3"
                if (!empty($tpqKelasWithoutPrefix)) {
                    // Pattern: TPQ diikuti angka dari mapping (bisa ada spasi atau karakter lain setelahnya)
                    if (preg_match('/TPQ\s*' . preg_quote($tpqKelasWithoutPrefix, '/') . '/i', $namaKelasNormalized)) {
                        $isMatch = true;
                    }
                }
            }

            if ($isMatch) {
                // Kelas santri sesuai dengan mapping MDA
                $useMdaData = true;
                $mappedMdaKelas = $mdaKelas; // Simpan kelas MDA yang sesuai
                log_message('debug', 'HelpFunctionModel: matchKelasWithMdaMapping - Kelas santri "' . $namaKelasSantri . '" sesuai dengan mapping MDA: ' . $tpqKelas . '=' . $mdaKelas);
                break;
            }
        }

        return [
            'useMdaData' => $useMdaData,
            'mappedMdaKelas' => $mappedMdaKelas
        ];
    }

    /**
     * Mengubah nama kelas menjadi kelas MDA jika sesuai dengan mapping
     * 
     * @param string $namaKelasSantri Nama kelas santri asli (contoh: "TPQ3/SD3" atau "TPQ3")
     * @param string|null $mappedMdaKelas Kelas MDA yang sesuai dari mapping (contoh: "MDA1")
     * @return string Nama kelas yang sudah diubah (jika sesuai) atau nama kelas asli (jika tidak sesuai)
     */
    public function convertKelasToMda($namaKelasSantri, $mappedMdaKelas)
    {
        if (empty($mappedMdaKelas)) {
            return $namaKelasSantri;
        }

        // Jika format kelas adalah "TPQ3/SD3", ganti menjadi "MDA1/SD3"
        // Jika format kelas adalah "TPQ3", ganti menjadi "MDA1"
        if (strpos($namaKelasSantri, '/') !== false) {
            // Ada separator "/", ganti bagian TPQ dengan MDA
            $parts = explode('/', $namaKelasSantri);
            $parts[0] = $mappedMdaKelas; // Ganti bagian pertama dengan kelas MDA
            $result = implode('/', $parts);
            log_message('debug', 'HelpFunctionModel: convertKelasToMda - Nama kelas diubah dari "' . $namaKelasSantri . '" menjadi "' . $result . '"');
            return $result;
        } else {
            // Tidak ada separator, ganti seluruhnya dengan kelas MDA
            log_message('info', 'HelpFunctionModel: convertKelasToMda - Nama kelas diubah dari "' . $namaKelasSantri . '" menjadi "' . $mappedMdaKelas . '"');
            return $mappedMdaKelas;
        }
    }

    /**
     * Get statistik santri berdasarkan jenis kelamin dan per kelas
     * @param mixed $IdTpq ID TPQ (0, null, atau empty untuk semua TPQ)
     * @return array
     */
    public function getStatistikSantri($IdTpq = 0)
    {
        // Handle null, empty, atau '0' sebagai 0 (semua TPQ)
        $idTpqForQuery = (empty($IdTpq) || $IdTpq == '0' || $IdTpq == 0) ? 0 : $IdTpq;

        $builder = $this->db->table('tbl_santri_baru');

        // Filter berdasarkan IdTpq jika tidak 0
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }

        // Hanya ambil santri aktif
        $builder->where('Active', 1);

        // Total santri
        $totalSantri = $builder->countAllResults(false);

        // Reset builder untuk query berikutnya
        $builder->resetQuery();

        // Santri laki-laki
        $builder->where('Active', 1);
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }
        $santriLaki = $builder->where('JenisKelamin', 'LAKI-LAKI')->countAllResults(false);

        // Reset builder
        $builder->resetQuery();

        // Santri perempuan
        $builder->where('Active', 1);
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }
        $santriPerempuan = $builder->where('JenisKelamin', 'PEREMPUAN')->countAllResults(false);

        // Statistik per kelas - buat builder baru untuk menghindari konflik alias
        $builderPerKelas = $this->db->table('tbl_santri_baru');
        $builderPerKelas->select('tbl_kelas.IdKelas, tbl_kelas.NamaKelas, 
                         COUNT(CASE WHEN tbl_santri_baru.JenisKelamin = "LAKI-LAKI" THEN 1 END) as LakiLaki,
                         COUNT(CASE WHEN tbl_santri_baru.JenisKelamin = "PEREMPUAN" THEN 1 END) as Perempuan,
                         COUNT(*) as Total');
        $builderPerKelas->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'inner');
        $builderPerKelas->where('tbl_santri_baru.Active', 1);
        if ($idTpqForQuery != 0) {
            $builderPerKelas->where('tbl_santri_baru.IdTpq', $idTpqForQuery);
        }
        $builderPerKelas->groupBy('tbl_kelas.IdKelas, tbl_kelas.NamaKelas');
        $builderPerKelas->orderBy('tbl_kelas.NamaKelas', 'ASC');
        $statistikPerKelas = $builderPerKelas->get()->getResultArray();

        return [
            'total' => $totalSantri,
            'laki_laki' => $santriLaki,
            'perempuan' => $santriPerempuan,
            'per_kelas' => $statistikPerKelas
        ];
    }

    /**
     * Get statistik guru berdasarkan jenis kelamin
     * @param mixed $IdTpq ID TPQ (0, null, atau empty untuk semua TPQ)
     * @return array
     */
    public function getStatistikGuru($IdTpq = 0)
    {
        // Handle null, empty, atau '0' sebagai 0 (semua TPQ)
        $idTpqForQuery = (empty($IdTpq) || $IdTpq == '0' || $IdTpq == 0) ? 0 : $IdTpq;

        $builder = $this->db->table('tbl_guru');

        // Filter berdasarkan IdTpq jika tidak 0
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }

        // Hanya ambil guru aktif
        $builder->where('Status', 1);

        // Total guru
        $totalGuru = $builder->countAllResults(false);

        // Reset builder untuk query berikutnya
        $builder->resetQuery();

        // Guru laki-laki
        $builder->where('Status', 1);
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }
        $guruLaki = $builder->where('JenisKelamin', 'LAKI-LAKI')->countAllResults(false);

        // Reset builder
        $builder->resetQuery();

        // Guru perempuan
        $builder->where('Status', 1);
        if ($idTpqForQuery != 0) {
            $builder->where('IdTpq', $idTpqForQuery);
        }
        $guruPerempuan = $builder->where('JenisKelamin', 'PEREMPUAN')->countAllResults(false);

        return [
            'total' => $totalGuru,
            'laki_laki' => $guruLaki,
            'perempuan' => $guruPerempuan
        ];
    }

    /**
     * Get statistik santri per TPQ (untuk admin)
     * @return array
     */
    public function getStatistikSantriPerTpq()
    {
        $builder = $this->db->table('tbl_santri_baru s');
        $builder->select('t.IdTpq, t.NamaTpq,
                         COUNT(CASE WHEN s.JenisKelamin = "LAKI-LAKI" THEN 1 END) as LakiLaki,
                         COUNT(CASE WHEN s.JenisKelamin = "PEREMPUAN" THEN 1 END) as Perempuan,
                         COUNT(*) as Total');
        $builder->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'inner');
        $builder->where('s.Active <', 2);
        $builder->groupBy('t.IdTpq, t.NamaTpq');
        $builder->orderBy('t.NamaTpq', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get statistik guru per TPQ (untuk admin)
     * @return array
     */
    public function getStatistikGuruPerTpq()
    {
        $builder = $this->db->table('tbl_guru g');
        $builder->select('t.IdTpq, t.NamaTpq,
                         COUNT(CASE WHEN g.JenisKelamin = "LAKI-LAKI" THEN 1 END) as LakiLaki,
                         COUNT(CASE WHEN g.JenisKelamin = "PEREMPUAN" THEN 1 END) as Perempuan,
                         COUNT(*) as Total');
        $builder->join('tbl_tpq t', 't.IdTpq = g.IdTpq', 'inner');
        $builder->where('g.Status', 1);
        $builder->groupBy('t.IdTpq, t.NamaTpq');
        $builder->orderBy('t.NamaTpq', 'ASC');

        return $builder->get()->getResultArray();
    }
}

