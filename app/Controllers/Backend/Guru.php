<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\HelpFunctionModel;

class Guru extends BaseController
{
    protected $DataModels;
    protected $helpFunction;

    public function __construct()
    {
        $this->DataModels = new GuruModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function show()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        // query data guru berdasarkan IdTpq jika idtpq tidak ada maka akan menampilkan semua data guru
        if ($IdTpq == null) {
            $data = [
                'page_title' => 'Data Guru',
                'guru' => $this->DataModels->findAll(),
                'tpq' => $this->helpFunction->getDataTpq()
            ];
        } else {
            $data = [
                'page_title' => 'Data Guru',
                'guru' => $this->DataModels->where('IdTpq', $IdTpq)->findAll(),
                'tpq' => $this->helpFunction->getDataTpq()
            ];
        }
        return view('backend/guru/guru', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Data Guru',
            'tpq' => $this->helpFunction->getDataTpq()
        ];
        return view('backend/guru/create', $data);
    }

    public function store()
    {
        // Validasi input
        $rules = [
            'IdGuru' => 'required|min_length[16]|max_length[16]|is_unique[tbl_guru.IdGuru]',
            'Nama' => 'required',
            'IdTpq' => 'required',
            'TanggalMulaiTugas' => 'required',
            'NoHp' => 'required|min_length[10]|max_length[13]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Gabungkan gelar dengan nama
        $nama = $this->request->getPost('Nama');
        $gelarDepan = $this->request->getPost('GelarDepan');
        $gelarBelakang = $this->request->getPost('GelarBelakang');

        $namaLengkap = '';
        if (!empty($gelarDepan)) {
            $namaLengkap .= trim($gelarDepan) . ' ';
        }
        $namaLengkap .= trim($nama);
        if (!empty($gelarBelakang)) {
            $namaLengkap .= ', ' . trim($gelarBelakang);
        }

        // Simpan data
        $data = [
            'IdGuru' => $this->request->getPost('IdGuru'),
            'Nama' => $namaLengkap,
            'IdTpq' => $this->request->getPost('IdTpq'),
            'TempatTugas' => $this->request->getPost('TempatTugas'),
            'TanggalMulaiTugas' => $this->request->getPost('TanggalMulaiTugas'),
            'NoHp' => $this->request->getPost('NoHp'),
            'JenisKelamin' => $this->request->getPost('JenisKelamin'),
            'TempatLahir' => $this->request->getPost('TempatLahir'),
            'TanggalLahir' => $this->request->getPost('TanggalLahir'),
            'PendidikanTerakhir' => $this->request->getPost('PendidikanTerakhir'),
            'Alamat' => $this->request->getPost('Alamat'),
            'Rt' => $this->request->getPost('Rt'),
            'Rw' => $this->request->getPost('Rw'),
            'KelurahanDesa' => $this->request->getPost('KelurahanDesa'),
        ];

        $this->DataModels->insert($data);

        return redirect()->to(base_url('backend/guru/show'))->with('success', 'Data guru berhasil ditambahkan');
    }
    //validasi nik
    public function validateNik()
    {
        $nik = $this->request->getPost('IdGuru');
        $exists = $this->DataModels->where('IdGuru', $nik)->first();
        return $this->response->setJSON([
            'exists' => !empty($exists), // true jika NIK ditemukan, false jika tidak
            'message' => !empty($exists) ? 'NIK sudah terdaftar' : 'NIK belum terdaftar',
            'data' => $exists
        ]);
    }

    //fungsi untuk menghapus data guru
    public function delete($id)
    {
        try {
            // Cek apakah data guru ada
            $guru = $this->DataModels->find($id);
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Hapus data guru
            $this->DataModels->delete($id);

            // Jika request AJAX, kembalikan response JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data guru berhasil dihapus'
                ]);
            }

            // Jika bukan AJAX, redirect dengan flash message
            return redirect()->to(base_url('backend/guru/show'))->with('success', 'Data guru berhasil dihapus');
        } catch (\Exception $e) {
            // Jika request AJAX, kembalikan response JSON dengan error
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data guru: ' . $e->getMessage()
                ]);
            }

            // Jika bukan AJAX, redirect dengan flash message error
            return redirect()->to(base_url('backend/guru/show'))->with('error', 'Gagal menghapus data guru: ' . $e->getMessage());
        }
    }

    // Fungsi untuk mengambil data guru
    public function getData($id)
    {
        try {
            $guru = $this->DataModels->find($id);
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Pisahkan nama dan gelar
            $namaParts = explode(',', $guru['Nama']);
            $namaLengkap = trim($namaParts[0]);

            // Cek apakah ada gelar belakang
            if (count($namaParts) > 1) {
                $guru['GelarBelakang'] = trim($namaParts[1]);
            }

            // Cek apakah ada gelar depan
            $namaWords = explode(' ', $namaLengkap);
            if (in_array($namaWords[0], ['dr.', 'Dr.', 'Prof.'])) {
                $guru['GelarDepan'] = $namaWords[0];
                array_shift($namaWords);
                $guru['Nama'] = implode(' ', $namaWords);
            } else {
                $guru['Nama'] = $namaLengkap;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $guru
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data guru: ' . $e->getMessage()
            ]);
        }
    }

    // Fungsi untuk update data guru
    public function update()
    {
        try {
            // Validasi input
            $rules = [
                'IdGuru' => 'required|min_length[16]|max_length[16]',
                'Nama' => 'required',
                'IdTpq' => 'required',
                'TanggalMulaiTugas' => 'required',
                'NoHp' => 'required|min_length[10]|max_length[13]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Cek apakah data guru ada
            $guru = $this->DataModels->find($this->request->getPost('IdGuru'));
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Gabungkan gelar dengan nama
            $nama = $this->request->getPost('Nama');
            $gelarDepan = $this->request->getPost('GelarDepan');
            $gelarBelakang = $this->request->getPost('GelarBelakang');

            $namaLengkap = '';
            if (!empty($gelarDepan)) {
                $namaLengkap .= trim($gelarDepan) . ' ';
            }
            $namaLengkap .= trim($nama);
            if (!empty($gelarBelakang)) {
                $namaLengkap .= ', ' . trim($gelarBelakang);
            }

            // Update data
            $data = [
                'Nama' => $namaLengkap,
                'IdTpq' => $this->request->getPost('IdTpq'),
                'TempatTugas' => $this->request->getPost('TempatTugas'),
                'TanggalMulaiTugas' => $this->request->getPost('TanggalMulaiTugas'),
                'NoHp' => $this->request->getPost('NoHp'),
                'JenisKelamin' => $this->request->getPost('JenisKelamin'),
                'TempatLahir' => $this->request->getPost('TempatLahir'),
                'TanggalLahir' => $this->request->getPost('TanggalLahir'),
                'PendidikanTerakhir' => $this->request->getPost('PendidikanTerakhir'),
                'Alamat' => $this->request->getPost('Alamat'),
                'Rt' => $this->request->getPost('Rt'),
                'Rw' => $this->request->getPost('Rw'),
                'KelurahanDesa' => $this->request->getPost('KelurahanDesa'),
                'Status' => $this->request->getPost('Status'),
            ];

            $this->DataModels->update($this->request->getPost('IdGuru'), $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data guru berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui data guru: ' . $e->getMessage()
            ]);
        }
    }
}