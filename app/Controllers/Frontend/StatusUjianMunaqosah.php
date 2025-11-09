<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\MunaqosahPesertaModel;
use App\Models\MunaqosahRegistrasiUjiModel;
use App\Models\MunaqosahNilaiModel;
use App\Models\MunaqosahGrupMateriUjiModel;
use App\Models\SantriBaruModel;
use App\Models\HelpFunctionModel;
use App\Models\MunaqosahBobotNilaiModel;
use App\Models\MunaqosahKonfigurasiModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class StatusUjianMunaqosah extends BaseController
{
    protected $munaqosahPesertaModel;
    protected $munaqosahRegistrasiUjiModel;
    protected $munaqosahNilaiModel;
    protected $munaqosahGrupMateriUjiModel;
    protected $santriBaruModel;
    protected $helpFunctionModel;
    protected $bobotNilaiMunaqosahModel;
    protected $munaqosahKonfigurasiModel;
    protected $db;

    public function __construct()
    {
        $this->munaqosahPesertaModel = new MunaqosahPesertaModel();
        $this->munaqosahRegistrasiUjiModel = new MunaqosahRegistrasiUjiModel();
        $this->munaqosahNilaiModel = new MunaqosahNilaiModel();
        $this->munaqosahGrupMateriUjiModel = new MunaqosahGrupMateriUjiModel();
        $this->santriBaruModel = new SantriBaruModel();
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->bobotNilaiMunaqosahModel = new MunaqosahBobotNilaiModel();
        $this->munaqosahKonfigurasiModel = new MunaqosahKonfigurasiModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Halaman input HashKey untuk cek status munaqosah
     * Dapat menerima HashKey sebagai parameter di URL: /munaqosah/cek-status/{hashKey}
     */
    public function index($hasKey = null)
    {
        // Jika HashKey diberikan langsung di URL, langsung verifikasi dan redirect
        if (!empty($hasKey)) {
            $peserta = $this->getPesertaByHashKey($hasKey);
            
            if (empty($peserta)) {
                return redirect()->to(base_url('munaqosah/cek-status'))
                    ->with('error', 'HashKey tidak valid atau tidak ditemukan. Silakan masukkan HashKey yang benar.');
            }

            // Pastikan data peserta lengkap dengan semua field termasuk JenisKelamin
            // Simpan data peserta di session
            session()->set('munaqosah_peserta', $peserta);

            // Redirect ke halaman konfirmasi
            return redirect()->to(base_url('munaqosah/konfirmasi-data'));
        }
        
        // Jika tidak ada HashKey di URL, tampilkan form input
        $data = [
            'page_title' => 'Cek Status Munaqosah',
            'isPublic' => true
        ];

        return view('frontend/munaqosah/chekStatusMunaqosah', $data);
    }
    
    /**
     * Helper method untuk mendapatkan data peserta berdasarkan HashKey
     */
    private function getPesertaByHashKey($hasKey)
    {
        if (empty($hasKey)) {
            return null;
        }
        
        $peserta = $this->munaqosahPesertaModel
            ->select('tbl_munaqosah_peserta.*, tbl_santri_baru.NamaSantri, tbl_santri_baru.JenisKelamin, tbl_santri_baru.TempatLahirSantri, tbl_santri_baru.TanggalLahirSantri, tbl_santri_baru.NamaAyah, tbl_tpq.NamaTpq')
            ->join('tbl_santri_baru', 'tbl_santri_baru.IdSantri = tbl_munaqosah_peserta.IdSantri', 'left')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_munaqosah_peserta.IdTpq', 'left')
            ->where('tbl_munaqosah_peserta.HasKey', $hasKey)
            ->first();
            
        return $peserta;
    }

    /**
     * Verifikasi HashKey dan redirect ke halaman konfirmasi
     * Digunakan untuk form POST submission
     */
    public function verifyHashKey()
    {
        $hasKey = $this->request->getPost('hasKey');
        
        if (empty($hasKey)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'HashKey harus diisi'
            ]);
        }

        // Gunakan helper method untuk mendapatkan data peserta
        $peserta = $this->getPesertaByHashKey($hasKey);

        if (empty($peserta)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'HashKey tidak valid atau tidak ditemukan'
            ]);
        }

        // Pastikan data peserta lengkap dengan semua field termasuk JenisKelamin
        // Simpan data peserta di session untuk digunakan di halaman berikutnya
        session()->set('munaqosah_peserta', $peserta);

        return $this->response->setJSON([
            'success' => true,
            'redirect' => base_url('munaqosah/konfirmasi-data')
        ]);
    }

    /**
     * Halaman konfirmasi data santri
     */
    public function konfirmasiData()
    {
        $pesertaSession = session()->get('munaqosah_peserta');

        if (empty($pesertaSession)) {
            return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Session expired. Silakan masukkan HashKey lagi.');
        }

        // Ambil data lengkap peserta dari database termasuk JenisKelamin
        // untuk memastikan semua field tersedia, terutama jika session dibuat sebelum perubahan
        $peserta = $this->munaqosahPesertaModel
            ->select('tbl_munaqosah_peserta.*, tbl_santri_baru.NamaSantri, tbl_santri_baru.JenisKelamin, tbl_santri_baru.TempatLahirSantri, tbl_santri_baru.TanggalLahirSantri, tbl_santri_baru.NamaAyah, tbl_tpq.NamaTpq')
            ->join('tbl_santri_baru', 'tbl_santri_baru.IdSantri = tbl_munaqosah_peserta.IdSantri', 'left')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_munaqosah_peserta.IdTpq', 'left')
            ->where('tbl_munaqosah_peserta.IdSantri', $pesertaSession['IdSantri'])
            ->where('tbl_munaqosah_peserta.IdTahunAjaran', $pesertaSession['IdTahunAjaran'])
            ->first();

        // Jika data tidak ditemukan, gunakan data dari session sebagai fallback
        if (empty($peserta)) {
            $peserta = $pesertaSession;
        }

        // Ambil semua TypeUjian yang tersedia untuk IdSantri ini
        $allRegistrasi = $this->munaqosahRegistrasiUjiModel
            ->select('TypeUjian, NoPeserta')
            ->where('IdSantri', $peserta['IdSantri'])
            ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
            ->groupBy('TypeUjian, NoPeserta')
            ->findAll();

        $availableTypeUjian = [];
        foreach ($allRegistrasi as $reg) {
            // Normalisasi TypeUjian (pramunaqsah -> pra-munaqosah)
            $typeUjian = strtolower(trim($reg['TypeUjian'] ?? ''));
            if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
                $typeUjian = 'pra-munaqosah';
            }
            if (!empty($typeUjian) && !in_array($typeUjian, $availableTypeUjian)) {
                $availableTypeUjian[] = $typeUjian;
            }
        }

        // Jika tidak ada TypeUjian ditemukan, coba ambil dari registrasi pertama
        if (empty($availableTypeUjian)) {
            $firstRegistrasi = $this->munaqosahRegistrasiUjiModel
                ->where('IdSantri', $peserta['IdSantri'])
                ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
                ->first();

            if (!empty($firstRegistrasi)) {
                $typeUjian = strtolower(trim($firstRegistrasi['TypeUjian'] ?? 'munaqosah'));
                if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
                    $typeUjian = 'pra-munaqosah';
                }
                $availableTypeUjian[] = $typeUjian;
            } else {
                // Default ke munaqosah jika tidak ada data
                $availableTypeUjian[] = 'munaqosah';
            }
        }

        // Ambil setting AktiveTombolKelulusan dari konfigurasi
        $idTpq = $peserta['IdTpq'] ?? 'default';
        $aktiveTombolKelulusan = $this->munaqosahKonfigurasiModel->getSetting((string)$idTpq, 'AktiveTombolKelulusan');

        // Jika tidak ada setting, default false (tidak aktif)
        $aktiveTombolKelulusan = $aktiveTombolKelulusan !== null ? (bool)$aktiveTombolKelulusan : false;

        // Ambil status verifikasi dari database
        $pesertaData = $this->munaqosahPesertaModel
            ->where('IdSantri', $peserta['IdSantri'])
            ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
            ->first();

        $statusVerifikasi = $pesertaData['status_verifikasi'] ?? null;
        $isVerified = ($statusVerifikasi === 'valid' || $statusVerifikasi === 'dikonfirmasi');

        $data = [
            'page_title' => 'Konfirmasi Data Santri',
            'isPublic' => true,
            'peserta' => $peserta,
            'aktiveTombolKelulusan' => $aktiveTombolKelulusan,
            'availableTypeUjian' => $availableTypeUjian,
            'statusVerifikasi' => $statusVerifikasi,
            'isVerified' => $isVerified
        ];

        return view('frontend/munaqosah/konfirmasiDataSantri', $data);
    }

    /**
     * Proses verifikasi data (saat checkbox confirmed diklik)
     */
    public function verifikasiData()
    {
        $peserta = session()->get('munaqosah_peserta');

        if (empty($peserta)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired. Silakan masukkan HashKey lagi.'
            ]);
        }

        // Cari data peserta di database
        $pesertaData = $this->munaqosahPesertaModel
            ->where('IdSantri', $peserta['IdSantri'])
            ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
            ->first();

        if (empty($pesertaData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data peserta tidak ditemukan'
            ]);
        }

        $statusVerifikasi = $this->request->getPost('status_verifikasi'); // 'valid' atau 'perlu_perbaikan'
        $keterangan = $this->request->getPost('keterangan');
        $perbaikanDataJson = $this->request->getPost('perbaikan_data');

        if (empty($statusVerifikasi) || !in_array($statusVerifikasi, ['valid', 'perlu_perbaikan'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status verifikasi tidak valid'
            ]);
        }

        // Jika perlu perbaikan, keterangan wajib (untuk valid, keterangan akan diisi otomatis)
        if ($statusVerifikasi === 'perlu_perbaikan' && empty($keterangan)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Keterangan perbaikan harus diisi'
            ]);
        }

        // Parse data perbaikan jika ada
        $perbaikanData = null;
        if (!empty($perbaikanDataJson)) {
            $perbaikanData = json_decode($perbaikanDataJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $perbaikanData = null;
            }
        }

        // Update status verifikasi
        $now = date('Y-m-d H:i:s');
        $updateData = [
            'status_verifikasi' => $statusVerifikasi,
            'verified_at' => $now
        ];

        // Jika status valid (user klik "Ya, Data Benar")
        if ($statusVerifikasi === 'valid') {
            $updateData['confirmed_at'] = $now;
            $updateData['verified_at'] = $now;
            $updateData['confirmed_by'] = 'wali-santri';
            $updateData['keterangan'] = 'Sudah di verifikasi oleh wali santri';
        } else if ($statusVerifikasi === 'perlu_perbaikan') {
            // Jika perlu perbaikan, simpan keterangan dari user
            if (!empty($keterangan)) {
                $updateData['keterangan'] = $keterangan;
            }

            // Simpan data perbaikan sebagai JSON di kolom keterangan (jika ada data perbaikan terstruktur)
            // Format: keterangan akan berisi text summary, dan data JSON bisa disimpan di kolom terpisah jika diperlukan
            // Untuk saat ini, kita simpan keduanya: keterangan (text) dan data perbaikan (JSON dalam keterangan)
            if (!empty($perbaikanData)) {
                // Gabungkan keterangan dengan data perbaikan JSON
                $keteranganWithData = $keterangan ?? '';
                if (!empty($perbaikanData)) {
                    $keteranganWithData .= "\n\n[Data Perbaikan JSON]\n" . json_encode($perbaikanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }
                $updateData['keterangan'] = $keteranganWithData;
            }
        }

        if ($this->munaqosahPesertaModel->update($pesertaData['id'], $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $statusVerifikasi === 'valid'
                    ? 'Data telah diverifikasi dan dinyatakan benar'
                    : 'Permintaan perbaikan telah dikirim. Silakan tunggu konfirmasi dari operator.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan verifikasi. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Proses konfirmasi dan redirect ke halaman yang dipilih
     */
    public function processKonfirmasi()
    {
        $action = $this->request->getPost('action');
        $confirmed = $this->request->getPost('confirmed');
        $typeUjian = $this->request->getPost('typeUjian');

        if (empty($confirmed)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda harus menyetujui informasi penting terlebih dahulu'
            ]);
        }

        $peserta = session()->get('munaqosah_peserta');
        
        if (empty($peserta)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired. Silakan masukkan HashKey lagi.'
            ]);
        }

        // Normalisasi TypeUjian
        if (!empty($typeUjian)) {
            $typeUjian = strtolower(trim($typeUjian));
            if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
                $typeUjian = 'pra-munaqosah';
            }
            // Validasi TypeUjian
            if (!in_array($typeUjian, ['munaqosah', 'pra-munaqosah'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Type Ujian tidak valid'
                ]);
            }
        }

        // Simpan TypeUjian yang dipilih ke session
        if (!empty($typeUjian)) {
            session()->set('munaqosah_type_ujian', $typeUjian);
        }

        if ($action === 'status') {
            $redirectUrl = base_url('munaqosah/status-proses') . (!empty($typeUjian) ? '?typeUjian=' . urlencode($typeUjian) : '');
        } elseif ($action === 'kelulusan') {
            $redirectUrl = base_url('munaqosah/kelulusan') . (!empty($typeUjian) ? '?typeUjian=' . urlencode($typeUjian) : '');
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Action tidak valid'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'redirect' => $redirectUrl
        ]);
    }

    /**
     * Halaman status proses munaqosah
     */
    public function statusProses()
    {
        $peserta = session()->get('munaqosah_peserta');
        
        if (empty($peserta)) {
            return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Session expired. Silakan masukkan HashKey lagi.');
        }

        // Ambil TypeUjian dari query parameter atau session
        $typeUjian = $this->request->getGet('typeUjian') ?? session()->get('munaqosah_type_ujian');

        // Normalisasi TypeUjian
        if (!empty($typeUjian)) {
            $typeUjian = strtolower(trim($typeUjian));
            if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
                $typeUjian = 'pra-munaqosah';
            }
        }

        // Ambil semua TypeUjian yang tersedia untuk IdSantri ini
        $allRegistrasi = $this->munaqosahRegistrasiUjiModel
            ->select('TypeUjian, NoPeserta')
            ->where('IdSantri', $peserta['IdSantri'])
            ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
            ->groupBy('TypeUjian, NoPeserta')
            ->findAll();

        $availableTypeUjian = [];
        $registrasiMap = [];
        foreach ($allRegistrasi as $reg) {
            $regTypeUjian = strtolower(trim($reg['TypeUjian'] ?? ''));
            if ($regTypeUjian === 'pramunaqsah' || $regTypeUjian === 'pra-munaqosah') {
                $regTypeUjian = 'pra-munaqosah';
            }
            if (!empty($regTypeUjian) && !in_array($regTypeUjian, $availableTypeUjian)) {
                $availableTypeUjian[] = $regTypeUjian;
                $registrasiMap[$regTypeUjian] = $reg['NoPeserta'];
            }
        }

        // Jika tidak ada TypeUjian yang dipilih dan ada lebih dari satu, ambil yang pertama
        if (empty($typeUjian)) {
            if (!empty($availableTypeUjian)) {
                $typeUjian = $availableTypeUjian[0];
            } else {
                // Default ke munaqosah jika tidak ada data
                $typeUjian = 'munaqosah';
            }
        }

        // Validasi TypeUjian yang dipilih ada di available list
        if (!in_array($typeUjian, $availableTypeUjian)) {
            if (!empty($availableTypeUjian)) {
                $typeUjian = $availableTypeUjian[0];
            } else {
                return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Data registrasi tidak ditemukan.');
            }
        }

        // Ambil NoPeserta berdasarkan TypeUjian
        $noPeserta = $registrasiMap[$typeUjian] ?? null;
        if (empty($noPeserta)) {
            // Fallback: ambil dari registrasi pertama yang cocok
            $registrasi = $this->munaqosahRegistrasiUjiModel
                ->where('IdSantri', $peserta['IdSantri'])
                ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
                ->where('TypeUjian', $typeUjian)
                ->first();

            if (empty($registrasi)) {
                return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Data registrasi tidak ditemukan.');
            }
            $noPeserta = $registrasi['NoPeserta'];
        }

        $idTahunAjaran = $peserta['IdTahunAjaran'];

        // Ambil semua grup materi ujian yang aktif
        $grupMateriList = $this->munaqosahGrupMateriUjiModel
            ->where('Status', 'Aktif')
            ->orderBy('NamaMateriGrup', 'ASC')
            ->findAll();

        // Ambil data registrasi per grup materi dengan filter TypeUjian
        $registrasiByGrup = $this->munaqosahRegistrasiUjiModel
            ->select('IdGrupMateriUjian, COUNT(DISTINCT IdMateri) as jumlah_materi')
            ->where('NoPeserta', $noPeserta)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->groupBy('IdGrupMateriUjian')
            ->findAll();

        $registrasiMap = [];
        foreach ($registrasiByGrup as $reg) {
            $registrasiMap[$reg['IdGrupMateriUjian']] = $reg['jumlah_materi'];
        }

        // Ambil data nilai per grup materi dengan filter TypeUjian
        $nilaiByGrup = $this->munaqosahNilaiModel
            ->select('IdGrupMateriUjian, COUNT(DISTINCT IdMateri) as jumlah_nilai')
            ->where('NoPeserta', $noPeserta)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->where('Nilai >', 0)
            ->groupBy('IdGrupMateriUjian')
            ->findAll();

        $nilaiMap = [];
        foreach ($nilaiByGrup as $nilai) {
            $nilaiMap[$nilai['IdGrupMateriUjian']] = $nilai['jumlah_nilai'];
        }

        // Cek status untuk setiap grup
        $statusGrup = [];
        foreach ($grupMateriList as $grup) {
            $idGrup = $grup['IdGrupMateriUjian'];
            $jumlahMateri = $registrasiMap[$idGrup] ?? 0;
            $jumlahNilai = $nilaiMap[$idGrup] ?? 0;
            
            $statusGrup[] = [
                'grup' => $grup,
                'jumlah_materi' => $jumlahMateri,
                'jumlah_nilai' => $jumlahNilai,
                'selesai' => $jumlahMateri > 0 && $jumlahNilai >= $jumlahMateri
            ];
        }

        $data = [
            'page_title' => 'Status Proses Munaqosah',
            'isPublic' => true,
            'peserta' => $peserta,
            'statusGrup' => $statusGrup,
            'typeUjian' => $typeUjian,
            'availableTypeUjian' => $availableTypeUjian
        ];

        return view('frontend/munaqosah/statusProsesMunaqosah', $data);
    }

    /**
     * Halaman kelulusan munaqosah
     */
    public function kelulusan()
    {
        $peserta = session()->get('munaqosah_peserta');
        
        if (empty($peserta)) {
            return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Session expired. Silakan masukkan HashKey lagi.');
        }

        // Ambil TypeUjian dari query parameter atau session
        $typeUjian = $this->request->getGet('typeUjian') ?? session()->get('munaqosah_type_ujian');

        // Normalisasi TypeUjian
        if (!empty($typeUjian)) {
            $typeUjian = strtolower(trim($typeUjian));
            if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
                $typeUjian = 'pra-munaqosah';
            }
        }

        // Ambil semua TypeUjian yang tersedia untuk IdSantri ini
        $allRegistrasi = $this->munaqosahRegistrasiUjiModel
            ->select('TypeUjian, NoPeserta')
            ->where('IdSantri', $peserta['IdSantri'])
            ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
            ->groupBy('TypeUjian, NoPeserta')
            ->findAll();

        $availableTypeUjian = [];
        $registrasiMap = [];
        foreach ($allRegistrasi as $reg) {
            $regTypeUjian = strtolower(trim($reg['TypeUjian'] ?? ''));
            if ($regTypeUjian === 'pramunaqsah' || $regTypeUjian === 'pra-munaqosah') {
                $regTypeUjian = 'pra-munaqosah';
            }
            if (!empty($regTypeUjian) && !in_array($regTypeUjian, $availableTypeUjian)) {
                $availableTypeUjian[] = $regTypeUjian;
                $registrasiMap[$regTypeUjian] = $reg['NoPeserta'];
            }
        }

        // Jika tidak ada TypeUjian yang dipilih dan ada lebih dari satu, ambil yang pertama
        if (empty($typeUjian)) {
            if (!empty($availableTypeUjian)) {
                $typeUjian = $availableTypeUjian[0];
            } else {
                // Default ke munaqosah jika tidak ada data
                $typeUjian = 'munaqosah';
            }
        }

        // Validasi TypeUjian yang dipilih ada di available list
        if (!in_array($typeUjian, $availableTypeUjian)) {
            if (!empty($availableTypeUjian)) {
                $typeUjian = $availableTypeUjian[0];
            } else {
                return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Data registrasi tidak ditemukan.');
            }
        }

        // Ambil NoPeserta berdasarkan TypeUjian
        $noPeserta = $registrasiMap[$typeUjian] ?? null;
        if (empty($noPeserta)) {
            // Fallback: ambil dari registrasi pertama yang cocok
            $registrasi = $this->munaqosahRegistrasiUjiModel
                ->where('IdSantri', $peserta['IdSantri'])
                ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
                ->where('TypeUjian', $typeUjian)
                ->first();

            if (empty($registrasi)) {
                return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Data registrasi tidak ditemukan.');
            }
            $noPeserta = $registrasi['NoPeserta'];
        }

        $idTahunAjaran = $peserta['IdTahunAjaran'];
        $idTpq = $peserta['IdTpq'] ?? null;

        // Gunakan fungsi prepareKelulusanPesertaData dari Munaqosah controller
        $kelulusanData = $this->prepareKelulusanPesertaData($noPeserta, $idTahunAjaran, $typeUjian, $idTpq);

        if (!$kelulusanData['success']) {
            return redirect()->to(base_url('munaqosah/cek-status'))->with('error', $kelulusanData['message'] ?? 'Data kelulusan tidak ditemukan.');
        }

        $pesertaData = $kelulusanData['peserta'];
        $totalBobot = $pesertaData['TotalWeighted'] ?? 0;
        $threshold = $pesertaData['KelulusanThreshold'] ?? 65;
        $lulus = $pesertaData['KelulusanMet'] ?? false;
        $status = $pesertaData['KelulusanStatus'] ?? 'Belum Lulus';

        $data = [
            'page_title' => 'Status Kelulusan Munaqosah',
            'isPublic' => true,
            'peserta' => $peserta,
            'totalBobot' => $totalBobot,
            'threshold' => $threshold,
            'lulus' => $lulus,
            'status' => $status,
            'noPeserta' => $noPeserta,
            'typeUjian' => $typeUjian,
            'availableTypeUjian' => $availableTypeUjian
        ];

        return view('frontend/munaqosah/kelulusanMunaqosah', $data);
    }

    /**
     * Generate PDF Surat Keterangan Kelulusan
     */
    public function generateSuratKelulusan()
    {
        $peserta = session()->get('munaqosah_peserta');
        
        if (empty($peserta)) {
            return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Session expired.');
        }

        // Ambil TypeUjian dari query parameter atau session
        $typeUjian = $this->request->getGet('typeUjian') ?? session()->get('munaqosah_type_ujian');

        // Normalisasi TypeUjian
        if (!empty($typeUjian)) {
            $typeUjian = strtolower(trim($typeUjian));
            if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
                $typeUjian = 'pra-munaqosah';
            }
        }

        // Ambil semua TypeUjian yang tersedia
        $allRegistrasi = $this->munaqosahRegistrasiUjiModel
            ->select('TypeUjian, NoPeserta')
            ->where('IdSantri', $peserta['IdSantri'])
            ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
            ->groupBy('TypeUjian, NoPeserta')
            ->findAll();

        $availableTypeUjian = [];
        $registrasiMap = [];
        foreach ($allRegistrasi as $reg) {
            $regTypeUjian = strtolower(trim($reg['TypeUjian'] ?? ''));
            if ($regTypeUjian === 'pramunaqsah' || $regTypeUjian === 'pra-munaqosah') {
                $regTypeUjian = 'pra-munaqosah';
            }
            if (!empty($regTypeUjian) && !in_array($regTypeUjian, $availableTypeUjian)) {
                $availableTypeUjian[] = $regTypeUjian;
                $registrasiMap[$regTypeUjian] = $reg['NoPeserta'];
            }
        }

        // Jika tidak ada TypeUjian yang dipilih, ambil yang pertama
        if (empty($typeUjian)) {
            if (!empty($availableTypeUjian)) {
                $typeUjian = $availableTypeUjian[0];
            } else {
                $typeUjian = 'munaqosah';
            }
        }

        // Ambil NoPeserta berdasarkan TypeUjian
        $noPeserta = $registrasiMap[$typeUjian] ?? null;
        if (empty($noPeserta)) {
            $registrasi = $this->munaqosahRegistrasiUjiModel
                ->where('IdSantri', $peserta['IdSantri'])
                ->where('IdTahunAjaran', $peserta['IdTahunAjaran'])
                ->where('TypeUjian', $typeUjian)
                ->first();

            if (empty($registrasi)) {
                return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Data registrasi tidak ditemukan.');
            }
            $noPeserta = $registrasi['NoPeserta'];
        }

        $idTahunAjaran = $peserta['IdTahunAjaran'];
        $idTpq = $peserta['IdTpq'] ?? null;

        // Ambil data kelulusan
        $kelulusanData = $this->prepareKelulusanPesertaData($noPeserta, $idTahunAjaran, $typeUjian, $idTpq);

        if (!$kelulusanData['success']) {
            return redirect()->to(base_url('munaqosah/cek-status'))->with('error', 'Data kelulusan tidak ditemukan.');
        }

        $pesertaData = $kelulusanData['peserta'];
        $categoryDetails = $kelulusanData['categoryDetails'] ?? [];
        $meta = $kelulusanData['meta'] ?? [];

        // Ambil data TPQ untuk logo/kop
        $tpqData = $this->helpFunctionModel->getNamaTpqById($peserta['IdTpq']);

        $data = [
            'peserta' => $pesertaData,
            'categoryDetails' => $categoryDetails,
            'meta' => $meta,
            'tpqData' => $tpqData,
            'generated_at' => date('d F Y H:i:s')
        ];

        // Generate PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('frontend/munaqosah/suratKelulusanUjianMunaqosah', $data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Surat_Kelulusan_' . str_replace(' ', '_', $pesertaData['NamaSantri']) . '.pdf';
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Helper function untuk prepare kelulusan data (mirip dengan di Munaqosah controller)
     */
    private function prepareKelulusanPesertaData(string $noPeserta, ?string $idTahunAjaran = null, ?string $typeUjian = null, ?int $idTpqFilter = null): array
    {
        $noPeserta = strtoupper(trim($noPeserta));
        if ($noPeserta === '') {
            return [
                'success' => false,
                'message' => 'NoPeserta harus diisi'
            ];
        }

        $registrasiBuilder = $this->db->table('tbl_munaqosah_registrasi_uji r');
        $registrasiBuilder->select('r.*, s.NamaSantri, t.NamaTpq');
        $registrasiBuilder->join('tbl_santri_baru s', 's.IdSantri = r.IdSantri', 'left');
        $registrasiBuilder->join('tbl_tpq t', 't.IdTpq = r.IdTpq', 'left');
        $registrasiBuilder->where('r.NoPeserta', $noPeserta);

        if (!empty($idTahunAjaran)) {
            $registrasiBuilder->where('r.IdTahunAjaran', $idTahunAjaran);
        }

        if (!empty($typeUjian)) {
            $registrasiBuilder->where('r.TypeUjian', $typeUjian);
        }

        if (!empty($idTpqFilter)) {
            $registrasiBuilder->where('r.IdTpq', $idTpqFilter);
        }

        $registrasiBuilder->orderBy('r.IdTahunAjaran', 'DESC');
        $registrasiBuilder->orderBy('r.TypeUjian', 'ASC');

        $registrasiRows = $registrasiBuilder->get()->getResultArray();

        if (empty($registrasiRows)) {
            return [
                'success' => false,
                'message' => 'Data peserta tidak ditemukan'
            ];
        }

        $primaryRow = $registrasiRows[0];
        $resolvedTahun = $idTahunAjaran ?: $primaryRow['IdTahunAjaran'];
        $resolvedType = $typeUjian ?: $primaryRow['TypeUjian'];
        $resolvedType = strtolower($resolvedType);
        $resolvedTpq = $idTpqFilter ?? ($primaryRow['IdTpq'] ?? 0);

        // Build monitoring dataset
        $dataset = $this->buildMonitoringDataset($resolvedTahun, (int)$resolvedTpq, $resolvedType, true, $noPeserta);

        if (!$dataset['success'] || empty($dataset['data']['rows'])) {
            return [
                'success' => false,
                'message' => $dataset['message'] ?? 'Data kelulusan tidak ditemukan'
            ];
        }

        $rowData = $dataset['data']['rows'][0];
        $categories = $dataset['data']['categories'];
        $meta = $dataset['data']['meta'];

        // Prepare category details
        $categoryDetails = [];
        foreach ($categories as $cat) {
            $catId = $cat['id'];
            $average = $rowData['averages'][$catId] ?? 0;
            $weighted = $rowData['weighted'][$catId] ?? 0;

            $categoryDetails[] = [
                'category' => $cat,
                'average' => $average,
                'weighted' => $weighted,
                'juri_scores' => [],
                'materi' => []
            ];
        }

        return [
            'success' => true,
            'peserta' => [
                'NoPeserta' => $rowData['NoPeserta'],
                'NamaSantri' => $rowData['NamaSantri'],
                'NamaTpq' => $rowData['NamaTpq'],
                'IdTahunAjaran' => $rowData['IdTahunAjaran'],
                'TypeUjian' => $rowData['TypeUjian'],
                'TotalWeighted' => $rowData['total_weighted'] ?? 0,
                'KelulusanThreshold' => $rowData['kelulusan_threshold'] ?? 65,
                'KelulusanMet' => $rowData['kelulusan_met'] ?? false,
                'KelulusanStatus' => $rowData['kelulusan_status'] ?? 'Belum Lulus',
                'KelulusanDifference' => $rowData['kelulusan_difference'] ?? 0
            ],
            'categoryDetails' => $categoryDetails,
            'meta' => $meta
        ];
    }

    /**
     * Helper function buildMonitoringDataset (mirip dengan di Munaqosah controller)
     */
    private function buildMonitoringDataset(string $idTahunAjaran, ?int $idTpq = 0, ?string $typeParam = null, bool $includeBobot = false, ?string $targetNoPeserta = null): array
    {
        try {
            $idTahunAjaran = trim($idTahunAjaran);
            if ($idTahunAjaran === '') {
                return [
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ];
            }

            $idTpq = (int)($idTpq ?? 0);
            $allowedTypes = ['munaqosah', 'pra-munaqosah'];
            $typeParam = $typeParam !== null ? strtolower($typeParam) : null;
            $typeUjian = in_array($typeParam, $allowedTypes, true)
                ? $typeParam
                : (($idTpq !== 0) ? 'pra-munaqosah' : 'munaqosah');

            $builder = $this->db->table('tbl_munaqosah_registrasi_uji r');
            $builder->select('r.NoPeserta,r.IdSantri,r.IdTpq,r.IdTahunAjaran,r.IdKategoriMateri,r.IdGrupMateriUjian,r.TypeUjian, s.NamaSantri, t.NamaTpq, km.NamaKategoriMateri');
            $builder->join('tbl_santri_baru s', 's.IdSantri = r.IdSantri', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = r.IdTpq', 'left');
            $builder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = r.IdKategoriMateri', 'left');
            $builder->where('r.IdTahunAjaran', $idTahunAjaran);
            $builder->where('r.TypeUjian', $typeUjian);
            if (!empty($idTpq)) {
                $builder->where('r.IdTpq', $idTpq);
            }
            if (!empty($targetNoPeserta)) {
                $builder->where('r.NoPeserta', $targetNoPeserta);
            }

            $registrasiRows = $builder->get()->getResultArray();

            if (empty($registrasiRows)) {
                return [
                    'success' => $targetNoPeserta ? false : true,
                    'message' => $targetNoPeserta ? 'Peserta tidak ditemukan untuk parameter yang diberikan' : null,
                    'data' => [
                        'categories' => [],
                        'rows' => [],
                        'meta' => []
                    ]
                ];
            }

            // Get bobot data
            $bobotData = ['map' => [], 'source' => $idTahunAjaran];
            if ($includeBobot) {
                $bobotData = $this->getBobotWeightData($idTahunAjaran);
            }
            $bobotMap = $bobotData['map'];
            $bobotSource = $bobotData['source'];

            // Build categories map
            $categoriesMap = [];
            foreach ($registrasiRows as $row) {
                $catId = $row['IdKategoriMateri'];
                if (empty($catId)) {
                    continue;
                }
                if (!isset($categoriesMap[$catId])) {
                    $categoriesMap[$catId] = [
                        'id' => $catId,
                        'name' => $row['NamaKategoriMateri'] ?? $catId,
                        'weight' => isset($bobotMap[$catId]) ? (float)$bobotMap[$catId] : 0.0,
                    ];
                }
            }

            ksort($categoriesMap);
            $categories = array_values($categoriesMap);

            // Get peserta info
            $pesertaInfo = [];
            foreach ($registrasiRows as $row) {
                $np = $row['NoPeserta'];
                if (!isset($pesertaInfo[$np])) {
                    $pesertaInfo[$np] = [
                        'NoPeserta' => $np,
                        'IdSantri' => $row['IdSantri'],
                        'IdTpq' => $row['IdTpq'],
                        'NamaSantri' => $row['NamaSantri'] ?? '-',
                        'NamaTpq' => $row['NamaTpq'] ?? '-',
                        'TypeUjian' => $row['TypeUjian'],
                        'IdTahunAjaran' => $row['IdTahunAjaran'],
                    ];
                }
            }

            $noPesertaList = array_keys($pesertaInfo);

            // Get nilai data
            $nilaiBuilder = $this->db->table('tbl_munaqosah_nilai n');
            $nilaiBuilder->select('n.NoPeserta,n.IdKategoriMateri,n.Nilai');
            $nilaiBuilder->where('n.IdTahunAjaran', $idTahunAjaran);
            $nilaiBuilder->where('n.TypeUjian', $typeUjian);
            $nilaiBuilder->whereIn('n.NoPeserta', $noPesertaList);
            $nilaiRows = $nilaiBuilder->get()->getResultArray();

            // Index nilai by NoPeserta and IdKategoriMateri
            $nilaiIndex = [];
            foreach ($nilaiRows as $row) {
                $np = $row['NoPeserta'];
                $catId = $row['IdKategoriMateri'];
                if ($catId === null) {
                    continue;
                }

                if (!isset($nilaiIndex[$np])) {
                    $nilaiIndex[$np] = [];
                }
                if (!isset($nilaiIndex[$np][$catId])) {
                    $nilaiIndex[$np][$catId] = [];
                }

                $nilaiIndex[$np][$catId][] = (float)$row['Nilai'];
            }

            // Build rows
            $rows = [];
            foreach ($pesertaInfo as $np => $info) {
                $row = $info;
                if ($includeBobot) {
                    $row['averages'] = [];
                    $row['weighted'] = [];
                }

                foreach ($categories as $cat) {
                    $catId = $cat['id'];
                    $rawScores = $nilaiIndex[$np][$catId] ?? [];
                    
                    $validScores = array_filter($rawScores, function ($score) {
                        return $score > 0;
                    });
                    $validScores = array_values($validScores);

                    if ($includeBobot) {
                        if (count($validScores) === 1) {
                            $average = (float)$validScores[0];
                        } elseif (count($validScores) > 1) {
                            $average = round(array_sum($validScores) / count($validScores), 2);
                        } else {
                            $average = 0;
                        }

                        $weight = isset($bobotMap[$catId]) ? (float)$bobotMap[$catId] : 0.0;
                        $weightedScore = round(($average * $weight) / 100, 2);

                        $row['averages'][$catId] = $average;
                        $row['weighted'][$catId] = $weightedScore;
                    }
                }

                if ($includeBobot) {
                    $totalWeighted = array_sum($row['weighted']);
                    $totalWeighted = round($totalWeighted, 2);
                    $threshold = $this->getKelulusanThresholdForTpq($row['IdTpq'] ?? null);

                    $row['total_weighted'] = $totalWeighted;
                    $row['kelulusan_threshold'] = $threshold;
                    $row['kelulusan_met'] = $totalWeighted >= $threshold;
                    $row['kelulusan_status'] = $row['kelulusan_met'] ? 'Lulus' : 'Belum Lulus';
                    $row['kelulusan_difference'] = round($totalWeighted - $threshold, 2);
                }

                $rows[] = $row;
            }

            return [
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'rows' => $rows,
                    'meta' => [
                        'IdTahunAjaran' => $idTahunAjaran,
                        'TypeUjian' => $typeUjian,
                        'IdTpq' => $idTpq,
                        'bobot_source' => $includeBobot ? $bobotSource : null,
                        'has_bobot' => $includeBobot && !empty($bobotMap),
                    ]
                ]
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Error in buildMonitoringDataset: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Get bobot weight data
     */
    private function getBobotWeightData(string $idTahunAjaran): array
    {
        $rows = $this->bobotNilaiMunaqosahModel->getBobotWithKategori($idTahunAjaran);
        $map = [];
        foreach ($rows as $row) {
            $map[$row['IdKategoriMateri']] = (float)$row['NilaiBobot'];
        }

        // Fallback ke default jika kosong
        if (empty($map)) {
            $defaultRows = $this->bobotNilaiMunaqosahModel->getBobotWithKategori('default');
            foreach ($defaultRows as $row) {
                $map[$row['IdKategoriMateri']] = (float)$row['NilaiBobot'];
            }
        }

        return [
            'map' => $map,
            'source' => $idTahunAjaran
        ];
    }

    /**
     * Get kelulusan threshold for TPQ
     */
    private function getKelulusanThresholdForTpq($idTpq): int
    {
        if (empty($idTpq)) {
            return 65; // Default threshold
        }

        $setting = $this->munaqosahKonfigurasiModel->getSetting((string)$idTpq, 'KelulusanThreshold');
        if ($setting !== null && is_numeric($setting)) {
            return (int)$setting;
        }

        // Fallback ke default
        $defaultSetting = $this->munaqosahKonfigurasiModel->getSetting('default', 'KelulusanThreshold');
        if ($defaultSetting !== null && is_numeric($defaultSetting)) {
            return (int)$defaultSetting;
        }

        return 65; // Final fallback
    }
}

