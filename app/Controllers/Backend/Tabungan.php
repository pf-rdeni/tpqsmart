<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TabunganModel;
use App\Models\EncryptModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriModel;

class Tabungan extends BaseController
{
    protected $dataSantri;
    protected $encryptModel;
    protected $helpFunction;
    protected $tabunganModel;

    public function __construct()
    {
        $this->encryptModel = new EncryptModel();
        $this->dataSantri = new SantriModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->tabunganModel = new TabunganModel();
    }

    public function create($page = null)
    {
        if ($this->request->getMethod() != 'POST') {
            return redirect()->to('backend/tabungan/showPerKelas/');
        }

        $data = $this->getPostData();

        // Get santri details
        $santri = $this->dataSantri->where('IdSantri', $data['IdSantri'])->first();
        $namaSantri = $santri ? $santri['NamaSantri'] : 'Santri Tidak Ditemukan';

        // Check if nominal is valid
        if ($data['Nominal'] <= 0) {
            $this->setFlashData('danger', 'Gagal disimpan: Nominal untuk Santri: <strong>' . $namaSantri . '</strong> harus lebih dari 0.');
            return redirect()->back()->withInput();
        }

        $this->tabunganModel->insert($data);
        $this->setFlashData('success', 'Uang sebesar <strong>Rp. ' . number_format($data['Nominal'], 0, ',', '.') . '</strong> untuk Santri: <strong>' . $namaSantri . '</strong> berhasil disimpan!');
        if (!$page) {
            return redirect()->to('backend/tabungan/showPerKelas/');
        }
        else {
            return redirect()->to('backend/tabungan/showMutasi/'. $data['IdSantri'].'/'. $data['IdTahunAjaran']);
        }
    }

    private function getPostData()
    {
        return [
            'page_title' => 'Transaksi Tabungan',
            'JenisTransaksi' => $this->request->getPost('JenisTransaksi'),
            'Nominal' => $this->helpFunction->convertToNumber($this->request->getPost('Nominal')),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdSantri' => $this->request->getPost('IdSantri'),
            'IdKelas' => $this->request->getPost('IdKelas'),
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdGuru' => $this->request->getPost('IdGuru'),
            'Keterangan' => $this->request->getPost('Keterangan')
        ];
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


    public function showPerKelas($encryptedIdGuru = null)
    {
        $IdTpq = session()->get('IdTpq');
        
        if($encryptedIdGuru !== null)
            $IdGuru = $this->encryptModel->decryptData($encryptedIdGuru);
        else 
            $IdGuru = $encryptedIdGuru;

        $IdGuru = session()->get('IdGuru');  
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Ambil semua data santri (tanpa filter kelas spesifik untuk mendapatkan semua kelas)
        $dataSantri = $this->tabunganModel->getSantriWithBalance($IdTpq, $IdTahunAjaran, null, $IdGuru);

        // Kelompokkan data per kelas
        $dataSantriPerKelas = [];
        $totalSaldoKeseluruhan = 0;

        if (!empty($dataSantri)) {
            foreach ($dataSantri as $santri) {
                $idKelas = $santri->IdKelas ?? 'unknown';
                $namaKelas = $santri->NamaKelas ?? 'Tidak Diketahui';

                if (!isset($dataSantriPerKelas[$idKelas])) {
                    $dataSantriPerKelas[$idKelas] = [
                        'IdKelas' => $idKelas,
                        'NamaKelas' => $namaKelas,
                        'santri' => [],
                        'totalSaldo' => 0
                    ];
                }

                $dataSantriPerKelas[$idKelas]['santri'][] = $santri;
                $balance = $santri->Balance ?? 0;
                $dataSantriPerKelas[$idKelas]['totalSaldo'] += $balance;
                $totalSaldoKeseluruhan += $balance;
            }

            // Urutkan berdasarkan nama kelas
            uasort($dataSantriPerKelas, function ($a, $b) {
                return strcmp($a['NamaKelas'], $b['NamaKelas']);
            });
        }

        $data = [
            'page_title' => 'Tabungan Santri',
            'dataSantriPerKelas' => $dataSantriPerKelas,
            'totalSaldoKeseluruhan' => $totalSaldoKeseluruhan
        ];

        return view('backend/tabungan/tabunganPerKelas', $data);
    }

    public function showDetail($IdSantri, $IdTahunAjaran)
    {
        // Retrieve the monthly contribution data for a specific student and academic year
        $dataIuran = $this->tabunganModel->getIuranBulanan($IdSantri, $IdTahunAjaran);

        foreach ($dataIuran as $Iuran) {
            $Iuran->Nominal = 'Rp. ' . number_format($Iuran->Nominal, 0, ',', '.');
        }
        
        foreach ($dataIuran as $Iuran) {
            $Iuran->Bulan = $this->helpFunction->numberToMonth($Iuran->Bulan);
        }
        

        $data = [
            'page_title' => 'Data Iuran Santri',
            'dataIuran' => $dataIuran,
        ];

        return view('backend/tabungan/tabunganSantriDetail', $data);
    }

    public function getMutasiSantri($IdSantri, $IdTahunAjaran)
    {
        // Initialize variables
        $saldo = 0;
        $query = $this->tabunganModel->table('tbl_tabungan_santri')
            ->select('
                          tbl_tabungan_santri.*,  
                          tbl_santri_baru.NamaSantri, tbl_kelas.NamaKelas')
            ->join('tbl_santri_baru', 'tbl_santri_baru.IdSantri = tbl_tabungan_santri.IdSantri')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
                          ->where('tbl_tabungan_santri.IdSantri', $IdSantri)
                          ->where('tbl_tabungan_santri.IdTahunAjaran', $IdTahunAjaran)
                          ->orderBy('tbl_tabungan_santri.UpdatedAt', 'ASC')
                          ->get();

        $mutasi = $query->getResultArray();


        // Array to hold the result with calculated saldo
        $mutasiWithSaldo = [];

        // Loop through each transaction and calculate the saldo (balance)
        foreach ($mutasi as $transaksi) {
            // If it's a Setoran (credit), add to saldo
            if ($transaksi['JenisTransaksi'] === 'Setoran') {
                $saldo += $transaksi['Nominal'];
            } 
            // If it's a Penarikan (debit), subtract from saldo
            else if ($transaksi['JenisTransaksi'] === 'Penarikan') {
                $saldo -= $transaksi['Nominal'];
            }

            // Add the saldo to the transaction record
            $transaksi['Saldo'] = $saldo;

            // Push to result array
            $mutasiWithSaldo[] = $transaksi;
        }

        // Reverse the array to make the last record the first one
        $mutasiWithSaldo = array_reverse($mutasiWithSaldo);

        return $mutasiWithSaldo;
    }

    // Create Method mutasi transactions for a specific Santri (IdSantri) and academic year (IdTahunAjaran), add new filed saldo from calculation debit and credit transactions for each transaction

    public function showMutasi($IdSantri, $IdTahunAjaran = null)
    {
        // Fetch student details
        $santriModel = new \App\Models\SantriModel(); // Assuming SantriModel is in \App\Models namespace
        // Fetch student details
        $santri = $santriModel->where('IdSantri', $IdSantri)->first();

        // Fetch transaction history (mutasi) for the student
        $mutasi = $this->getMutasiSantri($IdSantri, $IdTahunAjaran);

        $mutasi = array_map(function($item) {
            return json_decode(json_encode($item));
        }, $mutasi);

        // filter dataTabungan by IdTahunAjaran if IdTahunAjaran is not null
        if ($IdTahunAjaran !== null) {
            $mutasi = array_filter($mutasi, function($item) use ($IdTahunAjaran) {
                return $item->IdTahunAjaran == $IdTahunAjaran;
            });
        }

        $data = [
            'page_title' => 'Mutasi Tabungan Santri',
            'dataTabungan' => $mutasi,
            'santri' => $santri
        ];

     
        return view('backend/tabungan/tabunganSantriMutasi', $data);
    }

    /**
     * Menampilkan detail tabungan untuk user Santri
     */
    public function showTabunganSantri()
    {
        // Cek apakah user adalah Santri
        if (!in_groups('Santri')) {
            return redirect()->to(base_url())->with('error', 'Akses ditolak');
        }

        // Ambil NIK dari user yang login
        $userNik = user()->nik ?? null;
        if (empty($userNik)) {
            return redirect()->to(base_url())->with('error', 'Data user tidak valid');
        }

        // Ambil data santri berdasarkan NIK
        $santriModel = new \App\Models\SantriBaruModel();
        $santriData = $santriModel->getSantriByNik($userNik);

        if (empty($santriData)) {
            return redirect()->to(base_url())->with('error', 'Data santri tidak ditemukan');
        }

        $IdSantri = $santriData['IdSantri'];
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Ambil mutasi tabungan
        $mutasi = $this->getMutasiSantri($IdSantri, $IdTahunAjaran);
        $mutasi = array_map(function ($item) {
            return json_decode(json_encode($item));
        }, $mutasi);

        // Hitung saldo
        $saldo = $this->tabunganModel->calculateBalance($IdSantri);

        // Ambil transaksi terbaru
        $transaksiTerbaru = $this->tabunganModel->where('IdSantri', $IdSantri)
            ->orderBy('TanggalTransaksi', 'DESC')
            ->orderBy('CreatedAt', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'page_title' => 'Detail Tabungan',
            'dataTabungan' => $mutasi,
            'santri' => $santriData,
            'saldo' => $saldo,
            'transaksiTerbaru' => $transaksiTerbaru,
            'IdTahunAjaran' => $IdTahunAjaran,
        ];

        return view('backend/tabungan/tabunganSantriDetail', $data);
    }
}
