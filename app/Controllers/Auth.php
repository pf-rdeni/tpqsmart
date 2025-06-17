<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\HelpFunctionModel;
use App\Models\SantriModel;
use App\Models\TabunganModel;

class Auth extends BaseController
{
    protected $helpFunctionModel;
    protected $santriModel;
    protected $tabunganModel;
    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->santriModel = new SantriModel();
        $this->tabunganModel = new TabunganModel();
    }

    private function getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $kelasList, $semester)
    {
        // Ekstrak ID kelas dari array/object
        $kelasIds = array_map(function ($kelas) {
            return is_object($kelas) ? $kelas->IdKelas : $kelas;
        }, $kelasList);

        // Ambil semua data dalam satu query
        $statusNilai = $this->helpFunctionModel->getStatusInputNilaiBulk(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            IdKelas: $kelasIds,
            Semester: $semester
        );

        // Ambil semua nama kelas dalam satu query
        $namaKelas = $this->helpFunctionModel->getNamaKelasBulk($kelasIds);

        // Gabungkan data
        $result = [];
        foreach ($kelasIds as $idKelas) {
            if (isset($statusNilai[$idKelas])) {
                $result[] = [
                    'IdKelas' => $idKelas,
                    'NamaKelas' => $namaKelas[$idKelas] ?? '',
                    'StatusInputNilai' => $statusNilai[$idKelas] ?? false
                ];
            }
        }
        return $result;
    }

    private function getGuruDashboardData($idTpq, $idTahunAjaran, $idKelas, $idGuru)
    {
        $saldoTabungan = $this->tabunganModel->getSaldoTabunganSantri(
            $idTpq,
            $idTahunAjaran,
            $idKelas,
            $idGuru
        );

        $totalSantri = $this->santriModel->getTotalSantri(
            $idTpq,
            $idTahunAjaran,
            $idKelas,
            $idGuru
        );

        $JumlahKelasDiajar = empty($idKelas) ? 0 : count($idKelas);

        // Ambil jumlah santri per kelas
        $jumlahSantriPerKelas = $this->helpFunctionModel->getJumlahSantriPerKelas(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            kelasIds: $idKelas
        );

        $statusInputNilaiPerKelasGanjil = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $idKelas, 'Ganjil');
        $statusInputNilaiPerKelasGenap = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $idKelas, 'Genap');

        return [
            'page_title' => 'Dashboard',
            'JumlahKelasDiajar' => $JumlahKelasDiajar,
            'TotalSantri' => $totalSantri,
            'TotalTabungan' => $saldoTabungan ?? 0,
            'TahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran),
            'StatusInputNilaiSemesterGanjil' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                IdKelas: $idKelas,
                Semester: 'Ganjil'
            ),
            'StatusInputNilaiSemesterGenap' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                IdKelas: $idKelas,
                Semester: 'Genap'
            ),
            'StatusInputNilaiPerKelasGanjil' => $statusInputNilaiPerKelasGanjil,
            'StatusInputNilaiPerKelasGenap' => $statusInputNilaiPerKelasGenap,
            'JumlahSantriPerKelas' => $jumlahSantriPerKelas,
        ];
    }

    private function getAdminDashboardData($idTpq, $idTahunAjaran)
    {
        $listKelas = $this->helpFunctionModel->getListKelas(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
        );

        // Ambil jumlah santri per kelas
        $jumlahSantriPerKelas = $this->helpFunctionModel->getJumlahSantriPerKelas(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            kelasIds: array_map(function ($kelas) {
                return is_object($kelas) ? $kelas->IdKelas : $kelas;
            }, $listKelas)
        );

        $statusInputNilaiPerKelasGanjil = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $listKelas, 'Ganjil');
        $statusInputNilaiPerKelasGenap = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $listKelas, 'Genap');

        return [
            'page_title' => 'Dashboard',
            'TotalWaliKelas' => $this->helpFunctionModel->getTotalWaliKelas(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
            ),
            'TotalSantri' => $this->helpFunctionModel->getTotalSantri(IdTpq: $idTpq),
            'TotalGuru' => $this->helpFunctionModel->getTotalGuru(IdTpq: $idTpq),
            'TotalKelas' => $this->helpFunctionModel->getTotalKelas(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
            ),
            'TotalSantriBaru' => $this->helpFunctionModel->getTotalSantriBaru(
                IdTpq: $idTpq,
                IdKelas: session()->get('IdKelas'),
            ),
            'TahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran),
            'StatusInputNilaiSemesterGanjil' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                Semester: 'Ganjil'
            ),
            'StatusInputNilaiSemesterGenap' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                Semester: 'Genap'
            ),
            'StatusInputNilaiPerKelasGanjil' => $statusInputNilaiPerKelasGanjil,
            'StatusInputNilaiPerKelasGenap' => $statusInputNilaiPerKelasGenap,
            'JumlahSantriPerKelas' => $jumlahSantriPerKelas,
        ];
    }



    public function index()
    {
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $idKelas = session()->get('IdKelas');
        $idGuru = session()->get('IdGuru');

        if (in_groups('Guru')) {
            $data = $this->getGuruDashboardData($idTpq, $idTahunAjaran, $idKelas, $idGuru);
        } else if (in_groups('Admin') || in_groups('Operator')) {
            $idTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();
            $data = $this->getAdminDashboardData($idTpq, $idTahunAjaran);
        } else {
            $data = ['page_title' => 'Dashboard'];
        }

        return view('backend/dashboard/index', $data);
    }
    public function logout()
    {
        // Hapus session
        session()->destroy();

        // Redirect ke halaman login
        return redirect()->to(base_url('login'));
    }
}
