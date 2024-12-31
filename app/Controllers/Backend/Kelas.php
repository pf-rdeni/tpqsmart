<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\KelasModel;
use App\Models\SantriBaruModel;
use App\Models\KelasMateriPelajaranModel;
use App\Models\NilaiModel;

class Kelas extends BaseController
{
    protected $santriBaruModel;
    protected $kelasModel;
    protected $kelasMateriPelajaranModel;
    protected $nilaiModel;
    protected $helpFunction;
    protected $IdTpq;

    public function __construct()
    {
        // Initialize models
        $this->santriBaruModel = new SantriBaruModel();
        $this->kelasModel = new KelasModel();
        $this->kelasMateriPelajaranModel = new KelasMateriPelajaranModel();
        $this->nilaiModel = new NilaiModel();
        $this->helpFunction = new HelpFunctionModel();

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

        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $dataSantri,
            'dataKelas' => $dataKelas
        ];

        return view('backend/kelas/kelasBaru', $data);
    }


    // Metode untuk menyimpan data daan mengupdate di tabel 
    // tbl_kelas_santri : menampatkan registrasi di kelas di himpun berdasarkan kelas dan tahun ajaran set aktif
    // menampilkan kembali di view/backend/kelas/kelasBaru
    public function setKelasSantriBaru()
    {
        // Step 1 definisi set Tahun Ajaran saat ini
        $currentYear = date('Y');
        $currentMonth = date('n');
        $idTahunAjaran = ($currentMonth >= 7) ? $currentYear . ($currentYear + 1) : ($currentYear - 1) . $currentYear;

        // Step 2 ambil data yang dikirim dari proses POST masukan santri baru ke tabel tbl_kelas_santri 
        // Data ini diambildari data satri yang sudah registarasi tapi belum dimasukan ke kelas
        $idKelasArray = $this->request->getVar('IdKelas');
        $idTpqArray = $this->request->getVar('IdTpq');
        $dataSantriBaru = [];
        foreach ($idKelasArray as $idSantri => $idKelas) {
            // Memastikan bahwa IdTpq untuk IdSantri yang sama juga tersedia
            if (isset($idTpqArray[$idSantri])) {
                $dataSantriBaru[] = [
                    'IdSantri' => $idSantri,
                    'IdKelas' => $idKelas,
                    'IdTpq' => $idTpqArray[$idSantri],
                    'IdTahunAjaran' => $idTahunAjaran
                ];
            }
        }

        // Step 3 ambil individual santri dari POST IdKelas
        $this->saveDataSantriDanMateriDiTabelNilai(0, santriList: $dataSantriBaru);
        
        //Check kembali jika masih ada dan tampilkan
        $dataKelas = $this->helpFunction->getDataKelas();

        $dataStatusSantriBaru = $this->helpFunction->getDataSantriStatus(IdTpq: $this->IdTpq);
        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $dataStatusSantriBaru,
            'dataKelas' => $dataKelas
        ];

        return view('backend/kelas/kelasBaru', $data);
    }


    // Menampilkan list kelas dan jumlah santri berdasarkan kelas dan tahun ajaran
    // Digunakan untuk melihat data kelas yang mau di naikan ke kelas berikutnya
    // page : view/backend/kelas/naikKelas
    public function showListSantriPerKelas($idTahunAjaran = null)
    {
        $currentYear = date('Y');
        $currentMonth = date('n');

        // Ambil data berdasarkan tahun ajaran sebelumnya dan data tahun ajaran saat ini
        $previousAcademicYear = ($currentMonth >= 7) ? ($currentYear - 1) . $currentYear : ($currentYear - 2) . ($currentYear - 1);
        $currentAcademicYear = ($currentMonth >= 7) ? $currentYear . ($currentYear + 1) : ($currentYear - 1). ($currentYear + 1);

        // mengambil data query berdasarkan filter tahun ajaran tabel tbl_kelas_santri
        $this->kelasModel->select('tbl_kelas_santri.IdTahunAjaran, tbl_kelas_santri.IdKelas, tbl_kelas.NamaKelas, COUNT(tbl_kelas_santri.IdSantri) AS SumIdKelas')
                        ->join('tbl_kelas', 'tbl_kelas_santri.IdKelas = tbl_kelas.IdKelas')
                        ->groupBy('tbl_kelas_santri.IdTahunAjaran, tbl_kelas_santri.IdKelas')
                        ->orderBy('tbl_kelas_santri.IdTahunAjaran', 'ASC')
                        ->orderBy('tbl_kelas_santri.IdKelas', 'ASC')
                        ->where('tbl_kelas_santri.status', true)
                        ->whereIn('tbl_kelas_santri.IdTahunAjaran', [$previousAcademicYear, $currentAcademicYear]);

        $dataKelas = $this->kelasModel->get()->getResultArray();

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
        //Step 1 get tahun berikunya dari idTahun Sebelumnya/saat ini
        $newTahunAjaran = $this->helpFunction->getTahuanAjaranBerikutnya($idTahunAjaran);

        //Step 2 ambil list santri tbl_kelas_santri
        //tabel ini informasi penyimmpanan santri berdasarkan tahun ajaran, kelas, tpq dan status active = 1
        $santriList = $this->kelasModel->where('IdTahunAjaran', $idTahunAjaran)
                            ->where('IdKelas', $idKelas)
                            ->where('Status', 1)
                            ->findAll();

        //Step 3 Aambil santri dari list tersebut untuk di rubah kelas sebelumnya set status = Tidak Aktif = 0
        //       Kelas Berikutnya set Status = Aktif = 1 
        foreach ($santriList as $santri) {
            
            // 3.1 Konversi kelas sebelumnya ke kelas berikutnya
            $idKelasLama = $santri['IdKelas'];
            $idKelasBaru = $this->helpFunction->getNextKelas($idKelasLama);
            $idTpq = $santri['IdTpq'];
            $idSantri = $santri['IdSantri'];
            
            // 3.2 Insert Ulang Santri kelas sebelumnya untuk di naikan kelas Satatus default aktif = 1
            $this->kelasModel->insert([
                'IdKelas' => $idKelasBaru,
                'IdTpq' => $idTpq,
                'IdSantri' => $idSantri,
                'IdTahunAjaran' => $newTahunAjaran
            ]);
            // 3.3 Update Santri kelas sebelumnya sudah dinak status tidak aktif = 0
            $this->kelasModel->update($santri['Id'], ['Status' => 0]);

            // 3.4 Ambil Materi Pelajaran berdasarkan Kelas dan TPQ
            $listMateriPelajaran = $this->helpFunction->getKelasMateriPelajaran($idKelasBaru, $idTpq);

            // 3.5 Insert Into Tabel tbl_nilai
            foreach ($listMateriPelajaran['materi'] as $materiPelajaran) {
                $data = [
                    'IdTpq' => $idTpq,
                    'IdSantri' => $idSantri,
                    'IdTahunAjaran' => $newTahunAjaran,
                    'IdKelas' => $materiPelajaran['IdKelas'],
                    'IdMateri' => $materiPelajaran['IdMateri'],
                    'Semester' => $materiPelajaran['Semester']
                ];
                $this->nilaiModel->insertNilai($data);
            }
        }

        return redirect()->to('/kelas/showListSantriPerKelas/' . $idTahunAjaran);
    }

    private function saveDataSantriDanMateriDiTabelNilai($StatusSantri, $santriList)
    {
        if($StatusSantri == 0) // Santri Baru
        {
            //Get Tahun Ajaran Saat Ini
            $SantriBaru = true;
        }
        else{ // Naik Kelas
            //Get tahun ajaran berikutnya
            //Step 1 get tahun berikunya dari idTahun Sebelumnya/saat ini
            $idTahunAjaran = $this->helpFunction->getTahuanAjaranBerikutnya($idTahunAjaran = 0); //perlu disesuaikan
            $SantriBaru = false;
        }
        //Step 1 Aambil santri dari list tersebut 
        //       Kelas Berikutnya set Status = Aktif = 1 
        foreach ($santriList as $santri) {
            $idSantri = $santri['IdSantri'];
            $idTpq = $santri['IdTpq'];
            $idKelas = $santri['IdKelas'];
            $idTahunAjaran = $santri['IdTahunAjaran'];
            
            if($SantriBaru)
            {   
                // 1.2.1 Simpan di Tabel tbl_kelas_santri
                $dataSantriBaru = [
                    'IdSantri' => $idSantri,
                    'IdKelas' => $idKelas,
                    'IdTpq' => $idTpq,
                    'IdTahunAjaran' => $idTahunAjaran
                ];
                // 1.2.2 Insert Santri kelas
                $this->store($dataSantriBaru);
                // 1.2.3 Update Active Santri         
                $this->santriBaruModel->updateActiveSantri($idSantri);
            }
            else{
                $idKelas = $this->helpFunction->getNextKelas($idKelas);
                // 1.2.1 Insert Ulang Santri kelas sebelumnya untuk di naikan kelas Satatus default aktif = 1
                $this->kelasModel->insert([
                    'IdKelas' => $idKelas,
                    'IdTpq' => $idTpq,
                    'IdSantri' => $idSantri,
                    'IdTahunAjaran' => $idTahunAjaran
                ]);
                // 1.2.2 Update Santri kelas sebelumnya sudah dinak status tidak aktif = 0
                $this->kelasModel->update($santri['Id'], ['Status' => 0]);

            }

            // 1.3 Ambil Materi Pelajaran berdasarkan Kelas dan TPQ
            $listMateriPelajaran = $this->helpFunction->getKelasMateriPelajaran($idKelas, $idTpq);
            // 1.4 Insert Into Tabel tbl_nilai
            foreach ($listMateriPelajaran as $materiPelajaran) {
                $data = [
                    'IdTpq' => $idTpq,
                    'IdSantri' => $idSantri,
                    'IdKelas' => $materiPelajaran['IdKelas'],
                    'IdMateri' => $materiPelajaran['IdMateri'],
                    'IdTahunAjaran' => $idTahunAjaran,
                ];
                $this->nilaiModel->insertNilai($data);
            }
        }

    }
}
