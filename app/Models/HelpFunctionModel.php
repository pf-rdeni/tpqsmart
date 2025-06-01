<?php

namespace App\Models;
use CodeIgniter\Model;

class HelpFunctionModel extends Model
{
    protected $santriModel;
    protected $kelasModel;
    protected $nilaiModel;
    protected $santriBaruModel;

    public function __construct()
    {
        parent::__construct();
        $this->santriModel = new \App\Models\SantriModel();
        $this->kelasModel = new \App\Models\KelasModel();
        $this->nilaiModel = new \App\Models\NilaiModel();
        $this->santriBaruModel = new \App\Models\SantriBaruModel();
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

    // get list kelas grouped kelas filter IdTpq, IdTahunAjaran dari tbl_kelas_santri
    public function getListKelas($IdTpq, $IdTahunAjaran)
    {
        $builder = $this->db->table('tbl_kelas_santri');
        $builder->select('tbl_kelas_santri.IdKelas, NamaKelas');
        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_kelas_santri.IdKelas');
        $builder->where('IdTpq', $IdTpq);
        $builder->where('IdTahunAjaran', $IdTahunAjaran);
        $builder->groupBy('tbl_kelas_santri.IdKelas, NamaKelas');
        $builder->orderBy('NamaKelas', 'ASC');

        return $builder->get()->getResultObject();
    }

    // Get value setting input nilai min dan max dari tbl_tools
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

    // Get nama Materi pelajaran berdasarkan IdKelas, IdTpq, Semester, TahunAjaran
    public function getMateriPelajaranByKelas($IdTpq, $IdKelas = null, $Semester)
    {
        $builder = $this->db->table('tbl_kelas_materi_pelajaran');
        $builder->select('IdKelas, tbl_kelas_materi_pelajaran.IdMateri, NamaMateri, Kategori');
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
        $builder->orderBy('IdKelas, IdMateri, Kategori');
        // Mengembalikan hasil sebagai objek
        return $builder->get()->getResultObject();
    }

    /**
     * ====================================================================================
     * Start Fungsi untuk menyimpan data santri dan materi pelajaran ke dalam tabel nilai
     * ====================================================================================
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
     */
    private function getTahunAjaran($StatusSantri)
    {
        if ($StatusSantri == 0) {
            return $this->getTahunAjaranSaatIni(); // Tahun ajaran saat ini untuk santri baru
        }
        return $this->getTahuanAjaranBerikutnya(0); // Tahun ajaran berikutnya untuk naik kelas
    }

    /**
     * Memproses data satu santri
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
     * Memproses data santri baru
     */
    private function prosesSantriBaru($dataSantri)
    {
        // 1. Simpan di tabel kelas_santri
        $this->kelasModel->insert($dataSantri);

        // 2. Update status aktif santri
        $this->santriBaruModel->updateActiveSantri($dataSantri['IdSantri']);
    }

    /**
     * Memproses kenaikan kelas
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
     * Memproses materi dan nilai
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
}

