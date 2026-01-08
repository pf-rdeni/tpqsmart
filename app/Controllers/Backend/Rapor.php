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
    protected $serahTerimaRaporModel;

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
        $this->serahTerimaRaporModel = new \App\Models\SerahTerimaRaporModel();
        ini_set('memory_limit', '256M');
        set_time_limit(300);
        mb_internal_encoding('UTF-8');
    }

    /**
     * Setup Dompdf configuration dengan pengaturan optimal untuk karakter Arab dan performa
     */
    private function setupDompdfConfig($isBulk = false)
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
        $options->set('debugKeepTemp', false);
        $options->set('debugCss', false);
        $options->set('debugLayout', false);
        $options->set('debugLayoutLines', false);
        $options->set('debugLayoutBlocks', false);
        $options->set('debugLayoutInline', false);
        $options->set('debugLayoutPaddingBox', false);

        // Optimasi khusus untuk bulk processing
        if ($isBulk) {
            // Kurangi memory usage dengan optimasi tambahan
            // Set temp directory untuk mengurangi I/O
            $tempDir = sys_get_temp_dir() . '/dompdf_' . uniqid();
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            if (method_exists($options, 'setTempDir')) {
                $options->setTempDir($tempDir);
            }
        }

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
     * Group nilai berdasarkan kategori sesuai konfigurasi
     * RataKelas juga dihitung dari nilai yang sudah digabungkan
     */
    private function groupNilaiByKategori($nilai, $IdTpq, $IdKelas = null, $IdTahunAjaran = null, $semester = null)
    {
        // Cek apakah fitur grouping aktif
        $isGroupingEnabled = $this->toolsModel->getSettingAsBool($IdTpq, 'GroupKategoriNilai', false);

        if (!$isGroupingEnabled) {
            return $nilai; // Return as is jika tidak aktif
        }

        // Cek apakah kelas ini termasuk dalam daftar kelas yang menggunakan grouping
        $kelasGroupingString = $this->toolsModel->getSettingAsString($IdTpq, 'GroupKategoriNilaiKelas', '');
        $kelasGrouping = [];
        if (!empty($kelasGroupingString)) {
            // Explode string yang dipisahkan koma menjadi array
            $kelasGrouping = array_map('trim', explode(',', $kelasGroupingString));
            $kelasGrouping = array_filter($kelasGrouping, function ($item) {
                return !empty($item); // Hapus elemen kosong
            });
            $kelasGrouping = array_values($kelasGrouping); // Re-index array
        }

        if (!empty($kelasGrouping)) {
            // Ambil nama kelas
            $namaKelas = null;
            if ($IdKelas) {
                $kelasData = $this->helpFunctionModel->getNamaKelasBulk([$IdKelas]);
                $namaKelas = $kelasData[$IdKelas] ?? null;
            }

            // Jika ada daftar kelas spesifik, cek apakah kelas ini termasuk
            if ($namaKelas) {
                $isKelasInGroup = false;
                $namaKelasNormalized = strtoupper(trim($namaKelas));

                // Cek setiap kelas di grouping
                foreach ($kelasGrouping as $kelasGroup) {
                    $kelasGroupNormalized = strtoupper(trim($kelasGroup));

                    // 1. Exact match
                    if ($namaKelasNormalized === $kelasGroupNormalized) {
                        $isKelasInGroup = true;
                        break;
                    }

                    // 2. Contains match - untuk handle "TPQ3/SD3" dengan "TPQ3"
                    // Contoh: "TPQ3/SD3" mengandung "TPQ3" -> match
                    if (stripos($namaKelasNormalized, $kelasGroupNormalized) !== false) {
                        $isKelasInGroup = true;
                        break;
                    }

                    // 3. Reverse contains - untuk handle "TPQ3" dengan "TPQ3/SD3" di grouping
                    if (stripos($kelasGroupNormalized, $namaKelasNormalized) !== false) {
                        $isKelasInGroup = true;
                        break;
                    }

                    // 4. Pattern match untuk TPQ + angka
                    // Contoh: "TPQ3" di grouping bisa match dengan "TPQ3/SD3"
                    if (strpos($kelasGroupNormalized, 'TPQ') !== false) {
                        $tpqKelasWithoutPrefix = str_replace('TPQ', '', $kelasGroupNormalized);
                        if (!empty($tpqKelasWithoutPrefix)) {
                            // Pattern: TPQ diikuti angka dari grouping (bisa ada spasi atau karakter lain setelahnya)
                            if (preg_match('/TPQ\s*' . preg_quote($tpqKelasWithoutPrefix, '/') . '/i', $namaKelasNormalized)) {
                                $isKelasInGroup = true;
                                break;
                            }
                        }
                    }
                }

                // Jika kelas ini tidak ada di daftar grouping, return as is
                if (!$isKelasInGroup) {
                    return $nilai;
                }
            }
        }

        // Ambil konfigurasi grouping kategori
        $groupKategoriModel = new \App\Models\RaporGroupKategoriModel();
        $groupConfigs = $groupKategoriModel->getActiveByTpq($IdTpq);

        if (empty($groupConfigs)) {
            return $nilai; // Tidak ada konfigurasi, return as is
        }

        // Buat mapping kategori -> nama materi baru
        $kategoriMapping = [];
        foreach ($groupConfigs as $config) {
            $kategoriMapping[$config['KategoriAsal']] = [
                'NamaMateriBaru' => $config['NamaMateriBaru'],
                'Urutan' => $config['Urutan']
            ];
        }

        // ===== HITUNG RATA KELAS DARI NILAI YANG SUDAH DIGABUNGKAN =====
        // Ambil semua nilai dari semua santri di kelas yang sama untuk menghitung rata-rata kelas
        $rataKelasGrouped = [];

        if ($IdKelas && $IdTahunAjaran && $semester) {
            // Ambil semua nilai dari semua santri di kelas
            $db = \Config\Database::connect();
            $builder = $db->table('tbl_nilai n');
            $builder->select('n.IdSantri, n.IdMateri, n.Nilai, m.Kategori');
            $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = n.IdMateri');
            $builder->where('n.IdTpq', $IdTpq);
            $builder->where('n.IdKelas', $IdKelas);
            $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
            $builder->where('n.Semester', $semester);

            $allNilaiKelas = $builder->get()->getResult();

            // Kelompokkan nilai per santri dan kategori
            $nilaiPerSantri = [];
            foreach ($allNilaiKelas as $nilaiKelas) {
                $idSantri = $nilaiKelas->IdSantri;
                $kategori = $nilaiKelas->Kategori ?? '';

                if (!isset($nilaiPerSantri[$idSantri])) {
                    $nilaiPerSantri[$idSantri] = [];
                }

                if (!isset($nilaiPerSantri[$idSantri][$kategori])) {
                    $nilaiPerSantri[$idSantri][$kategori] = [];
                }

                $nilaiPerSantri[$idSantri][$kategori][] = floatval($nilaiKelas->Nilai);
            }

            // Hitung rata-rata per kategori untuk setiap santri (setelah grouping)
            $rataPerSantriPerKategori = [];
            foreach ($nilaiPerSantri as $idSantri => $kategoriNilai) {
                foreach ($kategoriNilai as $kategori => $nilaiList) {
                    if (isset($kategoriMapping[$kategori])) {
                        // Kategori ini perlu di-group
                        $count = count($nilaiList);
                        $rata = $count > 0 ? array_sum($nilaiList) / $count : 0;

                        if (!isset($rataPerSantriPerKategori[$kategori])) {
                            $rataPerSantriPerKategori[$kategori] = [];
                        }

                        $rataPerSantriPerKategori[$kategori][] = $rata;
                    }
                }
            }

            // Hitung rata-rata kelas untuk setiap kategori yang di-group
            foreach ($rataPerSantriPerKategori as $kategori => $rataList) {
                $count = count($rataList);
                $rataKelasGrouped[$kategori] = $count > 0 ? round(array_sum($rataList) / $count, 2) : 0;
            }
        }

        // ===== GROUP NILAI SANTRI YANG SEDANG DITAMPILKAN =====
        // Group nilai berdasarkan kategori
        $groupedNilai = [];
        $ungroupedNilai = [];

        foreach ($nilai as $n) {
            $kategori = $n->Kategori ?? '';

            if (isset($kategoriMapping[$kategori])) {
                // Kategori ini perlu di-group
                $key = $kategori;
                if (!isset($groupedNilai[$key])) {
                    $groupedNilai[$key] = [
                        'kategori' => $kategori,
                        'nama_materi_baru' => $kategoriMapping[$kategori]['NamaMateriBaru'],
                        'urutan' => $kategoriMapping[$kategori]['Urutan'],
                        'nilai_list' => [],
                        'rata_kelas' => $rataKelasGrouped[$kategori] ?? 0 // Gunakan rata kelas yang sudah dihitung
                    ];
                }

                $groupedNilai[$key]['nilai_list'][] = floatval($n->Nilai);
            } else {
                // Kategori ini tidak perlu di-group, tetap tampilkan individual
                // Untuk ungrouped, kita tetap perlu hitung rata kelas dari semua santri
                if ($IdKelas && $IdTahunAjaran && $semester) {
                    // Ambil rata kelas untuk materi ini (dari query terpisah)
                    $db = \Config\Database::connect();
                    $builderRata = $db->table('tbl_nilai n');
                    $builderRata->select('ROUND(AVG(n.Nilai), 2) as RataKelas');
                    $builderRata->where('n.IdTpq', $IdTpq);
                    $builderRata->where('n.IdKelas', $IdKelas);
                    $builderRata->where('n.IdTahunAjaran', $IdTahunAjaran);
                    $builderRata->where('n.Semester', $semester);
                    $builderRata->where('n.IdMateri', $n->IdMateri ?? '');

                    $rataResult = $builderRata->get()->getRow();
                    $n->RataKelas = $rataResult ? floatval($rataResult->RataKelas) : ($n->RataKelas ?? 0);
                }

                $ungroupedNilai[] = $n;
            }
        }

        // Hitung rata-rata untuk setiap group
        $groupedResults = [];
        foreach ($groupedNilai as $key => $group) {
            $count = count($group['nilai_list']);
            $rataNilai = $count > 0 ? array_sum($group['nilai_list']) / $count : 0;

            // Buat object mirip dengan struktur $nilai asli
            $groupedObj = (object)[
                'NamaMateri' => $group['nama_materi_baru'],
                'Kategori' => $group['kategori'],
                'Nilai' => round($rataNilai, 2),
                'RataKelas' => $group['rata_kelas'], // Gunakan rata kelas yang sudah dihitung dari nilai gabungan
                'UrutanMateri' => $group['urutan']
            ];

            // Copy property lain dari nilai pertama yang memiliki kategori ini untuk kompatibilitas
            $firstNilai = null;
            foreach ($nilai as $n) {
                if (($n->Kategori ?? '') === $group['kategori']) {
                    $firstNilai = $n;
                    break;
                }
            }

            if ($firstNilai) {
                foreach (get_object_vars($firstNilai) as $prop => $val) {
                    if (!isset($groupedObj->$prop) && $prop !== 'Nilai' && $prop !== 'RataKelas' && $prop !== 'NamaMateri') {
                        $groupedObj->$prop = $val;
                    }
                }
            }

            $groupedResults[] = $groupedObj;
        }

        // Gabungkan grouped results dengan ungrouped, urutkan berdasarkan UrutanMateri
        $allResults = array_merge($groupedResults, $ungroupedNilai);
        usort($allResults, function ($a, $b) {
            $urutanA = $a->UrutanMateri ?? 999;
            $urutanB = $b->UrutanMateri ?? 999;
            return $urutanA <=> $urutanB;
        });

        return $allResults;
    }

    /**
     * Siapkan data untuk view rapor
     */
    private function prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester, $tanggalCetak = null, $batasanPeringkat = null)
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
            semester: $semester,
            typeLembaga: $lembagaType
        );

        // Ambil IdKelas dari data santri (pastikan ada dan valid)
        $idKelas = null;
        if (isset($santriData['santri']['IdKelas']) && !empty($santriData['santri']['IdKelas'])) {
            $idKelas = $santriData['santri']['IdKelas'];
        }

        // Log untuk debugging
        log_message('debug', 'Rapor: prepareRaporData - idKelas dari santri: ' . ($idKelas ?? 'null'));

        // Group nilai berdasarkan kategori jika setting aktif
        // Pass parameter tambahan untuk menghitung RataKelas
        $nilaiGrouped = $this->groupNilaiByKategori(
            $santriData['nilai'],
            $IdTpq,
            $idKelas,
            $IdTahunAjaran,
            $semester
        );

        // Hitung rata-rata nilai untuk generate catatan raport (dari nilai yang sudah di-group)
        $nilaiRataRata = $this->hitungRataRataNilai($nilaiGrouped);

        // Generate catatan raport berdasarkan nilai rata-rata
        $catatanRaport = $this->generateKriteriaCatatanRapor($nilaiRataRata, $IdTahunAjaran, $IdTpq, $idKelas);

        // Ambil data setting rapor (catatan dan absensi)
        $raportSetting = $this->raportSettingModel->getDataBySantri(
            $santriData['santri']['IdSantri'],
            $IdTahunAjaran,
            $semester
        );

        // Hitung peringkat santri jika batasan peringkat diisi
        $peringkatData = null;
        if (!empty($batasanPeringkat) && $batasanPeringkat > 0 && !empty($idKelas)) {
            $peringkatData = $this->hitungPeringkatSantri(
                $santriData['santri']['IdSantri'],
                $idKelas,
                $IdTpq,
                $IdTahunAjaran,
                $semester,
                $batasanPeringkat
            );
        }

        return [
            'santri' => $santriData['santri'],
            'nilai' => $nilaiGrouped, // Gunakan nilai yang sudah di-group
            'tpq' => $tpq,
            'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
            'semester' => $semester,
            'tanggal' => !empty($tanggalCetak) ? formatTanggalIndonesia($tanggalCetak, 'd F Y') : formatTanggalIndonesia(date('Y-m-d'), 'd F Y'),
            'signatures' => $signatures,
            'lembagaType' => $lembagaType,
            'nilaiRataRata' => $nilaiRataRata,
            'catatanRaport' => $catatanRaport,
            'raportSetting' => $raportSetting,
            'peringkatData' => $peringkatData
        ];
    }

    /**
     * Hitung peringkat santri berdasarkan nilai rata-rata di kelas
     * @param int $IdSantri ID Santri yang akan dihitung peringkatnya
     * @param int $IdKelas ID Kelas
     * @param int $IdTpq ID TPQ
     * @param int $IdTahunAjaran ID Tahun Ajaran
     * @param string $semester Semester
     * @param int $batasanPeringkat Batasan peringkat (misal 10 untuk 10 besar)
     * @return array|null Array dengan data peringkat atau null jika tidak masuk batasan
     */
    private function hitungPeringkatSantri($IdSantri, $IdKelas, $IdTpq, $IdTahunAjaran, $semester, $batasanPeringkat)
    {
        // Ambil semua santri di kelas yang sama
        $listSantri = $this->santriBaruModel->where([
            'IdTpq' => $IdTpq,
            'IdKelas' => $IdKelas,
            'Active' => 1
        ])->findAll();

        if (empty($listSantri)) {
            return null;
        }

        // Hitung rata-rata nilai untuk setiap santri
        $dataPeringkat = [];
        foreach ($listSantri as $santri) {
            $santriData = $this->getSantriDataWithNilai($santri['IdSantri'], $IdTpq, $IdTahunAjaran, $semester);

            if (!$santriData || empty($santriData['nilai'])) {
                continue;
            }

            // Group nilai berdasarkan kategori
            $nilaiGrouped = $this->groupNilaiByKategori(
                $santriData['nilai'],
                $IdTpq,
                $IdKelas,
                $IdTahunAjaran,
                $semester
            );

            // Hitung rata-rata nilai
            $rataRata = $this->hitungRataRataNilai($nilaiGrouped);

            $dataPeringkat[] = [
                'IdSantri' => $santri['IdSantri'],
                'NamaSantri' => $santri['NamaSantri'],
                'rataRata' => $rataRata
            ];
        }

        // Urutkan berdasarkan rata-rata tertinggi ke terendah
        usort($dataPeringkat, function ($a, $b) {
            return $b['rataRata'] <=> $a['rataRata'];
        });

        // Cari peringkat santri yang sedang diproses
        $peringkat = null;
        foreach ($dataPeringkat as $index => $data) {
            if ($data['IdSantri'] == $IdSantri) {
                $peringkat = $index + 1; // Peringkat dimulai dari 1
                break;
            }
        }

        // Jika peringkat tidak ditemukan atau melebihi batasan, return null
        if ($peringkat === null || $peringkat > $batasanPeringkat) {
            return null;
        }

        // Return data peringkat
        return [
            'peringkat' => $peringkat,
            'batasanPeringkat' => $batasanPeringkat
        ];
    }

    /**
     * Ambil data summary nilai untuk setiap santri
     */
    private function getSummaryDataForSantri($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        // Ambil data summary nilai per semester
        $summaryData = $this->nilaiModel->getDataNilaiPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Buat array dataKelas untuk struktur data
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

        // Cek status tanda tangan bulk per kelas untuk Wali Kelas dan Kepala Sekolah
        $bulkSignatureStatus = [];
        foreach ($dataKelas as $kelas) {
            // Hitung jumlah santri di kelas
            $jumlahSantri = 0;
            $jumlahTtdWalas = 0;
            $jumlahTtdKepsek = 0;
            
            foreach ($summaryData['nilai'] as $nilaiDetail) {
                if ($nilaiDetail->IdKelas == $kelas->IdKelas) {
                    $jumlahSantri++;
                    
                    // Cek tanda tangan wali kelas - cari di semua signature dengan SignatureData = 'Walas'
                    foreach ($signatures as $sig) {
                        if (isset($sig['IdSantri']) && $sig['IdSantri'] == $nilaiDetail->IdSantri &&
                            isset($sig['IdKelas']) && $sig['IdKelas'] == $kelas->IdKelas &&
                            isset($sig['SignatureData']) && $sig['SignatureData'] === 'Walas' &&
                            isset($sig['Semester']) && $sig['Semester'] === $semester) {
                            $jumlahTtdWalas++;
                            break;
                        }
                    }
                    
                    // Cek tanda tangan kepala sekolah - cari di semua signature dengan SignatureData = 'Kepsek'
                    foreach ($signatures as $sig) {
                        if (isset($sig['IdSantri']) && $sig['IdSantri'] == $nilaiDetail->IdSantri &&
                            isset($sig['IdKelas']) && $sig['IdKelas'] == $kelas->IdKelas &&
                            isset($sig['SignatureData']) && $sig['SignatureData'] === 'Kepsek' &&
                            isset($sig['Semester']) && $sig['Semester'] === $semester) {
                            $jumlahTtdKepsek++;
                            break;
                        }
                    }
                }
            }
            
            // Hitung status catatan dan absensi per kelas
            $jumlahCatatan = 0;
            $jumlahAbsensi = 0;
            
            foreach ($summaryData['nilai'] as $nilaiDetail) {
                if ($nilaiDetail->IdKelas == $kelas->IdKelas) {
                    $keySetting = $nilaiDetail->IdSantri . '_' . $semester;
                    
                    // Cek catatan
                    if (isset($raportSettingsMap[$keySetting])) {
                        $setting = $raportSettingsMap[$keySetting];
                        if (isset($setting['ShowCatatan']) && $setting['ShowCatatan'] == 1) {
                            $jumlahCatatan++;
                        }
                    }
                    
                    // Cek absensi
                    if (isset($raportSettingsMap[$keySetting])) {
                        $setting = $raportSettingsMap[$keySetting];
                        if (isset($setting['ShowAbsensi']) && $setting['ShowAbsensi'] == 1) {
                            $jumlahAbsensi++;
                        }
                    }
                }
            }
            
            $bulkSignatureStatus[$kelas->IdKelas] = [
                'total' => $jumlahSantri,
                'ttd_walas' => $jumlahTtdWalas,
                'ttd_kepsek' => $jumlahTtdKepsek,
                'all_signed_walas' => ($jumlahSantri > 0 && $jumlahTtdWalas == $jumlahSantri),
                'all_signed_kepsek' => ($jumlahSantri > 0 && $jumlahTtdKepsek == $jumlahSantri),
                'catatan' => $jumlahCatatan,
                'absensi' => $jumlahAbsensi
            ];
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
            'raportSettingsMap' => $raportSettingsMap,
            'bulkSignatureStatus' => $bulkSignatureStatus
        ];

        return view('backend/rapor/index', $data);
    }

    public function getSantriByKelas($IdKelas)
    {
        try {
            $IdTpq = session()->get('IdTpq');
            $IdGuru = session()->get('IdGuru');
            $IdTahunAjaran = session()->get('IdTahunAjaran');

            if (empty($IdTpq) || empty($IdKelas)) {
                return $this->response->setJSON([]);
            }

            // Cek apakah user adalah Admin atau Operator
            $isAdmin = in_groups('Admin');
            $isOperator = in_groups('Operator');

            // Cek apakah user adalah Kepala Sekolah
            $jabatanData = $this->helpFunctionModel->getStrukturLembagaJabatan($IdGuru, $IdTpq);
            $isKepalaSekolah = false;
            if (!empty($jabatanData)) {
                foreach ($jabatanData as $jabatan) {
                    $jabatanArray = is_object($jabatan) ? (array)$jabatan : $jabatan;
                    if (isset($jabatanArray['NamaJabatan']) && $jabatanArray['NamaJabatan'] === 'Kepala TPQ') {
                        $isKepalaSekolah = true;
                        break;
                    }
                }
            }

            // Jika bukan Admin/Operator/Kepala Sekolah, validasi bahwa kelas tersebut diajar oleh guru
            if (!($isAdmin || $isOperator || $isKepalaSekolah) && !empty($IdGuru)) {
                $guruKelasData = $this->helpFunctionModel->getDataGuruKelas(
                    IdGuru: $IdGuru,
                    IdTpq: $IdTpq,
                    IdTahunAjaran: $IdTahunAjaran
                );

                // Cek apakah IdKelas ada di kelas yang diajar
                $isKelasDiajar = false;
                foreach ($guruKelasData as $gk) {
                    $gkArray = is_object($gk) ? (array)$gk : $gk;
                    $namaJabatan = $gkArray['NamaJabatan'] ?? '';
                    $idKelasGuru = $gkArray['IdKelas'] ?? '';
                    if (($namaJabatan === 'Guru Kelas' || $namaJabatan === 'Wali Kelas') && $idKelasGuru == $IdKelas) {
                        $isKelasDiajar = true;
                        break;
                    }
                }

                // Jika kelas tidak diajar, return empty
                if (!$isKelasDiajar) {
                    return $this->response->setJSON([]);
                }
            }

            $santriList = $this->santriBaruModel->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')->where([
                'tbl_santri_baru.IdTpq' => $IdTpq,
                'tbl_santri_baru.IdKelas' => $IdKelas,
                'tbl_santri_baru.Active' => 1
            ])->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')->findAll();

            // Tambahkan nama Ayah dan Ibu ke response
            foreach ($santriList as &$santri) {
                $santriArray = is_object($santri) ? (array)$santri : $santri;
                if (is_object($santri)) {
                    $santri->NamaAyah = $santri->NamaAyah ?? '';
                    $santri->NamaIbu = $santri->NamaIbu ?? '';
                    $santri->StatusAyah = $santri->StatusAyah ?? '';
                    $santri->StatusIbu = $santri->StatusIbu ?? '';
                } else {
                    $santri['NamaAyah'] = $santriArray['NamaAyah'] ?? '';
                    $santri['NamaIbu'] = $santriArray['NamaIbu'] ?? '';
                    $santri['StatusAyah'] = $santriArray['StatusAyah'] ?? '';
                    $santri['StatusIbu'] = $santriArray['StatusIbu'] ?? '';
                }
            }

            // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
            foreach ($santriList as &$santri) {
                $santriArray = is_object($santri) ? (array)$santri : $santri;
                $namaKelasOriginal = $santriArray['NamaKelas'] ?? '';

                if (!empty($namaKelasOriginal)) {
                    // Check MDA mapping dan convert nama kelas jika sesuai
                    $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $namaKelasConverted = $this->helpFunctionModel->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );

                    // Update nama kelas
                    if (is_object($santri)) {
                        $santri->NamaKelas = $namaKelasConverted;
                    } else {
                        $santri['NamaKelas'] = $namaKelasConverted;
                    }
                }
            }
            unset($santri); // Unset reference

            return $this->response->setJSON($santriList);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: getSantriByKelas - Error: ' . $e->getMessage());
            log_message('error', 'Rapor: getSantriByKelas - Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
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

            // Ambil tanggal dari query parameter
            $tanggalCetak = $this->request->getGet('tanggal');
            if (empty($tanggalCetak)) {
                // Default ke tanggal hari ini jika tidak ada
                $tanggalCetak = date('Y-m-d');
            }

            // Ambil parameter peringkat dari query parameter
            $batasanPeringkat = $this->request->getGet('peringkat');
            $batasanPeringkat = !empty($batasanPeringkat) ? (int)$batasanPeringkat : null;

            // Siapkan data untuk view rapor dengan tanggal yang sudah ditentukan
            $data = $this->prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester, $tanggalCetak, $batasanPeringkat);

            // Load view untuk PDF
            $html = view('backend/rapor/print', $data);

            // Setup Dompdf dan render PDF
            $dompdf = $this->setupDompdfConfig();
            $dompdf->loadHtml($html);
            // set paper folio atau F4
            $dompdf->setPaper([0, 0, 595.276, 935.433], 'portrait');
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

            // Disable output buffering untuk mengurangi memory
            if (ob_get_level()) {
                ob_end_clean();
            }

            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();

            // Ambil tanggal dari query parameter
            $tanggalCetak = $this->request->getGet('tanggal');
            if (empty($tanggalCetak)) {
                // Default ke tanggal hari ini jika tidak ada
                $tanggalCetak = date('Y-m-d');
            }

            // Ambil parameter peringkat dari query parameter
            $batasanPeringkat = $this->request->getGet('peringkat');
            $batasanPeringkat = !empty($batasanPeringkat) ? (int)$batasanPeringkat : null;

            // Ambil semua santri dalam kelas tersebut
            $listSantri = $this->santriBaruModel->where([
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'Active' => 1
            ])->findAll();

            if (empty($listSantri)) {
                throw new \Exception('Tidak ada santri dalam kelas ini');
            }

            $totalSantri = count($listSantri);
            log_message('info', "Rapor: printPdfBulk - Memproses {$totalSantri} santri untuk kelas {$IdKelas}");

            // Gunakan metode zip untuk stabilitas di webserver
            return $this->printPdfBulkZip($listSantri, $IdKelas, $IdTpq, $IdTahunAjaran, $semester, $tanggalCetak, $batasanPeringkat);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: printPdfBulk - Error: ' . $e->getMessage());
            log_message('error', 'Rapor: printPdfBulk - Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Print PDF bulk dengan metode zip (satu per satu lalu di-zip)
     * Lebih stabil untuk webserver karena memory usage lebih rendah
     */
    private function printPdfBulkZip($listSantri, $IdKelas, $IdTpq, $IdTahunAjaran, $semester, $tanggalCetak, $batasanPeringkat = null)
    {
        // Buat temporary directory untuk menyimpan PDF
        $tempDir = sys_get_temp_dir() . '/rapor_bulk_' . uniqid();
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $pdfFiles = [];
        $successCount = 0;
        $errorCount = 0;

        try {
            // Generate PDF satu per satu
            foreach ($listSantri as $index => $santri) {
                try {
                    log_message('info', "Rapor: printPdfBulkZip - Memproses santri " . ($index + 1) . " dari " . count($listSantri));

                    // Ambil data santri lengkap dengan nilai dan wali kelas
                    $santriData = $this->getSantriDataWithNilai($santri['IdSantri'], $IdTpq, $IdTahunAjaran, $semester);

                    if (!$santriData) {
                        $errorCount++;
                        log_message('warning', "Rapor: printPdfBulkZip - Data santri tidak ditemukan: " . $santri['IdSantri']);
                        continue;
                    }

                    // Siapkan data untuk view rapor
                    $data = $this->prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester, $tanggalCetak);

                    // Load view untuk PDF
                    $html = view('backend/rapor/print', $data);

                    // Setup Dompdf untuk setiap PDF
                    $dompdf = $this->setupDompdfConfig();
                    $dompdf->loadHtml($html);
                    // set paper folio atau F4
                    $dompdf->setPaper([0, 0, 595.276, 935.433], 'portrait');
                    $dompdf->render();

                    // Generate nama file untuk PDF dengan nama santri
                    // Format: NamaSantri_IdTahunAjaran_Semester.pdf
                    $namaSantri = $santriData['santri']['NamaSantri'];
                    // Sanitize nama santri untuk nama file (ganti spasi dan karakter khusus dengan _)
                    $namaSantriSanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', str_replace(' ', '_', $namaSantri));
                    $pdfFilename = $namaSantriSanitized . '_' . $IdTahunAjaran . '_' . $semester . '.pdf';
                    $pdfPath = $tempDir . '/' . $pdfFilename;

                    // Simpan PDF ke file
                    file_put_contents($pdfPath, $dompdf->output());

                    $pdfFiles[] = [
                        'path' => $pdfPath,
                        'name' => $pdfFilename
                    ];

                    $successCount++;

                    // Free memory
                    unset($dompdf, $html, $data, $santriData);
                    gc_collect_cycles();
                } catch (\Exception $e) {
                    $errorCount++;
                    log_message('error', "Rapor: printPdfBulkZip - Error untuk santri {$santri['IdSantri']}: " . $e->getMessage());
                    continue;
                }
            }

            if (empty($pdfFiles)) {
                throw new \Exception('Tidak ada PDF yang berhasil dibuat');
            }

            log_message('info', "Rapor: printPdfBulkZip - Berhasil membuat {$successCount} PDF, {$errorCount} error");

            // Buat ZIP file
            $zipFilename = $this->createZipFromPdfs($pdfFiles, $IdKelas, $IdTahunAjaran, $semester, $tempDir);

            // Cleanup temporary PDF files
            foreach ($pdfFiles as $pdfFile) {
                if (file_exists($pdfFile['path'])) {
                    @unlink($pdfFile['path']);
                }
            }

            // Output ZIP file
            if (file_exists($zipFilename)) {
                // Hapus semua output sebelumnya
                if (ob_get_level()) {
                    ob_end_clean();
                }

                // Set headers untuk download
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zipFilename) . '"');
                header('Content-Length: ' . filesize($zipFilename));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                header('X-Content-Type-Options: nosniff');

                // Read dan output file
                readfile($zipFilename);

                // Cleanup ZIP file setelah beberapa detik (background cleanup)
                // Jangan hapus langsung karena file sedang di-download
                register_shutdown_function(function () use ($zipFilename, $tempDir) {
                    sleep(2); // Tunggu 2 detik untuk memastikan download selesai
                    if (file_exists($zipFilename)) {
                        @unlink($zipFilename);
                    }
                    if (is_dir($tempDir)) {
                        @rmdir($tempDir);
                    }
                });

                exit();
            } else {
                throw new \Exception('Gagal membuat file ZIP');
            }
        } catch (\Exception $e) {
            // Cleanup on error
            foreach ($pdfFiles as $pdfFile) {
                if (file_exists($pdfFile['path'])) {
                    @unlink($pdfFile['path']);
                }
            }
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
            throw $e;
        }
    }

    /**
     * Buat ZIP file dari array PDF files
     */
    private function createZipFromPdfs($pdfFiles, $IdKelas, $IdTahunAjaran, $semester, $tempDir)
    {
        // Ambil nama kelas untuk nama file ZIP
        $namaKelas = $this->helpFunctionModel->getNamaKelas($IdKelas);
        // Maping MDA
        $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping(session()->get('IdTpq'), $namaKelas);
        $namaKelas = $this->helpFunctionModel->convertKelasToMda(
            $namaKelas,
            $mdaCheckResult['mappedMdaKelas']
        );
        // Sanitize nama kelas untuk nama file
        $namaKelas = preg_replace('/[^a-zA-Z0-9_-]/', '_', $namaKelas);

        $zipFilename = $tempDir . '/Rapor_Kelas_' . $namaKelas . '_' . $IdTahunAjaran . '_' . $semester . '.zip';

        // Cek apakah ZipArchive tersedia
        if (!class_exists('ZipArchive')) {
            throw new \Exception('Extension ZipArchive tidak tersedia di server');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Tidak dapat membuat file ZIP');
        }

        // Tambahkan setiap PDF ke ZIP
        foreach ($pdfFiles as $pdfFile) {
            if (file_exists($pdfFile['path'])) {
                $zip->addFile($pdfFile['path'], $pdfFile['name']);
            }
        }

        $zip->close();

        return $zipFilename;
    }

    /**
     * Hapus directory secara recursive
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
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

        // Cek apakah user adalah Admin atau Operator
        $isAdmin = in_groups('Admin');
        $isOperator = in_groups('Operator');

        // Cek apakah user adalah Wali Kelas (hanya jika bukan Admin/Operator)
        if (!empty($IdKelas) && !$isAdmin && !$isOperator) {
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

        // Ambil list kelas untuk dropdown
        $listKelas = [];

        // Jika Admin atau Operator, ambil semua kelas di TPQ
        if ($isAdmin || $isOperator) {
            $db = \Config\Database::connect();
            $builder = $db->table('tbl_kelas_santri ks');
            $builder->select('ks.IdKelas, k.NamaKelas');
            $builder->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas');
            $builder->where('ks.IdTpq', $IdTpq);
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
            $builder->where('ks.Status', 1);
            $builder->groupBy('ks.IdKelas, k.NamaKelas');
            $builder->orderBy('k.NamaKelas', 'ASC');
            $allKelas = $builder->get()->getResultArray();

            foreach ($allKelas as $kelas) {
                if (!empty($kelas['IdKelas'])) {
                    $listKelas[] = [
                        'IdKelas' => $kelas['IdKelas'],
                        'NamaKelas' => $kelas['NamaKelas'] ?? ''
                    ];
                }
            }
        }
        // Jika Wali Kelas, ambil hanya kelas yang diajar
        elseif (!empty($IdGuru)) {
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
                    $isWaliKelas = ($namaJabatan === 'Wali Kelas');
                    $guruPendampingList[] = [
                        'IdGuru' => $idGuru,
                        'Nama' => $namaGuruFormatted,
                        'NamaJabatan' => $namaJabatan,
                        'IsWaliKelas' => $isWaliKelas
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

    /**
     * Handle tanda tangan bulk wali kelas untuk semua santri dalam kelas
     */
    public function ttdBulkWalas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');
            
            // Baca data dari JSON body
            $jsonData = $this->request->getJSON(true);
            $IdKelas = $jsonData['IdKelas'] ?? $this->request->getPost('IdKelas');
            $semester = $jsonData['Semester'] ?? $this->request->getPost('Semester');

            if (empty($IdKelas) || empty($semester)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'IdKelas dan Semester harus diisi'
                ]);
            }

            // Cek permission: hanya Wali Kelas yang bisa tanda tangan
            $guruKelasPermission = $this->helpFunctionModel->checkGuruKelasPermission($IdTpq, $IdGuru, $IdKelas, $IdTahunAjaran);

            if (!$guruKelasPermission || $guruKelasPermission['NamaJabatan'] !== 'Wali Kelas') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk menandatangani sebagai wali kelas.'
                ]);
            }

            // Ambil semua santri di kelas
            $santriList = $this->santriBaruModel->where([
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'Active' => 1
            ])->findAll();

            if (empty($santriList)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada santri dalam kelas ini'
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($santriList as $santri) {
                $IdSantri = is_object($santri) ? $santri->IdSantri : $santri['IdSantri'];

                // Cek apakah signature sudah ada
                $existingSignature = $this->signatureModel->where([
                    'IdSantri' => $IdSantri,
                    'IdTpq' => $IdTpq,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdGuru' => $IdGuru,
                    'Semester' => $semester,
                    'JenisDokumen' => 'Rapor',
                    'SignatureData' => 'Walas',
                    'StatusValidasi' => 'Valid'
                ])->first();

                // Skip jika sudah ada
                if ($existingSignature) {
                    continue;
                }

                // Generate token unik
                $token = $this->generateUniqueToken();

                // Ambil IdKelas dari kelas data
                $kelasData = $this->helpFunctionModel->getIdKelasByTahunAjaranDanSemester($IdTpq, $IdTahunAjaran, $semester, $IdSantri);

                // Data untuk disimpan
                $signatureData = [
                    'Token' => $token,
                    'IdSantri' => $IdSantri,
                    'IdKelas' => !empty($kelasData) ? $kelasData[0]['IdKelas'] : $IdKelas,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'Semester' => $semester,
                    'IdGuru' => $IdGuru,
                    'IdTpq' => $IdTpq,
                    'JenisDokumen' => 'Rapor',
                    'SignatureData' => 'Walas',
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
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Gagal membuat QR Code untuk santri: {$IdSantri}";
                    }
                } else {
                    $errorCount++;
                    $errors[] = "Gagal menyimpan tanda tangan untuk santri: {$IdSantri}";
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Tanda tangan wali kelas berhasil dibuat untuk {$successCount} santri" . ($errorCount > 0 ? ". {$errorCount} gagal." : "."),
                'successCount' => $successCount,
                'errorCount' => $errorCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: ttdBulkWalas - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle tanda tangan bulk kepala sekolah untuk semua santri dalam kelas
     */
    public function ttdBulkKepsek()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');
            
            // Baca data dari JSON body
            $jsonData = $this->request->getJSON(true);
            $IdKelas = $jsonData['IdKelas'] ?? $this->request->getPost('IdKelas');
            $semester = $jsonData['Semester'] ?? $this->request->getPost('Semester');

            if (empty($IdKelas) || empty($semester)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'IdKelas dan Semester harus diisi'
                ]);
            }

            // Cek permission: hanya Kepala Sekolah yang bisa tanda tangan
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

            if (!$isKepalaSekolah) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk menandatangani sebagai kepala sekolah.'
                ]);
            }

            // Ambil semua santri di kelas
            $santriList = $this->santriBaruModel->where([
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'Active' => 1
            ])->findAll();

            if (empty($santriList)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada santri dalam kelas ini'
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($santriList as $santri) {
                $IdSantri = is_object($santri) ? $santri->IdSantri : $santri['IdSantri'];

                // Cek apakah signature sudah ada
                $existingSignature = $this->signatureModel->where([
                    'IdSantri' => $IdSantri,
                    'IdTpq' => $IdTpq,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdGuru' => $IdGuru,
                    'Semester' => $semester,
                    'JenisDokumen' => 'Rapor',
                    'SignatureData' => 'Kepsek',
                    'StatusValidasi' => 'Valid'
                ])->first();

                // Skip jika sudah ada
                if ($existingSignature) {
                    continue;
                }

                // Generate token unik
                $token = $this->generateUniqueToken();

                // Ambil IdKelas dari kelas data
                $kelasData = $this->helpFunctionModel->getIdKelasByTahunAjaranDanSemester($IdTpq, $IdTahunAjaran, $semester, $IdSantri);

                // Data untuk disimpan
                $signatureData = [
                    'Token' => $token,
                    'IdSantri' => $IdSantri,
                    'IdKelas' => !empty($kelasData) ? $kelasData[0]['IdKelas'] : $IdKelas,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'Semester' => $semester,
                    'IdGuru' => $IdGuru,
                    'IdTpq' => $IdTpq,
                    'JenisDokumen' => 'Rapor',
                    'SignatureData' => 'Kepsek',
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
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Gagal membuat QR Code untuk santri: {$IdSantri}";
                    }
                } else {
                    $errorCount++;
                    $errors[] = "Gagal menyimpan tanda tangan untuk santri: {$IdSantri}";
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Tanda tangan kepala sekolah berhasil dibuat untuk {$successCount} santri" . ($errorCount > 0 ? ". {$errorCount} gagal." : "."),
                'successCount' => $successCount,
                'errorCount' => $errorCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: ttdBulkKepsek - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batalkan/hapus tanda tangan bulk wali kelas untuk semua santri dalam kelas
     */
    public function cancelBulkWalas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');
            
            // Baca data dari JSON body
            $jsonData = $this->request->getJSON(true);
            $IdKelas = $jsonData['IdKelas'] ?? $this->request->getPost('IdKelas');
            $semester = $jsonData['Semester'] ?? $this->request->getPost('Semester');

            if (empty($IdKelas) || empty($semester)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'IdKelas dan Semester harus diisi'
                ]);
            }

            // Cek permission: hanya Wali Kelas yang bisa cancel
            $guruKelasPermission = $this->helpFunctionModel->checkGuruKelasPermission($IdTpq, $IdGuru, $IdKelas, $IdTahunAjaran);

            if (!$guruKelasPermission || $guruKelasPermission['NamaJabatan'] !== 'Wali Kelas') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk membatalkan tanda tangan sebagai wali kelas.'
                ]);
            }

            // Ambil semua santri di kelas
            $santriList = $this->santriBaruModel->where([
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'Active' => 1
            ])->findAll();

            if (empty($santriList)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada santri dalam kelas ini'
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($santriList as $santri) {
                $IdSantri = is_object($santri) ? $santri->IdSantri : $santri['IdSantri'];

                // Cari dan hapus signature wali kelas
                $signature = $this->signatureModel->where([
                    'IdSantri' => $IdSantri,
                    'IdTpq' => $IdTpq,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdGuru' => $IdGuru,
                    'Semester' => $semester,
                    'JenisDokumen' => 'Rapor',
                    'SignatureData' => 'Walas',
                    'StatusValidasi' => 'Valid'
                ])->first();

                if ($signature) {
                    // Hapus file QR code jika ada
                    if (!empty($signature['QrCode'])) {
                        $this->deleteQRCodeFile($signature['QrCode']);
                    }
                    
                    // Hapus signature dari database
                    if ($this->signatureModel->delete($signature['Id'])) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Gagal menghapus tanda tangan untuk santri: {$IdSantri}";
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Tanda tangan wali kelas berhasil dibatalkan untuk {$successCount} rapor" . ($errorCount > 0 ? ". {$errorCount} gagal." : "."),
                'successCount' => $successCount,
                'errorCount' => $errorCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: cancelBulkWalas - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batalkan/hapus tanda tangan bulk kepala sekolah untuk semua santri dalam kelas
     */
    public function cancelBulkKepsek()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');
            
            // Baca data dari JSON body
            $jsonData = $this->request->getJSON(true);
            $IdKelas = $jsonData['IdKelas'] ?? $this->request->getPost('IdKelas');
            $semester = $jsonData['Semester'] ?? $this->request->getPost('Semester');

            if (empty($IdKelas) || empty($semester)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'IdKelas dan Semester harus diisi'
                ]);
            }

            // Cek permission: hanya Kepala Sekolah yang bisa cancel
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

            if (!$isKepalaSekolah) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk membatalkan tanda tangan sebagai kepala sekolah.'
                ]);
            }

            // Ambil semua santri di kelas
            $santriList = $this->santriBaruModel->where([
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'Active' => 1
            ])->findAll();

            if (empty($santriList)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada santri dalam kelas ini'
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($santriList as $santri) {
                $IdSantri = is_object($santri) ? $santri->IdSantri : $santri['IdSantri'];

                // Cari dan hapus signature kepala sekolah
                $signature = $this->signatureModel->where([
                    'IdSantri' => $IdSantri,
                    'IdTpq' => $IdTpq,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdGuru' => $IdGuru,
                    'Semester' => $semester,
                    'JenisDokumen' => 'Rapor',
                    'SignatureData' => 'Kepsek',
                    'StatusValidasi' => 'Valid'
                ])->first();

                if ($signature) {
                    // Hapus file QR code jika ada
                    if (!empty($signature['QrCode'])) {
                        $this->deleteQRCodeFile($signature['QrCode']);
                    }
                    
                    // Hapus signature dari database
                    if ($this->signatureModel->delete($signature['Id'])) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Gagal menghapus tanda tangan untuk santri: {$IdSantri}";
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Tanda tangan kepala sekolah berhasil dibatalkan untuk {$successCount} rapor" . ($errorCount > 0 ? ". {$errorCount} gagal." : "."),
                'successCount' => $successCount,
                'errorCount' => $errorCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: cancelBulkKepsek - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman utama serah terima rapor
     */
    public function serahTerimaRapor($semester = null)
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdGuru = session()->get('IdGuru');

        // Default semester jika tidak ada
        if (empty($semester)) {
            $currentMonth = (int)date('m');
            $semester = ($currentMonth >= 7) ? 'Ganjil' : 'Genap';
        }

        // Cek apakah user adalah Admin atau Operator
        $isAdmin = in_groups('Admin');
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

        // Ambil data santri per kelas
        $dataSantri = [];
        if ($isAdmin || $isOperator || $isKepalaSekolah) {
            // Admin/Operator/Kepala Sekolah: ambil semua santri di TPQ
            $dataSantri = $this->santriBaruModel->GetDataSantriPerKelas($IdTahunAjaran, 0, null);
            // Filter berdasarkan IdTpq
            $dataSantri = array_filter($dataSantri, function ($santri) use ($IdTpq) {
                $santriArray = is_object($santri) ? (array)$santri : $santri;
                return ($santriArray['IdTpq'] ?? '') == $IdTpq;
            });
        } else {
            // Guru/Wali Kelas: ambil santri dari kelas yang diajar
            $dataSantri = $this->santriBaruModel->GetDataSantriPerKelas($IdTahunAjaran, 0, $IdGuru);
            // Filter berdasarkan IdTpq
            $dataSantri = array_filter($dataSantri, function ($santri) use ($IdTpq) {
                $santriArray = is_object($santri) ? (array)$santri : $santri;
                return ($santriArray['IdTpq'] ?? '') == $IdTpq;
            });
        }

        // Kelompokkan santri berdasarkan kelas (pastikan tidak ada duplikasi)
        $santriPerKelas = [];
        $dataKelas = [];
        $santriProcessed = []; // Track santri yang sudah diproses per kelas

        foreach ($dataSantri as $santri) {
            $santriArray = is_object($santri) ? (array)$santri : $santri;
            $idKelas = $santriArray['IdKelas'] ?? '';
            $namaKelas = $santriArray['NamaKelas'] ?? '';
            $idSantri = $santriArray['IdSantri'] ?? '';

            if (empty($idKelas) || empty($idSantri)) {
                continue;
            }

            // Cek apakah santri ini sudah ada di kelas ini (untuk menghindari duplikasi)
            $santriKey = $idKelas . '_' . $idSantri;
            if (isset($santriProcessed[$santriKey])) {
                continue; // Skip jika sudah diproses
            }
            $santriProcessed[$santriKey] = true;

            // Simpan kelas ke dataKelas
            if (!isset($dataKelas[$idKelas])) {
                $dataKelas[$idKelas] = $namaKelas;
            }

            // Kelompokkan santri per kelas
            if (!isset($santriPerKelas[$idKelas])) {
                $santriPerKelas[$idKelas] = [];
            }

            // Ambil status serah terima untuk setiap santri
            $statusSerahTerima = $this->serahTerimaRaporModel->getLatestStatus(
                $idSantri,
                $IdTahunAjaran,
                $semester
            );

            $santriArray['StatusSerahTerima'] = $statusSerahTerima;
            $santriPerKelas[$idKelas][] = $santriArray;
        }

        // Filter kelas aktif (bukan Alumni)
        $dataKelasFiltered = [];
        $santriPerKelasFiltered = [];

        foreach ($dataKelas as $idKelas => $namaKelas) {
            // Cek apakah kelas adalah Alumni (case-insensitive)
            // Filter: ALUMNI, Alumni, alumni, atau variasi lainnya
            $namaKelasTrimmed = trim($namaKelas);
            $namaKelasLower = strtolower($namaKelasTrimmed);
            if ($namaKelasLower === 'alumni' || strpos($namaKelasLower, 'alumni') !== false) {
                continue; // Skip kelas Alumni
            }

            $dataKelasFiltered[$idKelas] = $namaKelas;
            if (isset($santriPerKelas[$idKelas])) {
                $santriPerKelasFiltered[$idKelas] = $santriPerKelas[$idKelas];
            }
        }

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        foreach ($dataKelasFiltered as $idKelas => $namaKelas) {
            $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($IdTpq, $namaKelas);
            $dataKelasFiltered[$idKelas] = $this->helpFunctionModel->convertKelasToMda(
                $namaKelas,
                $mdaCheckResult['mappedMdaKelas']
            );
        }

        // Ambil data untuk view
        $data = [
            'page_title' => 'Serah Terima Rapor',
            'dataKelas' => $dataKelasFiltered,
            'santriPerKelas' => $santriPerKelasFiltered,
            'semester' => $semester,
            'IdTpq' => $IdTpq,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdGuru' => $IdGuru
        ];

        return view('backend/rapor/SerahTerimaRapor', $data);
    }

    /**
     * Get data serah terima untuk datatable (AJAX)
     */
    public function getSerahTerimaData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');

            // Cek apakah user adalah Admin atau Operator
            $isAdmin = in_groups('Admin');
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

            $filters = [
                'IdTpq' => $IdTpq,
                'idTahunAjaran' => $IdTahunAjaran
            ];

            // Filter dari request
            $IdKelas = $this->request->getPost('IdKelas');
            $semester = $this->request->getPost('Semester');
            $status = $this->request->getPost('Status');
            $transaksi = $this->request->getPost('Transaksi');

            // Jika bukan Admin/Operator/Kepala Sekolah, filter berdasarkan kelas yang diajar
            if (!($isAdmin || $isOperator || $isKepalaSekolah) && !empty($IdGuru)) {
                $guruKelasData = $this->helpFunctionModel->getDataGuruKelas(
                    IdGuru: $IdGuru,
                    IdTpq: $IdTpq,
                    IdTahunAjaran: $IdTahunAjaran
                );

                // Ambil IdKelas yang diajar oleh guru
                $listIdKelas = [];
                foreach ($guruKelasData as $gk) {
                    $gkArray = is_object($gk) ? (array)$gk : $gk;
                    $namaJabatan = $gkArray['NamaJabatan'] ?? '';
                    // Ambil kelas jika jabatan adalah Guru Kelas atau Wali Kelas
                    if ($namaJabatan === 'Guru Kelas' || $namaJabatan === 'Wali Kelas') {
                        if (!empty($gkArray['IdKelas'])) {
                            $listIdKelas[] = $gkArray['IdKelas'];
                        }
                    }
                }

                // Jika ada filter IdKelas dari request, pastikan kelas tersebut ada di list kelas yang diajar
                if (!empty($IdKelas)) {
                    if (!in_array($IdKelas, $listIdKelas)) {
                        // Jika kelas yang difilter tidak ada di kelas yang diajar, return empty
                        return $this->response->setJSON([
                            'status' => 'success',
                            'data' => []
                        ]);
                    }
                    $filters['IdKelas'] = $IdKelas;
                } else {
                    // Jika tidak ada filter IdKelas, filter berdasarkan semua kelas yang diajar
                    if (!empty($listIdKelas)) {
                        $filters['IdKelas'] = $listIdKelas; // Array untuk whereIn
                    } else {
                        // Jika tidak ada kelas yang diajar, return empty
                        return $this->response->setJSON([
                            'status' => 'success',
                            'data' => []
                        ]);
                    }
                }
            } else {
                // Admin/Operator/Kepala Sekolah: bisa filter atau tidak
                if (!empty($IdKelas)) {
                    $filters['IdKelas'] = $IdKelas;
                }
            }

            if (!empty($semester)) {
                $filters['Semester'] = $semester;
            }

            if (!empty($status)) {
                $filters['Status'] = $status;
            }

            if (!empty($transaksi)) {
                $filters['Transaksi'] = $transaksi;
            }

            $data = $this->serahTerimaRaporModel->getWithDetails($filters);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: getSerahTerimaData - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan transaksi serah terima rapor
     */
    public function saveSerahTerima()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');

            if (empty($IdGuru)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID Guru tidak ditemukan di session'
                ]);
            }

            // Ambil data dari POST
            $IdSantri = $this->request->getPost('IdSantri');
            $IdKelas = $this->request->getPost('IdKelas');
            $semester = $this->request->getPost('Semester');
            $namaWaliSantri = $this->request->getPost('NamaWaliSantri');
            $tanggalTransaksi = $this->request->getPost('TanggalTransaksi');
            
            // Handle upload foto bukti (opsional)
            $fotoBukti = null;
            $fileFoto = $this->request->getFile('FotoBukti');
            if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
                // Validasi ukuran file (max 5MB)
                if ($fileFoto->getSize() > 5 * 1024 * 1024) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Ukuran file foto terlalu besar. Maksimal 5MB'
                    ]);
                }

                // Validasi tipe file
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!in_array($fileFoto->getMimeType(), $allowedTypes)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, atau GIF'
                    ]);
                }

                // Generate nama file unik
                $newName = $fileFoto->getRandomName();
                $uploadPath = FCPATH . 'uploads/serah_terima_rapor/';
                
                // Buat folder jika belum ada
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Pindahkan file
                if ($fileFoto->move($uploadPath, $newName)) {
                    $fotoBukti = $newName;
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal mengupload foto bukti'
                    ]);
                }
            }

            // Validasi
            if (empty($IdSantri) || empty($IdKelas) || empty($semester) || empty($namaWaliSantri)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Semua field harus diisi'
                ]);
            }

            // Tentukan transaksi otomatis berdasarkan status saat ini
            $latestStatus = $this->serahTerimaRaporModel->getLatestStatus($IdSantri, $IdTahunAjaran, $semester);

            // Cek apakah sudah ada transaksi Serah
            $hasSerah = $this->serahTerimaRaporModel->hasSerahTransaction($IdSantri, $IdTahunAjaran, $semester);

            // Tentukan transaksi: jika belum ada serah -> Serah, jika sudah ada serah -> Terima
            if (!$hasSerah) {
                // Belum ada transaksi serah, lakukan transaksi Serah
                $transaksi = 'Serah';
            } else {
                // Sudah ada transaksi serah, lakukan transaksi Terima
                // Cek apakah sudah dikembalikan
                if ($latestStatus && $latestStatus['Status'] === 'Sudah Dikembalikan') {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Rapor sudah dikembalikan sebelumnya.'
                    ]);
                }
                $transaksi = 'Terima';
            }

            // Generate atau ambil HasKey untuk tahun ajaran ini
            // HasKey digunakan untuk tracking per tahun ajaran (bisa untuk semester Ganjil dan Genap)
            // Satu HasKey untuk satu IdSantri + IdTahunAjaran, digunakan untuk semua transaksi dalam tahun ajaran tersebut
            $hasKey = null;

            // Cek apakah sudah ada HasKey untuk IdSantri + IdTahunAjaran ini (tanpa filter Semester)
            // Ini memastikan satu HasKey untuk satu tahun ajaran, bisa digunakan untuk tracking semester Ganjil dan Genap
            $existingRecord = $this->serahTerimaRaporModel
                ->where('IdSantri', $IdSantri)
                ->where('idTahunAjaran', $IdTahunAjaran)
                ->where('(HasKey IS NOT NULL AND HasKey != \'\')', null, false)
                ->orderBy('TanggalTransaksi', 'ASC') // Ambil yang pertama (transaksi Serah pertama)
                ->first();

            if ($existingRecord && !empty($existingRecord['HasKey'])) {
                // Jika sudah ada HasKey untuk tahun ajaran ini, gunakan yang sama
                // Ini berlaku untuk semua transaksi (Serah dan Terima) dalam tahun ajaran yang sama
                $hasKey = $existingRecord['HasKey'];
            } else {
                // Jika belum ada HasKey, generate baru (hanya untuk transaksi Serah pertama)
                if ($transaksi === 'Serah') {
                    $hasKey = $this->generateUniqueHasKey();
                } else {
                    // Untuk transaksi Terima, jika belum ada HasKey, berarti belum ada transaksi Serah
                    // Ini seharusnya tidak terjadi karena logika controller sudah cek status sebelumnya
                    // Tapi untuk safety, return error
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Tidak dapat melakukan transaksi Terima karena belum ada transaksi Serah untuk tahun ajaran ini.'
                    ]);
                }
            }

            // Tentukan status
            $status = 'Belum Diserahkan';
            if ($transaksi === 'Serah') {
                $status = 'Sudah Diserahkan';
            } elseif ($transaksi === 'Terima') {
                $status = 'Sudah Dikembalikan';
            }

            // Format tanggal transaksi
            if (empty($tanggalTransaksi)) {
                // Jika kosong, gunakan tanggal hari ini dengan waktu 00:00:00
                $tanggalTransaksi = date('Y-m-d') . ' 00:00:00';
            } else {
                // Handle format date (YYYY-MM-DD) atau datetime (YYYY-MM-DD HH:mm:ss)
                if (strpos($tanggalTransaksi, ' ') === false && strpos($tanggalTransaksi, 'T') === false) {
                    // Format date saja: YYYY-MM-DD, tambahkan waktu 00:00:00
                    $tanggalTransaksi = $tanggalTransaksi . ' 00:00:00';
                } elseif (strpos($tanggalTransaksi, 'T') !== false) {
                    // Format datetime-local: YYYY-MM-DDTHH:mm
                    $tanggalTransaksi = str_replace('T', ' ', $tanggalTransaksi);
                    if (substr_count($tanggalTransaksi, ':') == 1) {
                        $tanggalTransaksi .= ':00'; // Tambahkan detik jika belum ada
                    }
                }
                // Pastikan format datetime
                $tanggalTransaksi = date('Y-m-d H:i:s', strtotime($tanggalTransaksi));
            }

            // Simpan data
            $data = [
                'IdSantri' => $IdSantri,
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'idTahunAjaran' => $IdTahunAjaran,
                'Semester' => $semester,
                'TanggalTransaksi' => $tanggalTransaksi,
                'Transaksi' => $transaksi,
                'IdGuru' => $IdGuru,
                'NamaWaliSantri' => $namaWaliSantri,
                'FotoBukti' => $fotoBukti,
                'HasKey' => $hasKey,
                'Status' => $status
            ];

            if ($this->serahTerimaRaporModel->save($data)) {
                $insertId = $this->serahTerimaRaporModel->getInsertID();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data serah terima rapor berhasil disimpan',
                    'data' => [
                        'id' => $insertId,
                        'HasKey' => $hasKey,
                        'status' => $status
                    ]
                ]);
            } else {
                $errors = $this->serahTerimaRaporModel->errors();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data',
                    'errors' => $errors
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Rapor: saveSerahTerima - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate unique HasKey untuk serah terima rapor
     */
    private function generateUniqueHasKey()
    {
        do {
            $hasKey = base64_encode(random_bytes(24));
            $hasKey = str_replace(['+', '/', '='], ['-', '_', ''], $hasKey); // URL-safe

        } while ($this->serahTerimaRaporModel->where('HasKey', $hasKey)->first());

        return $hasKey;
    }
    /**
     * Hapus transaksi serah terima rapor
     */
    public function deleteSerahTerima()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $id = $this->request->getPost('id');
            if (empty($id)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID transaksi tidak ditemukan'
                ]);
            }

            // Cek data transaksi
            $transaksi = $this->serahTerimaRaporModel->find($id);
            if (!$transaksi) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data transaksi tidak ditemukan'
                ]);
            }

            // Validasi: pastikan ini adalah transaksi terakhir untuk santri tersebut
            // Agar urutan status tetap terjaga
            $latestTransaction = $this->serahTerimaRaporModel
                ->where('IdSantri', $transaksi['IdSantri'])
                ->where('idTahunAjaran', $transaksi['idTahunAjaran'])
                ->where('Semester', $transaksi['Semester'])
                ->orderBy('TanggalTransaksi', 'DESC')
                ->orderBy('id', 'DESC')
                ->first();

            if ($latestTransaction && $latestTransaction['id'] != $id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Hanya transaksi terakhir yang dapat dihapus. Silakan hapus transaksi yang lebih baru terlebih dahulu.'
                ]);
            }

            // Hapus file foto bukti jika ada
            if (!empty($transaksi['FotoBukti'])) {
                $filePath = FCPATH . 'uploads/serah_terima_rapor/' . $transaksi['FotoBukti'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Hapus data
            if ($this->serahTerimaRaporModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Transaksi berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data dari database'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Rapor: deleteSerahTerima - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
