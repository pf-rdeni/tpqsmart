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
        try {
            $result = $this->materiModel->save([
                'IdMateri'  => $this->request->getPost('IdMateri'),
                'NamaMateri' => strtoupper($this->request->getPost('NamaMateri')),
                'Kategori' => strtoupper($this->request->getPost('Kategori')),
                'IdTpq' => session()->get('IdTpq')
            ]);

            if (!$result) {
                throw new \Exception('Gagal menyimpan data materi pelajaran');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi pelajaran berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $data['materi'] = $this->materiModel->find($id);
        return view('materipelajaran/edit', $data);
    }

    public function update($id)
    {
        try {
            $result = $this->materiModel->update($id, [
                'IdMateri'  => $this->request->getPost('IdMateri'),
                'NamaMateri' => strtoupper($this->request->getPost('NamaMateri')),
                'Kategori' => strtoupper($this->request->getPost('Kategori'))
            ]);

            if (!$result) {
                throw new \Exception('Gagal memperbarui data materi pelajaran');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi pelajaran berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ]);
        }
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
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        if ($IdTpq) {
            $IdTpq = $IdTpq['IdTpq'];
        }

        $dataMateriPelajaran = $this->materiModel
        ->select('tbl_materi_pelajaran.*, tpq.NamaTpq')
        ->join('tbl_tpq tpq', 'tpq.IdTpq = tbl_materi_pelajaran.IdTpq', 'left')
        ->where('tbl_materi_pelajaran.IdTpq', $IdTpq)
        ->orWhere('tbl_materi_pelajaran.IdTpq', null)
        ->findAll();

        $kategori = $this->materiModel
            ->select('Kategori')
            ->where('IdTpq', $IdTpq)
            ->orWhere('IdTpq', null)
            ->groupBy('Kategori')
            ->findAll();
        
        $data = [
            'page_title' => 'Data Materi Pelajaran',
            'materiPelajaran' => $dataMateriPelajaran,
            'kategoriPelajaran' => $kategori,
        ];
        return view('backend/materi/daftarMeteriPelajaran', $data);
    }

    public function getLastIdMateri()
    {
        $kategori = $this->request->getJSON()->kategori;

        // Ambil ID Materi terakhir berdasarkan kategori
        $lastId = $this->materiModel
            ->select('IdMateri')
            ->where('Kategori', $kategori)
            ->orderBy('IdMateri', 'DESC')
            ->get()
            ->getRowArray();

        if ($lastId) {
            // Ambil ID terakhir
            $currentId = $lastId['IdMateri'];

            // Pisahkan string dan angka
            preg_match('/([A-Za-z]+)(\d+)/', $currentId, $matches);

            if (count($matches) >= 3) {
                $prefix = $matches[1];  // Bagian huruf (contoh: "SH")
                $number = intval($matches[2]);  // Bagian angka (contoh: "01")

                // Tambah 1 ke angka dan format dengan leading zero
                $nextNumber = str_pad($number + 1, strlen($matches[2]), '0', STR_PAD_LEFT);
                $nextId = $prefix . $nextNumber;
            } else {
                // Jika format tidak sesuai, gunakan format default
                $nextId = $kategori . '01';
            }
        } else {
            // Jika belum ada data, mulai dari 01
            $nextId = $kategori . '01';
        }

        return $this->response->setJSON([
            'success' => true,
            'nextId' => $nextId
        ]);
    }

}
