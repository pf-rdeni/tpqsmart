<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahAntrianModel extends Model
{
    protected $table = 'tbl_munaqosah_antrian';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'IdTahunAjaran',
        'IdGrupMateriUjian',
        'TypeUjian',
        'KategoriMateriUjian',
        'Status',
        'RoomId',
        'Keterangan'
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]',
        'IdGrupMateriUjian' => 'permit_empty|max_length[50]',
        'TypeUjian' => 'permit_empty|in_list[pra-munaqosah,munaqosah]',
        'KategoriMateriUjian' => 'permit_empty|max_length[100]',
        'Status' => 'permit_empty|in_list[0,1,2]',
        'RoomId' => 'permit_empty|max_length[20]',
        'Keterangan' => 'permit_empty'
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
        'IdTahunAjaran' => [
            'required' => 'ID Tahun Ajaran harus diisi',
            'max_length' => 'ID Tahun Ajaran maksimal 50 karakter'
        ],
        'IdGrupMateriUjian' => [
            'max_length' => 'ID Grup Materi Ujian maksimal 50 karakter'
        ],
        'TypeUjian' => [
            'in_list' => 'Type ujian harus pra-munaqosah atau munaqosah'
        ],
        'KategoriMateriUjian' => [
            'max_length' => 'Kategori materi ujian maksimal 100 karakter'
        ],
        'Status' => [
            'in_list' => 'Status harus 0 (menunggu), 1 (proses), atau 2 (selesai)'
        ],
        'RoomId' => [
            'max_length' => 'Room ID maksimal 20 karakter'
        ]
    ];

    public function getAntrianByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getAntrianBelumSelesai($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
            ->where('Status', 0)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getAntrianSelesai($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
            ->where('Status', 2)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function updateStatus($id, $status, $roomId = null)
    {
        return $this->update($id, [
            'Status' => $status,
            'RoomId' => $roomId,
        ]);
    }

    public function getQueueWithDetails(array $filters = [])
    {
        $builder = $this->db->table($this->table . ' q');
        $builder->select('q.*, COALESCE(q.IdGrupMateriUjian, r.IdGrupMateriUjian) as IdGrupMateriResolved, COALESCE(q.TypeUjian, r.TypeUjian) as TypeUjianResolved, r.IdSantri, r.IdTpq, r.IdGrupMateriUjian, r.TypeUjian, s.NamaSantri, t.NamaTpq');
        $builder->join('tbl_munaqosah_registrasi_uji r', 'r.NoPeserta = q.NoPeserta AND r.IdTahunAjaran = q.IdTahunAjaran', 'left');
        $builder->join('tbl_santri_baru s', 's.IdSantri = r.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = r.IdTpq', 'left');

        if (!empty($filters['IdTahunAjaran'])) {
            $builder->where('q.IdTahunAjaran', $filters['IdTahunAjaran']);
        }

        if (!empty($filters['IdGrupMateriUjian'])) {
            $builder->groupStart()
                ->where('q.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->orGroupStart()
                ->where('q.IdGrupMateriUjian IS NULL')
                ->where('r.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->groupEnd()
                ->groupEnd();
        }

        if (!empty($filters['TypeUjian'])) {
            $builder->groupStart()
                ->where('q.TypeUjian', $filters['TypeUjian'])
                ->orGroupStart()
                ->where('(q.TypeUjian IS NULL OR q.TypeUjian = \'\')', null, false)
                ->where('r.TypeUjian', $filters['TypeUjian'])
                ->groupEnd()
                ->groupEnd();
        }

        if (isset($filters['Status']) && $filters['Status'] !== '') {
            $builder->where('q.Status', $filters['Status']);
        }

        $builder->orderBy('q.Status', 'ASC');
        $builder->orderBy('q.created_at', 'ASC');
        $builder->groupBy('q.id');

        return $builder->get()->getResultArray();
    }

    public function getStatusCounts(array $filters = [])
    {
        $builder = $this->db->table($this->table . ' q');
        $builder->select('q.Status, COUNT(DISTINCT q.id) as total');
        $builder->join('tbl_munaqosah_registrasi_uji r', 'r.NoPeserta = q.NoPeserta AND r.IdTahunAjaran = q.IdTahunAjaran', 'left');

        if (!empty($filters['IdTahunAjaran'])) {
            $builder->where('q.IdTahunAjaran', $filters['IdTahunAjaran']);
        }

        if (!empty($filters['IdGrupMateriUjian'])) {
            $builder->groupStart()
                ->where('q.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->orGroupStart()
                ->where('q.IdGrupMateriUjian IS NULL')
                ->where('r.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->groupEnd()
                ->groupEnd();
        }

        if (!empty($filters['TypeUjian'])) {
            $builder->groupStart()
                ->where('q.TypeUjian', $filters['TypeUjian'])
                ->orGroupStart()
                ->where('(q.TypeUjian IS NULL OR q.TypeUjian = \'\')', null, false)
                ->where('r.TypeUjian', $filters['TypeUjian'])
                ->groupEnd()
                ->groupEnd();
        }

        $builder->groupBy('q.Status');

        $results = $builder->get()->getResultArray();

        $counts = [
            0 => 0,
            1 => 0,
            2 => 0,
        ];

        foreach ($results as $row) {
            $status = (int) $row['Status'];
            $counts[$status] = (int) $row['total'];
        }

        return $counts;
    }
}
