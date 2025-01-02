<?php

namespace App\Models;

use CodeIgniter\Model;

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
            return $this->getWhere(['id' => $id]);
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
        $userDataSantri = $this->select('users.id, users.active, users.username, tbl_santri_baru.NamaSantri as Nama, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa, "Santri" as kategori')
        ->join('tbl_santri_baru', 'users.nik = tbl_santri_baru.NikSantri', 'inner')
        ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'inner')
        ->findAll();

        $userDataGuru = $this->select('users.id, users.active, users.username, tbl_guru.Nama as Nama, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa, "Guru" as kategori')
        ->join('tbl_guru', 'users.nik = tbl_guru.IdGuru', 'inner')
        ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_guru.IdTpq', 'inner')
        ->findAll();

        return array_merge($userDataSantri, $userDataGuru);
    }
}
