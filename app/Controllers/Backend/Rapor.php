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
        ini_set('memory_limit', '256M');
        set_time_limit(300);
        mb_internal_encoding('UTF-8');
    }

    public function index($semester = 'Ganjil')
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdKelas = session()->get('IdKelas');
        $IdGuru = session()->get('IdGuru');
        $lisKelas = $this->helpFunctionModel->getListKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

        // Ambil data santri joint dengan tbl_kelas_santri dan tbl_kelas
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

    public function printPdf($IdSantri, $semester)
    {
        try {
            // Set memory limit dan timeout
            ini_set('memory_limit', '256M');
            set_time_limit(300);
            mb_internal_encoding('UTF-8');

            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');

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

            // Ambil Nama Wali Kelas dari IdKelas data nilai ke tbl_guru_kelas
            if (!empty($santri)) {
                $IdKelas = $santri['IdKelas'];
                $waliKelas = $this->helpFunctionModel->getWaliKelasByIdKelas(IdKelas: $IdKelas, IdTpq: $IdTpq, IdTahunAjaran: $IdTahunAjaran);
                $santri['WaliKelas'] = $waliKelas->Nama;
            } else {
                $santri['WaliKelas'] = 'Tidak ada data nilai';
            }

            // Ambil data TPQ
            $tpq = $this->helpFunctionModel->getNamaTpqById($IdTpq);

            $data = [
                'santri' => $santri,
                'nilai' => $nilai,
                'tpq' => $tpq,
                'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
                'semester' => $semester,
                'tanggal' => formatTanggalIndonesia(date('Y-m-d'), 'd F Y')
            ];

            // Load view untuk PDF
            $html = view('backend/rapor/print', $data);

            // Inisialisasi Dompdf dengan konfigurasi minimal
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF dengan cara yang lebih sederhana
            $filename = 'rapor_' . str_replace(' ', '_', $santri['NamaSantri']) . '_' . $semester . '.pdf';

            // Hapus semua output sebelumnya
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set header
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Rapor: printPdf - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function printPdfBulk($IdKelas, $semester)
    {
        try {
            // Set memory limit dan timeout
            ini_set('memory_limit', '512M');
            set_time_limit(600);
            mb_internal_encoding('UTF-8');

            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();

            // Ambil semua santri dalam kelas tersebut
            $listSantri = $this->santriBaruModel->where([
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'Active' => 1
            ])->findAll();

            // Ambil data TPQ
            $tpq = $this->helpFunctionModel->getNamaTpqById($IdTpq);

            // Inisialisasi Dompdf dengan konfigurasi
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($options);

            // Gabungkan semua HTML
            $combinedHtml = '';
            foreach ($listSantri as $index => $santri) {
                // Ambil data nilai
                $nilai = $this->nilaiModel->getDataNilaiPerSantri(
                    IdTpq: $IdTpq,
                    IdTahunAjaran: $IdTahunAjaran,
                    IdKelas: $santri['IdKelas'],
                    IdSantri: $santri['IdSantri'],
                    semester: $semester
                );

                // Ambil Wali Kelas
                $waliKelas = $this->helpFunctionModel->getWaliKelasByIdKelas(
                    IdKelas: $santri['IdKelas'],
                    IdTpq: $IdTpq,
                    IdTahunAjaran: $IdTahunAjaran
                );
                $santri['WaliKelas'] = $waliKelas->Nama;

                $data = [
                    'santri' => $santri,
                    'nilai' => $nilai,
                    'tpq' => $tpq,
                    'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
                    'semester' => $semester,
                    'tanggal' => formatTanggalIndonesia(date('Y-m-d'), 'd F Y')
                ];

                // Load view untuk setiap santri
                $html = view('backend/rapor/print', $data);

                // Tambahkan margin bottom jika bukan santri terakhir
                if ($index < count($listSantri) - 1) {
                    $html = '<div style="margin-bottom: 30px;">' . $html . '</div>';
                }

                $combinedHtml .= $html;
            }

            // Load HTML gabungan ke Dompdf
            $dompdf->loadHtml($combinedHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = 'rapor_kelas_' . $IdKelas . '_' . $semester . '.pdf';

            // Hapus semua output sebelumnya
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set header
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Rapor: printPdfBulk - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}
