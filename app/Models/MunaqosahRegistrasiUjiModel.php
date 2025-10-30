<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahRegistrasiUjiModel extends Model
{
    protected $table = 'tbl_munaqosah_registrasi_uji';
    protected $primaryKey = 'IdRegistrasiUji';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'NoPeserta',
        'IdSantri',
        'IdTpq',
        'IdTahunAjaran',
        'IdMateri',
        'IdGrupMateriUjian',
        'KategoriMateriUjian',
        'TypeUjian',
        'HasKey',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'NoPeserta' => 'required|max_length[20]',
        'IdSantri' => 'required|integer',
        'IdTpq' => 'required|integer',
        'IdTahunAjaran' => 'required|integer',
        'IdMateri' => 'required|max_length[50]',
        'IdGrupMateriUjian' => 'required|max_length[50]',
        'KategoriMateriUjian' => 'required|max_length[50]',
        'TypeUjian' => 'required|in_list[munaqosah,pra-munaqosah]',
        'HasKey' => 'permit_empty|max_length[100]'
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'No Peserta harus diisi',
            'max_length' => 'No Peserta maksimal 20 karakter'
        ],
        'IdSantri' => [
            'required' => 'Id Santri harus diisi',
            'integer' => 'Id Santri harus berupa angka'
        ],
        'IdTpq' => [
            'required' => 'Id TPQ harus diisi',
            'integer' => 'Id TPQ harus berupa angka'
        ],
        'IdTahunAjaran' => [
            'required' => 'Id Tahun Ajaran harus diisi',
            'integer' => 'Id Tahun Ajaran harus berupa angka'
        ],
        'IdMateri' => [
            'required' => 'Id Materi harus diisi',
            'max_length' => 'Id Materi maksimal 50 karakter'
        ],
        'IdGrupMateriUjian' => [
            'required' => 'Id Grup Materi Ujian harus diisi',
            'max_length' => 'Id Grup Materi Ujian maksimal 50 karakter'
        ],
        'KategoriMateriUjian' => [
            'required' => 'Kategori Materi Ujian harus diisi',
            'max_length' => 'Kategori Materi Ujian maksimal 50 karakter'
        ],
        'TypeUjian' => [
            'required' => 'Type Ujian harus diisi',
            'in_list' => 'Type Ujian harus munaqosah atau pra-munaqosah'
        ],
        'HasKey' => [
            'max_length' => 'HasKey maksimal 100 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Ambil data registrasi berdasarkan filter
     */
    public function getRegistrasiByNoPeserta($noPeserta, $typeUjian, $idTahunAjaran, $idTpq)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*');
        $builder->where('r.NoPeserta', $noPeserta);
        $builder->where('r.TypeUjian', $typeUjian);
        $builder->where('r.IdTahunAjaran', $idTahunAjaran);
        $builder->where('r.IdTpq', $idTpq !== null ? $idTpq : 0);
        $builder->groupBy('r.NoPeserta');
        $result = $builder->get()->getRowArray();
        return $result ? $result : [];
    }

    /**
     * Ambil materi berdasarkan NoPeserta dengan join ke tabel materi pelajaran
     * dan khusus untuk grup Quran ambil dari tbl_munaqosah_alquran
     */
    public function getMateriByNoPeserta($noPeserta)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.IdMateri, r.IdGrupMateriUjian, r.KategoriMateriUjian, mp.NamaMateri, mp.Kategori as KategoriAsli');
        $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = r.IdMateri', 'left');
        $builder->where('r.NoPeserta', $noPeserta);
        $builder->where('r.IdGrupMateriUjian !=', 'GM001'); // Exclude Quran grup for now

        $materiData = $builder->get()->getResultArray();

        // Ambil data Quran secara terpisah jika ada
        $quranBuilder = $this->db->table($this->table . ' r');
        $quranBuilder->select('r.IdMateri, r.IdGrupMateriUjian, r.KategoriMateriUjian, a.NamaSurah as NamaMateri, a.WebLinkAyat');
        $quranBuilder->join('tbl_munaqosah_alquran a', 'a.IdMateri = r.IdMateri', 'left');
        $quranBuilder->where('r.NoPeserta', $noPeserta);
        $quranBuilder->where('r.IdGrupMateriUjian', 'GM001');

        $quranData = $quranBuilder->get()->getResultArray();

        // Gabungkan data
        return array_merge($materiData, $quranData);
    }

    /**
     * Ambil materi berdasarkan NoPeserta dan IdGrupMateriUjian
     */
    public function getMateriByNoPesertaAndGrup($noPeserta, $idGrupMateriUjian, $typeUjian, $idTahunAjaran)
    {
        if ($idGrupMateriUjian === 'GM001') {
            // Untuk grup Quran, ambil dari tbl_munaqosah_alquran
            $builder = $this->db->table($this->table . ' r');
            $builder->select('r.IdMateri, r.IdGrupMateriUjian, r.KategoriMateriUjian, a.NamaSurah as NamaMateri, a.WebLinkAyat');
            $builder->join('tbl_munaqosah_alquran a', 'a.IdMateri = r.IdMateri', 'left');
            $builder->where('r.NoPeserta', $noPeserta);
            $builder->where('r.IdGrupMateriUjian', $idGrupMateriUjian);
            $builder->where('r.TypeUjian', $typeUjian);
            $builder->where('r.IdTahunAjaran', $idTahunAjaran);
            $builder->groupBy('r.IdMateri');
        } else {
            // Untuk grup lain, ambil dari tbl_materi_pelajaran
            $builder = $this->db->table($this->table . ' r');
            $builder->select('r.IdMateri, r.IdGrupMateriUjian, r.KategoriMateriUjian, mp.NamaMateri, mp.Kategori as KategoriAsli');
            $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = r.IdMateri', 'left');
            $builder->where('r.NoPeserta', $noPeserta);
            $builder->where('r.IdGrupMateriUjian', $idGrupMateriUjian);
            $builder->where('r.TypeUjian', $typeUjian);
            $builder->where('r.IdTahunAjaran', $idTahunAjaran);
            $builder->groupBy('r.IdMateri');
        }
        $result = $builder->get()->getResultArray();
        return $result ? $result : [];
    }
    public function getRegistrasiByFilter($filterTpq = 0, $filterKelas = 0, $typeUjian = 'munaqosah')
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*, s.NamaSantri, s.IdKelas, k.NamaKelas, t.NamaTpq');
        $builder->join('tbl_santri_baru s', 'r.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_kelas k', 's.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_tpq t', 'r.IdTpq = t.IdTpq', 'left');
        $builder->where('r.TypeUjian', $typeUjian);

        if ($filterTpq > 0) {
            $builder->where('r.IdTpq', $filterTpq);
        }

        if ($filterKelas > 0) {
            $builder->where('s.IdKelas', $filterKelas);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Cek apakah santri sudah terdaftar untuk type ujian tertentu
     */
    public function isSantriRegistered($idSantri, $typeUjian)
    {
        return $this->where('IdSantri', $idSantri)
                   ->where('TypeUjian', $typeUjian)
                   ->first();
    }

    /**
     * Ambil data registrasi untuk print kartu ujian
     */
    public function getRegistrasiForPrint($santriIds, $typeUjian)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*, s.NamaSantri, s.IdKelas, k.NamaKelas, t.NamaTpq');
        $builder->join('tbl_santri_baru s', 'r.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_kelas k', 's.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_tpq t', 'r.IdTpq = t.IdTpq', 'left');
        $builder->where('r.TypeUjian', $typeUjian);
        $builder->whereIn('r.IdSantri', $santriIds);
        $builder->orderBy('r.NoPeserta', 'ASC');

        return $builder->get()->getResultArray();
    }


    /**
     * Ambil data registrasi berdasarkan IdSantri dan TypeUjian
     */
    public function getRegistrasiBySantriAndType($idSantri, $typeUjian)
    {
        return $this->where('IdSantri', $idSantri)
                   ->where('TypeUjian', $typeUjian)
                   ->findAll();
    }

    /**
     * Hapus registrasi berdasarkan IdSantri dan TypeUjian
     */
    public function deleteRegistrasiBySantriAndType($idSantri, $typeUjian)
    {
        return $this->where('IdSantri', $idSantri)
                   ->where('TypeUjian', $typeUjian)
                   ->delete();
    }

    /**
     * Ambil data registrasi untuk preview
     */
    public function getRegistrasiPreview($santriIds, $typeUjian)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*, s.NamaSantri, s.IdKelas, k.NamaKelas, t.NamaTpq');
        $builder->join('tbl_santri_baru s', 'r.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_kelas k', 's.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_tpq t', 'r.IdTpq = t.IdTpq', 'left');
        $builder->where('r.TypeUjian', $typeUjian);
        $builder->whereIn('r.IdSantri', $santriIds);
        $builder->orderBy('r.NoPeserta', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Ambil data registrasi berdasarkan HasKey
     */
    public function getRegistrasiByHasKey($hasKey)
    {
        return $this->where('HasKey', $hasKey)->first();
    }

    /**
     * Update HasKey untuk registrasi
     */
    public function updateHasKey($idRegistrasi, $hasKey)
    {
        return $this->update($idRegistrasi, ['HasKey' => $hasKey]);
    }

    /**
     * Ambil data registrasi untuk laporan
     */
    public function getRegistrasiForReport($filterTpq = 0, $filterKelas = 0, $typeUjian = 'munaqosah', $tahunAjaran = null)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*, s.NamaSantri, s.IdKelas, k.NamaKelas, t.NamaTpq');
        $builder->join('tbl_santri_baru s', 'r.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_kelas k', 's.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_tpq t', 'r.IdTpq = t.IdTpq', 'left');
        $builder->where('r.TypeUjian', $typeUjian);

        if ($filterTpq > 0) {
            $builder->where('r.IdTpq', $filterTpq);
        }

        if ($filterKelas > 0) {
            $builder->where('s.IdKelas', $filterKelas);
        }

        if ($tahunAjaran) {
            $builder->where('r.IdTahunAjaran', $tahunAjaran);
        }

        $builder->orderBy('r.NoPeserta', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get total unique participants registered per tahun ajaran, type ujian, and TPQ
     */
    public function getTotalRegisteredParticipants($idTahunAjaran, $typeUjian, $idTpq = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(DISTINCT NoPeserta) as count');
        $builder->where('IdTahunAjaran', $idTahunAjaran);
        $builder->where('TypeUjian', $typeUjian);
        $builder->where('IdTpq', $idTpq);

        return $builder->get()->getRow()->count;
    }
}
