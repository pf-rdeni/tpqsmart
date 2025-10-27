<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MunaqosahNilaiModel;
use App\Models\MunaqosahAntrianModel;
use App\Models\MunaqosahBobotNilaiModel;
use App\Models\MunaqosahMateriModel;
use App\Models\MunaqosahPesertaModel;
use App\Models\SantriModel;
use App\Models\TpqModel;
use App\Models\GuruModel;
use App\Models\MateriPelajaranModel;
use App\Models\HelpFunctionModel;
use App\Models\MunaqosahGrupMateriUjiModel;
use App\Models\SantriBaruModel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
class Munaqosah extends BaseController
{
    protected $nilaiMunaqosahModel;
    protected $antrianMunaqosahModel;
    protected $bobotNilaiMunaqosahModel;
    protected $materiMunaqosahModel;
    protected $pesertaMunaqosahModel;
    protected $santriModel;
    protected $tpqModel;
    protected $guruModel;
    protected $materiPelajaranModel;
    protected $helpFunction;
    protected $grupMateriUjiMunaqosahModel;
    protected $santriBaruModel;
    protected $db;
    
    public function __construct()
    {
        $this->nilaiMunaqosahModel = new MunaqosahNilaiModel();
        $this->antrianMunaqosahModel = new MunaqosahAntrianModel();
        $this->bobotNilaiMunaqosahModel = new MunaqosahBobotNilaiModel();
        $this->materiMunaqosahModel = new MunaqosahMateriModel();
        $this->pesertaMunaqosahModel = new MunaqosahPesertaModel();
        $this->santriModel = new SantriModel();
        $this->tpqModel = new TpqModel();
        $this->guruModel = new GuruModel();
        $this->materiPelajaranModel = new MateriPelajaranModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->grupMateriUjiMunaqosahModel = new MunaqosahGrupMateriUjiModel();
        $this->santriBaruModel = new SantriBaruModel();
        $this->db = \Config\Database::connect();
    }

    // ==================== NILAI MUNAQOSAH ====================
    
    public function index()
    {
        // Load helper
        helper('munaqosah');
        
        // Get statistik
        $statistik = getStatistikMunaqosah();
        
        $data = [
            'page_title' => 'Sistem Penilaian Munaqosah',
            'active_menu' => 'munaqosah',
            'statistik' => $statistik
        ];
        return view('backend/Munaqosah/index', $data);
    }

    public function nilai()
    {
        $data = [
            'page_title' => 'Data Nilai Munaqosah',
            'active_menu' => 'munaqosah',
            'nilai' => $this->nilaiMunaqosahModel->getNilaiWithRelations()
        ];
        return view('backend/Munaqosah/listNilai', $data);
    }

    public function inputNilai()
    {
        $data = [
            'page_title' => 'Input Nilai Munaqosah',
            'active_menu' => 'munaqosah',
            'santri' => $this->santriModel->findAll(),
            'tpq' => $this->tpqModel->findAll(),
            'guru' => $this->guruModel->findAll(),
            'materi' => $this->materiMunaqosahModel->getMateriWithRelations()
        ];
        return view('backend/Munaqosah/inputNilai', $data);
    }

    public function saveNilai()
    {
        $rules = [
            'NoPeserta' => 'required',
            'IdSantri' => 'required',
            'IdTpq' => 'required',
            'IdJuri' => 'required',
            'IdTahunAjaran' => 'required',
            'IdMateri' => 'required',
            'KategoriMateriUjian' => 'required',
            'TypeUjian' => 'required',
            'Nilai' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'NoPeserta' => $this->request->getPost('NoPeserta'),
            'IdSantri' => $this->request->getPost('IdSantri'),
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdJuri' => $this->request->getPost('IdJuri'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdMateri' => $this->request->getPost('IdMateri'),
            'KategoriMateriUjian' => $this->request->getPost('KategoriMateriUjian'),
            'TypeUjian' => $this->request->getPost('TypeUjian'),
            'Nilai' => $this->request->getPost('Nilai'),
            'Catatan' => $this->request->getPost('Catatan')
        ];

        if ($this->nilaiMunaqosahModel->save($data)) {
            return redirect()->to('/backend/munaqosah/nilai')->with('success', 'Data nilai berhasil disimpan');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->nilaiMunaqosahModel->errors());
        }
    }

    public function editNilai($id)
    {
        $data = [
            'page_title' => 'Edit Nilai Munaqosah',
            'active_menu' => 'munaqosah',
            'nilai' => $this->nilaiMunaqosahModel->find($id),
            'santri' => $this->santriModel->findAll(),
            'tpq' => $this->tpqModel->findAll(),
            'guru' => $this->guruModel->findAll(),
            'materi' => $this->materiMunaqosahModel->getMateriWithRelations()
        ];
        return view('backend/Munaqosah/editNilai', $data);
    }

    public function updateNilai($id)
    {
        $rules = [
            'NoPeserta' => 'required',
            'IdSantri' => 'required',
            'IdTpq' => 'required',
            'IdJuri' => 'required',
            'IdTahunAjaran' => 'required',
            'IdMateri' => 'required',
            'KategoriMateriUjian' => 'required',
            'TypeUjian' => 'required',
            'Nilai' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'NoPeserta' => $this->request->getPost('NoPeserta'),
            'IdSantri' => $this->request->getPost('IdSantri'),
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdJuri' => $this->request->getPost('IdJuri'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdMateri' => $this->request->getPost('IdMateri'),
            'KategoriMateriUjian' => $this->request->getPost('KategoriMateriUjian'),
            'TypeUjian' => $this->request->getPost('TypeUjian'),
            'Nilai' => $this->request->getPost('Nilai'),
            'Catatan' => $this->request->getPost('Catatan')
        ];

        if ($this->nilaiMunaqosahModel->update($id, $data)) {
            return redirect()->to('/backend/munaqosah/nilai')->with('success', 'Data nilai berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->nilaiMunaqosahModel->errors());
        }
    }

    public function deleteNilai($id)
    {
        if ($this->nilaiMunaqosahModel->delete($id)) {
            return redirect()->to('/backend/munaqosah/nilai')->with('success', 'Data nilai berhasil dihapus');
        } else {
            return redirect()->to('/backend/munaqosah/nilai')->with('error', 'Gagal menghapus data nilai');
        }
    }

    // ==================== ANTRIAN MUNAQOSAH ====================

    public function antrian()
    {
        $data = [
            'page_title' => 'Data Antrian Munaqosah',
            'active_menu' => 'munaqosah',
            'antrian' => $this->antrianMunaqosahModel->findAll()
        ];
        return view('backend/Munaqosah/listAntrian', $data);
    }

    public function inputAntrian()
    {
        $data = [
            'page_title' => 'Input Antrian Munaqosah',
            'active_menu' => 'munaqosah'
        ];
        return view('backend/Munaqosah/inputAntrian', $data);
    }

    public function saveAntrian()
    {
        $rules = [
            'NoPeserta' => 'required',
            'IdTahunAjaran' => 'required',
            'KategoriMateriUjian' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'NoPeserta' => $this->request->getPost('NoPeserta'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'KategoriMateriUjian' => $this->request->getPost('KategoriMateriUjian'),
            'Status' => false,
            'Keterangan' => $this->request->getPost('Keterangan')
        ];

        if ($this->antrianMunaqosahModel->save($data)) {
            return redirect()->to('/backend/munaqosah/antrian')->with('success', 'Data antrian berhasil disimpan');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->antrianMunaqosahModel->errors());
        }
    }

    public function updateStatusAntrian($id)
    {
        $status = $this->request->getPost('status');
        if ($this->antrianMunaqosahModel->updateStatus($id, $status)) {
            return redirect()->to('/backend/munaqosah/antrian')->with('success', 'Status antrian berhasil diupdate');
        } else {
            return redirect()->to('/backend/munaqosah/antrian')->with('error', 'Gagal mengupdate status antrian');
        }
    }

    public function deleteAntrian($id)
    {
        if ($this->antrianMunaqosahModel->delete($id)) {
            return redirect()->to('/backend/munaqosah/antrian')->with('success', 'Data antrian berhasil dihapus');
        } else {
            return redirect()->to('/backend/munaqosah/antrian')->with('error', 'Gagal menghapus data antrian');
        }
    }

    // ==================== BOBOT NILAI ====================

    public function bobotNilai()
    {
        // Ambil semua data bobot nilai
        $bobotData = $this->bobotNilaiMunaqosahModel->orderBy('IdTahunAjaran', 'ASC')
                                                   ->orderBy('id', 'ASC')
                                                   ->findAll();
        
        $data = [
            'page_title' => 'Data Bobot Nilai Munaqosah',
            'active_menu' => 'munaqosah',
            'bobot' => $bobotData
        ];
        return view('backend/Munaqosah/listBobotNilai', $data);
    }

    public function saveBobotNilai()
    {
        $rules = [
            'IdTahunAjaran' => 'required',
            'KategoriMateriUjian' => 'required',
            'NilaiBobot' => 'required|decimal|greater_than[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'KategoriMateriUjian' => $this->request->getPost('KategoriMateriUjian'),
            'NilaiBobot' => $this->request->getPost('NilaiBobot')
        ];

        if ($this->bobotNilaiMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data bobot nilai berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->bobotNilaiMunaqosahModel->errors()
            ]);
        }
    }

    public function updateBobotNilai($id)
    {
        $rules = [
            'IdTahunAjaran' => 'required',
            'KategoriMateriUjian' => 'required',
            'NilaiBobot' => 'required|decimal|greater_than[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'KategoriMateriUjian' => $this->request->getPost('KategoriMateriUjian'),
            'NilaiBobot' => $this->request->getPost('NilaiBobot')
        ];

        if ($this->bobotNilaiMunaqosahModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data bobot nilai berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->bobotNilaiMunaqosahModel->errors()
            ]);
        }
    }

    public function deleteBobotNilai($id)
    {
        if ($this->bobotNilaiMunaqosahModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data bobot nilai berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    // ==================== PESERTA MUNAQOSAH ====================

    public function pesertaMunaqosah()
    {
        // ambil tahun ajaran saat ini
        $tahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        // IdTpq dari session
        $idTpq = session()->get('IdTpq');
        // DataKelas dari help function model
        $dataKelas = $this->helpFunction->getDataKelas();
        $dataTpq = $this->helpFunction->getDataTpq($idTpq);
        $peserta = $this->pesertaMunaqosahModel->getPesertaWithRelations($idTpq);
        $data = [
            'page_title' => 'Data Peserta Munaqosah',
            'active_menu' => 'munaqosah',
            'peserta' => $peserta,
            'dataKelas' => $dataKelas,
            'dataTpq' => $dataTpq,
            'tahunAjaran' => $tahunAjaran
        ];
        return view('backend/Munaqosah/listPesertaMunaqosah', $data);
    }

    public function savePesertaMunaqosah()
    {
        $rules = [
            'IdSantri' => 'required',
            'IdTpq' => 'required',
            'IdTahunAjaran' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Cek apakah santri sudah terdaftar
        if ($this->pesertaMunaqosahModel->isPesertaExists(
            $this->request->getPost('IdSantri'),
            $this->request->getPost('IdTahunAjaran')
        )) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Santri sudah terdaftar sebagai peserta munaqosah'
            ]);
        }

        $data = [
            'IdSantri' => $this->request->getPost('IdSantri'),
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran')
        ];

        if ($this->pesertaMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data peserta berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->pesertaMunaqosahModel->errors()
            ]);
        }
    }

    public function savePesertaMunaqosahMultiple()
    {
        $rules = [
            'santri_ids' => 'required',
            'IdTpq' => 'required',
            'IdTahunAjaran' => 'required'
        ];

        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            $detailedErrors = [];
            
            // Detail error untuk setiap field
            foreach ($validationErrors as $field => $error) {
                switch ($field) {
                    case 'santri_ids':
                        $detailedErrors[] = "Field 'santri_ids' tidak boleh kosong. Pastikan Anda telah memilih minimal satu santri.";
                        break;
                    case 'IdTpq':
                        $detailedErrors[] = "Field 'IdTpq' tidak boleh kosong. Pastikan TPQ telah dipilih.";
                        break;
                    case 'IdTahunAjaran':
                        $detailedErrors[] = "Field 'IdTahunAjaran' tidak boleh kosong. Pastikan tahun ajaran telah dipilih.";
                        break;
                    default:
                        $detailedErrors[] = "Field '{$field}': {$error}";
                        break;
                }
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validationErrors,
                'detailed_errors' => $detailedErrors,
                'error_count' => count($validationErrors)
            ]);
        }

        $santriIds = $this->request->getPost('santri_ids');
        $idTpqList = $this->request->getPost('IdTpq'); // Sekarang berupa array
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran');

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        // Buat mapping IdTpq untuk setiap santri
        $santriTpqMap = [];
        
        // Ambil data santri untuk mendapatkan IdTpq mereka
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('IdSantri, IdTpq');
        $builder->whereIn('IdSantri', $santriIds);
        $santriData = $builder->get()->getResultArray();
        
        // Buat mapping IdSantri -> IdTpq
        foreach ($santriData as $santri) {
            $santriTpqMap[$santri['IdSantri']] = $santri['IdTpq'];
        }

        foreach ($santriIds as $idSantri) {
            // Cek apakah santri sudah terdaftar
            if ($this->pesertaMunaqosahModel->isPesertaExists($idSantri, $idTahunAjaran)) {
                $errorCount++;
                $errors[] = "Santri ID {$idSantri} sudah terdaftar";
                continue;
            }

            // Gunakan IdTpq dari data santri, bukan dari input
            $idTpq = $santriTpqMap[$idSantri] ?? null;
            
            if (!$idTpq) {
                $errorCount++;
                $errors[] = "Santri ID {$idSantri} tidak memiliki data TPQ yang valid";
                continue;
            }

            $data = [
                'IdSantri' => $idSantri,
                'IdTpq' => $idTpq,
                'IdTahunAjaran' => $idTahunAjaran
            ];

            if ($this->pesertaMunaqosahModel->save($data)) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Gagal menyimpan santri ID {$idSantri}";
            }
        }

        if ($successCount > 0) {
            $message = "Berhasil menyimpan {$successCount} peserta";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} gagal";
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'detailed_errors' => $errors
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan semua data',
                'detailed_errors' => $errors
            ]);
        }
    }


    // ==================== MATERI MUNAQOSAH ====================

    public function materiMunaqosah()
    {
        // Get materi relation dari materiMunaqosahModel
        $materi = $this->materiMunaqosahModel->getMateriWithRelations();
        // Get materi pelajaran dari materiPelajaranModel
        $materiPelajaran = $this->materiPelajaranModel->findAll();
        // Get grup materi aktif
        $grupMateriAktif = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();
        $data = [
            'page_title' => 'Data Materi Munaqosah',
            'active_menu' => 'munaqosah',
            'materi' => $materi,
            'materiPelajaran' => $materiPelajaran,
            'grupMateriAktif' => $grupMateriAktif
        ];
        return view('backend/Munaqosah/listMateriMunaqosah', $data);
    }

    public function saveMateriMunaqosah()
    {
        $rules = [
            'IdMateri' => 'required',
            'KategoriMateriUjian' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'IdMateri' => $this->request->getPost('IdMateri'),
            'KategoriMateriUjian' => $this->request->getPost('KategoriMateriUjian')
        ];

        if ($this->materiMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->materiMunaqosahModel->errors()
            ]);
        }
    }

    public function saveMateriBatch()
    {
        $materiArray = $this->request->getPost('materi');

        if (!is_array($materiArray) || empty($materiArray)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal satu materi'
            ]);
        }

        // Validasi data
        $validMateri = [];
        $errors = [];

        foreach ($materiArray as $materi) {
            if (!isset($materi['IdMateri']) || !isset($materi['KategoriMateri']) || !isset($materi['IdGrupMateriUjian'])) {
                $errors[] = "Data materi tidak lengkap";
                continue;
            }

            if (empty($materi['IdMateri']) || empty($materi['KategoriMateri']) || empty($materi['IdGrupMateriUjian'])) {
                $errors[] = "ID Materi, Kategori Materi, dan ID Grup Materi Ujian harus diisi";
                continue;
            }

            $validMateri[] = $materi;
        }

        if (empty($validMateri)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data materi yang valid',
                'errors' => $errors
            ]);
        }

        // Cek duplikasi
        $idMateriArray = array_column($validMateri, 'IdMateri');
        $duplicateMateri = $this->materiMunaqosahModel->checkDuplicateMateri($idMateriArray);
        
        if (!empty($duplicateMateri)) {
            // Ambil info materi yang duplikat
            $materiInfo = $this->materiMunaqosahModel->getMateriInfo($duplicateMateri);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terdapat materi yang sudah ada di sistem',
                'duplicate_check' => true,
                'duplicate_materi' => $duplicateMateri,
                'materi_info' => $materiInfo,
                'errors' => $errors
            ]);
        }

        // Simpan data jika tidak ada duplikasi
        $savedCount = 0;
        $saveErrors = [];

        foreach ($validMateri as $materi) {
            $data = [
                'IdMateri' => $materi['IdMateri'],
                'KategoriMateri' => $materi['KategoriMateri'],
                'IdGrupMateriUjian' => $materi['IdGrupMateriUjian'],
                'Status' => 'Aktif'
            ];

            if ($this->materiMunaqosahModel->save($data)) {
                $savedCount++;
            } else {
                $saveErrors[] = "Gagal menyimpan materi ID: " . $materi['IdMateri'];
            }
        }
        
        if ($savedCount > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil menyimpan $savedCount materi",
                'saved_count' => $savedCount,
                'errors' => array_merge($errors, $saveErrors)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan semua materi',
            'errors' => array_merge($errors, $saveErrors)
        ]);
    }

    public function saveMateriBatchWithConfirmation()
    {
        $materiArray = $this->request->getPost('materi');
        $skipDuplicates = $this->request->getPost('skip_duplicates') === 'true';

        if (!is_array($materiArray) || empty($materiArray)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal satu materi'
            ]);
        }

        // Validasi data
        $validMateri = [];
        $errors = [];

        foreach ($materiArray as $materi) {
            if (!isset($materi['IdMateri']) || !isset($materi['KategoriMateri']) || !isset($materi['IdGrupMateriUjian'])) {
                $errors[] = "Data materi tidak lengkap";
                continue;
            }

            if (empty($materi['IdMateri']) || empty($materi['KategoriMateri']) || empty($materi['IdGrupMateriUjian'])) {
                $errors[] = "ID Materi, Kategori Materi, dan ID Grup Materi Ujian harus diisi";
                continue;
            }

            $validMateri[] = $materi;
        }

        if (empty($validMateri)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data materi yang valid',
                'errors' => $errors
            ]);
        }

        // Cek duplikasi
        $idMateriArray = array_column($validMateri, 'IdMateri');
        $duplicateMateri = $this->materiMunaqosahModel->checkDuplicateMateri($idMateriArray);
        
        if (!empty($duplicateMateri) && !$skipDuplicates) {
            // Ambil info materi yang duplikat
            $materiInfo = $this->materiMunaqosahModel->getMateriInfo($duplicateMateri);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terdapat materi yang sudah ada di sistem',
                'duplicate_check' => true,
                'duplicate_materi' => $duplicateMateri,
                'materi_info' => $materiInfo,
                'errors' => $errors
            ]);
        }

        // Filter materi yang tidak duplikat jika skip_duplicates = true
        $materiToSave = $validMateri;
        if ($skipDuplicates && !empty($duplicateMateri)) {
            $materiToSave = array_filter($validMateri, function($materi) use ($duplicateMateri) {
                return !in_array($materi['IdMateri'], $duplicateMateri);
            });
        }

        // Simpan data
        $savedCount = 0;
        $saveErrors = [];

        foreach ($materiToSave as $materi) {
            $data = [
                'IdMateri' => $materi['IdMateri'],
                'KategoriMateri' => $materi['KategoriMateri'],
                'IdGrupMateriUjian' => $materi['IdGrupMateriUjian'],
                'Status' => 'Aktif'
            ];

            if ($this->materiMunaqosahModel->save($data)) {
                $savedCount++;
            } else {
                $saveErrors[] = "Gagal menyimpan materi ID: " . $materi['IdMateri'];
            }
        }

        $message = "Berhasil menyimpan $savedCount materi";
        if ($skipDuplicates && !empty($duplicateMateri)) {
            $skippedCount = count($duplicateMateri);
            $message .= " (Melewati $skippedCount materi yang sudah ada)";
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'saved_count' => $savedCount,
            'skipped_count' => $skipDuplicates && !empty($duplicateMateri) ? count($duplicateMateri) : 0,
            'errors' => array_merge($errors, $saveErrors)
        ]);
    }

    public function updateMateriMunaqosah($id)
    {
        $rules = [
            'IdGrupMateriUjian' => 'required',
            'Status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'IdGrupMateriUjian' => $this->request->getPost('IdGrupMateriUjian'),
            'Status' => $this->request->getPost('Status')
        ];

        if ($this->materiMunaqosahModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->materiMunaqosahModel->errors()
            ]);
        }
    }

    public function deleteMateriMunaqosah($id)
    {
        // Ambil data materi yang akan dihapus
        $materi = $this->materiMunaqosahModel->find($id);
        if (!$materi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data materi tidak ditemukan'
            ]);
        }

        // Cek apakah IdMateri sudah digunakan di tabel nilai
        $isUsed = $this->materiMunaqosahModel->checkMateriUsedInNilai($materi['IdMateri']);
        
        if ($isUsed) {
            // Ambil informasi penggunaan
            $usageInfo = $this->materiMunaqosahModel->getMateriUsageInfo($materi['IdMateri']);
            $usageCount = $usageInfo ? $usageInfo->usage_count : 0;
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Materi tidak dapat dihapus karena sudah digunakan',
                'blocked_delete' => true,
                'usage_count' => $usageCount,
                'materi_info' => [
                    'IdMateri' => $materi['IdMateri'],
                    'IdGrupMateriUjian' => $materi['IdGrupMateriUjian']
                ]
            ]);
        }

        // Jika tidak digunakan, lanjutkan delete
        if ($this->materiMunaqosahModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    // ==================== GRUP MATERI UJIAN ====================

    public function grupMateriUjian()
    {
        $data = [
            'page_title' => 'Data Grup Materi Ujian',
            'active_menu' => 'munaqosah',
            'grupMateri' => $this->grupMateriUjiMunaqosahModel->findAll()
        ];
        return view('backend/Munaqosah/listIdGrupMateriUjian', $data);
    }

    public function saveIdGrupMateriUjian()
    {
        $rules = [
            'IdIdGrupMateriUjian' => 'required|max_length[50]|is_unique[tbl_munaqosah_grup_materi_uji.IdIdGrupMateriUjian]',
            'NamaMateriGrup' => 'required|max_length[100]',
            'Status' => 'required|in_list[Aktif,Tidak Aktif]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Konversi nama materi grup ke huruf kapital
        $namaMateriGrup = strtoupper($this->request->getPost('NamaMateriGrup'));
        
        // Cek apakah nama grup materi sudah ada (case insensitive)
        $existingGrup = $this->grupMateriUjiMunaqosahModel->checkNamaMateriGrupExists($namaMateriGrup);
        
        if ($existingGrup) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama grup materi sudah ada',
                'duplicate_name' => true,
                'existing_name' => $existingGrup->NamaMateriGrup,
                'suggestion' => 'Gunakan nama yang berbeda untuk grup materi ini'
            ]);
        }

        $data = [
            'IdIdGrupMateriUjian' => $this->request->getPost('IdIdGrupMateriUjian'),
            'NamaMateriGrup' => $namaMateriGrup,
            'Status' => $this->request->getPost('Status')
        ];

        if ($this->grupMateriUjiMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data grup materi ujian berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->grupMateriUjiMunaqosahModel->errors()
            ]);
        }
    }

    public function updateIdGrupMateriUjian($id)
    {
        $rules = [
            'NamaMateriGrup' => 'required|max_length[100]',
            'Status' => 'required|in_list[Aktif,Tidak Aktif]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Konversi nama materi grup ke huruf kapital
        $namaMateriGrup = strtoupper($this->request->getPost('NamaMateriGrup'));
        
        // Cek apakah nama grup materi sudah ada (case insensitive) - exclude current record
        $existingGrup = $this->grupMateriUjiMunaqosahModel->checkNamaMateriGrupExists($namaMateriGrup, $id);
        
        if ($existingGrup) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama grup materi sudah ada',
                'duplicate_name' => true,
                'existing_name' => $existingGrup->NamaMateriGrup,
                'suggestion' => 'Gunakan nama yang berbeda untuk grup materi ini'
            ]);
        }

        $data = [
            'NamaMateriGrup' => $namaMateriGrup,
            'Status' => $this->request->getPost('Status')
        ];

        if ($this->grupMateriUjiMunaqosahModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data grup materi ujian berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->grupMateriUjiMunaqosahModel->errors()
            ]);
        }
    }

    public function deleteIdGrupMateriUjian($id)
    {
        // Ambil data grup materi yang akan dihapus
        $grupMateri = $this->grupMateriUjiMunaqosahModel->find($id);
        if (!$grupMateri) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data grup materi tidak ditemukan'
            ]);
        }

        // Cek apakah IdIdGrupMateriUjian sudah digunakan di tabel materi
        $isUsed = $this->grupMateriUjiMunaqosahModel->checkGrupMateriUsed($grupMateri['IdIdGrupMateriUjian']);
        
        if ($isUsed) {
            // Ambil informasi penggunaan
            $usageInfo = $this->grupMateriUjiMunaqosahModel->getGrupMateriUsageInfo($grupMateri['IdIdGrupMateriUjian']);
            $usageCount = $usageInfo ? $usageInfo->usage_count : 0;
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Grup materi tidak dapat dihapus karena sudah digunakan',
                'blocked_delete' => true,
                'usage_count' => $usageCount,
                'grup_info' => [
                    'IdIdGrupMateriUjian' => $grupMateri['IdIdGrupMateriUjian'],
                    'NamaMateriGrup' => $grupMateri['NamaMateriGrup']
                ]
            ]);
        }

        // Jika tidak digunakan, lanjutkan delete
        if ($this->grupMateriUjiMunaqosahModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data grup materi berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function getGrupMateriAktif()
    {
        $grupMateri = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();
        return $this->response->setJSON($grupMateri);
    }

    public function getNextIdIdGrupMateriUjian()
    {
        $nextId = $this->grupMateriUjiMunaqosahModel->generateNextIdIdGrupMateriUjian();
        return $this->response->setJSON([
            'success' => true,
            'next_id' => $nextId
        ]);
    }

    // ==================== API METHODS ====================

    public function getSantriData($idKelas, $idTpq)
    {
        // Ambil IdTpq dari session jika user bukan admin
        $sessionIdTpq = session()->get('IdTpq');
        
        // Jika ada session IdTpq (user bukan admin), gunakan session IdTpq
        if ($sessionIdTpq) {
            $idTpq = $sessionIdTpq;
        }
        
        // Handle parameter 0 untuk "semua"
        $filterTpq = ($idTpq == 0) ? 0 : $idTpq;
        $filterKelas = ($idKelas == 0) ? 0 : $idKelas;
        
        $santri = $this->helpFunction->getDataSantriStatus(1, $filterTpq, $filterKelas);
        return $this->response->setJSON($santri);
    }

    public function getTpqData()
    {
        $tpq = $this->tpqModel->findAll();
        return $this->response->setJSON($tpq);
    }

    public function getGuruData()
    {
        $guru = $this->guruModel->findAll();
        return $this->response->setJSON($guru);
    }

    public function getMateriData()
    {
        $materi = $this->materiPelajaranModel->findAll();
        return $this->response->setJSON($materi);
    }

    public function getStatistikData()
    {
        helper('munaqosah');
        $statistik = getStatistikMunaqosah();
        return $this->response->setJSON($statistik);
    }

    public function getNilaiByPeserta($noPeserta)
    {
        $nilai = $this->nilaiMunaqosahModel->getNilaiByPeserta($noPeserta);
        return $this->response->setJSON($nilai);
    }

    public function getAntrianByStatus($status)
    {
        $tahunAjaran = session()->get('IdTahunAjaran') ?? '2024/2025';
        
        if ($status == 'belum') {
            $antrian = $this->antrianMunaqosahModel->getAntrianBelumSelesai($tahunAjaran);
        } else {
            $antrian = $this->antrianMunaqosahModel->getAntrianSelesai($tahunAjaran);
        }
        
        return $this->response->setJSON($antrian);
    }

    public function getBobotByTahunAjaran($tahunAjaran)
    {
        $bobot = $this->bobotNilaiMunaqosahModel->getBobotByTahunAjaran($tahunAjaran);
        return $this->response->setJSON($bobot);
    }

    public function getPesertaByTpq($idTpq)
    {
        $tahunAjaran = session()->get('IdTahunAjaran') ?? '2024/2025';
        $peserta = $this->pesertaMunaqosahModel->getPesertaByTpq($idTpq, $tahunAjaran);
        return $this->response->setJSON($peserta);
    }

    public function checkDataTerkait($idSantri)
    {
        try {
            $dataTerkait = [];
            $db = \Config\Database::connect();
            
            // Cek di tbl_nilai_munaqosah
            $nilaiMunaqosah = $db->table('tbl_munaqosah_nilai')
                ->where('IdSantri', $idSantri)
                ->get()
                ->getResultArray();
            
            if (!empty($nilaiMunaqosah)) {
                $dataTerkait['nilai_munaqosah'] = [
                    'count' => count($nilaiMunaqosah),
                    'data' => $nilaiMunaqosah
                ];
            }
            
            // Cek di tbl_munaqosah_antrian
            $antrianMunaqosah = $db->table('tbl_munaqosah_antrian')
                ->where('IdSantri', $idSantri)
                ->get()
                ->getResultArray();
            
            if (!empty($antrianMunaqosah)) {
                $dataTerkait['antrian_munaqosah'] = [
                    'count' => count($antrianMunaqosah),
                    'data' => $antrianMunaqosah
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data_terkait' => $dataTerkait,
                'total_terkait' => count($dataTerkait)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengecek data terkait: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePesertaMunaqosah($id)
    {
        try {
            // Ambil data peserta untuk mendapatkan IdSantri
            $peserta = $this->pesertaMunaqosahModel->find($id);
            if (!$peserta) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data peserta tidak ditemukan'
                ]);
            }
            
            $idSantri = $peserta['IdSantri'];
            $db = \Config\Database::connect();
            
            // Hapus data terkait terlebih dahulu
            $db->table('tbl_munaqosah_nilai')->where('IdSantri', $idSantri)->delete();
            $db->table('tbl_munaqosah_antrian')->where('IdSantri', $idSantri)->delete();
            
            // Hapus peserta munaqosah
            $this->pesertaMunaqosahModel->delete($id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peserta dan semua data terkait berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePesertaBySantri($idSantri)
    {
        try {
            // Cari peserta munaqosah berdasarkan IdSantri
            $peserta = $this->pesertaMunaqosahModel->where('IdSantri', $idSantri)->first();
            if (!$peserta) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data peserta tidak ditemukan'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Hapus data terkait terlebih dahulu
            $db->table('tbl_munaqosah_nilai')->where('IdSantri', $idSantri)->delete();
            $db->table('tbl_munaqosah_antrian')->where('IdSantri', $idSantri)->delete();
            
            // Hapus peserta munaqosah
            $this->pesertaMunaqosahModel->where('IdSantri', $idSantri)->delete();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peserta dan semua data terkait berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
            ]);
        }
    }

    public function updateStatusMateri($id)
    {
        try {
            // Debug: Log input data
            log_message('debug', 'Update Status Request - ID: ' . $id);
            log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));
            log_message('debug', 'Request Method: ' . $this->request->getMethod());
            log_message('debug', 'Request URI: ' . $this->request->getUri());
            
            $rules = [
                'status' => 'required|in_list[Aktif,Tidak Aktif]'
            ];

            if (!$this->validate($rules)) {
                log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if record exists
            $materi = $this->materiMunaqosahModel->find($id);
            if (!$materi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data materi tidak ditemukan'
                ]);
            }

            $data = [
                'Status' => $this->request->getPost('status')
            ];

            log_message('debug', 'Update data: ' . json_encode($data));

            if ($this->materiMunaqosahModel->update($id, $data)) {
                log_message('info', 'Status updated successfully for ID: ' . $id);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status materi berhasil diupdate'
                ]);
            } else {
                log_message('error', 'Failed to update status: ' . json_encode($this->materiMunaqosahModel->errors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate status materi',
                    'errors' => $this->materiMunaqosahModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in updateStatusMateri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function testUpdateStatus($id)
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Test route berfungsi',
            'id' => $id,
            'method' => $this->request->getMethod(),
            'data' => $this->request->getPost()
        ]);
    }

    public function updateGrupMateri($id)
    {
        try {
            // Debug: Log input data
            log_message('debug', 'Update Grup Materi Request - ID: ' . $id);
            log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));
            log_message('debug', 'Request Method: ' . $this->request->getMethod());
            log_message('debug', 'Request URI: ' . $this->request->getUri());
            
            $rules = [
                'IdGrupMateriUjian' => 'required|max_length[50]'
            ];

            if (!$this->validate($rules)) {
                log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if record exists
            $materi = $this->materiMunaqosahModel->find($id);
            if (!$materi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data materi tidak ditemukan'
                ]);
            }

            // Check if new grup exists
            $grupExists = $this->grupMateriUjiMunaqosahModel->where('IdGrupMateriUjian', $this->request->getPost('IdGrupMateriUjian'))->first();
            if (!$grupExists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Grup materi ujian tidak ditemukan'
                ]);
            }

            $data = [
                'IdGrupMateriUjian' => $this->request->getPost('IdGrupMateriUjian')
            ];

            log_message('debug', 'Update data: ' . json_encode($data));

            if ($this->materiMunaqosahModel->update($id, $data)) {
                log_message('info', 'Grup materi updated successfully for ID: ' . $id);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Grup materi ujian berhasil diupdate'
                ]);
            } else {
                log_message('error', 'Failed to update grup materi: ' . json_encode($this->materiMunaqosahModel->errors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate grup materi ujian',
                    'errors' => $this->materiMunaqosahModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in updateGrupMateri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function saveBobotBatch()
    {
        try {
            $data = $this->request->getPost('data');
            
            if (!$data || !is_array($data)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak valid'
                ]);
            }

            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            // Validasi data
            foreach ($data as $item) {
                if (empty($item['IdTahunAjaran']) || empty($item['KategoriMateriUjian']) || !isset($item['NilaiBobot'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Data tidak lengkap'
                    ]);
                }
            }

            // Hapus data lama untuk tahun ajaran yang sama
            $tahunAjaran = $data[0]['IdTahunAjaran'];
            $bobotNilaiModel->where('IdTahunAjaran', $tahunAjaran)->delete();

            // Simpan data baru
            if ($bobotNilaiModel->insertBatch($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data bobot nilai berhasil disimpan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data bobot nilai'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteBobotByTahun()
    {
        try {
            $tahunAjaran = $this->request->getPost('IdTahunAjaran');
            
            if (!$tahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran tidak boleh kosong'
                ]);
            }

            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            if ($bobotNilaiModel->where('IdTahunAjaran', $tahunAjaran)->delete()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data bobot nilai berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data bobot nilai'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getDefaultBobot()
    {
        try {
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            // Ambil data default dari database
            $defaultData = $bobotNilaiModel->where('IdTahunAjaran', 'Default')
                                         ->orderBy('id', 'ASC')
                                         ->findAll();
            
            if (empty($defaultData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data default tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $defaultData,
                'message' => 'Data default berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function duplicateDefaultBobot()
    {
        try {
            $tahunAjaran = $this->request->getPost('IdTahunAjaran');
            
            if (!$tahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran tidak boleh kosong'
                ]);
            }

            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            // Ambil data default
            $defaultData = $bobotNilaiModel->where('IdTahunAjaran', 'Default')
                                         ->orderBy('id', 'ASC')
                                         ->findAll();
            
            if (empty($defaultData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data default tidak ditemukan'
                ]);
            }

            // Cek apakah tahun ajaran sudah ada
            $existingData = $bobotNilaiModel->where('IdTahunAjaran', $tahunAjaran)->first();
            if ($existingData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data untuk tahun ajaran ' . $tahunAjaran . ' sudah ada'
                ]);
            }

            // Duplicate data default dengan tahun ajaran baru
            $duplicateData = [];
            foreach ($defaultData as $item) {
                $duplicateData[] = [
                    'IdTahunAjaran' => $tahunAjaran,
                    'KategoriMateriUjian' => $item['KategoriMateriUjian'],
                    'NilaiBobot' => $item['NilaiBobot'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }

            // Simpan data duplicate
            if ($bobotNilaiModel->insertBatch($duplicateData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data default berhasil diduplikasi untuk tahun ajaran ' . $tahunAjaran
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menduplikasi data default'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getBobotByTahun($tahunAjaran)
    {
        try {
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            // Ambil data berdasarkan tahun ajaran
            $data = $bobotNilaiModel->where('IdTahunAjaran', $tahunAjaran)
                                   ->orderBy('id', 'ASC')
                                   ->findAll();
            
            if (empty($data)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data untuk tahun ajaran ' . $tahunAjaran . ' tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'message' => 'Data berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getTahunAjaranOptions()
    {
        try {
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            $data = $bobotNilaiModel->select('IdTahunAjaran')
                                   ->groupBy('IdTahunAjaran')
                                   ->orderBy('IdTahunAjaran', 'ASC')
                                   ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'message' => 'Data tahun ajaran berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function duplicateBobotData()
    {
        try {
            $sourceTahunAjaran = $this->request->getPost('sourceTahunAjaran');
            $targetTahunAjaran = $this->request->getPost('targetTahunAjaran');
            
            if (!$sourceTahunAjaran || !$targetTahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran sumber dan target harus diisi'
                ]);
            }
            
            if ($sourceTahunAjaran === $targetTahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran target tidak boleh sama dengan sumber'
                ]);
            }
            
            // Validasi format tahun ajaran target (harus 8 digit angka)
            if (!preg_match('/^\d{8}$/', $targetTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format tahun ajaran target harus berupa 8 digit angka (contoh: 20252026)'
                ]);
            }
            
            // Validasi tahun ajaran yang masuk akal
            $tahun1 = (int) substr($targetTahunAjaran, 0, 4);
            $tahun2 = (int) substr($targetTahunAjaran, 4, 4);
            $currentYear = (int) date('Y');
            
            if ($tahun2 !== $tahun1 + 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun kedua harus tahun pertama + 1 (contoh: 20252026)'
                ]);
            }
            
            if ($tahun1 < 2000 || $tahun1 > $currentYear + 10) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran harus antara 2000 dan ' . ($currentYear + 10)
                ]);
            }
            
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            // Cek apakah data target sudah ada
            $existingData = $bobotNilaiModel->where('IdTahunAjaran', $targetTahunAjaran)->first();
            if ($existingData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data untuk tahun ajaran ' . $targetTahunAjaran . ' sudah ada'
                ]);
            }
            
            // Ambil data sumber
            $sourceData = $bobotNilaiModel->where('IdTahunAjaran', $sourceTahunAjaran)->findAll();
            if (empty($sourceData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data sumber tidak ditemukan'
                ]);
            }
            
            // Duplikasi data
            $duplicateData = [];
            foreach ($sourceData as $item) {
                $duplicateData[] = [
                    'IdTahunAjaran' => $targetTahunAjaran,
                    'KategoriMateriUjian' => $item['KategoriMateriUjian'],
                    'NilaiBobot' => $item['NilaiBobot'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            
            if ($bobotNilaiModel->insertBatch($duplicateData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil diduplikasi dari ' . $sourceTahunAjaran . ' ke ' . $targetTahunAjaran
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menduplikasi data'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== REGISTRASI PESERTA MUNAQOSAH ====================

    public function registrasiPesertaMunaqosah()
    {
        // Ambil tahun ajaran saat ini
        $tahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

        // Ambil data TPQ
        $idTpq = session()->get('IdTpq');
        if ($idTpq) {
            // User TPQ - hanya tampilkan TPQ mereka
            $tpq = [$this->tpqModel->find($idTpq)];
        } else {
            // Admin - tampilkan semua TPQ
            $tpq = $this->tpqModel->findAll();
        }
        
        // Ambil data kelas
        $kelas = $this->helpFunction->getDataKelas();
        
        $data = [
            'page_title' => 'Registrasi Peserta Munaqosah',
            'active_menu' => 'munaqosah',
            'tahunAjaran' => $tahunAjaran,
            'tpq' => $tpq,
            'kelas' => $kelas
        ];
        
        return view('backend/Munaqosah/registrasiPesertaMunaqosah', $data);
    }

    public function getSantriForRegistrasi()
    {
        try {
            $filterTpq = $this->request->getGet('filterTpq') ?? 0;
            $filterKelas = $this->request->getGet('filterKelas') ?? 0;
            $typeUjian = $this->request->getGet('typeUjian') ?? 'munaqosah';
            $tahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

            // Check if user is admin (IdTpq = 0 or null means Admin)
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = empty($sessionIdTpq) || $sessionIdTpq == 0;

            // Force Pra-Munaqosah for non-admin users
            if (!$isAdmin) {
                $typeUjian = 'pra-munaqosah';
                // Force filter TPQ to user's TPQ only
                $filterTpq = $sessionIdTpq;
            }

            // Ambil data peserta munaqosah dengan relasi ke tabel santri
            $builder = $this->db->table('tbl_munaqosah_peserta mp');
            $builder->select('mp.*, s.*, t.NamaTpq, k.NamaKelas, 
                            mn_munaqosah.NoPeserta as NoPesertaMunaqosah,
                            mn_pra.NoPeserta as NoPesertaPraMunaqosah');
            $builder->join('tbl_santri_baru s', 's.IdSantri = mp.IdSantri', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = mp.IdTpq', 'left');
            $builder->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left');
            // Join untuk data munaqosah
            $builder->join('tbl_munaqosah_nilai mn_munaqosah', 'mn_munaqosah.IdSantri = mp.IdSantri AND mn_munaqosah.IdTahunAjaran = mp.IdTahunAjaran AND mn_munaqosah.TypeUjian = "munaqosah"', 'left');
            // Join untuk data pra-munaqosah
            $builder->join('tbl_munaqosah_nilai mn_pra', 'mn_pra.IdSantri = mp.IdSantri AND mn_pra.IdTahunAjaran = mp.IdTahunAjaran AND mn_pra.TypeUjian = "pra-munaqosah"', 'left');
            $builder->where('mp.IdTahunAjaran', $tahunAjaran);
            
            // Filter TPQ
            if ($filterTpq != 0) {
                $builder->where('mp.IdTpq', $filterTpq);
            }
            
            // Filter Kelas
            if ($filterKelas != 0) {
                $builder->where('s.IdKelas', $filterKelas);
            }

            // Group by untuk menghindari duplikasi
            $builder->groupBy('mp.IdSantri, mp.IdTpq, mp.IdTahunAjaran, mn_munaqosah.NoPeserta, mn_pra.NoPeserta');

            $builder->orderBy('mp.IdTpq', 'ASC');
            $builder->orderBy('s.NamaSantri', 'ASC');
            $builder->orderBy('mp.created_at', 'DESC');
            
            $santriData = $builder->get()->getResultArray();
            
            // Cek apakah santri sudah memiliki data di tabel nilai munaqosah
            $result = [];
            foreach ($santriData as $santri) {
                // Cek status berdasarkan type ujian yang dipilih
                if ($typeUjian === 'pra-munaqosah') {
                    $hasNilai = !empty($santri['NoPesertaPraMunaqosah']);
                    $santri['isPesertaPraMunaqosah'] = $hasNilai;
                    $santri['NoPesertaMunaqosah'] = $santri['NoPesertaPraMunaqosah'] ?? '-';
                } else {
                    $hasNilai = !empty($santri['NoPesertaMunaqosah']);
                    $santri['isPeserta'] = $hasNilai;
                    $santri['NoPesertaMunaqosah'] = $santri['NoPesertaMunaqosah'] ?? '-';
                }

                $result[] = $santri;
            }
            
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Error in getSantriForRegistrasi: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // Return detailed error information
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data santri',
                'error_details' => [
                    'error_message' => $e->getMessage(),
                    'error_type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'user_message' => 'Gagal memuat data santri. Silakan coba lagi atau hubungi administrator jika masalah berlanjut.'
            ]);
        }
    }

    public function getPreviewRegistrasi()
    {
        try {
            $santriIds = json_decode($this->request->getPost('santri_ids'), true);
            $tahunAjaran = $this->request->getPost('tahunAjaran');
            
            if (empty($santriIds)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada santri yang dipilih'
                ]);
            }
            
            // Ambil data grup materi ujian aktif
            $grupMateri = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();
            
            // Ambil data materi per kategori
            $materiPerKategori = [];
            foreach ($grupMateri as $grup) {
                $materi = $this->materiMunaqosahModel->getMateriByGrup($grup['IdGrupMateriUjian']);
                if (!empty($materi)) {
                    // Group by KategoriMateri instead of NamaMateriGrup
                    foreach ($materi as $m) {
                        $kategori = $m['KategoriMateri'];
                        if (!isset($materiPerKategori[$kategori])) {
                            $materiPerKategori[$kategori] = [];
                        }
                        $materiPerKategori[$kategori][] = $m;
                    }
                }
            }
            
            // Ambil semua data peserta munaqosah sekaligus
            $builder = $this->db->table('tbl_munaqosah_peserta mp');
            $builder->select('mp.*, s.*');
            $builder->join('tbl_santri_baru s', 's.IdSantri = mp.IdSantri', 'left');
            $builder->whereIn('mp.IdSantri', $santriIds);
            $builder->where('mp.IdTahunAjaran', $tahunAjaran);
            $allSantri = $builder->get()->getResultArray();
            
            // Buat mapping untuk akses cepat
            $santriMap = [];
            foreach ($allSantri as $santri) {
                $santriMap[$santri['IdSantri']] = $santri;
            }
            
            $previewData = [];
            
            foreach ($santriIds as $santriId) {
                if (!isset($santriMap[$santriId])) continue;
                
                $santri = $santriMap[$santriId];
                
                // Generate NoPeserta random (100-400)
                $noPeserta = $this->generateNoPeserta($tahunAjaran);
                
                // Untuk setiap kategori materi, pilih satu materi secara random
                foreach ($materiPerKategori as $kategori => $materiList) {
                    if (!empty($materiList)) {
                        $randomMateri = $materiList[array_rand($materiList)];
                        
                        $previewData[] = [
                            'NoPeserta' => $noPeserta,
                            'IdSantri' => $santriId,
                            'NamaSantri' => $santri['NamaSantri'],
                            'IdTpq' => $santri['IdTpq'],
                            'IdTahunAjaran' => $tahunAjaran,
                            'IdMateri' => $randomMateri['IdMateri'],
                            'NamaMateri' => $randomMateri['NamaMateri'],
                            'IdGrupMateriUjian' => $randomMateri['IdGrupMateriUjian'],
                            'KategoriMateriUjian' => $kategori
                        ];
                    }
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $previewData
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function processRegistrasiPeserta()
    {
        try {
            // Validasi input data
            $santriIds = json_decode($this->request->getPost('santri_ids'), true);
            $tahunAjaran = $this->request->getPost('tahunAjaran');
            $typeUjian = $this->request->getPost('typeUjian') ?? 'munaqosah';

            // Check if user is admin (IdTpq = 0 or null means Admin)
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = empty($sessionIdTpq) || $sessionIdTpq == 0;

            // Force Pra-Munaqosah for non-admin users
            if (!$isAdmin) {
                $typeUjian = 'pra-munaqosah';
                // Force filter TPQ to user's TPQ only
                $filterTpq = $sessionIdTpq;
            }
            
            // Detail validasi input
            $validationErrors = [];
            
            if (empty($santriIds)) {
                $validationErrors[] = "Parameter 'santri_ids' tidak boleh kosong";
            } elseif (!is_array($santriIds)) {
                $validationErrors[] = "Parameter 'santri_ids' harus berupa array";
            } elseif (count($santriIds) === 0) {
                $validationErrors[] = "Minimal harus memilih satu santri";
            }
            
            if (empty($tahunAjaran)) {
                $validationErrors[] = "Parameter 'tahunAjaran' tidak boleh kosong";
            } elseif (!is_numeric($tahunAjaran)) {
                $validationErrors[] = "Parameter 'tahunAjaran' harus berupa angka";
            }
            
            if (!empty($validationErrors)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi input gagal',
                    'detailed_errors' => $validationErrors,
                    'error_count' => count($validationErrors)
                ]);
            }

            // Validasi: cek apakah ada santri yang sudah memiliki data nilai berdasarkan type ujian
            $existingNilai = $this->nilaiMunaqosahModel->whereIn('IdSantri', $santriIds)
                                                      ->where('IdTahunAjaran', $tahunAjaran)
                ->where('TypeUjian', $typeUjian)
                                                      ->findAll();
            
            if (!empty($existingNilai)) {
                $existingIds = array_unique(array_column($existingNilai, 'IdSantri'));
                $existingCount = count($existingIds);
                $totalSelected = count($santriIds);
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Beberapa santri sudah memiliki data nilai munaqosah',
                    'detailed_errors' => [
                        "Ditemukan {$existingCount} santri yang sudah memiliki data nilai dari {$totalSelected} santri yang dipilih",
                        "ID Santri yang sudah memiliki data: " . implode(', ', $existingIds),
                        "Silakan pilih santri lain yang belum memiliki data nilai munaqosah"
                    ],
                    'existing_santri_ids' => $existingIds,
                    'existing_count' => $existingCount,
                    'total_selected' => $totalSelected
                ]);
            }
            
            // Start database transaction
            $this->db->transStart();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // Ambil semua data peserta munaqosah sekaligus
            $builder = $this->db->table('tbl_munaqosah_peserta mp');
            $builder->select('mp.*, s.*');
            $builder->join('tbl_santri_baru s', 's.IdSantri = mp.IdSantri', 'left');
            $builder->whereIn('mp.IdSantri', $santriIds);
            $builder->where('mp.IdTahunAjaran', $tahunAjaran);
            $allSantri = $builder->get()->getResultArray();
            
            // Buat mapping untuk akses cepat
            $santriMap = [];
            foreach ($allSantri as $santri) {
                $santriMap[$santri['IdSantri']] = $santri;
            }
            
            // Ambil data grup materi ujian aktif sekali saja
            $grupMateri = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();
            
            // Ambil semua materi sekaligus
            $allMateri = [];
            foreach ($grupMateri as $grup) {
                $materi = $this->materiMunaqosahModel->getMateriByGrup($grup['IdGrupMateriUjian']);
                if (!empty($materi)) {
                    foreach ($materi as $m) {
                        $kategori = $m['KategoriMateri'];
                        if (!isset($allMateri[$kategori])) {
                            $allMateri[$kategori] = [];
                        }
                        $allMateri[$kategori][] = $m;
                    }
                }
            }
            
            // Debug: Log data materi
            log_message('info', 'Grup materi: ' . json_encode($grupMateri));
            log_message('info', 'All materi: ' . json_encode($allMateri));
            
            // Generate semua NoPeserta sekaligus dengan validasi ketat
            $noPesertaMap = [];
            $usedNoPeserta = [];
            $minRange = 100;
            $maxRange = 400;
            
            foreach ($santriIds as $santriId) {
                try {
                    $noPeserta = $this->generateUniqueNoPeserta($tahunAjaran, $usedNoPeserta, $typeUjian);

                    // Validasi ketat: pastikan nomor peserta dalam range
                    if ($noPeserta < $minRange || $noPeserta > $maxRange) {
                        throw new \Exception("Nomor peserta {$noPeserta} di luar range yang diizinkan ({$minRange}-{$maxRange})");
                    }

                    $noPesertaMap[$santriId] = $noPeserta;
                    $usedNoPeserta[] = $noPeserta; // Tambahkan ke daftar yang sudah digunakan

                    log_message('info', "Generated NoPeserta {$noPeserta} for santri {$santriId} with type {$typeUjian}");
                } catch (\Exception $e) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal generate nomor peserta: ' . $e->getMessage(),
                        'santri_id' => $santriId
                    ]);
                }
            }
            
            // Proses semua santri dan kumpulkan data untuk batch insert
            $allNilaiData = [];
            foreach ($santriIds as $santriId) {
                try {
                    if (!isset($santriMap[$santriId])) {
                        $errorCount++;
                        $errors[] = "Peserta munaqosah dengan ID {$santriId} tidak ditemukan";
                        continue;
                    }
                    
                    $santri = $santriMap[$santriId];
                    $noPeserta = $noPesertaMap[$santriId];
                    
                    // Debug: Log data santri
                    log_message('info', "Processing santri: {$santriId}, NoPeserta: {$noPeserta}");
                    
                    // Generate data nilai untuk santri ini
                    foreach ($allMateri as $kategori => $materiList) {
                        if (!empty($materiList)) {
                            $randomMateri = $materiList[array_rand($materiList)];
                            
                            $nilaiRecord = [
                                'NoPeserta' => $noPeserta,
                                'IdSantri' => $santriId,
                                'IdTpq' => $santri['IdTpq'],
                                'IdTahunAjaran' => $tahunAjaran,
                                'IdMateri' => $randomMateri['IdMateri'],
                                'IdGrupMateriUjian' => $randomMateri['IdGrupMateriUjian'],
                                'KategoriMateriUjian' => $kategori,
                                'TypeUjian' => $typeUjian,
                                'Nilai' => 0,
                                'Catatan' => ''
                            ];
                            
                            $allNilaiData[] = $nilaiRecord;
                            
                            // Debug: Log setiap record
                            log_message('info', "Nilai record: " . json_encode($nilaiRecord));
                        }
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error processing santri ID {$santriId}: " . $e->getMessage();
                    log_message('error', "Error processing santri {$santriId}: " . $e->getMessage());
                }
            }
            
            // Insert semua data sekaligus
            if (!empty($allNilaiData)) {
                // Validasi final: cek range nomor peserta sebelum validasi lainnya
                $rangeValidation = $this->validateNoPesertaRange($allNilaiData, $minRange, $maxRange);
                if (!$rangeValidation['valid']) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi range gagal: ' . $rangeValidation['message']
                    ]);
                }

                // Validasi final: cek duplikasi NoPeserta sebelum insert dan perbaiki jika ada
                $finalValidation = $this->validateNoPesertaUniqueness($allNilaiData, $tahunAjaran, $typeUjian);
                if (!$finalValidation['valid']) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi gagal: ' . $finalValidation['message']
                    ]);
                }
                
                // Gunakan data yang sudah diperbaiki jika ada
                if (isset($finalValidation['fixedData'])) {
                    $allNilaiData = $finalValidation['fixedData'];
                    log_message('info', 'Data telah diperbaiki: ' . $finalValidation['message']);
                }
                
                // Debug: Log data yang akan diinsert
                log_message('info', 'Data yang akan diinsert: ' . json_encode($allNilaiData));
                
                $result = $this->nilaiMunaqosahModel->insertBatch($allNilaiData);
                
                // Debug: Log hasil insert
                log_message('info', 'Hasil insert: ' . ($result ? 'Berhasil' : 'Gagal'));
                
                if (!$result) {
                    $errorCount++;
                    $modelErrors = $this->nilaiMunaqosahModel->errors();
                    $errors[] = "Gagal insert data ke database: " . implode(', ', $modelErrors);
                    
                    // Log detail error untuk debugging
                    log_message('error', 'Insert batch gagal: ' . json_encode($modelErrors));
                    log_message('error', 'Data yang gagal diinsert: ' . json_encode($allNilaiData));
                }
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                $transactionError = $this->db->error();
                log_message('error', 'Database transaction failed: ' . json_encode($transactionError));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Database transaction failed',
                    'detailed_errors' => [
                        'Transaction rollback terjadi karena kesalahan database',
                        'Error code: ' . ($transactionError['code'] ?? 'Unknown'),
                        'Error message: ' . ($transactionError['message'] ?? 'Unknown error')
                    ],
                    'database_error' => $transactionError
                ]);
            }
            
            $message = "Berhasil memproses {$successCount} santri";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} gagal";
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            // Log detail error untuk debugging
            log_message('error', 'Exception in processRegistrasiPeserta: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'detailed_errors' => [
                    'Exception message: ' . $e->getMessage(),
                    'File: ' . $e->getFile() . ' Line: ' . $e->getLine(),
                    'Silakan hubungi administrator jika masalah berlanjut'
                ],
                'exception_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode()
                ]
            ]);
        }
    }

    private function generateNoPeserta($tahunAjaran)
    {
        // Generate random number between 100-400
        $noPeserta = rand(100, 400);
        
        // Cek apakah NoPeserta sudah ada di database untuk tahun ajaran dan TypeUjian yang sama
        $existing = $this->nilaiMunaqosahModel->where('NoPeserta', $noPeserta)
                                            ->where('IdTahunAjaran', $tahunAjaran)
                                            ->where('TypeUjian', 'munaqosah')
                                            ->first();
        
        // Jika sudah ada, generate ulang (maksimal 10 kali percobaan)
        $attempts = 0;
        while ($existing && $attempts < 10) {
            $noPeserta = rand(100, 400);
            $existing = $this->nilaiMunaqosahModel->where('NoPeserta', $noPeserta)
                                                ->where('IdTahunAjaran', $tahunAjaran)
                                                ->where('TypeUjian', 'munaqosah')
                                                ->first();
            $attempts++;
        }
        
        // Jika masih ada duplikasi setelah 10 percobaan, tambahkan timestamp
        if ($existing) {
            $noPeserta = $noPeserta . substr(time(), -3);
        }
        
        return $noPeserta;
    }

    private function generateUniqueNoPeserta($tahunAjaran, $usedNoPeserta = [], $typeUjian = 'munaqosah')
    {
        // Validasi ketat: nomor peserta harus dalam range 100-400
        $minRange = 100;
        $maxRange = 400;
        $maxAttempts = 100; // Maksimal 100 percobaan untuk menghindari infinite loop

        // Generate random number between 100-400
        $noPeserta = rand($minRange, $maxRange);
        
        // Cek apakah NoPeserta sudah ada di database untuk tahun ajaran dan TypeUjian yang sama
        $existing = $this->nilaiMunaqosahModel->where('NoPeserta', $noPeserta)
                                            ->where('IdTahunAjaran', $tahunAjaran)
            ->where('TypeUjian', $typeUjian)
                                            ->first();
        
        // Cek apakah NoPeserta sudah digunakan dalam batch yang sama
        $isUsedInBatch = in_array($noPeserta, $usedNoPeserta);
        
        // Jika sudah ada di database atau sudah digunakan dalam batch, coba generate ulang
        $attempts = 0;
        while (($existing || $isUsedInBatch) && $attempts < $maxAttempts) {
            $noPeserta = rand($minRange, $maxRange);
            $existing = $this->nilaiMunaqosahModel->where('NoPeserta', $noPeserta)
                                                ->where('IdTahunAjaran', $tahunAjaran)
                ->where('TypeUjian', $typeUjian)
                                                ->first();
            $isUsedInBatch = in_array($noPeserta, $usedNoPeserta);
            $attempts++;
        }

        // Jika masih ada duplikasi setelah maxAttempts, cari nomor yang tersedia secara sequential
        if ($existing || $isUsedInBatch) {
            $noPeserta = $this->findAvailableNoPesertaInRange($tahunAjaran, $usedNoPeserta, $minRange, $maxRange, $typeUjian);
        }

        // Validasi final: pastikan nomor peserta dalam range yang benar
        if ($noPeserta < $minRange || $noPeserta > $maxRange) {
            log_message('error', "Generated NoPeserta out of range: {$noPeserta}. Expected range: {$minRange}-{$maxRange}");
            throw new \Exception("Tidak dapat menghasilkan nomor peserta dalam range {$minRange}-{$maxRange}. Semua nomor dalam range sudah digunakan.");
        }

        return $noPeserta;
    }

    private function findAvailableNoPesertaInRange($tahunAjaran, $usedNoPeserta, $minRange, $maxRange, $typeUjian = 'munaqosah')
    {
        // Cari nomor yang tersedia secara sequential dalam range
        for ($i = $minRange; $i <= $maxRange; $i++) {
            // Cek apakah nomor sudah digunakan dalam batch
            if (in_array($i, $usedNoPeserta)) {
                continue;
            }

            // Cek apakah nomor sudah ada di database
            $existing = $this->nilaiMunaqosahModel->where('NoPeserta', $i)
                                                ->where('IdTahunAjaran', $tahunAjaran)
                ->where('TypeUjian', $typeUjian)
                                                ->first();

            if (!$existing) {
                return $i; // Nomor tersedia ditemukan
            }
        }

        // Jika tidak ada nomor yang tersedia dalam range, throw exception
        throw new \Exception("Tidak ada nomor peserta yang tersedia dalam range {$minRange}-{$maxRange} untuk tahun ajaran {$tahunAjaran}");
    }

    private function validateNoPesertaRange($allNilaiData, $minRange, $maxRange)
    {
        $outOfRangeNumbers = [];

        foreach ($allNilaiData as $data) {
            $noPeserta = $data['NoPeserta'];

            // Validasi: pastikan nomor peserta dalam range yang benar
            if ($noPeserta < $minRange || $noPeserta > $maxRange) {
                $outOfRangeNumbers[] = $noPeserta;
            }
        }

        if (!empty($outOfRangeNumbers)) {
            $uniqueOutOfRange = array_unique($outOfRangeNumbers);
            return [
                'valid' => false,
                'message' => "Ditemukan nomor peserta di luar range {$minRange}-{$maxRange}: " . implode(', ', $uniqueOutOfRange)
            ];
        }

        return ['valid' => true];
    }

    private function validateNoPesertaUniqueness($allNilaiData, $tahunAjaran, $typeUjian = 'munaqosah')
    {
        // Group data by IdSantri untuk memastikan satu IdSantri = satu NoPeserta
        $santriNoPesertaMap = [];
        $duplicateSantri = [];
        
        foreach ($allNilaiData as $data) {
            $idSantri = $data['IdSantri'];
            $noPeserta = $data['NoPeserta'];
            
            if (!isset($santriNoPesertaMap[$idSantri])) {
                $santriNoPesertaMap[$idSantri] = $noPeserta;
            } else {
                // Jika IdSantri yang sama memiliki NoPeserta berbeda, ini error
                if ($santriNoPesertaMap[$idSantri] !== $noPeserta) {
                    $duplicateSantri[] = $idSantri;
                }
            }
        }
        
        if (!empty($duplicateSantri)) {
            return [
                'valid' => false,
                'message' => 'Error: Satu IdSantri memiliki NoPeserta yang berbeda: ' . implode(', ', $duplicateSantri)
            ];
        }
        
        // Ekstrak unique NoPeserta (karena satu IdSantri = satu NoPeserta)
        $uniqueNoPesertaList = array_unique(array_column($allNilaiData, 'NoPeserta'));
        
        // Cek apakah NoPeserta sudah ada di database untuk TypeUjian yang sama
        $existingNoPeserta = $this->nilaiMunaqosahModel->whereIn('NoPeserta', $uniqueNoPesertaList)
                                                      ->where('IdTahunAjaran', $tahunAjaran)
            ->where('TypeUjian', $typeUjian)
                                                      ->findAll();
        
        if (!empty($existingNoPeserta)) {
            // Jika ada duplikasi dengan database, perbaiki dengan mengubah NoPeserta yang konflik
            $fixedData = $this->fixDuplicateNoPesertaWithDatabase($allNilaiData, $tahunAjaran, $existingNoPeserta, $typeUjian);
            return [
                'valid' => true,
                'message' => 'Duplikasi dengan database telah diperbaiki',
                'fixedData' => $fixedData
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Validasi berhasil',
            'fixedData' => $allNilaiData
        ];
    }


    private function fixDuplicateNoPesertaWithDatabase($allNilaiData, $tahunAjaran, $existingNoPeserta, $typeUjian = 'munaqosah')
    {
        $fixedData = [];
        $usedNoPeserta = [];
        $existingNumbers = array_column($existingNoPeserta, 'NoPeserta');
        
        // Group data by IdSantri untuk memastikan konsistensi
        $santriNoPesertaMap = [];
        
        foreach ($allNilaiData as $data) {
            $idSantri = $data['IdSantri'];
            $noPeserta = $data['NoPeserta'];
            
            // Jika IdSantri belum diproses atau NoPeserta konflik
            if (!isset($santriNoPesertaMap[$idSantri]) || 
                in_array($noPeserta, $existingNumbers) || 
                in_array($noPeserta, $usedNoPeserta)) {

                // Generate NoPeserta baru untuk IdSantri ini
                $newNoPeserta = $this->generateUniqueNoPeserta($tahunAjaran, $usedNoPeserta, $typeUjian);
                $santriNoPesertaMap[$idSantri] = $newNoPeserta;
                $usedNoPeserta[] = $newNoPeserta;
            }
            
            // Gunakan NoPeserta yang sudah ditetapkan untuk IdSantri ini
            $data['NoPeserta'] = $santriNoPesertaMap[$idSantri];
            $fixedData[] = $data;
        }
        
        return $fixedData;
    }

    /**
     * Format string menjadi Title Case (huruf kapital di awal setiap kata)
     */
    private function formatTitleCase($string)
    {
        if (empty($string)) {
            return $string;
        }
        
        // Decode HTML entities terlebih dahulu
        $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $string = trim($string);
        
        // Convert to title case
        $words = explode(' ', $string);
        $result = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                // Convert seluruh kata ke lowercase terlebih dahulu
                $word = mb_strtolower($word, 'UTF-8');
                
                // Cek apakah kata mengandung tanda petik
                if (strpos($word, "'") !== false) {
                    // Pisahkan kata berdasarkan tanda petik
                    $parts = explode("'", $word);
                    
                    // Proses setiap bagian
                    foreach ($parts as $key => $part) {
                        if (!empty($part)) {
                            if ($key === 0) {
                                // Untuk bagian pertama, ubah huruf pertama menjadi uppercase
                                $parts[$key] = mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8') .
                                    mb_substr($part, 1, null, 'UTF-8');
                            } else {
                                // Untuk bagian setelah tanda petik, biarkan lowercase
                                $parts[$key] = $part;
                            }
                        }
                    }
                    
                    // Gabungkan kembali dengan tanda petik
                    $word = implode("'", $parts);
                } else {
                    // Jika tidak ada tanda petik, gunakan title case biasa
                    $word = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') .
                        mb_substr($word, 1, null, 'UTF-8');
                }
                
                $result .= $word . ' ';
            }
        }
        
        return trim($result);
    }

    public function getDetailSantri()
    {
        $idSantri = $this->request->getPost('IdSantri');
        
        if (!$idSantri) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Santri tidak boleh kosong'
            ]);
        }

        try {
            // Ambil data santri dari SantriBaruModel
            $santriData = $this->santriBaruModel->getDetailSantri($idSantri);
            
            if (!$santriData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data santri berhasil diambil',
                'data' => $santriData
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getDetailSantri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data santri: ' . $e->getMessage()
            ]);
        }
    }

    public function printKartuUjian()
    {
        try {
            // Set memory limit dan timeout
            ini_set('memory_limit', '256M');
            set_time_limit(300);
            mb_internal_encoding('UTF-8');

            // Ambil data dari POST request
            $santriIds = $this->request->getPost('santri_ids');
            $typeUjian = $this->request->getPost('typeUjian') ?? 'munaqosah';
            $tahunAjaran = $this->request->getPost('tahunAjaran');
            $filterTpq = $this->request->getPost('filterTpq') ?? 0;
            $filterKelas = $this->request->getPost('filterKelas') ?? 0;

            // Validasi input
            if (empty($santriIds)) {
                throw new \Exception('Tidak ada santri yang dipilih untuk dicetak');
            }

            // Decode JSON jika berupa string
            if (is_string($santriIds)) {
                $santriIds = json_decode($santriIds, true);
            }

            if (!is_array($santriIds) || empty($santriIds)) {
                throw new \Exception('Data santri tidak valid');
            }

            // Ambil data peserta munaqosah dengan relasi
            $builder = $this->db->table('tbl_munaqosah_peserta mp');
            $builder->select('mp.*, s.*, t.NamaTpq, k.NamaKelas, 
                            mn.NoPeserta, mn.TypeUjian');
            $builder->join('tbl_santri_baru s', 's.IdSantri = mp.IdSantri', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = mp.IdTpq', 'left');
            $builder->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left');
            $builder->join('tbl_munaqosah_nilai mn', 'mn.IdSantri = mp.IdSantri AND mn.IdTahunAjaran = mp.IdTahunAjaran AND mn.TypeUjian = "' . $typeUjian . '"', 'left');
            $builder->whereIn('mp.IdSantri', $santriIds);
            $builder->where('mp.IdTahunAjaran', $tahunAjaran);
            $builder->where('mn.NoPeserta IS NOT NULL'); // Hanya ambil yang sudah ada nomor peserta

            // Filter TPQ
            if ($filterTpq != 0) {
                $builder->where('mp.IdTpq', $filterTpq);
            }

            // Filter Kelas
            if ($filterKelas != 0) {
                $builder->where('s.IdKelas', $filterKelas);
            }

            $builder->groupBy('mp.IdSantri');
            $builder->orderBy('mp.IdTpq', 'ASC');
            $builder->orderBy('s.NamaSantri', 'ASC');

            $pesertaData = $builder->get()->getResultArray();

            // Debug log untuk melihat jumlah data
            log_message('info', 'Print Kartu Ujian - Jumlah data peserta: ' . count($pesertaData));
            log_message('info', 'Print Kartu Ujian - Data peserta: ' . json_encode(array_column($pesertaData, 'IdSantri')));

            if (empty($pesertaData)) {
                throw new \Exception('Tidak ada data peserta yang ditemukan untuk dicetak');
            }

            // Tambahkan QR code ke data peserta
            foreach ($pesertaData as &$peserta) {
                $noPeserta = $peserta['NoPeserta'];

                // Generate QR code langsung untuk nomor peserta
                $qrOptions = new QROptions([
                    'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                    'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                    'scale' => 2,
                    'imageBase64' => false,
                    'addQuietzone' => true,
                    'quietzoneSize' => 1,
                ]);

                $qrCode = new QRCode($qrOptions);
                $qrContent = (string)$noPeserta;
                $svgContent = $qrCode->render($qrContent);
                $base64Svg = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                $peserta['qrCode'] = '<img src="' . $base64Svg . '" style="width: 40px; height: 40px;" />';

                // QR Code footer untuk link
                $footerQrOptions = new QROptions([
                    'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                    'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                    'scale' => 1,
                    'imageBase64' => false,
                    'addQuietzone' => true,
                    'quietzoneSize' => 1,
                ]);

                // Generate 64 bit hash dari no peserta
                $hash = hash('sha256', $noPeserta);
                $footerQrCode = new QRCode($footerQrOptions);
                $footerSvgContent = $footerQrCode->render('www.tpqsmart.simaq/nilai-ujian/' . $hash);
                $footerBase64Svg = 'data:image/svg+xml;base64,' . base64_encode($footerSvgContent);
                $peserta['footerQrCode'] = '<img src="' . $footerBase64Svg . '" style="width: 30px; height: 30px;" />';
            }

            // Siapkan data untuk view
            $data = [
                'peserta' => $pesertaData,
                'typeUjian' => $typeUjian,
                'tahunAjaran' => $tahunAjaran
            ];

            // Load view untuk PDF
            $html = view('backend/Munaqosah/printKartuUjian', $data);

            // Setup Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'Arial');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'print');
            $options->set('isJavascriptEnabled', false);
            $options->set('isCssFloatEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('debugPng', false);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('F4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = 'kartu_ujian_' . $typeUjian . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // Hapus semua output sebelumnya
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set header
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Munaqosah: printKartuUjian - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function updateSantri()
    {
        $validation = \Config\Services::validation();
        
        // Set validation rules
        $validation->setRules([
            'IdSantri' => 'required',
            'NamaSantri' => 'required|min_length[3]|max_length[100]',
            'TempatLahirSantri' => 'required|min_length[2]|max_length[100]',
            'TanggalLahirSantri' => 'required|valid_date',
            'JenisKelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'NamaAyah' => 'required|min_length[3]|max_length[100]'
        ], [
            'IdSantri' => [
                'required' => 'ID Santri harus diisi'
            ],
            'NamaSantri' => [
                'required' => 'Nama Santri harus diisi',
                'min_length' => 'Nama Santri minimal 3 karakter',
                'max_length' => 'Nama Santri maksimal 100 karakter'
            ],
            'TempatLahirSantri' => [
                'required' => 'Tempat Lahir harus diisi',
                'min_length' => 'Tempat Lahir minimal 2 karakter',
                'max_length' => 'Tempat Lahir maksimal 100 karakter'
            ],
            'TanggalLahirSantri' => [
                'required' => 'Tanggal Lahir harus diisi',
                'valid_date' => 'Format tanggal tidak valid'
            ],
            'JenisKelamin' => [
                'required' => 'Jenis Kelamin harus diisi',
                'in_list' => 'Jenis Kelamin harus Laki-laki atau Perempuan'
            ],
            'NamaAyah' => [
                'required' => 'Nama Ayah harus diisi',
                'min_length' => 'Nama Ayah minimal 3 karakter',
                'max_length' => 'Nama Ayah maksimal 100 karakter'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $detailedErrors = [];
            
            foreach ($errors as $field => $error) {
                $detailedErrors[] = $error;
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi input gagal',
                'detailed_errors' => $detailedErrors
            ]);
        }

        try {
            $idSantri = $this->request->getPost('IdSantri');
            
            // Cek apakah santri ada
            $existingSantri = $this->santriBaruModel->getDetailSantri($idSantri);
            if (!$existingSantri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            // Format data sebelum disimpan
            $namaSantri = $this->formatTitleCase($this->request->getPost('NamaSantri'));
            $tempatLahirSantri = $this->formatTitleCase($this->request->getPost('TempatLahirSantri'));
            $tanggalLahirSantri = $this->request->getPost('TanggalLahirSantri');
            $tanggalLahirSantri = date('Y-m-d', strtotime($tanggalLahirSantri));
            $jenisKelamin = $this->request->getPost('JenisKelamin');
            $namaAyah = $this->formatTitleCase($this->request->getPost('NamaAyah'));

            // Bandingkan data lama dan baru
            $changes = [];
            $changeMessages = [];
            
            // Compare NamaSantri
            if ($existingSantri['NamaSantri'] !== $namaSantri) {
                $changes['NamaSantri'] = $namaSantri;
                $changeMessages[] = "Nama Santri: '{$existingSantri['NamaSantri']}' → '{$namaSantri}'";
            }
            
            // Compare TempatLahirSantri
            if ($existingSantri['TempatLahirSantri'] !== $tempatLahirSantri) {
                $changes['TempatLahirSantri'] = $tempatLahirSantri;
                $changeMessages[] = "Tempat Lahir: '{$existingSantri['TempatLahirSantri']}' → '{$tempatLahirSantri}'";
            }
            
            // Compare TanggalLahirSantri
            $existingTanggal = date('Y-m-d', strtotime($existingSantri['TanggalLahirSantri']));
            if ($existingTanggal !== $tanggalLahirSantri) {
                $changes['TanggalLahirSantri'] = $tanggalLahirSantri;
                $changeMessages[] = "Tanggal Lahir: '{$existingTanggal}' → '{$tanggalLahirSantri}'";
            }
            
            // Compare JenisKelamin
            if ($existingSantri['JenisKelamin'] !== $jenisKelamin) {
                $changes['JenisKelamin'] = $jenisKelamin;
                $changeMessages[] = "Jenis Kelamin: '{$existingSantri['JenisKelamin']}' → '{$jenisKelamin}'";
            }
            
            // Compare NamaAyah
            if ($existingSantri['NamaAyah'] !== $namaAyah) {
                $changes['NamaAyah'] = $namaAyah;
                $changeMessages[] = "Nama Ayah: '{$existingSantri['NamaAyah']}' → '{$namaAyah}'";
            }
            
            // Cek apakah ada perubahan
            if (empty($changes)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tidak ada perubahan data. Semua data sudah sesuai dengan yang tersimpan.',
                    'no_changes' => true,
                    'data' => $existingSantri
                ]);
            }
            
            // Tambahkan updated_at ke changes
            $changes['updated_at'] = date('Y-m-d H:i:s');
            
            // Update data santri hanya field yang berubah
            $result = $this->santriBaruModel->update($existingSantri['id'], $changes);
            
            if ($result) {
                // Ambil data terbaru untuk response
                $updatedData = $this->santriBaruModel->getDetailSantri($idSantri);
                
                log_message('info', 'Santri updated successfully: ' . $idSantri . ' - Changes: ' . implode(', ', array_keys($changes)));
                
                $changeSummary = implode('<br>', $changeMessages);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data santri berhasil diperbarui',
                    'changes' => $changeSummary,
                    'change_count' => count($changes) - 1, // -1 untuk updated_at
                    'data' => $updatedData
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui data santri'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error in updateSantri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data santri: ' . $e->getMessage()
            ]);
        }
    }
}

