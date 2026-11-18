<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratTemplateModel;
use App\Models\Administrasi\SuratKategoriModel;
use App\Models\Administrasi\SuratKopCustomModel;
use App\Models\HelpFunctionModel;

class SuratTemplate extends BaseController
{
    protected $templateModel;
    protected $kategoriModel;
    protected $kopModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->templateModel = new SuratTemplateModel();
        $this->kategoriModel = new SuratKategoriModel();
        $this->kopModel      = new SuratKopCustomModel();
        $this->helpFunction  = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $templates = $this->templateModel->getByTpq($isAdmin ? null : $idTpq);

        $data = [
            'page_title' => 'Template Surat',
            'templates'  => $templates,
            'idTpq'      => $idTpq,
            'isAdmin'    => $isAdmin,
        ];

        return view('backend/administrasi/template/index', $data);
    }

    public function create()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $kategori = $this->kategoriModel->getActive();
        $kopList  = $this->kopModel->where('IsActive', 1)->findAll();
        $listTpq  = $this->helpFunction->getDataTpq(false);

        $data = [
            'page_title' => 'Buat Template Surat Baru',
            'template'   => null,
            'kategori'   => $kategori,
            'kopList'    => $kopList,
            'listTpq'    => $listTpq,
            'idTpq'      => $isAdmin ? 0 : (int)$idTpq,
            'isAdmin'    => $isAdmin,
        ];

        return view('backend/administrasi/template/form', $data);
    }

    public function store()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $rules = [
            'NamaTemplate' => 'required|min_length[3]',
            'IdKategori'   => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $postTpq = $this->request->getPost('IdTpq');
        if ($isAdmin) {
            $targetTpq = ($postTpq !== null && $postTpq !== '') ? (int)$postTpq : 0;
        } else {
            $targetTpq = (int)$idTpq;
        }

        $saveData = [
            'IdTpq'             => $targetTpq,
            'IdKategori'        => $this->request->getPost('IdKategori'),
            'NamaTemplate'      => $this->request->getPost('NamaTemplate'),
            'Deskripsi'         => $this->request->getPost('Deskripsi'),
            'UkuranKertas'      => $this->request->getPost('UkuranKertas') ?: 'A4',
            'Orientasi'         => $this->request->getPost('Orientasi') ?: 'Portrait',
            'MarginAtas'        => (float)($this->request->getPost('MarginAtas') ?: 20),
            'MarginBawah'       => (float)($this->request->getPost('MarginBawah') ?: 20),
            'MarginKiri'        => (float)($this->request->getPost('MarginKiri') ?: 25),
            'MarginKanan'       => (float)($this->request->getPost('MarginKanan') ?: 15),
            'FontFamily'        => $this->request->getPost('FontFamily') ?: 'Times New Roman',
            'FontSize'          => (int)($this->request->getPost('FontSize') ?: 12),
            'SumberKop'         => $this->request->getPost('SumberKop') ?: 'TPQ',
            'IdKopCustom'       => $this->request->getPost('IdKopCustom') ?: null,
            'ShowKopLembaga'    => $this->request->getPost('ShowKopLembaga') ? 1 : 0,
            'ShowGarisPemisah'  => $this->request->getPost('ShowGarisPemisah') ? 1 : 0,
            'ShowNomorSurat'    => $this->request->getPost('ShowNomorSurat') ? 1 : 0,
            'ShowLampiran'      => $this->request->getPost('ShowLampiran') ? 1 : 0,
            'ShowHal'           => $this->request->getPost('ShowHal') ? 1 : 0,
            'ShowTujuan'        => $this->request->getPost('ShowTujuan') ? 1 : 0,
            'ShowSalam'         => $this->request->getPost('ShowSalam') ? 1 : 0,
            'ShowIsi'           => $this->request->getPost('ShowIsi') ? 1 : 0,
            'ShowPenutup'       => $this->request->getPost('ShowPenutup') ? 1 : 0,
            'ShowTempatTanggal' => $this->request->getPost('ShowTempatTanggal') ? 1 : 0,
            'ShowMengetahui'    => $this->request->getPost('ShowMengetahui') ? 1 : 0,
            'ShowQrCode'        => $this->request->getPost('ShowQrCode') ? 1 : 0,
            'SalamPembuka'      => $this->request->getPost('SalamPembuka'),
            'MukadimahIsi'      => $this->request->getPost('MukadimahIsi'),
            'IsiSurat'          => $this->request->getPost('IsiSurat'),
            'Penutup'           => $this->request->getPost('Penutup'),
            'SalamPenutup'      => $this->request->getPost('SalamPenutup'),
            'FormatNomor'       => $this->request->getPost('FormatNomor') ?: '[NO:3]/[KODE-TPQ]/[BLN-ROMAWI]/[TAHUN]',
            'IsDefault'         => $this->request->getPost('IsDefault') ? 1 : 0,
            'IsActive'          => 1,
            'CreatedBy'         => function_exists('user_id') ? user_id() : null,
        ];

        $insertedId = $this->templateModel->insert($saveData);

        if ($saveData['IsDefault'] && $insertedId) {
            $this->templateModel->setDefault($insertedId, $targetTpq, $saveData['IdKategori']);
        }

        return redirect()->to(base_url('backend/administrasi/template'))->with('message', 'Template surat berhasil dibuat');
    }

    public function edit($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $template = $this->templateModel->find($id);
        if (!$template) {
            return redirect()->back()->with('error', 'Template tidak ditemukan');
        }

        if (!$isAdmin && $template['IdTpq'] != $idTpq) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $kategori = $this->kategoriModel->getActive();
        $kopList  = $this->kopModel->where('IsActive', 1)->findAll();
        $listTpq  = $this->helpFunction->getDataTpq(false);

        $data = [
            'page_title' => 'Edit Template Surat',
            'template'   => $template,
            'kategori'   => $kategori,
            'kopList'    => $kopList,
            'listTpq'    => $listTpq,
            'idTpq'      => $idTpq,
            'isAdmin'    => $isAdmin,
        ];

        return view('backend/administrasi/template/form', $data);
    }

    public function update($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->templateModel->find($id);
        if (!$existing) {
            return redirect()->back()->with('error', 'Template tidak ditemukan');
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $rules = [
            'NamaTemplate' => 'required|min_length[3]',
            'IdKategori'   => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $postTpq = $this->request->getPost('IdTpq');
        if ($isAdmin) {
            $targetTpq = ($postTpq !== null && $postTpq !== '') ? (int)$postTpq : (int)($existing['IdTpq'] ?? 0);
        } else {
            $targetTpq = (int)($existing['IdTpq'] ?? $idTpq);
        }

        $updateData = [
            'IdTpq'             => $targetTpq,
            'IdKategori'        => $this->request->getPost('IdKategori'),
            'NamaTemplate'      => $this->request->getPost('NamaTemplate'),
            'Deskripsi'         => $this->request->getPost('Deskripsi'),
            'UkuranKertas'      => $this->request->getPost('UkuranKertas') ?: 'A4',
            'Orientasi'         => $this->request->getPost('Orientasi') ?: 'Portrait',
            'MarginAtas'        => (float)($this->request->getPost('MarginAtas') ?: 20),
            'MarginBawah'       => (float)($this->request->getPost('MarginBawah') ?: 20),
            'MarginKiri'        => (float)($this->request->getPost('MarginKiri') ?: 25),
            'MarginKanan'       => (float)($this->request->getPost('MarginKanan') ?: 15),
            'FontFamily'        => $this->request->getPost('FontFamily') ?: 'Times New Roman',
            'FontSize'          => (int)($this->request->getPost('FontSize') ?: 12),
            'SumberKop'         => $this->request->getPost('SumberKop') ?: 'TPQ',
            'IdKopCustom'       => $this->request->getPost('IdKopCustom') ?: null,
            'ShowKopLembaga'    => $this->request->getPost('ShowKopLembaga') ? 1 : 0,
            'ShowGarisPemisah'  => $this->request->getPost('ShowGarisPemisah') ? 1 : 0,
            'ShowNomorSurat'    => $this->request->getPost('ShowNomorSurat') ? 1 : 0,
            'ShowLampiran'      => $this->request->getPost('ShowLampiran') ? 1 : 0,
            'ShowHal'           => $this->request->getPost('ShowHal') ? 1 : 0,
            'ShowTujuan'        => $this->request->getPost('ShowTujuan') ? 1 : 0,
            'ShowSalam'         => $this->request->getPost('ShowSalam') ? 1 : 0,
            'ShowIsi'           => $this->request->getPost('ShowIsi') ? 1 : 0,
            'ShowPenutup'       => $this->request->getPost('ShowPenutup') ? 1 : 0,
            'ShowTempatTanggal' => $this->request->getPost('ShowTempatTanggal') ? 1 : 0,
            'ShowMengetahui'    => $this->request->getPost('ShowMengetahui') ? 1 : 0,
            'ShowQrCode'        => $this->request->getPost('ShowQrCode') ? 1 : 0,
            'SalamPembuka'      => $this->request->getPost('SalamPembuka'),
            'MukadimahIsi'      => $this->request->getPost('MukadimahIsi'),
            'IsiSurat'          => $this->request->getPost('IsiSurat'),
            'Penutup'           => $this->request->getPost('Penutup'),
            'SalamPenutup'      => $this->request->getPost('SalamPenutup'),
            'FormatNomor'       => $this->request->getPost('FormatNomor') ?: '[NO:3]/[KODE-TPQ]/[BLN-ROMAWI]/[TAHUN]',
            'IsDefault'         => $this->request->getPost('IsDefault') ? 1 : 0,
        ];

        $this->templateModel->update($id, $updateData);

        if ($updateData['IsDefault']) {
            $this->templateModel->setDefault($id, $targetTpq, $updateData['IdKategori']);
        }

        return redirect()->to(base_url('backend/administrasi/template'))->with('message', 'Template surat berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->templateModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Template tidak ditemukan']);
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        if ($this->templateModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Template berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus template']);
    }

    public function toggleActive($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->templateModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Template tidak ditemukan']);
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        if ($this->templateModel->toggleAktif($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status berhasil diubah']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah status']);
    }

    public function setDefault($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->templateModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Template tidak ditemukan']);
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        if ($this->templateModel->setDefault($id, $existing['IdTpq'], $existing['IdKategori'])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Template berhasil diset sebagai default']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menetapkan template default']);
    }
}
