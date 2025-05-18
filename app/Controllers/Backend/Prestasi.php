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
        $this->prestasiModel->save([
            'IdSantri' => $this->request->getPost('IdSantri'),
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdKelas' => $this->request->getPost('IdKelas'),
            'IdGuru' => $this->request->getPost('IdGuru'),
            'IdMateriPelajaran' => $this->request->getPost('IdMateriPelajaran'),
            'JenisPrestasi' => $this->request->getPost('JenisPrestasi'),
            'Tingkatan' => $this->request->getPost('Tingkatan'),
            'Status' => $this->request->getPost('Status'),
            'Tanggal' => date('Y-m-d'),
            'Keterangan' => $this->request->getPost('Keterangan')
        ]);

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

        $dataSantri = $this->prestasiModel->getSantriWithPrestasi($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
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
