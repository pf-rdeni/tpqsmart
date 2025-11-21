<?php

namespace App\Models;

use CodeIgniter\Model;

class KriteriaCatatanRaporModel extends Model
{
    protected $table = 'tbl_kriteria_catatan_rapor';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'NilaiHuruf',
        'NilaiMin',
        'NilaiMax',
        'Catatan',
        'Status',
        'IdTahunAjaran',
        'IdTpq',
        'IdKelas'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'NilaiHuruf' => 'required|in_list[A,B,C,D]',
        'NilaiMin' => 'permit_empty|decimal',
        'NilaiMax' => 'permit_empty|decimal',
        'Catatan' => 'required|min_length[10]',
        'Status' => 'permit_empty|in_list[Aktif,Tidak Aktif]',
        'IdTahunAjaran' => 'permit_empty|max_length[50]',
        'IdTpq' => 'permit_empty|max_length[50]',
        'IdKelas' => 'permit_empty|max_length[50]'
    ];

    protected $validationMessages = [
        'NilaiHuruf' => [
            'required' => 'Nilai huruf harus diisi',
            'in_list' => 'Nilai huruf harus A, B, C, atau D'
        ],
        'Catatan' => [
            'required' => 'Catatan harus diisi',
            'min_length' => 'Catatan minimal 10 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil catatan berdasarkan nilai huruf
     * 
     * @param string $nilaiHuruf
     * @param string|null $idTahunAjaran
     * @param string|null $idTpq
     * @param string|null $idKelas
     * @return array|null
     */
    public function getCatatanByNilaiHuruf($nilaiHuruf, $idTahunAjaran = null, $idTpq = null, $idKelas = null)
    {
        $builder = $this->where('NilaiHuruf', $nilaiHuruf)
            ->where('Status', 'Aktif');

        // Filter berdasarkan IdTpq (prioritas: spesifik TPQ > default)
        if (!empty($idTpq)) {
            $builder->groupStart()
                ->where('IdTpq', $idTpq)
                ->orWhere('IdTpq', 'default')
                ->groupEnd();
        } else {
            $builder->where('IdTpq', 'default');
        }

        // Filter berdasarkan tahun ajaran
        if (!empty($idTahunAjaran)) {
            $builder->groupStart()
                ->where('IdTahunAjaran', $idTahunAjaran)
                ->orWhere('IdTahunAjaran IS NULL')
                ->groupEnd();
        } else {
            $builder->where('IdTahunAjaran IS NULL');
        }

        // Filter berdasarkan IdKelas (prioritas: spesifik kelas > NULL untuk semua kelas)
        if (!empty($idKelas)) {
            $builder->groupStart()
                ->where('IdKelas', $idKelas)
                ->orWhere('IdKelas IS NULL')
                ->groupEnd();
        } else {
            $builder->where('IdKelas IS NULL');
        }

        // Prioritas: IdKelas spesifik > NULL, IdTpq spesifik > default, IdTahunAjaran spesifik > NULL
        return $builder->orderBy('IdKelas', 'DESC') // NULL akan di bawah jika ada yang spesifik
            ->orderBy('IdTpq', 'DESC') // 'default' akan di bawah jika ada yang spesifik
            ->orderBy('IdTahunAjaran', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Ambil catatan berdasarkan nilai rata-rata
     * 
     * @param float $nilaiRataRata
     * @param string|null $idTahunAjaran
     * @param string|null $idTpq
     * @param string|null $idKelas
     * @return array|null
     */
    public function getCatatanByNilaiRataRata($nilaiRataRata, $idTahunAjaran = null, $idTpq = null, $idKelas = null)
    {
        // Tentukan nilai huruf berdasarkan rata-rata
        $nilaiHuruf = $this->konversiNilaiKeHuruf($nilaiRataRata);
        
        return $this->getCatatanByNilaiHuruf($nilaiHuruf, $idTahunAjaran, $idTpq, $idKelas);
    }

    /**
     * Konversi nilai numerik ke huruf
     * 
     * @param float $nilai
     * @return string
     */
    private function konversiNilaiKeHuruf($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    /**
     * Ambil semua catatan aktif
     * 
     * @param string|null $idTahunAjaran
     * @param string|null $idTpq
     * @param string|null $idKelas
     * @return array
     */
    public function getAllCatatanAktif($idTahunAjaran = null, $idTpq = null, $idKelas = null)
    {
        $builder = $this->where('Status', 'Aktif');

        // Filter berdasarkan IdTpq
        if (!empty($idTpq)) {
            $builder->groupStart()
                ->where('IdTpq', $idTpq)
                ->orWhere('IdTpq', 'default')
                ->groupEnd();
        } else {
            $builder->where('IdTpq', 'default');
        }

        // Filter berdasarkan tahun ajaran
        if (!empty($idTahunAjaran)) {
            $builder->groupStart()
                ->where('IdTahunAjaran', $idTahunAjaran)
                ->orWhere('IdTahunAjaran IS NULL')
                ->groupEnd();
        } else {
            $builder->where('IdTahunAjaran IS NULL');
        }

        // Filter berdasarkan IdKelas
        if (!empty($idKelas)) {
            $builder->groupStart()
                ->where('IdKelas', $idKelas)
                ->orWhere('IdKelas IS NULL')
                ->groupEnd();
        } else {
            $builder->where('IdKelas IS NULL');
        }

        return $builder->orderBy('NilaiHuruf', 'ASC')
            ->orderBy('IdKelas', 'ASC')
            ->orderBy('IdTpq', 'ASC')
            ->orderBy('IdTahunAjaran', 'DESC')
            ->findAll();
    }
}

