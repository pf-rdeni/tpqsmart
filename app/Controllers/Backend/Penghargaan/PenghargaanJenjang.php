<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\Backend\Penghargaan\PenghargaanJenjangModel;

/**
 * Controller Pengelompokan Kelas per Jenjang untuk Juara Umum Lembaga.
 */
class PenghargaanJenjang extends BaseController
{
    protected $jenjangModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->jenjangModel = new PenghargaanJenjangModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq       = (string) (session()->get('IdTpq') ?? '0');
        $listJenjang = $this->jenjangModel->getWithKelasByTpq($idTpq, false);
        $listKelas   = $this->helpFunction->getDataKelas();

        return view('backend/penghargaan/jenjang/index', [
            'page_title'  => 'Kelola Jenjang Kelas',
            'menu_open'   => 'penghargaan',
            'menu_active' => 'penghargaan-jenjang',
            'listJenjang' => $listJenjang,
            'listKelas'   => $listKelas,
        ]);
    }

    public function store()
    {
        $idTpq       = (string) (session()->get('IdTpq') ?? '0');
        $namaJenjang = trim((string) $this->request->getPost('NamaJenjang'));
        $idKelasList = $this->request->getPost('IdKelas');
        $urutan      = (int) ($this->request->getPost('Urutan') ?: 1);

        if ($namaJenjang === '' || empty($idKelasList) || !is_array($idKelasList)) {
            return redirect()->back()->with('error', 'Nama jenjang dan minimal 1 kelas harus dipilih.');
        }

        $inserted = 0;
        foreach ($idKelasList as $idKelas) {
            $exists = $this->jenjangModel
                ->where('IdTpq', $idTpq)
                ->where('NamaJenjang', $namaJenjang)
                ->where('IdKelas', (string) $idKelas)
                ->first();

            if (!$exists) {
                $this->jenjangModel->insert([
                    'IdTpq'       => $idTpq,
                    'NamaJenjang' => $namaJenjang,
                    'IdKelas'     => (string) $idKelas,
                    'Urutan'      => $urutan,
                    'IsActive'    => 1,
                ]);
                $inserted++;
            }
        }

        return redirect()->to(base_url('backend/penghargaan/jenjang'))
            ->with('success', "Berhasil menambahkan {$inserted} mapping kelas ke jenjang \"{$namaJenjang}\".");
    }

    public function delete(int $id)
    {
        $row = $this->jenjangModel->find($id);
        if (!$row) {
            return redirect()->back()->with('error', 'Data mapping jenjang tidak ditemukan.');
        }

        $this->jenjangModel->delete($id);
        return redirect()->to(base_url('backend/penghargaan/jenjang'))
            ->with('success', 'Mapping kelas jenjang berhasil dihapus.');
    }
}
