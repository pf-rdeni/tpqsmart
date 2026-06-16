<?php

namespace App\Controllers\Backend\Luckydraw;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\Backend\Luckydraw\LuckydrawKegiatanModel;
use App\Models\Backend\Luckydraw\LuckydrawUserKegiatanModel;
use Myth\Auth\Password;

class LuckydrawPanitia extends BaseController
{
    protected $userModel;
    protected $kegiatanModel;
    protected $userKegiatanModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->kegiatanModel = new LuckydrawKegiatanModel();
        $this->userKegiatanModel = new LuckydrawUserKegiatanModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Get users in group 10 (Pemenang) and 11 (Verifikasi)
        $panitia = $this->db->table('users')
            ->select('users.id, users.username, users.fullname, users.email, auth_groups.name as group_name, auth_groups.id as group_id')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id')
            ->whereIn('auth_groups.id', [10, 11])
            ->get()
            ->getResult();

        // Get assigned kegiatan for each panitia
        foreach ($panitia as &$p) {
            $assigned = $this->userKegiatanModel->getKegiatanByUser($p->id);
            $kegiatanNames = array_map(function($k) { return $k->nama_kegiatan; }, $assigned);
            $p->assigned_kegiatan = implode(', ', $kegiatanNames);
        }

        $data = [
            'page_title'   => 'Manajemen Panitia Lucky Draw',
            'panitia' => $panitia
        ];

        return view('backend/luckydraw/panitia/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title'    => 'Tambah Panitia Baru',
            'kegiatan' => $this->kegiatanModel->findAll()
        ];

        return view('backend/luckydraw/panitia/form', $data);
    }

    public function store()
    {
        $rules = [
            'fullname'    => 'required',
            'username'    => 'required|is_unique[users.username]',
            'password'    => 'required|min_length[6]',
            'group_id'    => 'required|in_list[10,11]',
            'id_kegiatan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username'      => $this->request->getPost('username'),
            'fullname'      => $this->request->getPost('fullname'),
            'email'         => $this->request->getPost('username') . '@luckydraw.local',
            'password_hash' => Password::hash($this->request->getPost('password')),
            'active'        => 1
        ];

        $userId = $this->userModel->insert($userData);

        if ($userId) {
            // Assign group
            $this->db->table('auth_groups_users')->insert([
                'group_id' => $this->request->getPost('group_id'),
                'user_id'  => $userId
            ]);

            // Assign kegiatan
            $kegiatanIds = $this->request->getPost('id_kegiatan');
            foreach ($kegiatanIds as $kId) {
                $this->userKegiatanModel->insert([
                    'user_id'     => $userId,
                    'id_kegiatan' => $kId
                ]);
            }

            return redirect()->to('backend/luckydraw/panitia')->with('message', 'Panitia berhasil ditambahkan');
        }

        return redirect()->back()->withInput()->with('errors', ['Gagal menambahkan pengguna']);
    }

    public function edit($id)
    {
        $userArray = $this->userModel->find($id);
        if (!$userArray) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $user = (object) $userArray;

        $group = $this->db->table('auth_groups_users')->where('user_id', $id)->get()->getRow();
        $user->group_id = $group ? $group->group_id : null;

        $userKegiatan = $this->userKegiatanModel->where('user_id', $id)->findAll();
        $user->assigned_kegiatan = array_map(function($k) { return $k->id_kegiatan; }, $userKegiatan);

        $data = [
            'page_title'    => 'Edit Panitia',
            'panitia'  => $user,
            'kegiatan' => $this->kegiatanModel->findAll()
        ];

        return view('backend/luckydraw/panitia/form', $data);
    }

    public function update($id)
    {
        $rules = [
            'fullname'    => 'required',
            'username'    => "required|is_unique[users.username,id,{$id}]",
            'group_id'    => 'required|in_list[10,11]',
            'id_kegiatan' => 'required'
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'fullname' => $this->request->getPost('fullname'),
        ];

        if ($this->request->getPost('password')) {
            $userData['password_hash'] = Password::hash($this->request->getPost('password'));
        }

        $this->userModel->update($id, $userData);

        // Update group
        $this->db->table('auth_groups_users')->where('user_id', $id)->delete();
        $this->db->table('auth_groups_users')->insert([
            'group_id' => $this->request->getPost('group_id'),
            'user_id'  => $id
        ]);

        // Update kegiatan
        $this->userKegiatanModel->where('user_id', $id)->delete();
        $kegiatanIds = $this->request->getPost('id_kegiatan');
        foreach ($kegiatanIds as $kId) {
            $this->userKegiatanModel->insert([
                'user_id'     => $id,
                'id_kegiatan' => $kId
            ]);
        }

        return redirect()->to('backend/luckydraw/panitia')->with('message', 'Data panitia berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->userKegiatanModel->where('user_id', $id)->delete();
        $this->db->table('auth_groups_users')->where('user_id', $id)->delete();
        $this->userModel->delete($id);
        
        return redirect()->to('backend/luckydraw/panitia')->with('message', 'Panitia berhasil dihapus');
    }
}
