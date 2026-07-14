<?php

namespace App\Controllers\Backend\Luckydraw;

use App\Controllers\BaseController;
use App\Models\Backend\Luckydraw\LuckydrawUndianModel;
use App\Models\Backend\Luckydraw\LuckydrawBarangModel;

class LuckydrawUndian extends BaseController
{
    protected $undianModel;
    protected $barangModel;

    public function __construct()
    {
        $this->undianModel = new LuckydrawUndianModel();
        $this->barangModel = new LuckydrawBarangModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Input Pemenang Lucky Draw',
            'barang' => $this->barangModel->getBarangWithSisa(session('active_id_kegiatan')),
            'pemenang' => $this->undianModel->getPemenangList(0, session('active_id_kegiatan')), // Hanya yang belum diambil
            'last_selected_id_barang' => session()->get('last_selected_id_barang')
        ];
        return view('backend/luckydraw/undian/input', $data);
    }

    public function store()
    {
        $id_barang = $this->request->getPost('id_barang');
        $no_undian = $this->request->getPost('no_undian');
        $isAjax = $this->request->isAJAX();

        // Store the selection in session so it persists after redirect
        session()->set('last_selected_id_barang', $id_barang);

        // Validate kupon range
        $id_kegiatan = session('active_id_kegiatan');
        $kegiatan = (new \App\Models\Backend\Luckydraw\LuckydrawKegiatanModel())->find($id_kegiatan);
        
        if ($kegiatan) {
            if ($no_undian < $kegiatan->kupon_min || $no_undian > $kegiatan->kupon_max) {
                $msg = 'Nomor undian harus antara ' . $kegiatan->kupon_min . ' dan ' . $kegiatan->kupon_max . '.';
                if ($isAjax) {
                    return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
                }
                session()->setFlashdata('pesan', '<div class="alert alert-danger">' . $msg . '</div>');
                return redirect()->to('/backend/luckydraw/undian');
            }
        }

        // Validate if item exists
        $barang = $this->barangModel->find($id_barang);
        if (!$barang) {
            $msg = 'Barang tidak ditemukan.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . $msg . '</div>');
            return redirect()->to('/backend/luckydraw/undian');
        }

        // Validate remaining stock
        $winnerCount = $this->undianModel->where('id_barang', $id_barang)->countAllResults();
        if ($winnerCount >= $barang->jumlah) {
            $msg = 'Gagal! Stok barang hadiah "' . esc($barang->nama_barang) . '" sudah terisi penuh / habis.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . $msg . '</div>');
            return redirect()->to('/backend/luckydraw/undian');
        }

        // Check if number already exists
        $exist = $this->undianModel->where('no_undian', $no_undian)->first();
        if ($exist) {
            $msg = 'Nomor undian tersebut sudah terdaftar sebagai pemenang.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . $msg . '</div>');
            return redirect()->to('/backend/luckydraw/undian');
        }

        $this->undianModel->save([
            'id_kegiatan' => session('active_id_kegiatan'),
            'id_barang' => $id_barang,
            'no_undian' => $no_undian,
            'status_diambil' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $msg = 'Pemenang berhasil ditambahkan.';
        if ($isAjax) {
            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        }
        session()->setFlashdata('pesan', '<div class="alert alert-success">' . $msg . '</div>');
        return redirect()->to('/backend/luckydraw/undian');
    }

    public function verifikasi()
    {
        $data = [
            'page_title' => 'Verifikasi Pemenang Lucky Draw',
            'pemenang' => $this->undianModel->getPemenangList(0, session('active_id_kegiatan')) // Hanya yang belum diambil
        ];
        return view('backend/luckydraw/undian/verifikasi', $data);
    }

    public function prosesSerahTerima()
    {
        $no_undian = $this->request->getPost('no_undian');
        $pemenang = $this->undianModel->where('no_undian', $no_undian)->first();

        if ($pemenang) {
            if ($pemenang->status_diambil == 1) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Hadiah untuk nomor undian ini sudah diambil.']);
            }

            $this->undianModel->update($pemenang->id, [
                'status_diambil' => 1,
                'waktu_diambil'  => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Verifikasi berhasil. Status telah diubah menjadi "Sudah Diambil".']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor undian tidak ditemukan atau belum menang.']);
    }

    // ----------------------------------------------------------------
    // Dashboard PanitiaUndianPemenang
    // ----------------------------------------------------------------
    public function dashboardPemenang()
    {
        $idKegiatan = session('active_id_kegiatan');
        $barang   = $this->barangModel->getBarangWithSisa($idKegiatan);
        $pemenang = $this->undianModel->getPemenangList(null, $idKegiatan);

        $totalBarang        = count($barang);
        $totalPemenang      = count($pemenang);
        $totalSudahDiambil  = count(array_filter((array) $pemenang, fn($p) => $p->status_diambil == 1));
        $totalBelumDiambil  = $totalPemenang - $totalSudahDiambil;

        // Hitung total slot barang & terisi
        $totalSlotBarang    = array_sum(array_map(fn($b) => $b->jumlah, $barang));
        $totalTerisi        = $totalPemenang;
        $totalSisaSlot      = $totalSlotBarang - $totalTerisi;

        // 5 pemenang terbaru
        $recentPemenang = array_slice($pemenang, 0, 5);

        $data = [
            'page_title'        => 'Dashboard Lucky Draw - Input Pemenang',
            'barang'            => $barang,
            'pemenang'          => $pemenang,
            'recentPemenang'    => $recentPemenang,
            'totalBarang'       => $totalBarang,
            'totalSlotBarang'   => $totalSlotBarang,
            'totalPemenang'     => $totalPemenang,
            'totalSudahDiambil' => $totalSudahDiambil,
            'totalBelumDiambil' => $totalBelumDiambil,
            'totalSisaSlot'     => $totalSisaSlot,
        ];

        return view('backend/luckydraw/dashboard/pemenang', $data);
    }

    // ----------------------------------------------------------------
    // Dashboard PanitiaUndianVerifikasi
    // ----------------------------------------------------------------
    public function dashboardVerifikasi()
    {
        $idKegiatan = session('active_id_kegiatan');
        $pemenang = $this->undianModel->getPemenangList(null, $idKegiatan);

        $totalPemenang      = count($pemenang);
        $totalSudahDiambil  = count(array_filter((array) $pemenang, fn($p) => $p->status_diambil == 1));
        $totalBelumDiambil  = $totalPemenang - $totalSudahDiambil;
        $persenSelesai      = $totalPemenang > 0 ? round(($totalSudahDiambil / $totalPemenang) * 100) : 0;

        // Pemenang yang belum diambil (antre untuk verifikasi)
        $antrean = array_filter((array) $pemenang, fn($p) => $p->status_diambil == 0);
        $antrean = array_values($antrean);

        $data = [
            'page_title'        => 'Dashboard Lucky Draw - Verifikasi',
            'pemenang'          => $pemenang,
            'antrean'           => $antrean,
            'totalPemenang'     => $totalPemenang,
            'totalSudahDiambil' => $totalSudahDiambil,
            'totalBelumDiambil' => $totalBelumDiambil,
            'persenSelesai'     => $persenSelesai,
        ];

        return view('backend/luckydraw/dashboard/verifikasi', $data);
    }

    // ----------------------------------------------------------------
    // Dashboard Admin Lucky Draw
    // ----------------------------------------------------------------
    public function dashboardAdmin()
    {
        $idKegiatan = session('active_id_kegiatan');

        $barang       = $this->barangModel->getBarangWithSisa($idKegiatan);
        $pemenang     = $this->undianModel->getPemenangList(null, $idKegiatan);

        $totalBarang       = count($barang);
        $totalSlotBarang   = array_sum(array_column((array) $barang, 'jumlah'));
        $totalPemenang     = count($pemenang);
        $totalSudahDiambil = count(array_filter((array) $pemenang, fn($p) => $p->status_diambil == 1));
        $totalBelumDiambil = $totalPemenang - $totalSudahDiambil;
        $totalSisaSlot     = $totalSlotBarang - $totalPemenang;

        $kegiatanModel = new \App\Models\Backend\Luckydraw\LuckydrawKegiatanModel();
        $kegiatan      = $kegiatanModel->find($idKegiatan);

        $recentPemenang = array_slice((array) $pemenang, 0, 5);

        $data = [
            'page_title'        => 'Dashboard Lucky Draw',
            'kegiatan'          => $kegiatan,
            'barang'            => $barang,
            'pemenang'          => $pemenang,
            'recentPemenang'    => $recentPemenang,
            'totalBarang'       => $totalBarang,
            'totalSlotBarang'   => $totalSlotBarang,
            'totalPemenang'     => $totalPemenang,
            'totalSudahDiambil' => $totalSudahDiambil,
            'totalBelumDiambil' => $totalBelumDiambil,
            'totalSisaSlot'     => $totalSisaSlot,
        ];

        return view('backend/luckydraw/dashboard/admin', $data);
    }

    // ----------------------------------------------------------------
    // Semua Pemenang (Belum & Sudah Diambil)
    // ----------------------------------------------------------------
    public function semuaPemenang()
    {
        $data = [
            'page_title' => 'Semua Pemenang Lucky Draw',
            'pemenang'   => $this->undianModel->getPemenangList(null, session('active_id_kegiatan'))
        ];
        return view('backend/luckydraw/undian/semua', $data);
    }

    public function controlReset()
    {
        if (!in_groups('Admin')) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger">Anda tidak memiliki hak akses ke halaman Control Reset.</div>');
            return redirect()->to('backend/luckydraw/dashboard/admin');
        }

        $kegiatanModel = new \App\Models\Backend\Luckydraw\LuckydrawKegiatanModel();
        $kegiatan = $kegiatanModel->orderBy('id', 'DESC')->findAll();

        $data = [
            'page_title' => 'Control Reset Lucky Draw',
            'kegiatan'   => $kegiatan,
            'active_id_kegiatan' => session('active_id_kegiatan')
        ];

        return view('backend/luckydraw/undian/control_reset', $data);
    }

    public function getBarangByKegiatan($idKegiatan)
    {
        if (!in_groups('Admin')) {
            return $this->response->setJSON([]);
        }
        $barang = $this->barangModel->where('id_kegiatan', $idKegiatan)->findAll();
        return $this->response->setJSON($barang);
    }

    public function prosesReset()
    {
        if (!in_groups('Admin')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk melakukan reset.']);
        }

        $idKegiatan = $this->request->getPost('id_kegiatan');
        $idBarang = $this->request->getPost('id_barang');
        $resetPemenang = $this->request->getPost('reset_pemenang');
        $resetStatusDiambil = $this->request->getPost('reset_status_diambil');
        $resetBarang = $this->request->getPost('reset_barang');
        $resetPanitia = $this->request->getPost('reset_panitia');

        if (!$idKegiatan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan pilih kegiatan terlebih dahulu.']);
        }

        $kegiatanModel = new \App\Models\Backend\Luckydraw\LuckydrawKegiatanModel();
        $kegiatan = $kegiatanModel->find($idKegiatan);
        if (!$kegiatan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kegiatan tidak ditemukan.']);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $messages = [];

            // 1. Reset Status Pengambilan Hadiah saja
            if ($resetStatusDiambil && !$resetPemenang && !$resetBarang) {
                $builder = $db->table('tbl_luckydraw_undian');
                $builder->where('id_kegiatan', $idKegiatan);
                if ($idBarang) {
                    $builder->where('id_barang', $idBarang);
                }
                $builder->update([
                    'status_diambil' => 0,
                    'waktu_diambil'  => null
                ]);
                $messages[] = 'Status pengambilan hadiah berhasil di-reset.';
            }

            // 2. Reset Pemenang Undian (Hapus pemenang)
            if ($resetPemenang && !$resetBarang) {
                $builder = $db->table('tbl_luckydraw_undian');
                $builder->where('id_kegiatan', $idKegiatan);
                if ($idBarang) {
                    $builder->where('id_barang', $idBarang);
                }
                $builder->delete();
                $messages[] = 'Data pemenang undian berhasil dihapus.';
            }

            // 3. Reset Barang Hadiah (Hapus barang)
            if ($resetBarang) {
                // Hapus pemenang untuk barang/kegiatan ini dulu (karena foreign key / integritas data)
                $undianBuilder = $db->table('tbl_luckydraw_undian');
                $undianBuilder->where('id_kegiatan', $idKegiatan);
                if ($idBarang) {
                    $undianBuilder->where('id_barang', $idBarang);
                }
                $undianBuilder->delete();

                // Hapus barang
                $barangBuilder = $db->table('tbl_luckydraw_barang');
                $barangBuilder->where('id_kegiatan', $idKegiatan);
                if ($idBarang) {
                    $barangBuilder->where('id', $idBarang);
                }
                $barangBuilder->delete();
                $messages[] = 'Data barang hadiah berhasil dihapus.';
            }

            // 4. Reset Panitia Kegiatan
            if ($resetPanitia) {
                $panitiaBuilder = $db->table('tbl_luckydraw_user_kegiatan');
                $panitiaBuilder->where('id_kegiatan', $idKegiatan);
                $panitiaBuilder->delete();
                $messages[] = 'Daftar penugasan panitia berhasil dihapus.';
            }

            if (empty($messages)) {
                $db->transRollback();
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada pilihan reset yang dicentang.']);
            }

            // Hapus session pilihan terakhir barang jika kegiatan yang di-reset adalah kegiatan aktif
            if ($idKegiatan == session('active_id_kegiatan')) {
                session()->remove('last_selected_id_barang');
            }

            $db->transCommit();
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => implode(' ', $messages)
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memproses reset: ' . $e->getMessage()
            ]);
        }
    }
}
