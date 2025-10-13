<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PrestasiModel;
use App\Models\EncryptModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriModel;

class Prestasi extends BaseController
{
    protected $prestasiModel;
    protected $dataSantri;
    protected $encryptModel;
    protected $helpFunction;

    public function __construct()
    {
        // Initialize models
        $this->prestasiModel = new PrestasiModel();
        $this->encryptModel = new EncryptModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->dataSantri = new SantriModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Daftar Prestasi Santri',
            'prestasiSantri' => $this->prestasiModel->findAll()
        ];

        return view('backend/prestasi/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Prestasi Santri'
        ];

        return view('backend/prestasi/create', $data);
    }

    public function store()
    {
        $jenisPrestasi = $this->request->getPost('JenisPrestasi');
        $idMateriPelajaran = $this->request->getPost('IdMateriPelajaran');
        $status = $this->request->getPost('Status');
        $keterangan = $this->request->getPost('Keterangan');

        // Jika JenisPrestasi adalah array (multiple)
        if (is_array($jenisPrestasi)) {
            foreach ($jenisPrestasi as $key => $jenis) {
                $this->prestasiModel->save([
                    'IdSantri' => $this->request->getPost('IdSantri'),
                    'IdTpq' => $this->request->getPost('IdTpq'),
                    'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
                    'IdKelas' => $this->request->getPost('IdKelas'),
                    'IdGuru' => $this->request->getPost('IdGuru'),
                    'IdMateriPelajaran' => $idMateriPelajaran[$key],
                    'JenisPrestasi' => $jenis,
                    'Tingkatan' => $this->request->getPost('Tingkatan'),
                    'Status' => $status[$idMateriPelajaran[$key]],
                    'Tanggal' => date('Y-m-d'),
                    'Keterangan' => $keterangan[$idMateriPelajaran[$key]]
                ]);
            }
        } else {
            // Jika JenisPrestasi single dan ada nilainya maka disimpan jika
            // tidak ada nilainya maka tidak disimpan
            if ($jenisPrestasi == '') {
                $this->setFlashData('info', 'Prestasi santri tidak ditambahkan. Silahkan pilih jenis prestasi minimal satu.');
                return redirect()->back();
            }
            $this->prestasiModel->save([
                'IdSantri' => $this->request->getPost('IdSantri'),
                'IdTpq' => $this->request->getPost('IdTpq'),
                'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
                'IdKelas' => $this->request->getPost('IdKelas'),
                'IdGuru' => $this->request->getPost('IdGuru'),
                'IdMateriPelajaran' => $this->request->getPost('IdMateriPelajaran'),
                'JenisPrestasi' => $jenisPrestasi,
                'Tingkatan' => $this->request->getPost('Tingkatan'),
                'Status' => $status,
                'Tanggal' => date('Y-m-d'),
                'Keterangan' => $keterangan
            ]);
        }

        $this->setFlashData('success', 'Prestasi santri berhasil ditambahkan.');
        return redirect()->back();
    }

    public function showPerKelas($encryptedIdGuru = null)
    {
        if ($encryptedIdGuru !== null) {
            $IdGuru = $this->encryptModel->decryptData($encryptedIdGuru);
        } else {
            $IdGuru = $encryptedIdGuru;
        }

        $IdGuru = session()->get('IdGuru');  
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdTpq = session()->get('IdTpq');

        try {
            // Gunakan method yang dioptimasi dengan window function
            $dataSantri = $this->prestasiModel->getSantriWithPrestasiOptimized($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
        } catch (\Exception $e) {
            // Log error dan fallback ke method lama
            log_message('error', 'Error in showPerKelas optimized method: ' . $e->getMessage());
            $dataSantri = $this->prestasiModel->getSantriWithPrestasi($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
        }

        //ambil data materi pelajaran
        $dataMateriPelajaran = $this->prestasiModel->getMateriPelajaran($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
        $data = [
            'page_title' => 'Prestasi Santri',
            'dataSantri' => $dataSantri,
            'dataMateriPelajaran' => $dataMateriPelajaran,
        ];

        return view('backend/prestasi/prestasiPerKelas', $data); // Update the view path as necessary
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
}
