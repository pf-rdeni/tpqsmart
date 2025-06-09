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
        if ($this->request->getMethod() == 'GET' && $this->request->getGet('content')) {
            // Ambil data dari GET menggunakan getGet()
            $content = $this->request->getGet('content');
            $size = (int)$this->request->getGet('size') ?: 300;
            $userType = $this->request->getGet('user_type');
            $userId = $this->request->getGet('user_id');
            $userName = $this->request->getGet('user_name');
            $userPosition = $this->request->getGet('user_position');

            // Validasi input
            if (empty($content) || empty($userType) || empty($userId) || empty($userName) || empty($userPosition)) {
                return redirect()->to(base_url('backend/qr'))->with('error', 'Semua field harus diisi.');
            }

            // Logika untuk generate QR code
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                'scale' => $size,
                'imageBase64' => false,
                'addQuietzone' => true,
                'quietzoneSize' => 4,
            ]);

            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrString = $qrcode->render($content);

            // Buat direktori jika belum ada
            if (!is_dir(FCPATH . 'uploads/qr')) {
                mkdir(FCPATH . 'uploads/qr', 0777, true);
            }

            // Simpan QR code sebagai file SVG
            $filename = uniqid() . '.svg';
            file_put_contents(FCPATH . 'uploads/qr/' . $filename, $qrString);

            // Simpan data ke database
            $data = [
                'FileName' => $filename,
                'Content' => $content,
                'UserType' => $userType,
                'UserId' => $userId,
                'UserName' => $userName,
                'UserPosition' => $userPosition
            ];

            if ($this->qrCodeModel->insert($data)) {
                return redirect()->to(base_url('backend/qr'))->with('success', 'QR Code berhasil dibuat dengan nama file: ' . $filename);
            } else {
                return redirect()->to(base_url('backend/qr'))->with('error', 'Gagal menyimpan data QR Code.');
            }
        }

        $data['page_title'] = 'QR Code Generator';
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
