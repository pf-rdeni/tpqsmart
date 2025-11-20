<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MdaModel;
use App\Models\TpqModel;
use Exception;


class Mda extends BaseController
{
    public $DataMda;
    protected $DataTpq;
    
    public function __construct()
    {
        $this->DataMda = new MdaModel();
        $this->DataTpq = new TpqModel();
    }

    public function show()
    {
        $id = '';
        $datamda = $this->DataMda->GetData($id);
        
        // Ambil list TPQ untuk dropdown
        $listTpq = $this->DataTpq->GetData($id);
        
        // Ambil IdTpq yang sudah digunakan untuk MDA (agar tidak muncul di dropdown)
        $usedIdTpq = [];
        foreach ($datamda as $mda) {
            if (!empty($mda['IdTpq'])) {
                $usedIdTpq[] = $mda['IdTpq'];
            }
        }
        
        // Filter TPQ yang belum digunakan untuk MDA
        $availableTpq = [];
        foreach ($listTpq as $tpq) {
            if (!in_array($tpq['IdTpq'], $usedIdTpq)) {
                $availableTpq[] = $tpq;
            }
        }
        
        $data = [
            'page_title' => 'Data MDA',
            'mda' => $datamda,
            'listTpq' => $availableTpq,
            'allTpq' => $listTpq, // Semua TPQ untuk referensi
            'validation' => \Config\Services::validation()
        ];
        return view('backend/mda/mda', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Form Data Tambah MDA',
            'validation' => \Config\Services::validation()
        ];

        return view('backend/mda/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'IdTpq' => [
                'rules' => 'required|is_unique[tbl_mda.IdTpq]',
                'errors' => [
                    'required' => 'ID TPQ harus di isi',
                    'is_unique' => 'ID TPQ sudah terdaftar'
                ]
            ],
            'NamaTpq' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama MDA harus di isi'
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
            return redirect()->to('/backend/mda/show')->withInput()->with('validation', $validation);
        }

        $this->DataMda->save([
            'IdTpq' => $this->request->getVar('IdTpq'),
            'IdMda' => $this->request->getVar('IdMda') ?? null,
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
        return redirect()->to('/backend/mda/show');
    }

    public function update($id)
    {
        // Gunakan parameter $id sebagai IdTpq
        $idTpq = $id;

        if (!$this->validate([
            'NamaTpq' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama MDA harus di isi'
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
            return redirect()->to('/backend/mda/show')->withInput()->with('validation', $validation);
        }

        // Update data menggunakan ID dari parameter
        $this->DataMda->update($idTpq, [
            'IdMda' => $this->request->getVar('IdMda') ?? null,
            'NamaTpq' => $this->request->getVar('NamaTpq'),
            'Alamat' => $this->request->getVar('AlamatTpq'),
            'TahunBerdiri' => $this->request->getVar('TanggalBerdiri'),
            'KepalaSekolah' => $this->request->getVar('NamaKepTpq'),
            'NoHp' => $this->request->getVar('NoHp'),
            'TempatBelajar' => $this->request->getVar('TempatBelajar')
        ]);

        session()->setFlashdata('pesan', '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data MDA berhasil diupdate 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
        return redirect()->to('/backend/mda/show');
    }

    public function delete($id)
    {
        try {
            $this->DataMda->delete($id);
            session()->setFlashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data Berhasil Di Hapus 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
        } catch (Exception $e) {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gagal menghapus data: ' . $e->getMessage() . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
        }
        return redirect()->to('/backend/mda/show');
    }

    // Method profilLembaga dihapus karena informasi MDA ditampilkan di halaman profilLembaga TPQ
    // Gunakan /backend/tpq/profilLembaga untuk melihat profil TPQ dan MDA

    public function uploadLogo()
    {
        // Ambil ID TPQ dari session atau post
        $idTpq = $this->request->getPost('IdTpq') ?? session('IdTpq');

        if (empty($idTpq)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTpq tidak tersedia'
                ]);
            }
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                IdTpq tidak tersedia
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/tpq/profilLembaga');
        }

        // Buat direktori uploads/logo jika belum ada
        $uploadPath = FCPATH . 'uploads/logo/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Cek apakah ada logo lama di database
        $mdaData = $this->DataMda->GetData($idTpq);
        if (!empty($mdaData) && !empty($mdaData[0]['LogoLembaga'])) {
            $oldLogoPath = $uploadPath . $mdaData[0]['LogoLembaga'];
            // Hapus file logo lama jika ada
            $this->deleteOldFile($oldLogoPath);
        }

        // Cek apakah input adalah base64 image (hasil crop) atau file biasa
        $logoCropped = $this->request->getPost('logo_cropped');

        if (!empty($logoCropped)) {
            // Handle base64 image dari crop
            if (preg_match('/^data:image\/(\w+);base64,/', $logoCropped, $type)) {
                $data = substr($logoCropped, strpos($logoCropped, ',') + 1);
                $data = base64_decode($data);

                if ($data === false) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal decode base64 image'
                        ]);
                    }
                    session()->setFlashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal decode base64 image
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                    return redirect()->to('/backend/tpq/profilLembaga');
                }

                $extension = strtolower($type[1] ?? 'jpg');
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                // Generate nama file baru
                $newFileName = 'logo_mda_' . $idTpq . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataMda->updateLogo($idTpq, $newFileName);

                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Logo berhasil diupload',
                            'logo_url' => base_url('uploads/logo/' . $newFileName)
                        ]);
                    }

                    session()->setFlashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Logo berhasil diupload dan file lama telah dihapus 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                    return redirect()->to('/backend/tpq/profilLembaga');
                } else {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal menyimpan logo'
                        ]);
                    }
                    session()->setFlashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal menyimpan logo
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                    return redirect()->to('/backend/tpq/profilLembaga');
                }
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Format base64 image tidak valid'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Format base64 image tidak valid
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                return redirect()->to('/backend/tpq/profilLembaga');
            }
        } else {
            // Handle file upload biasa (fallback untuk kompatibilitas)
            $file = $this->request->getFile('logo');

            if ($this->validateUploadFile($file) && !empty($idTpq)) {
                // Generate nama file unik
                $newName = 'logo_mda_' . $idTpq . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file logo berdasarkan IdTpq
                    $this->DataMda->updateLogo($idTpq, $newName);

                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Logo berhasil diupload',
                            'logo_url' => base_url('uploads/logo/' . $newName)
                        ]);
                    }

                    session()->setFlashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Logo berhasil diupload dan file lama telah dihapus 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                } else {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal memindahkan file logo'
                        ]);
                    }
                    session()->setFlashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal memindahkan file logo 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                }
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengupload logo. Pastikan file valid dan IdTpq tersedia.'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal mengupload logo. Pastikan file valid dan IdTpq tersedia.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang diupload'
            ]);
        }

        return redirect()->to('/backend/tpq/profilLembaga');
    }

    public function uploadKop()
    {
        // Ambil ID TPQ dari session atau post
        $idTpq = $this->request->getPost('IdTpq') ?? session('IdTpq');

        if (empty($idTpq)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTpq tidak tersedia'
                ]);
            }
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                IdTpq tidak tersedia
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/tpq/profilLembaga');
        }

        // Buat direktori uploads/kop jika belum ada
        $uploadPath = FCPATH . 'uploads/kop/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Cek apakah ada kop lama di database
        $mdaData = $this->DataMda->GetData($idTpq);
        if (!empty($mdaData) && !empty($mdaData[0]['KopLembaga'])) {
            $oldKopPath = $uploadPath . $mdaData[0]['KopLembaga'];
            // Hapus file kop lama jika ada
            $this->deleteOldFile($oldKopPath);
        }

        // Cek apakah input adalah base64 image (hasil crop) atau file biasa
        $kopCropped = $this->request->getPost('kop_lembaga_cropped');

        if (!empty($kopCropped)) {
            // Handle base64 image dari crop
            if (preg_match('/^data:image\/(\w+);base64,/', $kopCropped, $type)) {
                $data = substr($kopCropped, strpos($kopCropped, ',') + 1);
                $data = base64_decode($data);

                if ($data === false) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal decode base64 image'
                        ]);
                    }
                    session()->setFlashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal decode base64 image
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                    return redirect()->to('/backend/tpq/profilLembaga');
                }

                $extension = strtolower($type[1] ?? 'jpg');
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                // Generate nama file baru
                $newFileName = 'kop_mda_' . $idTpq . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataMda->updateKop($idTpq, $newFileName);

                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Kop lembaga berhasil diupload',
                            'kop_url' => base_url('uploads/kop/' . $newFileName)
                        ]);
                    }

                    session()->setFlashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Kop lembaga berhasil diupload dan file lama telah dihapus 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                    return redirect()->to('/backend/tpq/profilLembaga');
                } else {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal menyimpan kop lembaga'
                        ]);
                    }
                    session()->setFlashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal menyimpan kop lembaga
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                    return redirect()->to('/backend/tpq/profilLembaga');
                }
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Format base64 image tidak valid'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Format base64 image tidak valid
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                return redirect()->to('/backend/tpq/profilLembaga');
            }
        } else {
            // Handle file upload biasa (fallback untuk kompatibilitas)
            $file = $this->request->getFile('kop_lembaga');

            if ($this->validateUploadFile($file) && !empty($idTpq)) {
                // Generate nama file unik
                $newName = 'kop_mda_' . $idTpq . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file kop berdasarkan IdTpq
                    $this->DataMda->updateKop($idTpq, $newName);

                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Kop lembaga berhasil diupload',
                            'kop_url' => base_url('uploads/kop/' . $newName)
                        ]);
                    }

                    session()->setFlashdata('pesan', '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Kop lembaga berhasil diupload dan file lama telah dihapus 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                } else {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal memindahkan file kop lembaga'
                        ]);
                    }
                    session()->setFlashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal memindahkan file kop lembaga 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>');
                }
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengupload kop lembaga. Pastikan file valid dan IdTpq tersedia.'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal mengupload kop lembaga. Pastikan file valid dan IdTpq tersedia.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang diupload'
            ]);
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

        $datamda = $this->DataMda->GetData($idTpq);
        if (empty($datamda)) {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data MDA tidak ditemukan 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/tpq/profilLembaga');
        }

        $data = [
            'page_title' => 'Edit Profil Lembaga MDA',
            'mda' => $datamda[0],
            'validation' => \Config\Services::validation()
        ];
        return view('backend/mda/edit', $data);
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

    /**
     * API endpoint untuk mengambil semua data MDA
     * Digunakan untuk AJAX requests
     */
    public function getAll()
    {
        try {
            $data = $this->DataMda->GetData();

            // Format data untuk response JSON
            $response = [];
            foreach ($data as $mda) {
                $response[] = [
                    'IdTpq' => $mda['IdTpq'],
                    'NamaTpq' => $mda['NamaTpq']
                ];
            }

            return $this->response->setJSON($response);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'error' => 'Gagal mengambil data MDA: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

