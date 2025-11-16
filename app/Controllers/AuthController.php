<?php

namespace App\Controllers;

use Myth\Auth\Controllers\AuthController as MythAuthController;
use App\Models\HelpFunctionModel;

/**
 * Custom AuthController yang extend dari vendor AuthController
 * Menangani authentication dan setup session setelah login
 */
class AuthController extends MythAuthController
{
    /**
     * @var HelpFunctionModel
     */
    protected $helpFunctionModel;

    /**
     * Constructor - initialize models
     */
    public function __construct()
    {
        parent::__construct();
        $this->helpFunctionModel = new HelpFunctionModel();
    }

    /**
     * Override attemptLogin untuk menambahkan custom session setup
     */
    public function attemptLogin()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login    = strtolower($this->request->getPost('login'));
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to log them in...
        if (! $this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
        }

        // Is the user being forced to reset their password?
        if ($this->auth->user()->force_pass_reset === true) {
            return redirect()->to(route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash)->withCookies();
        }

        // Custom logic: Setup session data setelah login berhasil
        $redirectURL = $this->setupPostLoginSession();

        return redirect()->to($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
    }

    /**
     * Setup session data setelah login berhasil
     * Method ini dipanggil setelah user berhasil login
     * 
     * @return string Redirect URL
     */
    protected function setupPostLoginSession()
    {
        // Ambil informasi Guru Kelas
        $idGuru = user()->nik ?? null;

        if ($idGuru) {
            $this->setGuruSessionData($idGuru);
        }

        // Ambil redirect URL dari session atau default ke home
        $redirectURL = session('redirect_url') ?? site_url('/');
        unset($_SESSION['redirect_url']);

        return $redirectURL;
    }

    /**
     * Set Guru-related session data and scoring settings.
     * This centralizes logic previously in vendor auth controller.
     * 
     * @param string|null $idGuru NIK Guru
     */
    protected function setGuruSessionData($idGuru)
    {
        if ($idGuru == null) {
            return;
        }

        try {
            // Get all guru session data in optimized queries
            $guruData = $this->helpFunctionModel->getGuruSessionDataOptimized($idGuru);

            $dataGuruKelas = $guruData['guruKelasData'];
            $settings = $guruData['settings'];
            $kelasOnLatestTa = $guruData['kelasOnLatestTa'];
            $idTpqFromGuru = $guruData['idTpqFromGuru'];
            $tahunAjaranFromGuruKelas = $guruData['tahunAjaranFromGuruKelas'];

            // Set settings
            if ($settings) {
                session()->set('SettingNilaiMin', (int)($settings['Min'] ?? 0));
                session()->set('SettingNilaiMax', (int)($settings['Max'] ?? 100));

                if (isset($settings['Nilai_Alphabet'])) {
                    session()->set('SettingNilaiAlphabet', $settings['Nilai_Alphabet']);
                }

                if (isset($settings['Nilai_Angka_Arabic'])) {
                    session()->set('SettingNilaiArabic', $settings['Nilai_Angka_Arabic']);
                }
            }

            // Set session utama
            if ($dataGuruKelas) {
                $IdKelasList = [];
                $IdJabatanList = [];
                $IdTahunAjaranList = [];
                $IdTpq = '';

                foreach ($dataGuruKelas as $dataGuru) {
                    $IdKelasList[] = $dataGuru->IdKelas;
                    $IdJabatanList[] = $dataGuru->IdJabatan;
                    $IdTahunAjaranList[] = $dataGuru->IdTahunAjaran;
                    $IdTpq = $dataGuru->IdTpq;
                }

                $IdTahunAjaranList = array_unique($IdTahunAjaranList);
                $IdTahunAjaranList = array_values($IdTahunAjaranList);

                // Use optimized kelas data if available
                if ($kelasOnLatestTa) {
                    $IdKelasList = array_map(function ($kelas) {
                        return $kelas->IdKelas;
                    }, $kelasOnLatestTa);
                }

                session()->set('IdGuru', $idGuru);
                session()->set('IdKelas', $IdKelasList);
                session()->set('IdJabatan', $IdJabatanList);
                session()->set('IdTahunAjaranList', $IdTahunAjaranList);
                session()->set('IdTahunAjaran', $IdTahunAjaranList[count($IdTahunAjaranList) - 1]);
                session()->set('IdTpq', $IdTpq);
            } else {
                session()->set('IdGuru', $idGuru);

                if ($idTpqFromGuru) {
                    session()->set('IdTpq', $idTpqFromGuru);
                }

                if ($tahunAjaranFromGuruKelas && !empty($tahunAjaranFromGuruKelas)) {
                    session()->set('IdTahunAjaranList', $tahunAjaranFromGuruKelas);
                    $idTahunAjaran = $tahunAjaranFromGuruKelas[count($tahunAjaranFromGuruKelas) - 1];
                    session()->set('IdTahunAjaran', $idTahunAjaran);
                }
            }
        } catch (\Exception $e) {
            // Log error and fallback to original method if needed
            log_message('error', 'Error in optimized setGuruSessionData: ' . $e->getMessage());
        }
    }
}

