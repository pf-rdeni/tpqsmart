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

    public function GetDataPerKelasTpq($IdTpq, $IdTahunAjaran = null)
    {
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = session()->get('IdTahunAjaran');
        }
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = (new \App\Models\HelpFunctionModel())->getTahunAjaranSaatIni();
        }

        $db = db_connect();
        $builder = $db->table('tbl_santri_baru');

        if (!empty($IdTahunAjaran)) {
            $builder->select('
                tbl_santri_baru.*, 
                ks.IdKelas as IdKelas,
                ks.IdTahunAjaran,
                tbl_tpq.NamaTpq,
                tbl_kelas.NamaKelas
            ');
        $builder->select('
            tbl_santri_baru.*, 
            ks.IdKelas as IdKelas,
            ks.IdTahunAjaran,
            tbl_tpq.NamaTpq,
            tbl_kelas.NamaKelas
        ');

            $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'inner');
            $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'left');
            $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = ks.IdTpq', 'left');
            $builder->where('ks.IdTpq', $IdTpq);
        } else {
            $builder->select('
                tbl_santri_baru.*, 
                tbl_tpq.NamaTpq,
                tbl_kelas.NamaKelas
            ');
        // Inner join hanya untuk santri aktif di kelas pada tahun ajaran yang ditentukan
        $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = ' . $db->escape($IdTahunAjaran) . ' AND ks.Status = 1', 'inner');
        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'inner');
        $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = ks.IdTpq', 'left');

            $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left');
            $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left');
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
        }
        // Filter TPQ
        $builder->where('ks.IdTpq', $IdTpq);

        $builder->orderBy('tbl_santri_baru.updated_at', 'DESC'); 
        // Hanya santri aktif di data induk (Active = 1, bukan alumni / nonaktif Active = 2)
        $builder->where('tbl_santri_baru.Active', 1);

        // Jangan tampilkan kelas Alumni (IdKelas = 10 atau NamaKelas Alumni)
        $builder->where('ks.IdKelas !=', 10);
        $builder->where('tbl_santri_baru.IdKelas !=', 10);
        $builder->whereNotIn('tbl_kelas.NamaKelas', ['ALUMNI', 'Alumni', 'alumni']);

        $builder->orderBy('tbl_kelas.IdKelas', 'ASC');
        $builder->orderBy('tbl_santri_baru.NamaSantri', 'ASC');

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

    public function GetDataSantriPerKelas($IdTpq, $IdTahunAjaran = 0, $IdKelas = 0, $IdGuru = null)
    {
        $db = db_connect();
        $builder = $db->table('tbl_kelas_santri ks');
        $builder->select('
            ks.Id,
            ks.IdTahunAjaran,
            k.IdKelas,
            k.NamaKelas,
            g.IdGuru,
            g.Nama AS GuruNama,
            s.IdSantri,
            s.NamaSantri,
            s.JenisKelamin,
            s.PhotoProfil,
            t.IdTpq,
            t.NamaTpq,
            t.Alamat,
            w.IdJabatan,
            j.NamaJabatan
        ');

        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_tpq t', 'ks.IdTpq = t.IdTpq', 'left');
        $builder->join('tbl_guru_kelas w', 'w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq AND w.IdTahunAjaran = ks.IdTahunAjaran', 'left');
        $builder->join('tbl_guru g', 'w.IdGuru = g.IdGuru', 'left');
        $builder->join('tbl_jabatan j', 'w.IdJabatan = j.IdJabatan', 'left');

        // Menambahkan filter Active=1
        $builder->where('s.Active', 1);
        
        // Tambahkan filter Status = 1 untuk tbl_kelas_santri (hanya jika tahun ajaran adalah tahun ajaran terbaru/aktif)
        $latestYearRow = $db->table('tbl_kelas_santri')->selectMax('IdTahunAjaran')->get()->getRow();
        $latestYear = $latestYearRow ? $latestYearRow->IdTahunAjaran : null;

        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                if (in_array($latestYear, $IdTahunAjaran)) {
                    $builder->where('ks.Status', 1);
                }
            } else {
                if ($IdTahunAjaran == $latestYear) {
                    $builder->where('ks.Status', 1);
                }
            }
        } else {
            $builder->where('ks.Status', 1);
        }

        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('ks.IdTahunAjaran', (array)$IdTahunAjaran);
            } else {
                $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
            }
        }

        if ($IdGuru !== null && $IdGuru != 0) {
            $builder->where('w.IdGuru', $IdGuru);
        }

        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builder->whereIn('k.IdKelas', (array)$IdKelas);
            } else {
                $builder->where('k.IdKelas', $IdKelas);
            }
        }

        if (!empty($IdTpq)) {
            if (is_array($IdTpq)) {
                $builder->whereIn('ks.IdTpq', (array)$IdTpq);
            } else {
                $builder->where('ks.IdTpq', $IdTpq);
            }
        }

        // Gunakan DISTINCT untuk menghindari duplikasi
        $builder->distinct();
        
        // GroupBy dengan s.IdSantri sebagai kolom utama untuk menghindari duplikasi
        // Note: GroupBy might strict mode error in some SQL configs, but copying verbatim from SantriModel
        $builder->groupBy([
            's.IdSantri', 
            'ks.Id', 'ks.IdTahunAjaran', 'k.IdKelas', 'k.NamaKelas',
            's.NamaSantri', 's.JenisKelamin', 's.PhotoProfil',
            't.IdTpq', 't.NamaTpq', 't.Alamat',
            'w.IdJabatan', 'j.NamaJabatan'
        ]);

        $builder->orderBy('k.NamaKelas', 'ASC');
        $builder->orderBy('s.NamaSantri', 'ASC');

        return $builder->get()->getResultObject();
    }

    // GetTotalSantri
    public function GetTotalSantri($IdTpq, $IdTahunAjaran = 0, $IdKelas = 0, $IdGuru = null)
    {
        $db = db_connect();
        $builder = $db->table('tbl_kelas_santri ks');
        $builder->select('COUNT(DISTINCT s.IdSantri) AS TotalSantri');

        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri AND s.Active = 1', 'inner');
        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'inner');
        $builder->join('tbl_tpq t', 'ks.IdTpq = t.IdTpq', 'inner');
        $builder->join('tbl_guru_kelas w', 'w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq AND w.IdTahunAjaran = ks.IdTahunAjaran', 'left');
        $builder->join('tbl_guru g', 'w.IdGuru = g.IdGuru', 'left');

        $builder->where('ks.IdTpq', $IdTpq);

        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('ks.IdTahunAjaran', (array)$IdTahunAjaran);
            } else {
                $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
            }
        }

        if ($IdGuru !== null && $IdGuru != 0) {
            $builder->where('w.IdGuru', $IdGuru);
        }

        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builder->whereIn('k.IdKelas', (array)$IdKelas);
            } else {
                $builder->where('k.IdKelas', $IdKelas);
            }
        }

        return $builder->get()->getRow()->TotalSantri;
    }

    /**
     * Mendapatkan data profil santri dengan filter berdasarkan TPQ dan kelas
     * @param mixed $IdTpq ID TPQ (null untuk semua TPQ)
     * @param mixed $IdKelas ID Kelas atau array ID Kelas (null untuk semua kelas)
     * @param array $idKelasArray Array ID Kelas untuk filter whereIn (untuk Operator)
     * @return array
     */
    public function getProfilSantri($IdTpq = null, $IdKelas = null, $idKelasArray = null, $IdTahunAjaran = null)
    {
        if ($IdTahunAjaran === null) {
            $IdTahunAjaran = session()->get('IdTahunAjaran');
        }

        $builder = $this->db->table('tbl_santri_baru');
        
        // Filter santri aktif (Active = 1) agar sinkron dengan absensi
        $builder->where('tbl_santri_baru.Active', 1);
        $builder->where('tbl_kelas.IdKelas !=', 10);
        $builder->where('UPPER(tbl_kelas.NamaKelas) !=', 'ALUMNI');

        if (!empty($IdTahunAjaran)) {
            $builder->where('ks.Status', 1);
            $builder->select('tbl_santri_baru.*, ks.IdKelas as IdKelas, ks.IdTahunAjaran, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
                ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'inner')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = ks.IdTpq');
        } else {
            // Fallback ke perilaku lama jika tidak ada tahun ajaran terpilih
            $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa, NULL as IdTahunAjaran')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');
        }

        if ($IdTpq == null) {
            // Jika tidak ada filter TPQ, ambil semua data
            $builder->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC');
        } else {
            // Filter berdasarkan TPQ (gunakan ks.IdTpq jika ada tahun ajaran agar konsisten)
            if (!empty($IdTahunAjaran)) {
                $builder->where('ks.IdTpq', $IdTpq);
            } else {
                $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
            }

            // Filter berdasarkan kelas
            if (!empty($IdTahunAjaran)) {
                if (!empty($idKelasArray)) {
                    $builder->whereIn('ks.IdKelas', $idKelasArray);
                } elseif ($IdKelas !== null) {
                    if (is_array($IdKelas)) {
                        $builder->whereIn('ks.IdKelas', $IdKelas);
                    } else {
                        $builder->where('ks.IdKelas', $IdKelas);
                    }
                }
            } else {
                if (!empty($idKelasArray)) {
                    $builder->whereIn('tbl_santri_baru.IdKelas', $idKelasArray);
                } elseif ($IdKelas !== null) {
                    if (is_array($IdKelas)) {
                        $builder->whereIn('tbl_santri_baru.IdKelas', $IdKelas);
                    } else {
                        $builder->where('tbl_santri_baru.IdKelas', $IdKelas);
                    }
                }
            }

            if (!empty($IdTahunAjaran)) {
                $builder->orderBy('ks.IdKelas', 'ASC');
            } else {
                $builder->orderBy('tbl_santri_baru.IdKelas', 'ASC');
            }
            
            $builder->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->orderBy('tbl_santri_baru.Status', 'DESC');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan detail santri berdasarkan IdSantri dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getDetailSantriById($IdSantri, $IdTahunAjaran = null)
    {
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = session()->get('IdTahunAjaran');
        }

        $builder = $this->db->table('tbl_santri_baru');
        if (!empty($IdTahunAjaran)) {
            $builder->select('
                tbl_santri_baru.*, 
                COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas) as IdKelas, 
                COALESCE(tbl_kelas.NamaKelas, k_pendaftaran.NamaKelas) as NamaKelas, 
                tbl_tpq.NamaTpq
            ')
                ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'left')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'left')
                ->join('tbl_kelas k_pendaftaran', 'k_pendaftaran.IdKelas = tbl_santri_baru.IdKelas', 'left')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
                ->where('tbl_santri_baru.IdSantri', $IdSantri);
        } else {
            $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
                ->where('tbl_santri_baru.IdSantri', $IdSantri);
        }

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan detail santri untuk profil dengan join kelas dan TPQ
     * @param string $IdSantri
     * @return array|null
     */
    public function getProfilDetailSantri($IdSantri, $IdTahunAjaran = null)
    {
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = session()->get('IdTahunAjaran');
        }

        $builder = $this->db->table('tbl_santri_baru');
        if (!empty($IdTahunAjaran)) {
            $builder->select('
                tbl_santri_baru.*, 
                COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas) as IdKelas, 
                COALESCE(tbl_kelas.NamaKelas, k_pendaftaran.NamaKelas) as NamaKelas, 
                tbl_tpq.NamaTpq, 
                tbl_tpq.KelurahanDesa as KelurahanDesaTpq
            ')
                ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'left')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'left')
                ->join('tbl_kelas k_pendaftaran', 'k_pendaftaran.IdKelas = tbl_santri_baru.IdKelas', 'left')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
                ->where('tbl_santri_baru.IdSantri', $IdSantri);
        } else {
            $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa as KelurahanDesaTpq')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
                ->where('tbl_santri_baru.IdSantri', $IdSantri);
        }

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Mendapatkan santri berdasarkan NIK
     * @param string $NikSantri
     * @return array|null
     */
    public function getSantriByNik($NikSantri, $IdTahunAjaran = null)
    {
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = session()->get('IdTahunAjaran');
        }

        $builder = $this->db->table('tbl_santri_baru');
        if (!empty($IdTahunAjaran)) {
            $builder->select('
                tbl_santri_baru.*, 
                COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas) as IdKelas, 
                COALESCE(tbl_kelas.NamaKelas, k_pendaftaran.NamaKelas) as NamaKelas, 
                tbl_tpq.NamaTpq
            ')
                ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'left')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'left')
                ->join('tbl_kelas k_pendaftaran', 'k_pendaftaran.IdKelas = tbl_santri_baru.IdKelas', 'left')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
                ->where('tbl_santri_baru.NikSantri', $NikSantri);
        } else {
            $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
                ->where('tbl_santri_baru.NikSantri', $NikSantri);
        }

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
    public function getJumlahSantriPerTpq($IdTahunAjaran = null)
    {
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('tbl_tpq.IdTpq, COUNT(tbl_santri_baru.IdSantri) as JumlahSantri')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'right')
            ->groupBy('tbl_tpq.IdTpq');
        $db = db_connect();
        $builder = $db->table('tbl_tpq t');

        if (!empty($IdTahunAjaran)) {
            $builder->select('t.IdTpq, COUNT(DISTINCT ks.IdSantri) as JumlahSantri')
                ->join('tbl_kelas_santri ks', 'ks.IdTpq = t.IdTpq AND ks.IdTahunAjaran = ' . $db->escape($IdTahunAjaran) . ' AND ks.Status = 1 AND ks.IdKelas != 10', 'left')
                ->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri AND s.Active = 1 AND s.IdKelas != 10', 'left')
                ->groupBy('t.IdTpq');
        } else {
            $builder->select('t.IdTpq, COUNT(tbl_santri_baru.IdSantri) as JumlahSantri')
                ->join('tbl_santri_baru', 'tbl_santri_baru.IdTpq = t.IdTpq AND tbl_santri_baru.Active = 1 AND tbl_santri_baru.IdKelas != 10', 'left')
                ->groupBy('t.IdTpq');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan list santri untuk atur santri baru dengan filter kompleks
     * @param mixed $IdTpq ID TPQ
     * @param mixed $IdKelas ID Kelas atau array ID Kelas
     * @param bool $isGuru Apakah user adalah Guru (filter Active=1)
     * @param mixed $IdTahunAjaran ID Tahun Ajaran
     * @param bool $filterAlumniAll Tampilkan seluruh alumni lintas Tahun Ajaran
     * @return array
     */
    public function getListAturSantriBaru($IdTpq = null, $IdKelas = null, $isGuru = false, $IdTahunAjaran = null, $filterAlumniAll = false, $filterActive = null)
    {
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = session()->get('IdTahunAjaran');
        }

        $builder = $this->db->table('tbl_santri_baru');

        if ($filterAlumniAll) {
            $subQuery = $this->db->table('tbl_kelas_santri')
                ->select('IdSantri, MAX(IdTahunAjaran) as MaxTA')
                ->groupBy('IdSantri');

            $builder->select([
                'tbl_santri_baru.*',
                'COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas) as IdKelas',
                'COALESCE(ks.IdTahunAjaran, "") as IdTahunAjaran',
                'tbl_kelas.NamaKelas',
                'tbl_tpq.NamaTpq',
                'tbl_tpq.KelurahanDesa'
            ])
                ->join('(' . $subQuery->getCompiledSelect() . ') ks_max', 'ks_max.IdSantri = tbl_santri_baru.IdSantri', 'left')
                ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = ks_max.MaxTA', 'left')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas)', 'left')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left');

            $builder->groupStart()
                ->where('tbl_santri_baru.Active', 2)
                ->orWhere('UPPER(tbl_kelas.NamaKelas)', 'ALUMNI')
                ->groupEnd();

            if ($IdTpq) {
                $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
            }

            if ($isGuru) {
                $builder->where('tbl_santri_baru.Active', 1);
            } elseif ($filterActive !== null && $filterActive !== '') {
                $builder->where('tbl_santri_baru.Active', (int)$filterActive);
            }

            if ($IdKelas !== null) {
                if (is_array($IdKelas)) {
                    $kelasClean = array_filter(array_map('intval', $IdKelas));
                    if (!empty($kelasClean)) {
                        $builder->whereIn('COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas)', $kelasClean);
                    }
                } else if (!empty($IdKelas)) {
                    $builder->where('COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas)', (int)$IdKelas);
                }
            }

            $builder->orderBy('ks.IdTahunAjaran', 'DESC')
                ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->orderBy('tbl_santri_baru.Status', 'DESC');

            return $builder->get()->getResultArray();
        }

        if (!empty($IdTahunAjaran)) {
            $builder->select([
                'tbl_santri_baru.*',
                'ks.IdKelas as IdKelas',
                'ks.IdTahunAjaran',
                'tbl_kelas.NamaKelas',
                'tbl_tpq.NamaTpq',
                'tbl_tpq.KelurahanDesa'
            ])
                ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'inner')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

            if ($IdTpq) {
                $builder->where('ks.IdTpq', $IdTpq);
            }

            if ($isGuru) {
                $builder->where('tbl_santri_baru.Active', 1);
            } elseif ($filterActive !== null && $filterActive !== '') {
                $builder->where('tbl_santri_baru.Active', (int)$filterActive);
            }

            if ($IdKelas !== null) {
                if (is_array($IdKelas)) {
                    if (!empty($IdKelas)) {
                        $builder->whereIn('ks.IdKelas', $IdKelas);
                    }
                } else if (!empty($IdKelas)) {
                    $builder->where('ks.IdKelas', $IdKelas);
                }
            }

            $builder->orderBy('ks.IdKelas', 'ASC')
                ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->orderBy('tbl_santri_baru.Status', 'DESC');
        } else {
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
            } elseif ($filterActive !== null && $filterActive !== '') {
                $builder->where('tbl_santri_baru.Active', (int)$filterActive);
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
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan list santri untuk EMIS dengan filter TPQ dan Kelas
     * @param mixed $IdTpq ID TPQ (null untuk semua TPQ)
     * @param mixed $IdKelas ID Kelas (null untuk semua kelas)
     * @return array
     */
    /**
     * Mendapatkan daftar santri untuk sinkronisasi / transfer EMIS 4.0
     * Diselaraskan dengan GetDataPerKelasTpq agar hanya memunculkan data santri yang aktif di tbl_kelas_santri
     * pada tahun ajaran aktif saat ini dan bukan alumni.
     * 
     * @param mixed $IdTpq ID TPQ (null untuk semua TPQ)
     * @param mixed $IdKelas ID Kelas (null untuk semua kelas)
     * @param string|null $IdTahunAjaran Tahun Ajaran (null untuk otomatis tahun ajaran saat ini)
     * @return array
     */
    /**
     * Membersihkan nilai RT/RW agar '0', '00', '000' dianggap kosong (tidak valid untuk EMIS).
     */
    public function cleanRtRwEmis($val): string
    {
        $trimmed = trim((string) $val);
        $digits = preg_replace('/[^0-9]/', '', $trimmed);
        if ($digits === '' || (int) $digits === 0) {
            return '';
        }
        return $digits;
    }

    /**
     * Mengecek apakah Alamat Ayah kosong atau memiliki kemiripan wajar dengan Alamat Santri
     * (menangani variasi tanda baca, spasi, maupun typo seperti N01 vs No1 / Bloka vs Blok A).
     */
    public function isAlamatAyahSimilarToSantri(array $santri): bool
    {
        $normText = static function ($val): string {
            $s = strtolower(trim((string) $val));
            // Samakan pola "n01" -> "no1" (angka 0 setelah huruf 'n' sebelum digit)
            $s = preg_replace('/\bn0+(\d+)/', 'no$1', $s);
            return preg_replace('/[^a-z0-9]/', '', $s);
        };

        $alamatSantri = $normText($santri['AlamatSantri'] ?? '');
        $alamatAyah   = $normText($santri['AlamatAyah'] ?? '');

        if ($alamatAyah === '' && $alamatSantri !== '') {
            return true;
        }
        if ($alamatAyah === '' || $alamatSantri === '') {
            return false;
        }
        if ($alamatAyah === $alamatSantri) {
            return true;
        }

        similar_text($alamatAyah, $alamatSantri, $pct);
        if ($pct >= 75.0) {
            return true;
        }

        // Cek kemiripan jika nomor rumah/jalan utama sama dan kemiripan teks >= 65%
        preg_match_all('/\d+/', $alamatSantri, $mSantri);
        preg_match_all('/\d+/', $alamatAyah, $mAyah);
        $numsSantri = $mSantri[0] ?? [];
        $numsAyah   = $mAyah[0] ?? [];
        if (!empty($numsSantri) && !empty($numsAyah)) {
            $commonNums = array_intersect($numsSantri, $numsAyah);
            if (!empty($commonNums) && $pct >= 65.0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Menyelesaikan nilai RT dan RW Ayah untuk EMIS:
     * tidak pernah mengembalikan '000'/'00'/'0'; jika RT/RW Ayah tidak valid/kosong dan alamat mirip Santri,
     * ambil RT/RW milik Santri.
     */
    public function resolveRtRwAyahEmis(array $santri): array
    {
        $rtAyah = $this->cleanRtRwEmis($santri['RtAyah'] ?? '');
        $rwAyah = $this->cleanRtRwEmis($santri['RwAyah'] ?? '');
        $rtSantri = $this->cleanRtRwEmis($santri['RtSantri'] ?? '');
        $rwSantri = $this->cleanRtRwEmis($santri['RwSantri'] ?? '');

        $isSimilar = $this->isAlamatAyahSimilarToSantri($santri);

        if ($rtAyah === '' && $isSimilar) {
            $rtAyah = $rtSantri;
        }
        if ($rwAyah === '' && $isSimilar) {
            $rwAyah = $rwSantri;
        }

        // Jika masih kosong / 000 (baik di Ayah maupun Santri), fallback ke '001'
        if ($rtAyah === '') {
            $rtAyah = '001';
        }
        if ($rwAyah === '') {
            $rwAyah = '001';
        }

        return ['rt' => $rtAyah, 'rw' => $rwAyah];
    }

    /**
     * Menormalisasi StatusTempatTinggalSantri untuk kebutuhan tampilan & pengiriman EMIS:
     * jika tercatat "Lainnya" padahal alamat Ayah kosong atau identik/mirip dengan alamat Santri,
     * kembalikan "Tinggal dengan Ayah Kandung" (atau Ibu Kandung bila Ayah tidak masih hidup).
     */
    public function resolveStatusTempatTinggalEmis(array $santri): string
    {
        $rawStatus = !empty($santri['StatusTempatTinggalSantri'])
            ? trim((string) $santri['StatusTempatTinggalSantri'])
            : 'Tinggal dengan Ayah Kandung';

        if (stripos($rawStatus, 'lain') === false) {
            return $rawStatus;
        }

        $normAlnum = static function ($val): string {
            return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $val)));
        };

        $alamatMatch = $this->isAlamatAyahSimilarToSantri($santri);
        $rtSantri    = $this->cleanRtRwEmis($santri['RtSantri'] ?? '');
        $rtAyah      = $this->cleanRtRwEmis($santri['RtAyah'] ?? '');
        $rwSantri    = $this->cleanRtRwEmis($santri['RwSantri'] ?? '');
        $rwAyah      = $this->cleanRtRwEmis($santri['RwAyah'] ?? '');
        $desaSantri  = $normAlnum($santri['KelurahanDesaSantri'] ?? '');
        $desaAyah    = $normAlnum($santri['KelurahanDesaAyah'] ?? '');

        $rtMatch   = ($rtAyah === '' || $rtSantri === '' || $rtAyah === $rtSantri);
        $rwMatch   = ($rwAyah === '' || $rwSantri === '' || $rwAyah === $rwSantri);
        $desaMatch = ($desaAyah === '' || $desaSantri === '' || $desaAyah === $desaSantri);

        if ($alamatMatch && $rtMatch && $rwMatch && $desaMatch) {
            $statusAyah = trim((string) ($santri['StatusAyah'] ?? ''));
            $statusIbu  = trim((string) ($santri['StatusIbu'] ?? ''));
            if (strcasecmp($statusAyah, 'Masih Hidup') === 0) {
                return 'Tinggal dengan Ayah Kandung';
            }
            if (strcasecmp($statusIbu, 'Masih Hidup') === 0) {
                return 'Tinggal dengan Ibu Kandung';
            }
            return 'Tinggal dengan Ayah Kandung';
        }

        return $rawStatus;
    }

    public function getListSantriEmis($IdTpq = null, $IdKelas = null, $IdTahunAjaran = null, $Semester = null)
    {
        $helpFunc = new \App\Models\HelpFunctionModel();
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = session()->get('IdTahunAjaran') ?: $helpFunc->getTahunAjaranSaatIni();
        }
        if (empty($Semester)) {
            $Semester = session()->get('Semester') ?: $helpFunc->getSemesterSaatIni();
        }

        $builder = $this->db->table('tbl_santri_baru');

        $builder->select('
            tbl_santri_baru.*, 
            ks.IdKelas as IdKelas, 
            ks.IdTahunAjaran,
            tbl_kelas.NamaKelas as NamaKelas, 
            k_pendaftaran.NamaKelas as NamaKelasPendaftaran, 
            tbl_tpq.NamaTpq, 
            tbl_tpq.KelurahanDesa,
            COALESCE(se.StatusEmis, "belum") as StatusEmis,
            se.TglUpdateEmis,
            COALESCE(se.StatusAlamatEmis, "belum") as StatusAlamatEmis,
            se.TglUpdateAlamatEmis,
            se.UpdatedBy as EmisUpdatedBy,
            se.Catatan as CatatanEmis,
            se.IdTahunAjaran as EmisIdTahunAjaran,
            se.Semester as EmisSemester
        ')
        // Inner join ke tbl_kelas_santri persis seperti GetDataPerKelasTpq
        ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'inner')
        ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'left')
        ->join('tbl_kelas k_pendaftaran', 'k_pendaftaran.IdKelas = tbl_santri_baru.IdKelas', 'left')
        ->join('tbl_tpq', 'tbl_tpq.IdTpq = ks.IdTpq', 'left')
        ->join(
            'tbl_santri_emis se',
            'se.IdSantri = tbl_santri_baru.IdSantri AND se.IdTahunAjaran = ' . $this->db->escape($IdTahunAjaran) . ' AND se.Semester = ' . $this->db->escape($Semester),
            'left'
        );

        if (!empty($IdTpq)) {
            $builder->where('ks.IdTpq', $IdTpq);
        }

        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builder->whereIn('ks.IdKelas', $IdKelas);
            } else {
                $builder->where('ks.IdKelas', $IdKelas);
            }
        }

        // Hanya santri yang aktif di data induk (Active = 1, bukan nonaktif/alumni Active = 2)
        $builder->where('tbl_santri_baru.Active', 1);

        // Jangan tampilkan santri Alumni (IdKelas = 10 atau NamaKelas Alumni)
        $builder->where('ks.IdKelas !=', 10);
        $builder->where('tbl_santri_baru.IdKelas !=', 10);
        $builder->whereNotIn('tbl_kelas.NamaKelas', ['ALUMNI', 'Alumni', 'alumni']);

        $builder->orderBy('ks.IdKelas', 'ASC')
            ->orderBy('tbl_santri_baru.NamaSantri', 'ASC');

        $rows = $builder->get()->getResultArray();
        foreach ($rows as &$row) {
            $row['StatusTempatTinggalSantri'] = $this->resolveStatusTempatTinggalEmis($row);
            $resolvedRtRw = $this->resolveRtRwAyahEmis($row);
            $row['RtAyah'] = $resolvedRtRw['rt'];
            $row['RwAyah'] = $resolvedRtRw['rw'];
        }
        return $rows;
    }

    /**
     * Mendapatkan detail 1 santri untuk keperluan sinkronisasi / transfer EMIS 4.0
     * @param string $IdSantri
     * @return array|null
     */
    public function getSantriForEmis($IdSantri)
    {
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = (new \App\Models\HelpFunctionModel())->getTahunAjaranSaatIni();
        }

        $builder = $this->db->table('tbl_santri_baru');

        $builder->select('
            tbl_santri_baru.*, 
            COALESCE(ks.IdKelas, tbl_santri_baru.IdKelas) as IdKelas, 
            COALESCE(tbl_kelas.NamaKelas, k_pendaftaran.NamaKelas) as NamaKelas, 
            tbl_tpq.NamaTpq, 
            tbl_tpq.KelurahanDesa
        ')
        ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = "' . $IdTahunAjaran . '"', 'left')
        ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'left')
        ->join('tbl_kelas k_pendaftaran', 'k_pendaftaran.IdKelas = tbl_santri_baru.IdKelas', 'left')
        ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
        ->where('tbl_santri_baru.IdSantri', $IdSantri);

        return $builder->get()->getRowArray() ?: null;
    }

    /**
     * Helper internal untuk upsert baris tbl_santri_emis berdasarkan (IdSantri, IdTahunAjaran, Semester)
     */
    private function upsertStatusEmisRow(string $IdSantri, array $data, ?string $IdTpq = null, ?string $IdTahunAjaran = null, ?string $Semester = null): bool
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');
        $helpFunc = new \App\Models\HelpFunctionModel();

        if (empty($IdTahunAjaran)) {
            $IdTahunAjaran = session()->get('IdTahunAjaran') ?: $helpFunc->getTahunAjaranSaatIni();
        }
        if (empty($Semester)) {
            $Semester = session()->get('Semester') ?: $helpFunc->getSemesterSaatIni();
        }
        if (empty($IdTpq)) {
            $rowSantri = $db->table('tbl_santri_baru')->select('IdTpq')->where('IdSantri', $IdSantri)->get()->getRowArray();
            if ($rowSantri && !empty($rowSantri['IdTpq'])) {
                $IdTpq = $rowSantri['IdTpq'];
            }
        }

        $data['IdSantri']      = $IdSantri;
        $data['IdTahunAjaran'] = $IdTahunAjaran;
        $data['Semester']      = $Semester;
        $data['updated_at']    = $now;
        if (!empty($IdTpq)) {
            $data['IdTpq'] = $IdTpq;
        }

        $whereKey = [
            'IdSantri'      => $IdSantri,
            'IdTahunAjaran' => $IdTahunAjaran,
            'Semester'      => $Semester,
        ];

        $existing = $db->table('tbl_santri_emis')->where($whereKey)->get()->getRowArray();
        if ($existing) {
            $result = $db->table('tbl_santri_emis')->where('id', $existing['id'])->update($data);
            log_message('info', "[EMIS] upsertStatusEmisRow UPDATE id={$existing['id']} IdSantri={$IdSantri} TA={$IdTahunAjaran} Sem={$Semester} result=" . ($result ? 'true' : 'false'));
            return $result;
        }

        $data['created_at'] = $now;
        $result = $db->table('tbl_santri_emis')->insert($data);
        log_message('info', "[EMIS] upsertStatusEmisRow INSERT IdSantri={$IdSantri} TA={$IdTahunAjaran} Sem={$Semester} result=" . ($result ? 'true' : 'false'));
        return $result;
    }

    /**
     * Memperbarui / mencatat status sinkronisasi EMIS santri ke tbl_santri_emis
     * @param string $IdSantri
     * @param string $status 'selesai', 'konflik', atau 'belum'
     * @param string|null $updatedBy Nama/ID user operator
     * @param string|null $IdTpq
     * @param string|null $catatan Keterangan / alasan jika ada konflik dll
     * @param string|null $IdTahunAjaran
     * @param string|null $Semester
     * @return bool
     */
    public function updateStatusEmis($IdSantri, $status = 'selesai', $updatedBy = null, $IdTpq = null, $catatan = null, $IdTahunAjaran = null, $Semester = null)
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            'StatusEmis'    => $status,
            'TglUpdateEmis' => ($status === 'selesai' || $status === 'konflik') ? $now : null,
            'UpdatedBy'     => $updatedBy,
        ];
        if ($catatan !== null) {
            $data['Catatan'] = trim($catatan);
        }

        return $this->upsertStatusEmisRow((string) $IdSantri, $data, $IdTpq, $IdTahunAjaran, $Semester);
    }

    /**
     * Memperbarui status update alamat santri di EMIS ke tbl_santri_emis
     * @param string $IdSantri
     * @param string $status 'selesai' atau 'belum'
     * @param string|null $updatedBy
     * @param string|null $IdTpq
     * @param string|null $IdTahunAjaran
     * @param string|null $Semester
     * @return bool
     */
    public function updateStatusAlamatEmis($IdSantri, $status = 'selesai', $updatedBy = null, $IdTpq = null, $IdTahunAjaran = null, $Semester = null)
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            'StatusAlamatEmis'    => $status,
            'TglUpdateAlamatEmis' => ($status === 'selesai') ? $now : null,
            'UpdatedBy'           => $updatedBy,
        ];

        return $this->upsertStatusEmisRow((string) $IdSantri, $data, $IdTpq, $IdTahunAjaran, $Semester);
    }

    /**
     * Mereset status EMIS santri (menjadi 'belum')
     * @param string|null $IdTpq Filter per TPQ jika ada
     * @return bool
     */
    public function resetStatusEmis($IdTpq = null)
    {
        $db = $this->db;
        $builder = $db->table('tbl_santri_emis');
        $now = date('Y-m-d H:i:s');
        $data = [
            'StatusEmis'    => 'belum',
            'TglUpdateEmis' => null,
            'Catatan'       => null,
            'updated_at'    => $now
        ];

        if (!empty($IdTpq)) {
            $subQuery = $db->table('tbl_santri_baru')->select('IdSantri')->where('IdTpq', $IdTpq);
            $builder->whereIn('IdSantri', $subQuery);
        }

        return $builder->update($data);
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

    /**
     * Mendapatkan data peserta MDT dengan join tbl_mda untuk mendapatkan NamaTpq dan KepalaSekolah.
     * Menggunakan raw SQL untuk menghindari masalah collation antar tabel.
     *
     * @param mixed      $IdTpq        ID TPQ — null = semua
     * @param mixed      $IdKelas      ID Kelas atau array ID Kelas — null = semua
     * @param array|null $idKelasArray Array ID Kelas untuk filter whereIn (Operator)
     * @return array
     */
    public function getDataPesertaMdt($IdTpq = null, $IdKelas = null, $idKelasArray = null)
    {
        $db = \Config\Database::connect();

        // Base SQL — JOIN pakai COLLATE hanya pada sisi tbl_mda untuk fix collation mismatch
        $sql = "SELECT
                    sb.IdSantri,
                    sb.NamaSantri,
                    sb.JenisKelamin,
                    sb.NamaAyah,
                    sb.NamaIbu,
                    sb.TempatLahirSantri,
                    sb.TanggalLahirSantri,
                    sb.IdKelas,
                    sb.IdTpq,
                    k.NamaKelas,
                    COALESCE(mda.NamaTpq, tpq.NamaTpq) AS NamaMdt,
                    COALESCE(mda.KepalaSekolah, tpq.KepalaSekolah) AS KepalaSekolahMdt
                FROM tbl_santri_baru sb
                LEFT JOIN tbl_kelas k
                    ON k.IdKelas = sb.IdKelas
                LEFT JOIN tbl_tpq tpq
                    ON tpq.IdTpq = sb.IdTpq
                LEFT JOIN tbl_mda mda
                    ON mda.IdTpq COLLATE utf8mb4_unicode_ci = sb.IdTpq";

        // Build WHERE conditions dengan parameterized query
        $conditions = [];
        $params     = [];

        if (!empty($IdTpq)) {
            if (is_array($IdTpq)) {
                $placeholders = implode(',', array_fill(0, count($IdTpq), '?'));
                $conditions[] = "sb.IdTpq IN ($placeholders)";
                $params       = array_merge($params, array_values($IdTpq));
            } else {
                $conditions[] = "sb.IdTpq = ?";
                $params[]     = $IdTpq;
            }
        }

        if (!empty($idKelasArray)) {
            $placeholders = implode(',', array_fill(0, count($idKelasArray), '?'));
            $conditions[] = "sb.IdKelas IN ($placeholders)";
            $params       = array_merge($params, array_values($idKelasArray));
        } elseif ($IdKelas !== null) {
            if (is_array($IdKelas) && !empty($IdKelas)) {
                $placeholders = implode(',', array_fill(0, count($IdKelas), '?'));
                $conditions[] = "sb.IdKelas IN ($placeholders)";
                $params       = array_merge($params, array_values($IdKelas));
            } elseif (!is_array($IdKelas)) {
                $conditions[] = "sb.IdKelas = ?";
                $params[]     = $IdKelas;
            }
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY COALESCE(mda.NamaTpq, tpq.NamaTpq) ASC, k.NamaKelas ASC, sb.NamaSantri ASC';

        $query = $db->query($sql, $params);
        return $query ? $query->getResultArray() : [];
    }

    /**
     * Base query builder untuk Dashboard EMIS agar angka KPI, rekap, dan daftar konflik konsisten
     * dengan getListSantriEmis().
     */
    private function buildEmisDashboardBaseQuery(string $IdTahunAjaran, string $Semester, ?string $IdTpq = null, $IdKelas = null)
    {
        $builder = $this->db->table('tbl_santri_baru')
            ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri AND ks.IdTahunAjaran = ' . $this->db->escape($IdTahunAjaran), 'inner')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = ks.IdKelas', 'left')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = ks.IdTpq', 'left')
            ->join(
                'tbl_santri_emis se',
                'se.IdSantri = tbl_santri_baru.IdSantri AND se.IdTahunAjaran = ' . $this->db->escape($IdTahunAjaran) . ' AND se.Semester = ' . $this->db->escape($Semester),
                'left'
            )
            ->where('tbl_santri_baru.Active', 1)
            ->where('ks.IdKelas !=', 10)
            ->where('tbl_santri_baru.IdKelas !=', 10)
            ->whereNotIn('tbl_kelas.NamaKelas', ['ALUMNI', 'Alumni', 'alumni']);

        if (!empty($IdTpq)) {
            $builder->where('ks.IdTpq', $IdTpq);
        }

        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builder->whereIn('ks.IdKelas', $IdKelas);
            } else {
                $builder->where('ks.IdKelas', $IdKelas);
            }
        }

        return $builder;
    }

    /**
     * Klasifikasikan pesan konflik EMIS dari kolom Catatan untuk memudahkan filter & eskalasi.
     */
    public function classifyEmisConflict(?string $catatan): array
    {
        $text = strtolower(trim((string) $catatan));
        if ($text !== '' && (strpos($text, 'terdaftar aktif') !== false || strpos($text, 'aktif di') !== false)) {
            return [
                'kode' => 'aktif_lembaga_sendiri',
                'label' => 'Sudah Aktif di Lembaga',
                'badge' => 'warning'
            ];
        }
        if ($text !== '' && (strpos($text, 'sudah terdaftar di') !== false || strpos($text, 'nisn') !== false || strpos($text, 'nik') !== false)) {
            return [
                'kode' => 'terdaftar_lembaga_lain',
                'label' => 'Terdaftar di Lembaga Lain',
                'badge' => 'danger'
            ];
        }
        return [
            'kode' => 'lainnya',
            'label' => 'Error Validasi / Lainnya',
            'badge' => 'secondary'
        ];
    }

    /**
     * Ambil ringkasan KPI Dashboard EMIS untuk periode Tahun Ajaran & Semester tertentu.
     */
    public function getEmisDashboardSummary(string $IdTahunAjaran, string $Semester, ?string $IdTpq = null, $IdKelas = null): array
    {
        $row = $this->buildEmisDashboardBaseQuery($IdTahunAjaran, $Semester, $IdTpq, $IdKelas)
            ->select("
                COUNT(DISTINCT tbl_santri_baru.IdSantri) AS total_santri,
                COUNT(DISTINCT CASE WHEN se.StatusEmis = 'selesai' THEN tbl_santri_baru.IdSantri END) AS emis_selesai,
                COUNT(DISTINCT CASE WHEN se.StatusEmis = 'konflik' THEN tbl_santri_baru.IdSantri END) AS emis_konflik,
                COUNT(DISTINCT CASE WHEN COALESCE(se.StatusEmis, 'belum') = 'belum' THEN tbl_santri_baru.IdSantri END) AS emis_belum,
                COUNT(DISTINCT CASE WHEN se.StatusAlamatEmis = 'selesai' THEN tbl_santri_baru.IdSantri END) AS alamat_selesai,
                COUNT(DISTINCT CASE WHEN COALESCE(se.StatusAlamatEmis, 'belum') = 'belum' THEN tbl_santri_baru.IdSantri END) AS alamat_belum,
                MAX(se.TglUpdateEmis) AS last_update_emis,
                MAX(se.TglUpdateAlamatEmis) AS last_update_alamat
            ", false)
            ->get()
            ->getRowArray();

        $total = (int) ($row['total_santri'] ?? 0);
        $selesai = (int) ($row['emis_selesai'] ?? 0);
        $konflik = (int) ($row['emis_konflik'] ?? 0);
        $belum = (int) ($row['emis_belum'] ?? 0);
        $alamatSelesai = (int) ($row['alamat_selesai'] ?? 0);
        $alamatBelum = (int) ($row['alamat_belum'] ?? 0);

        $latestOperatorRow = $this->buildEmisDashboardBaseQuery($IdTahunAjaran, $Semester, $IdTpq, $IdKelas)
            ->select('se.UpdatedBy, se.TglUpdateEmis, se.TglUpdateAlamatEmis')
            ->where('se.UpdatedBy IS NOT NULL', null, false)
            ->orderBy('GREATEST(COALESCE(se.TglUpdateEmis, "1970-01-01"), COALESCE(se.TglUpdateAlamatEmis, "1970-01-01"))', 'DESC', false)
            ->limit(1)
            ->get()
            ->getRowArray();

        $pct = static function (int $num, int $den): float {
            return $den > 0 ? round(($num / $den) * 100, 1) : 0.0;
        };

        return [
            'total_santri' => $total,
            'emis_selesai' => $selesai,
            'emis_belum' => $belum,
            'emis_konflik' => $konflik,
            'alamat_selesai' => $alamatSelesai,
            'alamat_belum' => $alamatBelum,
            'pct_emis_selesai' => $pct($selesai, $total),
            'pct_emis_belum' => $pct($belum, $total),
            'pct_emis_konflik' => $pct($konflik, $total),
            'pct_alamat_selesai' => $pct($alamatSelesai, $total),
            'last_update_emis' => $row['last_update_emis'] ?? null,
            'last_update_alamat' => $row['last_update_alamat'] ?? null,
            'last_updated_by' => $latestOperatorRow['UpdatedBy'] ?? null,
        ];
    }

    /**
     * Rekap progres EMIS per Lembaga/TPQ (untuk hak akses Admin).
     */
    public function getEmisRekapPerTpq(string $IdTahunAjaran, string $Semester, ?string $IdTpq = null): array
    {
        $rows = $this->buildEmisDashboardBaseQuery($IdTahunAjaran, $Semester, $IdTpq)
            ->select("
                ks.IdTpq,
                COALESCE(tbl_tpq.NamaTpq, ks.IdTpq) AS NamaTpq,
                COUNT(DISTINCT tbl_santri_baru.IdSantri) AS total_santri,
                COUNT(DISTINCT CASE WHEN se.StatusEmis = 'selesai' THEN tbl_santri_baru.IdSantri END) AS emis_selesai,
                COUNT(DISTINCT CASE WHEN COALESCE(se.StatusEmis, 'belum') = 'belum' THEN tbl_santri_baru.IdSantri END) AS emis_belum,
                COUNT(DISTINCT CASE WHEN se.StatusEmis = 'konflik' THEN tbl_santri_baru.IdSantri END) AS emis_konflik,
                COUNT(DISTINCT CASE WHEN se.StatusAlamatEmis = 'selesai' THEN tbl_santri_baru.IdSantri END) AS alamat_selesai,
                MAX(se.TglUpdateEmis) AS last_update_emis
            ", false)
            ->groupBy(['ks.IdTpq', 'tbl_tpq.NamaTpq'])
            ->orderBy('emis_konflik', 'DESC')
            ->orderBy('emis_belum', 'DESC')
            ->orderBy('NamaTpq', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$r) {
            $total = (int) ($r['total_santri'] ?? 0);
            $selesai = (int) ($r['emis_selesai'] ?? 0);
            $r['pct_selesai'] = $total > 0 ? round(($selesai / $total) * 100, 1) : 0.0;
        }
        return $rows;
    }

    /**
     * Rekap progres EMIS per Kelas (untuk Operator/Kepala TPQ atau filter 1 TPQ).
     */
    public function getEmisRekapPerKelas(string $IdTahunAjaran, string $Semester, ?string $IdTpq = null): array
    {
        $rows = $this->buildEmisDashboardBaseQuery($IdTahunAjaran, $Semester, $IdTpq)
            ->select("
                ks.IdKelas,
                COALESCE(tbl_kelas.NamaKelas, '-') AS NamaKelas,
                COUNT(DISTINCT tbl_santri_baru.IdSantri) AS total_santri,
                COUNT(DISTINCT CASE WHEN se.StatusEmis = 'selesai' THEN tbl_santri_baru.IdSantri END) AS emis_selesai,
                COUNT(DISTINCT CASE WHEN COALESCE(se.StatusEmis, 'belum') = 'belum' THEN tbl_santri_baru.IdSantri END) AS emis_belum,
                COUNT(DISTINCT CASE WHEN se.StatusEmis = 'konflik' THEN tbl_santri_baru.IdSantri END) AS emis_konflik,
                COUNT(DISTINCT CASE WHEN se.StatusAlamatEmis = 'selesai' THEN tbl_santri_baru.IdSantri END) AS alamat_selesai
            ", false)
            ->groupBy(['ks.IdKelas', 'tbl_kelas.NamaKelas'])
            ->orderBy('ks.IdKelas', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$r) {
            $total = (int) ($r['total_santri'] ?? 0);
            $selesai = (int) ($r['emis_selesai'] ?? 0);
            $r['pct_selesai'] = $total > 0 ? round(($selesai / $total) * 100, 1) : 0.0;
        }
        return $rows;
    }

    /**
     * Daftar santri berstatus konflik di tbl_santri_emis untuk kebutuhan laporan & eskalasi.
     */
    public function getEmisKonflikList(string $IdTahunAjaran, string $Semester, ?string $IdTpq = null, $IdKelas = null, ?string $kategori = null): array
    {
        $rows = $this->buildEmisDashboardBaseQuery($IdTahunAjaran, $Semester, $IdTpq, $IdKelas)
            ->select('
                tbl_santri_baru.IdSantri,
                tbl_santri_baru.NamaSantri,
                tbl_santri_baru.NikSantri,
                tbl_santri_baru.NISN,
                tbl_santri_baru.TempatLahirSantri,
                tbl_santri_baru.TanggalLahirSantri,
                ks.IdTpq,
                COALESCE(tbl_tpq.NamaTpq, ks.IdTpq) AS NamaTpq,
                ks.IdKelas,
                COALESCE(tbl_kelas.NamaKelas, "-") AS NamaKelas,
                se.IdTahunAjaran,
                se.Semester,
                se.StatusEmis,
                se.Catatan,
                se.TglUpdateEmis,
                se.UpdatedBy
            ')
            ->where('se.StatusEmis', 'konflik')
            ->orderBy('se.TglUpdateEmis', 'DESC')
            ->orderBy('tbl_tpq.NamaTpq', 'ASC')
            ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $meta = $this->classifyEmisConflict($row['Catatan'] ?? '');
            if (!empty($kategori) && $meta['kode'] !== $kategori) {
                continue;
            }
            $row['KategoriKode'] = $meta['kode'];
            $row['KategoriLabel'] = $meta['label'];
            $row['KategoriBadge'] = $meta['badge'];
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Daftar Tahun Ajaran yang tersedia untuk filter Dashboard EMIS.
     */
    public function getAvailableTahunAjaranEmis(): array
    {
        $helpFunc = new \App\Models\HelpFunctionModel();
        $currentTa = $helpFunc->getTahunAjaranSaatIni();

        $ksRows = $this->db->table('tbl_kelas_santri')
            ->select('IdTahunAjaran')
            ->distinct()
            ->where('IdTahunAjaran IS NOT NULL', null, false)
            ->where('IdTahunAjaran !=', '')
            ->get()
            ->getResultArray();

        $emisRows = $this->db->table('tbl_santri_emis')
            ->select('IdTahunAjaran')
            ->distinct()
            ->where('IdTahunAjaran IS NOT NULL', null, false)
            ->where('IdTahunAjaran !=', '')
            ->get()
            ->getResultArray();

        $list = [$currentTa];
        foreach (array_merge($ksRows, $emisRows) as $r) {
            $ta = trim((string) ($r['IdTahunAjaran'] ?? ''));
            if ($ta !== '' && !in_array($ta, $list, true)) {
                $list[] = $ta;
            }
        }
        rsort($list);
        return $list;
    }
}
