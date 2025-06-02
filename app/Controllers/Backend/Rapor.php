<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\NilaiModel;
use App\Models\SantriBaruModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Rapor extends BaseController
{
    protected $helpFunctionModel;
    protected $nilaiModel;
    protected $santriBaruModel;

    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->nilaiModel = new NilaiModel();
        $this->santriBaruModel = new SantriBaruModel();
    }

    public function index($semester = 'Ganjil')
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();
        $IdKelas = session()->get('IdKelas');
        $lisKelas = $this->helpFunctionModel->getListKelas($IdTpq, $IdTahunAjaran, $IdKelas);

        // Ambil semua data santri
        $listSantri = $this->santriBaruModel->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->where([
                'tbl_santri_baru.IdTpq' => $IdTpq,
                'tbl_santri_baru.Active' => 1
            ])
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')
            ->findAll();

        $data = [
            'page_title' => 'Rapor Santri',
            'listKelas' => $lisKelas,
            'listSantri' => $listSantri,
            'semester' => $semester
        ];

        return view('backend/rapor/index', $data);
    }

    public function getSantriByKelas($IdKelas)
    {
        $santriList = $this->santriBaruModel->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')->where([
            'tbl_santri_baru.IdTpq' => session()->get('IdTpq'),
            'tbl_santri_baru.IdKelas' => $IdKelas,
            'tbl_santri_baru.Active' => 1
        ])->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')->findAll();

        return $this->response->setJSON($santriList);
    }

    public function previewRapor($IdSantri, $semester)
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();

        // Ambil data santri
        $santri = $this->santriBaruModel->getDetailSantri($IdSantri);

        // Ambil data nilai berdasarkan semester
        $nilai = $this->nilaiModel->getDataNilaiPerSantri(
            IdTpq: $IdTpq,
            IdTahunAjaran: $IdTahunAjaran,
            IdKelas: $santri['IdKelas'],
            IdSantri: $IdSantri,
            semester: $semester
        );

        // Ambil data TPQ
        $tpq = $this->helpFunctionModel->getNamaTpqById($IdTpq);

        $data = [
            'santri' => $santri,
            'nilai' => $nilai,
            'tpq' => $tpq,
            'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
            'semester' => $semester
        ];

        return view('backend/rapor/preview', $data);
    }

    public function printPdf($IdSantri, $semester)
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();

        // Ambil data santri
        $santri = $this->santriBaruModel->getDetailSantri($IdSantri);

        // Ambil data nilai berdasarkan semester
        $nilai = $this->nilaiModel->getDataNilaiPerSantri(
            IdTpq: $IdTpq,
            IdTahunAjaran: $IdTahunAjaran,
            IdKelas: $santri['IdKelas'],
            IdSantri: $IdSantri,
            semester: $semester
        );

        // Ambil data TPQ
        $tpq = $this->helpFunctionModel->getNamaTpqById($IdTpq);

        $data = [
            'santri' => $santri,
            'nilai' => $nilai,
            'tpq' => $tpq,
            'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
            'semester' => $semester
        ];

        // Load view untuk PDF
        $html = view('backend/rapor/print', $data);

        // Inisialisasi Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF
        $dompdf->stream('rapor_' . $santri['NamaSantri'] . '_' . $semester . '.pdf', ['Attachment' => false]);
    }
}
