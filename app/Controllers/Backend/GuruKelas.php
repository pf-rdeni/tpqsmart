<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\GuruKelasModel;
use App\Models\HelpFunctionModel;

class GuruKelas extends BaseController
{
    protected $guruKelasModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->guruKelasModel = new GuruKelasModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function show()
    {
        $IdTpq = session()->get('IdTpq');
        // Filter berdasarkan tahun ajaran saat ini
        $IdTahunAjaranSaatIni = $this->helpFunction->getTahunAjaranSaatIni();
        $GuruKelas = $this->helpFunction->getDataGuruKelas(IdTpq: $IdTpq, IdTahunAjaran: $IdTahunAjaranSaatIni);
        $Kelas = $this->helpFunction->getDataKelas();

        // Ambil semua tahun ajaran yang tersedia untuk dropdown filter (tanpa filter)
        $allGuruKelas = $this->helpFunction->getDataGuruKelas(IdTpq: $IdTpq);
        $tahunAjaranList = [];
        foreach ($allGuruKelas as $row) {
            if (!in_array($row->IdTahunAjaran, array_column($tahunAjaranList, 'IdTahunAjaran'))) {
                $tahunAjaranList[] = [
                    'IdTahunAjaran' => $row->IdTahunAjaran,
                    'NamaTahunAjaran' => $this->helpFunction->convertTahunAjaran($row->IdTahunAjaran)
                ];
            }
        }
        // Sort berdasarkan IdTahunAjaran (descending)
        usort($tahunAjaranList, function ($a, $b) {
            return $b['IdTahunAjaran'] <=> $a['IdTahunAjaran'];
        });

        $data = [
            'page_title' => 'Daftar Guru Kelas',
            'guruKelas' => $GuruKelas,
            'kelas' => $Kelas,
            'dataTpq' => $IdTpq,
            'tahunAjaranSaatIni' => $IdTahunAjaranSaatIni,
            'tahunAjaranList' => $tahunAjaranList
        ];

        return view('backend/kelas/guruKelas', $data);
    }

    public function getDataByTahunAjaran()
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = $this->request->getGet('IdTahunAjaran');
        $IdJabatan = $this->request->getGet('IdJabatan');
        $IdKelas = $this->request->getGet('IdKelas');

        // Jika IdTahunAjaran adalah "next", hitung tahun ajaran berikutnya
        if ($IdTahunAjaran === 'next') {
            $tahunAjaranSaatIni = $this->helpFunction->getTahunAjaranSaatIni();
            $IdTahunAjaran = $this->helpFunction->getTahuanAjaranBerikutnya($tahunAjaranSaatIni);
        }
        // Jika IdTahunAjaran kosong, gunakan tahun ajaran saat ini
        elseif (empty($IdTahunAjaran)) {
            $IdTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        }

        $GuruKelas = $this->helpFunction->getDataGuruKelas(
            IdTpq: $IdTpq,
            IdTahunAjaran: $IdTahunAjaran,
            IdJabatan: !empty($IdJabatan) ? $IdJabatan : null,
            IdKelas: !empty($IdKelas) ? $IdKelas : null
        );

        // Ambil semua guru untuk dibandingkan dengan yang sudah punya data
        $allGuru = $this->helpFunction->getDataGuru(id: false, status: true, IdTpq: $IdTpq);

        // Identifikasi guru yang sudah punya data di tahun ajaran ini
        $guruWithData = [];
        foreach ($GuruKelas as $row) {
            $idGuru = (string)$row->IdGuru;
            if (!in_array($idGuru, $guruWithData)) {
                $guruWithData[] = $idGuru;
            }
        }

        // Identifikasi guru yang belum punya data
        $guruWithoutData = [];
        foreach ($allGuru as $guru) {
            $idGuru = (string)$guru['IdGuru'];
            if (!in_array($idGuru, $guruWithData)) {
                $guruWithoutData[] = [
                    'IdGuru' => $guru['IdGuru'],
                    'Nama' => $guru['Nama']
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $GuruKelas,
            'isEmpty' => empty($GuruKelas),
            'allGuru' => $allGuru,
            'guruWithoutData' => $guruWithoutData,
            'IdTahunAjaran' => $IdTahunAjaran,
            'TahunAjaran' => $this->helpFunction->convertTahunAjaran($IdTahunAjaran)
        ]);
    }

    public function getFilterOptions()
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = $this->request->getGet('IdTahunAjaran');

        // Jika IdTahunAjaran adalah "next", hitung tahun ajaran berikutnya
        if ($IdTahunAjaran === 'next') {
            $tahunAjaranSaatIni = $this->helpFunction->getTahunAjaranSaatIni();
            $IdTahunAjaran = $this->helpFunction->getTahuanAjaranBerikutnya($tahunAjaranSaatIni);
        }
        // Jika IdTahunAjaran kosong, gunakan tahun ajaran saat ini
        elseif (empty($IdTahunAjaran)) {
            $IdTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        }

        // Ambil data berdasarkan tahun ajaran
        $GuruKelas = $this->helpFunction->getDataGuruKelas(IdTpq: $IdTpq, IdTahunAjaran: $IdTahunAjaran);

        // Extract unique posisi dan kelas
        $posisiList = [];
        $kelasList = [];
        foreach ($GuruKelas as $row) {
            // Posisi
            if (!in_array($row->IdJabatan, array_column($posisiList, 'IdJabatan'))) {
                $posisiList[] = [
                    'IdJabatan' => $row->IdJabatan,
                    'NamaJabatan' => $row->NamaJabatan
                ];
            }
            // Kelas
            if (!in_array($row->IdKelas, array_column($kelasList, 'IdKelas'))) {
                $kelasList[] = [
                    'IdKelas' => $row->IdKelas,
                    'NamaKelas' => $row->NamaKelas
                ];
            }
        }

        // Sort
        usort($posisiList, function ($a, $b) {
            return strcmp($a['NamaJabatan'], $b['NamaJabatan']);
        });
        usort($kelasList, function ($a, $b) {
            return strcmp($a['NamaKelas'], $b['NamaKelas']);
        });

        return $this->response->setJSON([
            'success' => true,
            'posisi' => $posisiList,
            'kelas' => $kelasList
        ]);
    }

    public function store()
    {
        $id = $this->request->getPost('Id');
        $idKelas = $this->request->getPost('IdKelas');
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $idJabatan = $this->request->getPost('IdJabatan');

        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        // Cek apakah kombinasi IdKelas, IdTahunAjaran dan IdJabatan=3 wali kelas sudah ada
        if ($idJabatan == 3) {
            $existing = $this->guruKelasModel
                ->select('tbl_guru_kelas.*, tbl_guru.Nama as NamaGuru, tbl_kelas.NamaKelas')
                ->join('tbl_guru', 'tbl_guru.IdGuru = tbl_guru_kelas.IdGuru')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_guru_kelas.IdKelas')
                ->where([
                    'tbl_guru_kelas.IdKelas' => $idKelas,
                    'tbl_guru_kelas.IdTahunAjaran' => $idTahunAjaran,
                'tbl_guru_kelas.IdJabatan' => 3,
                'tbl_guru_kelas.IdTpq' => $IdTpq
                ])->first();

            // Jika data existing ditemukan dan ini adalah data baru (tidak ada $id)
            // ATAU jika ini adalah update tapi untuk record yang berbeda
            if ($existing && ($id === null || $id != $existing['Id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Wali Kelas untuk kelas {$existing['NamaKelas']} sudah ditempati oleh {$existing['NamaGuru']}!"
                ]);
            }
        }
        
        $data = [
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdKelas' => $idKelas,
            'IdGuru' => $this->request->getPost('IdGuru'),
            'IdTahunAjaran' => $idTahunAjaran,
            'IdJabatan' => $idJabatan,
        ];
        if ($id) 
             $data['Id'] = $id;
        
        $this->guruKelasModel->save($data);
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function delete($id)
    {
        //cek apakah data guru kelas ada
        $data = $this->guruKelasModel->where('Id', $id)->first();
        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data guru kelas tidak ditemukan'
            ]);
        }
        //cek apakah data guru kelas ada di tabel tbl_guru_kelas
        $this->guruKelasModel->where('Id', $id)->delete();
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data guru kelas berhasil dihapus'
        ]);
    }
}
