<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KelasMateriPelajaranModel;
use App\Models\HelpFunctionModel;

class KelasMateriPelajaran extends BaseController
{
    public function index()
    {
        $model = new KelasMateriPelajaranModel();
        $data['materi'] = $model->findAll();

        return view('kelas_materi_pelajaran/index', $data);
    }

    public function create()
    {
        return view('kelas_materi_pelajaran/create');
    }

    
    public function store()
    {
        $model = new KelasMateriPelajaranModel();
        $data = [
            'IdKelas'       => $this->request->getPost('IdKelas'),
            'IdTpq'         => $this->request->getPost('IdTpq'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdMateri'      => $this->request->getPost('IdMateri'),
            'Semester'      => $this->request->getPost('Semester')
        ];
        $model->save($data);

        return redirect()->to('/kelasMateriPelajaran');
    }

    public function edit($id)
    {
        $model = new KelasMateriPelajaranModel();
        $data['materi'] = $model->find($id);

        return view('kelas_materi_pelajaran/edit', $data);
    }

    public function add()
    {
        $model = new KelasMateriPelajaranModel();

        $data = [
            'IdKelas'       => $this->request->getPost('IdKelas'),
            'IdTpq'         => $this->request->getPost('IdTpq'),
            'Materi'      => $this->request->getPost('Materi'),
        ];

        foreach ($data['Materi'] as $Materi) {
            $existingData = $model->where(['IdKelas' => $data['IdKelas'], 'IdTpq' => $data['IdTpq'], 'IdMateri' => $Materi['IdMateri']])->first();
            if ($existingData) {
                return $this->response->setJSON([
                    'status' => 'fail',
                    'message' => 'Data sudah ada di tabel untuk IdMateri: ' . $Materi['IdMateri']
                ])->setStatusCode(409);
            }
        }

        try {
            foreach ($data['Materi'] as $Materi) {
                $model->save([
                    'IdKelas' => $data['IdKelas'],
                    'IdTpq' => $data['IdTpq'],
                    'IdMateri' => $Materi['IdMateri'],
                    'SemesterGanjil' => $Materi['SemesterGanjil'] ?? 0,
                    'SemesterGenap' => $Materi['SemesterGenap'] ?? 0,
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'fail',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function update()
    {
        $model = new KelasMateriPelajaranModel();
        $filedNamaSemester = $this->request->getPost('NamaSemester');
        $id = $this->request->getPost('Id');
        $data = [
            $filedNamaSemester => $this->request->getPost('SemesterStatus')
        ];

        try {
            $model->update($id, $data);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'fail',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function delete($id)
    {
        try {
            $model = new KelasMateriPelajaranModel();
            $model->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'fail',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function showMateriKelas()
    {
        // ambil data IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        
        $model = new KelasMateriPelajaranModel();
        $helpModel = new HelpFunctionModel();

        $builder = $model->select(
            'tbl_kelas_materi_pelajaran.Id, tbl_kelas_materi_pelajaran.IdKelas, tbl_kelas_materi_pelajaran.IdMateri, tbl_kelas_materi_pelajaran.SemesterGanjil, tbl_kelas_materi_pelajaran.SemesterGenap, 
                                 tbl_kelas.NamaKelas,
                                 tbl_tpq.NamaTpq,
                                 tbl_materi_pelajaran.Kategori,tbl_materi_pelajaran.NamaMateri,'
        )
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_kelas_materi_pelajaran.IdKelas', 'left')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_kelas_materi_pelajaran.IdTpq', 'left')
            ->join('tbl_materi_pelajaran', 'tbl_materi_pelajaran.IdMateri = tbl_kelas_materi_pelajaran.IdMateri', 'left');


        if ($IdTpq !== null) {
            $builder->where('tbl_kelas_materi_pelajaran.IdTpq', $IdTpq);
        }

        $dataMateri = $builder->findAll();

        // Mengelompokkan data berdasarkan kelas
        $materiPerKelas = [];
        foreach ($dataMateri as $materi) {
            $kelasId = $materi['IdKelas'];
            $namaKelas = $materi['NamaKelas'];
            $namaTpq = $IdTpq !== null ? $materi['NamaTpq'] : null;

            if (!isset($materiPerKelas[$kelasId])) {
                $materiPerKelas[$kelasId] = [
                    'nama_kelas' => $namaKelas,
                    'nama_tpq' => $namaTpq,
                    'materi' => []
                ];
            }

            $materiPerKelas[$kelasId]['materi'][] = $materi;
        }


        $dataKelas = $helpModel->getDataKelas();
        $dataMateriPelajaran = $helpModel->getDataMateriPelajaran();
        $dataTpq = $helpModel->getDataTpq($IdTpq);

        $data = [
            'page_title' => 'Data Materi Pelajaran',
            'dataMateriPerKelas' => $materiPerKelas,
            'dataTpq' => $dataTpq,
            'defaultTpq' => $IdTpq,
            'dataKelas' => $dataKelas,
            'dataMateriPelajaran' => $dataMateriPelajaran,
        ];

        return view('backend/materi/daftarKelasMateriPelajaran', $data);
    }

}
