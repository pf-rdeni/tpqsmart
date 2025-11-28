<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KelasMateriPelajaranModel;
use App\Models\HelpFunctionModel;
use App\Models\MateriPelajaranModel;

class KelasMateriPelajaran extends BaseController
{
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
            'tbl_kelas_materi_pelajaran.Id, tbl_kelas_materi_pelajaran.IdKelas, tbl_kelas_materi_pelajaran.IdMateri, tbl_kelas_materi_pelajaran.UrutanMateri, tbl_kelas_materi_pelajaran.SemesterGanjil, tbl_kelas_materi_pelajaran.SemesterGenap, 
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
        // urutkan berdasarkan kelas, tpq, urutan materi
        $builder->orderBy('tbl_kelas.NamaKelas', 'ASC');
        $builder->orderBy('tbl_tpq.NamaTpq', 'ASC');
        $builder->orderBy('tbl_kelas_materi_pelajaran.UrutanMateri', 'ASC');
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

    // Update Materi pada table nilai dengan fungsi dari helpfunctionModel updateMateriPelajaranPadaTabelNilai
    public function updateMateriPelajaranPadaTabelNilai()
    {
        $helpModel = new HelpFunctionModel();
        // ambil data IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        // ambil tahun ajaran saat ini dari helpModel
        $tahunAjaran = $helpModel->getTahunAjaranSaatIni();

        // Step 1: Cek materi yang perlu dihapus
        $materiToDelete = $helpModel->getMateriPelajaranYangSudahTidakAda($IdTpq, $tahunAjaran);

        // Step 2: Cek materi baru yang perlu ditambahkan
        $materiToAdd = $helpModel->getMateriBaruUntukDitambahkan($IdTpq, $tahunAjaran);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'materiToDelete' => $materiToDelete,
                'materiToAdd' => $materiToAdd
            ]
        ]);
    }

    public function prosesHapusMateri()
    {
        $helpModel = new HelpFunctionModel();
        $IdTpq = session()->get('IdTpq');
        $tahunAjaran = $helpModel->getTahunAjaranSaatIni();

        $materiToDelete = $helpModel->getMateriPelajaranYangSudahTidakAda($IdTpq, $tahunAjaran);

        if (!empty($materiToDelete)) {
            $idsToDelete = array_column($materiToDelete, 'Id');
            $result = $helpModel->nilaiModel->whereIn('Id', $idsToDelete)->delete();

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data materi yang tidak valid berhasil dihapus.'
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tidak ada data yang perlu dihapus.'
        ]);
    }

    public function prosesTambahMateri()
    {
        $helpModel = new HelpFunctionModel();
        $IdTpq = session()->get('IdTpq');
        $tahunAjaran = $helpModel->getTahunAjaranSaatIni();

        $materiToAdd = $helpModel->getMateriBaruUntukDitambahkan($IdTpq, $tahunAjaran);

        if (!empty($materiToAdd)) {
            $batchData = [];
            foreach ($materiToAdd as $materi) {
                $batchData[] = [
                    'IdTpq' => $IdTpq,
                    'IdSantri' => $materi['IdSantri'],
                    'IdKelas' => $materi['IdKelas'],
                    'IdMateri' => $materi['IdMateri'],
                    'IdTahunAjaran' => $tahunAjaran,
                    'Semester' => $materi['Semester']
                ];
            }

            $result = $helpModel->nilaiModel->insertBatch($batchData);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data materi baru berhasil ditambahkan.'
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tidak ada data baru yang perlu ditambahkan.'
        ]);
    }

    public function checkUrutanMateri()
    {
        try {
            $model = new KelasMateriPelajaranModel();
            $id = $this->request->getPost('Id');
            $urutanMateri = $this->request->getPost('UrutanMateri');

            // Validasi input
            if (!$id || !$urutanMateri) {
                return $this->response->setJSON([
                    'status' => 'fail',
                    'message' => 'Data tidak lengkap'
                ])->setStatusCode(400);
            }

            // Ambil data materi yang akan diupdate
            $currentData = $model->find($id);
            if (!$currentData) {
                return $this->response->setJSON([
                    'status' => 'fail',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Cek apakah urutan sudah digunakan oleh materi lain di kelas dan TPQ yang sama
            $existingData = $model->select('tbl_kelas_materi_pelajaran.Id, tbl_kelas_materi_pelajaran.IdMateri, tbl_materi_pelajaran.NamaMateri')
                ->join('tbl_materi_pelajaran', 'tbl_materi_pelajaran.IdMateri = tbl_kelas_materi_pelajaran.IdMateri', 'left')
                ->where('tbl_kelas_materi_pelajaran.IdKelas', $currentData['IdKelas'])
                ->where('tbl_kelas_materi_pelajaran.IdTpq', $currentData['IdTpq'])
                ->where('tbl_kelas_materi_pelajaran.UrutanMateri', $urutanMateri)
                ->where('tbl_kelas_materi_pelajaran.Id !=', $id)
                ->first();

            if ($existingData) {
                return $this->response->setJSON([
                    'status' => 'conflict',
                    'message' => 'Urutan materi sudah digunakan',
                    'data' => [
                        'existingId' => $existingData['Id'],
                        'existingIdMateri' => $existingData['IdMateri'],
                        'existingNamaMateri' => $existingData['NamaMateri']
                    ]
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Urutan materi tersedia'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'fail',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function updateUrutan()
    {
        try {
            $model = new KelasMateriPelajaranModel();
            $id = $this->request->getPost('Id');
            $urutanMateri = $this->request->getPost('UrutanMateri');
            $replaceExisting = $this->request->getPost('replaceExisting'); // true jika ingin mengganti yang lama

            // Validasi input
            if (!$id || !$urutanMateri) {
                return $this->response->setJSON([
                    'status' => 'fail',
                    'message' => 'Data tidak lengkap'
                ])->setStatusCode(400);
            }

            // Ambil data materi yang akan diupdate
            $currentData = $model->find($id);
            if (!$currentData) {
                return $this->response->setJSON([
                    'status' => 'fail',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Jika replaceExisting = true, set urutan yang lama ke null
            if ($replaceExisting) {
                $existingData = $model->where('IdKelas', $currentData['IdKelas'])
                    ->where('IdTpq', $currentData['IdTpq'])
                    ->where('UrutanMateri', $urutanMateri)
                    ->where('Id !=', $id)
                    ->first();

                if ($existingData) {
                    $model->update($existingData['Id'], [
                        'UrutanMateri' => null
                    ]);
                }
            }

            // Update urutan materi
            $model->update($id, [
                'UrutanMateri' => $urutanMateri
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Urutan materi berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'fail',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getStatistik()
    {
        try {
            $helpModel = new HelpFunctionModel();
            $IdTpq = session()->get('IdTpq');
            
            // Statistik Perbarui Materi (jumlah yang perlu ditambah dan dihapus)
            $tahunAjaran = $helpModel->getTahunAjaranSaatIni();
            $materiToDelete = $helpModel->getMateriPelajaranYangSudahTidakAda($IdTpq, $tahunAjaran);
            $materiToAdd = $helpModel->getMateriBaruUntukDitambahkan($IdTpq, $tahunAjaran);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'perbaruiMateri' => [
                        'jumlahHapus' => count($materiToDelete),
                        'jumlahTambah' => count($materiToAdd)
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'fail',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
