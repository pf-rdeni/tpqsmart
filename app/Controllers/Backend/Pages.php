<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TabunganModel;
use App\Models\SantriModel;

class Pages extends BaseController
{
    protected $tabunganModel;
    protected $santriModel;

    public function __construct()
    {
        $this->tabunganModel = new TabunganModel();
        $this->santriModel = new SantriModel();
    }

    public function index()
    {
        // Mendapatkan saldo tabungan santri
        $saldoTabungan = $this->tabunganModel->getSaldoTabunganSantri(
            session()->get('IdTpq'),
            session()->get('IdTahunAjaran'),
            session()->get('IdKelas'),
            session()->get('IdGuru')
        );

        // Mengambil total santri dari model santri GetTotalSantri
        $totalSantri = $this->santriModel->getTotalSantri(
            session()->get('IdTpq'),
            session()->get('IdTahunAjaran'),
            session()->get('IdKelas'),
            session()->get('IdGuru')
        );

        //Jumloh kelas yang diajar count dari session IdKelas
        $JumlahKelasDiajar = count(session()->get('IdKelas'));

        $data = [
            'page_title' => 'Dashboard',
            'JumlahKelasDiajar' => $JumlahKelasDiajar,
            'TotalSantri' => $totalSantri, // Akan diisi dengan data dari model
            'TotalTabungan' => $saldoTabungan ?? 0 // Akan diisi dengan data dari model
        ];
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
