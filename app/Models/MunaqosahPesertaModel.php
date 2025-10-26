<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahPesertaModel extends Model
{
    protected $table = 'tbl_munaqosah_peserta';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdSantri',
        'IdTpq',
        'IdTahunAjaran'
    ];

    protected $validationRules = [
        'IdSantri' => 'required|max_length[50]',
        'IdTpq' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]'
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
        'IdTahunAjaran' => [
            'required' => 'ID Tahun Ajaran harus diisi',
            'max_length' => 'ID Tahun Ajaran maksimal 50 karakter'
        ]
    ];

    public function getPesertaWithRelations($idTpq = null)
    {
        $builder = $this->db->table($this->table . ' pm');
        $builder->select('pm.*, s.*, t.*');
        $builder->join('tbl_santri_baru s', 's.IdSantri = pm.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = pm.IdTpq', 'left');

        if ($idTpq) {
            $builder->where('pm.IdTpq', $idTpq);
        }

        $builder->orderBy('pm.IdTpq', 'ASC');
        $builder->orderBy('s.NamaSantri', 'ASC');

        return $builder->get()->getResult();
    }

    public function getPesertaByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getPesertaByTpq($idTpq, $idTahunAjaran)
    {
        return $this->where('IdTpq', $idTpq)
                   ->where('IdTahunAjaran', $idTahunAjaran)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function isPesertaExists($idSantri, $idTahunAjaran)
    {
        return $this->where('IdSantri', $idSantri)
                   ->where('IdTahunAjaran', $idTahunAjaran)
                   ->countAllResults() > 0;
    }
}
