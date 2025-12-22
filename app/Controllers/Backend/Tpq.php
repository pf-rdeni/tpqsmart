<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TpqModel;
use App\Models\MdaModel;
use App\Models\ToolsModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;


class Tpq extends BaseController
{
    public $DataTpq;
    protected $DataMda;
    protected $toolsModel;

    public function __construct()
    {
        $this->DataTpq = new TpqModel();
        $this->DataMda = new MdaModel();
        $this->toolsModel = new ToolsModel();
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

        // Cek apakah memiliki lembaga MDA
        $hasMda = $this->toolsModel->getSettingAsBool($idTpq, 'MDA_S1_ApakahMemilikiLembagaMDATA', false);

        // Ambil data MDA jika setting aktif
        $datamda = null;
        if ($hasMda) {
            $datamda = $this->DataMda->GetData($idTpq);
        }

        $data = [
            'page_title' => 'Profil Lembaga',
            'tpq' => $datatpq,
            'mda' => $datamda,
            'hasMda' => $hasMda,
            'validation' => \Config\Services::validation()
        ];
        return view('backend/tpq/profilLembaga', $data);
    }

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
        $tpqData = $this->DataTpq->GetData($idTpq);
        if (!empty($tpqData) && !empty($tpqData[0]['LogoLembaga'])) {
            $oldLogoPath = $uploadPath . $tpqData[0]['LogoLembaga'];
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
                $newFileName = 'logo_' . $idTpq . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataTpq->updateLogo($idTpq, $newFileName);

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
                $newName = 'logo_' . $idTpq . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file logo berdasarkan IdTpq
                    $this->DataTpq->updateLogo($idTpq, $newName);

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
        $tpqData = $this->DataTpq->GetData($idTpq);
        if (!empty($tpqData) && !empty($tpqData[0]['KopLembaga'])) {
            $oldKopPath = $uploadPath . $tpqData[0]['KopLembaga'];
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
                $newFileName = 'kop_' . $idTpq . '_' . time() . '.' . $extension;
                $filePath = $uploadPath . $newFileName;

                // Simpan file
                if (file_put_contents($filePath, $data)) {
                    // Update database
                    $this->DataTpq->updateKop($idTpq, $newFileName);

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
                $newName = 'kop_' . $idTpq . '_' . time() . '.' . $file->getExtension();

                // Pindahkan file baru
                if ($file->move($uploadPath, $newName)) {
                    // Update database dengan nama file kop berdasarkan IdTpq
                    $this->DataTpq->updateKop($idTpq, $newName);

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

    /**
     * API endpoint untuk mengambil semua data TPQ
     * Digunakan untuk AJAX requests
     */
    public function getAll()
    {
        try {
            $data = $this->DataTpq->GetData();

            // Format data untuk response JSON
            $response = [];
            foreach ($data as $tpq) {
                $response[] = [
                    'IdTpq' => $tpq['IdTpq'],
                    'NamaTpq' => $tpq['NamaTpq']
                ];
            }

            return $this->response->setJSON($response);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'error' => 'Gagal mengambil data TPQ: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Print PDF Profil Lembaga
     */
    public function printProfilLembaga()
    {
        try {
            // Ambil ID TPQ dari session
            $idTpq = session('IdTpq');

            if (empty($idTpq)) {
                session()->setFlashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    IdTpq tidak tersedia
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>');
                return redirect()->to('/backend/tpq/profilLembaga');
            }

            // Ambil data TPQ
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

            $tpq = $datatpq[0];

            // Siapkan path logo
            $logoPath = null;
            if (!empty($tpq['LogoLembaga'])) {
                $logoFullPath = FCPATH . 'uploads/logo/' . $tpq['LogoLembaga'];
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
                'tpq' => $tpq,
                'logoPath' => $logoPath
            ];

            // Load helper text_format
            helper('text_format');
            
            // Load view untuk PDF
            $html = view('backend/tpq/printProfilLembaga', $data);

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
            // Gunakan ukuran kertas Folio (F4) portrait seperti profil santri
            $dompdf->setPaper('folio', 'portrait');
            $dompdf->render();

            // Format filename
            $filename = 'Profil_Lembaga_' . str_replace(' ', '_', $tpq['NamaTpq']) . '_' . date('Y-m-d') . '.pdf';

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
            log_message('error', 'Tpq: printProfilLembaga - Error: ' . $e->getMessage());
            session()->setFlashdata('pesan', '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Gagal membuat PDF: ' . $e->getMessage() . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>');
            return redirect()->to('/backend/tpq/profilLembaga');
        }
    }
}
