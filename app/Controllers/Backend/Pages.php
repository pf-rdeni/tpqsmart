<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TabunganModel;
use App\Models\SantriModel;
use App\Models\HelpFunctionModel;

class Pages extends BaseController
{
    protected $tabunganModel;
    protected $santriModel;
    protected $helpFunctionModel;

    public function __construct()
    {
        $this->tabunganModel = new TabunganModel();
        $this->santriModel = new SantriModel();
        $this->helpFunctionModel = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $idKelas = session()->get('IdKelas');
        $idGuru = session()->get('IdGuru');
        if (in_groups('Guru')) {

            // Mendapatkan saldo tabungan santri
            $saldoTabungan = $this->tabunganModel->getSaldoTabunganSantri(
                $idTpq,
                $idTahunAjaran,
                $idKelas,
                $idGuru
            );

            // Mengambil total santri dari model santri GetTotalSantri
            $totalSantri = $this->santriModel->getTotalSantri(
                $idTpq,
                $idTahunAjaran,
                $idKelas,
                $idGuru
            );

            //Jumloh kelas yang diajar count dari session IdKelas
            // Jika IdKelas tidak ada di session, set ke 0
            $idKelas = session()->get('IdKelas') ?? 0;
            if ($idKelas == 0) {
                $JumlahKelasDiajar = 0;
            } else {
                $JumlahKelasDiajar = count($idKelas);
            }

            // mendapakan status input nilai semester ganjil untuk kelas
            $statusInputNilaiSemesterGanjil = $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                IdKelas: $idKelas,
                Semester: 'Ganjil'
            );
            // mendapakan status input nilai semester genap untuk kelas
            $statusInputNilaiSemesterGenap = $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                IdKelas: $idKelas,
                Semester: 'Genap'
            );

            // loop untuk mendapatkan status input nilai per kelas ganjil dan genap filter idTahunAjaran idKelas dan semester
            $statusInputNilaiPerKelasGanjil = [];
            $statusInputNilaiPerKelasGenap = [];
            foreach ($idKelas as $kelas) {
                $statusInputNilaiPerKelasGanjil[] = [
                    'IdKelas' => $kelas,
                    'NamaKelas' => $this->helpFunctionModel->getNamaKelas($kelas),
                    'StatusInputNilai' => $this->helpFunctionModel->getStatusInputNilai(
                        IdTpq: $idTpq,
                        IdTahunAjaran: $idTahunAjaran,
                        IdKelas: $kelas,
                        Semester: 'Ganjil'
                    )
                ];
                $statusInputNilaiPerKelasGenap[] = [
                    'IdKelas' => $kelas,
                    'NamaKelas' => $this->helpFunctionModel->getNamaKelas($kelas),
                    'StatusInputNilai' => $this->helpFunctionModel->getStatusInputNilai(
                        IdTpq: $idTpq,
                        IdTahunAjaran: $idTahunAjaran,
                        IdKelas: $kelas,
                        Semester: 'Genap'
                    )
                ];
            }

            $data = [
                'page_title' => 'Dashboard',
                'JumlahKelasDiajar' => $JumlahKelasDiajar,
                'TotalSantri' => $totalSantri, // Akan diisi dengan data dari model
                'TotalTabungan' => $saldoTabungan ?? 0, // Akan diisi dengan data dari model
                'TahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran),
                'StatusInputNilaiSemesterGanjil' => $statusInputNilaiSemesterGanjil,
                'StatusInputNilaiSemesterGenap' => $statusInputNilaiSemesterGenap,
                'StatusInputNilaiPerKelasGanjil' => $statusInputNilaiPerKelasGanjil,
                'StatusInputNilaiPerKelasGenap' => $statusInputNilaiPerKelasGenap,
            ];
        } else if (in_groups('Admin') || in_groups('Operator')) {
            // ambil tahun ajaran saat ini dari fungsi help function
            $idTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();
            // Mendapatkan total santri
            $totalSantri = $this->helpFunctionModel->getTotalSantri(
                IdTpq: $idTpq,
            );

            // Mendapatkan total guru
            $totalGuru = $this->helpFunctionModel->getTotalGuru(
                IdTpq: $idTpq
            );

            // Mendapatkan total kelas
            $totalKelas = $this->helpFunctionModel->getTotalKelas(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
            );
            // Mendapatkan total santri baru active =0
            $totalSantriBaru = $this->helpFunctionModel->getTotalSantriBaru(
                IdTpq: $idTpq,
                IdKelas: $idKelas,
            );

            // Mendapatkan jumlah nilai yang sudah diinput dan belum diinput
            $statusInputNilaiSemesterGanjil = $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                Semester: 'Ganjil'
            );

            //untuk status input nilai semester genap
            $statusInputNilaiSemesterGenap = $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                Semester: 'Genap'
            );

            // Loop untuk mendapatkan status input nilai per kelas Genap dan Ganjil filter tahun ajaran saat ini
            $statusInputNilaiPerKelasGanjil = [];
            $statusInputNilaiPerKelasGenap = [];
            for ($i = 1; $i <= 9; $i++) {
                $statusInputNilaiPerKelasGanjil[] = [
                    'IdKelas' => $i, // GetNamaKelas dari $i ke tbl_kelas
                    'NamaKelas' => $this->helpFunctionModel->getNamaKelas($i),
                    'StatusInputNilai' => $this->helpFunctionModel->getStatusInputNilai(
                        IdTpq: $idTpq,
                        IdTahunAjaran: $idTahunAjaran,
                        IdKelas: $i,
                        Semester: 'Ganjil'
                    )
                ];
                $statusInputNilaiPerKelasGenap[] = [
                    'IdKelas' => $i,
                    'NamaKelas' => $this->helpFunctionModel->getNamaKelas($i),
                    'StatusInputNilai' => $this->helpFunctionModel->getStatusInputNilai(
                        IdTpq: $idTpq,
                        IdTahunAjaran: $idTahunAjaran,
                        IdKelas: $i,
                        Semester: 'Genap'
                    )
                ];
            }

            // Mengambil data wali kelas
            $totalWaliKelas = $this->helpFunctionModel->getTotalWaliKelas(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
            );

            $data = [
                'page_title' => 'Dashboard',
                'TotalWaliKelas' => $totalWaliKelas,
                'TotalSantri' => $totalSantri,
                'TotalGuru' => $totalGuru,
                'TotalKelas' => $totalKelas,
                'TotalSantriBaru' => $totalSantriBaru,
                'TahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran),
                'StatusInputNilaiSemesterGanjil' => $statusInputNilaiSemesterGanjil,
                'StatusInputNilaiSemesterGenap' => $statusInputNilaiSemesterGenap,
                'StatusInputNilaiPerKelasGanjil' => $statusInputNilaiPerKelasGanjil,
                'StatusInputNilaiPerKelasGenap' => $statusInputNilaiPerKelasGenap,
            ];
        } else {
            $data = [
                'page_title' => 'Dashboard',
            ];
        }
        return view('backend/dashboard/index', $data);
    }

    public function about()
    {
        $data = ['pages_title' => 'About Me | MyPrograming'];
        return view('backend/dashboard/about', $data);
    }
    public function contact()
    {

        $data = [
            'page_title' => 'About Me | My Programing',
            'alamat' => [
                [
                    'tipe' => 'Admin Website',
                    'alamat' => 'Deni Rusandi',
                    'kota' => 'Phone: 081364290165 | Email: admin.app@simpedis.com'
                ],
                [
                    'tipe' => 'Sekretariat',
                    'alamat' => 'Masjid Al-Hikmah Kec. Seri Kuala Lobam',
                    'kota' => 'Kabupaten Bintan'
                ]
            ],
        ];
        return view('backend/dashboard/contact', $data);
    }

    public function login()
    {
        $data = ['page_title' => 'Login'];
        return view('backend/login/login', $data);
    }
}
