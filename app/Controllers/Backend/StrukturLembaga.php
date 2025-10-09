<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\StrukturLembagaModel;
use App\Models\HelpFunctionModel;

class StrukturLembaga extends BaseController
{
    protected $strukturLembagaModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->strukturLembagaModel = new StrukturLembagaModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function index()
    {
        $IdTpq = session()->get('IdTpq');
        $strukturLembaga = $this->getDataStrukturLembaga($IdTpq);
        $guru = $this->helpFunction->getDataGuru($IdTpq);
        $jabatan = $this->helpFunction->getDataJabatan();
        
        $data = [
            'page_title' => 'Struktur Lembaga',
            'strukturLembaga' => $strukturLembaga,
            'guru' => $guru,
            'jabatan' => $jabatan,
            'dataTpq' => $IdTpq
        ];

        return view('backend/struktur/index', $data);
    }

    public function create()
    {
        $IdTpq = session()->get('IdTpq');
        $guru = $this->helpFunction->getDataGuru($IdTpq);
        $jabatan = $this->helpFunction->getDataJabatan();
        
        $data = [
            'page_title' => 'Tambah Struktur Lembaga',
            'guru' => $guru,
            'jabatan' => $jabatan,
            'dataTpq' => $IdTpq
        ];

        return view('backend/struktur/create', $data);
    }

    public function store()
    {
        $id = $this->request->getPost('Id');
        $idTpq = $this->request->getPost('IdTpq');
        $idGuru = $this->request->getPost('IdGuru');
        $tanggalStart = $this->request->getPost('TanggalStart');
        $tanggalAkhir = $this->request->getPost('TanggalAkhir');
        $idJabatan = $this->request->getPost('IdJabatan');

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'IdTpq' => 'required|integer',
            'IdGuru' => 'required|integer',
            'TanggalStart' => 'required|valid_date',
            'TanggalAkhir' => 'permit_empty|valid_date',
            'IdJabatan' => 'required|integer'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validation->getErrors())
            ]);
        }

        // Validasi tanggal
        if ($tanggalAkhir && strtotime($tanggalAkhir) <= strtotime($tanggalStart)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal akhir harus lebih besar dari tanggal mulai'
            ]);
        }

        // Cek apakah guru sudah memiliki jabatan aktif di TPQ yang sama
        $existing = $this->strukturLembagaModel
            ->where([
                'IdTpq' => $idTpq,
                'IdGuru' => $idGuru,
                'IdJabatan' => $idJabatan
            ])
            ->where('(TanggalAkhir IS NULL OR TanggalAkhir >= CURDATE())')
            ->first();

        // Jika data existing ditemukan dan ini adalah data baru (tidak ada $id)
        // ATAU jika ini adalah update tapi untuk record yang berbeda
        if ($existing && ($id === null || $id != $existing['Id'])) {
            $guruData = $this->helpFunction->getDataGuru($idTpq);
            $jabatanData = $this->helpFunction->getDataJabatan();
            
            $namaGuru = '';
            $namaJabatan = '';
            
            foreach ($guruData as $g) {
                if ($g['IdGuru'] == $idGuru) {
                    $namaGuru = $g['Nama'];
                    break;
                }
            }
            
            foreach ($jabatanData as $j) {
                if ($j['IdJabatan'] == $idJabatan) {
                    $namaJabatan = $j['NamaJabatan'];
                    break;
                }
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => "Guru {$namaGuru} sudah memiliki jabatan {$namaJabatan} yang aktif!"
            ]);
        }

        $data = [
            'IdTpq' => $idTpq,
            'IdGuru' => $idGuru,
            'TanggalStart' => $tanggalStart,
            'TanggalAkhir' => $tanggalAkhir ?: null,
            'IdJabatan' => $idJabatan,
        ];

        if ($id) {
            $data['Id'] = $id;
        }

        if ($this->strukturLembagaModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . implode(', ', $this->strukturLembagaModel->errors())
            ]);
        }
    }

    public function edit($id)
    {
        $strukturLembaga = $this->strukturLembagaModel->find($id);
        
        if (!$strukturLembaga) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan');
        }

        $IdTpq = session()->get('IdTpq');
        $guru = $this->helpFunction->getDataGuru($IdTpq);
        $jabatan = $this->helpFunction->getDataJabatan();
        
        $data = [
            'page_title' => 'Edit Struktur Lembaga',
            'strukturLembaga' => $strukturLembaga,
            'guru' => $guru,
            'jabatan' => $jabatan,
            'dataTpq' => $IdTpq
        ];

        return view('backend/struktur/edit', $data);
    }

    public function update($id)
    {
        return $this->store(); // Reuse store method for update
    }

    public function delete($id)
    {
        try {
            $this->strukturLembagaModel->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data struktur lembaga berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data struktur lembaga: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mengambil data struktur lembaga dengan join ke tabel terkait
     */
    private function getDataStrukturLembaga($IdTpq = null)
    {
        $builder = $this->strukturLembagaModel->db->table('tbl_struktur_lembaga sl')
            ->select('sl.*, g.Nama as NamaGuru, t.NamaTpq, j.NamaJabatan')
            ->join('tbl_guru g', 'g.IdGuru = sl.IdGuru')
            ->join('tbl_tpq t', 't.IdTpq = sl.IdTpq')
            ->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan')
            ->orderBy('sl.TanggalStart', 'DESC');

        if ($IdTpq !== null) {
            $builder->where('sl.IdTpq', $IdTpq);
        }

        return $builder->get()->getResult();
    }
}
