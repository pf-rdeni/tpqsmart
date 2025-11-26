<?php

namespace App\Models;

use CodeIgniter\Model;

class SantriModel extends Model
{
    public $db;
    public function init()
    {
        $db = db_connect();
    }

    protected $table      = 'tbl_santri_baru';
    protected $allowedFields = [
        'Active',
        'IdKelas',
        'Status'
    ];

    public function GetData($id = false)
    {
        if ($id) {
            return $this->where(['IdTpq' => $id])->find();
        } else {
            return $this->findAll();
        }
    }


    public function GetDataSantriPerKelas($IdTpq, $IdTahunAjaran = 0, $IdKelas = 0, $IdGuru = null)
    {
        $builder = $this->db->table('tbl_kelas_santri ks');
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
            w.IdJabatan
        ');

        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_tpq t', 'ks.IdTpq = t.IdTpq', 'left');
        $builder->join('tbl_guru_kelas w', 'w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq', 'left');
        $builder->join('tbl_guru g', 'w.IdGuru = g.IdGuru', 'left');

        // Menambahkan filter Active=1
        $builder->where('s.Active', 1);
        
        // Tambahkan filter Status = 1 untuk tbl_kelas_santri (hanya record aktif)
        $builder->where('ks.Status', 1);

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
        $builder->groupBy([
            's.IdSantri',  // Group by IdSantri sebagai kolom utama
            'ks.Id',
            'ks.IdTahunAjaran',
            'k.IdKelas',
            'k.NamaKelas',
            's.NamaSantri',
            's.JenisKelamin',
            's.PhotoProfil',
            't.IdTpq',
            't.NamaTpq',
            't.Alamat'
        ]);

        $builder->orderBy('k.NamaKelas', 'ASC');
        $builder->orderBy('s.NamaSantri', 'ASC');

        return $builder->get()->getResultObject();
    }

    // GetTotalSantri
    public function GetTotalSantri($IdTpq, $IdTahunAjaran = 0, $IdKelas = 0, $IdGuru = null)
    {
        $builder = $this->db->table('tbl_kelas_santri ks');
        $builder->select('COUNT(DISTINCT s.IdSantri) AS TotalSantri');

        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri AND s.Active = 1', 'inner');
        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'inner');
        $builder->join('tbl_tpq t', 'ks.IdTpq = t.IdTpq', 'inner');
        $builder->join('tbl_guru_kelas w', 'w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq', 'left');
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
}