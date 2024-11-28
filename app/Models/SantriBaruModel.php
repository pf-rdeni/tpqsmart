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
        'StatusMukim', 'StatusTempatTinggal', 'ProvinsiSantri',
        'KabupatenKotaSantri', 'KecamatanSantri', 'KelurahanDesaSantri',
        'RwSantri', 'RtSantri', 'AlamatSantri', 'KodePosSantri',
        'JarakTempuhSantri', 'TransportasiSantri', 'WaktuTempuhSantri',
        'TitikKoordinatSantri'
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
    public function getSantriByNIK($nikSantri)
    {
        return $this->where('NikSantri', $nikSantri)->first();
    }

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
}