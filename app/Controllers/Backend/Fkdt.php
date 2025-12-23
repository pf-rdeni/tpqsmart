<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\FkdtModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;


class Fkdt extends BaseController
{
    public $DataFkdt;

    public function __construct()
    {
        $this->DataFkdt = new FkdtModel();
    }

    public function show()
    {
        $id = '';
        $datafkdt = $this->DataFkdt->GetData($id);
        $data = [
            'page_title' => 'Data FKDT',
            'fkdt' => $datafkdt,
            'validation' => \Config\Services::validation()
        ];
        return view('backend/fkdt/fkdt', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Form Data Tambah FKDT',
            'validation' => \Config\Services::validation()
        ];

        return view('backend/fkdt/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'IdFkdt' => [
                'rules' => 'required|is_unique[tbl_fkdt.IdFkdt]',
                'errors' => [
                    'required' => 'ID FKDT harus di isi',
                    'is_unique' => 'ID FKDT sudah terdaftar'
                ]
            ],
            'NamaFkdt' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama FKDT harus di isi'
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
            return redirect()->to('/backend/fkdt/create/')->withInput()->with('validation', $validation);
        }

        $this->DataFkdt->save([
            'IdFkdt' => $this->request->getVar('IdFkdt'),
            'NamaFkdt' => $this->request->getVar('NamaFkdt'),
            'Alamat' => $this->request->getVar('AlamatFkdt'),
            'TahunBerdiri' => $this->request->getVar('TanggalBerdiri'),
            'KepalaSekolah' => $this->request->getVar('NamaKepFkdt'),
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
        return redirect()->to('/backend/fkdt/show');
    }

    public function update($id)
    {
        if (!$this->validate([
            'NamaFkdt' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama FKDT harus di isi'
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
            return redirect()->to('/backend/fkdt/edit/' . $id)->withInput()->with('validation', $validation);
        }

        // Update data
        $this->DataFkdt->update($id, [
            'NamaFkdt' => $this->request->getVar('NamaFkdt'),
            'Alamat' => $this->request->getVar('AlamatFkdt'),
            'TahunBerdiri' => $this->request->getVar('TanggalBerdiri'),
            'KepalaSekolah' => $this->request->getVar('NamaKepFkdt'),
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
        return redirect()->to('/backend/fkdt/profil-lembaga/' . $id);
    }

    public function delete($id)
    {
        $this->DataFkdt->delete($id);
        session()->setFlashdata('pesan', '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data Berhasil Di Hapus 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
        return redirect()->to('/backend/fkdt/show');
    }

    public function profilLembaga($id)
    {
        // Ambil data FKDT berdasarkan ID
        $datafkdt = $this->DataFkdt->GetData($id);

        if (empty($datafkdt)) {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data FKDT tidak ditemukan
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkdt/show');
        }

        $data = [
            'page_title' => 'Profil Lembaga FKDT',
            'fkdt' => $datafkdt,
            'validation' => \Config\Services::validation()
        ];
        return view('backend/fkdt/profilLembaga', $data);
    }

    public function uploadLogo()
    {
        // Ambil ID FKDT dari post
        $idFkdt = $this->request->getPost('IdFkdt');

        if (empty($idFkdt)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdFkdt tidak tersedia'
                ]);
            }
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                IdFkdt tidak tersedia
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkdt/show');
        }

        // Buat direktori uploads/logo jika belum ada
        $uploadPath = FCPATH . 'uploads/logo/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Cek apakah ada logo lama di database
        $fkdtData = $this->DataFkdt->GetData($idFkdt);
        if (!empty($fkdtData) && !empty($fkdtData[0]['LogoLembaga'])) {
            $oldLogoPath = $uploadPath . $fkdtData[0]['LogoLembaga'];
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
                    return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
                }

                $extension = strtolower($type[1] ?? 'jpg');
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                // Generate nama file baru
                $newFileName = 'logo_fkdt_' . $idFkdt . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataFkdt->updateLogo($idFkdt, $newFileName);

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
                    return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
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
                    return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
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
                return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
            }
        } else {
            // Handle file upload biasa (fallback untuk kompatibilitas)
            $file = $this->request->getFile('logo');

            if ($this->validateUploadFile($file) && !empty($idFkdt)) {
                // Generate nama file unik
                $newName = 'logo_fkdt_' . $idFkdt . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file logo berdasarkan IdFkdt
                    $this->DataFkdt->updateLogo($idFkdt, $newName);

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
                        'message' => 'Gagal mengupload logo. Pastikan file valid dan IdFkdt tersedia.'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal mengupload logo. Pastikan file valid dan IdFkdt tersedia.
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

        return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
    }

    public function uploadKop()
    {
        // Ambil ID FKDT dari post
        $idFkdt = $this->request->getPost('IdFkdt');

        if (empty($idFkdt)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdFkdt tidak tersedia'
                ]);
            }
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                IdFkdt tidak tersedia
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkdt/show');
        }

        // Buat direktori uploads/kop jika belum ada
        $uploadPath = FCPATH . 'uploads/kop/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Cek apakah ada kop lama di database
        $fkdtData = $this->DataFkdt->GetData($idFkdt);
        if (!empty($fkdtData) && !empty($fkdtData[0]['KopLembaga'])) {
            $oldKopPath = $uploadPath . $fkdtData[0]['KopLembaga'];
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
                    return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
                }

                $extension = strtolower($type[1] ?? 'jpg');
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                // Generate nama file baru
                $newFileName = 'kop_fkdt_' . $idFkdt . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataFkdt->updateKop($idFkdt, $newFileName);

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
                    return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
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
                    return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
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
                return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
            }
        } else {
            // Handle file upload biasa (fallback untuk kompatibilitas)
            $file = $this->request->getFile('kop_lembaga');

            if ($this->validateUploadFile($file) && !empty($idFkdt)) {
                // Generate nama file unik
                $newName = 'kop_fkdt_' . $idFkdt . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file kop berdasarkan IdFkdt
                    $this->DataFkdt->updateKop($idFkdt, $newName);

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
                        'message' => 'Gagal mengupload kop lembaga. Pastikan file valid dan IdFkdt tersedia.'
                    ]);
                }
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal mengupload kop lembaga. Pastikan file valid dan IdFkdt tersedia.
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

        return redirect()->to('/backend/fkdt/profil-lembaga/' . $idFkdt);
    }

    public function edit($id)
    {
        $datafkdt = $this->DataFkdt->GetData($id);
        if (empty($datafkdt)) {
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data FKDT tidak ditemukan 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkdt/show');
        }

        $data = [
            'page_title' => 'Edit Profil Lembaga FKDT',
            'fkdt' => $datafkdt[0],
            'validation' => \Config\Services::validation()
        ];
        return view('backend/fkdt/edit', $data);
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
                    IdFkdt tidak tersedia
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                return redirect()->to('/backend/fkdt/show');
            }

            // Ambil data FKDT
            $datafkdt = $this->DataFkdt->GetData($id);

            if (empty($datafkdt)) {
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Data FKDT tidak ditemukan
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                return redirect()->to('/backend/fkdt/show');
            }

            $fkdt = $datafkdt[0];

            // Siapkan path logo
            $logoPath = null;
            if (!empty($fkdt['LogoLembaga'])) {
                $logoFullPath = FCPATH . 'uploads/logo/' . $fkdt['LogoLembaga'];
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
                'fkdt' => $fkdt,
                'logoPath' => $logoPath
            ];

            // Load helper text_format
            helper('text_format');
            
            // Load view untuk PDF
            $html = view('backend/fkdt/printProfilLembaga', $data);

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
            $filename = 'Profil_Lembaga_FKDT_' . str_replace(' ', '_', $fkdt['NamaFkdt']) . '_' . date('Y-m-d') . '.pdf';

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
            log_message('error', 'Fkdt: printProfilLembaga - Error: ' . $e->getMessage());
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gagal membuat PDF: ' . $e->getMessage() . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/fkdt/show');
        }
    }
}

