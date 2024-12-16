<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MateriPelajaranModel;


class MateriPelajaran extends BaseController
{
    protected $materiModel;

    public function __construct()
    {
        $this->materiModel = new MateriPelajaranModel();
    }

    public function index()
    {
        $data['materi'] = $this->materiModel->findAll();
        return view('materipelajaran/index', $data);
    }

    public function create()
    {
        return view('materipelajaran/create');
    }

    public function store()
    {
        $this->materiModel->save([
            'IdMateri'  => $this->request->getPost('IdMateri'),
            'NamaMateri' => $this->request->getPost('NamaMateri'),
            'Kategori' => $this->request->getPost('Kategori')
        ]);

        return redirect()->to('/materipelajaran');
    }

    public function edit($id)
    {
        $data['materi'] = $this->materiModel->find($id);
        return view('materipelajaran/edit', $data);
    }

    public function update($id)
    {
        $this->materiModel->update($id, [
            'IdMateri'  => $this->request->getPost('IdMateri'),
            'NamaMateri' => $this->request->getPost('NamaMateri'),
            'Kategori' => $this->request->getPost('Kategori')
        ]);

        return redirect()->to('/materipelajaran');
    }

    public function delete($id)
    {
        try {
            $result = $this->materiModel->delete($id);
            if (!$result) {
                throw new \Exception('Gagal menghapus data materi pelajaran');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi pelajaran berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function showMateriPelajaran()
    {

        $dataMateriPelajaran = $this->materiModel->findAll();
        $data = [
            'page_title' => 'Data Materi Pelajaran',
            'materiPelajaran' => $dataMateriPelajaran
        ];
        return view('backend/materi/daftarMeteriPelajaran', $data);
    }

    
}
