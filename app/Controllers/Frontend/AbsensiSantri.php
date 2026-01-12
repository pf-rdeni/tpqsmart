<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\Frontend\AbsensiSantriLinkModel;
use App\Models\Frontend\AbsensiDeviceModel;
use App\Models\AbsensiModel;
use App\Models\GuruModel;
use App\Models\SantriModel;
use App\Models\KelasModel;
use App\Models\HelpFunctionModel;

class AbsensiSantri extends BaseController
{
    protected $linkModel;
    protected $deviceModel;
    protected $absensiModel;
    protected $guruModel;
    protected $santriModel;
    protected $kelasModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->linkModel = new AbsensiSantriLinkModel();
        $this->deviceModel = new AbsensiDeviceModel();
        $this->absensiModel = new AbsensiModel();
        $this->guruModel = new GuruModel();
        $this->santriModel = new SantriModel();
        $this->kelasModel = new KelasModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function index($hashKey)
    {
        // 1. Validasi Link
        $linkData = $this->linkModel->getLinkByKey($hashKey);
        if (!$linkData) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Link Absensi tidak valid.");
        }

        // 2. Identify IdGuru for the session
        // Kita butuh IdGuru untuk menampilkan data.
        // Sumber IdGuru bisa dari:
        // A. Cookie Device Token (Priority 1 - Trusted Device)
        // B. Login Session Standard (Priority 2 - New Login)

        $IdGuru = null;
        $deviceToken = isset($_COOKIE['device_token']) ? $_COOKIE['device_token'] : null;

        // Cek Device Token
        if ($deviceToken) {
            $deviceData = $this->deviceModel->getDevice($deviceToken);
            if ($deviceData) {
                $IdGuru = $deviceData['IdGuru'];
                // Update Access
                $this->deviceModel->update($deviceData['Id'], ['LastAccess' => date('Y-m-d H:i:s')]);
            }
        }

        // Jika belum dapat IdGuru dari Token, cek Standard Login Session
        if (!$IdGuru) {
             // Load Auth Helper/Service if not autoloaded
             helper('auth'); // Myth Auth Helper

             if (logged_in()) {
                 // User sudah login via myAuth standard
                 $user = user();
                 
                 // Mapping User ke Guru
                 // Berdasarkan AuthController, relasi ada pada kolom 'nik' di table users yang berisi IdGuru
                 $IdGuru = $user->nik ?? null;
                 
                 // Fallback: Cek session jika AuthController sudah menjalankannya
                 if (!$IdGuru) {
                     $IdGuru = session()->get('IdGuru');
                 }

                 $guru = null;
                 if ($IdGuru) {
                     $guru = $this->guruModel->find($IdGuru);
                 }

                 if ($guru) {
                     // IdGuru sudah benar
                     $IdGuru = $guru['IdGuru']; // Pastikan ambil dari field primary key model
                     
                     // == CREATE PERSISTENT DEVICE TOKEN ==
                     // Karena user sudah login dan valid guru, kita buatkan Token agar next time check cookie saja
                     $newToken = bin2hex(random_bytes(32));
                     $userAgent = $this->request->getUserAgent()->getAgentString();
                     
                     $this->deviceModel->insert([
                        'DeviceToken' => $newToken,
                        'IdGuru' => $IdGuru,
                        'LastAccess' => date('Y-m-d H:i:s'),
                        'UserAgent' => $userAgent,
                        'CreatedAt' => date('Y-m-d H:i:s')
                     ]);
                     
                     // Set Cookie 1 Tahun
                     setcookie('device_token', $newToken, time() + (86400 * 365), "/");
                 } else {
                     // Logged in but not a Guru?
                     // Bisa logout atau tampilkan error
                     return redirect()->to(base_url())->with('error', 'Akun anda tidak terhubung dengan data Guru.');
                 }
             } else {
                 // Belum login sama sekali -> Redirect ke Standard Login
                 // Set Redirect URL agar kembali ke sini setelah login
                 session()->set('redirect_url', current_url());
                 return redirect()->to(base_url('login'))->with('message', 'Silakan login sekali untuk verifikasi perangkat.');
             }
        }

        // 3. Render View (IdGuru sudah didapat)
        $IdTpq = $linkData['IdTpq'];
        $IdTahunAjaran = $linkData['IdTahunAjaran'];

        $tanggalDipilih = $this->request->getGet('tanggal');
        $tanggalHariIni = date('Y-m-d');
        $tanggal = $tanggalDipilih ? $tanggalDipilih : $tanggalHariIni;

        $santriList = $this->santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, 0, $IdGuru);

        $kelasListAll = [];
        foreach ($santriList as $santriObj) {
            if (!isset($kelasListAll[$santriObj->IdKelas])) {
                $namaKelasOriginal = $santriObj->NamaKelas ?? '';
                $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                $namaKelasMapped = $this->helpFunction->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );

                $kelasListAll[$santriObj->IdKelas] = [
                    'IdKelas' => $santriObj->IdKelas,
                    'NamaKelas' => $namaKelasMapped,
                    'IdTahunAjaran' => $santriObj->IdTahunAjaran
                ];
            }
        }
        
        $selectedKelasId = $this->request->getGet('IdKelas');
        if (!$selectedKelasId && !empty($kelasListAll)) {
            $firstKey = array_key_first($kelasListAll);
            $selectedKelasId = $kelasListAll[$firstKey]['IdKelas'];
        }

        $santri = [];
        foreach ($santriList as $santriObj) {
            if ($santriObj->IdKelas == $selectedKelasId) {
                $cekAbsensi = $this->absensiModel
                    ->where('IdSantri', $santriObj->IdSantri)
                    ->where('Tanggal', $tanggal)
                    ->first();

                if (!$cekAbsensi) {
                    $namaKelasOriginal = $santriObj->NamaKelas ?? '';
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $santriObj->NamaKelas = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                    $santri[] = $santriObj;
                }
            }
        }
        
        $data = [
            'page_title' => 'Absensi Santri Public',
            'santri' => $santri,
            'kelas_list_all' => $kelasListAll,
            'selected_kelas' => $selectedKelasId,
            'tanggal_dipilih' => $tanggal,
            'tanggal_hari_ini' => $tanggalHariIni,
            'hash_key' => $hashKey,
            'guru_nama' => $this->getGuruName($IdGuru),
            'IdGuru' => $IdGuru,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdTpq' => $IdTpq
        ];
        
        return view('frontend/absensi/index', $data);
    }

    public function simpanAbsensi()
    {
        // Validasi Device Token
        $deviceToken = isset($_COOKIE['device_token']) ? $_COOKIE['device_token'] : null;
        
        // Cek DB Token
        $IdGuru = null;
        if($deviceToken) {
            $deviceData = $this->deviceModel->getDevice($deviceToken);
            if($deviceData) $IdGuru = $deviceData['IdGuru'];
        }

        // Fallback ke Login Session jika token invalid/expired tapi user masih login standard
        if (!$IdGuru) {
             helper('auth');
             if(logged_in()) {
                 $user = user();
                 // Resolve ID Guru again
                 $IdGuru = $user->nik ?? session()->get('IdGuru');
                 
                 if ($IdGuru) {
                     $guru = $this->guruModel->find($IdGuru);
                     if($guru) $IdGuru = $guru['IdGuru'];
                 }
             }
        }

        if (!$IdGuru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi habis, silakan login kembali.']);
        }
        
        // Proses Simpan
        $absensiModel = new \App\Models\AbsensiModel();

        $tanggal = $this->request->getPost('tanggal');
        $IdKelas = $this->request->getPost('IdKelas');
        $IdTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $kehadiran = $this->request->getPost('kehadiran');
        $keterangan = $this->request->getPost('keterangan');
        $IdTpq = $this->request->getPost('IdTpq');

        if (!$kehadiran) {
             return $this->response->setJSON(['success' => false, 'message' => 'Data kehadiran kosong.']);
        }

        try {
            foreach ($kehadiran as $IdSantri => $statusKehadiran) {
                $cekAbsensi = $absensiModel
                    ->where('IdSantri', $IdSantri)
                    ->where('Tanggal', $tanggal)
                    ->first();

                $data = [
                    'IdSantri' => $IdSantri,
                    'Tanggal' => $tanggal,
                    'Kehadiran' => $statusKehadiran,
                    'Keterangan' => isset($keterangan[$IdSantri]) ? $keterangan[$IdSantri] : '',
                    'IdKelas' => $IdKelas,
                    'IdGuru' => $IdGuru,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdTpq' => $IdTpq,
                ];

                if ($cekAbsensi) {
                    $absensiModel->update($cekAbsensi->Id, $data);
                } else {
                    $absensiModel->insert($data);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data absensi berhasil disimpan!',
                'tanggal' => $tanggal
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }
    
    private function getGuruName($idGuru)
    {
        $guru = $this->guruModel->find($idGuru);
        if($guru) {
             $nama = $guru['Nama'];
             $nama = ucwords(strtolower($nama));
             $jenisKelaminLower = strtolower($guru['JenisKelamin'] ?? '');
             if (stripos($jenisKelaminLower, 'l') === 0 || stripos($jenisKelaminLower, 'laki') !== false) {
                 return 'Ustadz ' . $nama;
             } elseif (stripos($jenisKelaminLower, 'p') === 0 || stripos($jenisKelaminLower, 'perempuan') !== false) {
                 return 'Ustadzah ' . $nama;
             }
             return $nama;
        }
        return "Guru";
    }
}
