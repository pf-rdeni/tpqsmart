<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\QrCodeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Qr extends BaseController
{
    protected $qrCodeModel;

    public function __construct()
    {
        $this->qrCodeModel = new QrCodeModel();
    }

    public function index()
    {
        // membawa data page_title ke view
        $data['page_title'] = 'QR Code Management';
        $data['qr_codes'] = $this->qrCodeModel->findAll();

        return view('backend/qr/index', $data);
    }

    public function generate()
    {
        $data['page_title'] = 'QR Code Generator Multi Fungsi';
        return view('backend/qr/generate', $data);
    }

    public function print()
    {
        // Logika untuk print QR label akan ditambahkan di sini
        $data['page_title'] = 'QR Code Print';
        $data['qr_codes'] = $this->qrCodeModel->findAll();
        return view('backend/qr/print', $data);
    }
}
