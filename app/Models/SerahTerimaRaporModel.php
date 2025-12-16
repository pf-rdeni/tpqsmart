<?php

namespace App\Models;

use CodeIgniter\Model;

class SerahTerimaRaporModel extends Model
{
    protected $table = 'tbl_serah_terima_rapor';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdSantri',
        'IdTpq',
        'IdKelas',
        'idTahunAjaran',
        'Semester',
        'TanggalTransaksi',
        'Transaksi',
        'IdGuru',
        'NamaWaliSantri',
        'FotoBukti',
        'HasKey',
        'Status'
    ];

    protected $validationRules = [
        'IdSantri' => 'required|max_length[50]',
        'IdTpq' => 'required|max_length[50]',
        'IdKelas' => 'required|max_length[50]',
        'idTahunAjaran' => 'required|max_length[50]',
        'Semester' => 'required|in_list[Ganjil,Genap]',
        'TanggalTransaksi' => 'required|valid_date',
        'Transaksi' => 'required|in_list[Serah,Terima]',
        'IdGuru' => 'required|max_length[50]',
        'NamaWaliSantri' => 'required|max_length[255]',
        'HasKey' => 'permit_empty|max_length[100]',
        'Status' => 'required|in_list[Belum Diserahkan,Sudah Diserahkan,Sudah Dikembalikan]'
    ];

    protected $validationMessages = [
        'IdSantri' => [
            'required' => 'ID Santri harus diisi',
            'max_length' => 'ID Santri maksimal 50 karakter'
        ],
        'IdTpq' => [
            'required' => 'ID TPQ harus diisi',
            'max_length' => 'ID TPQ maksimal 50 karakter'
        ],
        'IdKelas' => [
            'required' => 'ID Kelas harus diisi',
            'max_length' => 'ID Kelas maksimal 50 karakter'
        ],
        'idTahunAjaran' => [
            'required' => 'ID Tahun Ajaran harus diisi',
            'max_length' => 'ID Tahun Ajaran maksimal 50 karakter'
        ],
        'Semester' => [
            'required' => 'Semester harus diisi',
            'in_list' => 'Semester harus Ganjil atau Genap'
        ],
        'TanggalTransaksi' => [
            'required' => 'Tanggal Transaksi harus diisi',
            'valid_date' => 'Tanggal Transaksi tidak valid'
        ],
        'Transaksi' => [
            'required' => 'Transaksi harus diisi',
            'in_list' => 'Transaksi harus Serah atau Terima'
        ],
        'IdGuru' => [
            'required' => 'ID Guru harus diisi',
            'max_length' => 'ID Guru maksimal 50 karakter'
        ],
        'NamaWaliSantri' => [
            'required' => 'Nama Wali Santri harus diisi',
            'max_length' => 'Nama Wali Santri maksimal 255 karakter'
        ],
        'HasKey' => [
            'max_length' => 'HasKey maksimal 100 karakter'
        ],
        'Status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus Belum Diserahkan, Sudah Diserahkan, atau Sudah Dikembalikan'
        ]
    ];

    /**
     * Ambil data berdasarkan HasKey
     */
    public function getByHasKey($hasKey)
    {
        return $this->where('HasKey', $hasKey)->first();
    }

    /**
     * Ambil data berdasarkan IdSantri, Semester, dan Tahun Ajaran
     * Dengan join ke tabel guru untuk mendapatkan nama guru
     */
    public function getBySantri($idSantri, $idTahunAjaran, $semester)
    {
        $builder = $this->db->table($this->table . ' str');
        $builder->select('str.*, g.Nama as NamaGuru');
        $builder->join('tbl_guru g', 'g.IdGuru = str.IdGuru', 'left');
        $builder->where('str.IdSantri', $idSantri);
        $builder->where('str.idTahunAjaran', $idTahunAjaran);
        $builder->where('str.Semester', $semester);
        $builder->orderBy('str.TanggalTransaksi', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil status terbaru untuk santri tertentu
     */
    public function getLatestStatus($idSantri, $idTahunAjaran, $semester)
    {
        return $this->where('IdSantri', $idSantri)
                   ->where('idTahunAjaran', $idTahunAjaran)
                   ->where('Semester', $semester)
                   ->orderBy('TanggalTransaksi', 'DESC')
                   ->first();
    }

    /**
     * Cek apakah sudah ada transaksi Serah untuk santri tertentu
     */
    public function hasSerahTransaction($idSantri, $idTahunAjaran, $semester)
    {
        return $this->where('IdSantri', $idSantri)
                   ->where('idTahunAjaran', $idTahunAjaran)
                   ->where('Semester', $semester)
                   ->where('Transaksi', 'Serah')
                   ->first() !== null;
    }

    /**
     * Ambil data dengan join ke tabel santri dan kelas
     */
    public function getWithDetails(array $filters = [])
    {
        $builder = $this->db->table($this->table . ' str');
        $builder->select('str.*, s.NamaSantri, k.NamaKelas, g.Nama as NamaGuru, t.NamaTpq');
        
        $builder->join('tbl_santri_baru s', 's.IdSantri = str.IdSantri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = str.IdKelas', 'left');
        $builder->join('tbl_guru g', 'g.IdGuru = str.IdGuru', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = str.IdTpq', 'left');

        if (!empty($filters['IdTpq'])) {
            $builder->where('str.IdTpq', $filters['IdTpq']);
        }

        if (!empty($filters['IdKelas'])) {
            if (is_array($filters['IdKelas'])) {
                $builder->whereIn('str.IdKelas', $filters['IdKelas']);
            } else {
                $builder->where('str.IdKelas', $filters['IdKelas']);
            }
        }

        if (!empty($filters['idTahunAjaran'])) {
            $builder->where('str.idTahunAjaran', $filters['idTahunAjaran']);
        }

        if (!empty($filters['Semester'])) {
            $builder->where('str.Semester', $filters['Semester']);
        }

        if (!empty($filters['Status'])) {
            $builder->where('str.Status', $filters['Status']);
        }

        if (!empty($filters['Transaksi'])) {
            $builder->where('str.Transaksi', $filters['Transaksi']);
        }

        if (!empty($filters['IdSantri'])) {
            $builder->where('str.IdSantri', $filters['IdSantri']);
        }

        $builder->orderBy('str.TanggalTransaksi', 'DESC');
        $builder->orderBy('str.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }
}

