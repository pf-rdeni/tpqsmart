<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratKopCustomModel;
use App\Models\HelpFunctionModel;

class SuratKopCustom extends BaseController
{
    protected $kopModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->kopModel     = new SuratKopCustomModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        if ($isAdmin) {
            $kopList = $this->kopModel->orderBy('Id', 'DESC')->findAll();
        } else {
            $kopList = $this->kopModel->where('IdTpq', $idTpq)
                                      ->orWhere('IdTpq IS NULL', null, false)
                                      ->orderBy('Id', 'DESC')
                                      ->findAll();
        }

        $listTpq = $this->helpFunction->getDataTpq(false);

        $data = [
            'page_title' => 'Kop Kepanitiaan & Khusus',
            'kopList'    => $kopList,
            'listTpq'    => $listTpq,
            'idTpq'      => $idTpq,
            'isAdmin'    => $isAdmin,
        ];

        return view('backend/administrasi/kop_custom/index', $data);
    }

    public function store()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $rules = [
            'NamaKop'   => 'required|min_length[3]',
            'GambarKop' => 'permit_empty|is_image[GambarKop]|max_size[GambarKop,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileGambar = $this->request->getFile('GambarKop');
        $fileName   = null;

        if ($fileGambar && $fileGambar->isValid() && !$fileGambar->hasMoved()) {
            $fileName = $fileGambar->getRandomName();
            $fileGambar->move(FCPATH . 'uploads/kop_custom/', $fileName);
        }

        $targetTpq = $isAdmin ? ($this->request->getPost('IdTpq') ?: null) : $idTpq;

        $saveData = [
            'IdTpq'      => $targetTpq,
            'NamaKop'    => $this->request->getPost('NamaKop'),
            'GambarKop'  => $fileName,
            'Keterangan' => $this->request->getPost('Keterangan'),
            'IsActive'   => 1,
        ];

        $this->kopModel->insert($saveData);

        return redirect()->to(base_url('backend/administrasi/kop-custom'))->with('message', 'Kop kepanitiaan berhasil ditambahkan');
    }

    public function update($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->kopModel->find($id);
        if (!$existing) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq && !empty($existing['IdTpq'])) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $rules = [
            'NamaKop'   => 'required|min_length[3]',
            'GambarKop' => 'permit_empty|is_image[GambarKop]|max_size[GambarKop,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileGambar = $this->request->getFile('GambarKop');
        $fileName   = $existing['GambarKop'];

        if ($fileGambar && $fileGambar->isValid() && !$fileGambar->hasMoved()) {
            // Hapus gambar lama jika ada
            if (!empty($existing['GambarKop'])) {
                $oldPath = FCPATH . 'uploads/kop_custom/' . $existing['GambarKop'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $fileName = $fileGambar->getRandomName();
            $fileGambar->move(FCPATH . 'uploads/kop_custom/', $fileName);
        }

        $targetTpq = $isAdmin ? ($this->request->getPost('IdTpq') ?: null) : $existing['IdTpq'];

        $updateData = [
            'IdTpq'      => $targetTpq,
            'NamaKop'    => $this->request->getPost('NamaKop'),
            'GambarKop'  => $fileName,
            'Keterangan' => $this->request->getPost('Keterangan'),
        ];

        $this->kopModel->update($id, $updateData);

        return redirect()->to(base_url('backend/administrasi/kop-custom'))->with('message', 'Kop kepanitiaan berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->kopModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        if (!empty($existing['GambarKop'])) {
            $path = FCPATH . 'uploads/kop_custom/' . $existing['GambarKop'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if ($this->kopModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Kop berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus kop']);
    }

    public function toggleActive($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->kopModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        if ($this->kopModel->toggleAktif($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status berhasil diubah']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah status']);
    }
}
