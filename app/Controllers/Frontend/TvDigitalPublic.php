<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\Frontend\Infografis\InfografisLinkModel;
use App\Models\Frontend\Infografis\InfografisConfigModel;
use App\Models\Frontend\Infografis\InfografisGaleriModel;
use App\Models\Frontend\Infografis\InfografisAgendaModel;
use App\Models\HelpFunctionModel;
use App\Models\TpqModel;

class TvDigitalPublic extends BaseController
{
    protected $linkModel;
    protected $configModel;
    protected $galeriModel;
    protected $agendaModel;
    protected $helpFunctionModel;
    protected $tpqModel;
    protected $db;

    public function __construct()
    {
        $this->linkModel = new InfografisLinkModel();
        $this->configModel = new InfografisConfigModel();
        $this->galeriModel = new InfografisGaleriModel();
        $this->agendaModel = new InfografisAgendaModel();
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->tpqModel = new TpqModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Tampilan utama TV Digital
     */
    public function index($hashKey)
    {
        $link = $this->linkModel->getLinkByHash($hashKey);
        if (!$link) {
            return view('frontend/TvDigital/error', [
                'errorType' => 'invalid_token',
                'page_title' => 'Link TV Digital Tidak Valid'
            ]);
        }

        // Ambil list block yang aktif
        $activeBlocks = $this->configModel->getActiveBlocks($link['Id']);

        // Data Lembaga / TPQ
        $tpqName = "FKPQ";
        $logoUrl = base_url('/template/backend/dist/img/AdminLTELogo.png');
        $idTpq = $link['IdTpq'];

        if (!empty($idTpq) && $idTpq != '0') {
            $tpqData = $this->tpqModel->find($idTpq);
            if (!empty($tpqData)) {
                $tpqName = $tpqData['NamaTpq'];
                if (!empty($tpqData['LogoLembaga'])) {
                    $logoUrl = base_url('uploads/logo/' . $tpqData['LogoLembaga']);
                }
            }
        }

        $data = [
            'page_title' => 'TV Digital - ' . esc($tpqName),
            'link' => $link,
            'activeBlocks' => $activeBlocks,
            'tpqName' => $tpqName,
            'logoUrl' => $logoUrl,
        ];

        return view('frontend/TvDigital/tv', $data);
    }

    /**
     * API JSON: Mengambil data inisial & statistik ringkasan
     */
    public function getData($hashKey)
    {
        $link = $this->linkModel->getLinkByHash($hashKey);
        if (!$link) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Link tidak valid'])->setStatusCode(404);
        }

        $idTpq = $link['IdTpq'];
        $idTahunAjaran = $link['IdTahunAjaran'];

        // 1. Data TPQ / Profil
        $tpqName = "FKPQ";
        $logoUrl = base_url('/template/backend/dist/img/AdminLTELogo.png');
        $tpqAddress = "";
        
        if (!empty($idTpq) && $idTpq != '0') {
            $tpqData = $this->tpqModel->find($idTpq);
            if (!empty($tpqData)) {
                $tpqName = $tpqData['NamaTpq'];
                $tpqAddress = $tpqData['Alamat'] ?? '';
                if (!empty($tpqData['LogoLembaga'])) {
                    $logoUrl = base_url('uploads/logo/' . $tpqData['LogoLembaga']);
                }
            }
        }

        // 2. Statistik Santri & Guru
        $statSantri = $this->helpFunctionModel->getStatistikSantri($idTpq, $idTahunAjaran);
        $statGuru = $this->helpFunctionModel->getStatistikGuru($idTpq);
        
        $totalKelas = $this->helpFunctionModel->getTotalKelas($idTpq, $idTahunAjaran);

        // 3. Ringkasan Kehadiran Hari Ini
        $today = date('Y-m-d');
        
        // Absensi Santri Hari Ini
        $builderAbsensiSantri = $this->db->table('tbl_absensi_santri');
        if (!empty($idTpq) && $idTpq != '0') {
            $builderAbsensiSantri->where('IdTpq', $idTpq);
        }
        $absensiSantriToday = $builderAbsensiSantri->where('Tanggal', $today)
                                                  ->select('Kehadiran, COUNT(*) as count')
                                                  ->groupBy('Kehadiran')
                                                  ->get()
                                                  ->getResultArray();
                                                  
        $statAbsensiSantriToday = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alfa' => 0];
        foreach ($absensiSantriToday as $row) {
            $kehadiran = ucfirst(strtolower($row['Kehadiran']));
            if (isset($statAbsensiSantriToday[$kehadiran])) {
                $statAbsensiSantriToday[$kehadiran] = (int)$row['count'];
            }
        }

        // Absensi Guru Hari Ini (StatusKehadiran: Hadir, Izin, Sakit, Alfa dll)
        $builderAbsensiGuru = $this->db->table('tbl_absensi_guru ag');
        $builderAbsensiGuru->join('tbl_guru g', 'CONVERT(g.IdGuru USING utf8) = CONVERT(ag.IdGuru USING utf8)');
        if (!empty($idTpq) && $idTpq != '0') {
            $builderAbsensiGuru->where('g.IdTpq', $idTpq);
        }
        $absensiGuruToday = $builderAbsensiGuru->where('ag.TanggalOccurrence', $today)
                                              ->select('ag.StatusKehadiran, COUNT(*) as count')
                                              ->groupBy('ag.StatusKehadiran')
                                              ->get()
                                              ->getResultArray();
                                              
        $statAbsensiGuruToday = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alfa' => 0];
        foreach ($absensiGuruToday as $row) {
            $kehadiran = ucfirst(strtolower($row['StatusKehadiran']));
            if (isset($statAbsensiGuruToday[$kehadiran])) {
                $statAbsensiGuruToday[$kehadiran] = (int)$row['count'];
            }
        }

        // 4. Jika FKPQ (IdTpq = 0), Ambil statistik per TPQ
        $statistikPerTpq = [];
        if (empty($idTpq) || $idTpq == '0') {
            $statistikSantriPerTpq = $this->helpFunctionModel->getStatistikSantriPerTpq($idTahunAjaran);
            $statistikGuruPerTpq = $this->helpFunctionModel->getStatistikGuruPerTpq();
            
            // Gabungkan
            foreach ($statistikSantriPerTpq as $s) {
                $namaTpq = $s['NamaTpq'];
                $idTpqItem = $s['IdTpq'];
                $statistikPerTpq[$idTpqItem] = [
                    'IdTpq' => $idTpqItem,
                    'NamaTpq' => $namaTpq,
                    'Santri' => (int)$s['Total'],
                    'Guru' => 0
                ];
            }
            foreach ($statistikGuruPerTpq as $g) {
                $idTpqItem = $g['IdTpq'];
                if (isset($statistikPerTpq[$idTpqItem])) {
                    $statistikPerTpq[$idTpqItem]['Guru'] = (int)$g['Total'];
                }
            }
            $statistikPerTpq = array_values($statistikPerTpq);
            // Sort by Santri DESC
            usort($statistikPerTpq, function($a, $b) {
                return $b['Santri'] - $a['Santri'];
            });
        }

        // 5. Statistik Kehadiran Santri Per Kelas (Pekan Ini)
        $statistikKehadiranKelas = [];
        $ringkasanKehadiranMingguIni = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alfa' => 0];
        
        if (!empty($idTpq) && $idTpq != '0') {
            $dayOfWeek = date('N', strtotime($today)); // 1 (Senin) - 7 (Minggu)
            $startOfWeek = date('Y-m-d', strtotime($today . ' -' . ($dayOfWeek - 1) . ' days'));
            $endOfWeek = date('Y-m-d', strtotime($startOfWeek . ' +6 days'));

            $rawStats = $this->db->table('tbl_absensi_santri a')
                ->select('k.IdKelas, k.NamaKelas, a.Kehadiran, COUNT(*) as count')
                ->join('tbl_kelas k', 'k.IdKelas = a.IdKelas')
                ->where('a.IdTpq', $idTpq)
                ->where('a.Tanggal >=', $startOfWeek)
                ->where('a.Tanggal <=', $endOfWeek);
            
            if (!empty($idTahunAjaran)) {
                $rawStats->where('a.IdTahunAjaran', $idTahunAjaran);
            }
            
            $rawStatsResult = $rawStats->groupBy('k.IdKelas, k.NamaKelas, a.Kehadiran')
                                       ->get()
                                       ->getResultArray();

            $kelasStats = [];
            foreach ($rawStatsResult as $row) {
                $idKelas = $row['IdKelas'];
                if (!isset($kelasStats[$idKelas])) {
                    $namaKelasMapped = $this->helpFunctionModel->convertKelasToMda(
                        $row['NamaKelas'],
                        $this->helpFunctionModel->checkMdaKelasMapping($idTpq, $row['NamaKelas'])['mappedMdaKelas']
                    );
                    $kelasStats[$idKelas] = [
                        'IdKelas'   => $idKelas,
                        'NamaKelas' => $namaKelasMapped,
                        'Hadir'     => 0,
                        'Izin'      => 0,
                        'Sakit'     => 0,
                        'Alfa'      => 0
                    ];
                }
                $kehadiran = ucfirst(strtolower($row['Kehadiran'])); // Hadir, Izin, Sakit, Alfa
                if (isset($kelasStats[$idKelas][$kehadiran])) {
                    $kelasStats[$idKelas][$kehadiran] = (int)$row['count'];
                }
            }
            $statistikKehadiranKelas = array_values($kelasStats);

            foreach ($statistikKehadiranKelas as $ks) {
                $ringkasanKehadiranMingguIni['Hadir'] += $ks['Hadir'];
                $ringkasanKehadiranMingguIni['Izin'] += $ks['Izin'];
                $ringkasanKehadiranMingguIni['Sakit'] += $ks['Sakit'];
                $ringkasanKehadiranMingguIni['Alfa'] += $ks['Alfa'];
            }
        }

        $activeBlocks = $this->configModel->getActiveBlocks($link['Id']);

        // 6. Statistik Kelulusan Munaqosah per Tahun Ajaran
        $munaqosahGraduationStats = $this->getMunaqosahGraduationStats($idTpq);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'lembaga' => [
                    'nama' => $tpqName,
                    'logo' => $logoUrl,
                    'alamat' => $tpqAddress,
                    'isFkpq' => (empty($idTpq) || $idTpq == '0')
                ],
                'ringkasan' => [
                    'totalSantri' => (int)$statSantri['total'],
                    'totalGuru' => (int)$statGuru['total'],
                    'totalKelas' => (int)$totalKelas,
                    'santriLaki' => (int)$statSantri['laki_laki'],
                    'santriPerempuan' => (int)$statSantri['perempuan'],
                    'guruLaki' => (int)$statGuru['laki_laki'],
                    'guruPerempuan' => (int)$statGuru['perempuan'],
                    'absensiSantriToday' => $statAbsensiSantriToday,
                    'absensiGuruToday' => $statAbsensiGuruToday
                ],
                'santriPerKelas' => $statSantri['per_kelas'],
                'statistikPerTpq' => $statistikPerTpq,
                'slideshowInterval' => (int)$link['SlideshowInterval'],
                'refreshInterval' => (int)$link['RefreshInterval'],
                'activeBlocks' => $activeBlocks,
                'theme' => $link['Theme'] ?? 'dark',
                'statistikKehadiranKelas' => $statistikKehadiranKelas,
                'ringkasanKehadiranMingguIni' => $ringkasanKehadiranMingguIni,
                'munaqosahGraduationStats' => $munaqosahGraduationStats,
                'alumniList' => $this->getAlumniList($idTpq)
            ]
        ]);
    }

    /**
     * API JSON: Mendapatkan data grafik absensi santri (harian & bulanan)
     */
    public function getAbsensiSantri($hashKey)
    {
        $link = $this->linkModel->getLinkByHash($hashKey);
        if (!$link) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Link tidak valid'])->setStatusCode(404);
        }

        $idTpq = $link['IdTpq'];
        $idTahunAjaran = $link['IdTahunAjaran'];
        
        // 1. Data Absensi Harian (Minggu Kemarin & Minggu Ini)
        // Senin - Sabtu
        $today = date('Y-m-d');
        $dayOfWeek = date('N', strtotime($today)); // 1 (Senin) s/d 7 (Minggu)
        
        // Senin minggu ini
        $startThisWeek = date('Y-m-d', strtotime('last monday', strtotime($today . ' +1 day')));
        $endThisWeek = date('Y-m-d', strtotime($startThisWeek . ' +5 days')); // s/d Sabtu
        
        // Senin minggu lalu
        $startLastWeek = date('Y-m-d', strtotime($startThisWeek . ' -7 days'));
        $endLastWeek = date('Y-m-d', strtotime($startLastWeek . ' +5 days'));

        $harianThisWeek = $this->queryKehadiranHarian($idTpq, $startThisWeek, $endThisWeek);
        $harianLastWeek = $this->queryKehadiranHarian($idTpq, $startLastWeek, $endLastWeek);

        // 2. Data Absensi Bulanan (30 hari terakhir)
        $startMonth = date('Y-m-d', strtotime('-30 days'));
        $bulanan = $this->queryKehadiranHarian($idTpq, $startMonth, $today);

        // 3. Data Kehadiran Per Kelas Per Hari (2 Minggu)
        $absensiModel = new \App\Models\AbsensiModel();
        
        $dayOfWeekNum = (int)date('N', strtotime($today));
        $subDays = $dayOfWeekNum - 1;
        $currentWeekMonday = date('Y-m-d', strtotime("$today - $subDays days"));
        $previousWeekMonday = date('Y-m-d', strtotime("$currentWeekMonday - 7 days"));
        $currentWeekSunday = date('Y-m-d', strtotime("$currentWeekMonday + 6 days"));
        
        $startDate = $previousWeekMonday;
        $endDate = $currentWeekSunday;
        
        $kehadiranData = $absensiModel->getKehadiranPerKelasPerHari($idTpq, $startDate, $endDate, $idTahunAjaran);
        
        // Ambil daftar IdKelas yang unik dari data kehadiran
        $kelasIds = [];
        foreach ($kehadiranData as $tanggal => $kelasData) {
            if (is_array($kelasData)) {
                foreach ($kelasData as $idKelas => $count) {
                    if (!in_array($idKelas, $kelasIds)) {
                        $kelasIds[] = $idKelas;
                    }
                }
            }
        }

        // Jika tidak ada data kehadiran, ambil semua kelas dari TPQ
        if (empty($kelasIds)) {
            $kelasList = $this->helpFunctionModel->getListKelas($idTpq, $idTahunAjaran, null, null, true);
            foreach ($kelasList as $kelas) {
                $idKelas = is_array($kelas) ? ($kelas['IdKelas'] ?? 0) : ($kelas->IdKelas ?? 0);
                if ($idKelas && !in_array($idKelas, $kelasIds)) {
                    $kelasIds[] = $idKelas;
                }
            }
        }

        // Ambil nama kelas dari database
        $kelasMap = [];
        if (!empty($kelasIds)) {
            $builder = $this->db->table('tbl_kelas');
            $builder->select('IdKelas, NamaKelas');
            $builder->whereIn('IdKelas', $kelasIds);
            $kelasList = $builder->get()->getResultArray();

            foreach ($kelasList as $kelas) {
                $idKelas = $kelas['IdKelas'] ?? 0;
                $namaKelas = $kelas['NamaKelas'] ?? '';

                // Konversi nama kelas ke MDA jika perlu
                $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($idTpq, $namaKelas);
                $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda($namaKelas, $mdaCheckResult['mappedMdaKelas']);

                $kelasMap[$idKelas] = $namaKelasDisplay;
            }
        }

        // Generate semua tanggal dalam periode
        $tanggalList = [];
        $currentDate = new \DateTime($startDate);
        $endDateTime = new \DateTime($endDate);

        while ($currentDate <= $endDateTime) {
            $tanggalStr = $currentDate->format('Y-m-d');
            $tanggalList[] = $tanggalStr;
            $currentDate->modify('+1 day');
        }

        // Format data untuk chart: setiap kelas menjadi satu dataset
        $kelasDatasets = [];
        foreach ($kelasMap as $idKelas => $namaKelas) {
            $data = [];
            foreach ($tanggalList as $tanggal) {
                // Ambil count untuk tanggal dan kelas ini
                $count = isset($kehadiranData[$tanggal][$idKelas]) ? (int)$kehadiranData[$tanggal][$idKelas] : 0;
                $data[] = $count;
            }

            $kelasDatasets[] = [
                'label' => $namaKelas,
                'data' => $data,
                'IdKelas' => $idKelas
            ];
        }

        // Format label tanggal (format: d/m)
        $kelasLabels = [];
        foreach ($tanggalList as $tanggal) {
            $dateObj = new \DateTime($tanggal);
            $kelasLabels[] = $dateObj->format('d/m');
        }

        // Generate data absensi harian per kelas (Minggu Berjalan - Senin s/d Sabtu)
        $currentWeekDates = [];
        $currentDate = new \DateTime($currentWeekMonday);
        for ($i = 0; $i < 6; $i++) {
            $currentWeekDates[] = $currentDate->format('Y-m-d');
            $currentDate->modify('+1 day');
        }

        $harianPerKelasDatasets = [];
        foreach ($kelasMap as $idKelas => $namaKelas) {
            $data = [];
            foreach ($currentWeekDates as $tanggal) {
                $count = isset($kehadiranData[$tanggal][$idKelas]) ? (int)$kehadiranData[$tanggal][$idKelas] : 0;
                $data[] = $count;
            }

            $harianPerKelasDatasets[] = [
                'label' => $namaKelas,
                'data' => $data,
                'IdKelas' => $idKelas
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'mingguIni' => $harianThisWeek,
                'mingguLalu' => $harianLastWeek,
                'bulanan' => $bulanan,
                'kehadiranPerKelas' => [
                    'labels' => $kelasLabels,
                    'datasets' => $kelasDatasets
                ],
                'harianPerKelas' => [
                    'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    'datasets' => $harianPerKelasDatasets
                ]
            ]
        ]);
    }

    /**
     * Helper untuk query kehadiran harian santri
     */
    private function queryKehadiranHarian($idTpq, $startDate, $endDate)
    {
        $builder = $this->db->table('tbl_absensi_santri')
                            ->select('Tanggal, Kehadiran, COUNT(*) as count')
                            ->where('Tanggal >=', $startDate)
                            ->where('Tanggal <=', $endDate);
                            
        if (!empty($idTpq) && $idTpq != '0') {
            $builder->where('IdTpq', $idTpq);
        }
        
        $result = $builder->groupBy('Tanggal, Kehadiran')
                          ->orderBy('Tanggal', 'ASC')
                          ->get()
                          ->getResultArray();

        // Format data day-by-day
        $formatted = [];
        foreach ($result as $row) {
            $tgl = $row['Tanggal'];
            $status = ucfirst(strtolower($row['Kehadiran']));
            if (!isset($formatted[$tgl])) {
                $formatted[$tgl] = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alfa' => 0];
            }
            if (isset($formatted[$tgl][$status])) {
                $formatted[$tgl][$status] = (int)$row['count'];
            }
        }

        // Pastikan semua tanggal terisi
        $current = $startDate;
        $final = [];
        while ($current <= $endDate) {
            $final[$current] = $formatted[$current] ?? ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alfa' => 0];
            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }

        return $final;
    }

    /**
     * API JSON: Mendapatkan data grafik absensi guru
     */
    public function getAbsensiGuru($hashKey)
    {
        $link = $this->linkModel->getLinkByHash($hashKey);
        if (!$link) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Link tidak valid'])->setStatusCode(404);
        }

        $idTpq = $link['IdTpq'];
        
        // 1. Data Absensi Harian (Minggu Kemarin & Minggu Ini)
        $today = date('Y-m-d');
        $startThisWeek = date('Y-m-d', strtotime('last monday', strtotime($today . ' +1 day')));
        $endThisWeek = date('Y-m-d', strtotime($startThisWeek . ' +5 days'));
        
        $startLastWeek = date('Y-m-d', strtotime($startThisWeek . ' -7 days'));
        $endLastWeek = date('Y-m-d', strtotime($startLastWeek . ' +5 days'));

        $harianThisWeek = $this->queryKehadiranHarianGuru($idTpq, $startThisWeek, $endThisWeek);
        $harianLastWeek = $this->queryKehadiranHarianGuru($idTpq, $startLastWeek, $endLastWeek);

        // 2. Data Absensi Bulanan (30 hari terakhir)
        $startMonth = date('Y-m-d', strtotime('-30 days'));
        $bulanan = $this->queryKehadiranHarianGuru($idTpq, $startMonth, $today);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'mingguIni' => $harianThisWeek,
                'mingguLalu' => $harianLastWeek,
                'bulanan' => $bulanan
            ]
        ]);
    }

    /**
     * Helper untuk query kehadiran harian guru
     */
    private function queryKehadiranHarianGuru($idTpq, $startDate, $endDate)
    {
        $builder = $this->db->table('tbl_absensi_guru ag')
                            ->join('tbl_guru g', 'CONVERT(g.IdGuru USING utf8) = CONVERT(ag.IdGuru USING utf8)')
                            ->select('ag.TanggalOccurrence as Tanggal, ag.StatusKehadiran as Kehadiran, COUNT(*) as count')
                            ->where('ag.TanggalOccurrence >=', $startDate)
                            ->where('ag.TanggalOccurrence <=', $endDate);
                            
        if (!empty($idTpq) && $idTpq != '0') {
            $builder->where('g.IdTpq', $idTpq);
        }
        
        $result = $builder->groupBy('ag.TanggalOccurrence, ag.StatusKehadiran')
                          ->orderBy('ag.TanggalOccurrence', 'ASC')
                          ->get()
                          ->getResultArray();

        // Format data day-by-day
        $formatted = [];
        foreach ($result as $row) {
            $tgl = $row['Tanggal'];
            $status = ucfirst(strtolower($row['Kehadiran']));
            if (!isset($formatted[$tgl])) {
                $formatted[$tgl] = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alfa' => 0];
            }
            if (isset($formatted[$tgl][$status])) {
                $formatted[$tgl][$status] = (int)$row['count'];
            }
        }

        // Pastikan semua tanggal terisi
        $current = $startDate;
        $final = [];
        while ($current <= $endDate) {
            $final[$current] = $formatted[$current] ?? ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alfa' => 0];
            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }

        return $final;
    }

    /**
     * API JSON: Mengambil foto galeri
     */
    public function getGaleri($hashKey)
    {
        $link = $this->linkModel->getLinkByHash($hashKey);
        if (!$link) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Link tidak valid'])->setStatusCode(404);
        }

        $galeri = $this->galeriModel->getActiveGaleri($link['IdTpq'], 12);
        
        // Format path lengkap
        foreach ($galeri as &$g) {
            $g['FotoUrl'] = base_url('uploads/galeri/' . $g['NamaFile']);
            $g['TanggalFormatted'] = date('d M Y', strtotime($g['TanggalKegiatan']));
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $galeri
        ]);
    }

    /**
     * API JSON: Mengambil agenda mendatang
     */
    public function getAgenda($hashKey)
    {
        $link = $this->linkModel->getLinkByHash($hashKey);
        if (!$link) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Link tidak valid'])->setStatusCode(404);
        }

        $agenda = $this->agendaModel->getUpcomingAgenda($link['IdTpq'], 8);
        
        foreach ($agenda as &$a) {
            $a['TanggalFormatted'] = date('d M Y', strtotime($a['TanggalMulai']));
            if (!empty($a['TanggalSelesai']) && $a['TanggalSelesai'] != $a['TanggalMulai']) {
                $a['TanggalFormatted'] .= ' - ' . date('d M Y', strtotime($a['TanggalSelesai']));
            }
            
            // Format jam
            $a['JamFormatted'] = "";
            if (!empty($a['JamMulai'])) {
                $a['JamFormatted'] = substr($a['JamMulai'], 0, 5);
                if (!empty($a['JamSelesai'])) {
                    $a['JamFormatted'] .= ' - ' . substr($a['JamSelesai'], 0, 5) . ' WIB';
                } else {
                    $a['JamFormatted'] .= ' WIB - Selesai';
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $agenda
        ]);
    }

    /**
     * Mengambil statistik kelulusan Munaqosah per Tahun Ajaran
     */
    private function getMunaqosahGraduationStats($idTpq)
    {
        $taBuilder = $this->db->table('tbl_kelas_santri')
            ->select('DISTINCT(IdTahunAjaran) as IdTahunAjaran')
            ->orderBy('IdTahunAjaran', 'DESC')
            ->limit(5);
        
        if (!empty($idTpq) && $idTpq != '0') {
            $taBuilder->where('IdTpq', $idTpq);
        }
        $taRows = $taBuilder->get()->getResultArray();
        
        // Sort chronologically (oldest to newest)
        usort($taRows, function($a, $b) {
            return strcmp($a['IdTahunAjaran'], $b['IdTahunAjaran']);
        });
        
        $stats = [];
        foreach ($taRows as $taRow) {
            $ta = $taRow['IdTahunAjaran'];
            if (empty($ta)) continue;
            
            // Get threshold
            $threshold = 65.0;
            if (!empty($idTpq) && $idTpq != '0') {
                $conf = $this->db->table('tbl_munaqosah_konfigurasi')
                    ->where('IdTpq', $idTpq)
                    ->where('SettingKey', 'KelulusanThreshold')
                    ->get()
                    ->getRowArray();
                if ($conf && isset($conf['SettingValue']) && is_numeric($conf['SettingValue'])) {
                    $threshold = (float)$conf['SettingValue'];
                } else {
                    // Try global fallback '0'
                    $confGlobal = $this->db->table('tbl_munaqosah_konfigurasi')
                        ->where('IdTpq', '0')
                        ->where('SettingKey', 'KelulusanThreshold')
                        ->get()
                        ->getRowArray();
                    if ($confGlobal && isset($confGlobal['SettingValue']) && is_numeric($confGlobal['SettingValue'])) {
                        $threshold = (float)$confGlobal['SettingValue'];
                    } else {
                        // Try default
                        $confDefault = $this->db->table('tbl_munaqosah_konfigurasi')
                            ->where('IdTpq', 'default')
                            ->where('SettingKey', 'KelulusanThreshold')
                            ->get()
                            ->getRowArray();
                        if ($confDefault && isset($confDefault['SettingValue']) && is_numeric($confDefault['SettingValue'])) {
                            $threshold = (float)$confDefault['SettingValue'];
                        }
                    }
                }
            }
            
            // Get participants
            $regBuilder = $this->db->table('tbl_munaqosah_registrasi_uji r')
                ->select('r.NoPeserta, r.IdSantri, r.IdTpq, r.TypeUjian')
                ->where('r.IdTahunAjaran', $ta)
                ->where('r.TypeUjian', 'munaqosah');
            if (!empty($idTpq) && $idTpq != '0') {
                $regBuilder->where('r.IdTpq', $idTpq);
            }
            $registrasi = $regBuilder->groupBy('r.NoPeserta, r.IdSantri, r.IdTpq, r.TypeUjian')->get()->getResultArray();
            
            if (empty($registrasi)) {
                $stats[] = [
                    'TahunAjaran' => $ta,
                    'Peserta' => 0,
                    'Lulus' => 0,
                    'TidakLulus' => 0,
                    'Persentase' => 0
                ];
                continue;
            }
            
            $noPesertaList = array_column($registrasi, 'NoPeserta');
            
            // Get scores
            $nilaiRows = $this->db->table('tbl_munaqosah_nilai')
                ->select('NoPeserta, IdKategoriMateri, Nilai, TypeUjian')
                ->where('IdTahunAjaran', $ta)
                ->where('TypeUjian', 'munaqosah')
                ->whereIn('NoPeserta', $noPesertaList)
                ->get()
                ->getResultArray();
                
            // Get weights (bobot)
            $bobotRows = $this->db->table('tbl_munaqosah_bobot_nilai')
                ->select('IdKategoriMateri, NilaiBobot')
                ->where('IdTahunAjaran', $ta)
                ->get()
                ->getResultArray();
            if (empty($bobotRows)) {
                $bobotRows = $this->db->table('tbl_munaqosah_bobot_nilai')
                    ->select('IdKategoriMateri, NilaiBobot')
                    ->where('IdTahunAjaran', 'default')
                    ->get()
                    ->getResultArray();
            }
            $bobotMap = [];
            foreach ($bobotRows as $b) {
                $bobotMap[$b['IdKategoriMateri']] = (float)$b['NilaiBobot'];
            }
            
            // Group scores by NoPeserta, TypeUjian and Kategori
            $pesertaScores = [];
            foreach ($nilaiRows as $n) {
                $np = $n['NoPeserta'];
                $catId = $n['IdKategoriMateri'];
                $val = (float)$n['Nilai'];
                $type = strtolower($n['TypeUjian']);
                $pesertaScores[$np][$type][$catId][] = $val;
            }
            
            $passedCount = 0;
            $failedCount = 0;
            
            foreach ($registrasi as $p) {
                $np = $p['NoPeserta'];
                $pTpq = $p['IdTpq'];
                $pType = strtolower($p['TypeUjian']);
                
                // If aggregate FKPQ, get dynamic threshold for each TPQ
                $pThreshold = $threshold;
                if (empty($idTpq) || $idTpq == '0') {
                    $conf = $this->db->table('tbl_munaqosah_konfigurasi')
                        ->where('IdTpq', $pTpq)
                        ->where('SettingKey', 'KelulusanThreshold')
                        ->get()
                        ->getRowArray();
                    if ($conf && isset($conf['SettingValue']) && is_numeric($conf['SettingValue'])) {
                        $pThreshold = (float)$conf['SettingValue'];
                    } else {
                        // Try global fallback '0'
                        $confGlobal = $this->db->table('tbl_munaqosah_konfigurasi')
                            ->where('IdTpq', '0')
                            ->where('SettingKey', 'KelulusanThreshold')
                            ->get()
                            ->getRowArray();
                        if ($confGlobal && isset($confGlobal['SettingValue']) && is_numeric($confGlobal['SettingValue'])) {
                            $pThreshold = (float)$confGlobal['SettingValue'];
                        } else {
                            // Try default fallback
                            $confDefault = $this->db->table('tbl_munaqosah_konfigurasi')
                                ->where('IdTpq', 'default')
                                ->where('SettingKey', 'KelulusanThreshold')
                                ->get()
                                ->getRowArray();
                            if ($confDefault && isset($confDefault['SettingValue']) && is_numeric($confDefault['SettingValue'])) {
                                $pThreshold = (float)$confDefault['SettingValue'];
                            }
                        }
                    }
                }
                
                $weightedTotal = 0;
                if (isset($pesertaScores[$np][$pType])) {
                    foreach ($pesertaScores[$np][$pType] as $catId => $scores) {
                        $validScores = array_filter($scores, function($s) { return $s > 0; });
                        if (empty($validScores)) {
                            $avg = 0.0;
                        } else {
                            $avg = array_sum($validScores) / count($validScores);
                        }
                        $weight = $bobotMap[$catId] ?? 0.0;
                        $weightedTotal += ($avg * $weight) / 100;
                    }
                }
                
                if (round($weightedTotal, 2) >= $pThreshold) {
                    $passedCount++;
                } else {
                    $failedCount++;
                }
            }
            
            $totalPeserta = count($registrasi);
            $pct = $totalPeserta > 0 ? (int)round(($passedCount / $totalPeserta) * 100) : 0;
            
            $stats[] = [
                'TahunAjaran' => $ta,
                'Peserta' => $totalPeserta,
                'Lulus' => $passedCount,
                'TidakLulus' => $failedCount,
                'Persentase' => $pct
            ];
        }
        
        return $stats;
    }

    /**
     * Mengambil daftar Alumni (lulusan) TPQ dikelompokkan per Tahun Ajaran
     */
    private function getAlumniList($idTpq)
    {
        // 1. Find latest 5 years first
        $taBuilder = $this->db->table('tbl_kelas_santri')
            ->select('DISTINCT(IdTahunAjaran) as IdTahunAjaran')
            ->orderBy('IdTahunAjaran', 'DESC')
            ->limit(5);
        if (!empty($idTpq) && $idTpq != '0') {
            $taBuilder->where('IdTpq', $idTpq);
        }
        $taRows = $taBuilder->get()->getResultArray();
        
        // Sort chronologically
        usort($taRows, function($a, $b) {
            return strcmp($a['IdTahunAjaran'], $b['IdTahunAjaran']);
        });
        
        $allowedYears = array_column($taRows, 'IdTahunAjaran');
        
        // Get alumni from tbl_kelas_santri (IdKelas = 10) in those 5 years
        $builder1 = $this->db->table('tbl_kelas_santri ks')
            ->select('ks.IdSantri, ks.IdTahunAjaran, s.NamaSantri, s.JenisKelamin')
            ->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri')
            ->where('ks.IdKelas', 10);
        if (!empty($allowedYears)) {
            $builder1->whereIn('ks.IdTahunAjaran', $allowedYears);
        } else {
            $builder1->where('ks.IdTahunAjaran', 'none');
        }
        if (!empty($idTpq) && $idTpq != '0') {
            $builder1->where('ks.IdTpq', $idTpq);
        }
        $rows1 = $builder1->get()->getResultArray();
        
        $rows2 = [];
        foreach ($taRows as $taRow) {
            $ta = $taRow['IdTahunAjaran'];
            if (empty($ta)) continue;
            
            // Get threshold
            $threshold = 65.0;
            if (!empty($idTpq) && $idTpq != '0') {
                $conf = $this->db->table('tbl_munaqosah_konfigurasi')
                    ->where('IdTpq', $idTpq)
                    ->where('SettingKey', 'KelulusanThreshold')
                    ->get()
                    ->getRowArray();
                if ($conf && isset($conf['SettingValue']) && is_numeric($conf['SettingValue'])) {
                    $threshold = (float)$conf['SettingValue'];
                }
            }
            
            // Get participants
            $regBuilder = $this->db->table('tbl_munaqosah_registrasi_uji r')
                ->select('r.NoPeserta, r.IdSantri, r.IdTpq, s.NamaSantri, s.JenisKelamin')
                ->join('tbl_santri_baru s', 's.IdSantri = r.IdSantri')
                ->where('r.IdTahunAjaran', $ta)
                ->where('r.TypeUjian', 'munaqosah');
            if (!empty($idTpq) && $idTpq != '0') {
                $regBuilder->where('r.IdTpq', $idTpq);
            }
            $registrasi = $regBuilder->groupBy('r.NoPeserta, r.IdSantri, r.IdTpq, s.NamaSantri, s.JenisKelamin')->get()->getResultArray();
            
            if (empty($registrasi)) continue;
            
            $noPesertaList = array_column($registrasi, 'NoPeserta');
            
            // Get scores
            $nilaiRows = $this->db->table('tbl_munaqosah_nilai')
                ->select('NoPeserta, IdKategoriMateri, Nilai')
                ->where('IdTahunAjaran', $ta)
                ->where('TypeUjian', 'munaqosah')
                ->whereIn('NoPeserta', $noPesertaList)
                ->get()
                ->getResultArray();
                
            // Get weights (bobot)
            $bobotRows = $this->db->table('tbl_munaqosah_bobot_nilai')
                ->select('IdKategoriMateri, NilaiBobot')
                ->where('IdTahunAjaran', $ta)
                ->get()
                ->getResultArray();
            if (empty($bobotRows)) {
                $bobotRows = $this->db->table('tbl_munaqosah_bobot_nilai')
                    ->select('IdKategoriMateri, NilaiBobot')
                    ->where('IdTahunAjaran', 'default')
                    ->get()
                    ->getResultArray();
            }
            $bobotMap = [];
            foreach ($bobotRows as $b) {
                $bobotMap[$b['IdKategoriMateri']] = (float)$b['NilaiBobot'];
            }
            
            // Group scores
            $pesertaScores = [];
            foreach ($nilaiRows as $n) {
                $np = $n['NoPeserta'];
                $catId = $n['IdKategoriMateri'];
                $val = (float)$n['Nilai'];
                $pesertaScores[$np][$catId][] = $val;
            }
            
            foreach ($registrasi as $p) {
                $np = $p['NoPeserta'];
                $pTpq = $p['IdTpq'];
                
                $pThreshold = $threshold;
                if (empty($idTpq) || $idTpq == '0') {
                    $conf = $this->db->table('tbl_munaqosah_konfigurasi')
                        ->where('IdTpq', $pTpq)
                        ->where('SettingKey', 'KelulusanThreshold')
                        ->get()
                        ->getRowArray();
                    if ($conf && isset($conf['SettingValue']) && is_numeric($conf['SettingValue'])) {
                        $pThreshold = (float)$conf['SettingValue'];
                    }
                }
                
                $weightedTotal = 0;
                if (isset($pesertaScores[$np])) {
                    foreach ($pesertaScores[$np] as $catId => $scores) {
                        $validScores = array_filter($scores, function($s) { return $s > 0; });
                        $avg = empty($validScores) ? 0.0 : array_sum($validScores) / count($validScores);
                        $weight = $bobotMap[$catId] ?? 0.0;
                        $weightedTotal += ($avg * $weight) / 100;
                    }
                }
                
                if (round($weightedTotal, 2) >= $pThreshold) {
                    $rows2[] = [
                        'IdSantri' => $p['IdSantri'],
                        'IdTahunAjaran' => $ta,
                        'NamaSantri' => $p['NamaSantri'],
                        'JenisKelamin' => $p['JenisKelamin']
                    ];
                }
            }
        }
        
        // 3. Combine and de-duplicate
        $combined = [];
        foreach ($rows1 as $r) {
            $key = $r['IdSantri'] . '_' . $r['IdTahunAjaran'];
            $combined[$key] = [
                'NamaSantri' => $r['NamaSantri'],
                'JenisKelamin' => $r['JenisKelamin'],
                'IdTahunAjaran' => $r['IdTahunAjaran']
            ];
        }
        foreach ($rows2 as $r) {
            $key = $r['IdSantri'] . '_' . $r['IdTahunAjaran'];
            if (!isset($combined[$key])) {
                $combined[$key] = [
                    'NamaSantri' => $r['NamaSantri'],
                    'JenisKelamin' => $r['JenisKelamin'],
                    'IdTahunAjaran' => $r['IdTahunAjaran']
                ];
            }
        }
        
        // Group by Year for frontend
        $grouped = [];
        foreach ($combined as $c) {
            $ta = $c['IdTahunAjaran'];
            if (!isset($grouped[$ta])) {
                $grouped[$ta] = [];
            }
            $grouped[$ta][] = [
                'NamaSantri' => $c['NamaSantri'],
                'JenisKelamin' => $c['JenisKelamin']
            ];
        }
        
        // Sort keys DESC (latest year first)
        krsort($grouped);
        
        $finalList = [];
        foreach ($grouped as $ta => $students) {
            $finalList[] = [
                'TahunAjaran' => $ta,
                'Total' => count($students),
                'Santri' => $students
            ];
        }
        
        return $finalList;
    }

    /**
     * API JSON: Mengambil data ulang tahun terdekat (Guru & Santri)
     */
    public function getUlangTahun($hashKey)
    {
        $link = $this->linkModel->getLinkByHash($hashKey);
        if (!$link) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Link tidak valid'])->setStatusCode(404);
        }

        $idTpq = $link['IdTpq'];

        // 1. Ambil data santri aktif
        $santriList = $this->db->table('tbl_santri_baru s')
                              ->select('s.NamaSantri as Nama, s.TanggalLahirSantri as TanggalLahir, s.PhotoProfil as Photo, k.NamaKelas, s.JenisKelamin')
                              ->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left')
                              ->where('s.IdTpq', $idTpq)
                              ->where('s.Active', 1)
                              ->where('s.TanggalLahirSantri IS NOT NULL')
                              ->get()
                              ->getResultArray();

        // 2. Ambil data guru aktif
        $guruList = $this->db->table('tbl_guru')
                            ->select('Nama as Nama, TanggalLahir, LinkPhoto as Photo, JenisKelamin')
                            ->where('IdTpq', $idTpq)
                            ->where('Status', 'Aktif')
                            ->where('TanggalLahir IS NOT NULL')
                            ->get()
                            ->getResultArray();

        $closestSantri = $this->calculateUpcomingBirthdays($santriList, 5, true, $idTpq);
        $closestGuru = $this->calculateUpcomingBirthdays($guruList, 5, false, $idTpq);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'santri' => $closestSantri,
                'guru' => $closestGuru
            ]
        ]);
    }

    /**
     * Helper untuk menghitung sisa hari ulang tahun berikutnya
     */
    private function calculateUpcomingBirthdays($people, $limit = 5, $isSantri = true, $idTpq = 0)
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0); // reset time to avoid partial days
        $todayYear = (int)$today->format('Y');

        $monthsIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $list = [];
        foreach ($people as $person) {
            $dobStr = $person['TanggalLahir'] ?? '';
            if (empty($dobStr) || $dobStr === '0000-00-00') continue;

            try {
                $dob = new \DateTime($dobStr);
                $nextBday = new \DateTime($dob->format("Y-m-d"));
                $nextBday->setDate($todayYear, (int)$dob->format('m'), (int)$dob->format('d'));
                $nextBday->setTime(0, 0, 0);

                // If birthday has passed this year, it falls on next year
                if ($nextBday < $today) {
                    $nextBday->modify('+1 year');
                }

                $diff = $today->diff($nextBday);
                $days = (int)$diff->days;

                // Format occurrence date: "18 Juli 2026"
                $day = $nextBday->format('j');
                $monthNum = (int)$nextBday->format('n');
                $year = $nextBday->format('Y');
                $person['TanggalUlangTahun'] = $day . ' ' . $monthsIndo[$monthNum] . ' ' . $year;

                $person['SisaHari'] = $days;
                $person['Kategori'] = $isSantri ? 'Santri' : 'Guru';

                // Format Photo URL (tanpa konversi kelas di sini untuk menghindari N+1 query)
                if ($isSantri) {
                    $defaultAvatar = base_url('images/' . (strtolower($person['JenisKelamin'] ?? '') === 'laki-laki' ? 'putra.png' : 'putri.png'));
                    $person['PhotoUrl'] = !empty($person['Photo']) ? base_url('uploads/santri/' . $person['Photo']) : $defaultAvatar;
                } else {
                    $defaultAvatar = base_url('images/' . (strtolower($person['JenisKelamin'] ?? '') === 'laki-laki' ? 'putra.png' : 'putri.png'));
                    $person['PhotoUrl'] = !empty($person['Photo']) ? base_url('uploads/profil/user/' . $person['Photo']) : $defaultAvatar;
                }

                $list[] = $person;
            } catch (\Exception $e) {
                // Ignore invalid date strings
            }
        }

        // Sort by sisa hari ascending
        usort($list, function($a, $b) {
            return $a['SisaHari'] <=> $b['SisaHari'];
        });

        // Ambil hanya $limit teratas DULU, baru konversi kelas MDA (hemat query, hindari N+1)
        $sliced = array_slice($list, 0, $limit);

        // Konversi kelas MDA hanya untuk santri yang masuk hasil, dengan cache per nama kelas unik
        if ($isSantri && !empty($sliced)) {
            $kelasCache = [];
            foreach ($sliced as &$person) {
                $namaKelas = $person['NamaKelas'] ?? '-';
                if (!empty($namaKelas) && $namaKelas !== '-') {
                    if (!isset($kelasCache[$namaKelas])) {
                        $mdaCheck = $this->helpFunctionModel->checkMdaKelasMapping($idTpq, $namaKelas);
                        $kelasCache[$namaKelas] = $this->helpFunctionModel->convertKelasToMda($namaKelas, $mdaCheck['mappedMdaKelas']);
                    }
                    $person['NamaKelas'] = $kelasCache[$namaKelas];
                }
            }
            unset($person);
        }

        return $sliced;
    }
}
