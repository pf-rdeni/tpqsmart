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

        $activeBlocks = $this->configModel->getActiveBlocks($link['Id']);

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
                'activeBlocks' => $activeBlocks
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
}
