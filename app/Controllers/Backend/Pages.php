<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\TabunganModel;
use App\Models\SantriModel;
use App\Models\HelpFunctionModel;
use App\Models\UserModel;
use Myth\Auth\Password;

class Pages extends BaseController
{
    protected $tabunganModel;
    protected $santriModel;
    protected $helpFunctionModel;
    protected $userModel;

    public function __construct()
    {
        $this->tabunganModel = new TabunganModel();
        $this->santriModel = new SantriModel();
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->userModel = new UserModel();
    }



    public function about()
    {
        $data = ['pages_title' => 'About Me | MyPrograming'];
        return view('backend/dashboard/about', $data);
    }
    public function contact()
    {

        $data = [
            'page_title' => 'About Me | My Programing',
            'alamat' => [
                [
                    'tipe' => 'Admin Website',
                    'alamat' => 'Deni Rusandi',
                    'kota' => 'Phone: 081364290165 | Email: admin.app@simpedis.com'
                ],
                [
                    'tipe' => 'Sekretariat',
                    'alamat' => 'Masjid Al-Hikmah Kec. Seri Kuala Lobam',
                    'kota' => 'Kabupaten Bintan'
                ]
            ],
        ];
        return view('backend/dashboard/contact', $data);
    }

    public function login()
    {
        $data = ['page_title' => 'Login'];
        return view('backend/login/login', $data);
    }

    /**
     * Menampilkan halaman profil pengguna
     */
    public function profil()
    {
        // Ambil data user yang sedang login
        $userId = null;
        if (function_exists('user_id')) {
            $userId = user_id();
        } elseif (function_exists('user') && user()) {
            $userId = user()->id;
        } else {
            $auth = service('auth');
            $userId = $auth->id();
        }

        if (!$userId) {
            return redirect()->to('/auth/index')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = $this->userModel->getUser($userId);
        if (!$user) {
            return redirect()->to('/auth/index')->with('error', 'Data pengguna tidak ditemukan');
        }

        // Ambil informasi grup pengguna dari database
        $groups = [];
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('auth_groups_users agu');
            $builder->select('ag.name');
            $builder->join('auth_groups ag', 'ag.id = agu.group_id', 'inner');
            $builder->where('agu.user_id', $userId);
            $authGroups = $builder->get()->getResultArray();

            foreach ($authGroups as $group) {
                $groups[] = $group['name'];
            }
        } catch (\Exception $e) {
            // Jika terjadi error, groups tetap kosong
            log_message('error', 'Error getting user groups: ' . $e->getMessage());
        }

        $data = [
            'page_title' => 'Profil Pengguna',
            'user' => $user,
            'groups' => $groups
        ];

        return view('backend/pages/profil', $data);
    }

    /**
     * Update profil pengguna
     */
    public function updateProfil()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Ambil ID user yang sedang login
        $userId = null;
        if (function_exists('user_id')) {
            $userId = user_id();
        } elseif (function_exists('user') && user()) {
            $userId = user()->id;
        } else {
            $auth = service('auth');
            $userId = $auth->id();
        }

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
        }

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'fullname' => 'required|min_length[3]|max_length[255]',
            'username' => "required|min_length[3]|max_length[255]|is_unique[users.username,id,{$userId}]",
            'email' => "required|valid_email|max_length[255]|is_unique[users.email,id,{$userId}]"
        ], [
            'fullname' => [
                'required' => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
                'max_length' => 'Nama lengkap maksimal 255 karakter'
            ],
            'username' => [
                'required' => 'Username harus diisi',
                'min_length' => 'Username minimal 3 karakter',
                'max_length' => 'Username maksimal 255 karakter',
                'is_unique' => 'Username sudah digunakan'
            ],
            'email' => [
                'required' => 'Email harus diisi',
                'valid_email' => 'Format email tidak valid',
                'max_length' => 'Email maksimal 255 karakter',
                'is_unique' => 'Email sudah digunakan'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $fullname = $this->request->getPost('fullname');
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');

        try {
            $data = [
                'fullname' => $fullname,
                'username' => $username,
                'email' => $email
            ];

            $this->userModel->updateUser($data, $userId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profil berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset password pengguna
     */
    public function resetPassword()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Ambil ID user yang sedang login
        $userId = null;
        if (function_exists('user_id')) {
            $userId = user_id();
        } elseif (function_exists('user') && user()) {
            $userId = user()->id;
        } else {
            $auth = service('auth');
            $userId = $auth->id();
        }

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
        }

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]'
        ], [
            'current_password' => [
                'required' => 'Password saat ini harus diisi'
            ],
            'new_password' => [
                'required' => 'Password baru harus diisi',
                'min_length' => 'Password baru minimal 8 karakter',
                'max_length' => 'Password baru maksimal 255 karakter'
            ],
            'confirm_password' => [
                'required' => 'Konfirmasi password harus diisi',
                'matches' => 'Konfirmasi password tidak cocok dengan password baru'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        try {
            // Ambil user data
            $user = $this->userModel->getUser($userId);
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data pengguna tidak ditemukan'
                ]);
            }

            // Verifikasi password saat ini
            if (!Password::verify($currentPassword, $user['password_hash'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password saat ini salah'
                ]);
            }

            // Hash password baru
            $data = [
                'password_hash' => Password::hash($newPassword)
            ];

            $this->userModel->updateUser($data, $userId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah password: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Upload foto profil pengguna
     */
    public function uploadPhotoProfil()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Ambil ID user yang sedang login
        $userId = null;
        if (function_exists('user_id')) {
            $userId = user_id();
        } elseif (function_exists('user') && user()) {
            $userId = user()->id;
        } else {
            $auth = service('auth');
            $userId = $auth->id();
        }

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
        }

        try {
            // Buat direktori upload jika belum ada
            $uploadPath = FCPATH . 'uploads/profil/user/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Ambil data user untuk mendapatkan foto lama
            $user = $this->userModel->getUser($userId);
            $oldPhoto = $user['user_image'] ?? null;

            // Hapus foto lama jika ada
            if ($oldPhoto && file_exists($uploadPath . $oldPhoto)) {
                unlink($uploadPath . $oldPhoto);
            }

            // Cek apakah input adalah base64 image (hasil crop) atau file biasa
            $photoCropped = $this->request->getPost('photo_profil_cropped');

            if (!empty($photoCropped)) {
                // Handle base64 image dari crop
                if (preg_match('/^data:image\/(\w+);base64,/', $photoCropped, $type)) {
                    $data = substr($photoCropped, strpos($photoCropped, ',') + 1);
                    $data = base64_decode($data);

                    if ($data === false) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal decode base64 image'
                        ]);
                    }

                    $extension = strtolower($type[1] ?? 'jpg');
                    if ($extension === 'jpeg') {
                        $extension = 'jpg';
                    }

                    // Generate nama file baru
                    $newFileName = 'user_' . $userId . '_' . time() . '.' . $extension;
                    $filePath = $uploadPath . $newFileName;

                    // Simpan file
                    if (file_put_contents($filePath, $data)) {
                        // Update database
                        $this->userModel->updateUser(['user_image' => $newFileName], $userId);

                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Foto profil berhasil diupload',
                            'photo_url' => base_url('uploads/profil/user/' . $newFileName)
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal menyimpan foto profil'
                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Format base64 tidak valid'
                    ]);
                }
            } else {
                // Handle file upload biasa (fallback)
                $file = $this->request->getFile('photo_profil');

                if (!$file || !$file->isValid()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'File tidak valid atau tidak ada file yang diupload'
                    ]);
                }

                // Validasi ukuran file (max 5MB)
                if ($file->getSize() > 5242880) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Ukuran file terlalu besar. Maksimal 5MB'
                    ]);
                }

                // Validasi tipe file (hanya image)
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, atau GIF'
                    ]);
                }

                // Generate nama file baru
                $extension = $file->getExtension();
                $newFileName = 'user_' . $userId . '_' . time() . '.' . $extension;

                // Upload file
                if ($file->move($uploadPath, $newFileName)) {
                    // Update database
                    $this->userModel->updateUser(['user_image' => $newFileName], $userId);

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Foto profil berhasil diupload',
                        'photo_url' => base_url('uploads/profil/user/' . $newFileName)
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengupload foto profil'
                    ]);
                }
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupload foto profil: ' . $e->getMessage()
            ]);
        }
    }
}
