<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class Kontak extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'Kontak Kami'
        ];
        return view('frontend/kontak', $data);
    }

    public function kirim()
    {
        // Validasi input
        $rules = [
            'nama' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'pesan' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Proses kirim email
        $email = \Config\Services::email();
        
        $email->setFrom('noreply@tpqonline.com', 'TPQ Online');
        $email->setTo('admin@tpqonline.com');
        $email->setSubject('Pesan Kontak Baru dari ' . $this->request->getPost('nama'));
        
        $message = "Nama: " . $this->request->getPost('nama') . "\n";
        $message .= "Email: " . $this->request->getPost('email') . "\n";
        $message .= "Telepon: " . $this->request->getPost('telepon') . "\n\n";
        $message .= "Pesan:\n" . $this->request->getPost('pesan');
        
        $email->setMessage($message);

        if ($email->send()) {
            return redirect()->back()->with('success', 'Pesan Anda telah terkirim!');
        } else {
            return redirect()->back()->with('error', 'Maaf, terjadi kesalahan. Silakan coba lagi.');
        }
    }
} 