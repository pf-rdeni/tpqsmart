<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\Frontend\UjianMdta\UjianMdtaJadwalModel;
use App\Models\Frontend\UjianMdta\UjianMdtaSesiModel;
use App\Models\Frontend\UjianMdta\UjianMdtaJawabanModel;
use App\Models\Frontend\UjianMdta\UjianMdtaSoalSesiModel;
use App\Models\Frontend\UjianMdta\UjianMdtaPaketModel;

class UjianMdtaSantri extends BaseController
{
    protected $jadwalModel;
    protected $sesiModel;
    protected $jawabanModel;
    protected $soalSesiModel;
    protected $paketModel;
    protected $db;

    public function __construct()
    {
        $this->db          = \Config\Database::connect();
        $this->jadwalModel   = new UjianMdtaJadwalModel();
        $this->sesiModel     = new UjianMdtaSesiModel();
        $this->jawabanModel  = new UjianMdtaJawabanModel();
        $this->soalSesiModel = new UjianMdtaSoalSesiModel();
        $this->paketModel    = new UjianMdtaPaketModel();
    }

    /**
     * Dapatkan data santri dari session login yang aktif.
     */
    private function getSantriSession()
    {
        $idSantri = session()->get('IdSantri');
        $idTpq    = session()->get('IdTpq');
        $idKelas  = session()->get('IdKelas');

        // Jika data session santri belum lengkap, ambil dari NIK user yang sedang login (seperti Dashboard.php)
        if (empty($idSantri) || empty($idTpq) || empty($idKelas)) {
            $userNik = user()->nik ?? null;
            if (!empty($userNik)) {
                $santriModel = new \App\Models\SantriBaruModel();
                $santriData  = $santriModel->getSantriByNik($userNik);
                if (!empty($santriData)) {
                    $idSantri = $idSantri ?: $santriData['IdSantri'];
                    $idTpq    = $idTpq    ?: $santriData['IdTpq'];
                    $idKelas  = $idKelas  ?: ($santriData['IdKelas'] ?? '');
                    session()->set('IdSantri', $idSantri);
                    session()->set('IdTpq', $idTpq);
                    if ($idKelas) session()->set('IdKelas', $idKelas);
                }
            }
        }

        // Ambil kelas aktif dari tbl_kelas_santri
        if (!empty($idSantri)) {
            $idTahunAjaran = session()->get('IdTahunAjaran');
            $builder = $this->db->table('tbl_kelas_santri ks')
                ->select('ks.IdKelas, ks.IdTpq')
                ->where('ks.IdSantri', $idSantri);
            if ($idTahunAjaran) {
                $builder->where('ks.IdTahunAjaran', $idTahunAjaran);
            }
            $kelasSantri = $builder->get()->getRowArray();
            if (!empty($kelasSantri['IdKelas'])) {
                $idKelas = $kelasSantri['IdKelas'];
            }
            if (!empty($kelasSantri['IdTpq'])) {
                $idTpq = $kelasSantri['IdTpq'];
            }
        }

        return [
            'IdSantri' => (string)$idSantri,
            'IdTpq'    => (string)$idTpq,
            'IdKelas'  => (string)$idKelas,
        ];
    }

    /**
     * Halaman Utama Santri — Daftar Ujian MDTA Aktif yang tersedia untuk kelasnya.
     */
    public function daftarUjian()
    {
        $santri = $this->getSantriSession();

        if (empty($santri['IdKelas'])) {
            // Tampilkan view tanpa kelas atau warning
            $data = [
                'page_title' => 'Daftar Ujian MDTA',
                'jadwalList' => [],
                'warning'    => 'Data kelas santri belum terdaftar di sistem.',
            ];
            return view('backend/ujianMdta/santri/daftar_ujian', $data);
        }

        $jadwalList = $this->jadwalModel->getJadwalAktifBySantri($santri['IdKelas'], $santri['IdTpq']);

        // Tempelkan status sesi & riwayat pengerjaan santri untuk tiap jadwal
        foreach ($jadwalList as &$j) {
            $semuaSesi = $this->sesiModel->where('IdSantri', $santri['IdSantri'])
                                         ->where('IdJadwal', $j['id'])
                                         ->orderBy('id', 'ASC')
                                         ->findAll();

            $sesiTerakhir = $this->sesiModel->where('IdSantri', $santri['IdSantri'])
                                             ->where('IdJadwal', $j['id'])
                                             ->orderBy('id', 'DESC')
                                             ->first();

            $j['semuaSesi']        = $semuaSesi;
            $j['status_sesi']      = strtolower(trim($sesiTerakhir['StatusSesi'] ?? 'belum'));
            $j['token_sesi']       = $sesiTerakhir['TokenSesi'] ?? null;
            $j['nilai_akhir']      = $sesiTerakhir['NilaiAkhir'] ?? null;
            $j['attempt_ke']       = $sesiTerakhir['AttemptKe'] ?? 1;
            $j['sisa_waktu_detik'] = 0;

            if ($j['status_sesi'] === 'sedang' && !empty($j['token_sesi'])) {
                $j['sisa_waktu_detik'] = $this->sesiModel->getSisaWaktu($j['token_sesi']);
            }

            if (strtolower(trim($j['Status'] ?? 'aktif')) === 'pause') {
                $j['status_sesi'] = 'pause';
            }

            // Evaluasi hak & jadwal waktu remedial santri
            $canRemedial           = false;
            $remedialPendingActive = false;
            $remedialNotStarted    = false;
            $remedialExpired       = false;

            if ($sesiTerakhir && in_array($j['status_sesi'], ['selesai', 'timeout'])) {
                $bolehRemedial     = (int)($j['BolehRemedial'] ?? 0);
                $nilaiMin          = (float)($j['NilaiMinimum'] ?? 0);
                $nilaiAkhir        = (float)($sesiTerakhir['NilaiAkhir'] ?? 0);
                $currAttempt       = (int)($sesiTerakhir['AttemptKe'] ?? 1);
                $isRemedialAllowed = (int)($sesiTerakhir['IsRemedialAllowed'] ?? 0);
                $statusRemedial    = strtolower(trim($j['StatusRemedial'] ?? 'nonaktif'));

                if ($bolehRemedial && $nilaiAkhir < $nilaiMin) {
                    if ($isRemedialAllowed == 1 || $statusRemedial === 'aktif') {
                        $now = time();
                        $tMulai   = !empty($j['TanggalMulaiRemedial']) ? strtotime($j['TanggalMulaiRemedial']) : null;
                        $tSelesai = !empty($j['TanggalSelesaiRemedial']) ? strtotime($j['TanggalSelesaiRemedial']) : null;

                        if ($tMulai && $now < $tMulai) {
                            $remedialNotStarted = true;
                        } else if ($tSelesai && $now > $tSelesai) {
                            $remedialExpired = true;
                        } else {
                            $canRemedial = true;
                        }
                    } else {
                        $remedialPendingActive = true;
                    }
                }
            }
            $j['can_remedial']            = $canRemedial;
            $j['remedial_pending_active'] = $remedialPendingActive;
            $j['remedial_not_started']    = $remedialNotStarted;
            $j['remedial_expired']        = $remedialExpired;
        }

        $data = [
            'page_title' => 'Daftar Ujian MDTA',
            'jadwalList' => $jadwalList,
            'santri'     => $santri,
        ];
        return view('backend/ujianMdta/santri/daftar_ujian', $data);
    }

    /**
     * Santri klik "Mulai Ujian". Generate Sesi + Distribusi Soal Acak dari Paket.
     */
    public function mulaiUjian(int $idJadwal)
    {
        $santri = $this->getSantriSession();
        $jadwal = $this->jadwalModel->find($idJadwal);

        if (!$jadwal || !in_array(strtolower(trim($jadwal['Status'] ?? '')), ['aktif', 'pause'])) {
            return redirect()->to(base_url('backend/ujian-mdta/santri'))->with('error', 'Jadwal ujian tidak ditemukan atau tidak aktif.');
        }

        if (strtolower(trim($jadwal['Status'] ?? '')) === 'pause') {
            return redirect()->to(base_url('backend/ujian-mdta/santri'))->with('warning', 'Jadwal ujian ini saat ini sedang di-pause oleh pengawas ujian.');
        }

        // Cek sesi santri sebelumnya untuk jadwal ini
        $sesiTerakhir = $this->sesiModel->where('IdSantri', $santri['IdSantri'])
                                         ->where('IdJadwal', $idJadwal)
                                         ->orderBy('id', 'DESC')
                                         ->first();

        $attemptKe = 1;

        if ($sesiTerakhir) {
            // Sesi aktif/pause -> langsung arahkan ke lembar ujian
            if (in_array(strtolower(trim($sesiTerakhir['StatusSesi'])), ['sedang', 'pause'])) {
                return redirect()->to(base_url("backend/ujian-mdta/santri/ujian/{$sesiTerakhir['TokenSesi']}"));
            }

            // Sesi sudah selesai/timeout
            if (in_array($sesiTerakhir['StatusSesi'], ['selesai', 'timeout'])) {
                $bolehRemedial = (int)($jadwal['BolehRemedial'] ?? 0);
                $nilaiMin      = (float)($jadwal['NilaiMinimum'] ?? 0);
                $nilaiAkhir    = (float)($sesiTerakhir['NilaiAkhir'] ?? 0);
                $currAttempt   = (int)($sesiTerakhir['AttemptKe'] ?? 1);

                if ($nilaiAkhir >= $nilaiMin) {
                    return redirect()->to(base_url("backend/ujian-mdta/santri/hasil/{$sesiTerakhir['TokenSesi']}"))
                                     ->with('warning', "Selamat! Anda telah LULUS pada ujian ini dengan nilai {$nilaiAkhir}. Sesi ujian baru tidak dapat dibuka.");
                }

                if (!$bolehRemedial) {
                    return redirect()->to(base_url("backend/ujian-mdta/santri/hasil/{$sesiTerakhir['TokenSesi']}"))
                                     ->with('error', 'Ujian telah selesai dan pengawas/admin tidak mengaktifkan fitur remedial untuk jadwal ini.');
                }

                // Memenuhi syarat remedial -> lanjut ke attempt berikutnya
                $attemptKe = $currAttempt + 1;
            }
        }

        // Buat sesi ujian baru (atau remedial)
        $resSesi = $this->sesiModel->buatSesi($idJadwal, $santri['IdSantri'], $santri['IdTpq'], $attemptKe);
        if (!$resSesi) {
            return redirect()->to(base_url('backend/ujian-mdta/santri'))->with('error', 'Gagal membuat sesi ujian.');
        }

        $idSesi    = $resSesi['id'];
        $tokenSesi = $resSesi['token'];

        // Generate soal acak jika belum di-generate
        if (!$this->soalSesiModel->sudahDigenerate($idSesi)) {
            $genOk = $this->soalSesiModel->generateDistribusi($idSesi, $idJadwal);
            if (!$genOk) {
                return redirect()->to(base_url('backend/ujian-mdta/santri'))
                    ->with('error', 'Gagal mendistribusikan soal acak. Pastikan paket soal memiliki soal aktif yang cukup.');
            }
        }

        return redirect()->to(base_url("backend/ujian-mdta/santri/ujian/{$tokenSesi}"));
    }

    /**
     * Lembar CBT Ujian Santri — Timer, navigasi soal, auto-save jawaban.
     */
    public function lembarUjian(string $token)
    {
        $sesi = $this->sesiModel->getSesiByToken($token);
        if (!$sesi) {
            return redirect()->to(base_url('backend/ujian-mdta/santri'))->with('error', 'Sesi ujian tidak valid.');
        }

        if ($sesi['StatusSesi'] === 'selesai' || $sesi['StatusSesi'] === 'timeout') {
            return redirect()->to(base_url("backend/ujian-mdta/santri/hasil/{$token}"));
        }

        if ($sesi['StatusSesi'] === 'pause') {
            return redirect()->to(base_url('backend/ujian-mdta/santri'))
                ->with('warning', 'Ujian Anda saat ini sedang di-pause oleh pengawas ujian. Tombol Lanjutkan Ujian akan aktif otomatis setelah pengawas melanjutkan ujian.');
        }

        // Hitung sisa waktu pengerjaan
        $sisaWaktuDetik = $this->sesiModel->getSisaWaktu($token);
        if ($sisaWaktuDetik <= 0 && $sesi['StatusSesi'] === 'sedang') {
            $this->sesiModel->timeout((int)$sesi['id']);
            return redirect()->to(base_url("backend/ujian-mdta/santri/hasil/{$token}"));
        }

        $jadwal     = $this->jadwalModel->find($sesi['IdJadwal']);
        $paket      = $this->db->table('tbl_ujian_mdta_paket')->where('id', $jadwal['IdPaket'])->get()->getRowArray();
        $namaPaket  = $paket['NamaPaket'] ?? '-';
        $distribusi = $this->soalSesiModel->getDistribusiBySesi((int)$sesi['id']);
        $jawabanMap = $this->jawabanModel->getJawabanMap((int)$sesi['id']);
        $raguMap    = $this->jawabanModel->getRaguMap((int)$sesi['id']);

        // Ambil nama dan kelas santri
        $santriRow = $this->db->table('tbl_santri_baru sb')
            ->select('sb.NamaSantri, k.NamaKelas')
            ->join('tbl_kelas k', 'k.IdKelas = sb.IdKelas', 'left')
            ->where('sb.IdSantri', $sesi['IdSantri'])
            ->get()->getRowArray();

        $data = [
            'page_title'     => 'CBT Ujian — ' . $jadwal['NamaUjian'],
            'sesi'           => $sesi,
            'jadwal'         => $jadwal,
            'paket'          => $paket,
            'namaPaket'      => $namaPaket,
            'distribusi'     => $distribusi,
            'soalList'       => $distribusi,
            'jawabanMap'     => $jawabanMap,
            'jawabanEsaiMap' => $this->jawabanModel->getJawabanEsaiMap((int)$sesi['id']),
            'raguMap'        => $raguMap,
            'sisaWaktuDetik' => $sisaWaktuDetik,
            'token'          => $token,
            'namaSantri'     => $santriRow['NamaSantri'] ?? 'Santri',
            'namaKelas'      => $santriRow['NamaKelas'] ?? '-',
            'isPreview'      => false,
            'exitUrl'        => base_url('backend/ujian-mdta/santri'),
            'exitLabel'      => 'Keluar Ujian',
        ];
        return view('backend/ujianMdta/santri/lembar_ujian', $data);
    }

    /**
     * AJAX auto-save jawaban santri tiap diklik/diketik (PG maupun Esai).
     */
    public function simpanJawaban(string $token)
    {
        $sesi = $this->sesiModel->getSesiByToken($token);
        if (!$sesi || $sesi['StatusSesi'] !== 'sedang') {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak aktif atau sudah selesai.']);
        }

        $idSoal      = (int)$this->request->getPost('idSoal');
        $idPilihan   = $this->request->getPost('idPilihan') ? (int)$this->request->getPost('idPilihan') : null;
        $jawabanEsai = $this->request->getPost('jawabanEsai');

        $ok = $this->jawabanModel->simpanJawaban((int)$sesi['id'], $idSoal, $idPilihan, $jawabanEsai);

        return $this->response->setJSON([
            'success'   => $ok,
            'idSoal'    => $idSoal,
            'idPilihan' => $idPilihan,
        ]);
    }

    /**
     * AJAX auto-save status Ragu-Ragu.
     */
    public function simpanRagu(string $token)
    {
        $sesi = $this->sesiModel->getSesiByToken($token);
        if (!$sesi || $sesi['StatusSesi'] !== 'sedang') {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak aktif.']);
        }

        $idSoal = (int)$this->request->getPost('idSoal');
        $isRagu = (int)$this->request->getPost('isRagu');

        $ok = $this->jawabanModel->simpanRagu((int)$sesi['id'], $idSoal, $isRagu);

        return $this->response->setJSON([
            'success' => $ok,
            'idSoal'  => $idSoal,
            'isRagu'  => $isRagu,
        ]);
    }

    /**
     * AJAX live status polling untuk santri (cek pause, sisa waktu, stop).
     */
    public function cekStatusSesi(string $token)
    {
        $sesi = $this->sesiModel->getSesiByToken($token);
        if (!$sesi) {
            return $this->response->setJSON(['success' => false, 'status' => 'invalid']);
        }

        $sisaDetik = $this->sesiModel->getSisaWaktu($token);

        return $this->response->setJSON([
            'success'   => true,
            'status'    => strtolower(trim($sesi['StatusSesi'] ?? 'sedang')),
            'sisaDetik' => $sisaDetik,
        ]);
    }

    /**
     * Santri menekan tombol "Selesaikan Ujian" / Submit.
     */
    public function selesaikanUjian(string $token)
    {
        $sesi = $this->sesiModel->getSesiByToken($token);
        if (!$sesi) {
            return redirect()->to(base_url('backend/ujian-mdta/santri'));
        }

        if ($sesi['StatusSesi'] === 'sedang') {
            // Evaluasi jawaban benar/salah & hitung nilai akhir
            $this->jawabanModel->evaluasiJawaban((int)$sesi['id']);
            $this->sesiModel->hitungNilai((int)$sesi['id']);
        }

        return redirect()->to(base_url("backend/ujian-mdta/santri/hasil/{$token}"));
    }

    /**
     * Halaman Hasil Ujian Santri — Skor akhir, status lulus/gagal, dan pembahasan.
     */
    public function hasilUjian(string $token)
    {
        $sesi = $this->sesiModel->getSesiByToken($token);
        if (!$sesi) {
            return redirect()->to(base_url('backend/ujian-mdta/santri'))->with('error', 'Sesi tidak ditemukan.');
        }

        $jadwal = $this->jadwalModel->find($sesi['IdJadwal']);
        $paket  = $this->paketModel->find($jadwal['IdPaket']);
        $detail = $this->jawabanModel->getDetailHasilBySesi((int)$sesi['id']);

        $jumlahBenar = 0;
        $jumlahSalah = 0;
        foreach ($detail as $d) {
            if ($d['IsBenar'] == 1) {
                $jumlahBenar++;
            } else {
                $jumlahSalah++;
            }
        }

        $isLulus = ($sesi['NilaiAkhir'] >= $jadwal['NilaiMinimum']);

        $data = [
            'page_title'  => 'Hasil Ujian — ' . $jadwal['NamaUjian'],
            'sesi'        => $sesi,
            'jadwal'      => $jadwal,
            'paket'       => $paket,
            'detail'      => $detail,
            'jumlahBenar' => $jumlahBenar,
            'jumlahSalah' => $jumlahSalah,
            'isLulus'     => $isLulus,
        ];
        return view('backend/ujianMdta/santri/hasil_ujian', $data);
    }

    /**
     * AJAX — Cek sisa waktu dalam detik.
     */
    public function cekWaktu(string $token)
    {
        $sisaWaktu = $this->sesiModel->getSisaWaktu($token);
        return $this->response->setJSON([
            'sisaWaktuDetik' => $sisaWaktu,
            'isExpired'      => ($sisaWaktu <= 0),
        ]);
    }

    /**
     * AJAX — Cek status live jadwal & sesi santri (tanpa reload halaman terus-menerus).
     */
    public function cekJadwalStatus()
    {
        $santri = $this->getSantriSession();
        if (!$santri) {
            return $this->response->setJSON(['success' => false]);
        }

        $idKelas    = $santri['IdKelas'] ?? null;
        $idTpq      = $santri['IdTpq'] ?? null;
        $jadwalList = $this->jadwalModel->getJadwalAktifBySantri($idKelas, $idTpq);

        $hasPaused    = false;
        $activeTimers = [];

        foreach ($jadwalList as $j) {
            $sesi = $this->sesiModel->where('IdSantri', $santri['IdSantri'])
                                    ->where('IdJadwal', $j['id'])
                                    ->orderBy('id', 'DESC')
                                    ->first();

            $statusSesi = strtolower(trim($sesi['StatusSesi'] ?? 'belum'));

            if ($statusSesi === 'pause') {
                $hasPaused = true;
            }

            if ($statusSesi === 'sedang' && !empty($sesi['TokenSesi'])) {
                $activeTimers[$j['id']] = $this->sesiModel->getSisaWaktu($sesi['TokenSesi']);
            }
        }

        return $this->response->setJSON([
            'success'      => true,
            'hasPaused'    => $hasPaused,
            'activeTimers' => $activeTimers,
        ]);
    }
}
