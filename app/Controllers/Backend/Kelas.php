<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\KelasModel;
use App\Models\SantriBaruModel;
use App\Models\KelasMateriPelajaranModel;
use App\Models\NilaiModel;
use App\Models\SantriModel;
class Kelas extends BaseController
{
    protected $santriBaruModel;
    protected $kelasModel;
    protected $kelasMateriPelajaranModel;
    protected $nilaiModel;
    protected $helpFunction;
    protected $IdTpq;
    protected $santriModel;

    public function __construct()
    {
        // Initialize models
        $this->santriBaruModel = new SantriBaruModel();
        $this->kelasModel = new KelasModel();
        $this->kelasMateriPelajaranModel = new KelasMateriPelajaranModel();
        $this->nilaiModel = new NilaiModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->santriModel = new SantriModel();

        $this->IdTpq = session()->get('IdTpq');
    }

    // Function to display all records (Read)
    public function index()
    {
        $data['kelas'] = $this->kelasModel->findAll();
        
        // Load the view and pass the data
        return view('kelas/index', $data);
    }

    // Function to create a new record (Create)
    public function create()
    {
        return view('kelas/create');  // Load the form to create a new record
    }

    // Function to store a new record in the database (Create)
    public function store($data)
    {
        $this->kelasModel->save([
            'IdKelas' => $data['IdKelas'],
            'IdTpq' => $data['IdTpq'],
            'IdSantri' => $data['IdSantri'],
            'IdTahunAjaran' => $data['IdTahunAjaran']
        ]);
    }

    // Function to edit an existing record (Read)
    public function edit($id)
    {
        $data['kelas'] = $this->kelasModel->find($id);

        return view('kelas/edit', $data);  // Load the form to edit the record
    }

    // Function to update the record in the database (Update)
    public function update($id)
    {
        if ($this->validate([
            'IdKelas' => 'required',
            'IdTpq' => 'required',
            'IdSantri' => 'required',
            'IdTahunAjaran' => 'required'
        ])) {
            $this->kelasModel->update($id, [
                'IdKelas' => $this->request->getPost('IdKelas'),
                'IdTpq' => $this->request->getPost('IdTpq'),
                'IdSantri' => $this->request->getPost('IdSantri'),
                'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran')
            ]);

            return redirect()->to('/kelas');  // Redirect to the list of records
        } else {
            return view('kelas/edit', ['kelas' => $this->kelasModel->find($id)]);
        }
    }

    // Function to delete a record from the database (Delete)
    public function delete($id)
    {
        $this->kelasModel->delete($id);

        return redirect()->to('/kelas');  // Redirect to the list of records
    }
    
    // =============================================================
    // Control untuk di tampikan di view

    // Metode untuk mengambil data dan menampilkan di view/backend/kelas/kelasBaru
    public function showSantriKelasBaru()
    {
        // Step 1 Menampilkan data santri baru untuk di crosceck sebelum di masukan ke tabel tbl_kelas_santri
        $dataKelas = $this->helpFunction->getDataKelas();
        $dataSantri = $this->helpFunction->getDataSantriStatus(IdTpq: $this->IdTpq);

        $dataTpq = $this->helpFunction->getDataTpq($this->IdTpq);

        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $dataSantri,
            'dataKelas' => $dataKelas,
            'dataTpq' => $dataTpq
        ];

        return view('backend/kelas/kelasBaru', $data);
    }

    // Menampilkan list kelas dan jumlah santri berdasarkan kelas dan tahun ajaran
    // Digunakan untuk melihat data kelas yang mau di naikan ke kelas berikutnya
    // page : view/backend/kelas/naikKelas
    public function showListSantriPerKelas($idTahunAjaran = null)
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        $currentYear = date('Y');
        $currentMonth = date('n');

        // Ambil data berdasarkan tahun ajaran sebelumnya dan data tahun ajaran saat ini
        $previousAcademicYear = ($currentMonth >= 7) ? ($currentYear - 1) . $currentYear : ($currentYear - 2) . ($currentYear - 1);
        $currentAcademicYear = ($currentMonth >= 7) ? $currentYear . ($currentYear + 1) : ($currentYear - 1) . $currentYear;

        // Ambil data kelas per tahun ajaran dari model
        $IdTpqForQuery = ($IdTpq != 0) ? $IdTpq : null;
        $dataKelas = $this->kelasModel->getKelasPerTahunAjaran($IdTpqForQuery, [$previousAcademicYear, $currentAcademicYear]);

        // Pisahkan data dari tahun ajaran sebelumnya dan saat ini
        $kelas_previous = array_filter($dataKelas, function($item) use ($previousAcademicYear) {
            return $item['IdTahunAjaran'] === $previousAcademicYear;
        });

        $kelas_current = array_filter($dataKelas, function($item) use ($currentAcademicYear) {
            return $item['IdTahunAjaran'] === $currentAcademicYear;
        });

        // persiapkan data untuk di kirim ke 
        $data = [
            'page_title' => 'Daftar Naik Kelas',
            'kelas_previous' => $kelas_previous,
            'kelas_current' => $kelas_current,
            'current_tahun_ajaran' => $currentAcademicYear,
            'previous_tahun_ajaran' => $previousAcademicYear
        ];

        return view('backend/kelas/naikKelas', $data);
    }
    // return direct ke kontrolerll showListSantriPerKelas
    // page : view/backend/kelas/naikKelas
    public function updateNaikKelas($idTahunAjaran, $idKelas)
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        //Step 1 get tahun berikunya dari idTahun Sebelumnya/saat ini
        $newTahunAjaran = $this->helpFunction->getTahuanAjaranBerikutnya($idTahunAjaran);
        // Tambahkan previousAcademicYear
        $previousAcademicYear = $this->helpFunction->getTahunAjaranSebelumnya();

        //Step 2 ambil list santri tbl_kelas_santri
        //tabel ini informasi penyimmpanan santri berdasarkan tahun ajaran, kelas, tpq dan status active = 1
        $IdTpqForQuery = (!empty($IdTpq) && $IdTpq != 0) ? $IdTpq : null;
        $santriList = $this->kelasModel->getSantriByTahunAjaranDanKelas($idTahunAjaran, $idKelas, $IdTpqForQuery);

        $dataKelasBaru = [];
        $dataNilaiBaru = [];
        $idsKelasLama = [];
        $dataSantri = [];

        // Step 3 collect data santri yang akan di naikkan kelas dan data nilai
        foreach ($santriList as $santri) {
            $idKelasLama = $santri['IdKelas'];
            $idKelasBaru = $this->helpFunction->getNextKelas($idKelasLama);
            $idTpq = $santri['IdTpq'];
            $idSantri = $santri['IdSantri'];

            // Ambil id santri dari tabel santri untuk update IdKelas pada tabel santri
            $dataSantri[] = [
                'IdSantri' => $idSantri,
                'IdKelas' => $idKelasBaru
            ];

            // Tentukan tahun ajaran untuk alumni
            $tahunAjaranUntukKelasBaru = ($idKelasBaru == 10) ? $previousAcademicYear : $newTahunAjaran;

            // Data untuk update status kelas lama
            $idsKelasLama[] = $santri['Id'];

            // Jika alumni, tidak perlu collect listMateri dan dataNilaiBaru
            if ($idKelasBaru == 10) {
                continue;
            }

            // Data untuk insert kelas baru
            $dataKelasBaru[] = [
                'IdKelas' => $idKelasBaru,
                'IdTpq' => $idTpq,
                'IdSantri' => $idSantri,
                'IdTahunAjaran' => $tahunAjaranUntukKelasBaru
            ];

            // Data nilai
            $listMateriPelajaran = $this->helpFunction->getKelasMateriPelajaran($idKelasBaru, $idTpq);
            foreach ($listMateriPelajaran as $materiPelajaran) {
                if ($materiPelajaran->SemesterGanjil == 1) {
                    $dataNilaiBaru[] = [
                        'IdTpq' => $idTpq,
                        'IdSantri' => $idSantri,
                        'IdKelas' => $materiPelajaran->IdKelas,
                        'IdMateri' => $materiPelajaran->IdMateri,
                        'IdTahunAjaran' => $tahunAjaranUntukKelasBaru,
                        'Semester' => "Ganjil"
                    ];
                }
                if ($materiPelajaran->SemesterGenap == 1) {
                    $dataNilaiBaru[] = [
                        'IdTpq' => $idTpq,
                        'IdSantri' => $idSantri,
                        'IdKelas' => $materiPelajaran->IdKelas,
                        'IdMateri' => $materiPelajaran->IdMateri,
                        'IdTahunAjaran' => $tahunAjaranUntukKelasBaru,
                        'Semester' => "Genap"
                    ];
                }
            }
        }

        // update tabel santri dengan IdSantri dan IdKelas baru  
        if (!empty($dataSantri)) {
            $this->santriModel->updateBatch($dataSantri, 'IdSantri');
        }

        // Insert semua santri naik kelas sekaligus
        if (!empty($dataKelasBaru)) {
            $this->kelasModel->insertKelasBaruBatch($dataKelasBaru);
        }

        // Update status kelas lama sekaligus
        if (!empty($idsKelasLama)) {
            $this->kelasModel->updateStatusKelasLama($idsKelasLama);
        }

        // Insert semua nilai sekaligus
        if (!empty($dataNilaiBaru)) {
            $this->nilaiModel->insertBatch($dataNilaiBaru);
        }

        return redirect()->to('backend/kelas/showListSantriPerKelas/' . $idTahunAjaran);
    }
    // Metode untuk menyimpan data daan mengupdate di tabel 
    // tbl_kelas_santri : menampatkan registrasi di kelas di himpun berdasarkan kelas dan tahun ajaran set aktif
    // menampilkan kembali di view/backend/kelas/kelasBaru
    public function setKelasSantriBaru()
    {
        // Step 1 definisi set Tahun Ajaran saat ini
        $idTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

        // Step 2 ambil data yang dikirim dari proses POST masukan santri baru ke tabel tbl_kelas_santri 
        // Data ini diambildari data satri yang sudah registarasi tapi belum dimasukan ke kelas
        $idKelasArray = $this->request->getVar('IdKelas');
        $idTpqArray = $this->request->getVar('IdTpq');
        
        /**
         * PENTING: Validasi IdTpq dari form dengan IdTpq di tbl_santri_baru
         * Untuk memastikan konsistensi data, terutama untuk santri yang pindah TPQ
         */
        $dataSantriBaru = [];
        foreach ($idKelasArray as $idSantri => $idKelas) {
            // Memastikan bahwa IdTpq untuk IdSantri yang sama juga tersedia
            if (isset($idTpqArray[$idSantri])) {
                // Ambil IdTpq aktual dari database untuk validasi
                $santriData = $this->santriBaruModel->where('IdSantri', $idSantri)->first();
                
                if (!$santriData) {
                    log_message('warning', "Santri dengan IdSantri {$idSantri} tidak ditemukan");
                    continue;
                }
                
                // Gunakan IdTpq dari database (bukan dari form) untuk keamanan
                // Ini memastikan IdTpq yang digunakan sesuai dengan data aktual di tbl_santri_baru
                $idTpqAktual = $santriData['IdTpq'];
                
                // Validasi: pastikan IdTpq dari form sama dengan di database
                if ($idTpqArray[$idSantri] != $idTpqAktual) {
                    log_message('warning', "IdTpq tidak konsisten untuk IdSantri {$idSantri}. Form: {$idTpqArray[$idSantri]}, DB: {$idTpqAktual}");
                    // Gunakan IdTpq dari database untuk keamanan
                }
                
                $dataSantriBaru[] = [
                    'IdSantri' => $idSantri,
                    'IdKelas' => $idKelas,
                    'IdTpq' => $idTpqAktual, // Gunakan IdTpq dari database
                    'IdTahunAjaran' => $idTahunAjaran
                ];
            }
        }


        // Step 3 Update IdKelas pada tabel santri sesuai dataSantriBaru
        $dataUpdateSantri = [];
        foreach ($dataSantriBaru as $row) {
            $dataUpdateSantri[] = [
                'IdSantri' => $row['IdSantri'], // ambil dari data santri
                'IdKelas' => $row['IdKelas']
            ];
        }
        if (!empty($dataUpdateSantri)) {
            $this->santriModel->updateBatch($dataUpdateSantri, 'IdSantri');
        }

        // Step 4 Generate nilai dan kelas santri dengan IdTpq yang baru
        // Fungsi ini akan:
        // - Insert ke tbl_kelas_santri dengan IdTpq baru
        // - Generate nilai di tbl_nilai dengan IdTpq baru
        // - Semua menggunakan IdTpq dari dataSantriBaru (yang sudah valid)
        try {
            // Gunakan method yang dioptimasi dengan bulk operations
            $result = $this->helpFunction->saveDataSantriDanMateriDiTabelNilaiOptimized(0, $dataSantriBaru);

            if ($result['success'] > 0) {
                $this->setFlashData('success', "Berhasil memproses {$result['success']} santri baru.");
            }

            if ($result['errors'] > 0) {
                $this->setFlashData('warning', "Ada {$result['errors']} santri yang gagal diproses.");
            }
        } catch (\Exception $e) {
            // Log error dan fallback ke method lama
            log_message('error', 'Error in saveDataSantriDanMateriDiTabelNilaiOptimized: ' . $e->getMessage());
            $this->helpFunction->saveDataSantriDanMateriDiTabelNilai(0, santriList: $dataSantriBaru);
            $this->setFlashData('info', 'Santri baru diproses dengan method fallback.');
        }

        //Check kembali jika masih ada dan tampilkan
        $dataKelas = $this->helpFunction->getDataKelas();

        $dataStatusSantriBaru = $this->helpFunction->getDataSantriStatus(IdTpq: $this->IdTpq);
        $dataTpq = $this->helpFunction->getDataTpq($this->IdTpq);
        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $dataStatusSantriBaru,
            'dataKelas' => $dataKelas,
            'dataTpq' => $dataTpq
        ];

        return view('backend/kelas/kelasBaru', $data);
    }

    private function setFlashData($type, $message)
    {
        session()->setFlashdata('pesan', '
        <div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
            ' . $message . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');
    }

    /**
     * Menampilkan halaman untuk mengecek duplikasi IdSantri di tbl_kelas_santri
     * @return view
     */
    public function showCheckDuplikasiKelasSantri()
    {
        $data = [
            'page_title' => 'Cek Duplikasi Kelas Santri'
        ];

        return view('backend/kelas/checkDuplikasiKelasSantri', $data);
    }

    /**
     * Mengecek duplikasi IdSantri di tbl_kelas_santri
     * Duplikasi terjadi jika IdSantri yang sama memiliki IdKelas yang sama pada IdTahunAjaran yang sama dan IdTpq yang sama
     * @return json
     */
    public function checkDuplikasiKelasSantri()
    {
        $IdTpq = session()->get('IdTpq');

        try {
            // Ambil data duplikasi dari model
            $duplikasi = $this->kelasModel->getDuplikasiKelasSantri($IdTpq);

            // Ambil detail duplikasi dengan informasi santri, kelas, dan TPQ
            $result = $this->kelasModel->getDetailDuplikasiKelasSantri($duplikasi);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result,
                'total_duplikasi' => count($result)
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Menormalisasi duplikasi dengan menghapus record duplikasi
     * Menyisakan 1 record per kombinasi IdSantri, IdKelas, IdTahunAjaran, IdTpq
     * Menyisakan record dengan Id terkecil (terlama)
     * @return json
     */
    public function normalisasiDuplikasiKelasSantri()
    {
        $IdTpq = session()->get('IdTpq');
        $selectedData = $this->request->getPost('selectedData');

        try {
            // Jika ada data yang dipilih, hanya normalisasi yang dipilih
            if (!empty($selectedData) && is_array($selectedData)) {
                $result = $this->kelasModel->normalisasiDuplikasiKelasSantriSelected($selectedData);
            } else {
                // Jika tidak ada data yang dipilih, normalisasi semua
                $result = $this->kelasModel->normalisasiDuplikasiKelasSantri($IdTpq);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Berhasil menormalisasi {$result['total_groups']} grup duplikasi. Total {$result['total_deleted']} record dihapus.",
                'total_groups' => $result['total_groups'],
                'total_deleted' => $result['total_deleted']
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
