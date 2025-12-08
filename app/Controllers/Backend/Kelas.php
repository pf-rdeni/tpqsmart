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

        // Ambil list tahun ajaran dari session atau buat default
        $tahunAjaranSaatIni = $this->helpFunction->getTahunAjaranSaatIni();
        $tahunAjaranBerikutnya = $this->helpFunction->getTahuanAjaranBerikutnya($tahunAjaranSaatIni);
        $tahunAjaranList = session()->get('IdTahunAjaranList');
        
        // Jika tidak ada di session, gunakan tahun ajaran saat ini
        if (empty($tahunAjaranList) || !is_array($tahunAjaranList)) {
            $tahunAjaranList = [$tahunAjaranSaatIni];
        }
        
        // Pastikan tahun ajaran saat ini ada di list
        if (!in_array($tahunAjaranSaatIni, $tahunAjaranList)) {
            $tahunAjaranList[] = $tahunAjaranSaatIni;
        }
        
        // Tambahkan tahun ajaran berikutnya ke list
        if (!in_array($tahunAjaranBerikutnya, $tahunAjaranList)) {
            $tahunAjaranList[] = $tahunAjaranBerikutnya;
        }
        
        // Sort tahun ajaran descending
        rsort($tahunAjaranList);

        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $dataSantri,
            'dataKelas' => $dataKelas,
            'dataTpq' => $dataTpq,
            'tahunAjaranList' => $tahunAjaranList,
            'tahunAjaranSaatIni' => $tahunAjaranSaatIni,
            'tahunAjaranBerikutnya' => $tahunAjaranBerikutnya,
            'IdTpq' => $this->IdTpq
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
        
        // Ambil array santri yang dipilih (checkbox)
        $selectedSantri = $this->request->getVar('selectedSantri');
        
        // Jika tidak ada santri yang dipilih, redirect dengan pesan
        if (empty($selectedSantri) || !is_array($selectedSantri)) {
            $this->setFlashData('warning', 'Tidak ada santri yang dipilih untuk diproses.');
            
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
        
        // Konversi array selectedSantri menjadi array dengan key sebagai value
        $selectedSantriArray = array_flip($selectedSantri);
        
        $idKelasArray = $this->request->getVar('IdKelas');
        $idTpqArray = $this->request->getVar('IdTpq');
        
        /**
         * PENTING: Validasi IdTpq dari form dengan IdTpq di tbl_santri_baru
         * Untuk memastikan konsistensi data, terutama untuk santri yang pindah TPQ
         * Hanya proses santri yang ada di array selectedSantri
         */
        $dataSantriBaru = [];
        foreach ($idKelasArray as $idSantri => $idKelas) {
            // Hanya proses santri yang tercentang
            if (!isset($selectedSantriArray[$idSantri])) {
                continue;
            }
            
            // Memastikan bahwa IdTpq untuk IdSantri yang sama juga tersedia
            if (isset($idTpqArray[$idSantri])) {
                // Ambil IdTpq aktual dari database untuk validasi
                $santriData = $this->santriBaruModel->where('IdSantri', $idSantri)->first();
                
                if (!$santriData) {
                    log_message('warning', "Santri dengan IdSantri {$idSantri} tidak ditemukan");
                    continue;
                }
                
                // Validasi IdKelas tidak kosong
                if (empty($idKelas)) {
                    log_message('warning', "IdKelas kosong untuk IdSantri {$idSantri}");
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

        // Validasi apakah ada data yang valid untuk diproses
        if (empty($dataSantriBaru)) {
            $this->setFlashData('warning', 'Tidak ada data santri yang valid untuk diproses. Pastikan semua santri yang dipilih sudah memiliki kelas yang dipilih.');
            
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

        // Step 4 Generate nilai dan kelas santri dengan IdTpq yang baru
        // Fungsi ini akan:
        // - Insert ke tbl_kelas_santri dengan IdTpq baru
        // - Generate nilai di tbl_nilai dengan IdTpq baru
        // - Semua menggunakan IdTpq dari dataSantriBaru (yang sudah valid)
        $totalDiproses = count($dataSantriBaru);
        
        try {
            // Gunakan method yang dioptimasi dengan bulk operations
            $result = $this->helpFunction->saveDataSantriDanMateriDiTabelNilaiOptimized(0, $dataSantriBaru);

            if ($result['success'] > 0) {
                $this->setFlashData('success', "Berhasil memproses {$result['success']} dari {$totalDiproses} santri baru yang dipilih.");
            }

            if ($result['errors'] > 0) {
                $this->setFlashData('warning', "Ada {$result['errors']} dari {$totalDiproses} santri yang gagal diproses.");
            }
        } catch (\Exception $e) {
            // Log error dan fallback ke method lama
            log_message('error', 'Error in saveDataSantriDanMateriDiTabelNilaiOptimized: ' . $e->getMessage());
            $this->helpFunction->saveDataSantriDanMateriDiTabelNilai(0, santriList: $dataSantriBaru);
            $this->setFlashData('info', "Santri baru diproses dengan method fallback. Total {$totalDiproses} santri diproses.");
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

    /**
     * Mengecek data nilai yang memiliki IdSantri, IdTpq, IdTahunAjaran 
     * tetapi IdSantri tersebut tidak ada di tabel KelasSantri
     * @return json
     */
    public function checkNilaiTanpaKelasSantri()
    {
        $IdTpq = session()->get('IdTpq');

        try {
            $db = \Config\Database::connect();

            // Query untuk mendapatkan data nilai yang tidak ada di tbl_kelas_santri
            // dengan kombinasi IdSantri, IdTpq, IdTahunAjaran yang sama
            $builder = $db->table('tbl_nilai n');
            $builder->select('n.*, s.NamaSantri, m.NamaMateri, k.NamaKelas, t.NamaTpq');
            $builder->join('tbl_santri_baru s', 's.IdSantri = n.IdSantri', 'left');
            $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = n.IdMateri', 'left');
            $builder->join('tbl_kelas k', 'k.IdKelas = n.IdKelas', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = n.IdTpq', 'left');

            // Left join dengan tbl_kelas_santri untuk menemukan yang tidak ada
            // PENTING: Cek semua record di kelas_santri (aktif dan tidak aktif) 
            // untuk kombinasi IdSantri, IdTpq, IdTahunAjaran yang sama
            // Jika tidak ada sama sekali, berarti santri sudah pindah TPQ atau tidak ada di kelas tersebut
            $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = n.IdSantri AND ks.IdTpq = n.IdTpq AND ks.IdTahunAjaran = n.IdTahunAjaran', 'left');

            // Hanya ambil yang tidak ada di tbl_kelas_santri (ks.Id IS NULL)
            // Ini berarti untuk kombinasi IdSantri, IdTpq, IdTahunAjaran tersebut
            // TIDAK ADA record sama sekali di tbl_kelas_santri (baik aktif maupun tidak aktif)
            $builder->where('ks.Id', null);

            // Filter berdasarkan IdTpq jika ada
            if ($IdTpq != null && $IdTpq != 0) {
                $builder->where('n.IdTpq', $IdTpq);
            }

            $builder->orderBy('n.IdSantri', 'ASC');
            $builder->orderBy('n.IdTahunAjaran', 'ASC');
            $builder->orderBy('n.IdTpq', 'ASC');

            $result = $builder->get()->getResultArray();

            // Hitung rangkuman per TPQ dengan detail lebih lengkap
            $summaryByTpq = [];

            // Format data untuk response dengan informasi detail
            $formattedData = [];
            foreach ($result as $item) {
                $idSantri = $item['IdSantri'];
                $idTpqNilai = $item['IdTpq'];
                $idTahunAjaran = $item['IdTahunAjaran'];

                // Cek apakah santri masih ada di tbl_santri_baru
                $santriInfo = $db->table('tbl_santri_baru')
                    ->select('IdTpq, Active, Status')
                    ->where('IdSantri', $idSantri)
                    ->get()
                    ->getRowArray();

                // Cek apakah santri ada di kelas_santri dengan TPQ lain
                $kelasSantriLain = $db->table('tbl_kelas_santri')
                    ->select('IdTpq, Status')
                    ->where('IdSantri', $idSantri)
                    ->where('IdTahunAjaran', $idTahunAjaran)
                    ->where('IdTpq !=', $idTpqNilai)
                    ->get()
                    ->getResultArray();

                // Tentukan jenis masalah dan alasan
                $jenisMasalah = 'Tidak Ada di Kelas Santri';
                $reason = 'IdSantri tidak ada di tbl_kelas_santri untuk kombinasi IdTpq dan IdTahunAjaran ini';
                $kategori = 'tidak_diketahui';

                if ($santriInfo) {
                    // Santri masih ada di database
                    if ($santriInfo['IdTpq'] != $idTpqNilai) {
                        // Santri pindah ke TPQ lain
                        $tpqBaru = $db->table('tbl_tpq')
                            ->select('NamaTpq')
                            ->where('IdTpq', $santriInfo['IdTpq'])
                            ->get()
                            ->getRowArray();

                        $namaTpqBaru = $tpqBaru['NamaTpq'] ?? 'TPQ Lain';
                        $jenisMasalah = 'Pindah ke TPQ Lain';
                        $reason = "Santri sudah pindah ke TPQ: {$namaTpqBaru} (IdTpq: {$santriInfo['IdTpq']}). Data nilai di TPQ lama perlu dihapus.";
                        $kategori = 'pindah_tpq';
                    } else if (!empty($kelasSantriLain)) {
                        // Ada record di kelas_santri dengan TPQ lain untuk tahun ajaran yang sama
                        $tpqLain = $db->table('tbl_tpq')
                            ->select('NamaTpq')
                            ->whereIn('IdTpq', array_column($kelasSantriLain, 'IdTpq'))
                            ->get()
                            ->getResultArray();

                        $namaTpqLain = !empty($tpqLain) ? $tpqLain[0]['NamaTpq'] : 'TPQ Lain';
                        $jenisMasalah = 'Pindah ke TPQ Lain';
                        $reason = "Santri memiliki record di kelas_santri dengan TPQ lain ({$namaTpqLain}) untuk tahun ajaran yang sama. Data nilai di TPQ ini perlu dihapus.";
                        $kategori = 'pindah_tpq';
                    } else if ($santriInfo['Active'] == 0) {
                        // Santri tidak aktif
                        $jenisMasalah = 'Santri Tidak Aktif';
                        $reason = "Santri masih ada di database tetapi status Active = 0 (tidak aktif). Data nilai perlu dihapus.";
                        $kategori = 'tidak_aktif';
                    } else {
                        // Santri ada tapi tidak ada di kelas_santri untuk kombinasi ini
                        $jenisMasalah = 'Tidak Terdaftar di Kelas';
                        $reason = "Santri masih ada di TPQ ini tetapi tidak terdaftar di kelas_santri untuk tahun ajaran {$idTahunAjaran}. Kemungkinan belum di-assign ke kelas atau data kelas_santri terhapus.";
                        $kategori = 'tidak_terdaftar';
                    }
                } else {
                    // Santri tidak ada sama sekali di database
                    $jenisMasalah = 'Santri Tidak Ditemukan';
                    $reason = "Santri tidak ditemukan di tbl_santri_baru. Kemungkinan data santri sudah dihapus atau pindah luar daerah (belum terdefinisikan di status santri).";
                    $kategori = 'tidak_ditemukan';
                }

                $formattedItem = [
                    'Id' => $item['Id'],
                    'IdSantri' => $idSantri,
                    'NamaSantri' => $item['NamaSantri'] ?? 'Tidak ditemukan',
                    'IdMateri' => $item['IdMateri'],
                    'NamaMateri' => $item['NamaMateri'] ?? 'Tidak ditemukan',
                    'IdKelas' => $item['IdKelas'],
                    'NamaKelas' => $item['NamaKelas'] ?? 'Tidak ditemukan',
                    'IdTpq' => $idTpqNilai,
                    'NamaTpq' => $item['NamaTpq'] ?? 'Tidak ditemukan',
                    'IdTahunAjaran' => $idTahunAjaran,
                    'Semester' => $item['Semester'],
                    'Nilai' => $item['Nilai'] ?? 0,
                    'type' => 'tanpa_kelas_santri',
                    'jenis_masalah' => $jenisMasalah,
                    'kategori' => $kategori,
                    'reason' => $reason
                ];

                $formattedData[] = $formattedItem;

                // Hitung rangkuman per TPQ
                $idTpq = $formattedItem['IdTpq'];
                $namaTpq = $formattedItem['NamaTpq'] ?? 'Tidak ditemukan';

                if (!isset($summaryByTpq[$idTpq])) {
                    $summaryByTpq[$idTpq] = [
                        'IdTpq' => $idTpq,
                        'NamaTpq' => $namaTpq,
                        'total' => 0,
                        'total_dengan_nilai' => 0,      // Data yang memiliki nilai > 0
                        'total_tanpa_nilai' => 0,        // Data yang tidak memiliki nilai (0 atau null)
                        'santri_pindah_tpq' => [],        // Array IdSantri unik dengan kategori pindah_tpq
                        'santri_tidak_aktif' => [],       // Array IdSantri unik dengan kategori tidak_aktif
                        'santri_tidak_terdaftar' => [],  // Array IdSantri unik dengan kategori tidak_terdaftar
                        'santri_tidak_ditemukan' => [],   // Array IdSantri unik dengan kategori tidak_ditemukan
                        'santri_tidak_diketahui' => [],   // Array IdSantri unik dengan kategori tidak_diketahui
                        'santri_terkena' => [],
                        'tahun_ajaran_terkena' => []     // Tahun ajaran yang terkena
                    ];
                }

                $summaryByTpq[$idTpq]['total']++;

                // Hitung data dengan nilai dan tanpa nilai
                $nilai = (float)($formattedItem['Nilai'] ?? 0);
                if ($nilai > 0) {
                    $summaryByTpq[$idTpq]['total_dengan_nilai']++;
                } else {
                    $summaryByTpq[$idTpq]['total_tanpa_nilai']++;
                }

                // Kumpulkan IdSantri berdasarkan kategori jenis masalah (unik per kategori)
                $idSantri = $formattedItem['IdSantri'];
                if (isset($idSantri)) {
                    switch ($kategori) {
                        case 'pindah_tpq':
                            if (!in_array($idSantri, $summaryByTpq[$idTpq]['santri_pindah_tpq'])) {
                                $summaryByTpq[$idTpq]['santri_pindah_tpq'][] = $idSantri;
                            }
                            break;
                        case 'tidak_aktif':
                            if (!in_array($idSantri, $summaryByTpq[$idTpq]['santri_tidak_aktif'])) {
                                $summaryByTpq[$idTpq]['santri_tidak_aktif'][] = $idSantri;
                            }
                            break;
                        case 'tidak_terdaftar':
                            if (!in_array($idSantri, $summaryByTpq[$idTpq]['santri_tidak_terdaftar'])) {
                                $summaryByTpq[$idTpq]['santri_tidak_terdaftar'][] = $idSantri;
                            }
                            break;
                        case 'tidak_ditemukan':
                            if (!in_array($idSantri, $summaryByTpq[$idTpq]['santri_tidak_ditemukan'])) {
                                $summaryByTpq[$idTpq]['santri_tidak_ditemukan'][] = $idSantri;
                            }
                            break;
                        default:
                            if (!in_array($idSantri, $summaryByTpq[$idTpq]['santri_tidak_diketahui'])) {
                                $summaryByTpq[$idTpq]['santri_tidak_diketahui'][] = $idSantri;
                            }
                    }
                }

                // Kumpulkan IdSantri yang terkena (unik)
                if (isset($formattedItem['IdSantri']) && !in_array($formattedItem['IdSantri'], $summaryByTpq[$idTpq]['santri_terkena'])) {
                    $summaryByTpq[$idTpq]['santri_terkena'][] = $formattedItem['IdSantri'];
                }

                // Kumpulkan tahun ajaran yang terkena (unik)
                if (isset($formattedItem['IdTahunAjaran']) && !in_array($formattedItem['IdTahunAjaran'], $summaryByTpq[$idTpq]['tahun_ajaran_terkena'])) {
                    $summaryByTpq[$idTpq]['tahun_ajaran_terkena'][] = $formattedItem['IdTahunAjaran'];
                }
            }

            // Hitung jumlah santri dan tahun ajaran yang terkena per TPQ
            foreach ($summaryByTpq as $key => $tpq) {
                $summaryByTpq[$key]['jumlah_santri_terkena'] = count($tpq['santri_terkena']);
                $summaryByTpq[$key]['jumlah_tahun_ajaran_terkena'] = count($tpq['tahun_ajaran_terkena']);

                // Hitung jumlah santri unik per kategori (bukan jumlah record)
                $summaryByTpq[$key]['total_pindah_tpq'] = count($tpq['santri_pindah_tpq']);
                $summaryByTpq[$key]['total_tidak_aktif'] = count($tpq['santri_tidak_aktif']);
                $summaryByTpq[$key]['total_tidak_terdaftar'] = count($tpq['santri_tidak_terdaftar']);
                $summaryByTpq[$key]['total_tidak_ditemukan'] = count($tpq['santri_tidak_ditemukan']);
                $summaryByTpq[$key]['total_tidak_diketahui'] = count($tpq['santri_tidak_diketahui']);

                // Hapus array dari output (tidak perlu dikirim ke frontend)
                unset($summaryByTpq[$key]['santri_terkena']);
                unset($summaryByTpq[$key]['tahun_ajaran_terkena']);
                unset($summaryByTpq[$key]['santri_pindah_tpq']);
                unset($summaryByTpq[$key]['santri_tidak_aktif']);
                unset($summaryByTpq[$key]['santri_tidak_terdaftar']);
                unset($summaryByTpq[$key]['santri_tidak_ditemukan']);
                unset($summaryByTpq[$key]['santri_tidak_diketahui']);
            }

            // Convert to array untuk JSON
            $summaryByTpqArray = array_values($summaryByTpq);

            return $this->response->setJSON([
                'success' => true,
                'total_checked' => count($result),
                'total_to_delete' => count($result),
                'data' => $formattedData,
                'summary_by_tpq' => $summaryByTpqArray
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Kelas: checkNilaiTanpaKelasSantri - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Menormalisasi data nilai yang tidak memiliki referensi di tbl_kelas_santri
     * Menghapus data nilai berdasarkan ID yang dipilih
     * @return json
     */
    public function normalisasiNilaiTanpaKelasSantri()
    {
        try {
            $json = $this->request->getJSON();
            $idsToDelete = $json->ids ?? [];

            if (empty($idsToDelete)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih untuk dihapus'
                ]);
            }

            // Validasi bahwa ids adalah array
            if (!is_array($idsToDelete)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format data tidak valid'
                ]);
            }

            log_message('info', 'Kelas: normalisasiNilaiTanpaKelasSantri - Akan menghapus ' . count($idsToDelete) . ' records');

            $deletedCount = 0;
            $errors = [];

            // Hapus data berdasarkan ID
            if (!empty($idsToDelete)) {
                try {
                    $deletedCount = $this->nilaiModel->whereIn('Id', $idsToDelete)->delete();

                    if ($deletedCount === false) {
                        throw new \Exception('Gagal menghapus data');
                    }

                    log_message('info', 'Kelas: normalisasiNilaiTanpaKelasSantri - Berhasil menghapus ' . $deletedCount . ' records');
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus data: " . $e->getMessage();
                    log_message('error', 'Kelas: normalisasiNilaiTanpaKelasSantri - Error menghapus data: ' . $e->getMessage());
                }
            }

            $message = "Normalisasi selesai. Data yang dihapus: {$deletedCount} dari " . count($idsToDelete) . " yang dipilih.";

            if (!empty($errors)) {
                $message .= " Terjadi beberapa error: " . implode(', ', $errors);
            }

            log_message('info', 'Kelas: normalisasiNilaiTanpaKelasSantri - ' . $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'data' => [
                    'total_selected' => count($idsToDelete),
                    'deleted_count' => $deletedCount,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Kelas: normalisasiNilaiTanpaKelasSantri - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal melakukan normalisasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mengecek santri yang aktif (Active = 1) di tbl_santri_baru 
     * Prioritas utama: harus memiliki Kelas Santri dan harus memiliki materi nilai di tabel nilai
     * Juga mengecek duplikasi: jika IdSantri Active dan di tabel kelas IdSantri Status 1 dengan IdTpq Berbeda tapi kelas sama
     * @return json
     */
    public function checkSantriAktifTanpaKelas()
    {
        $IdTpq = session()->get('IdTpq');

        try {
            $db = \Config\Database::connect();
            $idTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

            // ============================================
            // PRIORITAS UTAMA: Check santri aktif harus punya kelas santri dan nilai
            // ============================================
            
            // Query 1: Santri aktif yang tidak ada di tbl_kelas_santri untuk IdTpq aktifnya
            $builder1 = $db->table('tbl_santri_baru s');
            $builder1->select('s.IdSantri, s.NamaSantri, s.IdTpq, s.IdKelas, s.Active, s.Status, 
                            t.NamaTpq, k.NamaKelas, NULL as IdKelasSantri, NULL as IdKelasSantriLain, 
                            NULL as IdTpqLain, NULL as NamaTpqLain');
            $builder1->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left');
            $builder1->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left');

            // Left join dengan tbl_kelas_santri untuk IdTpq yang sama dengan santri (TPQ aktif)
            $builder1->join(
                'tbl_kelas_santri ks',
                'ks.IdSantri = s.IdSantri AND ks.IdTahunAjaran = ' . $db->escape($idTahunAjaran) . ' AND ks.IdTpq = s.IdTpq',
                'left'
            );

            // Hanya ambil santri yang:
            // 1. Active = 1 (aktif)
            // 2. Tidak ada di tbl_kelas_santri untuk IdTpq aktifnya (ks.Id IS NULL)
            // 3. Bukan kelas alumni (IdKelas != 10)
            $builder1->where('s.Active', 1);
            $builder1->where('ks.Id IS NULL', null, false);
            $builder1->where('s.IdKelas !=', 10);
            $builder1->where('s.IdKelas IS NOT NULL', null, false);

            // Filter berdasarkan IdTpq jika ada
            if ($IdTpq != null && $IdTpq != 0) {
                $builder1->where('s.IdTpq', $IdTpq);
            }

            $query1 = $builder1->getCompiledSelect(false);

            // Query 2: Santri aktif yang ada di tbl_kelas_santri untuk IdTpq aktifnya tetapi tidak ada data nilai
            $builder2 = $db->table('tbl_santri_baru s');
            $builder2->select('s.IdSantri, s.NamaSantri, s.IdTpq, ks.IdKelas, s.Active, s.Status, 
                            t.NamaTpq, k.NamaKelas, ks.Id as IdKelasSantri, NULL as IdKelasSantriLain, 
                            NULL as IdTpqLain, NULL as NamaTpqLain');
            $builder2->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left');
            $builder2->join(
                'tbl_kelas_santri ks',
                'ks.IdSantri = s.IdSantri AND ks.IdTahunAjaran = ' . $db->escape($idTahunAjaran) . ' AND ks.Status = 1 AND ks.IdTpq = s.IdTpq',
                'inner'
            );
            $builder2->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left');

            // Left join dengan tbl_nilai untuk menemukan yang tidak ada data nilai
            $builder2->join(
                'tbl_nilai n',
                'n.IdSantri = s.IdSantri AND n.IdTahunAjaran = ' . $db->escape($idTahunAjaran) . ' AND n.IdTpq = s.IdTpq',
                'left'
            );

            // Hanya ambil santri yang:
            // 1. Active = 1 (aktif)
            // 2. Ada di tbl_kelas_santri untuk IdTpq aktifnya (Status = 1)
            // 3. Tidak ada data nilai (n.Id IS NULL)
            // 4. Bukan kelas alumni (ks.IdKelas != 10)
            $builder2->where('s.Active', 1);
            $builder2->where('n.Id IS NULL', null, false);
            $builder2->where('ks.IdKelas !=', 10);
            $builder2->where('ks.IdKelas IS NOT NULL', null, false);

            // Filter berdasarkan IdTpq jika ada
            if ($IdTpq != null && $IdTpq != 0) {
                $builder2->where('s.IdTpq', $IdTpq);
            }

            $query2 = $builder2->getCompiledSelect(false);

            // Query 3: Santri aktif yang punya kelas santri di TPQ berbeda (duplikasi)
            // IdSantri Active dan di tabel kelas IdSantri Status 1 dengan IdTpq Berbeda tapi kelas sama
            $builder3 = $db->table('tbl_santri_baru s');
            $builder3->select('s.IdSantri, s.NamaSantri, s.IdTpq, s.IdKelas, s.Active, s.Status, 
                            t.NamaTpq, k.NamaKelas, NULL as IdKelasSantri, ks_lain.Id as IdKelasSantriLain, 
                            ks_lain.IdTpq as IdTpqLain, t_lain.NamaTpq as NamaTpqLain');
            $builder3->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left');
            $builder3->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left');
            
            // Join dengan tbl_kelas_santri untuk TPQ aktif (harus tidak ada)
            $builder3->join(
                'tbl_kelas_santri ks',
                'ks.IdSantri = s.IdSantri AND ks.IdTahunAjaran = ' . $db->escape($idTahunAjaran) . ' AND ks.IdTpq = s.IdTpq',
                'left'
            );
            
            // Join dengan tbl_kelas_santri untuk TPQ lain (harus ada)
            $builder3->join(
                'tbl_kelas_santri ks_lain',
                'ks_lain.IdSantri = s.IdSantri AND ks_lain.IdTahunAjaran = ' . $db->escape($idTahunAjaran) . ' AND ks_lain.Status = 1 AND ks_lain.IdTpq != s.IdTpq AND ks_lain.IdKelas = s.IdKelas',
                'inner'
            );
            $builder3->join('tbl_tpq t_lain', 't_lain.IdTpq = ks_lain.IdTpq', 'left');

            // Hanya ambil santri yang:
            // 1. Active = 1 (aktif)
            // 2. Tidak ada di tbl_kelas_santri untuk IdTpq aktifnya (ks.Id IS NULL)
            // 3. Ada di tbl_kelas_santri untuk TPQ lain (ks_lain.Id IS NOT NULL)
            // 4. Bukan kelas alumni (IdKelas != 10)
            $builder3->where('s.Active', 1);
            $builder3->where('ks.Id IS NULL', null, false);
            $builder3->where('s.IdKelas !=', 10);
            $builder3->where('s.IdKelas IS NOT NULL', null, false);

            // Filter berdasarkan IdTpq jika ada
            if ($IdTpq != null && $IdTpq != 0) {
                $builder3->where('s.IdTpq', $IdTpq);
            }

            // Jalankan query terpisah dan gabungkan hasilnya
            $result1 = $builder1->get()->getResultArray();
            $result2 = $builder2->get()->getResultArray();
            $result3 = $builder3->get()->getResultArray();
            
            // Gabungkan hasil
            $result = array_merge($result1, $result2, $result3);
            
            // Urutkan hasil
            usort($result, function($a, $b) {
                if ($a['IdTpq'] == $b['IdTpq']) {
                    return strcmp($a['NamaSantri'] ?? '', $b['NamaSantri'] ?? '');
                }
                return $a['IdTpq'] <=> $b['IdTpq'];
            });

            // Hitung rangkuman per TPQ
            $summaryByTpq = [];
            $formattedData = [];

            foreach ($result as $item) {
                $idTpq = $item['IdTpq'];
                $namaTpq = $item['NamaTpq'] ?? 'Tidak ditemukan';
                $namaKelas = $item['NamaKelas'] ?? 'Belum ada kelas';
                $idKelas = $item['IdKelas'] ?? null;
                $idKelasSantri = $item['IdKelasSantri'] ?? null;
                $idKelasSantriLain = $item['IdKelasSantriLain'] ?? null;
                $idTpqLain = $item['IdTpqLain'] ?? null;
                $namaTpqLain = $item['NamaTpqLain'] ?? null;

                // Tentukan type dan reason
                $type = 'santri_aktif_tanpa_kelas';
                if (!empty($idKelasSantriLain)) {
                    // Kasus duplikasi: ada di TPQ lain tapi tidak di TPQ aktif
                    $reason = 'Santri aktif (Active = 1) terdaftar di kelas santri TPQ lain (' . ($namaTpqLain ?? 'TPQ ' . $idTpqLain) . ') tetapi tidak terdaftar di TPQ aktif (' . $namaTpq . ') untuk tahun ajaran ' . $idTahunAjaran;
                    $type = 'santri_duplikasi_tpq';
                } else if (!empty($idKelasSantri)) {
                    // Santri ada di tbl_kelas_santri tetapi tidak ada data nilai
                    $reason = 'Santri aktif (Active = 1) terdaftar di kelas santri tetapi tidak memiliki data nilai untuk tahun ajaran ' . $idTahunAjaran;
                    $type = 'santri_aktif_tanpa_nilai';
                } else {
                    // Santri tidak ada di tbl_kelas_santri
                    $reason = 'Santri aktif (Active = 1) tetapi tidak terdaftar di tbl_kelas_santri untuk tahun ajaran ' . $idTahunAjaran;
                }

                $formattedItem = [
                    'IdSantri' => $item['IdSantri'],
                    'NamaSantri' => $item['NamaSantri'] ?? 'Tidak ditemukan',
                    'IdTpq' => $idTpq,
                    'NamaTpq' => $namaTpq,
                    'IdKelas' => $idKelas,
                    'NamaKelas' => $namaKelas,
                    'Active' => $item['Active'],
                    'Status' => $item['Status'] ?? null,
                    'IdTahunAjaran' => $idTahunAjaran,
                    'IdKelasSantri' => $idKelasSantri,
                    'IdKelasSantriLain' => $idKelasSantriLain,
                    'IdTpqLain' => $idTpqLain,
                    'NamaTpqLain' => $namaTpqLain,
                    'type' => $type,
                    'reason' => $reason
                ];

                $formattedData[] = $formattedItem;

                // Hitung rangkuman per TPQ
                if (!isset($summaryByTpq[$idTpq])) {
                    $summaryByTpq[$idTpq] = [
                        'IdTpq' => $idTpq,
                        'NamaTpq' => $namaTpq,
                        'total' => 0,
                        'total_dengan_kelas' => 0,      // Santri yang sudah punya IdKelas
                        'total_tanpa_kelas' => 0,        // Santri yang belum punya IdKelas
                        'total_tanpa_nilai' => 0,        // Santri yang ada kelas tetapi tidak ada nilai
                        'total_duplikasi_tpq' => 0,      // Santri yang ada di TPQ lain
                        'santri_terkena' => []
                    ];
                }

                $summaryByTpq[$idTpq]['total']++;

                // Kategorikan berdasarkan type
                if (!empty($idKelasSantriLain)) {
                    // Kasus duplikasi TPQ
                    $summaryByTpq[$idTpq]['total_duplikasi_tpq']++;
                    if (!empty($idKelas)) {
                        $summaryByTpq[$idTpq]['total_dengan_kelas']++;
                    }
                } else if (!empty($idKelasSantri)) {
                    // Santri ada di kelas santri tetapi tidak ada nilai
                    $summaryByTpq[$idTpq]['total_tanpa_nilai']++;
                    if (!empty($idKelas)) {
                        $summaryByTpq[$idTpq]['total_dengan_kelas']++;
                    }
                } else {
                    // Santri tidak ada di kelas santri
                    if (!empty($idKelas)) {
                        $summaryByTpq[$idTpq]['total_dengan_kelas']++;
                    } else {
                        $summaryByTpq[$idTpq]['total_tanpa_kelas']++;
                    }
                }

                // Kumpulkan IdSantri yang terkena (unik)
                if (!in_array($item['IdSantri'], $summaryByTpq[$idTpq]['santri_terkena'])) {
                    $summaryByTpq[$idTpq]['santri_terkena'][] = $item['IdSantri'];
                }
            }

            // Hitung jumlah santri yang terkena per TPQ
            foreach ($summaryByTpq as $key => $tpq) {
                $summaryByTpq[$key]['jumlah_santri_terkena'] = count($tpq['santri_terkena']);
                // Hapus array dari output
                unset($summaryByTpq[$key]['santri_terkena']);
            }

            // Convert to array untuk JSON
            $summaryByTpqArray = array_values($summaryByTpq);

            return $this->response->setJSON([
                'success' => true,
                'total_checked' => count($result),
                'total_to_add' => count($result),
                'data' => $formattedData,
                'summary_by_tpq' => $summaryByTpqArray,
                'id_tahun_ajaran' => $idTahunAjaran
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Kelas: checkSantriAktifTanpaKelas - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Menambahkan/update santri aktif ke tbl_kelas_santri dan memproses materi/nilai
     * Proses seperti memproses santri baru: insert ke kelas_santri dan generate nilai
     * @return json
     */
    public function updateSantriAktifTanpaKelas()
    {
        try {
            $json = $this->request->getJSON();
            $selectedSantri = $json->santri ?? [];

            if (empty($selectedSantri) || !is_array($selectedSantri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada santri yang dipilih untuk diproses'
                ]);
            }

            $db = \Config\Database::connect();
            $idTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
            $addedCount = 0;
            $updatedCount = 0;
            $nilaiGeneratedCount = 0;
            $errors = [];
            $dataSantriBaru = []; // Data untuk diproses seperti santri baru (belum ada di kelas_santri)
            $dataSantriHanyaNilai = []; // Data untuk generate nilai saja (sudah ada di kelas_santri)

            // Validasi dan siapkan data untuk diproses
            foreach ($selectedSantri as $idSantri) {
                try {
                    // Ambil data santri
                    $santri = $db->table('tbl_santri_baru')
                        ->where('IdSantri', $idSantri)
                        ->where('Active', 1)
                        ->get()
                        ->getRowArray();

                    if (!$santri) {
                        $errors[] = "Santri dengan ID {$idSantri} tidak ditemukan atau tidak aktif";
                        continue;
                    }

                    // Pastikan IdKelas ada
                    if (empty($santri['IdKelas'])) {
                        $errors[] = "Santri {$santri['NamaSantri']} (ID: {$idSantri}) belum memiliki kelas (IdKelas kosong)";
                        continue;
                    }

                    // Skip jika kelas alumni (IdKelas = 10)
                    if ($santri['IdKelas'] == 10) {
                        continue; // Skip alumni tanpa error message
                    }

                    // Cek apakah ada duplikasi: kelas santri di TPQ lain dengan kelas sama
                    $duplikasiKelasSantri = $db->table('tbl_kelas_santri')
                        ->where('IdSantri', $idSantri)
                        ->where('IdTahunAjaran', $idTahunAjaran)
                        ->where('Status', 1)
                        ->where('IdTpq !=', $santri['IdTpq'])
                        ->where('IdKelas', $santri['IdKelas'])
                        ->get()
                        ->getResultArray();

                    // Jika ada duplikasi, hapus kelas santri di TPQ lain
                    if (!empty($duplikasiKelasSantri)) {
                        foreach ($duplikasiKelasSantri as $dup) {
                            $db->table('tbl_kelas_santri')
                                ->where('Id', $dup['Id'])
                                ->delete();
                            log_message('info', 'Kelas: updateSantriAktifTanpaKelas - Menghapus duplikasi kelas santri ID ' . $dup['Id'] . ' di TPQ ' . $dup['IdTpq']);
                        }
                    }

                    // Cek apakah sudah ada di tbl_kelas_santri untuk tahun ajaran ini dan TPQ aktif
                    $existing = $db->table('tbl_kelas_santri')
                        ->where('IdSantri', $idSantri)
                        ->where('IdTahunAjaran', $idTahunAjaran)
                        ->where('IdTpq', $santri['IdTpq'])
                        ->get()
                        ->getRowArray();

                    if ($existing) {
                        // Update status menjadi aktif jika belum aktif
                        if ($existing['Status'] != 1) {
                            $db->table('tbl_kelas_santri')
                                ->where('Id', $existing['Id'])
                                ->update(['Status' => 1]);
                            $updatedCount++;
                        }

                        // Cek apakah sudah ada nilai untuk santri ini
                        $existingNilai = $db->table('tbl_nilai')
                            ->where('IdSantri', $idSantri)
                            ->where('IdTahunAjaran', $idTahunAjaran)
                            ->where('IdTpq', $santri['IdTpq'])
                            ->countAllResults();

                        // Jika belum ada nilai, perlu generate nilai
                        // Gunakan IdKelas dari existing kelas_santri jika berbeda dengan IdKelas di santri
                        $idKelasToUse = !empty($existing['IdKelas']) ? $existing['IdKelas'] : $santri['IdKelas'];
                        
                        // Skip jika kelas alumni (IdKelas = 10)
                        if ($idKelasToUse == 10) {
                            continue;
                        }
                        
                        if ($existingNilai == 0) {
                            // Masukkan ke kelompok yang hanya perlu generate nilai
                            $dataSantriHanyaNilai[] = [
                                'IdSantri' => $idSantri,
                                'IdTpq' => $santri['IdTpq'],
                                'IdKelas' => $idKelasToUse,
                                'IdTahunAjaran' => $idTahunAjaran
                            ];
                        }
                        
                        // Skip karena sudah ada di kelas_santri
                        continue;
                    }

                    // Siapkan data untuk diproses seperti santri baru (belum ada di kelas_santri)
                    // Format sesuai yang dibutuhkan saveDataSantriDanMateriDiTabelNilaiOptimized
                    $dataSantriBaru[] = [
                        'IdSantri' => $idSantri,
                        'IdTpq' => $santri['IdTpq'],
                        'IdKelas' => $santri['IdKelas'],
                        'IdTahunAjaran' => $idTahunAjaran
                    ];
                } catch (\Exception $e) {
                    $errors[] = "Error memproses santri ID {$idSantri}: " . $e->getMessage();
                    log_message('error', 'Kelas: updateSantriAktifTanpaKelas - Error untuk santri ' . $idSantri . ': ' . $e->getMessage());
                }
            }

            // Proses santri yang belum ada di kelas_santri: insert ke kelas_santri dan generate nilai
            if (!empty($dataSantriBaru)) {
                try {
                    // Gunakan method yang sama seperti memproses santri baru
                    // StatusSantri = 0 berarti seperti santri baru
                    $result = $this->helpFunction->saveDataSantriDanMateriDiTabelNilaiOptimized(0, $dataSantriBaru);

                    if ($result['success'] > 0) {
                        $addedCount = $result['success'];
                        log_message('info', 'Kelas: updateSantriAktifTanpaKelas - Berhasil memproses ' . $addedCount . ' santri (kelas_santri + nilai)');
                    }

                    if (isset($result['errors']) && $result['errors'] > 0) {
                        $errors[] = "Ada {$result['errors']} santri yang gagal diproses";
                        log_message('warning', 'Kelas: updateSantriAktifTanpaKelas - Ada ' . $result['errors'] . ' santri yang gagal diproses');
                    }
                } catch (\Exception $e) {
                    // Fallback ke method lama jika optimized gagal
                    log_message('error', 'Kelas: updateSantriAktifTanpaKelas - Error optimized method: ' . $e->getMessage());
                    try {
                        $this->helpFunction->saveDataSantriDanMateriDiTabelNilai(0, $dataSantriBaru);
                        $addedCount = count($dataSantriBaru);
                        log_message('info', 'Kelas: updateSantriAktifTanpaKelas - Berhasil memproses dengan method fallback: ' . $addedCount . ' santri');
                    } catch (\Exception $e2) {
                        $errors[] = "Gagal memproses santri: " . $e2->getMessage();
                        log_message('error', 'Kelas: updateSantriAktifTanpaKelas - Error fallback method: ' . $e2->getMessage());
                    }
                }
            }

            // Proses santri yang sudah ada di kelas_santri: hanya generate nilai
            if (!empty($dataSantriHanyaNilai)) {
                try {
                    // Gunakan method generate nilai saja (tanpa insert ke kelas_santri)
                    // Kita perlu memanggil fungsi yang hanya generate nilai
                    $resultNilai = $this->helpFunction->generateNilaiUntukSantri($dataSantriHanyaNilai, $idTahunAjaran);
                    
                    if ($resultNilai['success'] > 0) {
                        $nilaiGeneratedCount = $resultNilai['success'];
                        log_message('info', 'Kelas: updateSantriAktifTanpaKelas - Berhasil generate nilai untuk ' . $nilaiGeneratedCount . ' santri');
                    }

                    if (isset($resultNilai['errors']) && $resultNilai['errors'] > 0) {
                        $errors[] = "Ada {$resultNilai['errors']} santri yang gagal generate nilai";
                        log_message('warning', 'Kelas: updateSantriAktifTanpaKelas - Ada ' . $resultNilai['errors'] . ' santri yang gagal generate nilai');
                    }
                } catch (\Exception $e) {
                    $errors[] = "Gagal generate nilai: " . $e->getMessage();
                    log_message('error', 'Kelas: updateSantriAktifTanpaKelas - Error generate nilai: ' . $e->getMessage());
                }
            }

            $totalProcessed = $addedCount + $updatedCount + $nilaiGeneratedCount;
            $message = "Update selesai. Santri yang diproses: {$totalProcessed} dari " . count($selectedSantri) . " yang dipilih.";
            
            $details = [];
            if ($addedCount > 0) {
                $details[] = "{$addedCount} ditambahkan ke kelas_santri dan generate nilai";
            }
            if ($updatedCount > 0) {
                $details[] = "{$updatedCount} diupdate status";
            }
            if ($nilaiGeneratedCount > 0) {
                $details[] = "{$nilaiGeneratedCount} generate nilai (sudah ada di kelas_santri)";
            }
            
            if (!empty($details)) {
                $message .= " (" . implode(", ", $details) . ")";
            }

            if (!empty($errors)) {
                $message .= " Terjadi beberapa error: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " dan " . (count($errors) - 5) . " error lainnya.";
                }
            }

            log_message('info', 'Kelas: updateSantriAktifTanpaKelas - ' . $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'data' => [
                    'total_selected' => count($selectedSantri),
                    'added_count' => $addedCount,
                    'updated_count' => $updatedCount,
                    'nilai_generated_count' => $nilaiGeneratedCount,
                    'total_processed' => $totalProcessed,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Kelas: updateSantriAktifTanpaKelas - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal melakukan update: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mengecek santri yang IdKelas di tbl_santri_baru tidak sesuai dengan IdKelas di tbl_kelas_santri
     * untuk tahun ajaran saat ini
     * @return json
     */
    public function checkSantriKelasTidakSesuai()
    {
        $IdTpq = session()->get('IdTpq');

        try {
            $db = \Config\Database::connect();
            $idTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

            // Query untuk mendapatkan santri yang IdKelas di tbl_santri_baru berbeda dengan tbl_kelas_santri
            $builder = $db->table('tbl_santri_baru s');
            $builder->select('s.IdSantri, s.NamaSantri, s.IdTpq, s.IdKelas AS IdKelasSantriBaru, s.Active, s.Status,
                            ks.Id AS IdKelasSantri, ks.IdKelas AS IdKelasKelasSantri, ks.Status AS StatusKelasSantri,
                            k1.NamaKelas AS NamaKelasSantriBaru, k2.NamaKelas AS NamaKelasKelasSantri,
                            t.NamaTpq');
            $builder->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left');
            $builder->join('tbl_kelas k1', 'k1.IdKelas = s.IdKelas', 'left');
            // Join dengan tbl_kelas_santri untuk tahun ajaran saat ini
            $builder->join(
                'tbl_kelas_santri ks',
                'ks.IdSantri = s.IdSantri AND ks.IdTahunAjaran = ' . $db->escape($idTahunAjaran),
                'inner'
            );
            $builder->join('tbl_kelas k2', 'k2.IdKelas = ks.IdKelas', 'left');

            // Hanya ambil santri yang:
            // 1. Active = 1 (aktif)
            // 2. IdKelas di tbl_santri_baru berbeda dengan IdKelas di tbl_kelas_santri
            // 3. Status kelas_santri = 1 (aktif)
            $builder->where('s.Active', 1);
            $builder->where('ks.Status', 1);
            // Gunakan where dengan raw SQL untuk membandingkan kolom
            $builder->where('s.IdKelas != ks.IdKelas', null, false);

            // Filter null values untuk IdKelas
            $builder->where('s.IdKelas IS NOT NULL', null, false);
            $builder->where('ks.IdKelas IS NOT NULL', null, false);

            // Filter berdasarkan IdTpq jika ada
            if ($IdTpq != null && $IdTpq != 0) {
                $builder->where('s.IdTpq', $IdTpq);
            }

            $builder->orderBy('s.IdTpq', 'ASC');
            $builder->orderBy('s.NamaSantri', 'ASC');

            $result = $builder->get()->getResultArray();

            // Hitung rangkuman per TPQ
            $summaryByTpq = [];
            $formattedData = [];

            foreach ($result as $item) {
                $idTpq = $item['IdTpq'];
                $namaTpq = $item['NamaTpq'] ?? 'Tidak ditemukan';

                $formattedItem = [
                    'IdSantri' => $item['IdSantri'],
                    'NamaSantri' => $item['NamaSantri'] ?? 'Tidak ditemukan',
                    'IdTpq' => $idTpq,
                    'NamaTpq' => $namaTpq,
                    'IdKelasSantriBaru' => $item['IdKelasSantriBaru'],
                    'NamaKelasSantriBaru' => $item['NamaKelasSantriBaru'] ?? 'Tidak ditemukan',
                    'IdKelasKelasSantri' => $item['IdKelasKelasSantri'],
                    'NamaKelasKelasSantri' => $item['NamaKelasKelasSantri'] ?? 'Tidak ditemukan',
                    'IdKelasSantri' => $item['IdKelasSantri'], // ID record di tbl_kelas_santri
                    'Active' => $item['Active'],
                    'Status' => $item['Status'] ?? null,
                    'IdTahunAjaran' => $idTahunAjaran,
                    'type' => 'kelas_tidak_sesuai',
                    'reason' => 'IdKelas di tbl_santri_baru (' . ($item['NamaKelasSantriBaru'] ?? 'Tidak ditemukan') .
                        ') tidak sesuai dengan IdKelas di tbl_kelas_santri (' .
                        ($item['NamaKelasKelasSantri'] ?? 'Tidak ditemukan') . ') untuk tahun ajaran ' . $idTahunAjaran
                ];

                $formattedData[] = $formattedItem;

                // Hitung rangkuman per TPQ
                if (!isset($summaryByTpq[$idTpq])) {
                    $summaryByTpq[$idTpq] = [
                        'IdTpq' => $idTpq,
                        'NamaTpq' => $namaTpq,
                        'total' => 0,
                        'santri_terkena' => []
                    ];
                }

                $summaryByTpq[$idTpq]['total']++;

                // Kumpulkan IdSantri yang terkena (unik)
                if (!in_array($item['IdSantri'], $summaryByTpq[$idTpq]['santri_terkena'])) {
                    $summaryByTpq[$idTpq]['santri_terkena'][] = $item['IdSantri'];
                }
            }

            // Hitung jumlah santri yang terkena per TPQ
            foreach ($summaryByTpq as $key => $tpq) {
                $summaryByTpq[$key]['jumlah_santri_terkena'] = count($tpq['santri_terkena']);
                // Hapus array dari output
                unset($summaryByTpq[$key]['santri_terkena']);
            }

            // Convert to array untuk JSON
            $summaryByTpqArray = array_values($summaryByTpq);

            return $this->response->setJSON([
                'success' => true,
                'total_checked' => count($result),
                'total_to_update' => count($result),
                'data' => $formattedData,
                'summary_by_tpq' => $summaryByTpqArray,
                'id_tahun_ajaran' => $idTahunAjaran
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Kelas: checkSantriKelasTidakSesuai - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Mengupdate kelas santri yang tidak sesuai dan update nilai sesuai kelas baru
     * @return json
     */
    public function updateSantriKelasTidakSesuai()
    {
        try {
            $json = $this->request->getJSON();
            $selectedSantri = $json->santri ?? [];

            if (empty($selectedSantri) || !is_array($selectedSantri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada santri yang dipilih untuk diproses'
                ]);
            }

            $db = \Config\Database::connect();
            $idTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
            $updatedCount = 0;
            $errors = [];

            // Proses setiap santri yang dipilih
            foreach ($selectedSantri as $idSantri) {
                try {
                    // Ambil data santri dari tbl_santri_baru
                    $santri = $db->table('tbl_santri_baru')
                        ->where('IdSantri', $idSantri)
                        ->where('Active', 1)
                        ->get()
                        ->getRowArray();

                    if (!$santri) {
                        $errors[] = "Santri dengan ID {$idSantri} tidak ditemukan atau tidak aktif";
                        continue;
                    }

                    // Pastikan IdKelas ada
                    if (empty($santri['IdKelas'])) {
                        $errors[] = "Santri {$santri['NamaSantri']} (ID: {$idSantri}) belum memiliki kelas (IdKelas kosong)";
                        continue;
                    }

                    // Ambil data kelas_santri untuk tahun ajaran ini
                    $kelasSantri = $db->table('tbl_kelas_santri')
                        ->where('IdSantri', $idSantri)
                        ->where('IdTahunAjaran', $idTahunAjaran)
                        ->where('Status', 1)
                        ->get()
                        ->getRowArray();

                    if (!$kelasSantri) {
                        $errors[] = "Santri {$santri['NamaSantri']} (ID: {$idSantri}) tidak ditemukan di tbl_kelas_santri untuk tahun ajaran {$idTahunAjaran}";
                        continue;
                    }

                    // Cek apakah IdKelas sudah sesuai
                    if ($kelasSantri['IdKelas'] == $santri['IdKelas']) {
                        // Sudah sesuai, skip
                        continue;
                    }

                    // Update IdKelas di tbl_kelas_santri
                    $db->table('tbl_kelas_santri')
                        ->where('Id', $kelasSantri['Id'])
                        ->update(['IdKelas' => $santri['IdKelas']]);

                    // Hapus nilai lama yang tidak sesuai dengan kelas baru
                    $db->table('tbl_nilai')
                        ->where('IdSantri', $idSantri)
                        ->where('IdTahunAjaran', $idTahunAjaran)
                        ->where('IdTpq', $santri['IdTpq'])
                        ->where('IdKelas', $kelasSantri['IdKelas']) // Kelas lama
                        ->delete();

                    // Generate nilai baru sesuai kelas baru
                    $listMateriPelajaran = $this->helpFunction->getKelasMateriPelajaran($santri['IdKelas'], $santri['IdTpq']);
                    $dataNilaiBaru = [];

                    foreach ($listMateriPelajaran as $materi) {
                        if ($materi->SemesterGanjil == 1) {
                            // Cek apakah sudah ada nilai untuk semester ganjil
                            $existingNilai = $db->table('tbl_nilai')
                                ->where('IdSantri', $idSantri)
                                ->where('IdTahunAjaran', $idTahunAjaran)
                                ->where('IdTpq', $santri['IdTpq'])
                                ->where('IdKelas', $santri['IdKelas'])
                                ->where('IdMateri', $materi->IdMateri)
                                ->where('Semester', 'Ganjil')
                                ->countAllResults();

                            if ($existingNilai == 0) {
                                $dataNilaiBaru[] = [
                                    'IdTpq' => $santri['IdTpq'],
                                    'IdSantri' => $idSantri,
                                    'IdKelas' => $santri['IdKelas'],
                                    'IdMateri' => $materi->IdMateri,
                                    'IdTahunAjaran' => $idTahunAjaran,
                                    'Semester' => 'Ganjil'
                                ];
                            }
                        }

                        if ($materi->SemesterGenap == 1) {
                            // Cek apakah sudah ada nilai untuk semester genap
                            $existingNilai = $db->table('tbl_nilai')
                                ->where('IdSantri', $idSantri)
                                ->where('IdTahunAjaran', $idTahunAjaran)
                                ->where('IdTpq', $santri['IdTpq'])
                                ->where('IdKelas', $santri['IdKelas'])
                                ->where('IdMateri', $materi->IdMateri)
                                ->where('Semester', 'Genap')
                                ->countAllResults();

                            if ($existingNilai == 0) {
                                $dataNilaiBaru[] = [
                                    'IdTpq' => $santri['IdTpq'],
                                    'IdSantri' => $idSantri,
                                    'IdKelas' => $santri['IdKelas'],
                                    'IdMateri' => $materi->IdMateri,
                                    'IdTahunAjaran' => $idTahunAjaran,
                                    'Semester' => 'Genap'
                                ];
                            }
                        }
                    }

                    // Insert nilai baru jika ada
                    if (!empty($dataNilaiBaru)) {
                        $db->table('tbl_nilai')->insertBatch($dataNilaiBaru);
                    }

                    $updatedCount++;
                    log_message('info', 'Kelas: updateSantriKelasTidakSesuai - Berhasil update santri ' . $idSantri .
                        ' dari kelas ' . $kelasSantri['IdKelas'] . ' ke kelas ' . $santri['IdKelas']);
                } catch (\Exception $e) {
                    $errors[] = "Error memproses santri ID {$idSantri}: " . $e->getMessage();
                    log_message('error', 'Kelas: updateSantriKelasTidakSesuai - Error untuk santri ' . $idSantri . ': ' . $e->getMessage());
                }
            }

            $message = "Update selesai. Santri yang diupdate: {$updatedCount} dari " . count($selectedSantri) . " yang dipilih.";

            if (!empty($errors)) {
                $message .= " Terjadi beberapa error: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " dan " . (count($errors) - 5) . " error lainnya.";
                }
            }

            log_message('info', 'Kelas: updateSantriKelasTidakSesuai - ' . $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'data' => [
                    'total_selected' => count($selectedSantri),
                    'updated_count' => $updatedCount,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Kelas: updateSantriKelasTidakSesuai - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal melakukan update: ' . $e->getMessage()
            ]);
        }
    }
}
