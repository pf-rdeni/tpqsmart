<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahNilaiModel extends Model
{
    protected $table = 'tbl_munaqosah_nilai';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'IdSantri',
        'IdTpq',
        'IdTahunAjaran',
        'IdMateri',
        'IdGrupMateriUjian',
        'KategoriMateriUjian',
        'TypeUjian',
        'Nilai',
        'Catatan'
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'IdSantri' => 'required|max_length[50]',
        'IdTpq' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]',
        'IdMateri' => 'required|max_length[50]',
        'IdGrupMateriUjian' => 'required|max_length[50]',
        'KategoriMateriUjian' => 'required|max_length[100]',
        'TypeUjian' => 'required|in_list[munaqosah,pra-munaqosah]',
        'Nilai' => 'required|decimal',
        'Catatan' => 'permit_empty'
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
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
        ],
        'IdMateri' => [
            'required' => 'ID Materi harus diisi',
            'max_length' => 'ID Materi maksimal 50 karakter'
        ],
        'IdGrupMateriUjian' => [
            'required' => 'ID Grup Materi Ujian harus diisi',
            'max_length' => 'ID Grup Materi Ujian maksimal 50 karakter'
        ],
        'KategoriMateriUjian' => [
            'required' => 'Kategori materi ujian harus diisi',
            'max_length' => 'Kategori materi ujian maksimal 100 karakter'
        ],
        'TypeUjian' => [
            'required' => 'Tipe ujian harus diisi',
            'in_list' => 'Tipe ujian harus munaqosah atau pra-munaqosah'
        ],
        'Nilai' => [
            'required' => 'Nilai harus diisi',
            'decimal' => 'Nilai harus berupa angka desimal'
        ]
    ];

    public function getNilaiWithRelations($id = null)
    {
        $builder = $this->db->table($this->table . ' nm');
        $builder->select('nm.*, s.NamaSantri, t.NamaTpq, mp.NamaMateri');
        $builder->join('tbl_santri_baru s', 's.IdSantri = nm.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = nm.IdTpq', 'left');
        $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = nm.IdMateri', 'left');

        if ($id) {
            $builder->where('nm.id', $id);
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    public function getNilaiByPeserta($noPeserta)
    {
        return $this->where('NoPeserta', $noPeserta)->findAll();
    }

    public function getNilaiByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)->findAll();
    }
}
