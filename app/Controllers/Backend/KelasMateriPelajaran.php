<?php

namespace App\Controllers\Backend;

use App\Models\KelasMateriPelajaranModel;
use App\Controllers\BaseController;

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

    public function update($id)
    {
        $model = new KelasMateriPelajaranModel();
        $data = [
            'IdKelas'       => $this->request->getPost('IdKelas'),
            'IdTpq'         => $this->request->getPost('IdTpq'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdMateri'      => $this->request->getPost('IdMateri'),
            'Semester'      => $this->request->getPost('Semester')
        ];
        $model->update($id, $data);

        return redirect()->to('/kelasMateriPelajaran');
    }

    public function delete($id)
    {
        $model = new KelasMateriPelajaranModel();
        $model->delete($id);

        return redirect()->to('/kelasMateriPelajaran');
    }

    public function showMateriKelas($IdTpq = 411221010225)
    {
        $model = new KelasMateriPelajaranModel();

        // Query builder tetap sama
        $builder = $model->select('tbl_kelas_materi_pelajaran.*, 
                                 tbl_kelas.NamaKelas,
                                 tbl_tpq.NamaTpq,
                                 tbl_tahun_ajaran.NamaTahunAjaran,
                                 tbl_materi_pelajaran.*')
        ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_kelas_materi_pelajaran.IdKelas')
        ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_kelas_materi_pelajaran.IdTpq')
        ->join('tbl_tahun_ajaran', 'tbl_tahun_ajaran.IdTahunAjaran = tbl_kelas_materi_pelajaran.IdTahunAjaran')
        ->join('tbl_materi_pelajaran', 'tbl_materi_pelajaran.IdMateri = tbl_kelas_materi_pelajaran.IdMateri');

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

        $data = [
            'page_title' => 'Data Materi Pelajaran',
            'materi_per_kelas' => $materiPerKelas,
            'tpq' => $IdTpq,
        ];

        return view('backend/materi/daftarKelasMateriPelajaran', $data);
    }

}
