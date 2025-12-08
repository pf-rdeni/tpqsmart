<?php

namespace App\Models;

use CodeIgniter\Model;
use Myth\Auth\Password;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fullname', 'username', 'email', 'password_hash', 'nik', 'active', 'user_image'];

    public function getUser($id = false)
    {
        if ($id === false) {
            return $this->findAll();
        } else {
            return $this->find($id);
        }
    }

    public function store($data)
    {
        $this->save($data);
        return $this->getInsertID();
    }

    public function updateUser($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }

    public function deleteUser($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

    public function getAllUserData()
    {
        // Ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        // Query untuk data santri jika IdTpq ada
        if ($IdTpq) {
            $userDataSantri = $this->select('
                users.id,
                users.active,
                users.username,
                users.password_hash,
                tbl_santri_baru.NamaSantri as nama,
                tbl_tpq.NamaTpq as namaTpq,
                tbl_tpq.KelurahanDesa as kelurahanDesa,
                "Santri" as kategori
            ')
            ->join('tbl_santri_baru', 'users.nik = tbl_santri_baru.NikSantri', 'inner')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'inner')
                ->where('tbl_tpq.IdTpq', $IdTpq)
            ->findAll();
        } else {
            $userDataSantri = $this->select('
                users.id,
                users.active,
                users.username,
                users.password_hash,
                tbl_santri_baru.NamaSantri as nama,
                tbl_tpq.NamaTpq as namaTpq,
                tbl_tpq.KelurahanDesa as kelurahanDesa,
                "Santri" as kategori
            ')
                ->join('tbl_santri_baru', 'users.nik = tbl_santri_baru.NikSantri', 'inner')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'inner')
                ->findAll();
        }

        // Query untuk data guru jika IdTpq ada
        if ($IdTpq) {
            $userDataGuru = $this->select('
                users.id,
                users.active,
                users.username,
                users.password_hash,
                tbl_guru.Nama as nama,
                tbl_tpq.NamaTpq as namaTpq,
                tbl_tpq.KelurahanDesa as kelurahanDesa,
                "Guru" as kategori
            ')
            ->join(
                'tbl_guru',
                'users.nik = tbl_guru.IdGuru',
                'inner'
            )
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_guru.IdTpq', 'inner')
            ->where('tbl_tpq.IdTpq', $IdTpq)
                ->findAll();
        } else {
            $userDataGuru = $this->select('
                users.id,
                users.active,
                users.username,
                users.password_hash,
                tbl_guru.Nama as nama,
                tbl_tpq.NamaTpq as namaTpq,
                tbl_tpq.KelurahanDesa as kelurahanDesa,
                "Guru" as kategori
            ')
            ->join(
                'tbl_guru',
                'users.nik = tbl_guru.IdGuru',
                'inner'
            )
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_guru.IdTpq', 'inner')
                ->findAll();
        }

        $dataMerge = array_merge($userDataSantri, $userDataGuru);

        return $dataMerge;
    }

    /**
     * Mendapatkan data user Guru
     */
    public function getUserDataGuru($IdTpq = null)
    {
        $builder = $this->select('
                users.id,
                users.active,
                users.username,
                users.password_hash,
                tbl_guru.Nama as nama,
                tbl_tpq.NamaTpq as namaTpq,
                tbl_tpq.KelurahanDesa as kelurahanDesa,
                "Guru" as kategori
            ')
            ->join('tbl_guru', 'users.nik = tbl_guru.IdGuru', 'inner')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_guru.IdTpq', 'inner');

        if ($IdTpq) {
            $builder->where('tbl_tpq.IdTpq', $IdTpq);
        }

        return $builder->findAll();
    }

    /**
     * Mendapatkan data user Santri per kelas dari tbl_kelas_santri
     */
    public function getUserDataSantriPerKelas($IdTpq = null, $IdTahunAjaran = null)
    {
        $builder = $this->db->table('users');
        $builder->select('
            users.id,
            users.active,
            users.username,
            users.password_hash,
            tbl_santri_baru.NamaSantri as nama,
            tbl_tpq.NamaTpq as namaTpq,
            tbl_tpq.KelurahanDesa as kelurahanDesa,
            k.IdKelas,
            k.NamaKelas,
            "Santri" as kategori
        ')
        ->join('tbl_santri_baru', 'users.nik = tbl_santri_baru.NikSantri', 'inner')
        ->join('tbl_kelas_santri ks', 'ks.IdSantri = tbl_santri_baru.IdSantri', 'inner')
        ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'inner')
        ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'inner')
        ->where('tbl_santri_baru.Active', 1)
        ->where('ks.Status', 1);

        if ($IdTpq) {
            $builder->where('tbl_tpq.IdTpq', $IdTpq);
        }

        if ($IdTahunAjaran) {
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }

        $results = $builder->orderBy('k.NamaKelas', 'ASC')
                          ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                          ->get()
                          ->getResultArray();

        // Group by kelas
        $grouped = [];
        foreach ($results as $row) {
            $kelasKey = $row['IdKelas'];
            if (!isset($grouped[$kelasKey])) {
                $grouped[$kelasKey] = [
                    'IdKelas' => $row['IdKelas'],
                    'NamaKelas' => $row['NamaKelas'],
                    'users' => []
                ];
            }
            $grouped[$kelasKey]['users'][] = $row;
        }

        return $grouped;
    }

    /**
     * Mendapatkan daftar kelas yang memiliki santri dengan user
     */
    public function getKelasWithSantri($IdTpq = null, $IdTahunAjaran = null)
    {
        $builder = $this->db->table('tbl_kelas k');
        $builder->select('
            k.IdKelas,
            k.NamaKelas
        ')
        ->distinct()
        ->join('tbl_kelas_santri ks', 'ks.IdKelas = k.IdKelas', 'inner')
        ->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri', 'inner')
        ->join('users u', 'u.nik = s.NikSantri', 'inner')
        ->where('s.Active', 1)
        ->where('ks.Status', 1);

        if ($IdTpq) {
            $builder->where('s.IdTpq', $IdTpq);
        }

        if ($IdTahunAjaran) {
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }

        return $builder->orderBy('k.NamaKelas', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Mendapatkan data santri untuk dropdown (create user)
     */
    public function getSantriForUserCreation($IdTpq = null, $IdTahunAjaran = null)
    {
        $builder = $this->db->table('tbl_santri_baru s');
        $builder->select('
            s.IdSantri,
            s.NikSantri,
            s.NamaSantri,
            k.IdKelas,
            k.NamaKelas
        ')
        ->join('tbl_kelas_santri ks', 'ks.IdSantri = s.IdSantri', 'inner')
        ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'inner')
        ->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'inner')
        ->join('users u', 'u.nik = s.NikSantri', 'left') // Left join untuk cek apakah sudah punya user
        ->where('s.Active', 1)
        ->where('ks.Status', 1)
        ->where('u.id IS NULL'); // Hanya santri yang belum punya user account

        if ($IdTpq) {
            $builder->where('s.IdTpq', $IdTpq);
        }

        if ($IdTahunAjaran) {
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }

        return $builder->orderBy('k.NamaKelas', 'ASC')
                      ->orderBy('s.NamaSantri', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Mendapatkan daftar kelas yang memiliki santri tanpa user account
     */
    public function getKelasForSantriUserCreation($IdTpq = null, $IdTahunAjaran = null)
    {
        $builder = $this->db->table('tbl_kelas k');
        $builder->select('
            k.IdKelas,
            k.NamaKelas
        ')
        ->join('tbl_kelas_santri ks', 'ks.IdKelas = k.IdKelas', 'inner')
        ->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri', 'inner')
        ->join('users u', 'u.nik = s.NikSantri', 'left') // Left join untuk cek apakah sudah punya user
        ->where('s.Active', 1)
        ->where('ks.Status', 1)
        ->where('u.id IS NULL'); // Hanya kelas yang memiliki santri tanpa user account

        if ($IdTpq) {
            $builder->where('s.IdTpq', $IdTpq);
        }

        if ($IdTahunAjaran) {
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }

        return $builder->distinct()
                      ->orderBy('k.NamaKelas', 'ASC')
                      ->get()
                      ->getResultArray();
    }
}
