<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahJadwalUjianModel extends Model
{
    protected $table = 'tbl_munaqosah_jadwal_ujian';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'GroupPeserta',
        'Tanggal',
        'Jam',
        'IdTpq',
        'IdTahunAjaran',
        'TypeUjian',
        'Status',
    ];

    protected $validationRules = [
        'GroupPeserta' => 'required|max_length[50]',
        'Tanggal' => 'required',
        'Jam' => 'required|max_length[50]',
        'IdTpq' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]',
        'TypeUjian' => 'permit_empty|max_length[50]',
        'Status' => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'GroupPeserta' => [
            'required' => 'Group Peserta harus diisi',
            'max_length' => 'Group Peserta maksimal 50 karakter'
        ],
        'Tanggal' => [
            'required' => 'Tanggal harus diisi'
        ],
        'Jam' => [
            'required' => 'Jam harus diisi',
            'max_length' => 'Jam maksimal 50 karakter'
        ],
        'IdTpq' => [
            'required' => 'ID TPQ harus diisi',
            'max_length' => 'ID TPQ maksimal 50 karakter'
        ],
        'IdTahunAjaran' => [
            'required' => 'ID Tahun Ajaran harus diisi',
            'max_length' => 'ID Tahun Ajaran maksimal 50 karakter'
        ],
    ];

    /**
     * Get jadwal dengan relasi TPQ
     */
    public function getJadwalWithTpq($idTahunAjaran = null, $typeUjian = null, $idTpq = null)
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.id, j.GroupPeserta, j.Tanggal, j.Jam, j.IdTpq, j.IdTahunAjaran, j.TypeUjian, j.Status, t.NamaTpq, t.KelurahanDesa');
        $builder->join('tbl_tpq t', 't.IdTpq = j.IdTpq', 'left');
        
        if ($idTahunAjaran) {
            $builder->where('j.IdTahunAjaran', $idTahunAjaran);
        }
        
        if ($typeUjian) {
            $builder->where('j.TypeUjian', $typeUjian);
        }
        
        if ($idTpq) {
            $builder->where('j.IdTpq', $idTpq);
        }
        
        $builder->orderBy('j.Tanggal', 'ASC');
        $builder->orderBy('j.Jam', 'ASC');
        $builder->orderBy('j.GroupPeserta', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get jadwal grouped by Group, Tanggal, Jam
     */
    public function getJadwalGrouped($idTahunAjaran = null, $typeUjian = null)
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.id, j.GroupPeserta, j.Tanggal, j.Jam, j.IdTpq, j.IdTahunAjaran, j.TypeUjian, j.Status, t.NamaTpq, t.KelurahanDesa');
        $builder->join('tbl_tpq t', 't.IdTpq = j.IdTpq', 'left');
        
        if ($idTahunAjaran) {
            $builder->where('j.IdTahunAjaran', $idTahunAjaran);
        }
        
        if ($typeUjian) {
            $builder->where('j.TypeUjian', $typeUjian);
        }
        
        $builder->where('j.Status', 'aktif');
        $builder->orderBy('j.Tanggal', 'ASC');
        $builder->orderBy('j.Jam', 'ASC');
        $builder->orderBy('j.GroupPeserta', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get count peserta per TPQ dari tabel peserta
     */
    public function getCountPesertaByTpq($idTpq, $idTahunAjaran, $typeUjian = null)
    {
        $pesertaModel = new \App\Models\MunaqosahPesertaModel();
        return $pesertaModel->getCountPesertaByTpq($idTpq, $idTahunAjaran);
    }

    /**
     * Get GroupPeserta berdasarkan IdTpq, IdTahunAjaran, dan TypeUjian
     * Mengembalikan GroupPeserta pertama yang ditemukan atau 'Group 1' sebagai default
     */
    public function getGroupPesertaByTpq($idTpq, $idTahunAjaran, $typeUjian = null)
    {
        if (empty($idTpq)) {
            return 'Group 1'; // Default jika IdTpq tidak ada
        }

        $builder = $this->db->table($this->table);
        $builder->select('GroupPeserta');
        $builder->where('IdTpq', $idTpq);
        $builder->where('IdTahunAjaran', $idTahunAjaran);

        if (!empty($typeUjian)) {
            $builder->where('TypeUjian', $typeUjian);
        }

        $builder->where('Status', 'aktif');
        $builder->orderBy('GroupPeserta', 'ASC');
        $builder->limit(1);

        $result = $builder->get()->getRowArray();

        return !empty($result['GroupPeserta']) ? $result['GroupPeserta'] : 'Group 1';
    }

    /**
     * Get statistik per GroupPeserta dan IdTpq
     * Menghitung total peserta yang terdaftar berdasarkan GroupPeserta dari jadwal ujian
     * 
     * @param string $idTahunAjaran
     * @param string|null $typeUjian
     * @param int|null $idTpq
     * @return array
     */
    public function getStatistikGroupPeserta($idTahunAjaran, $typeUjian = null, $idTpq = null)
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.GroupPeserta, j.IdTpq, t.NamaTpq');
        $builder->join('tbl_tpq t', 't.IdTpq = j.IdTpq', 'left');

        $builder->where('j.IdTahunAjaran', $idTahunAjaran);
        $builder->where('j.Status', 'aktif');

        if (!empty($typeUjian)) {
            $builder->where('j.TypeUjian', $typeUjian);
        }

        if (!empty($idTpq)) {
            $builder->where('j.IdTpq', $idTpq);
        }

        $builder->groupBy('j.GroupPeserta, j.IdTpq, t.NamaTpq');
        $builder->orderBy('j.GroupPeserta', 'ASC');
        $builder->orderBy('j.IdTpq', 'ASC');

        $jadwalGroups = $builder->get()->getResultArray();

        // Hitung total peserta dari tabel registrasi untuk setiap GroupPeserta
        $result = [];
        foreach ($jadwalGroups as $jadwalGroup) {
            $registrasiBuilder = $this->db->table('tbl_munaqosah_registrasi_uji r');
            $registrasiBuilder->select('COUNT(DISTINCT r.NoPeserta) as total_peserta');
            $registrasiBuilder->where('r.IdTahunAjaran', $idTahunAjaran);
            $registrasiBuilder->where('r.IdTpq', $jadwalGroup['IdTpq']);

            if (!empty($typeUjian)) {
                $registrasiBuilder->where('r.TypeUjian', $typeUjian);
            }

            $totalPeserta = $registrasiBuilder->get()->getRowArray();

            $result[] = [
                'GroupPeserta' => $jadwalGroup['GroupPeserta'],
                'IdTpq' => $jadwalGroup['IdTpq'],
                'NamaTpq' => $jadwalGroup['NamaTpq'],
                'total_peserta' => (int)($totalPeserta['total_peserta'] ?? 0),
            ];
        }

        return $result;
    }
}

