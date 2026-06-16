<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\Backend\Luckydraw\LuckydrawUndianModel;
use App\Models\Backend\Luckydraw\LuckydrawKegiatanModel;

class Luckydraw extends BaseController
{
    protected $undianModel;

    public function __construct()
    {
        $this->undianModel = new LuckydrawUndianModel();
    }

    private function resolveKegiatan()
    {
        $kegiatanModel = new LuckydrawKegiatanModel();
        $activeKegiatan = $kegiatanModel->getActiveKegiatan();

        if (empty($activeKegiatan)) {
            return null; // No active events
        }

        // If a specific one is selected via session and it's still active
        if (session()->has('public_id_kegiatan')) {
            $selectedId = session()->get('public_id_kegiatan');
            foreach ($activeKegiatan as $k) {
                if ($k->id == $selectedId) {
                    return $k;
                }
            }
        }

        // If only 1 active, auto select
        if (count($activeKegiatan) === 1) {
            session()->set('public_id_kegiatan', $activeKegiatan[0]->id);
            return $activeKegiatan[0];
        }

        // Multiple active, none selected
        return 'multiple';
    }

    public function index()
    {
        $kegiatan = $this->resolveKegiatan();

        if ($kegiatan === null) {
            return view('frontend/luckydraw/no_event', ['page_title' => 'Tidak Ada Kegiatan Aktif']);
        } elseif ($kegiatan === 'multiple') {
            return redirect()->to('luckydraw/pilih');
        }

        $data = [
            'page_title' => 'Cek Undian Lucky Draw',
            'kegiatan'   => $kegiatan
        ];
        return view('frontend/luckydraw/index', $data);
    }

    public function pilih()
    {
        $kegiatanModel = new LuckydrawKegiatanModel();
        $activeKegiatan = $kegiatanModel->getActiveKegiatan();

        if (count($activeKegiatan) <= 1) {
            return redirect()->to('luckydraw'); // Auto resolve handles this
        }

        $data = [
            'page_title' => 'Pilih Kegiatan Lucky Draw',
            'kegiatan'   => $activeKegiatan
        ];

        return view('frontend/luckydraw/pilih_kegiatan', $data);
    }

    public function set($id)
    {
        $kegiatanModel = new LuckydrawKegiatanModel();
        $kegiatan = $kegiatanModel->find($id);

        if ($kegiatan && $kegiatan->status === 'active') {
            session()->set('public_id_kegiatan', $kegiatan->id);
        }

        return redirect()->to('luckydraw');
    }

    public function search()
    {
        $no_undian = $this->request->getPost('no_undian');
        
        $kegiatan = $this->resolveKegiatan();
        $idKegiatan = is_object($kegiatan) ? $kegiatan->id : null;

        // Find if this number won
        $pemenang = $this->undianModel->select('tbl_luckydraw_undian.*, tbl_luckydraw_barang.nama_barang, tbl_luckydraw_barang.no_barang, tbl_luckydraw_barang.kategori')
                                      ->join('tbl_luckydraw_barang', 'tbl_luckydraw_barang.id = tbl_luckydraw_undian.id_barang')
                                      ->where('no_undian', $no_undian);
                                      
        if ($idKegiatan) {
            $pemenang = $pemenang->where('tbl_luckydraw_undian.id_kegiatan', $idKegiatan);
        }

        $pemenang = $pemenang->first();

        if ($pemenang) {
            return $this->response->setJSON([
                'status' => 'success',
                'is_winner' => true,
                'data' => $pemenang
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'is_winner' => false,
            'message' => 'Maaf, nomor undian ' . $no_undian . ' belum beruntung.'
        ]);
    }

    public function list()
    {
        $kegiatan = $this->resolveKegiatan();

        if ($kegiatan === null) {
            return view('frontend/luckydraw/no_event', ['page_title' => 'Tidak Ada Kegiatan Aktif']);
        } elseif ($kegiatan === 'multiple') {
            return redirect()->to('luckydraw/pilih');
        }

        $data = [
            'page_title' => 'Daftar Pemenang Lucky Draw',
            'kegiatan'   => $kegiatan,
            'pemenang'   => $this->undianModel->getPemenangList(null, $kegiatan->id)
        ];
        return view('frontend/luckydraw/list', $data);
    }
}
