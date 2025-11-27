<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\NilaiModel;
use App\Models\SantriBaruModel;
use App\Models\SignatureModel;
use App\Models\QrCodeModel;
use App\Models\MdaModel;
use App\Models\TpqModel;
use App\Models\ToolsModel;
use App\Models\KriteriaCatatanRaporModel;
use App\Models\RaportSettingModel;
use App\Models\AbsensiModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Rapor extends BaseController
{
    protected $helpFunctionModel;
    protected $nilaiModel;
    protected $santriBaruModel;
    protected $signatureModel;
    protected $qrCodeModel;
    protected $mdaModel;
    protected $tpqModel;
    protected $toolsModel;
    protected $kriteriaCatatanRaporModel;
    protected $raportSettingModel;
    protected $absensiModel;
    protected $rombelWalikelasModel;

    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->nilaiModel = new NilaiModel();
        $this->santriBaruModel = new SantriBaruModel();
        $this->signatureModel = new SignatureModel();
        $this->qrCodeModel = new QrCodeModel();
        $this->mdaModel = new MdaModel();
        $this->tpqModel = new TpqModel();
        $this->toolsModel = new ToolsModel();
        $this->kriteriaCatatanRaporModel = new KriteriaCatatanRaporModel();
        $this->raportSettingModel = new RaportSettingModel();
        $this->absensiModel = new AbsensiModel();
        $this->rombelWalikelasModel = new \App\Models\RombelWalikelasModel();
        ini_set('memory_limit', '256M');
        set_time_limit(300);
        mb_internal_encoding('UTF-8');
    }

    /**
     * Setup Dompdf configuration dengan pengaturan optimal untuk karakter Arab dan performa
     */
    private function setupDompdfConfig()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', false); // Disable remote untuk performa
        // Gunakan font yang mendukung huruf Arab dengan baik
        $options->set('defaultFont', 'Arial Unicode MS');
        // Aktifkan subsetting font untuk dukungan karakter luas
        $options->set('isFontSubsettingEnabled', true);
        // Aktifkan dukungan Unicode untuk karakter Arab
        $options->set('defaultMediaType', 'print');
        $options->set('isJavascriptEnabled', false);
        // Optimasi untuk performa
        $options->set('isFontSubsettingEnabled', true);
        $options->set('debugKeepTemp', false);
        $options->set('debugCss', false);
        $options->set('debugLayout', false);
        $options->set('debugLayoutLines', false);
        $options->set('debugLayoutBlocks', false);
        $options->set('debugLayoutInline', false);
        $options->set('debugLayoutPaddingBox', false);

        return new Dompdf($options);
    }

    /**
     * Output PDF dengan headers yang sesuai
     */
    private function outputPdf($dompdf, $filename)
    {
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
    }

    /**
     * Ambil data santri lengkap dengan nilai dan wali kelas
     */
    private function getSantriDataWithNilai($IdSantri, $IdTpq, $IdTahunAjaran, $semester)
    {
        // Ambil data santri dari tbl_kelas_santri join tbl_santri_baru
        $santri = $this->helpFunctionModel->getDetailSantriByKelasSantri(
            $IdSantri,
            $IdTahunAjaran,
            $IdTpq
        );

        if (empty($santri)) {
            return null;
        }

        // Ambil data nilai berdasarkan semester
        $nilai = $this->nilaiModel->getDataNilaiPerSantri(
            IdTpq: $IdTpq,
            IdTahunAjaran: $IdTahunAjaran,
            IdKelas: $santri['IdKelas'],
            IdSantri: $IdSantri,
            semester: $semester
        );

        // Simpan nama kelas asli sebelum dikonversi untuk digunakan di prepareRaporData
        $namaKelasOriginal = '';
        if (!empty($nilai) && isset($nilai[0]->NamaKelas)) {
            $namaKelasOriginal = $nilai[0]->NamaKelas;
        } elseif (isset($santri['NamaKelas'])) {
            $namaKelasOriginal = $santri['NamaKelas'];
        }

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        if (!empty($nilai) && isset($nilai[0]->NamaKelas)) {
            // Check MDA mapping dan convert nama kelas jika sesuai
            $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
            $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );

            // Update nama kelas di semua data nilai
            foreach ($nilai as $nilaiItem) {
                $nilaiItem->NamaKelas = $namaKelasDisplay;
            }
        }

        // Ambil Nama Wali Kelas dengan mapping dari tbl_rombel
        $IdKelas = $santri['IdKelas'];

        // Cek setting MappingWaliKelas dari tbl_tools
        $mappingEnabled = $this->toolsModel->getSetting($IdTpq, 'MappingWaliKelas');

        if ($mappingEnabled) {
            // Cek apakah ada mapping di tbl_rombel
            $mapping = $this->rombelWalikelasModel->getMappingBySantri(
                $santri['IdSantri'],
                $IdTahunAjaran,
                $IdKelas,
                $IdTpq
            );

            // Convert mapping ke array jika object
            $mappingArray = is_object($mapping) ? (array)$mapping : $mapping;

            if ($mapping && !empty($mappingArray['IdGuru'])) {
                // Ambil nama guru dari mapping
                $guru = $this->helpFunctionModel->getDataGuruKelas(
                    IdGuru: $mappingArray['IdGuru'],
                    IdTpq: $IdTpq,
                    IdKelas: $IdKelas,
                    IdTahunAjaran: $IdTahunAjaran
                );
                // getDataGuruKelas mengembalikan array of objects
                if (!empty($guru) && isset($guru[0]) && isset($guru[0]->Nama)) {
                    $santri['WaliKelas'] = $guru[0]->Nama;
                } else {
                    // Fallback ke wali kelas asli
                    $waliKelas = $this->helpFunctionModel->getWaliKelasByIdKelas(
                        IdKelas: $IdKelas,
                        IdTpq: $IdTpq,
                        IdTahunAjaran: $IdTahunAjaran
                    );
                    $santri['WaliKelas'] = $waliKelas ? $waliKelas->Nama : '';
                }
            } else {
                // Tidak ada mapping, gunakan wali kelas asli
                $waliKelas = $this->helpFunctionModel->getWaliKelasByIdKelas(
                    IdKelas: $IdKelas,
                    IdTpq: $IdTpq,
                    IdTahunAjaran: $IdTahunAjaran
                );
                $santri['WaliKelas'] = $waliKelas ? $waliKelas->Nama : '';
            }
        } else {
            // Setting tidak aktif, gunakan wali kelas asli
            $waliKelas = $this->helpFunctionModel->getWaliKelasByIdKelas(
                IdKelas: $IdKelas,
                IdTpq: $IdTpq,
                IdTahunAjaran: $IdTahunAjaran
            );
            $santri['WaliKelas'] = $waliKelas ? $waliKelas->Nama : '';
        }

        // Ambil guru pendamping dari tbl_guru_kelas
        $guruPendamping = $this->helpFunctionModel->getGuruPendampingByIdKelas($IdKelas, $IdTpq, $IdTahunAjaran);
        $santri['GuruPendamping'] = !empty($guruPendamping) ? $guruPendamping : [];

        // Simpan nama kelas asli di data santri untuk digunakan di prepareRaporData
        $santri['NamaKelasOriginal'] = $namaKelasOriginal;

        return [
            'santri' => $santri,
            'nilai' => $nilai
        ];
    }

    /**
     * Siapkan data untuk view rapor
     */
    private function prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester)
    {
        // Ambil data TPQ
        $tpqRow = $this->helpFunctionModel->getNamaTpqById($IdTpq);
        if (empty($tpqRow) || !is_array($tpqRow)) {
            $tpqRow = [];
        }

        // Check status MDA dan mapping kelas
        // Ambil nama kelas asli langsung dari database berdasarkan IdKelas untuk memastikan menggunakan nama asli
        $namaKelasSantri = '';
        if (isset($santriData['santri']['IdKelas']) && !empty($santriData['santri']['IdKelas'])) {
            // Ambil nama kelas asli langsung dari database
            $kelasData = $this->helpFunctionModel->getNamaKelasBulk([$santriData['santri']['IdKelas']]);
            if (!empty($kelasData) && isset($kelasData[$santriData['santri']['IdKelas']])) {
                $namaKelasSantri = $kelasData[$santriData['santri']['IdKelas']];
            }
        }

        // Fallback: gunakan nama kelas asli yang sudah disimpan atau dari data santri
        if (empty($namaKelasSantri)) {
            if (isset($santriData['santri']['NamaKelasOriginal']) && !empty($santriData['santri']['NamaKelasOriginal'])) {
                $namaKelasSantri = $santriData['santri']['NamaKelasOriginal'];
            } elseif (isset($santriData['santri']['NamaKelas'])) {
                $namaKelasSantri = $santriData['santri']['NamaKelas'];
            }
        }

        log_message('info', 'Rapor: prepareRaporData - Nama kelas asli untuk check mapping: ' . $namaKelasSantri);

        // Gunakan helper function untuk check MDA mapping
        $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($IdTpq, $namaKelasSantri);
        $useMdaData = $mdaCheckResult['useMdaData'];
        $mappedMdaKelas = $mdaCheckResult['mappedMdaKelas'];
        $mdaRow = null;

        log_message('info', 'Rapor: prepareRaporData - Check MDA mapping untuk kelas: ' . $namaKelasSantri . ', useMdaData: ' . ($useMdaData ? 'true' : 'false') . ', mappedMdaKelas: ' . ($mappedMdaKelas ?? 'null'));

        // Jika sesuai, ambil data MDA
        if ($useMdaData) {
            $mdaData = $this->mdaModel->GetData($IdTpq);
            if (!empty($mdaData) && !empty($mdaData[0])) {
                $mdaRow = $mdaData[0];
                log_message('info', 'Rapor: prepareRaporData - Menggunakan data MDA untuk kelas ' . $namaKelasSantri . ', KopLembaga: ' . ($mdaRow['KopLembaga'] ?? 'kosong'));
            } else {
                // Jika data MDA tidak ditemukan, fallback ke TPQ
                $useMdaData = false;
                log_message('warning', 'Rapor: prepareRaporData - Data MDA tidak ditemukan, menggunakan data TPQ');
            }
        } else {
            log_message('info', 'Rapor: prepareRaporData - Kelas tidak sesuai mapping MDA, menggunakan data TPQ');
        }

        // Tentukan data yang akan digunakan (MDA atau TPQ)
        $lembagaType = $useMdaData && $mdaRow ? 'MDA' : 'TPQ';

        // Untuk kop lembaga, gunakan data MDA jika sesuai, fallback ke TPQ
        $kopLembaga = $useMdaData && $mdaRow ? ($mdaRow['KopLembaga'] ?? $tpqRow['KopLembaga'] ?? '') : ($tpqRow['KopLembaga'] ?? '');
        $kepalaSekolah = $useMdaData && $mdaRow ? ($mdaRow['KepalaSekolah'] ?? $tpqRow['KepalaSekolah'] ?? '') : ($tpqRow['KepalaSekolah'] ?? '');
        $namaLembaga = $useMdaData && $mdaRow ? ($mdaRow['NamaTpq'] ?? $tpqRow['NamaTpq'] ?? '') : ($tpqRow['NamaTpq'] ?? '');

        // Siapkan data TPQ untuk view (dengan kop lembaga yang sudah disesuaikan)
        $tpq = $tpqRow;
        $tpq['KopLembaga'] = $kopLembaga;
        $tpq['KepalaSekolah'] = $kepalaSekolah;
        $tpq['NamaTpq'] = $namaLembaga;

        // Ambil data signature untuk santri ini dengan informasi posisi guru
        $signatures = $this->signatureModel->getSignaturesWithPosition(
            idSantri: $santriData['santri']['IdSantri'],
            idTahunAjaran: $IdTahunAjaran,
            semester: $semester
        );

        // Hitung rata-rata nilai untuk generate catatan raport
        $nilaiRataRata = $this->hitungRataRataNilai($santriData['nilai']);

        // Ambil IdKelas dari data santri (pastikan ada dan valid)
        $idKelas = null;
        if (isset($santriData['santri']['IdKelas']) && !empty($santriData['santri']['IdKelas'])) {
            $idKelas = $santriData['santri']['IdKelas'];
        }

        // Log untuk debugging
        log_message('debug', 'Rapor: prepareRaporData - idKelas dari santri: ' . ($idKelas ?? 'null'));

        // Generate catatan raport berdasarkan nilai rata-rata
        $catatanRaport = $this->generateKriteriaCatatanRapor($nilaiRataRata, $IdTahunAjaran, $IdTpq, $idKelas);

        // Ambil data setting rapor (catatan dan absensi)
        $raportSetting = $this->raportSettingModel->getDataBySantri(
            $santriData['santri']['IdSantri'],
            $IdTahunAjaran,
            $semester
        );

        return [
            'santri' => $santriData['santri'],
            'nilai' => $santriData['nilai'],
            'tpq' => $tpq,
            'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
            'semester' => $semester,
            'tanggal' => formatTanggalIndonesia(date('Y-m-d'), 'd F Y'),
            'signatures' => $signatures,
            'lembagaType' => $lembagaType,
            'nilaiRataRata' => $nilaiRataRata,
            'catatanRaport' => $catatanRaport,
            'raportSetting' => $raportSetting
        ];
    }

    /**
     * Ambil data summary nilai untuk setiap santri
     */
    private function getSummaryDataForSantri($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        // Ambil data summary menggunakan method yang sama seperti showSumaryPersemester
        $summaryData = $this->nilaiModel->getDataNilaiPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Buat array dataKelas untuk struktur yang sama dengan nilaiSantriPerSemester
        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        $dataKelas = [];
        foreach ($summaryData as $nilai) {
            $namaKelasOriginal = $nilai->NamaKelas;

            // Check MDA mapping dan convert nama kelas jika sesuai
            $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
            $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );

            // Simpan nama kelas yang sudah dikonversi
            if (!isset($dataKelas[$nilai->IdKelas])) {
                $dataKelas[$nilai->IdKelas] = $namaKelasDisplay;
            }

            // Update nama kelas di data summary untuk ditampilkan di tabel
            $nilai->NamaKelas = $namaKelasDisplay;
        }

        return [
            'nilai' => $summaryData,
            'dataKelas' => $dataKelas
        ];
    }

    public function index($semester = 'Ganjil')
    {
        // Debug: Log jika method index dipanggil dengan parameter yang tidak diharapkan
        if ($semester !== 'Ganjil' && $semester !== 'Genap' && $semester !== 'kriteriaCatatanRapor') {
            log_message('debug', 'Rapor::index called with semester: ' . $semester);
        }

        // Jika parameter adalah kriteriaCatatanRapor, redirect ke method yang benar
        if ($semester === 'kriteriaCatatanRapor') {
            return $this->kriteriaCatatanRapor();
        }

        // Ambil data permission guru kelas untuk semua kelas
        $IdTpq = session()->get('IdTpq');
        $IdGuru = session()->get('IdGuru');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Cek apakah user adalah Operator
        $isOperator = in_groups('Operator');

        // Cek apakah user adalah Kepala Sekolah
        $jabatanData = $this->helpFunctionModel->getStrukturLembagaJabatan($IdGuru, $IdTpq);
        $isKepalaSekolah = false;
        if (!empty($jabatanData)) {
            foreach ($jabatanData as $jabatan) {
                if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                    $isKepalaSekolah = true;
                    break;
                }
            }
        }

        // Ambil list id kelas dari tbl_kelas_santri
        // Operator dan Kepala Sekolah memiliki akses ke semua kelas
        if ($isKepalaSekolah || $isOperator) {
            $listIdKelas = $this->helpFunctionModel->getListIdKelasFromKelasSantri($IdTpq, $IdTahunAjaran);
        } else {
            $listIdKelas = session()->get('IdKelas');
            // Jika tidak ada kelas di session, ambil semua kelas dari TPQ
            if (empty($listIdKelas)) {
                $listIdKelas = $this->helpFunctionModel->getListIdKelasFromKelasSantri($IdTpq, $IdTahunAjaran);
            }
        }

        // Pastikan listIdKelas adalah array (bisa jadi null atau bukan array)
        if (empty($listIdKelas) || !is_array($listIdKelas)) {
            $listIdKelas = [];
        }

        // Untuk Operator, set IdGuru menjadi null agar getListKelas menggunakan mode admin/kepala sekolah
        $guruIdForKelas = ($isOperator && empty($IdGuru)) ? null : $IdGuru;

        // Ambil object data kelas - kirim flag isOperator agar diperlakukan seperti kepala sekolah
        $dataKelas = $this->helpFunctionModel->getListKelas($IdTpq, $IdTahunAjaran, $listIdKelas, $guruIdForKelas, $isOperator);

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        foreach ($dataKelas as $kelas) {
            $namaKelasOriginal = $kelas->NamaKelas;

            // Check MDA mapping dan convert nama kelas jika sesuai
            $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
            $kelas->NamaKelas = $this->helpFunctionModel->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );
        }

        // Ambil data summary nilai untuk setiap santri
        $summaryData = $this->getSummaryDataForSantri($IdTpq, $listIdKelas, $IdTahunAjaran, $semester);

        // Ambil data raport setting untuk semua santri sekaligus (batch query)
        $raportSettingsMap = [];
        if (!empty($summaryData['nilai'])) {
            // Ekstrak semua IdSantri dari data nilai
            $santriIds = [];
            foreach ($summaryData['nilai'] as $nilai) {
                if (!empty($nilai->IdSantri) && !in_array($nilai->IdSantri, $santriIds)) {
                    $santriIds[] = $nilai->IdSantri;
                }
            }

            // Batch query untuk semua santri
            if (!empty($santriIds)) {
                $raportSettingsData = $this->raportSettingModel->getDataBySantriBatch(
                    $santriIds,
                    $IdTahunAjaran,
                    $semester
                );

                // Buat mapping dengan key: IdSantri_Semester untuk akses cepat
                foreach ($raportSettingsData as $key => $setting) {
                    $raportSettingsMap[$key] = [
                        'ShowAbsensi' => $setting['ShowAbsensi'] ?? 0,
                        'ShowCatatan' => $setting['ShowCatatan'] ?? 0,
                        'AbsensiData' => $setting['AbsensiData'] ?? [],
                        'CatatanData' => $setting['CatatanData'] ?? []
                    ];
                }
            }
        }

        // Ambil data permission guru kelas untuk semua kelas
        // Untuk Operator tanpa IdGuru, kembalikan array kosong karena tidak perlu permission signature
        if ($isOperator && empty($IdGuru)) {
            $guruKelasPermissions = [];
        } else {
            $guruKelasPermissions = $this->helpFunctionModel->getGuruKelasPermissions($IdTpq, $IdGuru, $listIdKelas, $IdTahunAjaran);
        }


        // Ambil status signature untuk semua santri dalam kelas ini
        $signatures = $this->signatureModel->getSignaturesWithPosition(
            idKelas: $listIdKelas,
            idTpq: $IdTpq,
            idTahunAjaran: $IdTahunAjaran,
            semester: $semester
        );

        // Buat mapping signature status per santri dan guru
        $signatureStatus = [];
        foreach ($signatures as $signature) {
            $key = $signature['IdSantri'] . '_' . $signature['IdGuru'];
            if (!isset($signatureStatus[$key])) {
                $signatureStatus[$key] = [];
            }
            $signatureStatus[$key][] = $signature;
        }

        $data = [
            'page_title' => 'Rapor Santri',
            'dataKelas' => $dataKelas,
            'nilai' => $summaryData['nilai'],
            'semester' => $semester,
            'guruKelasPermissions' => $guruKelasPermissions,
            'signatures' => $signatures,
            'signatureStatus' => $signatureStatus,
            'currentGuruId' => $IdGuru,
            'raportSettingsMap' => $raportSettingsMap
        ];

        return view('backend/rapor/index', $data);
    }

    public function getSantriByKelas($IdKelas)
    {
        $IdTpq = session()->get('IdTpq');
        $santriList = $this->santriBaruModel->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')->where([
            'tbl_santri_baru.IdTpq' => $IdTpq,
            'tbl_santri_baru.IdKelas' => $IdKelas,
            'tbl_santri_baru.Active' => 1
        ])->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')->findAll();

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        foreach ($santriList as $santri) {
            $namaKelasOriginal = $santri->NamaKelas;

            // Check MDA mapping dan convert nama kelas jika sesuai
            $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
            $santri->NamaKelas = $this->helpFunctionModel->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );
        }

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

            // Ambil data santri lengkap dengan nilai dan wali kelas
            $santriData = $this->getSantriDataWithNilai($IdSantri, $IdTpq, $IdTahunAjaran, $semester);

            if (!$santriData) {
                throw new \Exception('Data santri tidak ditemukan');
            }

            // Siapkan data untuk view rapor
            $data = $this->prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester);

            // Load view untuk PDF
            $html = view('backend/rapor/print', $data);

            // Setup Dompdf dan render PDF
            $dompdf = $this->setupDompdfConfig();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = str_replace(' ', '_', $santriData['santri']['NamaSantri']) . '_' . $IdTahunAjaran . '_' . $semester . '.pdf';
            $this->outputPdf($dompdf, $filename);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: printPdf - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function printPdfBulk($IdKelas, $semester)
    {
        try {
            // Set memory limit dan timeout untuk bulk processing
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

            if (empty($listSantri)) {
                throw new \Exception('Tidak ada santri dalam kelas ini');
            }

            // Setup Dompdf
            $dompdf = $this->setupDompdfConfig();

            // Gabungkan semua HTML
            $combinedHtml = '';
            foreach ($listSantri as $index => $santri) {
                // Ambil data santri lengkap dengan nilai dan wali kelas
                $santriData = $this->getSantriDataWithNilai($santri['IdSantri'], $IdTpq, $IdTahunAjaran, $semester);

                if (!$santriData) {
                    continue; // Skip jika data santri tidak ditemukan
                }

                // Siapkan data untuk view rapor
                $data = $this->prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester);

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
            $this->outputPdf($dompdf, $filename);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: printPdfBulk - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Handle tanda tangan wali kelas
     */
    public function ttdWalas($IdSantri, $IdKelas, $semester)
    {
        return $this->handleSignature($IdSantri, $IdKelas, $semester, 'walas');
    }

    /**
     * Handle tanda tangan kepala sekolah
     */
    public function ttdKepsek($IdSantri, $IdKelas, $semester)
    {
        return $this->handleSignature($IdSantri, $IdKelas, $semester, 'kepsek');
    }

    /**
     * Generate token unik untuk tanda tangan
     */
    private function generateUniqueToken()
    {
        do {
            $token = base64_encode(random_bytes(24));
            $token = str_replace(['+', '/', '='], ['-', '_', ''], $token); // URL-safe

        } while ($this->signatureModel->where('Token', $token)->first());

        return $token;
    }

    /**
     * Hapus file QR code
     */
    private function deleteQRCodeFile($qrCodeFilename)
    {
        if (!empty($qrCodeFilename)) {
            $qrFilePath = FCPATH . 'uploads/qr/' . $qrCodeFilename;
            if (file_exists($qrFilePath)) {
                return unlink($qrFilePath);
            }
        }
        return false;
    }

    /**
     * Handle tanda tangan (untuk wali kelas dan kepala sekolah)
     */
    private function handleSignature($IdSantri, $IdKelas, $semester, $signatureType)
    {
        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');

            // Cek permission berdasarkan tbl_guru_kelas
            $guruKelasPermission = $this->helpFunctionModel->checkGuruKelasPermission($IdTpq, $IdGuru, $IdKelas, $IdTahunAjaran);

            if (!$guruKelasPermission) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk kelas ini pada tahun ajaran ini.'
                ]);
            }

            // Cek permission berdasarkan jabatan dari tbl_guru_kelas
            if ($signatureType === 'walas' && $guruKelasPermission['NamaJabatan'] !== 'Wali Kelas') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk menandatangani sebagai wali kelas.'
                ]);
            }

            if ($signatureType === 'kepsek' && $guruKelasPermission['NamaJabatan'] !== 'Kepala TPQ') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk menandatangani sebagai kepala sekolah.'
                ]);
            }

            // Cek apakah signature sudah ada
            $existingSignature = $this->signatureModel->where([
                'IdSantri' => $IdSantri,
                'IdTpq' => $IdTpq,
                'IdTahunAjaran' => $IdTahunAjaran,
                'IdGuru' => $IdGuru,
                'Semester' => $semester,
                'JenisDokumen' => 'Rapor',
                'Status' => 'active'
            ])->first();

            // Jika ada request untuk replace dan signature sudah ada
            if ($this->request->getPost('replace') && $existingSignature) {
                // Hapus file QR code lama jika ada
                $this->deleteQRCodeFile($existingSignature['QrCode']);
                // Hapus signature lama
                $this->signatureModel->delete($existingSignature['Id']);
            } elseif ($existingSignature && !$this->request->getPost('replace')) {
                $typeName = $signatureType === 'walas' ? 'wali kelas' : 'kepala sekolah';
                return $this->response->setJSON([
                    'status' => 'info',
                    'message' => "Tanda tangan {$typeName} untuk santri ini sudah pernah dibuat pada " . date('d F Y H:i', strtotime($existingSignature['TanggalTtd'])) . '. Apakah Anda ingin menggantinya?',
                    'existing_signature' => true,
                    'existing_id' => $existingSignature['Id']
                ]);
            }

            $kelasData = $this->helpFunctionModel->getIdKelasByTahunAjaranDanSemester($IdTpq, $IdTahunAjaran, $semester, $IdSantri);

            // Generate token unik
            $token = $this->generateUniqueToken();

            // Data untuk signature
            $signatureData = $signatureType === 'walas' ? 'Walas' : 'Kepsek';

            // Data untuk disimpan ke tbl_tanda_tangan
            $signatureData = [
                'Token' => $token,
                'IdSantri' => $IdSantri,
                'IdKelas' => !empty($kelasData) ? $kelasData[0]['IdKelas'] : null,
                'IdTahunAjaran' => $IdTahunAjaran,
                'Semester' => $semester,
                'IdGuru' => $IdGuru,
                'IdTpq' => $IdTpq,
                'JenisDokumen' => 'Rapor',
                'SignatureData' => $signatureData,
                'StatusValidasi' => 'Valid',
                'TanggalTtd' => date('Y-m-d H:i:s')
            ];

            // Simpan data tanda tangan
            $IdSignature = $this->signatureModel->insert($signatureData);

            if ($IdSignature) {
                // Generate QR Code
                $qrCodeData = $this->generateQRCode($token);

                if ($qrCodeData) {
                    // Update data tanda tangan dengan nama file QR
                    $this->signatureModel->where('Id', $IdSignature)
                        ->set(['QrCode' => $qrCodeData['filename']])
                        ->update();

                    $typeName = $signatureType === 'walas' ? 'wali kelas' : 'kepala sekolah';
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => "Tanda tangan {$typeName} berhasil disimpan dan QR Code telah dibuat."
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'warning',
                        'message' => 'Tanda tangan berhasil disimpan, namun gagal membuat QR Code.'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan tanda tangan.'
                ]);
            }
        } catch (\Exception $e) {
            $typeName = $signatureType === 'walas' ? 'wali kelas' : 'kepala sekolah';
            log_message('error', "Rapor: ttd{$typeName} - Error: " . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate QR Code untuk validasi tanda tangan
     */
    private function generateQRCode($token)
    {
        try {
            // URL untuk validasi tanda tangan
            $validationUrl = base_url("signature/validateSignature/{$token}");

            // Buat direktori jika belum ada
            if (!is_dir(FCPATH . 'uploads/qr')) {
                mkdir(FCPATH . 'uploads/qr', 0777, true);
            }

            // Generate QR Code
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                'scale' => 300,
                'imageBase64' => false,
                'addQuietzone' => true,
                'quietzoneSize' => 4,
            ]);

            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrString = $qrcode->render($validationUrl);

            // Simpan QR code sebagai file SVG
            $filename = 'signature_' . $token . '.svg';
            file_put_contents(FCPATH . 'uploads/qr/' . $filename, $qrString);

            return [
                'filename' => $filename,
                'url' => $validationUrl
            ];
        } catch (\Exception $e) {
            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hitung rata-rata nilai dari data nilai santri
     * 
     * @param array $nilaiData Array of nilai objects
     * @return float
     */
    private function hitungRataRataNilai($nilaiData)
    {
        if (empty($nilaiData)) {
            return 0;
        }

        $total = 0;
        $count = 0;

        foreach ($nilaiData as $nilai) {
            $nilaiValue = is_object($nilai) ? $nilai->Nilai : (is_array($nilai) ? $nilai['Nilai'] : 0);
            if ($nilaiValue > 0) {
                $total += floatval($nilaiValue);
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 2) : 0;
    }

    /**
     * Generate catatan raport berdasarkan nilai rata-rata
     * 
     * @param float $nilaiRataRata
     * @param string|null $idTahunAjaran
     * @param int|null $idTpq
     * @param string|null $idKelas
     * @return string
     */
    private function generateKriteriaCatatanRapor($nilaiRataRata, $idTahunAjaran = null, $idTpq = null, $idKelas = null)
    {
        // Konversi idTpq ke string jika null atau 0, gunakan 'default'
        $idTpqString = (!empty($idTpq) && $idTpq != 0) ? (string)$idTpq : 'default';

        // Konversi idKelas ke string jika ada (pastikan tidak kosong dan bukan 0)
        $idKelasString = null;
        if (!empty($idKelas) && $idKelas != 0 && $idKelas != '0') {
            $idKelasString = (string)$idKelas;
        }

        // Log untuk debugging
        log_message('debug', 'Rapor: generateKriteriaCatatanRapor - nilaiRataRata: ' . $nilaiRataRata . ', idTahunAjaran: ' . ($idTahunAjaran ?? 'null') . ', idTpq: ' . $idTpqString . ', idKelas: ' . ($idKelasString ?? 'null'));

        // Ambil catatan dari model
        $catatan = $this->kriteriaCatatanRaporModel->getCatatanByNilaiRataRata($nilaiRataRata, $idTahunAjaran, $idTpqString, $idKelasString);

        log_message('debug', 'Rapor: generateKriteriaCatatanRapor - catatan found: ' . ($catatan ? 'yes' : 'no'));

        return $catatan ? $catatan['Catatan'] : '';
    }

    // ==================== MANAJEMEN KRITERIA CATATAN RAPOR ====================

    /**
     * Halaman manajemen kriteria catatan rapor
     */
    public function kriteriaCatatanRapor()
    {
        // Debug: Pastikan method ini dipanggil
        log_message('debug', 'kriteriaCatatanRapor method called');

        // Ambil tahun ajaran dari session, jika tidak ada gunakan tahun ajaran saat ini
        $currentTahunAjaran = session()->get('IdTahunAjaran') ?? $this->helpFunctionModel->getTahunAjaranSaatIni();
        $idTpq = session()->get('IdTpq') ?? 'default';
        $idKelas = $this->request->getGet('IdKelas') ?? null;
        $idGuru = session()->get('IdGuru');

        // Cek apakah user adalah Admin, Operator, atau Kepala Sekolah
        $isAdmin = in_groups('Admin');
        $isOperator = in_groups('Operator');

        // Cek apakah user adalah Kepala Sekolah (Kepala TPQ)
        $isKepalaSekolah = false;
        if (!empty($idGuru) && !empty($idTpq) && $idTpq !== 'default' && $idTpq !== '0') {
            $jabatanData = $this->helpFunctionModel->getStrukturLembagaJabatan($idGuru, $idTpq);
            if (!empty($jabatanData)) {
                foreach ($jabatanData as $jabatan) {
                    if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                        $isKepalaSekolah = true;
                        break;
                    }
                }
            }
        }

        // Jika user adalah Operator atau Kepala Sekolah (bukan Admin), pastikan IdTpq dari session
        $isRestrictedUser = ($isOperator || $isKepalaSekolah) && !$isAdmin;
        if ($isRestrictedUser) {
            // Pastikan IdTpq dari session digunakan (jika ada)
            $sessionIdTpq = session()->get('IdTpq');
            if (!empty($sessionIdTpq) && $sessionIdTpq !== 'default' && $sessionIdTpq !== '0') {
                $idTpq = $sessionIdTpq;
            }
        }

        // Cek apakah user adalah GuruKelas atau Wali Kelas
        $isGuruKelas = false;
        $guruKelasData = [];
        $userKelasList = [];

        if (!empty($idGuru) && !$isAdmin && !$isOperator && !$isKepalaSekolah) {
            // Ambil data guru kelas untuk user yang login
            $guruKelasData = $this->helpFunctionModel->getDataGuruKelas(
                IdGuru: $idGuru,
                IdTahunAjaran: $currentTahunAjaran
            );

            // Cek apakah user adalah GuruKelas atau Wali Kelas
            if (!empty($guruKelasData)) {
                foreach ($guruKelasData as $gk) {
                    // Convert object to array if needed
                    $gkArray = is_object($gk) ? (array)$gk : $gk;
                    $namaJabatan = $gkArray['NamaJabatan'] ?? '';
                    if ($namaJabatan === 'Guru Kelas' || $namaJabatan === 'Wali Kelas') {
                        $isGuruKelas = true;
                        // Kumpulkan IdTpq dan IdKelas yang dimiliki
                        if (!empty($gkArray['IdTpq']) && !empty($gkArray['IdKelas'])) {
                            $userKelasList[] = [
                                'IdTpq' => $gkArray['IdTpq'],
                                'IdKelas' => $gkArray['IdKelas'],
                                'NamaKelas' => $gkArray['NamaKelas'] ?? ''
                            ];
                        }
                    }
                }
            }

            // Jika user adalah GuruKelas/Wali Kelas, set IdTpq ke TPQ mereka
            if ($isGuruKelas && !empty($userKelasList)) {
                // Ambil IdTpq dari kelas pertama (biasanya guru hanya punya 1 TPQ)
                $idTpq = $userKelasList[0]['IdTpq'];
            }
        }

        // Flag untuk disable filter TPQ di view
        $shouldDisableTpqFilter = $isGuruKelas || $isRestrictedUser;

        // Get list TPQ untuk dropdown
        $listTpq = $this->helpFunctionModel->getDataTpq(false); // false = ambil semua TPQ

        // Get list tahun ajaran untuk dropdown (dari tbl_kelas_santri atau tbl_guru_kelas)
        $db = \Config\Database::connect();
        $tahunAjaranQuery = $db->query("
            SELECT DISTINCT IdTahunAjaran 
            FROM (
                SELECT IdTahunAjaran FROM tbl_kelas_santri
                UNION
                SELECT IdTahunAjaran FROM tbl_guru_kelas
            ) AS combined
            ORDER BY IdTahunAjaran DESC
        ");
        $listTahunAjaran = $tahunAjaranQuery->getResultArray();

        // Get list kelas untuk dropdown
        $listKelas = [];
        if (!empty($idTpq) && $idTpq !== 'default' && !empty($currentTahunAjaran)) {
            if ($isGuruKelas && !empty($userKelasList)) {
                // Jika user adalah GuruKelas/Wali Kelas, hanya tampilkan kelas yang mereka miliki
                foreach ($userKelasList as $userKelas) {
                    if ($userKelas['IdTpq'] == $idTpq) {
                        // Ambil NamaKelas dari database jika belum ada
                        $namaKelas = $userKelas['NamaKelas'] ?? '';
                        if (empty($namaKelas)) {
                            $kelasData = $db->table('tbl_kelas')
                                ->where('IdKelas', $userKelas['IdKelas'])
                                ->get()
                                ->getRowArray();
                            $namaKelas = $kelasData['NamaKelas'] ?? $userKelas['IdKelas'];
                        }

                        $listKelas[] = [
                            'IdKelas' => $userKelas['IdKelas'],
                            'NamaKelas' => $namaKelas
                        ];
                    }
                }

                // Jika IdKelas dari GET request tidak ada di list, set ke kelas pertama
                if (empty($idKelas) && !empty($listKelas)) {
                    $idKelas = $listKelas[0]['IdKelas'];
                }
            } else {
                // Untuk Admin/Operator/Kepala Sekolah, tampilkan semua kelas di TPQ mereka
                $kelasQuery = $db->table('tbl_guru_kelas gk')
                    ->select('gk.IdKelas, k.NamaKelas')
                    ->join('tbl_kelas k', 'k.IdKelas = gk.IdKelas')
                    ->where('gk.IdTpq', $idTpq)
                    ->where('gk.IdTahunAjaran', $currentTahunAjaran)
                    ->groupBy('gk.IdKelas, k.NamaKelas')
                    ->orderBy('gk.IdKelas', 'ASC');
                $listKelas = $kelasQuery->get()->getResultArray();
            }

            // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
            foreach ($listKelas as &$kelas) {
                $namaKelasOriginal = $kelas['NamaKelas'];

                // Check MDA mapping dan convert nama kelas jika sesuai
                $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($idTpq, $namaKelasOriginal);
                $kelas['NamaKelas'] = $this->helpFunctionModel->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );
            }
            unset($kelas); // Unset reference untuk menghindari side effects
        }

        // Ambil data catatan dan tambahkan nama kelas dengan mapping MDA
        $catatanList = $this->kriteriaCatatanRaporModel->getAllCatatanAktif($currentTahunAjaran, $idTpq, $idKelas);

        // Tambahkan nama kelas dengan mapping MDA untuk setiap catatan
        foreach ($catatanList as &$catatan) {
            if (!empty($catatan['IdKelas'])) {
                // Ambil nama kelas dari database
                $kelasData = $db->table('tbl_kelas')
                    ->where('IdKelas', $catatan['IdKelas'])
                    ->get()
                    ->getRowArray();

                if (!empty($kelasData)) {
                    $namaKelasOriginal = $kelasData['NamaKelas'];

                    // Gunakan IdTpq dari catatan untuk mapping MDA (bisa berbeda dari filter)
                    $catatanIdTpq = !empty($catatan['IdTpq']) ? $catatan['IdTpq'] : $idTpq;

                    // Check MDA mapping dan convert nama kelas jika sesuai
                    $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($catatanIdTpq, $namaKelasOriginal);
                    $catatan['NamaKelas'] = $this->helpFunctionModel->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                } else {
                    $catatan['NamaKelas'] = $catatan['IdKelas'];
                }
            } else {
                $catatan['NamaKelas'] = 'Semua';
            }
        }
        unset($catatan); // Unset reference untuk menghindari side effects

        $data = [
            'page_title' => 'Kriteria Catatan Raport',
            'currentTahunAjaran' => $currentTahunAjaran,
            'currentIdTpq' => $idTpq,
            'currentIdKelas' => $idKelas,
            'listTpq' => $listTpq,
            'listTahunAjaran' => $listTahunAjaran,
            'listKelas' => $listKelas,
            'catatanList' => $catatanList,
            'helpFunctionModel' => $this->helpFunctionModel,
            'isGuruKelas' => $isGuruKelas, // Flag untuk disable filter TPQ di view
            'isRestrictedUser' => $shouldDisableTpqFilter, // Flag untuk disable filter TPQ (GuruKelas, Operator, Kepala Sekolah)
            'isAdmin' => $isAdmin,
            'isOperator' => $isOperator,
            'isKepalaSekolah' => $isKepalaSekolah
        ];

        return view('backend/kriteriaCatatanRaport/index', $data);
    }

    /**
     * Ambil data kriteria catatan rapor via AJAX
     */
    public function getKriteriaCatatanRapor()
    {
        $currentTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran') ?? $currentTahunAjaran;
        $idTpq = $this->request->getPost('IdTpq') ?? session()->get('IdTpq') ?? 'default';
        $idKelas = $this->request->getPost('IdKelas') ?? null;

        $catatanList = $this->kriteriaCatatanRaporModel->getAllCatatanAktif($idTahunAjaran, $idTpq, $idKelas);

        return $this->response->setJSON([
            'success' => true,
            'data' => $catatanList
        ]);
    }

    /**
     * Simpan kriteria catatan rapor
     */
    public function saveKriteriaCatatanRapor()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $data = [
            'NilaiHuruf' => $this->request->getPost('NilaiHuruf'),
            'NilaiMin' => $this->request->getPost('NilaiMin'),
            'NilaiMax' => $this->request->getPost('NilaiMax'),
            'Catatan' => $this->request->getPost('Catatan'),
            'Status' => $this->request->getPost('Status') ?? 'Aktif',
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran') ?: null,
            'IdTpq' => $this->request->getPost('IdTpq') ?: 'default',
            'IdKelas' => $this->request->getPost('IdKelas') ?: null
        ];

        if (!$this->kriteriaCatatanRaporModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan kriteria catatan',
                'errors' => $this->kriteriaCatatanRaporModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Kriteria catatan berhasil disimpan',
            'data' => $this->kriteriaCatatanRaporModel->find($this->kriteriaCatatanRaporModel->getInsertID())
        ]);
    }

    /**
     * Update kriteria catatan rapor
     */
    public function updateKriteriaCatatanRapor($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $data = [
            'NilaiHuruf' => $this->request->getPost('NilaiHuruf'),
            'NilaiMin' => $this->request->getPost('NilaiMin'),
            'NilaiMax' => $this->request->getPost('NilaiMax'),
            'Catatan' => $this->request->getPost('Catatan'),
            'Status' => $this->request->getPost('Status') ?? 'Aktif',
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran') ?: null,
            'IdTpq' => $this->request->getPost('IdTpq') ?: 'default',
            'IdKelas' => $this->request->getPost('IdKelas') ?: null
        ];

        if (!$this->kriteriaCatatanRaporModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate kriteria catatan',
                'errors' => $this->kriteriaCatatanRaporModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Kriteria catatan berhasil diupdate',
            'data' => $this->kriteriaCatatanRaporModel->find($id)
        ]);
    }

    /**
     * Hapus kriteria catatan rapor
     */
    public function deleteKriteriaCatatanRapor($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        if (!$this->kriteriaCatatanRaporModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus kriteria catatan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Kriteria catatan berhasil dihapus'
        ]);
    }

    /**
     * Duplicate kriteria catatan rapor dari default ke individual
     */
    public function duplicateKriteriaCatatanRapor()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $sourceId = $this->request->getPost('source_id');
            $targetIdTpq = $this->request->getPost('IdTpq');
            $targetIdTahunAjaran = $this->request->getPost('IdTahunAjaran') ?: null;
            $targetIdKelas = $this->request->getPost('IdKelas') ?: null;
            $nilaiHuruf = $this->request->getPost('NilaiHuruf');
            $nilaiMin = $this->request->getPost('NilaiMin');
            $nilaiMax = $this->request->getPost('NilaiMax');
            $catatan = $this->request->getPost('Catatan');
            $status = $this->request->getPost('Status') ?? 'Aktif';

            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));

            // Validate input
            if (empty($sourceId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Source ID tidak boleh kosong'
                ]);
            }

            if (empty($targetIdTpq)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID TPQ tujuan harus diisi'
                ]);
            }

            // Prevent duplicating to 'default'
            if ($targetIdTpq === 'default') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat menduplikasi ke "default". Gunakan ID TPQ lain atau "0" untuk admin.'
                ]);
            }

            // Get source configuration
            $source = $this->kriteriaCatatanRaporModel->find($sourceId);
            if (!$source) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data kriteria catatan sumber tidak ditemukan'
                ]);
            }

            // Verify source is from 'default'
            if ($source['IdTpq'] !== 'default') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya konfigurasi dengan IdTpq = "default" yang dapat diduplikasi'
                ]);
            }

            // Check if configuration already exists for target
            $existing = $this->kriteriaCatatanRaporModel
                ->where('IdTpq', $targetIdTpq)
                ->where('NilaiHuruf', $nilaiHuruf ?: $source['NilaiHuruf'])
                ->where('IdTahunAjaran', $targetIdTahunAjaran)
                ->where('IdKelas', $targetIdKelas)
                ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Konfigurasi dengan kriteria tersebut sudah ada. Silakan edit konfigurasi yang sudah ada.',
                    'duplicate' => true,
                    'existing_id' => $existing['id']
                ]);
            }

            // Create new configuration
            $data = [
                'IdTpq' => $targetIdTpq,
                'IdTahunAjaran' => $targetIdTahunAjaran,
                'IdKelas' => $targetIdKelas,
                'NilaiHuruf' => $nilaiHuruf ?: $source['NilaiHuruf'],
                'NilaiMin' => !empty($nilaiMin) ? $nilaiMin : $source['NilaiMin'],
                'NilaiMax' => !empty($nilaiMax) ? $nilaiMax : $source['NilaiMax'],
                'Catatan' => !empty($catatan) ? $catatan : $source['Catatan'],
                'Status' => $status
            ];

            if ($this->kriteriaCatatanRaporModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Konfigurasi berhasil diduplikasi'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menduplikasi data',
                    'errors' => $this->kriteriaCatatanRaporModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in duplicateKriteriaCatatanRapor: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Method untuk membuat tabel kriteria catatan rapor
     * Hanya untuk development/setup awal
     */
    public function createTableKriteriaCatatanRapor()
    {
        try {
            $db = \Config\Database::connect();

            // SQL untuk membuat tabel
            $createTableSQL = "
            CREATE TABLE IF NOT EXISTS `tbl_kriteria_catatan_rapor` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `NilaiHuruf` enum('A','B','C','D') NOT NULL COMMENT 'Nilai huruf berdasarkan rata-rata',
              `NilaiMin` decimal(5,2) DEFAULT NULL COMMENT 'Nilai minimum untuk kategori ini',
              `NilaiMax` decimal(5,2) DEFAULT NULL COMMENT 'Nilai maksimum untuk kategori ini',
              `Catatan` text NOT NULL COMMENT 'Catatan penilaian untuk raport',
              `Status` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
              `IdTahunAjaran` varchar(50) DEFAULT NULL COMMENT 'Tahun ajaran (opsional, NULL untuk default)',
              `IdTpq` varchar(50) DEFAULT 'default' COMMENT 'ID TPQ, \"default\" untuk catatan umum',
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_nilai_huruf` (`NilaiHuruf`),
              KEY `idx_status` (`Status`),
              KEY `idx_tahun_ajaran` (`IdTahunAjaran`),
              KEY `idx_id_tpq` (`IdTpq`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            // Jalankan query untuk membuat tabel
            $db->query($createTableSQL);

            // Cek apakah tabel berhasil dibuat
            $tableExists = $db->tableExists('tbl_kriteria_catatan_rapor');

            if ($tableExists) {
                // Cek apakah data default sudah ada
                $existingData = $db->table('tbl_kriteria_catatan_rapor')
                    ->where('IdTpq', 'default')
                    ->where('IdTahunAjaran IS NULL')
                    ->countAllResults();

                if ($existingData == 0) {
                    // Insert data default jika belum ada
                    $defaultCatatan = [
                        [
                            'NilaiHuruf' => 'A',
                            'NilaiMin' => 90.00,
                            'NilaiMax' => 100.00,
                            'Catatan' => 'Alhamdulillah, ananda telah menunjukkan prestasi yang sangat baik dalam pembelajaran semester ini. Semangat belajar yang tinggi dan ketekunan dalam menghafal Al-Qur\'an serta memahami ilmu agama patut diacungi jempol. Terus pertahankan semangat belajar dan jangan pernah berhenti mencari ilmu, karena menuntut ilmu adalah ibadah. Semoga Allah SWT senantiasa memberikan keberkahan dalam setiap langkah ananda.',
                            'Status' => 'Aktif',
                            'IdTahunAjaran' => null,
                            'IdTpq' => 'default'
                        ],
                        [
                            'NilaiHuruf' => 'B',
                            'NilaiMin' => 80.00,
                            'NilaiMax' => 89.99,
                            'Catatan' => 'Alhamdulillah, ananda telah menunjukkan hasil yang baik dalam pembelajaran semester ini. Prestasi yang dicapai sudah cukup memuaskan, namun masih ada ruang untuk peningkatan. Terus tingkatkan semangat belajar, perbanyak latihan menghafal, dan jangan ragu untuk bertanya kepada ustadz/ustadzah jika ada yang belum dipahami. Ingatlah bahwa menuntut ilmu membutuhkan kesabaran dan ketekunan. Semoga Allah SWT memberikan kemudahan dalam belajar.',
                            'Status' => 'Aktif',
                            'IdTahunAjaran' => null,
                            'IdTpq' => 'default'
                        ],
                        [
                            'NilaiHuruf' => 'C',
                            'NilaiMin' => 70.00,
                            'NilaiMax' => 79.99,
                            'Catatan' => 'Ananda telah berusaha dalam pembelajaran semester ini, namun masih perlu meningkatkan fokus dan ketekunan dalam belajar. Perbanyak waktu untuk mengulang pelajaran, latihan membaca Al-Qur\'an, dan menghafal. Jangan mudah menyerah, karena setiap usaha yang ikhlas akan mendapat balasan dari Allah SWT. Tingkatkan kedisiplinan dalam belajar dan jangan lupa untuk selalu berdoa memohon kemudahan. Semoga semester depan ananda bisa lebih baik lagi.',
                            'Status' => 'Aktif',
                            'IdTahunAjaran' => null,
                            'IdTpq' => 'default'
                        ],
                        [
                            'NilaiHuruf' => 'D',
                            'NilaiMin' => 60.00,
                            'NilaiMax' => 69.99,
                            'Catatan' => 'Ananda perlu lebih serius dan fokus dalam pembelajaran. Hasil yang dicapai masih perlu ditingkatkan dengan lebih giat belajar dan berlatih. Perbanyak waktu untuk mengulang pelajaran di rumah, latihan membaca Al-Qur\'an setiap hari, dan jangan malu untuk bertanya jika ada kesulitan. Ingatlah bahwa menuntut ilmu adalah kewajiban setiap muslim. Mulai dari sekarang, tingkatkan semangat belajar dan jadikan Al-Qur\'an sebagai pedoman hidup. Semoga dengan tekad yang kuat, ananda bisa meraih hasil yang lebih baik di semester berikutnya.',
                            'Status' => 'Aktif',
                            'IdTahunAjaran' => null,
                            'IdTpq' => 'default'
                        ]
                    ];

                    foreach ($defaultCatatan as $catatan) {
                        $db->table('tbl_kriteria_catatan_rapor')->insert($catatan);
                    }
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tabel tbl_kriteria_catatan_rapor berhasil dibuat dan data default berhasil diinsert.',
                    'table_exists' => true,
                    'default_data_count' => $existingData > 0 ? $existingData : 4
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat tabel tbl_kriteria_catatan_rapor'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== CATATAN DAN ABSENSI RAPOR ====================

    /**
     * Ambil data setting rapor untuk santri
     */
    public function getCatatanAbsensi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $IdSantri = $this->request->getPost('IdSantri');
        $IdTahunAjaran = $this->request->getPost('IdTahunAjaran') ?? session()->get('IdTahunAjaran');
        $semester = $this->request->getPost('Semester');

        if (empty($IdSantri) || empty($semester)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IdSantri dan Semester harus diisi'
            ]);
        }

        $data = $this->raportSettingModel->getDataBySantri($IdSantri, $IdTahunAjaran, $semester);

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Simpan data absensi
     */
    public function saveAbsensi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $IdSantri = $this->request->getPost('IdSantri');
        $IdKelas = $this->request->getPost('IdKelas');
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $semester = $this->request->getPost('Semester');
        $showAbsensi = $this->request->getPost('ShowAbsensi') ? 1 : 0;

        // Ambil atau buat data
        $existingData = $this->raportSettingModel->getOrCreateData($IdSantri, $IdKelas, $IdTpq, $IdTahunAjaran, $semester);

        // Siapkan data absensi dalam format array
        $absensiData = [
            'ShowAbsensi' => $showAbsensi,
            'jumlahTidakMasuk' => (int)$this->request->getPost('JumlahTidakMasuk') ?? 0,
            'jumlahIzin' => (int)$this->request->getPost('JumlahIzin') ?? 0,
            'jumlahAlfa' => (int)$this->request->getPost('JumlahAlfa') ?? 0,
            'jumlahSakit' => (int)$this->request->getPost('JumlahSakit') ?? 0
        ];

        // Simpan data absensi
        if ($this->raportSettingModel->saveAbsensiData($IdSantri, $IdTahunAjaran, $semester, $absensiData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data absensi berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data absensi'
            ]);
        }
    }

    /**
     * Simpan data catatan
     */
    public function saveCatatan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $IdSantri = $this->request->getPost('IdSantri');
        $IdKelas = $this->request->getPost('IdKelas');
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $semester = $this->request->getPost('Semester');
        $showCatatan = $this->request->getPost('ShowCatatan') ? 1 : 0;
        $catatanDefault = $this->request->getPost('CatatanDefault') ?? '';
        $catatanKhusus = $this->request->getPost('CatatanKhusus') ?? '';
        $selectedCatatanId = $this->request->getPost('CatatanSource') ?? null;

        // Ambil atau buat data
        $existingData = $this->raportSettingModel->getOrCreateData($IdSantri, $IdKelas, $IdTpq, $IdTahunAjaran, $semester);

        // Siapkan data catatan dalam format array
        $catatanData = [
            'ShowCatatan' => $showCatatan,
            'catatanDefault' => $catatanDefault,
            'catatanKhusus' => $catatanKhusus,
            'selectedCatatanId' => $selectedCatatanId
        ];

        // Simpan data catatan
        if ($this->raportSettingModel->saveCatatanData($IdSantri, $IdTahunAjaran, $semester, $catatanData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data catatan berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data catatan'
            ]);
        }
    }

    /**
     * Ambil catatan default berdasarkan nilai rata-rata
     */
    public function getCatatanDefaultByNilai()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $IdSantri = $this->request->getPost('IdSantri');
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $semester = $this->request->getPost('Semester');

        // Ambil data santri dengan nilai
        $santriData = $this->getSantriDataWithNilai($IdSantri, $IdTpq, $IdTahunAjaran, $semester);

        if (!$santriData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data santri tidak ditemukan'
            ]);
        }

        // Hitung rata-rata nilai
        $nilaiRataRata = $this->hitungRataRataNilai($santriData['nilai']);

        // Ambil IdKelas
        $idKelas = $santriData['santri']['IdKelas'] ?? null;

        // Konversi nilai ke huruf
        $nilaiHuruf = $this->konversiNilaiKeHuruf($nilaiRataRata);

        // Ambil catatan default (yang paling spesifik sesuai prioritas)
        $catatanDefault = $this->generateKriteriaCatatanRapor($nilaiRataRata, $IdTahunAjaran, $IdTpq, $idKelas);

        // Ambil semua opsi catatan yang tersedia
        $idTpqString = (!empty($IdTpq) && $IdTpq != 0) ? (string)$IdTpq : 'default';
        $idKelasString = null;
        if (!empty($idKelas) && $idKelas != 0 && $idKelas != '0') {
            $idKelasString = (string)$idKelas;
        }

        $allOpsiCatatan = $this->kriteriaCatatanRaporModel->getAllOpsiCatatanByNilaiHuruf(
            $nilaiHuruf,
            $IdTahunAjaran,
            $idTpqString,
            $idKelasString
        );

        // Tentukan catatan default yang dipilih (yang paling spesifik)
        $selectedCatatanId = null;
        if (!empty($allOpsiCatatan)) {
            // Prioritas: spesifik_kelas > spesifik_tpq > umum
            foreach ($allOpsiCatatan as $opsi) {
                if ($opsi['level'] === 'spesifik_kelas') {
                    $selectedCatatanId = $opsi['id'];
                    break;
                } elseif ($opsi['level'] === 'spesifik_tpq' && $selectedCatatanId === null) {
                    $selectedCatatanId = $opsi['id'];
                } elseif ($opsi['level'] === 'umum' && $selectedCatatanId === null) {
                    $selectedCatatanId = $opsi['id'];
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'catatan' => $catatanDefault,
            'nilaiRataRata' => $nilaiRataRata,
            'nilaiHuruf' => $nilaiHuruf,
            'allOpsiCatatan' => $allOpsiCatatan,
            'selectedCatatanId' => $selectedCatatanId
        ]);
    }

    /**
     * Konversi nilai numerik ke huruf
     */
    private function konversiNilaiKeHuruf($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    /**
     * Ambil data absensi dari tbl_absensi_santri untuk generate ke rapor setting
     */
    public function getAbsensiFromTable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $IdSantri = $this->request->getPost('IdSantri');
        $IdTahunAjaran = $this->request->getPost('IdTahunAjaran') ?? session()->get('IdTahunAjaran');
        $semester = $this->request->getPost('Semester');

        if (empty($IdSantri) || empty($semester)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IdSantri dan Semester harus diisi'
            ]);
        }

        // Tentukan rentang tanggal berdasarkan semester
        // Format IdTahunAjaran biasanya: "2023/2024", "2023-2024", atau "20232024"
        $tahunAjaranParts = preg_split('/[\/\-]/', $IdTahunAjaran);

        // Jika tidak ada separator, coba split berdasarkan 4 digit tahun
        if (count($tahunAjaranParts) < 2) {
            // Format: "20232024" -> split menjadi "2023" dan "2024"
            if (strlen($IdTahunAjaran) == 8 && is_numeric($IdTahunAjaran)) {
                $tahunAwal = (int)substr($IdTahunAjaran, 0, 4);
                $tahunAkhir = (int)substr($IdTahunAjaran, 4, 4);
            } else {
                // Fallback: gunakan tahun saat ini
                $tahunAwal = (int)date('Y');
                $tahunAkhir = $tahunAwal + 1;
            }
        } else {
            $tahunAwal = isset($tahunAjaranParts[0]) ? (int)trim($tahunAjaranParts[0]) : (int)date('Y');
            $tahunAkhir = isset($tahunAjaranParts[1]) ? (int)trim($tahunAjaranParts[1]) : ($tahunAwal + 1);
        }

        if ($semester === 'Ganjil') {
            // Semester Ganjil: Juli - Desember (tahun awal)
            $startDate = $tahunAwal . '-07-01';
            $endDate = $tahunAwal . '-12-31';
        } else {
            // Semester Genap: Januari - Juni (tahun akhir)
            $startDate = $tahunAkhir . '-01-01';
            $endDate = $tahunAkhir . '-06-30';
        }

        // Ambil data absensi dari tabel
        $absensiData = $this->absensiModel
            ->where('IdSantri', $IdSantri)
            ->where('IdTahunAjaran', $IdTahunAjaran)
            ->where('Tanggal >=', $startDate)
            ->where('Tanggal <=', $endDate)
            ->findAll();

        if (empty($absensiData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data absensi dari tabel untuk periode ini',
                'data' => null
            ]);
        }

        // Hitung jumlah berdasarkan Kehadiran
        $jumlahIzin = 0;
        $jumlahAlfa = 0;
        $jumlahSakit = 0;
        $jumlahHadir = 0;

        foreach ($absensiData as $absensi) {
            $kehadiran = strtolower(trim($absensi['Kehadiran'] ?? ''));

            if ($kehadiran === 'izin') {
                $jumlahIzin++;
            } elseif ($kehadiran === 'alfa') {
                $jumlahAlfa++;
            } elseif ($kehadiran === 'sakit') {
                $jumlahSakit++;
            } elseif ($kehadiran === 'hadir') {
                $jumlahHadir++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data absensi berhasil diambil dari tabel',
            'data' => [
                'jumlahIzin' => $jumlahIzin,
                'jumlahAlfa' => $jumlahAlfa,
                'jumlahSakit' => $jumlahSakit,
                'jumlahHadir' => $jumlahHadir,
                'totalRecords' => count($absensiData),
                'periode' => [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'semester' => $semester
                ]
            ]
        ]);
    }

    /**
     * Halaman setting mapping wali kelas
     */
    public function settingMappingWaliKelas($IdKelas = null)
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdGuru = session()->get('IdGuru');

        // Cek apakah user adalah Wali Kelas
        if (!empty($IdKelas)) {
            $guruKelasPermission = $this->helpFunctionModel->checkGuruKelasPermission(
                $IdTpq,
                $IdGuru,
                $IdKelas,
                $IdTahunAjaran
            );

            if (!$guruKelasPermission || $guruKelasPermission['NamaJabatan'] !== 'Wali Kelas') {
                return redirect()->back()->with('error', 'Hanya Wali Kelas yang dapat mengakses halaman ini.');
            }
        }

        // Cek setting MappingWaliKelas
        $mappingEnabled = $this->toolsModel->getSetting($IdTpq, 'MappingWaliKelas');
        if (!$mappingEnabled) {
            return redirect()->back()->with('error', 'Fitur mapping wali kelas belum diaktifkan. Silakan hubungi admin/operator untuk mengaktifkan setting MappingWaliKelas di Tools.');
        }

        // Ambil list kelas untuk dropdown (jika Wali Kelas)
        $listKelas = [];
        if (!empty($IdGuru)) {
            $guruKelasData = $this->helpFunctionModel->getDataGuruKelas(
                IdGuru: $IdGuru,
                IdTpq: $IdTpq,
                IdTahunAjaran: $IdTahunAjaran
            );

            foreach ($guruKelasData as $gk) {
                $gkArray = is_object($gk) ? (array)$gk : $gk;
                if (($gkArray['NamaJabatan'] ?? '') === 'Wali Kelas') {
                    $listKelas[] = [
                        'IdKelas' => $gkArray['IdKelas'],
                        'NamaKelas' => $gkArray['NamaKelas'] ?? ''
                    ];
                }
            }
        }

        // Jika IdKelas tidak dipilih, gunakan kelas pertama
        if (empty($IdKelas) && !empty($listKelas)) {
            $IdKelas = $listKelas[0]['IdKelas'];
        }

        // Ambil data santri di kelas
        $santriList = [];
        $guruPendampingList = [];
        $mappingData = [];

        if (!empty($IdKelas)) {
            // Ambil santri di kelas
            $santriList = $this->santriBaruModel->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
                ->where([
                    'tbl_santri_baru.IdTpq' => $IdTpq,
                    'tbl_santri_baru.IdKelas' => $IdKelas,
                    'tbl_santri_baru.Active' => 1
                ])
                ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')
                ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->findAll();

            // Ambil semua guru yang mengajar di kelas yang sama dengan wali kelas
            // Ini termasuk Wali Kelas, Guru Pendamping, dan guru lain yang mengajar di kelas tersebut
            $allGuruKelas = $this->helpFunctionModel->getDataGuruKelas(
                IdTpq: $IdTpq,
                IdKelas: $IdKelas,
                IdTahunAjaran: $IdTahunAjaran
            );

            $guruPendampingList = [];
            $addedGuruIds = []; // Untuk menghindari duplikasi

            // Tambahkan semua guru yang mengajar di kelas ini
            foreach ($allGuruKelas as $gk) {
                $gkArray = is_object($gk) ? (array)$gk : $gk;
                $idGuru = $gkArray['IdGuru'] ?? null;
                $namaGuru = $gkArray['Nama'] ?? '';
                $namaJabatan = $gkArray['NamaJabatan'] ?? '';

                // Pastikan IdGuru valid dan belum ditambahkan
                if (!empty($idGuru) && !in_array($idGuru, $addedGuruIds)) {
                    // Terapkan ucwords pada nama guru
                    $namaGuruFormatted = ucwords(strtolower($namaGuru));
                    $guruPendampingList[] = [
                        'IdGuru' => $idGuru,
                        'Nama' => $namaGuruFormatted . ($namaJabatan ? ' (' . $namaJabatan . ')' : '')
                    ];
                    $addedGuruIds[] = $idGuru;
                }
            }

            // Ambil mapping yang sudah ada
            $mappingList = $this->rombelWalikelasModel->getMappingByKelas(
                $IdKelas,
                $IdTahunAjaran,
                $IdTpq
            );

            // Buat array mapping dengan key IdSantri
            foreach ($mappingList as $mapping) {
                $mappingData[$mapping['IdSantri']] = $mapping['IdGuru'];
            }

            // Ambil Wali Kelas asli untuk kelas ini (IdGuru dengan IdJabatan = 3)
            $waliKelasAsli = null;
            $waliKelasIdGuru = null;
            $db = \Config\Database::connect();
            $builder = $db->table('tbl_guru_kelas');
            $builder->select('tbl_guru_kelas.IdGuru, g.Nama');
            $builder->join('tbl_guru g', 'g.IdGuru = tbl_guru_kelas.IdGuru');
            $builder->where('tbl_guru_kelas.IdKelas', $IdKelas);
            $builder->where('tbl_guru_kelas.IdTpq', $IdTpq);
            $builder->where('tbl_guru_kelas.IdTahunAjaran', $IdTahunAjaran);
            $builder->where('tbl_guru_kelas.IdJabatan', 3); // Wali Kelas
            $builder->limit(1);
            $waliKelasAsli = $builder->get()->getRowArray();

            if (!empty($waliKelasAsli) && !empty($waliKelasAsli['IdGuru'])) {
                $waliKelasIdGuru = $waliKelasAsli['IdGuru'];
            }
        }

        $data = [
            'page_title' => 'Setting Mapping Wali Kelas',
            'IdKelas' => $IdKelas,
            'listKelas' => $listKelas,
            'santriList' => $santriList,
            'guruPendampingList' => $guruPendampingList,
            'mappingData' => $mappingData,
            'waliKelasIdGuru' => $waliKelasIdGuru ?? null,
            'mappingEnabled' => $mappingEnabled
        ];

        return view('backend/rapor/settingMappingWaliKelas', $data);
    }

    /**
     * Simpan mapping wali kelas (mass update)
     */
    public function saveMappingWaliKelas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdGuru = session()->get('IdGuru');

        // Ambil array mappings dari request
        $mappings = $this->request->getPost('mappings');
        $IdKelas = $this->request->getPost('IdKelas');

        // Log untuk debugging
        log_message('debug', 'Rapor: saveMappingWaliKelas - Request data: ' . json_encode([
            'mappings_count' => is_array($mappings) ? count($mappings) : 0,
            'IdKelas' => $IdKelas,
            'IdTpq' => $IdTpq,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdGuru' => $IdGuru
        ]));

        // Validasi
        if (empty($mappings) || !is_array($mappings) || empty($IdKelas)) {
            log_message('error', 'Rapor: saveMappingWaliKelas - Data tidak lengkap');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Cek permission: hanya Wali Kelas, Admin, atau Operator yang bisa save
        $isAdmin = in_groups('Admin');
        $isOperator = in_groups('Operator');

        if (!$isAdmin && !$isOperator) {
            // Untuk Guru, cek apakah adalah Wali Kelas
            $guruKelasPermission = $this->helpFunctionModel->checkGuruKelasPermission(
                $IdTpq,
                $IdGuru,
                $IdKelas,
                $IdTahunAjaran
            );

            if (!$guruKelasPermission || $guruKelasPermission['NamaJabatan'] !== 'Wali Kelas') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki permission untuk melakukan mapping wali kelas.'
                ]);
            }
        }

        // Cek setting MappingWaliKelas
        $mappingEnabled = $this->toolsModel->getSetting($IdTpq, 'MappingWaliKelas');
        if (!$mappingEnabled) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fitur mapping wali kelas belum diaktifkan.'
            ]);
        }

        // Validasi dan siapkan data untuk mass update
        $dataToSave = [];
        foreach ($mappings as $mapping) {
            $IdSantri = $mapping['IdSantri'] ?? null;
            $IdGuruMapping = $mapping['IdGuru'] ?? null;

            // Skip jika IdSantri tidak ada
            if (empty($IdSantri)) {
                continue;
            }

            // Jika IdGuru null atau kosong, berarti mapping dihapus (akan di-handle di model)
            $dataToSave[] = [
                'IdSantri' => $IdSantri,
                'IdTahunAjaran' => $IdTahunAjaran,
                'IdGuru' => $IdGuruMapping, // bisa null untuk delete
                'IdKelas' => $IdKelas,
                'IdTpq' => $IdTpq
            ];
        }

        if (empty($dataToSave)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data mapping yang valid untuk disimpan'
            ]);
        }

        try {
            // Mass save ke model
            $result = $this->rombelWalikelasModel->saveMappingBatch($dataToSave);

            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'saved' => $result['saved'],
                        'updated' => $result['updated'],
                        'deleted' => $result['deleted']
                    ]
                ]);
            } else {
                log_message('error', 'Rapor: saveMappingWaliKelas - Error: ' . $result['message']);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Rapor: saveMappingWaliKelas - Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hapus mapping wali kelas
     */
    public function deleteMappingWaliKelas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdGuru = session()->get('IdGuru');

        $IdSantri = $this->request->getPost('IdSantri');
        $IdKelas = $this->request->getPost('IdKelas');

        // Validasi
        if (empty($IdSantri) || empty($IdKelas)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Cek permission: hanya Wali Kelas yang bisa delete
        $guruKelasPermission = $this->helpFunctionModel->checkGuruKelasPermission(
            $IdTpq,
            $IdGuru,
            $IdKelas,
            $IdTahunAjaran
        );

        if (!$guruKelasPermission || $guruKelasPermission['NamaJabatan'] !== 'Wali Kelas') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk menghapus mapping wali kelas.'
            ]);
        }

        // Hapus mapping
        if ($this->rombelWalikelasModel->deleteMapping($IdSantri, $IdTahunAjaran, $IdKelas, $IdTpq)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Mapping wali kelas berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus mapping'
            ]);
        }
    }
}
