<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\FkpqModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;


class Fkpq extends BaseController
{
    public $DataFkpq;

    public function __construct()
    {
        $this->DataFkpq = new FkpqModel();
    }

    public function show()
    {
        $id = '';
        $datafkpq = $this->DataFkpq->GetData($id);
        $data = [
            'page_title' => 'Data FKPQ',
            'fkpq' => $datafkpq,
            'validation' => \Config\Services::validation()
        ];
        return view('backend/fkpq/fkpq', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Form Data Tambah FKPQ',
            'validation' => \Config\Services::validation()
        ];

        return view('backend/fkpq/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'IdFkpq' => [
                'rules' => 'required|is_unique[tbl_fkpq.IdFkpq]',
                'errors' => [
                    'required' => 'ID FKPQ harus di isi',
                    'is_unique' => 'ID FKPQ sudah terdaftar'
                ]
            ],
            'NamaFkpq' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama FKPQ harus di isi'
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
            return redirect()->to('/backend/fkpq/create/')->withInput()->with('validation', $validation);
        }

        $this->DataFkpq->save([
            'IdFkpq' => $this->request->getVar('IdFkpq'),
            'NamaFkpq' => $this->request->getVar('NamaFkpq'),
            'Alamat' => $this->request->getVar('AlamatFkpq'),
            'Kecamatan' => $this->request->getVar('Kecamatan'),
            'TahunBerdiri' => $this->request->getVar('TanggalBerdiri'),
            'KetuaFkpq' => $this->request->getVar('NamaKepFkpq'),
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
        return redirect()->to('/backend/fkpq/show');
    }

    public function update($id)
    {
        if (!$this->validate([
            'NamaFkpq' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama FKPQ harus di isi'
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
            return redirect()->to('/backend/fkpq/edit/' . $id)->withInput()->with('validation', $validation);
        }

        // Update data
        $this->DataFkpq->update($id, [
            'NamaFkpq' => $this->request->getVar('NamaFkpq'),
            'Alamat' => $this->request->getVar('AlamatFkpq'),
            'Kecamatan' => $this->request->getVar('Kecamatan'),
            'TahunBerdiri' => $this->request->getVar('TanggalBerdiri'),
            'KetuaFkpq' => $this->request->getVar('NamaKepFkpq'),
            'NoHp' => $this->request->getVar('NoHp'),
            'TempatBelajar' => $this->request->getVar('TempatBelajar'),
            'Visi' => $this->request->getVar('Visi'),
            'Misi' => $this->request->getVar('Misi')
        ]);

        session()->setFlashdata('pesan', '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Profil lembaga berhasil diupdate 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
        return redirect()->to('/backend/fkpq/profil-lembaga/' . $id);
    }

    public function delete($id)
    {
        $this->DataFkpq->delete($id);
        session()->setFlashdata('pesan', '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Berhasil Di Hapus 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
        return redirect()->to('/backend/fkpq/show');
    }

    public function profilLembaga($id)
    {
        // Ambil data FKPQ berdasarkan ID
        $datafkpq = $this->DataFkpq->GetData($id);

        if (empty($datafkpq)) {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data FKPQ tidak ditemukan
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkpq/show');
        }

        $data = [
            'page_title' => 'Profil Lembaga FKPQ',
            'fkpq' => $datafkpq,
            'validation' => \Config\Services::validation()
        ];
        return view('backend/fkpq/profilLembaga', $data);
    }

    public function uploadLogo()
    {
        // Ambil ID FKPQ dari post
        $idFkpq = $this->request->getPost('IdFkpq');

        if (empty($idFkpq)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdFkpq tidak tersedia'
                ]);
            }
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                IdFkpq tidak tersedia
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkpq/show');
        }

        // Buat direktori uploads/logo jika belum ada
        $uploadPath = FCPATH . 'uploads/logo/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Cek apakah ada logo lama di database
        $fkpqData = $this->DataFkpq->GetData($idFkpq);
        if (!empty($fkpqData) && !empty($fkpqData[0]['LogoLembaga'])) {
            $oldLogoPath = $uploadPath . $fkpqData[0]['LogoLembaga'];
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
                    return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
                }

                $extension = strtolower($type[1] ?? 'jpg');
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                // Generate nama file baru
                $newFileName = 'logo_fkpq_' . $idFkpq . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataFkpq->updateLogo($idFkpq, $newFileName);

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
                    return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
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
                    return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
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
                return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
            }
        } else {
            // Handle file upload biasa (fallback untuk kompatibilitas)
            $file = $this->request->getFile('logo');

            if ($this->validateUploadFile($file) && !empty($idFkpq)) {
                // Generate nama file unik
                $newName = 'logo_fkpq_' . $idFkpq . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file logo berdasarkan IdFkpq
                    $this->DataFkpq->updateLogo($idFkpq, $newName);

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
                        'message' => 'Gagal mengupload logo. Pastikan file valid dan IdFkpq tersedia.'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal mengupload logo. Pastikan file valid dan IdFkpq tersedia.
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

        return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
    }

    public function uploadKop()
    {
        // Ambil ID FKPQ dari post
        $idFkpq = $this->request->getPost('IdFkpq');

        if (empty($idFkpq)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdFkpq tidak tersedia'
                ]);
            }
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                IdFkpq tidak tersedia
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkpq/show');
        }

        // Buat direktori uploads/kop jika belum ada
        $uploadPath = FCPATH . 'uploads/kop/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Cek apakah ada kop lama di database
        $fkpqData = $this->DataFkpq->GetData($idFkpq);
        if (!empty($fkpqData) && !empty($fkpqData[0]['KopLembaga'])) {
            $oldKopPath = $uploadPath . $fkpqData[0]['KopLembaga'];
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
                    return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
                }

                $extension = strtolower($type[1] ?? 'jpg');
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                // Generate nama file baru
                $newFileName = 'kop_fkpq_' . $idFkpq . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataFkpq->updateKop($idFkpq, $newFileName);

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
                    return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
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
                    return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
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
                return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
            }
        } else {
            // Handle file upload biasa (fallback untuk kompatibilitas)
            $file = $this->request->getFile('kop_lembaga');

            if ($this->validateUploadFile($file) && !empty($idFkpq)) {
                // Generate nama file unik
                $newName = 'kop_fkpq_' . $idFkpq . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file kop berdasarkan IdFkpq
                    $this->DataFkpq->updateKop($idFkpq, $newName);

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
                        'message' => 'Gagal mengupload kop lembaga. Pastikan file valid dan IdFkpq tersedia.'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal mengupload kop lembaga. Pastikan file valid dan IdFkpq tersedia.
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

        return redirect()->to('/backend/fkpq/profil-lembaga/' . $idFkpq);
    }

    public function edit($id)
    {
        $datafkpq = $this->DataFkpq->GetData($id);
        if (empty($datafkpq)) {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data FKPQ tidak ditemukan 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkpq/show');
        }

        $data = [
            'page_title' => 'Edit Profil Lembaga FKPQ',
            'fkpq' => $datafkpq[0],
            'validation' => \Config\Services::validation()
        ];
        return view('backend/fkpq/edit', $data);
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
     * Print PDF Profil Lembaga
     */
    public function printProfilLembaga($id)
    {
        try {
            if (empty($id)) {
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    IdFkpq tidak tersedia
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                return redirect()->to('/backend/fkpq/show');
            }

            // Ambil data FKPQ
            $datafkpq = $this->DataFkpq->GetData($id);

            if (empty($datafkpq)) {
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Data FKPQ tidak ditemukan
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                return redirect()->to('/backend/fkpq/show');
            }

            $fkpq = $datafkpq[0];

            // Siapkan path logo
            $logoPath = null;
            if (!empty($fkpq['LogoLembaga'])) {
                $logoFullPath = FCPATH . 'uploads/logo/' . $fkpq['LogoLembaga'];
                if (file_exists($logoFullPath)) {
                    // Convert image to base64 untuk PDF
                    $imageData = file_get_contents($logoFullPath);
                    $logoBase64 = base64_encode($imageData);
                    $imageInfo = getimagesize($logoFullPath);
                    $mimeType = $imageInfo['mime'];
                    $logoPath = 'data:' . $mimeType . ';base64,' . $logoBase64;
                }
            }

            // Siapkan data untuk view
            $data = [
                'fkpq' => $fkpq,
                'logoPath' => $logoPath
            ];

            // Load helper text_format
            helper('text_format');
            
            // Load view untuk PDF
            $html = view('backend/fkpq/printProfilLembaga', $data);

            // Setup Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'print');
            $options->set('isJavascriptEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            // Gunakan ukuran kertas Folio (F4) portrait
            $dompdf->setPaper('folio', 'portrait');
            $dompdf->render();

            // Format filename
            $filename = 'Profil_Lembaga_FKPQ_' . str_replace(' ', '_', $fkpq['NamaFkpq']) . '_' . date('Y-m-d') . '.pdf';

            // Clear output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (Exception $e) {
            log_message('error', 'Fkpq: printProfilLembaga - Error: ' . $e->getMessage());
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gagal membuat PDF: ' . $e->getMessage() . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkpq/show');
        }
    }
}

