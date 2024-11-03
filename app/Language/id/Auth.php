<?php

return [
    // Exceptions
    'invalidModel'              => 'Model {0} harus dimuat sebelum digunakan.',
    'userNotFound'              => 'Pengguna dengan ID {0} tidak dapat ditemukan.',
    'noUserEntity'              => 'Entitas Pengguna harus disediakan untuk validasi kata sandi.',
    'tooManyCredentials'        => 'Hanya dapat memvalidasi 1 kredensial selain kata sandi.',
    'invalidFields'             => 'Field "{0}" tidak dapat digunakan untuk validasi kredensial.',
    'unsetPasswordLength'       => 'Anda harus mengatur properti `minimumPasswordLength` di file konfigurasi Auth.',
    'unknownError'              => 'Maaf, ada masalah yang terjadi. Silakan coba lagi nanti.',
    'notLoggedIn'               => 'Anda harus login untuk mengakses halaman ini.',
    'notEnoughPrivilege'        => 'Anda tidak memiliki hak akses yang cukup untuk mengakses halaman ini.',

    // Registration
    'registerDisabled'          => 'Pendaftaran akun saat ini tidak diperbolehkan.',
    'registerSuccess'           => 'Selamat! Akun Anda telah berhasil didaftarkan.',
    'registerCLI'               => 'Pengguna baru berhasil dibuat: {0}, #{1}',

    // Activation
    'activationNoUser'          => 'Tidak dapat menemukan pengguna dengan kode aktivasi tersebut.',
    'activationSuccess'         => 'Silakan aktivasi akun Anda.',
    'activationResend'          => 'Kirim ulang pesan aktivasi berhasil.',

    // Login
    'badAttempt'                => 'Tidak dapat login. Silakan cek kredensial login Anda.',
    'loginSuccess'              => 'Selamat datang kembali!',
    'invalidPassword'           => 'Kata sandi yang Anda masukkan salah.',
    'alreadyRegistered'         => 'Sudah punya akun?',

    // Forgotten Passwords
    'forgotNoUser'              => 'Tidak dapat menemukan pengguna dengan email tersebut.',
    'forgotSuccess'             => 'Email untuk pengaturan ulang kata sandi telah dikirim. Silakan cek kotak masuk Anda.',
    'forgotEmailSent'           => 'Email dengan instruksi pengaturan ulang kata sandi telah dikirim ke alamat email Anda.',
];

