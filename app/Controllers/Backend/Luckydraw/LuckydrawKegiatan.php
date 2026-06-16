<?php

namespace App\Controllers\Backend\Luckydraw;

use App\Controllers\BaseController;
use App\Models\Backend\Luckydraw\LuckydrawKegiatanModel;

class LuckydrawKegiatan extends BaseController
{
    protected $kegiatanModel;

    public function __construct()
    {
        $this->kegiatanModel = new LuckydrawKegiatanModel();
    }

    public function index()
    {
        $data = [
            'page_title'    => 'Manajemen Kegiatan Lucky Draw',
            'kegiatan' => $this->kegiatanModel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('backend/luckydraw/kegiatan/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Kegiatan Baru',
        ];

        return view('backend/luckydraw/kegiatan/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama_kegiatan'      => 'required',
            'tanggal_kegiatan'   => 'required|valid_date',
            'kupon_min'          => 'required|numeric',
            'kupon_max'          => 'required|numeric|greater_than_equal_to[' . $this->request->getPost('kupon_min') . ']',
            'status'             => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kegiatanModel->save([
            'nama_kegiatan'      => $this->request->getPost('nama_kegiatan'),
            'tanggal_kegiatan'   => $this->request->getPost('tanggal_kegiatan'),
            'tempat_pelaksanaan' => $this->request->getPost('tempat_pelaksanaan'),
            'kupon_min'          => $this->request->getPost('kupon_min'),
            'kupon_max'          => $this->request->getPost('kupon_max'),
            'status'             => $this->request->getPost('status'),
        ]);

        return redirect()->to('backend/luckydraw/kegiatan')->with('message', 'Kegiatan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'page_title'    => 'Edit Kegiatan',
            'kegiatan' => $kegiatan,
        ];

        return view('backend/luckydraw/kegiatan/form', $data);
    }

    public function update($id)
    {
        $rules = [
            'nama_kegiatan'      => 'required',
            'tanggal_kegiatan'   => 'required|valid_date',
            'kupon_min'          => 'required|numeric',
            'kupon_max'          => 'required|numeric|greater_than_equal_to[' . $this->request->getPost('kupon_min') . ']',
            'status'             => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kegiatanModel->update($id, [
            'nama_kegiatan'      => $this->request->getPost('nama_kegiatan'),
            'tanggal_kegiatan'   => $this->request->getPost('tanggal_kegiatan'),
            'tempat_pelaksanaan' => $this->request->getPost('tempat_pelaksanaan'),
            'kupon_min'          => $this->request->getPost('kupon_min'),
            'kupon_max'          => $this->request->getPost('kupon_max'),
            'status'             => $this->request->getPost('status'),
        ]);

        return redirect()->to('backend/luckydraw/kegiatan')->with('message', 'Kegiatan berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->kegiatanModel->delete($id);
        return redirect()->to('backend/luckydraw/kegiatan')->with('message', 'Kegiatan berhasil dihapus');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($this->kegiatanModel->update($id, ['status' => $status])) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false]);
    }
}
