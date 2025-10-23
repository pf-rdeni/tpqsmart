<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TpqModel;
use Exception;


class Tpq extends BaseController
{
    public $DataTpq;
    public function __construct()
    {
        $this->DataTpq = new TpqModel();
    }

    public function show()
    {
        $id = '';
        $datatpq = $this->DataTpq->GetData($id);
        $data = [
            'page_title' => 'Data Tpq',
            'tpq' => $datatpq,
            'validation' => \Config\Services::validation()
        ];
        return view('backend/tpq/tpq', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Form Data Tambah Tpq',
            'validation' => \Config\Services::validation()
        ];

        return view('backend/tpq/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'IdTpq' => [
                'rules' => 'required|is_unique[tbl_tpq.IdTpq]',
                'errors' => [
                    'required' => 'Nama TPQ harus di isi',
                    'is_unique' => '{field} TPQ sudah terdaftar'
                ]
            ],
            'NamaTpq' => [
                'rule' => 'required',
                'errors' => [
                    'required' => 'Nama TPQ harus di isi'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();

            session()->setflashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data Gagal Disimpan 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> 
                    <span aria-hidden="true">&times;</span> 
                </button>
            </div>');
            return redirect()->to('/backend/tpq/create/')->withInput()->with('validation', $validation);
        }

        $this->DataTpq->save([
            'IdTpq' => $this->request->getVar('IdTpq'),
            'NamaTpq' => $this->request->getVar('NamaTpq'),
            'Alamat' => $this->request->getVar('AlamatTpq'),
            'TahunBerdiri' => $this->request->getVar('TanggalBerdiri'),
            'KepalaSekolah' => $this->request->getVar('NamaKepTpq'),
            'NoHp' => $this->request->getVar('NoHp'),
            'TempatBelajar' => $this->request->getVar('TempatBelajar')
        ]);

        session()->setFlashdata('pesan', '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Berhasil Disimpan 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
        return redirect()->to('/backend/tpq/show');
    }

    public function update($id)
    {
        // Ambil ID TPQ dari session
        $idTpq = session('IdTpq');

        if (!$this->validate([
            'NamaTpq' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama TPQ harus di isi'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();

            session()->setflashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data Gagal Disimpan 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> 
                    <span aria-hidden="true">&times;</span> 
                </button>
            </div>');
            return redirect()->to('/backend/tpq/edit/' . $idTpq)->withInput()->with('validation', $validation);
        }

        // Update data menggunakan ID dari session
        $this->DataTpq->update($idTpq, [
            'NamaTpq' => $this->request->getVar('NamaTpq'),
            'Alamat' => $this->request->getVar('AlamatTpq'),
            'TahunBerdiri' => $this->request->getVar('TanggalBerdiri'),
            'KepalaSekolah' => $this->request->getVar('NamaKepTpq'),
            'NoHp' => $this->request->getVar('NoHp'),
            'TempatBelajar' => $this->request->getVar('TempatBelajar')
        ]);

        session()->setFlashdata('pesan', '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Profil lembaga berhasil diupdate 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
        return redirect()->to('/backend/tpq/profilLembaga');
    }

    public function delete($id)
    {
        $this->DataTpq->delete($id);
        session()->setFlashdata('pesan', '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Berhasil Di Hapus 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
        return redirect()->to('/backend/tpq/show');
    }

    public function profilLembaga()
    {
        // Ambil ID TPQ dari session
        $idTpq = session('IdTpq');

        // Ambil data TPQ berdasarkan ID dari session
        $datatpq = $this->DataTpq->GetData($idTpq);

        $data = [
            'page_title' => 'Profil Lembaga',
            'tpq' => $datatpq,
            'validation' => \Config\Services::validation()
        ];
        return view('backend/tpq/profilLembaga', $data);
    }

    public function uploadLogo()
    {
        $file = $this->request->getFile('logo');
        // Ambil ID TPQ dari session
        $idTpq = session('IdTpq');

        if ($this->validateUploadFile($file) && !empty($idTpq)) {
            // Buat direktori uploads/logo jika belum ada
            $uploadPath = FCPATH . 'uploads/logo/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Cek apakah ada logo lama di database
            $tpqData = $this->DataTpq->GetData($idTpq);
            if (!empty($tpqData) && !empty($tpqData[0]['LogoLembaga'])) {
                $oldLogoPath = $uploadPath . $tpqData[0]['LogoLembaga'];
                // Hapus file logo lama jika ada
                $this->deleteOldFile($oldLogoPath);
            }

            // Generate nama file unik
            $newName = 'logo_' . $idTpq . '_' . time() . '.' . $file->getExtension();

            // Pindahkan file baru
            if ($file->move($uploadPath, $newName)) {
                // Update database dengan nama file logo berdasarkan IdTpq
                $this->DataTpq->updateLogo($idTpq, $newName);

                session()->setFlashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Logo berhasil diupload dan file lama telah dihapus 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            } else {
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal memindahkan file logo 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            }
        } else {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gagal mengupload logo. Pastikan file valid dan IdTpq tersedia.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
        }

        return redirect()->to('/backend/tpq/profilLembaga');
    }

    public function uploadKop()
    {
        $file = $this->request->getFile('kop_lembaga');
        // Ambil ID TPQ dari session
        $idTpq = session('IdTpq');

        if ($this->validateUploadFile($file) && !empty($idTpq)) {
            // Buat direktori uploads/kop jika belum ada
            $uploadPath = FCPATH . 'uploads/kop/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Cek apakah ada kop lama di database
            $tpqData = $this->DataTpq->GetData($idTpq);
            if (!empty($tpqData) && !empty($tpqData[0]['KopLembaga'])) {
                $oldKopPath = $uploadPath . $tpqData[0]['KopLembaga'];
                // Hapus file kop lama jika ada
                $this->deleteOldFile($oldKopPath);
            }

            // Generate nama file unik
            $newName = 'kop_' . $idTpq . '_' . time() . '.' . $file->getExtension();

            // Pindahkan file baru
            if ($file->move($uploadPath, $newName)) {
                // Update database dengan nama file kop berdasarkan IdTpq
                $this->DataTpq->updateKop($idTpq, $newName);

                session()->setFlashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Kop lembaga berhasil diupload dan file lama telah dihapus 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            } else {
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal memindahkan file kop lembaga 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            }
        } else {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gagal mengupload kop lembaga. Pastikan file valid dan IdTpq tersedia.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
        }

        return redirect()->to('/backend/tpq/profilLembaga');
    }

    public function edit($id)
    {
        // Ambil ID TPQ dari session
        $idTpq = session('IdTpq');

        // Jika tidak ada di session, gunakan parameter $id
        if (empty($idTpq)) {
            $idTpq = $id;
            session()->set('id_tpq', $idTpq);
        }

        $datatpq = $this->DataTpq->GetData($idTpq);
        if (empty($datatpq)) {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data TPQ tidak ditemukan 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/tpq/profilLembaga');
        }

        $data = [
            'page_title' => 'Edit Profil Lembaga',
            'tpq' => $datatpq[0],
            'validation' => \Config\Services::validation()
        ];
        return view('backend/tpq/edit', $data);
    }

    /**
     * Helper method untuk validasi file upload
     */
    private function validateUploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'])
    {
        if (!$file->isValid()) {
            return false;
        }

        if ($file->hasMoved()) {
            return false;
        }

        // Validasi tipe file
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $allowedTypes)) {
            return false;
        }

        // Validasi ukuran file (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return false;
        }

        return true;
    }

    /**
     * Helper method untuk menghapus file lama
     */
    private function deleteOldFile($filePath)
    {
        if (file_exists($filePath)) {
            try {
                unlink($filePath);
                return true;
            } catch (Exception $e) {
                log_message('error', 'Gagal menghapus file: ' . $filePath . ' - ' . $e->getMessage());
                return false;
            }
        }
        return true;
    }
}
