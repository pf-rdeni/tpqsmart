<?php

namespace App\Models;

use CodeIgniter\Model;

class SantriBaruModel extends Model
{
    protected $table            = 'tbl_santri_baru';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        // Data TPQ
        'IdTpq', 'IdKelas',
        
        // Data Santri
        'IdSantri',
        'PhotoProfil', 'NikSantri', 'NamaSantri', 'JenisKelamin', 'NISN',
        'TempatLahirSantri', 'TanggalLahirSantri', 'AnakKe', 'JumlahSaudara',
        'CitaCita', 'CitaCitaLainya', 'Hobi', 'HobiLainya', 'NoHpSantri', 'EmailSantri',
        'KebutuhanKhusus', 'KebutuhanKhususLainya', 'KebutuhanDisabilitas',
        'KebutuhanDisabilitasLainya', 'YangBiayaSekolah', 'NamaKepalaKeluarga',
        'NoKIP', 'IdKartuKeluarga', 'FileKIP', 'FileKkSantri',
        
        // Data Ayah
        'NamaAyah', 'StatusAyah', 'NikAyah', 'KewarganegaraanAyah',
        'TempatLahirAyah', 'TanggalLahirAyah', 'PendidikanAyah',
        'PekerjaanUtamaAyah', 'PenghasilanUtamaAyah', 'NoHpAyah', 'FileKkAyah',
        
        // Alamat Ayah
        'TinggalDiluarNegeriAyah', 'StatusKepemilikanRumahAyah', 'ProvinsiAyah',
        'KabupatenKotaAyah', 'KecamatanAyah', 'KelurahanDesaAyah',
        'RwAyah',
        'RtAyah',
        'AlamatAyah',
        'KodePosAyah',
        
        // Data Ibu
        'NamaIbu', 'StatusIbu', 'NikIbu', 'KewarganegaraanIbu',
        'TempatLahirIbu', 'TanggalLahirIbu', 'PendidikanIbu',
        'PekerjaanUtamaIbu', 'PenghasilanUtamaIbu', 'NoHpIbu',
        'FileKkIbu',
        
        // Alamat Ibu
        'AlamatIbuSamaDenganAyah', 'TinggalDiluarNegeriIbu',
        'StatusKepemilikanRumahIbu', 'ProvinsiIbu', 'KabupatenKotaIbu',
        'KecamatanIbu',
        'KelurahanDesaIbu',
        'RwIbu',
        'RtIbu',
        'AlamatIbu', 'KodePosIbu',

        // Data Wali
        'StatusWali',
        'NamaWali',
        'NikWali',
        'KewarganegaraanWali',
        'TempatLahirWali', 'TanggalLahirWali', 'PendidikanWali',
        'PekerjaanUtamaWali', 'PenghasilanUtamaWali', 'NoHpWali',
        'FileKkWali',
        'NomorKKS',
        'NomorPKH',
        'FileKKS',
        'FilePKH',

        // Data Alamat Santri
        'StatusMukim',
        'StatusTempatTinggalSantri',
        'ProvinsiSantri',
        'KabupatenKotaSantri', 'KecamatanSantri', 'KelurahanDesaSantri',
        'RwSantri', 'RtSantri', 'AlamatSantri', 'KodePosSantri',
        'JarakTempuhSantri', 'TransportasiSantri', 'WaktuTempuhSantri',
        'TitikKoordinatSantri',

        // Active Santri
        'Active',
        'Status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    
    // Validation
    protected $validationRules = [
        'NikSantri'     => 'required|min_length[16]|max_length[16]|is_unique[DataSantriBaru.NikSantri,id,{id}]',
        'NamaSantri'    => 'required|min_length[3]|max_length[100]',
        'JenisKelamin'  => 'required|in_list[LAKI-LAKI,PEREMPUAN]',
        'TempatLahirSantri' => 'required',
        'TanggalLahirSantri' => 'required|valid_date',
        'AnakKe'        => 'required|numeric',
        'JumlahSaudara' => 'required|numeric',
        'NamaAyah'      => 'required|min_length[3]|max_length[100]',
        'NamaIbu'       => 'required|min_length[3]|max_length[100]',
        'AlamatSantri'  => 'required'
    ];
    
    protected $validationMessages = [
        'NikSantri' => [
            'required' => 'NIK Santri harus diisi',
            'min_length' => 'NIK Santri harus 16 digit',
            'max_length' => 'NIK Santri harus 16 digit',
            'is_unique' => 'NIK Santri sudah terdaftar'
        ],
        'NamaSantri' => [
            'required' => 'Nama Santri harus diisi',
            'min_length' => 'Nama Santri minimal 3 karakter',
            'max_length' => 'Nama Santri maksimal 100 karakter'
        ],
        // Tambahkan pesan validasi lainnya sesuai kebutuhan
    ];

    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom Methods
    public function getDetailSantri($IdSantri)
    {
        return $this->where('IdSantri', $IdSantri)->first();
    }

    public function getSantriByTPQ($idTpq)
    {
        return $this->where('IdTpq', $idTpq)->findAll();
    }

    public function searchSantri($keyword)
    {
        return $this->like('NamaSantri', $keyword)
                    ->orLike('NikSantri', $keyword)
                    ->orLike('NISN', $keyword)
                    ->findAll();
    }

    public function updateActiveSantri($idSantri)
    {
        if ($idSantri) {
            $this->set('Active', 1)
            ->where('Active', 0)
            ->where('IdSantri', $idSantri)
                ->update();
        }
    }

    public function insert($data = null, bool $returnID = true)
    {
        // Validasi data sebelum insert
        // if (!$this->validate($data)) {
        //     return false;
        // }

        // Lakukan insert data
        $result = parent::insert($data, $returnID);

        // Jika berhasil dan returnID true, kembalikan ID yang baru dibuat
        if ($result && $returnID) {
            return $this->getInsertID();
        }

        // Jika returnID false, kembalikan status berhasil/gagal
        return $result;
    }

    public function GetData()
    {
        $db = db_connect();
        $builder = $db->table('tbl_santri_baru');
        $builder->select('
            tbl_santri_baru.updated_at, 
            tbl_santri_baru.IdSantri, 
            tbl_santri_baru.NamaSantri, 
            tbl_santri_baru.JenisKelamin, 
            tbl_santri_baru.NamaAyah, 
            tbl_santri_baru.AlamatSantri,
            tbl_santri_baru.KelurahanDesaSantri,
            tbl_tpq.NamaTpq, 
            tbl_kelas.NamaKelas,
            tbl_santri_baru.Status,
            tbl_santri_baru.PhotoProfil
        ');
        
        // Tambahkan pengecekan join
        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left');
        $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left');
        $builder->orderBy('tbl_santri_baru.Status', 'DESC');
        $builder->orderBy('tbl_santri_baru.updated_at', 'ASC');
        // Tambahkan try-catch untuk menangani error
        try {
            $query = $builder->get();
            if ($query) {
                return $query->getResultArray();
            }
            return [];
        } catch (\Exception $e) {
            log_message('error', 'Error in GetData: ' . $e->getMessage());
            return [];
        }
    }

    public function GetDataPerKelasTpq($IdTpq)
    {
        $db = db_connect();
        $builder = $db->table('tbl_santri_baru');
        $builder->select('
            tbl_santri_baru.*, 
            tbl_tpq.NamaTpq,
            tbl_kelas.NamaKelas
        ');

        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left');
        $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left');
        $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
        $builder->orderBy('tbl_santri_baru.updated_at', 'DESC'); 

        try {
            $query = $builder->get();
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error in GetDataPerKelasTpq: ' . $e->getMessage());
            return [];
        }
    }

    public function GetDataAttachment($IdSantri)
    {
        return $this->where('IdSantri', $IdSantri)->first();
    }

    public function GetDataSantriPerKelas($IdTahunAjaran = 0, $IdKelas = 0, $IdGuru = null)
    {
        $db = db_connect();
        $builder = $db->table('tbl_kelas_santri ks');

        $builder->select('ks.IdTahunAjaran, k.IdKelas, k.NamaKelas, g.IdGuru, g.Nama AS GuruNama, 
                        s.IdSantri, s.NamaSantri, s.JenisKelamin, t.IdTpq, t.NamaTpq, t.Alamat, 
                        w.IdJabatan, j.NamaJabatan')
            ->distinct()
            ->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas')
            ->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri')
            ->join('tbl_tpq t', 'ks.IdTpq = t.IdTpq')
            // Tambahkan filter IdTahunAjaran di JOIN untuk menghindari duplikasi saat guru memiliki multiple kelas
            ->join('tbl_guru_kelas w', 'w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq AND w.IdTahunAjaran = ks.IdTahunAjaran', 'left')
            ->join('tbl_guru g', 'w.IdGuru = g.IdGuru', 'left')
            ->join('tbl_jabatan j', 'w.IdJabatan = j.IdJabatan', 'left')
            ->where('s.Active', 1);

        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('ks.IdTahunAjaran', $IdTahunAjaran);
            } else {
                $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
            }
        }

        if (!empty($IdGuru)) {
            $builder->where('w.IdGuru', $IdGuru);
        }

        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builder->whereIn('k.IdKelas', $IdKelas);
            } else {
                $builder->where('k.IdKelas', $IdKelas);
            }
        }

        $builder->orderBy('k.NamaKelas ASC, s.NamaSantri ASC');

        return $builder->get()->getResultObject();
    }

    /**
     * Mendapatkan data profil santri dengan filter berdasarkan TPQ dan kelas
     * @param mixed $IdTpq ID TPQ (null untuk semua TPQ)
     * @param mixed $IdKelas ID Kelas atau array ID Kelas (null untuk semua kelas)
     * @param array $idKelasArray Array ID Kelas untuk filter whereIn (untuk Operator)
     * @return array
     */
    public function getProfilSantri($IdTpq = null, $IdKelas = null, $idKelasArray = null)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        if ($IdTpq == null) {
            // Jika tidak ada filter TPQ, ambil semua data
            $builder->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC');
        } else {
            // Filter berdasarkan TPQ
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq);

            // Filter berdasarkan kelas
            if (!empty($idKelasArray)) {
                // Untuk Operator: filter berdasarkan array kelas dari TPQ
                $builder->whereIn('tbl_santri_baru.IdKelas', $idKelasArray);
            } elseif ($IdKelas !== null) {
                // Untuk role lain: filter berdasarkan IdKelas dari session
                if (is_array($IdKelas)) {
                    $builder->whereIn('tbl_santri_baru.IdKelas', $IdKelas);
                } else {
                    $builder->where('tbl_santri_baru.IdKelas', $IdKelas);
                }
            }

            $builder->orderBy('tbl_santri_baru.IdKelas', 'ASC')
                ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->orderBy('tbl_santri_baru.Status', 'DESC');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan detail santri berdasarkan IdSantri dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getDetailSantriById($IdSantri)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan detail santri untuk profil dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getProfilDetailSantri($IdSantri)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa as KelurahanDesaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan santri berdasarkan NIK
     * @param string $NikSantri
     * @return array|null
     */
    public function getSantriByNik($NikSantri)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.NikSantri', $NikSantri);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan list santri baru dengan filter TPQ dan kelas
     * @param mixed $IdTpq ID TPQ (null untuk semua TPQ)
     * @param mixed $IdKelas ID Kelas atau array ID Kelas (null untuk semua kelas)
     * @return array
     */
    public function getListSantriBaru($IdTpq = null, $IdKelas = null)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        if ($IdTpq == null) {
            $builder->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC');
        } else {
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq);

            if ($IdKelas !== null) {
                if (is_array($IdKelas)) {
                    $builder->whereIn('tbl_santri_baru.IdKelas', $IdKelas);
                } else {
                    $builder->where('tbl_santri_baru.IdKelas', $IdKelas);
                }
            }

            $builder->orderBy('tbl_santri_baru.IdKelas', 'ASC')
                ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->orderBy('tbl_santri_baru.Status', 'DESC');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan jumlah santri per TPQ
     * @return array
     */
    public function getJumlahSantriPerTpq()
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_tpq.IdTpq, COUNT(tbl_santri_baru.IdSantri) as JumlahSantri')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'right')
            ->groupBy('tbl_tpq.IdTpq');

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan list santri untuk atur santri baru dengan filter kompleks
     * @param mixed $IdTpq ID TPQ
     * @param mixed $IdKelas ID Kelas atau array ID Kelas
     * @param bool $isGuru Apakah user adalah Guru (filter Active=1)
     * @return array
     */
    public function getListAturSantriBaru($IdTpq = null, $IdKelas = null, $isGuru = false)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select([
            'tbl_santri_baru.*',
            'tbl_kelas.NamaKelas',
            'tbl_tpq.NamaTpq',
            'tbl_tpq.KelurahanDesa'
        ])
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        if ($IdTpq) {
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
        }

        if ($isGuru) {
            $builder->where('tbl_santri_baru.Active', 1);
        }

        if ($IdKelas !== null) {
            if (is_array($IdKelas)) {
                if (!empty($IdKelas)) {
                    $builder->whereIn('tbl_santri_baru.IdKelas', $IdKelas);
                }
            } else if (!empty($IdKelas)) {
                $builder->where('tbl_santri_baru.IdKelas', $IdKelas);
            }
        }

        $builder->orderBy('tbl_santri_baru.IdKelas', 'ASC')
            ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
            ->orderBy('tbl_santri_baru.Status', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan list santri untuk EMIS dengan filter TPQ
     * @param mixed $IdTpq ID TPQ (null untuk semua TPQ)
     * @return array
     */
    public function getListSantriEmis($IdTpq = null)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        if ($IdTpq == null) {
            $builder->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC');
        } else {
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq)
                ->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan detail santri untuk edit dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getDetailSantriForEdit($IdSantri)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa as KelurahanDesaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan detail santri untuk ubah kelas dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getDetailSantriForUbahKelas($IdSantri)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan detail santri untuk ubah TPQ dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getDetailSantriForUbahTpq($IdSantri)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan detail santri untuk konfirmasi delete dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getDetailSantriForDelete($IdSantri)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
            ->where('tbl_santri_baru.IdSantri', $IdSantri);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }
}