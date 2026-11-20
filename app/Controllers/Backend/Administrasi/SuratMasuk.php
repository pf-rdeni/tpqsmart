<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratModel;
use App\Models\Administrasi\SuratKategoriModel;
use App\Models\Administrasi\SuratDisposisiModel;
use App\Models\Administrasi\SuratLampiranModel;
use App\Models\UserModel;
use App\Models\TpqModel;
use App\Models\HelpFunctionModel;

class SuratMasuk extends BaseController
{
    protected $suratModel;
    protected $kategoriModel;
    protected $disposisiModel;
    protected $lampiranModel;
    protected $userModel;
    protected $tpqModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->suratModel     = new SuratModel();
        $this->kategoriModel  = new SuratKategoriModel();
        $this->disposisiModel = new SuratDisposisiModel();
        $this->lampiranModel  = new SuratLampiranModel();
        $this->userModel      = new UserModel();
        $this->tpqModel       = new TpqModel();
        $this->helpFunction   = new HelpFunctionModel();
    }

    /**
     * Daftar Surat Masuk
     */
    public function index()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $builder = $this->suratModel->select('tbl_surat.*, k.NamaKategori, k.Warna as WarnaKategori, t.NamaTpq,
                                            COUNT(d.Id) as TotalDisposisi,
                                            l.PathFile as FileScan, l.NamaFile as NamaFileScan')
                                    ->join('tbl_surat_kategori k', 'k.Id = tbl_surat.IdKategori', 'left')
                                    ->join('tbl_tpq t', 't.IdTpq = tbl_surat.IdTpq', 'left')
                                    ->join('tbl_surat_disposisi d', 'd.IdSurat = tbl_surat.Id', 'left')
                                    ->join('tbl_surat_lampiran l', 'l.IdSurat = tbl_surat.Id', 'left')
                                    ->where('tbl_surat.JenisSurat', 'Masuk');

        if (!$isAdmin && !empty($idTpq)) {
            $builder->where('tbl_surat.IdTpq', $idTpq);
        }

        $suratMasukList = $builder->groupBy('tbl_surat.Id')
                                  ->orderBy('tbl_surat.Id', 'DESC')
                                  ->findAll();

        $kategoriList = $this->kategoriModel->getByJenis('Masuk');

        $data = [
            'page_title'     => 'Daftar Surat Masuk',
            'suratMasukList' => $suratMasukList,
            'kategoriList'   => $kategoriList,
            'idTpq'          => $idTpq,
            'isAdmin'        => $isAdmin,
        ];

        return view('backend/administrasi/surat_masuk/index', $data);
    }

    /**
     * Form Catat Surat Masuk Baru
     */
    public function create()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $kategoriList = $this->kategoriModel->getByJenis('Masuk');
        $listTpq      = $this->helpFunction->getDataTpq(false);

        // Auto generate no agenda
        $currentYear = date('Y');
        $totalTahunIni = $this->suratModel->where('JenisSurat', 'Masuk')
                                          ->where('YEAR(TanggalTerima)', $currentYear)
                                          ->countAllResults();
        $suggestedAgenda = 'AGM-' . $currentYear . '-' . str_pad($totalTahunIni + 1, 3, '0', STR_PAD_LEFT);

        $data = [
            'page_title'      => 'Catat Surat Masuk Baru',
            'kategoriList'    => $kategoriList,
            'listTpq'         => $listTpq,
            'suggestedAgenda' => $suggestedAgenda,
            'idTpq'           => $idTpq,
            'isAdmin'         => $isAdmin,
        ];

        return view('backend/administrasi/surat_masuk/create', $data);
    }

    /**
     * Simpan Surat Masuk
     */
    public function store()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $rules = [
            'AsalSurat'     => 'required|min_length[3]',
            'NomorSurat'    => 'required',
            'Hal'           => 'required|min_length[3]',
            'TanggalTerima' => 'required|valid_date',
            'FileScan'      => 'permit_empty|ext_in[FileScan,pdf,jpg,jpeg,png]|max_size[FileScan,5120]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $targetTpq = $isAdmin ? ($this->request->getPost('IdTpq') ?: ($idTpq ?: 1)) : $idTpq;

        $saveData = [
            'IdTpq'         => $targetTpq,
            'IdKategori'    => $this->request->getPost('IdKategori') ?: 2, // Default Pemberitahuan jika kosong
            'JenisSurat'    => 'Masuk',
            'NoAgenda'      => $this->request->getPost('NoAgenda'),
            'NomorSurat'    => $this->request->getPost('NomorSurat'),
            'AsalSurat'     => $this->request->getPost('AsalSurat'),
            'Tanggal'       => $this->request->getPost('Tanggal') ?: date('Y-m-d'),
            'TanggalTerima' => $this->request->getPost('TanggalTerima'),
            'Hal'           => $this->request->getPost('Hal'),
            'Lampiran'      => $this->request->getPost('Lampiran'),
            'Catatan'       => $this->request->getPost('Catatan'),
            'Status'        => 'Final',
            'CreatedBy'     => function_exists('user_id') ? user_id() : null,
        ];

        $idSurat = $this->suratModel->insert($saveData);

        // Upload Berkas Scan jika ada
        $fileScan = $this->request->getFile('FileScan');
        if ($fileScan && $fileScan->isValid() && !$fileScan->hasMoved()) {
            $fileName = $fileScan->getRandomName();
            $fileScan->move(FCPATH . 'uploads/surat_masuk/', $fileName);

            $this->lampiranModel->insert([
                'IdSurat'  => $idSurat,
                'NamaFile' => $fileScan->getClientName(),
                'PathFile' => $fileName,
                'Ukuran'   => $fileScan->getSize(),
                'TipeFile' => $fileScan->getClientMimeType(),
            ]);
        }

        return redirect()->to(base_url('backend/administrasi/surat-masuk'))->with('message', 'Surat masuk berhasil dicatat');
    }

    /**
     * Detail Surat Masuk & Lembar Disposisi
     */
    public function detail($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $surat = $this->suratModel->select('tbl_surat.*, k.NamaKategori, k.Warna as WarnaKategori, t.NamaTpq')
                                  ->join('tbl_surat_kategori k', 'k.Id = tbl_surat.IdKategori', 'left')
                                  ->join('tbl_tpq t', 't.IdTpq = tbl_surat.IdTpq', 'left')
                                  ->where('tbl_surat.Id', $id)
                                  ->first();

        if (!$surat) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat masuk tidak ditemukan');
        }

        if (!$isAdmin && $surat['IdTpq'] != $idTpq) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses ditolak: Surat masuk bukan milik lembaga Anda');
        }

        $disposisiList = $this->disposisiModel->getBySurat($id);
        $lampiranList  = $this->lampiranModel->getBySurat($id);

        // Daftar staf / user untuk tujuan disposisi (terisolasi sesuai lembaga jika bukan admin)
        $db = \Config\Database::connect();
        if ($isAdmin) {
            $userList = $db->table('users')->select('id, username, email')->orderBy('username', 'ASC')->get()->getResultArray();
        } else {
            $userList = $db->table('users')
                           ->select('users.id, users.username, users.email')
                           ->join('tbl_guru', 'tbl_guru.IdGuru = users.nik', 'inner')
                           ->where('tbl_guru.IdTpq', $idTpq)
                           ->orderBy('users.username', 'ASC')
                           ->get()->getResultArray();
        }

        $data = [
            'page_title'    => 'Detail & Disposisi Surat Masuk',
            'surat'         => $surat,
            'disposisiList' => $disposisiList,
            'lampiranList'  => $lampiranList,
            'userList'      => $userList,
            'idTpq'         => $idTpq,
            'isAdmin'       => $isAdmin,
        ];

        return view('backend/administrasi/surat_masuk/detail', $data);
    }

    /**
     * Tambah Disposisi Staf
     */
    public function tambahDisposisi()
    {
        $rules = [
            'IdSurat'   => 'required|numeric',
            'KeUser'    => 'required|numeric',
            'Instruksi' => 'required|min_length[3]',
            'Prioritas' => 'required|in_list[Biasa,Segera,Sangat Segera]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idSurat = $this->request->getPost('IdSurat');
        $currentUserId = function_exists('user_id') ? user_id() : 1;

        $saveData = [
            'IdSurat'    => $idSurat,
            'DariUser'   => $currentUserId,
            'KeUser'     => $this->request->getPost('KeUser'),
            'Instruksi'  => $this->request->getPost('Instruksi'),
            'Prioritas'  => $this->request->getPost('Prioritas'),
            'Deadline'   => $this->request->getPost('Deadline') ?: null,
            'StatusBaca' => 0,
        ];

        $this->disposisiModel->insert($saveData);

        return redirect()->to(base_url('backend/administrasi/surat-masuk/detail/' . $idSurat))->with('message', 'Disposisi berhasil ditambahkan');
    }

    /**
     * Tandai Disposisi Telah Dibaca / Ditindaklanjuti
     */
    public function updateStatusDisposisi($idDisposisi = null)
    {
        $disposisi = $this->disposisiModel->find($idDisposisi);
        if (!$disposisi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data disposisi tidak ditemukan']);
        }

        $catatanBalas = $this->request->getPost('CatatanBalas');

        $this->disposisiModel->update($idDisposisi, [
            'StatusBaca'   => 1,
            'TanggalBaca'  => date('Y-m-d H:i:s'),
            'CatatanBalas' => $catatanBalas ?: $disposisi['CatatanBalas']
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Status disposisi berhasil diperbarui']);
    }

    /**
     * Cetak Lembar Disposisi Standar Kedinasan (Format Print Resmi)
     */
    public function cetakDisposisi($idSurat = null)
    {
        $idTpqSession = session()->get('IdTpq');
        $isAdmin      = ($idTpqSession === '0' || $idTpqSession === 0 || empty($idTpqSession));

        $surat = $this->suratModel->select('tbl_surat.*, k.NamaKategori, t.NamaTpq, t.Alamat as AlamatTpq')
                                  ->join('tbl_surat_kategori k', 'k.Id = tbl_surat.IdKategori', 'left')
                                  ->join('tbl_tpq t', 't.IdTpq = tbl_surat.IdTpq', 'left')
                                  ->where('tbl_surat.Id', $idSurat)
                                  ->first();

        if (!$surat) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat tidak ditemukan');
        }

        if (!$isAdmin && $surat['IdTpq'] != $idTpqSession) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akses ditolak: Dokumen bukan milik lembaga Anda');
        }

        $disposisiList = $this->disposisiModel->getBySurat($idSurat);

        $data = [
            'surat'         => $surat,
            'disposisiList' => $disposisiList,
        ];

        return view('backend/administrasi/surat_masuk/disposisi/cetak', $data);
    }

    /**
     * Hapus Surat Masuk
     */
    public function delete($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if (!$isAdmin && $surat['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        // Hapus file lampiran jika ada
        $lampiran = $this->lampiranModel->getBySurat($id);
        foreach ($lampiran as $l) {
            $path = FCPATH . 'uploads/surat_masuk/' . $l['PathFile'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $this->lampiranModel->where('IdSurat', $id)->delete();
        $this->disposisiModel->where('IdSurat', $id)->delete();
        $this->suratModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Surat masuk dan riwayat disposisi berhasil dihapus']);
    }
}
