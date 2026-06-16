<?php

namespace App\Controllers\Backend\Luckydraw;

use App\Controllers\BaseController;
use App\Models\Backend\Luckydraw\LuckydrawKegiatanModel;
use App\Models\Backend\Luckydraw\LuckydrawUserKegiatanModel;

class PilihKegiatan extends BaseController
{
    public function index()
    {
        $kegiatanModel = new LuckydrawKegiatanModel();
        $userKegiatanModel = new LuckydrawUserKegiatanModel();
        
        $userId = session()->get('user_id') ?? user_id(); // Using Myth:Auth user_id()
        $isAdmin = in_groups('Admin');

        if ($isAdmin) {
            $kegiatan = $kegiatanModel->findAll();
        } else {
            $kegiatan = $userKegiatanModel->getKegiatanByUser($userId);
        }

        // If only 1 kegiatan available, auto select it
        if (count($kegiatan) === 1) {
            session()->set('active_id_kegiatan', $kegiatan[0]->id);
            session()->set('active_nama_kegiatan', $kegiatan[0]->nama_kegiatan);
            // Redirect based on role
            if ($isAdmin) {
                return redirect()->to('backend/luckydraw/dashboard/admin');
            } elseif (in_groups('PanitiaUndianPemenang')) {
                return redirect()->to('backend/luckydraw/undian');
            } else {
                return redirect()->to('backend/luckydraw/undian/verifikasi');
            }
        }

        $data = [
            'page_title'    => 'Pilih Kegiatan Lucky Draw',
            'kegiatan' => $kegiatan
        ];

        return view('backend/luckydraw/pilih_kegiatan', $data);
    }

    public function set($id)
    {
        $kegiatanModel = new LuckydrawKegiatanModel();
        $kegiatan = $kegiatanModel->find($id);

        if ($kegiatan) {
            session()->set('active_id_kegiatan', $kegiatan->id);
            session()->set('active_nama_kegiatan', $kegiatan->nama_kegiatan);
        }

        // Redirect based on role
        if (in_groups('Admin')) {
            return redirect()->to('backend/luckydraw/dashboard/admin');
        } elseif (in_groups('PanitiaUndianPemenang')) {
            return redirect()->to('backend/luckydraw/undian');
        } else {
            return redirect()->to('backend/luckydraw/undian/verifikasi');
        }
    }
}
