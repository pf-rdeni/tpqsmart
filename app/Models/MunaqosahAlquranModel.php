<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahAlquranModel extends Model
{
    protected $table = 'tbl_munaqosah_alquran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'IdMateri',
        'NamaSurah',
        'WebLinkAyat',
        'Status',
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
        'IdMateri' => 'required|max_length[50]',
        'NamaSurah' => 'required|max_length[255]',
        'WebLinkAyat' => 'permit_empty|max_length[500]',
        'Status' => 'required|in_list[Aktif,Tidak Aktif]'
    ];

    protected $validationMessages = [
        'IdMateri' => [
            'required' => 'Id Materi harus diisi',
            'max_length' => 'Id Materi maksimal 50 karakter'
        ],
        'NamaSurah' => [
            'required' => 'Nama Surah harus diisi',
            'max_length' => 'Nama Surah maksimal 255 karakter'
        ],
        'WebLinkAyat' => [
            'max_length' => 'Web Link Ayat maksimal 500 karakter'
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
     * Ambil data surah alquran yang aktif
     */
    public function getSurahAktif()
    {
        return $this->where('Status', 'Aktif')
                   ->orderBy('NamaSurah', 'ASC')
                   ->findAll();
    }

    /**
     * Ambil data surah alquran berdasarkan Status
     */
    public function getSurahByStatus($Status = 'Aktif')
    {
        return $this->where('Status', $Status)
                   ->orderBy('NamaSurah', 'ASC')
                   ->findAll();
    }

    /**
     * Ambil data surah alquran untuk registrasi munaqosah
     * Format khusus untuk kompatibilitas dengan sistem materi munaqosah
     */
    public function getSurahForMunaqosah()
    {
        $surahData = $this->getSurahAktif();
        $formattedData = [];
        
        foreach ($surahData as $surah) {
            $formattedData[] = [
                'IdMateri' => $surah['IdMateri'], 
                'NamaMateri' => $surah['NamaSurah'],
                'IdGrupMateriUjian' => 'QURAN', // Grup khusus untuk alquran
                'KategoriMateri' => 'QURAN',
                'WebLinkAyat' => $surah['WebLinkAyat'],
                'Status' => $surah['Status']
            ];
        }
        
        return $formattedData;
    }

    /**
     * Cek apakah surah dengan nama tertentu sudah ada
     */
    public function checkSurahExists($namaSurah, $excludeId = null)
    {
        $builder = $this->where('NamaSurah', $namaSurah);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->first();
    }

    /**
     * Ambil data surah untuk dropdown/select
     */
    public function getSurahOptions()
    {
        $surahData = $this->getSurahAktif();
        $options = [];
        
        foreach ($surahData as $surah) {
            $options[$surah['IdMateri']] = $surah['NamaSurah'];
        }
        
        return $options;
    }
}
