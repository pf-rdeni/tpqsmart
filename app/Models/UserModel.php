<?php

namespace App\Models;

use CodeIgniter\Model;
use Myth\Auth\Password;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fullname', 'username', 'email', 'password_hash', 'nik', 'active'];

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
}
